<?php
/**
 * Plugin Name:       Codboost Money Manager
 * Plugin URI:        https://codboost.pro/
 * Description:       Financial tracking tools for Codboost teams with transaction logging, account management, and analytics dashboards.
 * Version:           1.0.0
 * Author:            Codboost
 * Author URI:        https://codboost.pro/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       codboost-money-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Codboost_Money_Manager' ) ) {

    class Codboost_Money_Manager {
        const VERSION = '1.0.0';
        const NONCE_ACTION = 'codboost_money_manager_action';

        /**
         * Singleton instance.
         *
         * @var Codboost_Money_Manager|null
         */
        protected static $instance = null;

        /**
         * Get singleton instance.
         */
        public static function instance() : Codboost_Money_Manager {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructor.
         */
        private function __construct() {
            register_activation_hook( __FILE__, [ $this, 'activate' ] );

            add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_front_assets' ] );
            add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );

            add_action( 'admin_post_cmm_save_account', [ $this, 'handle_save_account' ] );
            add_action( 'admin_post_cmm_delete_account', [ $this, 'handle_delete_account' ] );

            add_action( 'admin_post_cmm_save_reason', [ $this, 'handle_save_reason' ] );
            add_action( 'admin_post_cmm_delete_reason', [ $this, 'handle_delete_reason' ] );

            add_action( 'admin_post_cmm_save_transaction', [ $this, 'handle_save_transaction' ] );
            add_action( 'admin_post_cmm_delete_transaction', [ $this, 'handle_delete_transaction' ] );

            add_shortcode( 'codboost_money_manager', [ $this, 'render_shortcode' ] );
        }

        /**
         * Plugin activation tasks (create tables and seed data).
         */
        public function activate() : void {
            global $wpdb;

            $charset_collate    = $wpdb->get_charset_collate();
            $transactions_table = $wpdb->prefix . 'cbm_transactions';
            $accounts_table     = $wpdb->prefix . 'cbm_accounts';
            $reasons_table      = $wpdb->prefix . 'cbm_reasons';

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $transactions_sql = "CREATE TABLE {$transactions_table} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                transaction_type VARCHAR(20) NOT NULL,
                amount DECIMAL(14,2) NOT NULL DEFAULT 0,
                reason_id BIGINT UNSIGNED NULL,
                account_id BIGINT UNSIGNED NULL,
                note TEXT NULL,
                transaction_date DATE NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY transaction_type (transaction_type),
                KEY account_id (account_id),
                KEY reason_id (reason_id),
                KEY transaction_date (transaction_date)
            ) {$charset_collate};";

            $accounts_sql = "CREATE TABLE {$accounts_table} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(191) NOT NULL,
                description TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY name (name)
            ) {$charset_collate};";

            $reasons_sql = "CREATE TABLE {$reasons_table} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(191) NOT NULL,
                type VARCHAR(20) NOT NULL DEFAULT 'general',
                description TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY name (name)
            ) {$charset_collate};";

            dbDelta( $accounts_sql );
            dbDelta( $reasons_sql );
            dbDelta( $transactions_sql );

            $default_accounts = [ 'Cash Wallet', 'Baridi Mob' ];
            foreach ( $default_accounts as $account ) {
                $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$accounts_table} WHERE name = %s", $account ) );
                if ( ! $existing ) {
                    $wpdb->insert( $accounts_table, [
                        'name'        => $account,
                        'description' => sprintf( __( '%s account added by default during plugin activation.', 'codboost-money-manager' ), $account ),
                    ] );
                }
            }
        }

        /**
         * Load text domain.
         */
        public function load_textdomain() : void {
            load_plugin_textdomain( 'codboost-money-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        /**
         * Register admin menu pages.
         */
        public function register_admin_menu() : void {
            add_menu_page(
                __( 'Codboost Finance', 'codboost-money-manager' ),
                __( 'Codboost Finance', 'codboost-money-manager' ),
                'read',
                'codboost-money-manager',
                [ $this, 'render_admin_page' ],
                'dashicons-chart-area',
                58
            );
        }

        /**
         * Enqueue assets for admin pages.
         */
        public function enqueue_admin_assets( string $hook ) : void {
            if ( 'toplevel_page_codboost-money-manager' !== $hook ) {
                return;
            }

            $this->enqueue_shared_assets();
        }

        /**
         * Enqueue assets for frontend shortcode.
         */
        public function enqueue_front_assets() : void {
            if ( ! is_user_logged_in() ) {
                return;
            }

            if ( ! is_singular() ) {
                return;
            }

            global $post;
            if ( has_shortcode( (string) $post->post_content, 'codboost_money_manager' ) ) {
                $this->enqueue_shared_assets();
            }
        }

        /**
         * Shared enqueue logic for admin + frontend.
         */
        protected function enqueue_shared_assets() : void {
            $plugin_url = plugin_dir_url( __FILE__ );

            wp_enqueue_style(
                'codboost-money-manager',
                $plugin_url . 'assets/css/codboost-money-manager.css',
                [],
                self::VERSION
            );

            wp_enqueue_script( 'chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], '4.4.0', true );
            wp_enqueue_script(
                'codboost-money-manager',
                $plugin_url . 'assets/js/codboost-money-manager.js',
                [ 'chartjs', 'jquery' ],
                self::VERSION,
                true
            );

            wp_localize_script( 'codboost-money-manager', 'codboostMoneyManager', [
                'ajax'        => admin_url( 'admin-ajax.php' ),
                'analytics'   => $this->get_chart_data(),
                'i18n'        => [
                    'income'  => __( 'Income', 'codboost-money-manager' ),
                    'outcome' => __( 'Outcome', 'codboost-money-manager' ),
                ],
            ] );
        }

        /**
         * Render the main admin page.
         */
        public function render_admin_page() : void {
            if ( ! current_user_can( 'read' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'codboost-money-manager' ) );
            }

            $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'dashboard';

            echo '<div class="wrap codboost-money-manager">';
            echo '<h1 class="cbm-page-title">' . esc_html__( 'Codboost Money Manager', 'codboost-money-manager' ) . '</h1>';

            if ( isset( $_GET['cbm_msg'] ) ) {
                $message = sanitize_text_field( wp_unslash( $_GET['cbm_msg'] ) );
                $type    = isset( $_GET['cbm_typ'] ) ? sanitize_key( wp_unslash( $_GET['cbm_typ'] ) ) : 'success';
                printf(
                    '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
                    'error' === $type ? 'error' : 'success',
                    esc_html( $message )
                );
            }

            $this->render_tabs( $active_tab );

            switch ( $active_tab ) {
                case 'transactions':
                    $this->render_transactions_tab();
                    break;
                case 'accounts':
                    $this->render_accounts_tab();
                    break;
                case 'reasons':
                    $this->render_reasons_tab();
                    break;
                case 'dashboard':
                default:
                    $this->render_dashboard_tab();
                    break;
            }

            echo '</div>';
        }

        /**
         * Render tab navigation.
         */
        protected function render_tabs( string $active_tab ) : void {
            $tabs = [
                'dashboard'    => __( 'Analytics', 'codboost-money-manager' ),
                'transactions' => __( 'Transactions', 'codboost-money-manager' ),
                'accounts'     => __( 'Accounts', 'codboost-money-manager' ),
                'reasons'      => __( 'Reasons', 'codboost-money-manager' ),
            ];

            echo '<nav class="cbm-tabs">';
            foreach ( $tabs as $tab => $label ) {
                $class = $active_tab === $tab ? 'cbm-tab is-active' : 'cbm-tab';
                printf(
                    '<a class="%1$s" href="%2$s">%3$s</a>',
                    esc_attr( $class ),
                    esc_url( add_query_arg( [ 'tab' => $tab ], menu_page_url( 'codboost-money-manager', false ) ) ),
                    esc_html( $label )
                );
            }
            echo '</nav>';
        }

        /**
         * Render dashboard tab.
         */
        protected function render_dashboard_tab() : void {
            $totals        = $this->get_totals();
            $recent        = $this->get_transactions( 5 );
            $accounts_data = $this->get_account_distribution();

            echo '<section class="cbm-grid">';
            $this->render_stat_card( __( 'Total Income', 'codboost-money-manager' ), $totals['income'], 'cbm-card--income' );
            $this->render_stat_card( __( 'Total Outcome', 'codboost-money-manager' ), $totals['outcome'], 'cbm-card--outcome' );
            $this->render_stat_card( __( 'Balance', 'codboost-money-manager' ), $totals['balance'], 'cbm-card--balance' );
            echo '</section>';

            echo '<section class="cbm-grid cbm-grid--two">';
            echo '<div class="cbm-panel">';
            echo '<h2>' . esc_html__( '12-Month Cashflow', 'codboost-money-manager' ) . '</h2>';
            echo '<canvas id="cbm-cashflow-chart" height="220"></canvas>';
            echo '</div>';

            echo '<div class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Accounts Snapshot', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $accounts_data ) ) {
                echo '<p>' . esc_html__( 'Add transactions to see distribution per account.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<canvas id="cbm-accounts-chart" height="220"></canvas>';
            }
            echo '</div>';
            echo '</section>';

            echo '<section class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Recent Activity', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $recent ) ) {
                echo '<p>' . esc_html__( 'No transactions recorded yet.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<table class="cbm-table">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__( 'Date', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Type', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Amount', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Account', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Reason', 'codboost-money-manager' ) . '</th>';
                echo '</tr></thead><tbody>';
                foreach ( $recent as $row ) {
                    printf(
                        '<tr><td>%1$s</td><td class="cbm-pill cbm-pill--%6$s">%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s</td></tr>',
                        esc_html( date_i18n( get_option( 'date_format' ), strtotime( $row->transaction_date ) ) ),
                        esc_html( ucfirst( $row->transaction_type ) ),
                        esc_html( $this->format_currency( $row->amount ) ),
                        esc_html( $row->account_name ?: __( 'Unassigned', 'codboost-money-manager' ) ),
                        esc_html( $row->reason_name ?: __( 'Unassigned', 'codboost-money-manager' ) ),
                        esc_attr( $row->transaction_type )
                    );
                }
                echo '</tbody></table>';
            }
            echo '</section>';
        }

        /**
         * Render transactions tab with form + table.
         */
        protected function render_transactions_tab() : void {
            $accounts   = $this->get_accounts();
            $reasons    = $this->get_reasons();
            $rows       = $this->get_transactions( 20 );
            $form_url   = admin_url( 'admin-post.php' );
            $delete_url = admin_url( 'admin-post.php' );

            echo '<section class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Log New Transaction', 'codboost-money-manager' ) . '</h2>';
            echo '<form class="cbm-form" method="post" action="' . esc_url( $form_url ) . '">';
            wp_nonce_field( self::NONCE_ACTION, '_cbm_nonce' );
            echo '<input type="hidden" name="action" value="cmm_save_transaction">';
            echo '<div class="cbm-form__grid">';
            echo $this->render_select_field( 'transaction_type', __( 'Type', 'codboost-money-manager' ), [
                'income'  => __( 'Income', 'codboost-money-manager' ),
                'outcome' => __( 'Outcome', 'codboost-money-manager' ),
            ] );
            echo $this->render_input_field( 'amount', __( 'Amount', 'codboost-money-manager' ), 'number', [ 'step' => '0.01', 'min' => '0' ] );
            echo $this->render_select_field( 'account_id', __( 'Account', 'codboost-money-manager' ), $this->convert_records_to_options( $accounts ) );
            echo $this->render_select_field( 'reason_id', __( 'Reason', 'codboost-money-manager' ), $this->convert_records_to_options( $reasons ) );
            echo $this->render_input_field( 'transaction_date', __( 'Date', 'codboost-money-manager' ), 'date', [ 'value' => wp_date( 'Y-m-d' ) ] );
            echo '</div>';
            echo $this->render_textarea_field( 'note', __( 'Notes (optional)', 'codboost-money-manager' ) );
            echo '<button type="submit" class="button button-primary">' . esc_html__( 'Save Transaction', 'codboost-money-manager' ) . '</button>';
            echo '</form>';
            echo '</section>';

            echo '<section class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Latest Transactions', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $rows ) ) {
                echo '<p>' . esc_html__( 'No transactions yet. Add your first entry above.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<table class="cbm-table">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__( 'Date', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Type', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Amount', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Account', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Reason', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Notes', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Actions', 'codboost-money-manager' ) . '</th>';
                echo '</tr></thead><tbody>';
                foreach ( $rows as $row ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $row->transaction_date ) ) ) . '</td>';
                    echo '<td><span class="cbm-pill cbm-pill--' . esc_attr( $row->transaction_type ) . '">' . esc_html( ucfirst( $row->transaction_type ) ) . '</span></td>';
                    echo '<td>' . esc_html( $this->format_currency( $row->amount ) ) . '</td>';
                    echo '<td>' . esc_html( $row->account_name ?: __( 'Unassigned', 'codboost-money-manager' ) ) . '</td>';
                    echo '<td>' . esc_html( $row->reason_name ?: __( 'Unassigned', 'codboost-money-manager' ) ) . '</td>';
                    echo '<td>' . esc_html( $row->note ) . '</td>';
                    echo '<td>';
                    printf(
                        '<form method="post" action="%1$s" onsubmit="return confirm(\'%2$s\');">',
                        esc_url( $delete_url ),
                        esc_js( __( 'Are you sure you want to delete this transaction?', 'codboost-money-manager' ) )
                    );
                    wp_nonce_field( self::NONCE_ACTION, '_cbm_nonce' );
                    echo '<input type="hidden" name="action" value="cmm_delete_transaction">';
                    echo '<input type="hidden" name="transaction_id" value="' . esc_attr( $row->id ) . '">';
                    echo '<button type="submit" class="button button-link-delete">' . esc_html__( 'Delete', 'codboost-money-manager' ) . '</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }
            echo '</section>';
        }

        /**
         * Render accounts tab.
         */
        protected function render_accounts_tab() : void {
            $accounts = $this->get_accounts();
            $form_url = admin_url( 'admin-post.php' );

            echo '<section class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Manage Accounts', 'codboost-money-manager' ) . '</h2>';
            echo '<form class="cbm-form" method="post" action="' . esc_url( $form_url ) . '">';
            wp_nonce_field( self::NONCE_ACTION, '_cbm_nonce' );
            echo '<input type="hidden" name="action" value="cmm_save_account">';
            echo $this->render_input_field( 'name', __( 'Account Name', 'codboost-money-manager' ) );
            echo $this->render_textarea_field( 'description', __( 'Description (optional)', 'codboost-money-manager' ) );
            echo '<button type="submit" class="button button-primary">' . esc_html__( 'Add Account', 'codboost-money-manager' ) . '</button>';
            echo '</form>';
            echo '</section>';

            echo '<section class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Saved Accounts', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $accounts ) ) {
                echo '<p>' . esc_html__( 'No accounts created yet.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<ul class="cbm-list">';
                foreach ( $accounts as $account ) {
                    echo '<li>';
                    echo '<span class="cbm-list__title">' . esc_html( $account->name ) . '</span>';
                    if ( ! empty( $account->description ) ) {
                        echo '<span class="cbm-list__meta">' . esc_html( $account->description ) . '</span>';
                    }
                    printf(
                        '<form method="post" action="%1$s" onsubmit="return confirm(\'%2$s\');">',
                        esc_url( $form_url ),
                        esc_js( __( 'Delete this account? Existing transactions will keep the previous reference.', 'codboost-money-manager' ) )
                    );
                    wp_nonce_field( self::NONCE_ACTION, '_cbm_nonce' );
                    echo '<input type="hidden" name="action" value="cmm_delete_account">';
                    echo '<input type="hidden" name="account_id" value="' . esc_attr( $account->id ) . '">';
                    echo '<button type="submit" class="button button-link-delete">' . esc_html__( 'Delete', 'codboost-money-manager' ) . '</button>';
                    echo '</form>';
                    echo '</li>';
                }
                echo '</ul>';
            }
            echo '</section>';
        }

        /**
         * Render reasons tab.
         */
        protected function render_reasons_tab() : void {
            $reasons  = $this->get_reasons();
            $form_url = admin_url( 'admin-post.php' );

            echo '<section class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Manage Reasons', 'codboost-money-manager' ) . '</h2>';
            echo '<form class="cbm-form" method="post" action="' . esc_url( $form_url ) . '">';
            wp_nonce_field( self::NONCE_ACTION, '_cbm_nonce' );
            echo '<input type="hidden" name="action" value="cmm_save_reason">';
            echo '<div class="cbm-form__grid">';
            echo $this->render_input_field( 'name', __( 'Reason Name', 'codboost-money-manager' ) );
            echo $this->render_select_field( 'type', __( 'Default Type', 'codboost-money-manager' ), [
                'income'  => __( 'Income', 'codboost-money-manager' ),
                'outcome' => __( 'Outcome', 'codboost-money-manager' ),
                'general' => __( 'General', 'codboost-money-manager' ),
            ], 'general' );
            echo '</div>';
            echo $this->render_textarea_field( 'description', __( 'Description (optional)', 'codboost-money-manager' ) );
            echo '<button type="submit" class="button button-primary">' . esc_html__( 'Add Reason', 'codboost-money-manager' ) . '</button>';
            echo '</form>';
            echo '</section>';

            echo '<section class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Saved Reasons', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $reasons ) ) {
                echo '<p>' . esc_html__( 'No reasons created yet.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<ul class="cbm-list">';
                foreach ( $reasons as $reason ) {
                    echo '<li>';
                    echo '<span class="cbm-list__title">' . esc_html( $reason->name ) . '</span>';
                    echo '<span class="cbm-list__badge">' . esc_html( ucfirst( $reason->type ) ) . '</span>';
                    if ( ! empty( $reason->description ) ) {
                        echo '<span class="cbm-list__meta">' . esc_html( $reason->description ) . '</span>';
                    }
                    printf(
                        '<form method="post" action="%1$s" onsubmit="return confirm(\'%2$s\');">',
                        esc_url( $form_url ),
                        esc_js( __( 'Delete this reason?', 'codboost-money-manager' ) )
                    );
                    wp_nonce_field( self::NONCE_ACTION, '_cbm_nonce' );
                    echo '<input type="hidden" name="action" value="cmm_delete_reason">';
                    echo '<input type="hidden" name="reason_id" value="' . esc_attr( $reason->id ) . '">';
                    echo '<button type="submit" class="button button-link-delete">' . esc_html__( 'Delete', 'codboost-money-manager' ) . '</button>';
                    echo '</form>';
                    echo '</li>';
                }
                echo '</ul>';
            }
            echo '</section>';
        }

        /**
         * Handle account creation.
         */
        public function handle_save_account() : void {
            $this->verify_permissions();

            $name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
            $description = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';

            if ( empty( $name ) ) {
                $this->redirect_with_message( __( 'Account name is required.', 'codboost-money-manager' ), 'error', 'accounts' );
            }

            global $wpdb;
            $accounts_table = $wpdb->prefix . 'cbm_accounts';

            $wpdb->insert( $accounts_table, [
                'name'        => $name,
                'description' => $description,
            ] );

            $this->redirect_with_message( __( 'Account created successfully.', 'codboost-money-manager' ), 'success', 'accounts' );
        }

        /**
         * Handle account deletion.
         */
        public function handle_delete_account() : void {
            $this->verify_permissions();

            $account_id = isset( $_POST['account_id'] ) ? absint( $_POST['account_id'] ) : 0;
            if ( ! $account_id ) {
                $this->redirect_with_message( __( 'Invalid account.', 'codboost-money-manager' ), 'error', 'accounts' );
            }

            global $wpdb;
            $accounts_table = $wpdb->prefix . 'cbm_accounts';
            $wpdb->delete( $accounts_table, [ 'id' => $account_id ], [ '%d' ] );

            $this->redirect_with_message( __( 'Account deleted.', 'codboost-money-manager' ), 'success', 'accounts' );
        }

        /**
         * Handle reason creation.
         */
        public function handle_save_reason() : void {
            $this->verify_permissions();

            $name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
            $type        = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'general';
            $description = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';

            if ( empty( $name ) ) {
                $this->redirect_with_message( __( 'Reason name is required.', 'codboost-money-manager' ), 'error', 'reasons' );
            }

            global $wpdb;
            $reasons_table = $wpdb->prefix . 'cbm_reasons';

            $wpdb->insert( $reasons_table, [
                'name'        => $name,
                'type'        => in_array( $type, [ 'income', 'outcome', 'general' ], true ) ? $type : 'general',
                'description' => $description,
            ] );

            $this->redirect_with_message( __( 'Reason created successfully.', 'codboost-money-manager' ), 'success', 'reasons' );
        }

        /**
         * Handle reason deletion.
         */
        public function handle_delete_reason() : void {
            $this->verify_permissions();

            $reason_id = isset( $_POST['reason_id'] ) ? absint( $_POST['reason_id'] ) : 0;
            if ( ! $reason_id ) {
                $this->redirect_with_message( __( 'Invalid reason.', 'codboost-money-manager' ), 'error', 'reasons' );
            }

            global $wpdb;
            $reasons_table = $wpdb->prefix . 'cbm_reasons';
            $wpdb->delete( $reasons_table, [ 'id' => $reason_id ], [ '%d' ] );

            $this->redirect_with_message( __( 'Reason deleted.', 'codboost-money-manager' ), 'success', 'reasons' );
        }

        /**
         * Handle transaction creation.
         */
        public function handle_save_transaction() : void {
            $this->verify_permissions();

            $type             = isset( $_POST['transaction_type'] ) ? sanitize_key( wp_unslash( $_POST['transaction_type'] ) ) : 'income';
            $amount           = isset( $_POST['amount'] ) ? floatval( wp_unslash( $_POST['amount'] ) ) : 0;
            $account_id       = isset( $_POST['account_id'] ) ? absint( $_POST['account_id'] ) : 0;
            $reason_id        = isset( $_POST['reason_id'] ) ? absint( $_POST['reason_id'] ) : 0;
            $transaction_date = isset( $_POST['transaction_date'] ) ? sanitize_text_field( wp_unslash( $_POST['transaction_date'] ) ) : '';
            $note             = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';

            if ( $amount <= 0 ) {
                $this->redirect_with_message( __( 'Amount must be greater than zero.', 'codboost-money-manager' ), 'error', 'transactions' );
            }

            if ( ! in_array( $type, [ 'income', 'outcome' ], true ) ) {
                $type = 'income';
            }

            $timestamp = $transaction_date ? strtotime( $transaction_date ) : false;
            if ( false === $timestamp ) {
                $transaction_date = wp_date( 'Y-m-d' );
            } else {
                $transaction_date = wp_date( 'Y-m-d', $timestamp );
            }

            global $wpdb;
            $transactions_table = $wpdb->prefix . 'cbm_transactions';

            $wpdb->insert( $transactions_table, [
                'user_id'          => get_current_user_id(),
                'transaction_type' => $type,
                'amount'           => $amount,
                'account_id'       => $account_id ?: null,
                'reason_id'        => $reason_id ?: null,
                'note'             => $note,
                'transaction_date' => $transaction_date,
            ], [
                '%d',
                '%s',
                '%f',
                '%d',
                '%d',
                '%s',
                '%s',
            ] );

            $this->redirect_with_message( __( 'Transaction saved successfully.', 'codboost-money-manager' ), 'success', 'transactions' );
        }

        /**
         * Handle transaction deletion.
         */
        public function handle_delete_transaction() : void {
            $this->verify_permissions();

            $transaction_id = isset( $_POST['transaction_id'] ) ? absint( $_POST['transaction_id'] ) : 0;
            if ( ! $transaction_id ) {
                $this->redirect_with_message( __( 'Invalid transaction.', 'codboost-money-manager' ), 'error', 'transactions' );
            }

            global $wpdb;
            $transactions_table = $wpdb->prefix . 'cbm_transactions';
            $wpdb->delete( $transactions_table, [ 'id' => $transaction_id ], [ '%d' ] );

            $this->redirect_with_message( __( 'Transaction deleted.', 'codboost-money-manager' ), 'success', 'transactions' );
        }

        /**
         * Verify nonce and permissions.
         */
        protected function verify_permissions() : void {
            if ( ! is_user_logged_in() ) {
                wp_die( __( 'You must be logged in.', 'codboost-money-manager' ) );
            }

            if ( ! isset( $_POST['_cbm_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_cbm_nonce'] ) ), self::NONCE_ACTION ) ) {
                wp_die( __( 'Security check failed.', 'codboost-money-manager' ) );
            }
        }

        /**
         * Redirect helper.
         */
        protected function redirect_with_message( string $message, string $type = 'success', string $tab = 'dashboard' ) : void {
            $referer = isset( $_POST['_wp_http_referer'] ) ? esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) ) : '';

            if ( $referer && false === strpos( $referer, 'wp-admin' ) ) {
                $url = add_query_arg(
                    [
                        'cbm_msg' => rawurlencode( $message ),
                        'cbm_typ' => $type,
                    ],
                    $referer
                );
            } else {
                $url = add_query_arg(
                    [
                        'page'    => 'codboost-money-manager',
                        'tab'     => $tab,
                        'cbm_msg' => rawurlencode( $message ),
                        'cbm_typ' => $type,
                    ],
                    admin_url( 'admin.php' )
                );
            }

            wp_safe_redirect( $url );
            exit;
        }

        /**
         * Get totals summary.
         */
        protected function get_totals() : array {
            global $wpdb;
            $table = $wpdb->prefix . 'cbm_transactions';

            $results = $wpdb->get_results( "SELECT transaction_type, SUM(amount) AS total FROM {$table} GROUP BY transaction_type" );
            $totals  = [ 'income' => 0.0, 'outcome' => 0.0 ];

            foreach ( $results as $row ) {
                $totals[ $row->transaction_type ] = (float) $row->total;
            }

            $totals['balance'] = $totals['income'] - $totals['outcome'];

            return $totals;
        }

        /**
         * Fetch recent transactions.
         */
        protected function get_transactions( int $limit = 20 ) : array {
            global $wpdb;
            $transactions_table = $wpdb->prefix . 'cbm_transactions';
            $accounts_table     = $wpdb->prefix . 'cbm_accounts';
            $reasons_table      = $wpdb->prefix . 'cbm_reasons';

            $query = $wpdb->prepare(
                "SELECT t.*, a.name AS account_name, r.name AS reason_name
                FROM {$transactions_table} t
                LEFT JOIN {$accounts_table} a ON a.id = t.account_id
                LEFT JOIN {$reasons_table} r ON r.id = t.reason_id
                ORDER BY t.transaction_date DESC, t.created_at DESC
                LIMIT %d",
                $limit
            );

            return $wpdb->get_results( $query ) ?: [];
        }

        /**
         * Fetch accounts.
         */
        protected function get_accounts() : array {
            global $wpdb;
            $table = $wpdb->prefix . 'cbm_accounts';

            return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY name ASC" ) ?: [];
        }

        /**
         * Fetch reasons.
         */
        protected function get_reasons() : array {
            global $wpdb;
            $table = $wpdb->prefix . 'cbm_reasons';

            return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY name ASC" ) ?: [];
        }

        /**
         * Build chart data for JS.
         */
        protected function get_chart_data() : array {
            global $wpdb;
            $transactions_table = $wpdb->prefix . 'cbm_transactions';
            $accounts_table     = $wpdb->prefix . 'cbm_accounts';

            $labels      = [];
            $income_data = [];
            $out_data    = [];

            for ( $i = 11; $i >= 0; $i -- ) {
                $month_key = gmdate( 'Y-m', strtotime( '-' . $i . ' months' ) );
                $labels[]  = date_i18n( 'M Y', strtotime( $month_key . '-01' ) );

                $prepared = $wpdb->prepare(
                    "SELECT transaction_type, SUM(amount) as total
                    FROM {$transactions_table}
                    WHERE DATE_FORMAT(transaction_date, '%%Y-%%m') = %s
                    GROUP BY transaction_type",
                    $month_key
                );

                $results = $wpdb->get_results( $prepared );
                $income  = 0.0;
                $outcome = 0.0;
                foreach ( $results as $row ) {
                    if ( 'income' === $row->transaction_type ) {
                        $income = (float) $row->total;
                    }
                    if ( 'outcome' === $row->transaction_type ) {
                        $outcome = (float) $row->total;
                    }
                }
                $income_data[] = $income;
                $out_data[]    = $outcome;
            }

            $accounts_distribution = $wpdb->get_results(
                "SELECT a.name AS account, SUM(CASE WHEN t.transaction_type = 'income' THEN t.amount ELSE -t.amount END) AS balance
                FROM {$transactions_table} t
                LEFT JOIN {$accounts_table} a ON a.id = t.account_id
                GROUP BY a.name"
            );

            return [
                'labels'               => $labels,
                'income'               => $income_data,
                'outcome'              => $out_data,
                'accountsDistribution' => $accounts_distribution,
            ];
        }

        /**
         * Account distribution for server rendering fallback.
         */
        protected function get_account_distribution() : array {
            $data = $this->get_chart_data()['accountsDistribution'];

            if ( empty( $data ) ) {
                return [];
            }

            $distribution = [];
            foreach ( $data as $row ) {
                if ( null === $row->account ) {
                    $row->account = __( 'Unassigned', 'codboost-money-manager' );
                }
                $distribution[] = [
                    'account' => $row->account,
                    'balance' => (float) $row->balance,
                ];
            }

            return $distribution;
        }

        /**
         * Render stat card helper.
         */
        protected function render_stat_card( string $title, float $value, string $class = '' ) : void {
            printf(
                '<article class="cbm-card %3$s"><h3>%1$s</h3><p>%2$s</p></article>',
                esc_html( $title ),
                esc_html( $this->format_currency( $value ) ),
                esc_attr( $class )
            );
        }

        /**
         * Render input helper.
         */
        protected function render_input_field( string $name, string $label, string $type = 'text', array $attrs = [], bool $required = true ) : string {
            $value      = '';
            $attributes = '';

            if ( isset( $attrs['value'] ) ) {
                $value = $attrs['value'];
                unset( $attrs['value'] );
            }

            foreach ( $attrs as $attr => $attr_value ) {
                $attributes .= sprintf( ' %s="%s"', esc_attr( $attr ), esc_attr( $attr_value ) );
            }

            return sprintf(
                '<label class="cbm-field"><span>%1$s</span><input type="%3$s" name="%2$s" id="%2$s" value="%5$s" %4$s %6$s></label>',
                esc_html( $label ),
                esc_attr( $name ),
                esc_attr( $type ),
                $attributes,
                esc_attr( $value ),
                $required ? 'required' : ''
            );
        }

        /**
         * Render textarea helper.
         */
        protected function render_textarea_field( string $name, string $label ) : string {
            return sprintf(
                '<label class="cbm-field"><span>%1$s</span><textarea name="%2$s" id="%2$s" rows="3"></textarea></label>',
                esc_html( $label ),
                esc_attr( $name )
            );
        }

        /**
         * Render select helper.
         */
        protected function render_select_field( string $name, string $label, array $options, string $default = '' ) : string {
            $html = '<label class="cbm-field"><span>' . esc_html( $label ) . '</span><select name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '">';

            $html .= '<option value="">' . esc_html__( 'Select option', 'codboost-money-manager' ) . '</option>';

            foreach ( $options as $value => $text ) {
                $selected = selected( (string) $default, (string) $value, false );
                $html    .= '<option value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $text ) . '</option>';
            }

            $html .= '</select></label>';

            return $html;
        }

        /**
         * Convert DB rows to options array.
         */
        protected function convert_records_to_options( array $records ) : array {
            $options = [];
            foreach ( $records as $record ) {
                $options[ $record->id ] = $record->name;
            }

            return $options;
        }

        /**
         * Format currency helper.
         */
        protected function format_currency( float $value ) : string {
            $symbol = get_option( 'woocommerce_currency_symbol' );
            if ( empty( $symbol ) ) {
                $symbol = 'DZD';
            }

            $symbol = apply_filters( 'codboost_money_manager_currency_symbol', $symbol, $value );

            return sprintf( '%s %s', $symbol, number_format_i18n( $value, 2 ) );
        }

        /**
         * Shortcode renderer for frontend dashboard.
         */
        public function render_shortcode() : string {
            if ( ! is_user_logged_in() ) {
                $login_url = wp_login_url( get_permalink() );
                return '<div class="cbm-notice">' . sprintf(
                    /* translators: %s is the login URL */
                    esc_html__( 'You need to be logged in to access the Codboost Money Manager. %s', 'codboost-money-manager' ),
                    '<a href="' . esc_url( $login_url ) . '" class="cbm-link">' . esc_html__( 'Login now', 'codboost-money-manager' ) . '</a>'
                ) . '</div>';
            }

            ob_start();
            echo '<div class="codboost-money-manager">';
            if ( isset( $_GET['cbm_msg'] ) ) {
                $message = sanitize_text_field( wp_unslash( $_GET['cbm_msg'] ) );
                $type    = isset( $_GET['cbm_typ'] ) ? sanitize_key( wp_unslash( $_GET['cbm_typ'] ) ) : 'success';
                printf(
                    '<div class="cbm-panel %1$s"><p>%2$s</p></div>',
                    'error' === $type ? 'cbm-panel--error' : 'cbm-panel--success',
                    esc_html( $message )
                );
            }
            $this->render_dashboard_tab();
            $this->render_transactions_tab();
            echo '</div>';

            return ob_get_clean();
        }
    }
}

Codboost_Money_Manager::instance();
