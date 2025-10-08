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
    <header class="site-header">
        <div class="topbar">
            <div class="container">
                <div class="topbar__contact">
                    <?php $phone = get_theme_mod( 'medexpress_contact_phone', '+213 (0) 21 123 456' ); ?>
                    <a href="<?php echo esc_url( medexpress_get_phone_href( $phone ) ); ?>" class="topbar__item">
                        <span class="dashicons dashicons-phone"></span>
                        <?php echo esc_html( $phone ); ?>
                    </a>
                    <?php $email = get_theme_mod( 'medexpress_contact_email', 'contact@medexpress.dz' ); ?>
                    <a href="mailto:<?php echo antispambot( $email ); ?>" class="topbar__item">
                        <span class="dashicons dashicons-email"></span>
                        <?php echo esc_html( $email ); ?>
                    </a>
                </div>
                <div class="topbar__cta">
                    <?php
                    $cta_text = get_theme_mod( 'medexpress_cta_button_text', __( 'Schedule a Call', 'med-express' ) );
                    $cta_link = get_theme_mod( 'medexpress_cta_button_link', '#contact' );
                    ?>
                    <a class="button button--ghost" href="<?php echo esc_url( $cta_link ); ?>"><?php echo esc_html( $cta_text ); ?></a>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="site-branding">
                <?php the_custom_logo(); ?>
                <div class="site-title-wrapper">
                    <?php if ( is_front_page() && is_home() ) : ?>
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php else : ?>
                        <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                    <?php endif; ?>
                    <?php
                    $description = get_bloginfo( 'description', 'display' );
                    if ( $description || is_customize_preview() ) :
                        ?>
                        <p class="site-description"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                </div>
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
                        'fallback_cb'    => '__return_false',
                    )
                );
                ?>
            </nav>
        </div>
    </header>
    <main id="content" class="site-main">
