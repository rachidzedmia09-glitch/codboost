<?php
/**
 * Customizer additions for Med Express Delivery.
 *
 * @package MedExpress
 */

/**
 * Register customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function medexpress_customize_register( $wp_customize ) {
    // Branding section.
    $wp_customize->add_section(
        'medexpress_branding_section',
        array(
            'title'       => __( 'Company Identity', 'med-express' ),
            'priority'    => 20,
            'description' => __( 'Manage company-wide information such as contact details displayed throughout the theme.', 'med-express' ),
        )
    );

    $wp_customize->add_setting(
        'medexpress_contact_phone',
        array(
            'default'           => '+213 (0) 21 123 456',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'medexpress_contact_phone',
        array(
            'label'   => __( 'Contact Phone', 'med-express' ),
            'section' => 'medexpress_branding_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'medexpress_contact_email',
        array(
            'default'           => 'contact@medexpress.dz',
            'sanitize_callback' => 'sanitize_email',
        )
    );

    $wp_customize->add_control(
        'medexpress_contact_email',
        array(
            'label'   => __( 'Contact Email', 'med-express' ),
            'section' => 'medexpress_branding_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'medexpress_contact_address',
        array(
            'default'           => __( '30 Rue Didouche Mourad, Algiers, Algeria', 'med-express' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'medexpress_contact_address',
        array(
            'label'   => __( 'Office Address', 'med-express' ),
            'section' => 'medexpress_branding_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'medexpress_whatsapp_number',
        array(
            'default'           => '+213771234567',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'medexpress_whatsapp_number',
        array(
            'label'       => __( 'WhatsApp Number (international format without spaces)', 'med-express' ),
            'section'     => 'medexpress_branding_section',
            'description' => __( 'Used for the floating WhatsApp button on the site.', 'med-express' ),
            'type'        => 'text',
        )
    );

    // Hero section.
    $wp_customize->add_section(
        'medexpress_hero_section',
        array(
            'title'       => __( 'Homepage Hero', 'med-express' ),
            'priority'    => 30,
            'description' => __( 'Control the hero messaging and call-to-action buttons on the homepage.', 'med-express' ),
        )
    );

    $wp_customize->add_setting(
        'medexpress_hero_headline',
        array(
            'default'           => __( 'Delivery without boundaries across Algeria', 'med-express' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'medexpress_hero_headline',
        array(
            'label'   => __( 'Headline', 'med-express' ),
            'section' => 'medexpress_hero_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'medexpress_hero_subheadline',
        array(
            'default'           => __( 'Same-day pickup, real-time tracking, and COD reconciliation for Algerian eCommerce brands.', 'med-express' ),
            'sanitize_callback' => 'medexpress_sanitize_textarea',
        )
    );

    $wp_customize->add_control(
        'medexpress_hero_subheadline',
        array(
            'label'   => __( 'Subheadline', 'med-express' ),
            'section' => 'medexpress_hero_section',
            'type'    => 'textarea',
        )
    );

    $wp_customize->add_setting(
        'medexpress_hero_primary_text',
        array(
            'default'           => __( 'Get a Quote', 'med-express' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'medexpress_hero_primary_text',
        array(
            'label'   => __( 'Primary Button Text', 'med-express' ),
            'section' => 'medexpress_hero_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'medexpress_hero_primary_link',
        array(
            'default'           => '#contact',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'medexpress_hero_primary_link',
        array(
            'label'   => __( 'Primary Button Link', 'med-express' ),
            'section' => 'medexpress_hero_section',
            'type'    => 'url',
        )
    );

    $wp_customize->add_setting(
        'medexpress_hero_secondary_text',
        array(
            'default'           => __( 'Track a Parcel', 'med-express' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'medexpress_hero_secondary_text',
        array(
            'label'   => __( 'Secondary Button Text', 'med-express' ),
            'section' => 'medexpress_hero_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'medexpress_hero_secondary_link',
        array(
            'default'           => '#',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'medexpress_hero_secondary_link',
        array(
            'label'   => __( 'Secondary Button Link', 'med-express' ),
            'section' => 'medexpress_hero_section',
            'type'    => 'url',
        )
    );

    $wp_customize->add_setting(
        'medexpress_hero_background',
        array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'medexpress_hero_background',
            array(
                'label'    => __( 'Hero Background Image', 'med-express' ),
                'section'  => 'medexpress_hero_section',
                'settings' => 'medexpress_hero_background',
            )
        )
    );

    // Call to Action.
    $wp_customize->add_section(
        'medexpress_cta_section',
        array(
            'title'    => __( 'Call To Action', 'med-express' ),
            'priority' => 40,
        )
    );

    $wp_customize->add_setting(
        'medexpress_cta_headline',
        array(
            'default'           => __( 'Ready to scale with reliable delivery?', 'med-express' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'medexpress_cta_headline',
        array(
            'label'   => __( 'CTA Headline', 'med-express' ),
            'section' => 'medexpress_cta_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'medexpress_cta_text',
        array(
            'default'           => __( 'Book a consultation with our logistics experts and get your parcels moving today.', 'med-express' ),
            'sanitize_callback' => 'medexpress_sanitize_textarea',
        )
    );

    $wp_customize->add_control(
        'medexpress_cta_text',
        array(
            'label'   => __( 'CTA Description', 'med-express' ),
            'section' => 'medexpress_cta_section',
            'type'    => 'textarea',
        )
    );

    $wp_customize->add_setting(
        'medexpress_cta_button_text',
        array(
            'default'           => __( 'Schedule a Call', 'med-express' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'medexpress_cta_button_text',
        array(
            'label'   => __( 'CTA Button Text', 'med-express' ),
            'section' => 'medexpress_cta_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'medexpress_cta_button_link',
        array(
            'default'           => '#contact',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'medexpress_cta_button_link',
        array(
            'label'   => __( 'CTA Button Link', 'med-express' ),
            'section' => 'medexpress_cta_section',
            'type'    => 'url',
        )
    );
}
add_action( 'customize_register', 'medexpress_customize_register' );

/**
 * Sanitize textarea content allowing basic HTML.
 *
 * @param string $input Value to sanitize.
 *
 * @return string
 */
function medexpress_sanitize_textarea( $input ) {
    $allowed_tags = array(
        'a'      => array(
            'href'  => array(),
            'title' => array(),
            'target'=> array(),
        ),
        'br'     => array(),
        'em'     => array(),
        'strong' => array(),
        'span'   => array(
            'class' => array(),
        ),
    );

    return wp_kses( $input, $allowed_tags );
}
