<?php
/**
 * Theme setup and functionality for Med Express Delivery.
 *
 * @package MedExpress
 */

if ( ! defined( 'MEDEXPRESS_VERSION' ) ) {
    define( 'MEDEXPRESS_VERSION', '1.2.0' );
}

if ( ! function_exists( 'medexpress_theme_setup' ) ) {
    /**
     * Configure core theme supports.
     */
    function medexpress_theme_setup() {
        load_theme_textdomain( 'med-express', get_template_directory() . '/languages' );

        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'editor-styles' );
        add_theme_support( 'align-wide' );
        add_theme_support(
            'html5',
            array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
        );
        add_theme_support(
            'custom-logo',
            array(
                'height'      => 80,
                'width'       => 240,
                'flex-width'  => true,
                'flex-height' => true,
            )
        );

        register_nav_menus(
            array(
                'primary' => __( 'Primary Menu', 'med-express' ),
                'footer'  => __( 'Footer Menu', 'med-express' ),
            )
        );
    }
}
add_action( 'after_setup_theme', 'medexpress_theme_setup' );

if ( ! function_exists( 'medexpress_enqueue_assets' ) ) {
    /**
     * Enqueue styles and scripts.
     */
    function medexpress_enqueue_assets() {
        $theme_version = wp_get_theme()->get( 'Version' );

        wp_enqueue_style( 'dashicons' );
        wp_enqueue_style( 'medexpress-google-fonts', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap', array(), null );
        wp_enqueue_style( 'medexpress-style', get_stylesheet_uri(), array(), $theme_version );

        wp_enqueue_script( 'medexpress-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), $theme_version, true );
        wp_enqueue_script( 'medexpress-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), $theme_version, true );
    }
}
add_action( 'wp_enqueue_scripts', 'medexpress_enqueue_assets' );

if ( ! function_exists( 'medexpress_widgets_init' ) ) {
    /**
     * Register widget areas.
     */
    function medexpress_widgets_init() {
        $widget_args = array(
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        );

        register_sidebar(
            array_merge(
                $widget_args,
                array(
                    'name'        => __( 'Footer Column 1', 'med-express' ),
                    'id'          => 'footer-1',
                    'description' => __( 'Widgets in this area will be shown in the first footer column.', 'med-express' ),
                )
            )
        );

        register_sidebar(
            array_merge(
                $widget_args,
                array(
                    'name'        => __( 'Footer Column 2', 'med-express' ),
                    'id'          => 'footer-2',
                    'description' => __( 'Widgets in this area will be shown in the second footer column.', 'med-express' ),
                )
            )
        );

        register_sidebar(
            array_merge(
                $widget_args,
                array(
                    'name'        => __( 'Footer Column 3', 'med-express' ),
                    'id'          => 'footer-3',
                    'description' => __( 'Widgets in this area will be shown in the third footer column.', 'med-express' ),
                )
            )
        );
    }
}
add_action( 'widgets_init', 'medexpress_widgets_init' );

require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/elementor.php';
require get_template_directory() . '/inc/plugins.php';
require get_template_directory() . '/inc/demo-import.php';
