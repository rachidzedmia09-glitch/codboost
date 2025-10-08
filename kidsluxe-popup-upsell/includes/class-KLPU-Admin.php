<?php
/**
 * Admin integration.
 */

defined( 'ABSPATH' ) || exit;

class KLPU_Admin {
    private static ?KLPU_Admin $instance = null;

    public static function get_instance(): KLPU_Admin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_product_data_tab' ] );
        add_action( 'woocommerce_product_data_panels', [ $this, 'render_product_data_panel' ] );
        add_action( 'save_post_product', [ $this, 'save_product_data' ], 10, 2 );
        add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_product_data_tab( array $tabs ): array {
        $tabs['klpu'] = [
            'label'    => __( 'Pop-Up Upsell Kids-Luxe', 'kidsluxe-popup-upsell' ),
            'target'   => 'klpu_product_data',
            'class'    => [ 'show_if_simple', 'show_if_variable', 'klpu-data-tab' ],
            'priority' => 80,
        ];

        return $tabs;
    }

    public function render_product_data_panel(): void {
        global $post;

        if ( ! $post ) {
            return;
        }

        $product_id = (int) $post->ID;
        $settings   = KLPU_get_product_settings( $product_id );
        $triggers   = array_map( 'sanitize_text_field', (array) $settings['triggers'] );

        wp_nonce_field( 'klpu_save_product', 'klpu_meta_nonce' );
        ?>
        <div id="klpu_product_data" class="panel woocommerce_options_panel hidden">
            <div class="options_group">
                <?php
                woocommerce_wp_checkbox(
                    [
                        'id'          => '_klpu_enabled',
                        'label'       => __( 'Activer le pop-up pour ce produit', 'kidsluxe-popup-upsell' ),
                        'description' => __( 'Active l’upsell Kids-Luxe pour ce produit.', 'kidsluxe-popup-upsell' ),
                        'value'       => $settings['enabled'] ? 'yes' : 'no',
                    ]
                );

                woocommerce_wp_text_input(
                    [
                        'id'                => '_klpu_offer_product_id',
                        'label'             => __( 'Produit à proposer', 'kidsluxe-popup-upsell' ),
                        'class'             => 'wc-product-search',
                        'type'              => 'hidden',
                        'value'             => $settings['offer_product'] ?: '',
                        'data-placeholder'  => esc_attr__( 'Rechercher un produit…', 'kidsluxe-popup-upsell' ),
                        'data-action'       => 'woocommerce_json_search_products_and_variations',
                        'desc_tip'          => true,
                        'description'       => __( 'Sélectionnez le produit à proposer dans le pop-up.', 'kidsluxe-popup-upsell' ),
                    ]
                );
                ?>
            </div>

            <div class="options_group">
                <p class="form-field">
                    <label><?php esc_html_e( 'Type de profit', 'kidsluxe-popup-upsell' ); ?></label>
                    <span class="woocommerce-input-wrapper">
                        <label><input type="radio" name="_klpu_profit_type" value="montant_fixe" <?php checked( 'montant_fixe', $settings['profit_type'] ); ?> /> <?php esc_html_e( 'Montant fixe', 'kidsluxe-popup-upsell' ); ?></label><br />
                        <label><input type="radio" name="_klpu_profit_type" value="pourcentage" <?php checked( 'pourcentage', $settings['profit_type'] ); ?> /> <?php esc_html_e( 'Pourcentage', 'kidsluxe-popup-upsell' ); ?></label>
                    </span>
                </p>

                <?php
                woocommerce_wp_text_input(
                    [
                        'id'          => '_klpu_profit_value',
                        'label'       => __( 'Valeur du profit', 'kidsluxe-popup-upsell' ),
                        'type'        => 'number',
                        'custom_attributes' => [
                            'step' => '0.01',
                            'min'  => '0',
                        ],
                        'value'       => $settings['profit_value'],
                        'description' => __( 'Montant ou pourcentage ajouté au prix du produit proposé.', 'kidsluxe-popup-upsell' ),
                        'desc_tip'    => true,
                    ]
                );
                ?>
            </div>

            <div class="options_group">
                <p class="form-field">
                    <label><?php esc_html_e( 'Déclencheurs', 'kidsluxe-popup-upsell' ); ?></label>
                    <span class="woocommerce-input-wrapper">
                        <label><input type="checkbox" name="_klpu_triggers[]" value="add_to_cart" <?php checked( in_array( 'add_to_cart', $triggers, true ) ); ?> /> <?php esc_html_e( 'Ajouter au panier', 'kidsluxe-popup-upsell' ); ?></label><br />
                        <label><input type="checkbox" name="_klpu_triggers[]" value="buy_now" <?php checked( in_array( 'buy_now', $triggers, true ) ); ?> /> <?php esc_html_e( 'Acheter maintenant', 'kidsluxe-popup-upsell' ); ?></label>
                    </span>
                </p>
            </div>
        </div>
        <?php
    }

