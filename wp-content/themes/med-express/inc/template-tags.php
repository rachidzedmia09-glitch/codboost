<?php
/**
 * Template helper functions for Med Express Delivery.
 *
 * @package MedExpress
 */

if ( ! function_exists( 'medexpress_get_phone_href' ) ) {
    /**
     * Format phone number into tel: link.
     *
     * @param string $number Phone number.
     *
     * @return string
     */
    function medexpress_get_phone_href( $number ) {
        $number = preg_replace( '/[^0-9+]/', '', $number );
        return 'tel:' . $number;
    }
}

if ( ! function_exists( 'medexpress_get_whatsapp_link' ) ) {
    /**
     * Build a WhatsApp API link.
     *
     * @param string $number WhatsApp number.
     *
     * @return string
     */
    function medexpress_get_whatsapp_link( $number ) {
        $number = preg_replace( '/[^0-9]/', '', $number );

        if ( empty( $number ) ) {
            return '#';
        }

        $message = rawurlencode( __( 'Hello Med Express team! I would like to learn more about your delivery services.', 'med-express' ) );

        return sprintf( 'https://wa.me/%1$s?text=%2$s', $number, $message );
    }
}

if ( ! function_exists( 'medexpress_render_menu_shortcode' ) ) {
    /**
     * Shortcode callback to render a theme menu by location.
     *
     * Usage: [medexpress_menu location="primary" class="my-menu"].
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    function medexpress_render_menu_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'location' => 'primary',
                'class'    => 'medexpress-menu-shortcode',
            ),
            $atts,
            'medexpress_menu'
        );

        if ( empty( $atts['location'] ) ) {
            return '';
        }

        $classes = array_filter( array_map( 'sanitize_html_class', explode( ' ', $atts['class'] ) ) );
        $menu    = wp_nav_menu(
            array(
                'theme_location' => sanitize_key( $atts['location'] ),
                'container'      => false,
                'menu_class'     => implode( ' ', $classes ),
                'echo'           => false,
                'fallback_cb'    => false,
            )
        );

        if ( empty( $menu ) ) {
            return '';
        }

        return $menu;
    }

    add_shortcode( 'medexpress_menu', 'medexpress_render_menu_shortcode' );
}
