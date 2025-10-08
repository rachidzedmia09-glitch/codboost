<?php
/**
 * Footer template for Med Express Delivery theme.
 *
 * @package MedExpress
 */
?>
    </main><!-- #content -->
    <?php if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) : ?>
    <?php else : ?>
        <footer id="colophon" class="site-footer" role="contentinfo">
            <div class="site-container site-footer__inner">
                <div class="site-footer__brand">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php endif; ?>

                    <?php if ( display_header_text() ) : ?>
                        <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>
                        <p class="site-description"><?php bloginfo( 'description' ); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
                    <div class="site-footer__widgets">
                        <?php
                        dynamic_sidebar( 'footer-1' );
                        dynamic_sidebar( 'footer-2' );
                        dynamic_sidebar( 'footer-3' );
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                    <nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'med-express' ); ?>">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer',
                                'container'      => false,
                                'fallback_cb'    => false,
                            )
                        );
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
            <div class="site-footer__credits">
                <div class="site-container">
                    <p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'med-express' ); ?></p>
                </div>
            </div>
        </footer><!-- #colophon -->
    <?php endif; ?>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
