<?php
/**
 * Price adjustments for offers.
 */

defined( 'ABSPATH' ) || exit;

class KLPU_Price {
    private static ?KLPU_Price $instance = null;

    public static function get_instance(): KLPU_Price {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_action( 'woocommerce_before_calculate_totals', [ $this, 'apply_offer_price' ], 20 );
        add_filter( 'woocommerce_get_item_data', [ $this, 'add_cart_item_badge' ], 10, 2 );
    }

    public function apply_offer_price( WC_Cart $cart ): void {
        if ( is_admin() && ! wp_doing_ajax() ) {
            return;
        }

        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            if ( empty( $cart_item['klpu_adjusted'] ) || empty( $cart_item['data'] ) ) {
                continue;
            }

            $product = $cart_item['data'];

            if ( ! $product instanceof WC_Product ) {
                continue;
            }

            $profit_type  = sanitize_text_field( $cart_item['klpu_profit_type'] ?? 'montant_fixe' );
            $profit_value = floatval( $cart_item['klpu_profit_value'] ?? 0 );

            $new_price = KLPU_calculate_price_with_profit( $product, $profit_type, $profit_value );
            $product->set_price( $new_price );
        }
    }

    public function add_cart_item_badge( array $item_data, array $cart_item ): array {
        if ( empty( $cart_item['klpu_adjusted'] ) ) {
            return $item_data;
        }

        $label = apply_filters( 'klpu_offer_title', __( 'Offre Kids-Luxe', 'kidsluxe-popup-upsell' ), $cart_item );
        $value = apply_filters( 'klpu_offer_price_label', __( 'Prix spécial', 'kidsluxe-popup-upsell' ) );

        $item_data[] = [
            'name'  => $label,
            'value' => esc_html( $value ),
        ];

        return $item_data;
    }
}
