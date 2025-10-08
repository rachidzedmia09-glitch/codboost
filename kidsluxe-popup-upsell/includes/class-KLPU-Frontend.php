<?php
/**
 * Front-end rendering and behaviour.
 */

defined( 'ABSPATH' ) || exit;

class KLPU_Frontend {
    private static ?KLPU_Frontend $instance = null;

    private array $products = [];

    public static function get_instance(): KLPU_Frontend {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp', [ $this, 'setup_collectors' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_footer', [ $this, 'render_modal' ] );
        add_filter( 'woocommerce_loop_add_to_cart_args', [ $this, 'add_loop_button_attributes' ], 20, 2 );
        add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'render_single_marker' ] );
    }

    public function setup_collectors(): void {
        if ( ! KLPU_is_globally_enabled() ) {
            return;
        }

        if ( is_product() ) {
            add_action( 'woocommerce_before_single_product', [ $this, 'capture_single_product' ] );
        }

        if ( is_shop() || is_product_taxonomy() ) {
            add_action( 'woocommerce_after_shop_loop_item', [ $this, 'capture_loop_product' ], 20 );
        }
    }

    private function should_enqueue(): bool {
        if ( ! KLPU_is_globally_enabled() ) {
            return false;
        }

        return is_product() || is_shop() || is_product_taxonomy() || is_cart();
    }

    public function enqueue_assets(): void {
        if ( ! $this->should_enqueue() ) {
            return;
        }

        wp_enqueue_style( 'klpu-popup', KLPU_PLUGIN_URL . 'assets/css/popup.css', [], KLPU_VERSION );
        wp_enqueue_script( 'klpu-popup', KLPU_PLUGIN_URL . 'assets/js/popup.js', [], KLPU_VERSION, true );
        wp_script_add_data( 'klpu-popup', 'defer', true );

        $buy_now_selector = apply_filters( 'klpu_buy_now_selector', KLPU_get_option( 'buy_now_selector' ) );

        wp_localize_script(
            'klpu-popup',
            'KLPUGlobals',
            [
                'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
                'nonce'        => wp_create_nonce( KLPU_get_nonce_action() ),
                'buyNow'       => $buy_now_selector,
                'texts'        => [
                    'title'       => KLPU_get_option( 'popup_title' ),
                    'subtitle'    => KLPU_get_option( 'popup_subtitle' ),
                    'accept'      => KLPU_get_option( 'popup_accept_label' ),
                    'decline'     => KLPU_get_option( 'popup_decline_label' ),
                    'priceLabel'  => apply_filters( 'klpu_offer_price_label', __( 'Prix spécial :', 'kidsluxe-popup-upsell' ) ),
                    'profitLabel' => __( 'Inclut une marge Kids-Luxe.', 'kidsluxe-popup-upsell' ),
                ],
                'showProfit'   => KLPU_get_option( 'show_profit_notice', true ),
                'fragments'    => function_exists( 'wc_cart_fragments_enabled' ) ? wc_cart_fragments_enabled() : true,
            ]
        );
    }

    public function capture_single_product(): void {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            return;
        }

        $this->register_product( $product );
    }

    public function capture_loop_product(): void {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            return;
        }

        $this->register_product( $product );
    }

    private function register_product( WC_Product $product ): void {
        $product_id = $product->get_id();

        if ( isset( $this->products[ $product_id ] ) ) {
            return;
        }

        $settings = KLPU_get_product_settings( $product_id );

        if ( empty( $settings['enabled'] ) ) {
            return;
        }

        $offer_product = KLPU_get_offer_product( $settings['offer_product'] );

        if ( ! $offer_product ) {
            return;
        }

        $should_show = apply_filters( 'klpu_should_show_for_product', true, $product_id );
        if ( ! $should_show ) {
            return;
        }

        $thumbnail = wp_get_attachment_image_src( $offer_product->get_image_id(), 'medium' );
        $image      = $thumbnail ? $thumbnail[0] : wc_placeholder_img_src();

        $profit_type  = $settings['profit_type'] ?: 'montant_fixe';
        $profit_value = $settings['profit_value'] ?: 0.0;

        $price_display = KLPU_format_price_with_profit( $offer_product, $profit_type, $profit_value );

        $triggers       = array_map( 'sanitize_key', array_values( $settings['triggers'] ?? [] ) );
        $processed_trigger = [];

        foreach ( $triggers as $trigger ) {
            $trigger_allowed = true;

            if ( 'add_to_cart' === $trigger ) {
                $trigger_allowed = apply_filters( 'klpu_should_open_on_add_to_cart', true, $product_id, 'add_to_cart' );
            }

            if ( 'buy_now' === $trigger ) {
                $trigger_allowed = apply_filters( 'klpu_should_open_on_add_to_cart', true, $product_id, 'buy_now' );
            }

            if ( $trigger_allowed ) {
                $processed_trigger[] = $trigger;
            }
        }

        if ( empty( $processed_trigger ) ) {
            return;
        }

        $excerpt = $offer_product->get_short_description() ?: $offer_product->get_description();
        $excerpt = wp_trim_words( wp_strip_all_tags( $excerpt ), 30 );

        $data = [
            'productId'       => $product_id,
            'offerProductId'  => $offer_product->get_id(),
            'offerName'       => $offer_product->get_name(),
            'offerExcerpt'    => $excerpt,
            'offerImage'      => esc_url_raw( $image ),
            'profitType'      => $profit_type,
            'profitValue'     => $profit_value,
            'priceDisplay'    => $price_display,
            'triggers'        => $processed_trigger,
            'regularPrice'    => wc_get_price_to_display( $offer_product, [ 'price' => $offer_product->get_regular_price() ] ),
        ];

        $data = apply_filters( 'klpu_modal_payload', $data, $product, $offer_product );

        $this->products[ $product_id ] = $data;
    }

    public function add_loop_button_attributes( array $args, WC_Product $product ): array {
        $product_id = $product->get_id();

        if ( ! isset( $this->products[ $product_id ] ) ) {
            $this->register_product( $product );
        }

        if ( isset( $this->products[ $product_id ] ) ) {
            $args['attributes']['data-klpu-product'] = (string) $product_id;
        }

        return $args;
    }

    public function render_single_marker(): void {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            return;
        }

        $product_id = $product->get_id();

        if ( isset( $this->products[ $product_id ] ) ) {
            printf( '<div class="klpu-marker" data-klpu-product="%d"></div>', (int) $product_id );
        }
    }

    public function render_modal(): void {
        if ( empty( $this->products ) ) {
            return;
        }

        $products = array_values( $this->products );
        $json     = wp_json_encode( $products, JSON_UNESCAPED_UNICODE );

        if ( $json ) {
            wp_add_inline_script( 'klpu-popup', 'window.KLPUProducts=' . $json . ';', 'before' );
        }

        wc_get_template( 'modal-offer.php', [], '', KLPU_PLUGIN_DIR . 'templates/' );
    }
}
