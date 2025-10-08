<?php
/**
 * Header template for Med Express Delivery theme.
 *
 * @package MedExpress
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site" id="page">
    <?php if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) : ?>
    <?php else : ?>
        <header class="site-header" role="banner">
            <div class="site-container site-header__inner">
                <div class="site-branding">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php endif; ?>

                    <?php if ( display_header_text() ) : ?>
                        <?php if ( is_front_page() && is_home() ) : ?>
                            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                        <?php else : ?>
                            <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                        <?php endif; ?>
                        <?php
                        $medexpress_description = get_bloginfo( 'description', 'display' );
                        if ( $medexpress_description || is_customize_preview() ) :
                            ?>
                            <p class="site-description"><?php echo esc_html( $medexpress_description ); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="menu-toggle__bar"></span>
                    <span class="menu-toggle__bar"></span>
                    <span class="menu-toggle__bar"></span>
                    <span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'med-express' ); ?></span>
                </button>

                <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'med-express' ); ?>">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'fallback_cb'    => 'wp_page_menu',
                        )
                    );
                    ?>
                </nav>
            </div>
        </header>
    <?php endif; ?>

    <main id="content" class="site-main">
