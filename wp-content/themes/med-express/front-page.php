<?php
/**
 * Front page template relying on Elementor or regular page content.
 *
 * @package MedExpress
 */

get_header();

if ( function_exists( 'medexpress_elementor_render_location' ) && medexpress_elementor_render_location( array( 'front-page', 'single-page', 'single' ) ) ) {
    get_footer();
    return;
}

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        get_template_part( 'template-parts/content', 'page' );
    endwhile;
else :
    get_template_part( 'template-parts/content', 'none' );
endif;

get_footer();
