<?php
/**
 * Plugin Name: Marketing Tools by Codboost
 * Plugin URI: https://kids-luxe.com/
 * Description: Pop-up d'offre promotionnelle pour Kids-Luxe.com offrant des ventes croisées premium avec tarification packagée.
 * Version: 1.1.0
 * Author: Codboost
 * Author URI: https://codboost.pro/
 * License: GPL2+
 * Text Domain: codboost-marketing-tools
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Codboost_Marketing_Tools' ) ) {
    class Codboost_Marketing_Tools {
        const OPTION_KEY = 'codboost_bundle_settings';
        const META_ENABLED = '_codboost_bundle_enabled';
        const META_PRODUCT = '_codboost_bundle_product_id';
        const META_TOTAL   = '_codboost_bundle_total_price';
        const META_MESSAGE = '_codboost_bundle_message';

        /**
         * Valeurs par défaut pour les options générales.
         *
         * @var array<string, mixed>
         */
        protected $default_settings = array(
            'accent_color'  => '#274472',
            'title'         => 'Offre Prestige',
            'subtitle'      => 'Complétez votre sélection avec une pièce exclusive à prix doux.',
            'accept_label'  => 'Oui, j\'en profite',
            'decline_label' => 'Non merci',
        );

        /**
         * Instance unique du plugin.
         *
         * @var Codboost_Marketing_Tools|null
         */
        protected static $instance = null;

        /**
         * Récupère l\'instance singleton.
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
        protected function __construct() {
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
            add_action( 'init', array( $this, 'register_assets' ) );

            if ( is_admin() ) {
                add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
                add_action( 'admin_init', array( $this, 'register_settings' ) );
                add_action( 'add_meta_boxes', array( $this, 'register_product_metabox' ) );
                add_action( 'save_post_product', array( $this, 'save_product_meta' ), 10, 2 );
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
            }

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_assets' ) );
            add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_bundle_price' ), 20 );

            add_action( 'wp_ajax_codboost_add_bundle_offer', array( $this, 'handle_bundle_ajax' ) );
            add_action( 'wp_ajax_nopriv_codboost_add_bundle_offer', array( $this, 'handle_bundle_ajax' ) );
        }

        /**
         * Chargement des traductions.
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'codboost-marketing-tools', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        /**
         * Enfile les assets pour l'administration.
         *
         * @param string $hook Hook en cours.
         */
        public function enqueue_admin_assets( $hook ) {
            if ( 'toplevel_page_codboost-bundle-settings' !== $hook ) {
                return;
            }

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            wp_add_inline_script( 'wp-color-picker', 'jQuery(function($){$(".codboost-color-field").wpColorPicker();});' );
        }

        /**
         * Enregistre les assets du plugin.
         */
        public function register_assets() {
            wp_register_style(
                'codboost-bundle-style',
                plugins_url( 'assets/css/frontend.css', __FILE__ ),
                array(),
                filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/frontend.css' )
            );

            wp_register_script(
                'codboost-bundle-script',
                plugins_url( 'assets/js/popup.js', __FILE__ ),
                array( 'jquery' ),
                filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/popup.js' ),
                true
            );
        }

        /**
         * Ajoute la page d\'options.
         */
        public function register_settings_page() {
            add_menu_page(
                __( 'Outils Marketing Kids Luxe', 'codboost-marketing-tools' ),
                __( 'Marketing Kids Luxe', 'codboost-marketing-tools' ),
                'manage_options',
                'codboost-bundle-settings',
                array( $this, 'render_settings_page' ),
                'dashicons-megaphone',
                58
            );
        }

        /**
         * Enregistre les réglages généraux.
         */
        public function register_settings() {
            register_setting( 'codboost_bundle_group', self::OPTION_KEY, array( $this, 'sanitize_settings' ) );

            add_settings_section(
                'codboost_bundle_main',
                __( 'Personnalisation du pop-up', 'codboost-marketing-tools' ),
                '__return_null',
                'codboost_bundle_settings'
            );

            add_settings_field(
                'accent_color',
                __( 'Couleur principale', 'codboost-marketing-tools' ),
                array( $this, 'render_color_field' ),
                'codboost_bundle_settings',
                'codboost_bundle_main',
                array( 'label_for' => 'accent_color' )
            );

            $text_fields = array(
                'title'         => __( 'Titre du pop-up', 'codboost-marketing-tools' ),
                'subtitle'      => __( 'Texte d\'accroche', 'codboost-marketing-tools' ),
                'accept_label'  => __( 'Texte du bouton d\'acceptation', 'codboost-marketing-tools' ),
                'decline_label' => __( 'Texte du bouton de refus', 'codboost-marketing-tools' ),
            );

            foreach ( $text_fields as $field => $label ) {
                add_settings_field(
                    $field,
                    $label,
                    array( $this, 'render_text_field' ),
                    'codboost_bundle_settings',
                    'codboost_bundle_main',
                    array( 'label_for' => $field )
                );
            }
        }

        /**
         * Rendu de la page de réglages.
         */
        public function render_settings_page() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $settings = $this->get_settings();
            ?>
            <div class="wrap codboost-bundle-admin">
                <h1><?php esc_html_e( 'Marketing Tools by Codboost', 'codboost-marketing-tools' ); ?></h1>
                <p class="description"><?php esc_html_e( 'Configurez l\'identité de votre pop-up luxe et ses libellés en quelques clics.', 'codboost-marketing-tools' ); ?></p>
                <form action="options.php" method="post">
                    <?php
                    settings_fields( 'codboost_bundle_group' );
                    do_settings_sections( 'codboost_bundle_settings' );
                    submit_button( __( 'Enregistrer', 'codboost-marketing-tools' ) );
                    ?>
                </form>
                <div class="codboost-preview" style="margin-top:40px;">
                    <h2><?php esc_html_e( 'Aperçu', 'codboost-marketing-tools' ); ?></h2>
                    <div class="codboost-preview-popup" style="border-left: 4px solid <?php echo esc_attr( $settings['accent_color'] ); ?>;">
                        <span class="codboost-badge" style="background: <?php echo esc_attr( $settings['accent_color'] ); ?>;">
                            <?php echo esc_html( $settings['title'] ); ?>
                        </span>
                        <p><?php echo esc_html( $settings['subtitle'] ); ?></p>
                        <div class="codboost-preview-actions">
                            <button class="button button-primary" style="background: <?php echo esc_attr( $settings['accent_color'] ); ?>; border-color: <?php echo esc_attr( $settings['accent_color'] ); ?>;">
                                <?php echo esc_html( $settings['accept_label'] ); ?>
                            </button>
                            <button class="button" style="color: <?php echo esc_attr( $settings['accent_color'] ); ?>; border-color: <?php echo esc_attr( $settings['accent_color'] ); ?>;">
                                <?php echo esc_html( $settings['decline_label'] ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Ajoute la meta box sur le produit.
         */
        public function register_product_metabox() {
            add_meta_box(
                'codboost_bundle_offer',
                __( 'Offre Pop-up Codboost', 'codboost-marketing-tools' ),
                array( $this, 'render_product_metabox' ),
                'product',
                'side',
                'high'
            );
        }

        /**
         * Affiche les champs de la meta box produit.
         *
         * @param WP_Post $post Current product.
         */
        public function render_product_metabox( $post ) {
            wp_nonce_field( 'codboost_bundle_meta', 'codboost_bundle_meta_nonce' );

            $enabled  = (bool) get_post_meta( $post->ID, self::META_ENABLED, true );
            $product  = (int) get_post_meta( $post->ID, self::META_PRODUCT, true );
            $total    = get_post_meta( $post->ID, self::META_TOTAL, true );
            $message  = get_post_meta( $post->ID, self::META_MESSAGE, true );

            echo '<p><label for="codboost_bundle_enabled">';
            esc_html_e( 'Activer l\'offre pop-up pour ce produit', 'codboost-marketing-tools' );
            echo '</label></p>';
            printf(
                '<p><label><input type="checkbox" id="codboost_bundle_enabled" name="codboost_bundle_enabled" value="1" %s> %s</label></p>',
                checked( $enabled, true, false ),
                esc_html__( 'Oui, afficher la proposition après l\'ajout au panier', 'codboost-marketing-tools' )
            );

            if ( function_exists( 'wc_dropdown_products' ) ) {
                echo '<p><label for="codboost_bundle_product_id">' . esc_html__( 'Produit mis en avant', 'codboost-marketing-tools' ) . '</label></p>';
                echo wp_kses_post(
                    wc_dropdown_products(
                        array(
                            'name'             => 'codboost_bundle_product_id',
                            'id'               => 'codboost_bundle_product_id',
                            'class'            => 'wc-product-search',
                            'data-placeholder' => esc_attr__( 'Recherchez un produit…', 'codboost-marketing-tools' ),
                            'limit'            => -1,
                            'selected'         => $product,
                            'return'           => 'id',
                            'exclude'          => array( $post->ID ),
                            'show_variations'  => false,
                            'echo'             => false,
                        )
                    )
                );
            }

            echo '<p><label for="codboost_bundle_total_price">' . esc_html__( 'Tarif total du pack (TTC)', 'codboost-marketing-tools' ) . '</label></p>';
            printf(
                '<p><input type="number" step="0.01" min="0" class="widefat" name="codboost_bundle_total_price" id="codboost_bundle_total_price" value="%s" placeholder="199.00"></p>',
                esc_attr( $total )
            );

            echo '<p><label for="codboost_bundle_message">' . esc_html__( 'Message personnalisé', 'codboost-marketing-tools' ) . '</label></p>';
            printf(
                '<p><textarea class="widefat" rows="3" name="codboost_bundle_message" id="codboost_bundle_message" placeholder="%s">%s</textarea></p>',
                esc_attr__( 'Ajoutez ce produit pour compléter votre univers Kids Luxe.', 'codboost-marketing-tools' ),
                esc_textarea( $message )
            );
        }

        /**
         * Sauvegarde les données de la meta box.
         *
         * @param int     $post_id Post ID.
         * @param WP_Post $post    Post object.
         */
        public function save_product_meta( $post_id, $post ) {
            if ( ! isset( $_POST['codboost_bundle_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['codboost_bundle_meta_nonce'] ), 'codboost_bundle_meta' ) ) {
                return;
            }

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            if ( 'product' !== $post->post_type || ! current_user_can( 'edit_product', $post_id ) ) {
                return;
            }

            $enabled = isset( $_POST['codboost_bundle_enabled'] ) ? '1' : '';
            update_post_meta( $post_id, self::META_ENABLED, $enabled );

            $product_id = isset( $_POST['codboost_bundle_product_id'] ) ? absint( $_POST['codboost_bundle_product_id'] ) : 0;
            update_post_meta( $post_id, self::META_PRODUCT, $product_id );

            $total_price = isset( $_POST['codboost_bundle_total_price'] ) ? wc_format_decimal( wp_unslash( $_POST['codboost_bundle_total_price'] ) ) : '';
            update_post_meta( $post_id, self::META_TOTAL, $total_price );

            $message = isset( $_POST['codboost_bundle_message'] ) ? wp_kses_post( wp_unslash( $_POST['codboost_bundle_message'] ) ) : '';
            update_post_meta( $post_id, self::META_MESSAGE, $message );
        }

        /**
         * Récupère les réglages du plugin.
         */
        public function get_settings() {
            $options = get_option( self::OPTION_KEY, array() );

            return wp_parse_args( $options, $this->default_settings );
        }

        /**
         * Nettoie les valeurs enregistrées.
         *
         * @param array<string, mixed> $settings Données de formulaire.
         *
         * @return array<string, mixed>
         */
        public function sanitize_settings( $settings ) {
            $clean = array();
            $defaults = $this->default_settings;

            foreach ( $defaults as $key => $value ) {
                if ( ! isset( $settings[ $key ] ) ) {
                    $clean[ $key ] = $value;
                    continue;
                }

                switch ( $key ) {
                    case 'accent_color':
                        $color = sanitize_hex_color( $settings[ $key ] );
                        $clean[ $key ] = $color ? $color : $this->default_settings['accent_color'];
                        break;
                    default:
                        $clean[ $key ] = sanitize_text_field( $settings[ $key ] );
                        break;
                }
            }

            return $clean;
        }

        /**
         * Champ couleur.
         *
         * @param array<string, string> $args Arguments.
         */
        public function render_color_field( $args ) {
            $settings = $this->get_settings();
            $value    = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : $this->default_settings['accent_color'];

            printf(
                '<input type="text" class="codboost-color-field" id="%1$s" name="%2$s[%1$s]" value="%3$s" data-default-color="%4$s">',
                esc_attr( $args['label_for'] ),
                esc_attr( self::OPTION_KEY ),
                esc_attr( $value ),
                esc_attr( $this->default_settings['accent_color'] )
            );
        }

        /**
         * Champ texte.
         *
         * @param array<string, string> $args Arguments.
         */
        public function render_text_field( $args ) {
            $settings = $this->get_settings();
            $value    = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : '';

            printf(
                '<input type="text" class="regular-text" id="%1$s" name="%2$s[%1$s]" value="%3$s">',
                esc_attr( $args['label_for'] ),
                esc_attr( self::OPTION_KEY ),
                esc_attr( $value )
            );
        }

        /**
         * Ajoute les assets front uniquement si nécessaire.
         */
        public function enqueue_front_assets() {
            if ( ! is_product() ) {
                return;
            }

            $product_id = get_the_ID();
            if ( ! $product_id ) {
                return;
            }

            $enabled = get_post_meta( $product_id, self::META_ENABLED, true );
            $target  = (int) get_post_meta( $product_id, self::META_PRODUCT, true );
            $total   = get_post_meta( $product_id, self::META_TOTAL, true );

            if ( ! $enabled || ! $target || '' === $total ) {
                return;
            }

            $target_product = wc_get_product( $target );
            $current_product = wc_get_product( $product_id );

            if ( ! $target_product instanceof WC_Product || ! $current_product instanceof WC_Product ) {
                return;
            }

            wp_enqueue_style( 'codboost-bundle-style' );
            wp_enqueue_script( 'codboost-bundle-script' );

            $settings = $this->get_settings();
            $image_id = $target_product->get_image_id();
            $image    = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : wc_placeholder_img_src( 'woocommerce_single' );

            $payload = array(
                'ajax_url'     => admin_url( 'admin-ajax.php' ),
                'nonce'        => wp_create_nonce( 'codboost_bundle_offer' ),
                'settings'     => array(
                    'accent_color'  => $settings['accent_color'],
                    'title'         => $settings['title'],
                    'subtitle'      => $settings['subtitle'],
                    'accept_label'  => $settings['accept_label'],
                    'decline_label' => $settings['decline_label'],
                ),
                'offer'        => array(
                    'base_id'       => $product_id,
                    'base_price'    => (float) wc_get_price_to_display( $current_product ),
                    'base_regular'  => (float) wc_get_price_to_display( $current_product, array( 'price' => $current_product->get_regular_price() ) ),
                    'target_id'     => $target_product->get_id(),
                    'target_name'   => $target_product->get_name(),
                    'target_price'  => (float) wc_get_price_to_display( $target_product ),
                    'target_regular'=> (float) wc_get_price_to_display( $target_product, array( 'price' => $target_product->get_regular_price() ) ),
                    'total_price'   => (float) $total,
                    'message'       => get_post_meta( $product_id, self::META_MESSAGE, true ),
                    'image'         => $image,
                    'currency'      => get_woocommerce_currency_symbol(),
                ),
                'price_copy'  => __( 'Profitez d\'un tarif exclusif pour compléter votre panier.', 'codboost-marketing-tools' ),
                'strings'     => array(
                    'savings'  => __( 'Vous économisez %s', 'codboost-marketing-tools' ),
                    'original' => __( 'Valeur initiale', 'codboost-marketing-tools' ),
                ),
            );

            wp_localize_script( 'codboost-bundle-script', 'codboostBundleData', $payload );
        }

        /**
         * Applique la tarification spéciale sur l\'article du pack.
         *
         * @param WC_Cart $cart Instance du panier.
         */
        public function apply_bundle_price( $cart ) {
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                return;
            }

            if ( ! $cart || $cart->is_empty() ) {
                return;
            }

            foreach ( $cart->get_cart() as $cart_item_key => $item ) {
                if ( empty( $item['codboost_bundle_offer'] ) || empty( $item['data'] ) ) {
                    continue;
                }

                $bundle_data = $item['codboost_bundle_offer'];
                if ( isset( $bundle_data['override_price'] ) ) {
                    $item['data']->set_price( (float) $bundle_data['override_price'] );
                }
            }
        }

        /**
         * Traite l\'AJAX d\'ajout de l\'offre.
         */
        public function handle_bundle_ajax() {
            check_ajax_referer( 'codboost_bundle_offer', 'nonce' );

            if ( ! class_exists( 'WC_AJAX' ) ) {
                wp_send_json_error( array( 'message' => __( 'WooCommerce est requis.', 'codboost-marketing-tools' ) ) );
            }

            $base_id   = isset( $_POST['base_product_id'] ) ? absint( $_POST['base_product_id'] ) : 0;
            $quantity  = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;

            if ( ! $base_id || ! $quantity ) {
                wp_send_json_error( array( 'message' => __( 'Paramètres invalides.', 'codboost-marketing-tools' ) ) );
            }

            $enabled = get_post_meta( $base_id, self::META_ENABLED, true );
            $target  = (int) get_post_meta( $base_id, self::META_PRODUCT, true );
            $total   = get_post_meta( $base_id, self::META_TOTAL, true );

            if ( ! $enabled || ! $target || '' === $total ) {
                wp_send_json_error( array( 'message' => __( 'Offre non disponible.', 'codboost-marketing-tools' ) ) );
            }

            $target_product  = wc_get_product( $target );
            $base_product    = wc_get_product( $base_id );

            if ( ! $target_product instanceof WC_Product || ! $base_product instanceof WC_Product ) {
                wp_send_json_error( array( 'message' => __( 'Produit introuvable.', 'codboost-marketing-tools' ) ) );
            }

            $base_total   = (float) wc_get_price_to_display( $base_product, array( 'qty' => $quantity ) );
            $desired      = (float) $total;
            $promo_price  = max( $desired - $base_total, 0 );
            $promo_price  = wc_format_decimal( $promo_price );

            if ( empty( WC()->cart ) ) {
                if ( function_exists( 'wc_load_cart' ) ) {
                    wc_load_cart();
                } else {
                    WC()->cart = new WC_Cart();
                }
            }

            $cart_item_data = array(
                'codboost_bundle_offer' => array(
                    'base_product_id' => $base_id,
                    'override_price'  => (float) $promo_price,
                ),
            );

            $added_key = WC()->cart->add_to_cart( $target_product->get_id(), 1, 0, array(), $cart_item_data );

            if ( ! $added_key ) {
                wp_send_json_error( array( 'message' => __( 'Impossible d\'ajouter le produit.', 'codboost-marketing-tools' ) ) );
            }

            WC()->cart->calculate_totals();

            $mini_cart = wc_get_template_html( 'cart/mini-cart.php', array(), '', WC()->template_path() );

            $fragments = apply_filters( 'woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
            ) );

            wp_send_json_success(
                array(
                    'fragments' => $fragments,
                    'cart_hash' => WC()->cart->get_cart_hash(),
                )
            );
        }
    }

    if ( class_exists( 'WooCommerce' ) ) {
        Codboost_Marketing_Tools::instance();
    } else {
        add_action(
            'admin_notices',
            static function () {
                echo '<div class="notice notice-error"><p>' . esc_html__( 'Marketing Tools by Codboost nécessite WooCommerce pour fonctionner.', 'codboost-marketing-tools' ) . '</p></div>';
            }
        );
    }
}