    public function save_product_data( int $post_id, \WP_Post $post ): void {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_product', $post_id ) ) {
            return;
        }

        if ( ! isset( $_POST['klpu_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['klpu_meta_nonce'] ) ), 'klpu_save_product' ) ) {
            return;
        }

        $enabled = isset( $_POST['_klpu_enabled'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['_klpu_enabled'] ) );
        update_post_meta( $post_id, '_klpu_enabled', $enabled ? 'yes' : 'no' );

        $offer_product = isset( $_POST['_klpu_offer_product_id'] ) ? absint( wp_unslash( $_POST['_klpu_offer_product_id'] ) ) : 0;
        update_post_meta( $post_id, '_klpu_offer_product_id', $offer_product );

        $profit_type = isset( $_POST['_klpu_profit_type'] ) ? sanitize_text_field( wp_unslash( $_POST['_klpu_profit_type'] ) ) : 'montant_fixe';
        if ( ! in_array( $profit_type, [ 'montant_fixe', 'pourcentage' ], true ) ) {
            $profit_type = 'montant_fixe';
        }
        update_post_meta( $post_id, '_klpu_profit_type', $profit_type );

        $profit_value = isset( $_POST['_klpu_profit_value'] ) ? floatval( wp_unslash( $_POST['_klpu_profit_value'] ) ) : 0.0;
        update_post_meta( $post_id, '_klpu_profit_value', $profit_value );

        $triggers = [];
        if ( isset( $_POST['_klpu_triggers'] ) && is_array( $_POST['_klpu_triggers'] ) ) {
            foreach ( $_POST['_klpu_triggers'] as $trigger ) {
                $trigger = sanitize_text_field( wp_unslash( $trigger ) );
                if ( in_array( $trigger, [ 'add_to_cart', 'buy_now' ], true ) ) {
                    $triggers[] = $trigger;
                }
            }
        }
        update_post_meta( $post_id, '_klpu_triggers', $triggers );
    }

    public function register_settings_page(): void {
        add_submenu_page(
            'woocommerce-marketing',
            __( 'Kids-Luxe Pop-Up Upsell', 'kidsluxe-popup-upsell' ),
            __( 'Kids-Luxe Pop-Up', 'kidsluxe-popup-upsell' ),
            'manage_woocommerce',
            'kidsluxe-popup-upsell',
            [ $this, 'render_settings_page' ]
        );
    }

    public function register_settings(): void {
        register_setting( 'klpu_settings_group', 'klpu_settings', [ $this, 'sanitize_settings' ] );

        add_settings_section(
            'klpu_general_section',
            __( 'Configuration générale', 'kidsluxe-popup-upsell' ),
            '__return_false',
            'kidsluxe-popup-upsell'
        );

        add_settings_field(
            'klpu_enabled',
            __( 'Activer globalement', 'kidsluxe-popup-upsell' ),
            [ $this, 'render_field_enabled' ],
            'kidsluxe-popup-upsell',
            'klpu_general_section'
        );

        add_settings_field(
            'klpu_buy_now_selector',
            __( 'Sélecteur CSS « Acheter maintenant »', 'kidsluxe-popup-upsell' ),
            [ $this, 'render_field_buy_now_selector' ],
            'kidsluxe-popup-upsell',
            'klpu_general_section'
        );

        add_settings_field(
            'klpu_popup_texts',
            __( 'Textes du pop-up', 'kidsluxe-popup-upsell' ),
            [ $this, 'render_field_popup_texts' ],
            'kidsluxe-popup-upsell',
            'klpu_general_section'
        );

        add_settings_field(
            'klpu_show_profit_notice',
            __( 'Afficher la mention de marge', 'kidsluxe-popup-upsell' ),
            [ $this, 'render_field_show_profit_notice' ],
            'kidsluxe-popup-upsell',
            'klpu_general_section'
        );
    }

    public function sanitize_settings( array $input ): array {
        $defaults = KLPU_get_default_options();
        $sanitized = $defaults;

        $sanitized['enabled']             = isset( $input['enabled'] ) && '1' === sanitize_text_field( wp_unslash( $input['enabled'] ?? '' ) );
        $sanitized['buy_now_selector']    = isset( $input['buy_now_selector'] ) ? sanitize_text_field( wp_unslash( $input['buy_now_selector'] ) ) : $defaults['buy_now_selector'];
        $sanitized['popup_title']         = isset( $input['popup_title'] ) ? sanitize_text_field( wp_unslash( $input['popup_title'] ) ) : $defaults['popup_title'];
        $sanitized['popup_subtitle']      = isset( $input['popup_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $input['popup_subtitle'] ) ) : $defaults['popup_subtitle'];
        $sanitized['popup_accept_label']  = isset( $input['popup_accept_label'] ) ? sanitize_text_field( wp_unslash( $input['popup_accept_label'] ) ) : $defaults['popup_accept_label'];
        $sanitized['popup_decline_label'] = isset( $input['popup_decline_label'] ) ? sanitize_text_field( wp_unslash( $input['popup_decline_label'] ) ) : $defaults['popup_decline_label'];
        $sanitized['show_profit_notice']  = isset( $input['show_profit_notice'] ) && '1' === sanitize_text_field( wp_unslash( $input['show_profit_notice'] ?? '' ) );

        return $sanitized;
    }

    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $options = KLPU_get_options();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Kids-Luxe Pop-Up Upsell', 'kidsluxe-popup-upsell' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'klpu_settings_group' );
                do_settings_sections( 'kidsluxe-popup-upsell' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_field_enabled(): void {
        $options = KLPU_get_options();
        ?>
        <label>
            <input type="checkbox" name="klpu_settings[enabled]" value="1" <?php checked( $options['enabled'] ); ?> />
            <?php esc_html_e( 'Activer tous les pop-ups Kids-Luxe.', 'kidsluxe-popup-upsell' ); ?>
        </label>
        <?php
    }

    public function render_field_buy_now_selector(): void {
        $options = KLPU_get_options();
        ?>
        <input type="text" class="regular-text" name="klpu_settings[buy_now_selector]" value="<?php echo esc_attr( $options['buy_now_selector'] ); ?>" />
        <p class="description"><?php esc_html_e( 'Définissez le sélecteur CSS pour détecter le bouton « Acheter maintenant ».', 'kidsluxe-popup-upsell' ); ?></p>
        <?php
    }

    public function render_field_popup_texts(): void {
        $options = KLPU_get_options();
        ?>
        <p>
            <label for="klpu_popup_title"><strong><?php esc_html_e( 'Titre', 'kidsluxe-popup-upsell' ); ?></strong></label><br />
            <input type="text" class="regular-text" id="klpu_popup_title" name="klpu_settings[popup_title]" value="<?php echo esc_attr( $options['popup_title'] ); ?>" />
        </p>
        <p>
            <label for="klpu_popup_subtitle"><strong><?php esc_html_e( 'Sous-titre', 'kidsluxe-popup-upsell' ); ?></strong></label><br />
            <textarea class="large-text" id="klpu_popup_subtitle" name="klpu_settings[popup_subtitle]" rows="3"><?php echo esc_textarea( $options['popup_subtitle'] ); ?></textarea>
        </p>
        <p>
            <label for="klpu_popup_accept_label"><strong><?php esc_html_e( 'Bouton accepter', 'kidsluxe-popup-upsell' ); ?></strong></label><br />
            <input type="text" class="regular-text" id="klpu_popup_accept_label" name="klpu_settings[popup_accept_label]" value="<?php echo esc_attr( $options['popup_accept_label'] ); ?>" />
        </p>
        <p>
            <label for="klpu_popup_decline_label"><strong><?php esc_html_e( 'Bouton refuser', 'kidsluxe-popup-upsell' ); ?></strong></label><br />
            <input type="text" class="regular-text" id="klpu_popup_decline_label" name="klpu_settings[popup_decline_label]" value="<?php echo esc_attr( $options['popup_decline_label'] ); ?>" />
        </p>
        <?php
    }

    public function render_field_show_profit_notice(): void {
        $options = KLPU_get_options();
        ?>
        <label>
            <input type="checkbox" name="klpu_settings[show_profit_notice]" value="1" <?php checked( $options['show_profit_notice'] ); ?> />
            <?php esc_html_e( 'Afficher l’information « inclut une marge Kids-Luxe ».', 'kidsluxe-popup-upsell' ); ?>
        </label>
        <?php
    }
}
