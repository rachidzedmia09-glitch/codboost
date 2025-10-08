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
        const VERSION = '1.1.0';
        const NONCE_ACTION = 'codboost_money_manager_action';
        const DEMO_OPTION = 'codboost_money_manager_demo_seeded';

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

            add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
            add_action( 'init', [ $this, 'maybe_upgrade_schema' ] );

            add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_front_assets' ] );

            add_action( 'admin_post_cmm_save_account', [ $this, 'handle_save_account' ] );
            add_action( 'admin_post_cmm_delete_account', [ $this, 'handle_delete_account' ] );

            add_action( 'admin_post_cmm_save_reason', [ $this, 'handle_save_reason' ] );
            add_action( 'admin_post_cmm_delete_reason', [ $this, 'handle_delete_reason' ] );

            add_action( 'admin_post_cmm_save_transaction', [ $this, 'handle_save_transaction' ] );
            add_action( 'admin_post_cmm_delete_transaction', [ $this, 'handle_delete_transaction' ] );

            add_action( 'admin_post_cmm_import_demo', [ $this, 'handle_import_demo' ] );

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
                party VARCHAR(191) NULL,
                reference VARCHAR(191) NULL,
                tags TEXT NULL,
                currency VARCHAR(10) NOT NULL DEFAULT 'DZD',
                status VARCHAR(40) NOT NULL DEFAULT 'cleared',
                exchange_rate DECIMAL(14,4) NOT NULL DEFAULT 1,
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

            $this->seed_default_terms();
        }

        /**
         * Ensure schema stays up to date when new versions introduce columns.
         */
        public function maybe_upgrade_schema() : void {
            global $wpdb;

            $transactions_table = $wpdb->prefix . 'cbm_transactions';

            $columns = [
                'party'         => "ALTER TABLE {$transactions_table} ADD COLUMN party VARCHAR(191) NULL AFTER note",
                'reference'     => "ALTER TABLE {$transactions_table} ADD COLUMN reference VARCHAR(191) NULL AFTER party",
                'tags'          => "ALTER TABLE {$transactions_table} ADD COLUMN tags TEXT NULL AFTER reference",
                'currency'      => "ALTER TABLE {$transactions_table} ADD COLUMN currency VARCHAR(10) NOT NULL DEFAULT 'DZD' AFTER tags",
                'status'        => "ALTER TABLE {$transactions_table} ADD COLUMN status VARCHAR(40) NOT NULL DEFAULT 'cleared' AFTER currency",
                'exchange_rate' => "ALTER TABLE {$transactions_table} ADD COLUMN exchange_rate DECIMAL(14,4) NOT NULL DEFAULT 1 AFTER status",
            ];

            foreach ( $columns as $column => $sql ) {
                $has_column = $wpdb->get_results( $wpdb->prepare( "SHOW COLUMNS FROM {$transactions_table} LIKE %s", $column ) );
                if ( empty( $has_column ) ) {
                    $wpdb->query( $sql );
                }
            }
        }

        /**
         * Seed core accounts and reasons when activating or importing demo data.
         */
        protected function seed_default_terms() : void {
            global $wpdb;

            $accounts_table = $wpdb->prefix . 'cbm_accounts';
            $reasons_table  = $wpdb->prefix . 'cbm_reasons';

            $default_accounts = [
                [
                    'name'        => __( 'Cash Wallet', 'codboost-money-manager' ),
                    'description' => __( 'Physical cash on hand for Codboost operations.', 'codboost-money-manager' ),
                ],
                [
                    'name'        => __( 'Baridi Mob', 'codboost-money-manager' ),
                    'description' => __( 'Primary mobile payment account.', 'codboost-money-manager' ),
                ],
                [
                    'name'        => __( 'Bank - BNP Paribas', 'codboost-money-manager' ),
                    'description' => __( 'Corporate current account for settlements and payroll.', 'codboost-money-manager' ),
                ],
            ];

            foreach ( $default_accounts as $account ) {
                $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$accounts_table} WHERE name = %s", $account['name'] ) );
                if ( ! $existing ) {
                    $wpdb->insert( $accounts_table, $account );
                }
            }

            $default_reasons = [
                [
                    'name'        => __( 'Product Revenue', 'codboost-money-manager' ),
                    'type'        => 'income',
                    'description' => __( 'Income generated from Codboost product sales and subscriptions.', 'codboost-money-manager' ),
                ],
                [
                    'name'        => __( 'Client Services', 'codboost-money-manager' ),
                    'type'        => 'income',
                    'description' => __( 'Consulting and services invoiced to Codboost partners.', 'codboost-money-manager' ),
                ],
                [
                    'name'        => __( 'Team Salaries', 'codboost-money-manager' ),
                    'type'        => 'outcome',
                    'description' => __( 'Monthly payroll and benefits.', 'codboost-money-manager' ),
                ],
                [
                    'name'        => __( 'Platform Expenses', 'codboost-money-manager' ),
                    'type'        => 'outcome',
                    'description' => __( 'Hosting, marketing, and tooling costs.', 'codboost-money-manager' ),
                ],
            ];

            foreach ( $default_reasons as $reason ) {
                $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$reasons_table} WHERE name = %s", $reason['name'] ) );
                if ( ! $existing ) {
                    $wpdb->insert( $reasons_table, $reason );
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
                case 'setup':
                    $this->render_setup_tab();
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
                'setup'        => __( 'Setup & Import', 'codboost-money-manager' ),
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
            $totals          = $this->get_totals();
            $recent          = $this->get_transactions( 5 );
            $accounts_data   = $this->get_account_distribution();
            $chart_data      = $this->get_chart_data();
            $reason_data     = $chart_data['reasonBreakdown'] ?? [];
            $insights        = $chart_data['insights'] ?? [];

            echo '<section class="cbm-grid cbm-grid--stats">';
            $this->render_stat_card( __( 'Total Income', 'codboost-money-manager' ), $totals['income'], 'cbm-card--income' );
            $this->render_stat_card( __( 'Total Outcome', 'codboost-money-manager' ), $totals['outcome'], 'cbm-card--outcome' );
            $this->render_stat_card( __( 'Balance', 'codboost-money-manager' ), $totals['balance'], 'cbm-card--balance' );
            $this->render_stat_card( __( 'Average Monthly Net', 'codboost-money-manager' ), $totals['average_net'], 'cbm-card--trend' );
            echo '</section>';

            echo '<section class="cbm-grid cbm-grid--two">';
            echo '<div class="cbm-panel cbm-panel--elevated">';
            echo '<h2>' . esc_html__( '12-Month Cashflow', 'codboost-money-manager' ) . '</h2>';
            echo '<canvas id="cbm-cashflow-chart" height="240"></canvas>';
            echo '</div>';

            echo '<div class="cbm-panel cbm-panel--elevated">';
            echo '<h2>' . esc_html__( 'Cumulative Balance', 'codboost-money-manager' ) . '</h2>';
            echo '<canvas id="cbm-balance-chart" height="240"></canvas>';
            echo '</div>';
            echo '</section>';

            echo '<section class="cbm-grid cbm-grid--two">';
            echo '<div class="cbm-panel cbm-panel--elevated">';
            echo '<h2>' . esc_html__( 'Accounts Snapshot', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $accounts_data ) ) {
                echo '<p>' . esc_html__( 'Add transactions to see distribution per account.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<canvas id="cbm-accounts-chart" height="240"></canvas>';
            }
            echo '</div>';

            echo '<div class="cbm-panel cbm-panel--elevated">';
            echo '<h2>' . esc_html__( 'Reason Efficiency', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $reason_data ) ) {
                echo '<p>' . esc_html__( 'Log transactions to understand which initiatives drive your performance.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<canvas id="cbm-reason-chart" height="240"></canvas>';
            }
            echo '</div>';
            echo '</section>';

            echo '<section class="cbm-grid cbm-grid--two">';
            echo '<div class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Insights & Signals', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $insights ) ) {
                echo '<p>' . esc_html__( 'Insights will appear after you import or log more transactions.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<ul class="cbm-insights">';
                foreach ( $insights as $insight ) {
                    printf( '<li><strong>%1$s</strong><span>%2$s</span></li>', esc_html( $insight['title'] ), esc_html( $insight['detail'] ) );
                }
                echo '</ul>';
            }
            echo '</div>';

            echo '<div class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Recent Activity', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $recent ) ) {
                echo '<p>' . esc_html__( 'No transactions recorded yet.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<table class="cbm-table cbm-table--compact">';
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
                        esc_html( $this->format_transaction_amount( (float) $row->amount, (string) $row->currency, (float) $row->exchange_rate ) ),
                        esc_html( $row->account_name ?: __( 'Unassigned', 'codboost-money-manager' ) ),
                        esc_html( $row->reason_name ?: __( 'Unassigned', 'codboost-money-manager' ) ),
                        esc_attr( $row->transaction_type )
                    );
                }
                echo '</tbody></table>';
            }
            echo '</div>';
            echo '</section>';
        }

        /**
         * Render transactions tab with form + table.
         */
        protected function render_transactions_tab() : void {
            $accounts          = $this->get_accounts();
            $reasons           = $this->get_reasons();
            $rows              = $this->get_transactions( 20 );
            $form_url          = admin_url( 'admin-post.php' );
            $delete_url        = admin_url( 'admin-post.php' );
            $status_breakdown  = $this->get_transaction_status_breakdown();
            $default_currency  = $this->get_default_currency();
            $status_options    = $this->get_transaction_statuses();
            $currency_prefill  = strtoupper( $default_currency );
            $exchange_prefill  = 1.0;

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
            echo $this->render_input_field( 'party', __( 'Stakeholder', 'codboost-money-manager' ), 'text', [ 'placeholder' => __( 'Client, vendor, or internal team', 'codboost-money-manager' ) ], false );
            echo $this->render_input_field( 'reference', __( 'Reference ID', 'codboost-money-manager' ), 'text', [ 'placeholder' => __( 'Invoice, receipt, or project code', 'codboost-money-manager' ) ], false );
            echo $this->render_select_field( 'status', __( 'Status', 'codboost-money-manager' ), $status_options, 'cleared' );
            echo $this->render_input_field( 'currency', __( 'Currency', 'codboost-money-manager' ), 'text', [ 'maxlength' => '10', 'value' => $currency_prefill ], true );
            echo $this->render_input_field( 'exchange_rate', __( 'Exchange Rate', 'codboost-money-manager' ), 'number', [ 'step' => '0.0001', 'min' => '0', 'value' => (string) $exchange_prefill ], true );
            echo '</div>';
            echo $this->render_textarea_field( 'note', __( 'Narrative (optional)', 'codboost-money-manager' ) );
            echo $this->render_input_field( 'tags', __( 'Tags', 'codboost-money-manager' ), 'text', [ 'placeholder' => __( 'Comma separated insights: marketing, q1, renewal…', 'codboost-money-manager' ) ], false );
            echo '<p class="cbm-form__hint">' . esc_html__( 'Amounts are stored in your base currency. Provide an exchange rate if the original currency differs.', 'codboost-money-manager' ) . '</p>';
            echo '<button type="submit" class="button button-primary cbm-button--glow">' . esc_html__( 'Save Transaction', 'codboost-money-manager' ) . '</button>';
            echo '</form>';
            echo '</section>';

            echo '<section class="cbm-panel">';
            echo '<h2>' . esc_html__( 'Latest Transactions', 'codboost-money-manager' ) . '</h2>';
            if ( empty( $rows ) ) {
                echo '<p>' . esc_html__( 'No transactions yet. Add your first entry above.', 'codboost-money-manager' ) . '</p>';
            } else {
                echo '<div class="cbm-badges">';
                foreach ( $status_breakdown as $status => $count ) {
                    printf(
                        '<span class="cbm-status-pill"><strong>%1$s</strong> %2$s</span>',
                        esc_html( $count ),
                        esc_html( ucfirst( $status ) )
                    );
                }
                echo '</div>';
                echo '<table class="cbm-table cbm-table--responsive">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__( 'Date', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Type', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Status', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Amount', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Account', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Reason', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Stakeholder', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Reference', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Tags', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Narrative', 'codboost-money-manager' ) . '</th>';
                echo '<th>' . esc_html__( 'Actions', 'codboost-money-manager' ) . '</th>';
                echo '</tr></thead><tbody>';
                foreach ( $rows as $row ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $row->transaction_date ) ) ) . '</td>';
                    echo '<td><span class="cbm-pill cbm-pill--' . esc_attr( $row->transaction_type ) . '">' . esc_html( ucfirst( $row->transaction_type ) ) . '</span></td>';
                    echo '<td>' . $this->render_status_badge( $row->status ) . '</td>';
                    echo '<td>' . esc_html( $this->format_transaction_amount( (float) $row->amount, (string) $row->currency, (float) $row->exchange_rate ) ) . '</td>';
                    echo '<td>' . esc_html( $row->account_name ?: __( 'Unassigned', 'codboost-money-manager' ) ) . '</td>';
                    echo '<td>' . esc_html( $row->reason_name ?: __( 'Unassigned', 'codboost-money-manager' ) ) . '</td>';
                    echo '<td>' . esc_html( $row->party ?: __( '—', 'codboost-money-manager' ) ) . '</td>';
                    echo '<td>' . esc_html( $row->reference ?: __( '—', 'codboost-money-manager' ) ) . '</td>';
                    echo '<td>' . $this->render_tag_badges( (string) $row->tags ) . '</td>';
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
         * Render setup tab for demo import and plugin checklist.
         */
        protected function render_setup_tab() : void {
            $form_url          = admin_url( 'admin-post.php' );
            $required_plugins  = $this->get_required_plugins();
            $demo_already_used = (bool) get_option( self::DEMO_OPTION, false );

            echo '<section class="cbm-grid cbm-grid--two">';
            echo '<div class="cbm-panel cbm-panel--elevated">';
            echo '<h2>' . esc_html__( 'Required Plugins', 'codboost-money-manager' ) . '</h2>';
            echo '<p>' . esc_html__( 'Install these essentials to unlock the full Codboost finance experience.', 'codboost-money-manager' ) . '</p>';
            echo '<ul class="cbm-plugin-checklist">';
            foreach ( $required_plugins as $plugin ) {
                $status  = $plugin['status'];
                $classes = 'cbm-plugin-checklist__item ' . ( 'active' === $status ? 'is-active' : 'is-inactive' );
                echo '<li class="' . esc_attr( $classes ) . '">';
                echo '<div>'; 
                echo '<strong>' . esc_html( $plugin['name'] ) . '</strong>';
                if ( ! empty( $plugin['description'] ) ) {
                    echo '<p>' . esc_html( $plugin['description'] ) . '</p>';
                }
                echo '</div>';
                if ( 'active' !== $status && ! empty( $plugin['action'] ) ) {
                    printf( '<a class="button" href="%1$s">%2$s</a>', esc_url( $plugin['action'] ), esc_html__( 'Install / Activate', 'codboost-money-manager' ) );
                } else {
                    echo '<span class="cbm-status-chip">' . esc_html__( 'Ready', 'codboost-money-manager' ) . '</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';

            echo '<div class="cbm-panel cbm-panel--elevated">';
            echo '<h2>' . esc_html__( 'One-Click Demo Data', 'codboost-money-manager' ) . '</h2>';
            echo '<p>' . esc_html__( 'Import storytelling-ready accounts, reasons, and multi-channel transactions to explore analytics instantly.', 'codboost-money-manager' ) . '</p>';
            echo '<form method="post" action="' . esc_url( $form_url ) . '">';
            wp_nonce_field( self::NONCE_ACTION, '_cbm_nonce' );
            echo '<input type="hidden" name="action" value="cmm_import_demo">';
            if ( $demo_already_used ) {
                echo '<p class="cbm-form__hint">' . esc_html__( 'Demo data was imported previously. Re-importing will append any missing records and refresh performance analytics.', 'codboost-money-manager' ) . '</p>';
                echo '<label class="cbm-field cbm-field--inline"><input type="checkbox" name="force" value="1"> <span>' . esc_html__( 'Re-import demo data', 'codboost-money-manager' ) . '</span></label>';
            }
            echo '<button type="submit" class="button button-primary cbm-button--glow">' . esc_html__( 'Import Codboost Demo', 'codboost-money-manager' ) . '</button>';
            echo '</form>';
            echo '</div>';
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
            $party            = isset( $_POST['party'] ) ? sanitize_text_field( wp_unslash( $_POST['party'] ) ) : '';
            $reference        = isset( $_POST['reference'] ) ? sanitize_text_field( wp_unslash( $_POST['reference'] ) ) : '';
            $status           = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : 'cleared';
            $currency         = isset( $_POST['currency'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['currency'] ) ) ) : $this->get_default_currency();
            $exchange_rate    = isset( $_POST['exchange_rate'] ) ? floatval( wp_unslash( $_POST['exchange_rate'] ) ) : 1;
            $tags             = isset( $_POST['tags'] ) ? sanitize_text_field( wp_unslash( $_POST['tags'] ) ) : '';

            if ( $amount <= 0 ) {
                $this->redirect_with_message( __( 'Amount must be greater than zero.', 'codboost-money-manager' ), 'error', 'transactions' );
            }

            if ( ! in_array( $type, [ 'income', 'outcome' ], true ) ) {
                $type = 'income';
            }

            $statuses = $this->get_transaction_statuses();
            if ( ! array_key_exists( $status, $statuses ) ) {
                $status = 'cleared';
            }

            if ( $exchange_rate <= 0 ) {
                $exchange_rate = 1;
            }

            if ( empty( $currency ) ) {
                $currency = $this->get_default_currency();
            }

            if ( strlen( $currency ) > 10 ) {
                $currency = substr( $currency, 0, 10 );
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
                'party'            => $party ?: null,
                'reference'        => $reference ?: null,
                'tags'             => $tags ?: null,
                'currency'         => $currency,
                'status'           => $status,
                'exchange_rate'    => $exchange_rate,
                'transaction_date' => $transaction_date,
                'created_at'       => current_time( 'mysql' ),
            ], [
                '%d',
                '%s',
                '%f',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%f',
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
         * Handle demo data import.
         */
        public function handle_import_demo() : void {
            $this->verify_permissions();

            $force  = isset( $_POST['force'] ) && '1' === $_POST['force'];
            $result = $this->import_demo_dataset( $force );

            if ( ! empty( $result['error'] ) ) {
                $this->redirect_with_message( $result['error'], 'error', 'setup' );
            }

            update_option( self::DEMO_OPTION, time() );

            $message = sprintf(
                /* translators: 1: number of accounts, 2: number of reasons, 3: number of transactions */
                __( 'Imported %1$s accounts, %2$s reasons, and %3$s transactions ready for analytics.', 'codboost-money-manager' ),
                number_format_i18n( (int) $result['accounts'] ),
                number_format_i18n( (int) $result['reasons'] ),
                number_format_i18n( (int) $result['transactions'] )
            );

            $this->redirect_with_message( $message, 'success', 'setup' );
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

            $results = $wpdb->get_results( "SELECT transaction_type, SUM(amount * exchange_rate) AS total FROM {$table} GROUP BY transaction_type" );
            $totals  = [ 'income' => 0.0, 'outcome' => 0.0 ];

            foreach ( $results as $row ) {
                $totals[ $row->transaction_type ] = (float) $row->total;
            }

            $totals['balance'] = $totals['income'] - $totals['outcome'];
            $totals['average_net'] = 0.0;

            $chart_data = $this->get_chart_data();
            if ( ! empty( $chart_data['net'] ) ) {
                $valid_months = array_filter( $chart_data['net'], static function ( $value ) {
                    return abs( (float) $value ) > 0.01;
                } );
                if ( ! empty( $valid_months ) ) {
                    $totals['average_net'] = array_sum( $valid_months ) / count( $valid_months );
                }
            }

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
         * Return required plugin checklist entries.
         */
        protected function get_required_plugins() : array {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';

            $plugins = [];

            $plugins[] = [
                'name'        => __( 'Codboost Money Manager', 'codboost-money-manager' ),
                'description' => __( 'Core plugin powering transactions, analytics, and the finance dashboard.', 'codboost-money-manager' ),
                'status'      => 'active',
                'action'      => '',
            ];

            $plugins[] = $this->resolve_plugin_state(
                'wordpress-importer',
                'wordpress-importer/wordpress-importer.php',
                __( 'WordPress Importer', 'codboost-money-manager' ),
                __( 'Allow importing additional demo templates or migrating data between sites.', 'codboost-money-manager' )
            );

            $plugins[] = $this->resolve_plugin_state(
                'woocommerce',
                'woocommerce/woocommerce.php',
                __( 'WooCommerce', 'codboost-money-manager' ),
                __( 'Provides enterprise-grade currency formatting and payment integrations.', 'codboost-money-manager' )
            );

            return apply_filters( 'codboost_money_manager_required_plugins', $plugins );
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

            $net_data        = [];
            $cumulative_data = [];
            $running_total   = 0.0;

            for ( $i = 11; $i >= 0; $i -- ) {
                $month_key = gmdate( 'Y-m', strtotime( '-' . $i . ' months' ) );
                $labels[]  = date_i18n( 'M Y', strtotime( $month_key . '-01' ) );

                $prepared = $wpdb->prepare(
                    "SELECT transaction_type, SUM(amount * exchange_rate) as total
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
                $net_value     = $income - $outcome;
                $net_data[]    = $net_value;
                $running_total += $net_value;
                $cumulative_data[] = $running_total;
            }

            $accounts_distribution = $wpdb->get_results(
                "SELECT a.name AS account, SUM(CASE WHEN t.transaction_type = 'income' THEN t.amount * t.exchange_rate ELSE -t.amount * t.exchange_rate END) AS balance
                FROM {$transactions_table} t
                LEFT JOIN {$accounts_table} a ON a.id = t.account_id
                GROUP BY a.name"
            );

            $reason_breakdown = $wpdb->get_results(
                "SELECT COALESCE(r.name, 'Unassigned') AS reason,
                    SUM(CASE WHEN t.transaction_type = 'income' THEN t.amount * t.exchange_rate ELSE 0 END) AS income_total,
                    SUM(CASE WHEN t.transaction_type = 'outcome' THEN t.amount * t.exchange_rate ELSE 0 END) AS outcome_total
                FROM {$transactions_table} t
                LEFT JOIN {$reasons_table} r ON r.id = t.reason_id
                GROUP BY r.name
                ORDER BY income_total DESC"
            );

            $insights = $this->build_insights_from_chart( $labels, $net_data, $income_data, $out_data );

            return [
                'labels'               => $labels,
                'income'               => $income_data,
                'outcome'              => $out_data,
                'net'                  => $net_data,
                'cumulative'           => $cumulative_data,
                'accountsDistribution' => $accounts_distribution,
                'reasonBreakdown'      => array_map( static function ( $row ) {
                    $income  = (float) $row->income_total;
                    $outcome = (float) $row->outcome_total;
                    return [
                        'reason' => (string) $row->reason,
                        'income' => $income,
                        'outcome' => $outcome,
                        'net'    => $income - $outcome,
                    ];
                }, $reason_breakdown ?? [] ),
                'insights'             => $insights,
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
         * Describe plugin activation status for checklist UI.
         */
        protected function resolve_plugin_state( string $slug, string $plugin_file, string $name, string $description ) : array {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';

            $plugin_path   = WP_PLUGIN_DIR . '/' . $plugin_file;
            $is_installed  = file_exists( $plugin_path );
            $is_active     = function_exists( 'is_plugin_active' ) && is_plugin_active( $plugin_file );
            $status        = 'missing';
            $action        = '';

            if ( $is_active ) {
                $status = 'active';
            } elseif ( $is_installed ) {
                $status = 'inactive';
                $action = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin_file ), 'activate-plugin_' . $plugin_file );
            } else {
                $status = 'missing';
                $action = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
            }

            return [
                'name'        => $name,
                'description' => $description,
                'status'      => $status,
                'action'      => $action,
            ];
        }

        /**
         * Import bundled demo dataset.
         */
        protected function import_demo_dataset( bool $force = false ) : array {
            $file = plugin_dir_path( __FILE__ ) . 'data/demo-finance-data.json';

            if ( ! file_exists( $file ) ) {
                return [
                    'accounts'     => 0,
                    'reasons'      => 0,
                    'transactions' => 0,
                    'error'        => __( 'Demo data file is missing.', 'codboost-money-manager' ),
                ];
            }

            $raw  = file_get_contents( $file );
            $data = json_decode( (string) $raw, true );

            if ( empty( $data ) || ! is_array( $data ) ) {
                return [
                    'accounts'     => 0,
                    'reasons'      => 0,
                    'transactions' => 0,
                    'error'        => __( 'Demo dataset could not be parsed.', 'codboost-money-manager' ),
                ];
            }

            $this->seed_default_terms();

            global $wpdb;
            $accounts_table     = $wpdb->prefix . 'cbm_accounts';
            $reasons_table      = $wpdb->prefix . 'cbm_reasons';
            $transactions_table = $wpdb->prefix . 'cbm_transactions';

            $created = [ 'accounts' => 0, 'reasons' => 0, 'transactions' => 0, 'error' => '' ];

            $accounts_map = [];
            foreach ( $this->get_accounts() as $account ) {
                $accounts_map[ strtolower( $account->name ) ] = (int) $account->id;
            }

            if ( ! empty( $data['accounts'] ) && is_array( $data['accounts'] ) ) {
                foreach ( $data['accounts'] as $account ) {
                    $name        = sanitize_text_field( $account['name'] ?? '' );
                    $description = sanitize_textarea_field( $account['description'] ?? '' );
                    if ( empty( $name ) ) {
                        continue;
                    }

                    $key = strtolower( $name );
                    if ( isset( $accounts_map[ $key ] ) ) {
                        continue;
                    }

                    $wpdb->insert( $accounts_table, [
                        'name'        => $name,
                        'description' => $description,
                    ] );
                    $accounts_map[ $key ] = (int) $wpdb->insert_id;
                    $created['accounts'] ++;
                }
            }

            $reasons_map = [];
            foreach ( $this->get_reasons() as $reason ) {
                $reasons_map[ strtolower( $reason->name ) ] = (int) $reason->id;
            }

            if ( ! empty( $data['reasons'] ) && is_array( $data['reasons'] ) ) {
                foreach ( $data['reasons'] as $reason ) {
                    $name        = sanitize_text_field( $reason['name'] ?? '' );
                    $description = sanitize_textarea_field( $reason['description'] ?? '' );
                    $type        = sanitize_key( $reason['type'] ?? 'general' );
                    if ( empty( $name ) ) {
                        continue;
                    }

                    $key = strtolower( $name );
                    if ( isset( $reasons_map[ $key ] ) ) {
                        continue;
                    }

                    if ( ! in_array( $type, [ 'income', 'outcome', 'general' ], true ) ) {
                        $type = 'general';
                    }

                    $wpdb->insert( $reasons_table, [
                        'name'        => $name,
                        'type'        => $type,
                        'description' => $description,
                    ] );
                    $reasons_map[ $key ] = (int) $wpdb->insert_id;
                    $created['reasons'] ++;
                }
            }

            if ( empty( $data['transactions'] ) || ! is_array( $data['transactions'] ) ) {
                return $created;
            }

            if ( ! $force ) {
                $existing = (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$transactions_table}" );
                if ( $existing > 0 ) {
                    $created['transactions'] = 0;
                    $created['error']        = __( 'Demo data skipped because transactions already exist. Enable re-import to append the scenario.', 'codboost-money-manager' );
                    return $created;
                }
            }

            foreach ( $data['transactions'] as $transaction ) {
                $type          = sanitize_key( $transaction['type'] ?? 'income' );
                $amount        = isset( $transaction['amount'] ) ? (float) $transaction['amount'] : 0.0;
                $currency      = strtoupper( sanitize_text_field( $transaction['currency'] ?? $this->get_default_currency() ) );
                $exchange_rate = isset( $transaction['exchange_rate'] ) ? (float) $transaction['exchange_rate'] : 1.0;
                $status        = sanitize_key( $transaction['status'] ?? 'cleared' );
                $note          = sanitize_textarea_field( $transaction['note'] ?? '' );
                $party         = sanitize_text_field( $transaction['party'] ?? '' );
                $reference     = sanitize_text_field( $transaction['reference'] ?? '' );
                $tags          = sanitize_text_field( $transaction['tags'] ?? '' );
                $account_name  = isset( $transaction['account'] ) ? strtolower( sanitize_text_field( $transaction['account'] ) ) : '';
                $reason_name   = isset( $transaction['reason'] ) ? strtolower( sanitize_text_field( $transaction['reason'] ) ) : '';
                $date_string   = sanitize_text_field( $transaction['date'] ?? '' );

                if ( $amount <= 0 ) {
                    continue;
                }

                if ( ! in_array( $type, [ 'income', 'outcome' ], true ) ) {
                    $type = 'income';
                }

                $statuses = $this->get_transaction_statuses();
                if ( ! array_key_exists( $status, $statuses ) ) {
                    $status = 'cleared';
                }

                $account_id = $account_name && isset( $accounts_map[ $account_name ] ) ? $accounts_map[ $account_name ] : null;
                $reason_id  = $reason_name && isset( $reasons_map[ $reason_name ] ) ? $reasons_map[ $reason_name ] : null;

                $timestamp = strtotime( $date_string );
                $date      = $timestamp ? wp_date( 'Y-m-d', $timestamp ) : wp_date( 'Y-m-d' );

                $wpdb->insert( $transactions_table, [
                    'user_id'          => get_current_user_id(),
                    'transaction_type' => $type,
                    'amount'           => $amount,
                    'account_id'       => $account_id,
                    'reason_id'        => $reason_id,
                    'note'             => $note,
                    'party'            => $party ?: null,
                    'reference'        => $reference ?: null,
                    'tags'             => $tags ?: null,
                    'currency'         => $currency ?: $this->get_default_currency(),
                    'status'           => $status,
                    'exchange_rate'    => $exchange_rate > 0 ? $exchange_rate : 1,
                    'transaction_date' => $date,
                    'created_at'       => current_time( 'mysql' ),
                ] );

                $created['transactions'] ++;
            }

            return $created;
        }

        /**
         * Format currency helper.
         */
        protected function format_currency( float $value, string $currency = '' ) : string {
            $currency = $currency ?: $this->get_default_currency();
            $symbol   = $this->get_currency_symbol( $currency );

            return sprintf( '%s %s', $symbol, number_format_i18n( $value, 2 ) );
        }

        /**
         * Format amounts for display, including original currency if different.
         */
        protected function format_transaction_amount( float $amount, string $currency, float $exchange_rate ) : string {
            $currency      = $currency ? strtoupper( $currency ) : $this->get_default_currency();
            $base_currency = $this->get_default_currency();
            $exchange_rate = $exchange_rate > 0 ? $exchange_rate : 1;
            $base_value    = $amount * $exchange_rate;

            $display = $this->format_currency( $base_value, $base_currency );

            if ( $currency !== $base_currency ) {
                $display .= sprintf(
                    ' (%1$s %2$s @ %3$s)',
                    $currency,
                    number_format_i18n( $amount, 2 ),
                    number_format_i18n( $exchange_rate, 3 )
                );
            }

            return $display;
        }

        /**
         * Determine default currency.
         */
        protected function get_default_currency() : string {
            $currency = get_option( 'woocommerce_currency' );
            if ( empty( $currency ) ) {
                $currency = 'DZD';
            }

            $currency = apply_filters( 'codboost_money_manager_default_currency', $currency );

            return strtoupper( (string) $currency );
        }

        /**
         * Resolve currency symbol for display.
         */
        protected function get_currency_symbol( string $currency ) : string {
            $currency = strtoupper( $currency );

            if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
                $symbol = get_woocommerce_currency_symbol( $currency );
                if ( $symbol ) {
                    return (string) $symbol;
                }
            }

            $fallback = [
                'DZD' => 'DA',
                'USD' => '$',
                'EUR' => '€',
                'GBP' => '£',
                'CAD' => 'C$',
                'AUD' => 'A$',
                'SAR' => '﷼',
                'AED' => 'د.إ',
            ];

            $symbol = $fallback[ $currency ] ?? $currency;

            return apply_filters( 'codboost_money_manager_currency_symbol', $symbol, $currency );
        }

        /**
         * Compute status options.
         */
        protected function get_transaction_statuses() : array {
            $statuses = [
                'cleared'   => __( 'Cleared', 'codboost-money-manager' ),
                'pending'   => __( 'Pending', 'codboost-money-manager' ),
                'scheduled' => __( 'Scheduled', 'codboost-money-manager' ),
                'disputed'  => __( 'Disputed', 'codboost-money-manager' ),
            ];

            return apply_filters( 'codboost_money_manager_statuses', $statuses );
        }

        /**
         * Aggregate status counts for the dashboard.
         */
        protected function get_transaction_status_breakdown() : array {
            global $wpdb;
            $table    = $wpdb->prefix . 'cbm_transactions';
            $statuses = array_fill_keys( array_keys( $this->get_transaction_statuses() ), 0 );

            $results = $wpdb->get_results( "SELECT status, COUNT(id) AS total FROM {$table} GROUP BY status" );
            foreach ( $results as $row ) {
                $key = sanitize_key( $row->status );
                if ( isset( $statuses[ $key ] ) ) {
                    $statuses[ $key ] = (int) $row->total;
                }
            }

            return $statuses;
        }

        /**
         * Render status badge HTML.
         */
        protected function render_status_badge( ?string $status ) : string {
            $status  = $status ? sanitize_key( $status ) : 'cleared';
            $labels  = $this->get_transaction_statuses();
            $label   = $labels[ $status ] ?? ucfirst( $status );

            return '<span class="cbm-status cbm-status--' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
        }

        /**
         * Render tag chips from comma separated list.
         */
        protected function render_tag_badges( string $tags ) : string {
            $parts = array_filter( array_map( 'trim', explode( ',', $tags ) ) );

            if ( empty( $parts ) ) {
                return '<span class="cbm-muted">' . esc_html__( '—', 'codboost-money-manager' ) . '</span>';
            }

            $html = '<span class="cbm-tags">';
            foreach ( $parts as $tag ) {
                $html .= '<span class="cbm-tag">' . esc_html( $tag ) . '</span>';
            }
            $html .= '</span>';

            return $html;
        }

        /**
         * Compose story-driven insights from monthly data.
         */
        protected function build_insights_from_chart( array $labels, array $net, array $income, array $outcome ) : array {
            $insights = [];

            if ( empty( $net ) ) {
                return $insights;
            }

            $base_currency = $this->get_default_currency();

            $max_net   = max( $net );
            $min_net   = min( $net );
            $max_index = array_search( $max_net, $net, true );
            $min_index = array_search( $min_net, $net, true );

            if ( false !== $max_index && isset( $labels[ $max_index ] ) ) {
                $insights[] = [
                    'title'  => __( 'Best Month', 'codboost-money-manager' ),
                    'detail' => sprintf(
                        /* translators: 1: month label, 2: amount */
                        __( '%1$s delivered %2$s net contribution.', 'codboost-money-manager' ),
                        $labels[ $max_index ],
                        $this->format_currency( $max_net, $base_currency )
                    ),
                ];
            }

            if ( false !== $min_index && isset( $labels[ $min_index ] ) ) {
                $insights[] = [
                    'title'  => __( 'Pressure Point', 'codboost-money-manager' ),
                    'detail' => sprintf(
                        __( '%1$s saw a %2$s dip — review spending or pricing.', 'codboost-money-manager' ),
                        $labels[ $min_index ],
                        $this->format_currency( abs( $min_net ), $base_currency )
                    ),
                ];
            }

            $recent_net    = end( $net );
            $previous_net  = count( $net ) > 1 ? $net[ count( $net ) - 2 ] : 0;
            $momentum_diff = $recent_net - $previous_net;
            $momentum_copy = $momentum_diff >= 0
                ? sprintf( __( 'Momentum up %s vs last month.', 'codboost-money-manager' ), $this->format_currency( $momentum_diff, $base_currency ) )
                : sprintf( __( 'Momentum down %s vs last month.', 'codboost-money-manager' ), $this->format_currency( abs( $momentum_diff ), $base_currency ) );
            $insights[]     = [
                'title'  => __( 'Momentum', 'codboost-money-manager' ),
                'detail' => $momentum_copy,
            ];

            $income_months = array_filter( $income, static function ( $value ) {
                return abs( (float) $value ) > 0.01;
            } );
            $out_months = array_filter( $outcome, static function ( $value ) {
                return abs( (float) $value ) > 0.01;
            } );

            if ( ! empty( $income_months ) ) {
                $avg_income  = array_sum( $income_months ) / count( $income_months );
                $avg_outcome = ! empty( $out_months ) ? array_sum( $out_months ) / count( $out_months ) : 0;
                $insights[]  = [
                    'title'  => __( 'Monthly Run Rate', 'codboost-money-manager' ),
                    'detail' => sprintf(
                        __( '%1$s inflow vs %2$s outflow on average.', 'codboost-money-manager' ),
                        $this->format_currency( $avg_income, $base_currency ),
                        $this->format_currency( $avg_outcome, $base_currency )
                    ),
                ];
            }

            return $insights;
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
