<?php
/**
 * Plugin Name: Marketing Tools by Codboost
 * Plugin URI: https://kids-luxe.com/
 * Description: Suite d'outils marketing premium (ventes croisées, montées/baissées en gamme et promotions pop-up) pensée pour Kids-luxe.com.
 * Version: 1.0.0
 * Author: Codboost
 * Author URI: https://codboost.pro/
 * License: GPL2
 * Text Domain: codboost-marketing-tools
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Codboost_Marketing_Tools' ) ) {
    class Codboost_Marketing_Tools {
        const OPTION_KEY = 'codboost_marketing_settings';

        /**
         * Valeurs par défaut pour les options du plugin.
         *
         * @var array<string, mixed>
         */
        protected $default_settings = array(
            'enable_cross_sell' => 1,
            'enable_upsell'     => 1,
            'enable_downsell'   => 1,
            'enable_popup'      => 1,
            'accent_color'      => '#d4af37',
            'background_color'  => '#111111',
            'button_color'      => '#f1c40f',
            'popup_delay'       => 6,
            'popup_title'       => "Offre exclusive",
            'popup_message'     => "Profitez d'une remise exceptionnelle sur notre sélection Kids Luxe.",
            'popup_cta_text'    => "Je découvre",
            'popup_cta_link'    => '/boutique/',
            'cross_sell_title'  => 'Vous aimerez aussi',
            'upsell_title'      => 'Le luxe absolu pour vous',
            'downsell_title'    => 'Alternatives élégantes à petit prix',
        );

        /**
         * Instance singleton.
         *
         * @var Codboost_Marketing_Tools
         */
        protected static $instance;

        /**
         * Récupère l'instance unique du plugin.
         */
        public static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructeur.
         */
        private function __construct() {
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
            add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_assets' ) );
            add_action( 'wp_footer', array( $this, 'render_popup' ) );

            add_action( 'woocommerce_after_single_product_summary', array( $this, 'render_upsell_block' ), 9 );
            add_action( 'woocommerce_after_single_product_summary', array( $this, 'render_cross_sell_block' ), 11 );
            add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'render_downsell_block' ) );
        }

        /**
         * Actions à l'activation du plugin.
         */
        public static function activate_plugin() {
            $instance = self::instance();
            $defaults = $instance->default_settings;
            $settings = get_option( self::OPTION_KEY, array() );
            if ( empty( $settings ) || ! is_array( $settings ) ) {
                update_option( self::OPTION_KEY, $defaults );
            } else {
                update_option( self::OPTION_KEY, wp_parse_args( $settings, $defaults ) );
            }
        }

        /**
         * Charge les traductions.
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'codboost-marketing-tools', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        /**
         * Retourne la configuration du plugin.
         */
        public function get_settings() {
            $options = get_option( self::OPTION_KEY, array() );

            return wp_parse_args( $options, $this->default_settings );
        }

        /**
         * Déclare la page d'options dans l'administration.
         */
        public function register_settings_page() {
            add_menu_page(
                __( 'Marketing Kids Luxe', 'codboost-marketing-tools' ),
                __( 'Marketing Kids Luxe', 'codboost-marketing-tools' ),
                'manage_options',
                'codboost-marketing-tools',
                array( $this, 'render_settings_page' ),
                'dashicons-megaphone',
                58
            );
        }

        /**
         * Enregistre les réglages.
         */
        public function register_settings() {
            register_setting( 'codboost_marketing_group', self::OPTION_KEY, array( $this, 'sanitize_settings' ) );

            add_settings_section(
                'codboost_marketing_main_section',
                __( 'Configuration générale', 'codboost-marketing-tools' ),
                '__return_null',
                'codboost_marketing_settings'
            );

            $fields = array(
                'enable_cross_sell' => __( 'Activer les ventes croisées', 'codboost-marketing-tools' ),
                'enable_upsell'     => __( 'Activer les montées en gamme', 'codboost-marketing-tools' ),
                'enable_downsell'   => __( 'Activer les baisses en gamme', 'codboost-marketing-tools' ),
                'enable_popup'      => __( 'Activer la promotion pop-up', 'codboost-marketing-tools' ),
            );

            foreach ( $fields as $field => $label ) {
                add_settings_field(
                    $field,
                    $label,
                    array( $this, 'render_toggle_field' ),
                    'codboost_marketing_settings',
                    'codboost_marketing_main_section',
                    array( 'label_for' => $field )
                );
            }

            add_settings_section(
                'codboost_marketing_style',
                __( 'Identité visuelle', 'codboost-marketing-tools' ),
                '__return_null',
                'codboost_marketing_settings'
            );

            $color_fields = array(
                'accent_color'     => __( 'Couleur principale', 'codboost-marketing-tools' ),
                'background_color' => __( 'Couleur de fond', 'codboost-marketing-tools' ),
                'button_color'     => __( 'Couleur des boutons', 'codboost-marketing-tools' ),
            );

            foreach ( $color_fields as $field => $label ) {
                add_settings_field(
                    $field,
                    $label,
                    array( $this, 'render_color_field' ),
                    'codboost_marketing_settings',
                    'codboost_marketing_style',
                    array( 'label_for' => $field )
                );
            }

            add_settings_section(
                'codboost_marketing_texts',
                __( 'Contenus personnalisés', 'codboost-marketing-tools' ),
                '__return_null',
                'codboost_marketing_settings'
            );

            $text_fields = array(
                'cross_sell_title' => __( 'Titre des ventes croisées', 'codboost-marketing-tools' ),
                'upsell_title'     => __( 'Titre des montées en gamme', 'codboost-marketing-tools' ),
                'downsell_title'   => __( 'Titre des baisses en gamme', 'codboost-marketing-tools' ),
                'popup_title'      => __( 'Titre du pop-up', 'codboost-marketing-tools' ),
            );

            foreach ( $text_fields as $field => $label ) {
                add_settings_field(
                    $field,
                    $label,
                    array( $this, 'render_text_field' ),
                    'codboost_marketing_settings',
                    'codboost_marketing_texts',
                    array( 'label_for' => $field )
                );
            }

            add_settings_field(
                'popup_message',
                __( 'Message du pop-up', 'codboost-marketing-tools' ),
                array( $this, 'render_textarea_field' ),
                'codboost_marketing_settings',
                'codboost_marketing_texts',
                array( 'label_for' => 'popup_message' )
            );

            add_settings_field(
                'popup_cta_text',
                __( 'Texte du bouton du pop-up', 'codboost-marketing-tools' ),
                array( $this, 'render_text_field' ),
                'codboost_marketing_settings',
                'codboost_marketing_texts',
                array( 'label_for' => 'popup_cta_text' )
            );

            add_settings_field(
                'popup_cta_link',
                __( 'Lien du pop-up', 'codboost-marketing-tools' ),
                array( $this, 'render_text_field' ),
                'codboost_marketing_settings',
                'codboost_marketing_texts',
                array( 'label_for' => 'popup_cta_link' )
            );

            add_settings_field(
                'popup_delay',
                __( 'Délai du pop-up (secondes)', 'codboost-marketing-tools' ),
                array( $this, 'render_number_field' ),
                'codboost_marketing_settings',
                'codboost_marketing_texts',
                array( 'label_for' => 'popup_delay', 'min' => 0, 'max' => 60 )
            );
        }

        /**
         * Nettoie et valide les options.
         *
         * @param array<string, mixed> $settings Paramètres soumis.
         */
        public function sanitize_settings( $settings ) {
            $clean = $this->get_settings();

            $boolean_fields = array( 'enable_cross_sell', 'enable_upsell', 'enable_downsell', 'enable_popup' );
            foreach ( $boolean_fields as $field ) {
                $clean[ $field ] = isset( $settings[ $field ] ) ? 1 : 0;
            }

            $color_fields = array( 'accent_color', 'background_color', 'button_color' );
            foreach ( $color_fields as $field ) {
                if ( isset( $settings[ $field ] ) && preg_match( '/^#([0-9a-f]{3}){1,2}$/i', $settings[ $field ] ) ) {
                    $clean[ $field ] = $settings[ $field ];
                }
            }

            $text_fields = array( 'cross_sell_title', 'upsell_title', 'downsell_title', 'popup_title', 'popup_cta_text', 'popup_cta_link' );
            foreach ( $text_fields as $field ) {
                if ( isset( $settings[ $field ] ) ) {
                    $clean[ $field ] = sanitize_text_field( $settings[ $field ] );
                }
            }

            if ( isset( $settings['popup_message'] ) ) {
                $clean['popup_message'] = wp_kses_post( $settings['popup_message'] );
            }

            if ( isset( $settings['popup_delay'] ) ) {
                $clean['popup_delay'] = max( 0, min( 60, (int) $settings['popup_delay'] ) );
            }

            return $clean;
        }

        /**
         * Affiche la page de réglages.
         */
        public function render_settings_page() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $settings = $this->get_settings();
            ?>
            <div class="wrap codboost-marketing-admin">
                <h1><?php esc_html_e( 'Marketing Kids Luxe – Paramètres', 'codboost-marketing-tools' ); ?></h1>
                <p class="description">
                    <?php esc_html_e( 'Personnalisez la suite marketing (ventes croisées, montées et baisses en gamme, pop-up promotionnel) pour une expérience luxueuse et performante.', 'codboost-marketing-tools' ); ?>
                </p>
                <form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
                    <?php
                    settings_fields( 'codboost_marketing_group' );
                    do_settings_sections( 'codboost_marketing_settings' );
                    submit_button( __( 'Enregistrer les réglages', 'codboost-marketing-tools' ) );
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Rendu d'un bouton toggle.
         */
        public function render_toggle_field( $args ) {
            $settings = $this->get_settings();
            $field    = $args['label_for'];
            ?>
            <label class="codboost-switch">
                <input type="checkbox" id="<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( self::OPTION_KEY . "[$field]" ); ?>" value="1" <?php checked( 1, (int) $settings[ $field ] ); ?> />
                <span class="codboost-slider"></span>
            </label>
            <?php
        }

        /**
         * Rendu d'un champ couleur.
         */
        public function render_color_field( $args ) {
            $settings = $this->get_settings();
            $field    = $args['label_for'];
            ?>
            <input type="text" class="codboost-color-field" id="<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( self::OPTION_KEY . "[$field]" ); ?>" value="<?php echo esc_attr( $settings[ $field ] ); ?>" data-default-color="<?php echo esc_attr( $this->default_settings[ $field ] ); ?>" />
            <?php
        }

        /**
         * Rendu d'un champ texte.
         */
        public function render_text_field( $args ) {
            $settings = $this->get_settings();
            $field    = $args['label_for'];
            ?>
            <input type="text" class="regular-text" id="<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( self::OPTION_KEY . "[$field]" ); ?>" value="<?php echo esc_attr( $settings[ $field ] ); ?>" />
            <?php
        }

        /**
         * Rendu d'un champ de texte long.
         */
        public function render_textarea_field( $args ) {
            $settings = $this->get_settings();
            $field    = $args['label_for'];
            ?>
            <textarea class="large-text" rows="4" id="<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( self::OPTION_KEY . "[$field]" ); ?>"><?php echo esc_textarea( $settings[ $field ] ); ?></textarea>
            <?php
        }

        /**
         * Rendu d'un champ numérique.
         */
        public function render_number_field( $args ) {
            $settings = $this->get_settings();
            $field    = $args['label_for'];
            $min      = isset( $args['min'] ) ? (int) $args['min'] : 0;
            $max      = isset( $args['max'] ) ? (int) $args['max'] : 60;
            ?>
            <input type="number" id="<?php echo esc_attr( $field ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" name="<?php echo esc_attr( self::OPTION_KEY . "[$field]" ); ?>" value="<?php echo esc_attr( $settings[ $field ] ); ?>" />
            <?php
        }

        /**
         * Enfile les assets côté administration.
         */
        public function enqueue_admin_assets( $hook ) {
            if ( 'toplevel_page_codboost-marketing-tools' !== $hook ) {
                return;
            }

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );

            wp_add_inline_style(
                'wp-color-picker',
                '.codboost-switch{position:relative;display:inline-block;width:52px;height:26px}.codboost-switch input{opacity:0;width:0;height:0}.codboost-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#ccc;transition:.4s;border-radius:26px}.codboost-slider:before{position:absolute;content:"";height:20px;width:20px;left:4px;bottom:3px;background-color:white;transition:.4s;border-radius:50%}.codboost-switch input:checked+.codboost-slider{background-color:#111}.codboost-switch input:checked+.codboost-slider:before{transform:translateX(26px)}.codboost-marketing-admin .description{max-width:640px;font-size:14px;color:#555}'
            );
        }

        /**
         * Enfile les assets front-end.
         */
        public function enqueue_front_assets() {
            if ( ! class_exists( 'WooCommerce' ) ) {
                return;
            }

            $settings = $this->get_settings();
            $handle   = 'codboost-marketing-styles';

            wp_enqueue_style( 'codboost-marketing-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap', array(), null );

            wp_register_style( $handle, plugins_url( 'assets/css/frontend.css', __FILE__ ), array(), '1.0.0' );
            wp_enqueue_style( $handle );

            $custom_css = sprintf(
                '.codboost-marketing-block{background:%1$s;color:%2$s;border-radius:18px;padding:32px;margin:40px 0;font-family:"Montserrat",sans-serif;box-shadow:0 30px 60px rgba(0,0,0,0.12)}'
                .'.codboost-marketing-block h2{margin-top:0;color:%3$s;font-size:1.8rem;text-transform:uppercase;letter-spacing:2px}'
                .'.codboost-marketing-block .products{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:18px;margin-top:24px}'
                .'.codboost-marketing-block .product{background:rgba(255,255,255,0.05);padding:16px;border-radius:16px;transition:transform .3s ease,box-shadow .3s ease;text-align:center}'
                .'.codboost-marketing-block .product:hover{transform:translateY(-6px);box-shadow:0 24px 40px rgba(0,0,0,0.18)}'
                .'.codboost-marketing-block .button{background:%3$s;color:%2$s;border-radius:999px;padding:10px 24px;text-transform:uppercase;letter-spacing:1px;font-weight:600;display:inline-flex;align-items:center;justify-content:center}'
                .'.codboost-marketing-popup{position:fixed;z-index:9999;bottom:30px;right:30px;max-width:380px;background:%1$s;color:%2$s;padding:28px;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,0.35);transform:translateY(20px);opacity:0;pointer-events:none;transition:all .4s ease;font-family:"Montserrat",sans-serif}'
                .'.codboost-marketing-popup.is-visible{transform:translateY(0);opacity:1;pointer-events:auto}'
                .'.codboost-marketing-popup h3{margin-top:0;margin-bottom:12px;color:%3$s;font-size:1.5rem}'
                .'.codboost-marketing-popup p{margin-bottom:20px;line-height:1.6}'
                .'.codboost-marketing-popup .codboost-popup-actions{display:flex;gap:12px;align-items:center}'
                .'.codboost-marketing-popup .codboost-popup-cta{flex:1;background:%3$s;color:%2$s;border-radius:999px;padding:12px 20px;text-align:center;text-transform:uppercase;font-weight:600;letter-spacing:1px;box-shadow:0 12px 30px rgba(0,0,0,0.25)}'
                .'.codboost-marketing-popup .codboost-popup-close{background:transparent;border:none;color:%2$s;font-size:20px;cursor:pointer}'
                .'.codboost-marketing-popup a{color:%2$s;text-decoration:none}'
                .'.codboost-marketing-popup a:hover{text-decoration:underline}'
                .'.codboost-marketing-block .price{display:block;margin:12px 0;font-weight:600;font-size:1.1rem}'
                .'.codboost-marketing-block .woocommerce-LoopProduct-link{color:%2$s;text-decoration:none}'
                ,
                esc_attr( $settings['background_color'] ),
                '#ffffff',
                esc_attr( $settings['accent_color'] )
            );

            wp_add_inline_style( $handle, $custom_css );

            if ( $settings['enable_popup'] ) {
                wp_register_script( 'codboost-marketing-popup', plugins_url( 'assets/js/popup.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
                wp_enqueue_script( 'codboost-marketing-popup' );
                wp_localize_script( 'codboost-marketing-popup', 'codboostMarketingPopup', array(
                    'delay'   => (int) $settings['popup_delay'] * 1000,
                    'enabled' => (bool) $settings['enable_popup'],
                ) );
            }
        }

        /**
         * Affiche les upsells sur la fiche produit.
         */
        public function render_upsell_block() {
            if ( ! class_exists( 'WooCommerce' ) || ! is_product() ) {
                return;
            }

            $settings = $this->get_settings();
            if ( ! $settings['enable_upsell'] ) {
                return;
            }

            global $product;
            if ( ! $product instanceof WC_Product ) {
                $product = wc_get_product( get_the_ID() );
            }

            if ( ! $product ) {
                return;
            }

            $upsell_ids = $product->get_upsell_ids();
            if ( empty( $upsell_ids ) ) {
                return;
            }

            $args = array(
                'post_type' => 'product',
                'post__in'  => $upsell_ids,
                'orderby'   => 'post__in',
            );

            $query = new WP_Query( $args );
            if ( ! $query->have_posts() ) {
                return;
            }

            echo '<section class="codboost-marketing-block codboost-upsell">';
            echo '<h2>' . esc_html( $settings['upsell_title'] ) . '</h2>';
            echo '<div class="products">';

            while ( $query->have_posts() ) {
                $query->the_post();
                wc_get_template_part( 'content', 'product' );
            }

            echo '</div>';
            echo '</section>';

            wp_reset_postdata();
        }

        /**
         * Affiche les ventes croisées sur la fiche produit.
         */
        public function render_cross_sell_block() {
            if ( ! class_exists( 'WooCommerce' ) || ! is_product() ) {
                return;
            }

            $settings = $this->get_settings();
            if ( ! $settings['enable_cross_sell'] ) {
                return;
            }

            $product = wc_get_product( get_the_ID() );
            if ( ! $product ) {
                return;
            }

            $cross_sell_ids = $product->get_cross_sell_ids();
            if ( empty( $cross_sell_ids ) ) {
                $cross_sell_ids = wc_get_related_products( $product->get_id(), 4 );
            }

            if ( empty( $cross_sell_ids ) ) {
                return;
            }

            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 4,
                'post__in'       => $cross_sell_ids,
                'orderby'        => 'rand',
            );

            $query = new WP_Query( $args );
            if ( ! $query->have_posts() ) {
                return;
            }

            echo '<section class="codboost-marketing-block codboost-cross-sell">';
            echo '<h2>' . esc_html( $settings['cross_sell_title'] ) . '</h2>';
            echo '<div class="products">';

            while ( $query->have_posts() ) {
                $query->the_post();
                wc_get_template_part( 'content', 'product' );
            }

            echo '</div>';
            echo '</section>';

            wp_reset_postdata();
        }

        /**
         * Affiche les produits alternatifs à prix doux sur le panier.
         */
        public function render_downsell_block() {
            if ( ! class_exists( 'WooCommerce' ) ) {
                return;
            }

            $settings = $this->get_settings();
            if ( ! $settings['enable_downsell'] ) {
                return;
            }

            if ( WC()->cart->is_empty() ) {
                return;
            }

            $cart_items = WC()->cart->get_cart();
            $first_item = reset( $cart_items );

            if ( empty( $first_item['data'] ) ) {
                return;
            }

            $product       = $first_item['data'];
            $price_current = (float) $product->get_price();
            $categories    = $product->get_category_ids();

            if ( empty( $categories ) ) {
                return;
            }

            $category_terms = get_terms( array(
                'taxonomy'   => 'product_cat',
                'include'    => $categories,
                'hide_empty' => false,
            ) );

            if ( is_wp_error( $category_terms ) || empty( $category_terms ) ) {
                return;
            }

            $category_slugs = wp_list_pluck( $category_terms, 'slug' );
            $exclude_ids    = array_unique( array_map( 'absint', wp_list_pluck( $cart_items, 'product_id' ) ) );

            $downsell_products = wc_get_products( array(
                'status'  => 'publish',
                'limit'   => 3,
                'orderby' => 'price',
                'order'   => 'ASC',
                'category'=> implode( ',', $category_slugs ),
                'exclude' => $exclude_ids,
                'max_price' => $price_current,
            ) );

            if ( empty( $downsell_products ) ) {
                return;
            }

            echo '<section class="codboost-marketing-block codboost-downsell">';
            echo '<h2>' . esc_html( $settings['downsell_title'] ) . '</h2>';
            echo '<div class="products">';

            global $post;

            foreach ( $downsell_products as $downsell_product ) {
                $post = get_post( $downsell_product->get_id() );
                setup_postdata( $post );
                wc_get_template_part( 'content', 'product' );
            }

            wp_reset_postdata();

            echo '</div>';
            echo '</section>';
        }

        /**
         * Affiche le pop-up marketing.
         */
        public function render_popup() {
            $is_json_request = function_exists( 'wp_is_json_request' ) ? wp_is_json_request() : false;

            if ( is_admin() || $is_json_request ) {
                return;
            }

            $settings = $this->get_settings();
            if ( ! $settings['enable_popup'] ) {
                return;
            }

            ?>
            <div class="codboost-marketing-popup" id="codboost-marketing-popup" role="dialog" aria-live="polite" aria-hidden="true">
                <button class="codboost-popup-close" aria-label="<?php esc_attr_e( 'Fermer la promotion', 'codboost-marketing-tools' ); ?>">&times;</button>
                <h3><?php echo esc_html( $settings['popup_title'] ); ?></h3>
                <p><?php echo wp_kses_post( $settings['popup_message'] ); ?></p>
                <div class="codboost-popup-actions">
                    <a class="codboost-popup-cta" href="<?php echo esc_url( $settings['popup_cta_link'] ); ?>"><?php echo esc_html( $settings['popup_cta_text'] ); ?></a>
                </div>
            </div>
            <?php
        }
    }
}

/**
 * Initialise le plugin.
 */
function codboost_marketing_tools_init() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'Marketing Tools by Codboost nécessite WooCommerce pour fonctionner.', 'codboost-marketing-tools' ) . '</p></div>';
        } );

        return;
    }

    Codboost_Marketing_Tools::instance();
}
add_action( 'plugins_loaded', 'codboost_marketing_tools_init', 5 );

register_activation_hook( __FILE__, array( 'Codboost_Marketing_Tools', 'activate_plugin' ) );
