<?php
/**
 * AJAX endpoints.
 */

defined( 'ABSPATH' ) || exit;

class KLPU_Ajax {
    private static ?KLPU_Ajax $instance = null;

    public static function get_instance(): KLPU_Ajax {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_ajax_klpu_add_offer', [ $this, 'handle_add_offer' ] );
        add_action( 'wp_ajax_nopriv_klpu_add_offer', [ $this, 'handle_add_offer' ] );
    }

    public function handle_add_offer(): void {
        check_ajax_referer( KLPU_get_nonce_action(), 'nonce' );

        if ( ! KLPU_is_globally_enabled() ) {
            wp_send_json_error( [ 'message' => __( 'Upsell désactivé.', 'kidsluxe-popup-upsell' ) ], 403 );
        }

        $product_id   = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
        $profit_type  = isset( $_POST['profit_type'] ) ? sanitize_text_field( wp_unslash( $_POST['profit_type'] ) ) : 'montant_fixe';
        $profit_value = isset( $_POST['profit_value'] ) ? floatval( wp_unslash( $_POST['profit_value'] ) ) : 0.0;

        if ( ! in_array( $profit_type, [ 'montant_fixe', 'pourcentage' ], true ) ) {
            $profit_type = 'montant_fixe';
        }

        if ( $product_id <= 0 ) {
            wp_send_json_error( [ 'message' => __( 'Produit invalide.', 'kidsluxe-popup-upsell' ) ], 400 );
        }

        $product = wc_get_product( $product_id );

        if ( ! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
            wp_send_json_error( [ 'message' => __( 'Produit indisponible.', 'kidsluxe-popup-upsell' ) ], 400 );
        }

        if ( ! function_exists( 'WC' ) ) {
            wp_send_json_error( [ 'message' => __( 'Panier indisponible.', 'kidsluxe-popup-upsell' ) ], 500 );
        }

        $cart_item_data = [
            'klpu_adjusted'    => true,
            'klpu_profit_type' => $profit_type,
            'klpu_profit_value'=> $profit_value,
        ];

        $cart_item_key = WC()->cart->add_to_cart( $product_id, 1, 0, [], $cart_item_data );

        if ( ! $cart_item_key ) {
            wp_send_json_error( [ 'message' => __( 'Impossible d’ajouter l’offre.', 'kidsluxe-popup-upsell' ) ], 500 );
        }

        WC()->cart->calculate_totals();

        $response = [
            'success' => true,
            'message' => __( 'Offre ajoutée au panier.', 'kidsluxe-popup-upsell' ),
        ];

        if ( apply_filters( 'klpu_return_fragments', true ) ) {
            $fragments = $this->generate_fragments();
            if ( $fragments ) {
                $response['fragments'] = $fragments['fragments'];
                $response['cart_hash'] = $fragments['cart_hash'];
            }
        }

        wp_send_json_success( $response );
    }

    private function generate_fragments(): array {
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        $fragments = [];
        if ( null !== $mini_cart ) {
            $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>';
        }

        $fragments = apply_filters( 'woocommerce_add_to_cart_fragments', $fragments );

        return [
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
        ];
    }
}
