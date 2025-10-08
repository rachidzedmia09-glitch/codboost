<?php
/**
 * Footer template for Med Express Delivery theme.
 *
 * @package MedExpress
 */
?>
    </main><!-- #content -->
    <?php
    $cta_headline = get_theme_mod( 'medexpress_cta_headline', __( 'Ready to scale with reliable delivery?', 'med-express' ) );
    $cta_text     = get_theme_mod( 'medexpress_cta_text', __( 'Book a consultation with our logistics experts and get your parcels moving today.', 'med-express' ) );
    $cta_button   = get_theme_mod( 'medexpress_cta_button_text', __( 'Schedule a Call', 'med-express' ) );
    $cta_link     = get_theme_mod( 'medexpress_cta_button_link', '#contact' );
    ?>
    <section class="cta" id="cta">
        <div class="container">
            <div class="cta__content">
                <h2><?php echo esc_html( $cta_headline ); ?></h2>
                <p><?php echo wp_kses_post( $cta_text ); ?></p>
            </div>
            <a class="button button--primary" href="<?php echo esc_url( $cta_link ); ?>"><?php echo esc_html( $cta_button ); ?></a>
        </div>
    </section>

    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer__grid">
                <div class="footer__brand">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <h2 class="footer__title"><?php bloginfo( 'name' ); ?></h2>
                    <?php endif; ?>
                    <p><?php bloginfo( 'description' ); ?></p>
                    <?php $address = get_theme_mod( 'medexpress_contact_address', __( '30 Rue Didouche Mourad, Algiers, Algeria', 'med-express' ) ); ?>
                    <p class="footer__address"><?php echo esc_html( $address ); ?></p>
                </div>
                <div class="footer__widgets">
                    <?php
                    if ( is_active_sidebar( 'footer-1' ) ) {
                        dynamic_sidebar( 'footer-1' );
                    }
                    if ( is_active_sidebar( 'footer-2' ) ) {
                        dynamic_sidebar( 'footer-2' );
                    }
                    if ( is_active_sidebar( 'footer-3' ) ) {
                        dynamic_sidebar( 'footer-3' );
                    }
                    ?>
                </div>
                <div class="footer__menu">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer',
                            'container'      => false,
                            'fallback_cb'    => '__return_false',
                        )
                    );
                    ?>
                </div>
            </div>
            <div class="footer__bottom">
                <p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'med-express' ); ?></p>
            </div>
        </div>
    </footer><!-- #colophon -->
</div><!-- #page -->
<?php
$whatsapp_number = get_theme_mod( 'medexpress_whatsapp_number', '+213771234567' );
if ( ! empty( $whatsapp_number ) ) :
    ?>
    <a class="whatsapp-float" href="<?php echo esc_url( medexpress_get_whatsapp_link( $whatsapp_number ) ); ?>" target="_blank" rel="noopener">
        <span class="dashicons dashicons-format-chat"></span>
        <span class="screen-reader-text"><?php esc_html_e( 'Chat on WhatsApp', 'med-express' ); ?></span>
    </a>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
