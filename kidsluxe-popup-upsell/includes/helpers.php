<?php
/**
 * Helper functions.
 *
 * @package KidsLuxe\Helpers
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'KLPU_get_default_options' ) ) {
    function KLPU_get_default_options(): array {
        return [
            'enabled'             => true,
            'buy_now_selector'    => '.klpu-buy-now, .buy-now, .single_buy_now_button',
            'popup_title'         => __( 'Complétez votre commande', 'kidsluxe-popup-upsell' ),
            'popup_subtitle'      => __( 'Profitez de notre sélection exclusive Kids-Luxe.', 'kidsluxe-popup-upsell' ),
            'popup_accept_label'  => __( "Ajouter l’offre", 'kidsluxe-popup-upsell' ),
            'popup_decline_label' => __( 'Non merci', 'kidsluxe-popup-upsell' ),
            'show_profit_notice'  => true,
        ];
    }
}

if ( ! function_exists( 'KLPU_get_options' ) ) {
    function KLPU_get_options(): array {
        $defaults = KLPU_get_default_options();
        $options  = get_option( 'klpu_settings', [] );

        if ( ! is_array( $options ) ) {
            $options = [];
        }

        return wp_parse_args( $options, $defaults );
    }
}

if ( ! function_exists( 'KLPU_get_option' ) ) {
    function KLPU_get_option( string $key, $default = null ) {
        $options = KLPU_get_options();
        return $options[ $key ] ?? $default;
    }
}

if ( ! function_exists( 'KLPU_is_globally_enabled' ) ) {
    function KLPU_is_globally_enabled(): bool {
        return (bool) KLPU_get_option( 'enabled', true );
    }
}

if ( ! function_exists( 'KLPU_get_product_settings' ) ) {
    function KLPU_get_product_settings( int $product_id ): array {
        $enabled_meta = get_post_meta( $product_id, '_klpu_enabled', true );

        return [
            'enabled'        => 'yes' === $enabled_meta || true === $enabled_meta,
            'offer_product'  => (int) get_post_meta( $product_id, '_klpu_offer_product_id', true ),
            'profit_type'    => get_post_meta( $product_id, '_klpu_profit_type', true ) ?: 'montant_fixe',
            'profit_value'   => (float) get_post_meta( $product_id, '_klpu_profit_value', true ),
            'triggers'       => (array) get_post_meta( $product_id, '_klpu_triggers', true ),
        ];
    }
}

if ( ! function_exists( 'KLPU_format_price_with_profit' ) ) {
    function KLPU_format_price_with_profit( WC_Product $product, string $profit_type, float $profit_value ): string {
        $price = KLPU_calculate_price_with_profit( $product, $profit_type, $profit_value );
        return wc_price( $price );
    }
}

if ( ! function_exists( 'KLPU_calculate_price_with_profit' ) ) {
    function KLPU_calculate_price_with_profit( WC_Product $product, string $profit_type, float $profit_value ): float {
        $regular = $product->get_regular_price();
        if ( '' === $regular ) {
            $regular = $product->get_price();
        }

        $price = (float) wc_get_price_to_display( $product, [ 'price' => $regular ] );

        if ( 'pourcentage' === $profit_type ) {
            $price *= ( 1 + ( $profit_value / 100 ) );
        } else {
            $price += $profit_value;
        }

        $decimals = wc_get_price_decimals();
        return round( $price, $decimals );
    }
}

if ( ! function_exists( 'KLPU_get_offer_product' ) ) {
    function KLPU_get_offer_product( int $product_id ): ?WC_Product {
        $product = wc_get_product( $product_id );

        if ( ! $product instanceof WC_Product ) {
            return null;
        }

        if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
            return null;
        }

        return $product;
    }
}

if ( ! function_exists( 'KLPU_get_nonce_action' ) ) {
    function KLPU_get_nonce_action(): string {
        return 'klpu_add_offer';
    }
}
