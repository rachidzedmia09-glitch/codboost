<?php
/**
 * Theme bootstrap for Codboost Finance Portal.
 */

declare( strict_types=1 );

add_action( 'after_setup_theme', function () : void {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'codboost-finance' ),
    ] );
} );

add_action( 'wp_enqueue_scripts', function () : void {
    wp_enqueue_style( 'codboost-finance-style', get_stylesheet_uri(), [], '1.0.0' );
    wp_enqueue_style( 'codboost-finance-components', get_theme_file_uri( 'assets/css/components.css' ), [ 'codboost-finance-style' ], '1.0.0' );
} );

add_action( 'login_enqueue_scripts', function () : void {
    wp_enqueue_style( 'codboost-finance-style', get_stylesheet_uri(), [], '1.0.0' );
    echo '<style>body.login{background:linear-gradient(135deg,#0f172a 0%,#1114ff 100%);} .login h1 a{background-image:url(' . esc_url( get_theme_file_uri( 'assets/img/codboost-logo-light.svg' ) ) . ');background-size:contain;width:180px;height:60px;}</style>';
} );

add_action( 'after_switch_theme', function () : void {
    $page_id = (int) get_option( 'codboost_finance_dashboard_page', 0 );
    if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
        return;
    }

    $page_id = wp_insert_post( [
        'post_title'   => __( 'Finance Dashboard', 'codboost-finance' ),
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ] );

    if ( ! is_wp_error( $page_id ) ) {
        update_post_meta( $page_id, '_wp_page_template', 'page-money-manager.php' );
        update_option( 'codboost_finance_dashboard_page', $page_id );
    }
} );

/**
 * Helper to embed the money manager automatically when the plugin is active.
 */
add_filter( 'the_content', function ( string $content ) : string {
    if ( is_page_template( 'page-money-manager.php' ) && class_exists( 'Codboost_Money_Manager' ) ) {
        if ( false === strpos( $content, '[codboost_money_manager]' ) ) {
            $content .= '\n[codboost_money_manager]';
        }
    }

    return $content;
} );
