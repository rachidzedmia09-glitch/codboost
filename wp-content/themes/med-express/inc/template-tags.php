<?php
/**
 * Template helper functions for Med Express Delivery.
 *
 * @package MedExpress
 */

if ( ! function_exists( 'medexpress_get_hero_background_style' ) ) {
    /**
     * Build hero background inline style.
     *
     * @return string
     */
    function medexpress_get_hero_background_style() {
        $background = get_theme_mod( 'medexpress_hero_background' );

        if ( empty( $background ) ) {
            return '';
        }

        return sprintf( 'style="background-image: url(%s);"', esc_url( $background ) );
    }
}

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
