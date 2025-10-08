<?php
use PHPUnit\Framework\TestCase;

if ( ! class_exists( 'WC_Product' ) ) {
    class WC_Product {
        protected $regular_price;

        public function __construct( $price ) {
            $this->regular_price = $price;
        }

        public function get_regular_price() {
            return $this->regular_price;
        }

        public function get_price() {
            return $this->regular_price;
        }

        public function is_purchasable() {
            return true;
        }

        public function is_in_stock() {
            return true;
        }
    }
}

if ( ! function_exists( 'wc_get_price_to_display' ) ) {
    function wc_get_price_to_display( $product, $args = [] ) {
        return $args['price'] ?? 0;
    }
}

if ( ! function_exists( 'wc_get_price_decimals' ) ) {
    function wc_get_price_decimals() {
        return 2;
    }
}

if ( ! function_exists( '__' ) ) {
    function __( $text ) {
        return $text;
    }
}

if ( ! function_exists( 'get_option' ) ) {
    function get_option( $name, $default = [] ) {
        return $default;
    }
}

if ( ! function_exists( 'wp_parse_args' ) ) {
    function wp_parse_args( $args, $defaults ) {
        return array_merge( $defaults, (array) $args );
    }
}

if ( ! function_exists( 'get_post_meta' ) ) {
    function get_post_meta() {
        return '';
    }
}

if ( ! function_exists( 'wc_get_product' ) ) {
    function wc_get_product() {
        return null;
    }
}

if ( ! function_exists( 'wc_price' ) ) {
    function wc_price( $price ) {
        return (string) $price;
    }
}

if ( ! function_exists( 'wp_get_attachment_image_src' ) ) {
    function wp_get_attachment_image_src() {
        return false;
    }
}

if ( ! function_exists( 'wc_placeholder_img_src' ) ) {
    function wc_placeholder_img_src() {
        return '';
    }
}

if ( ! function_exists( 'apply_filters' ) ) {
    function apply_filters( $tag, $value ) {
        return $value;
    }
}

require_once dirname( __DIR__ ) . '/includes/helpers.php';

final class PriceTest extends TestCase {
    public function test_fixed_profit_addition(): void {
        $product = new WC_Product( 100 );
        $result  = KLPU_calculate_price_with_profit( $product, 'montant_fixe', 20 );
        $this->assertSame( 120.0, $result );
    }

    public function test_percentage_profit(): void {
        $product = new WC_Product( 200 );
        $result  = KLPU_calculate_price_with_profit( $product, 'pourcentage', 10 );
        $this->assertSame( 220.0, $result );
    }
}
