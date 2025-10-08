<?php
/**
 * Default page template.
 *
 * @package MedExpress
 */

get_header();

if ( function_exists( 'medexpress_elementor_render_location' ) && medexpress_elementor_render_location( array( 'single-page', 'single' ) ) ) {
    get_footer();
    return;
}

while ( have_posts() ) :
    the_post();
    get_template_part( 'template-parts/content', 'page' );
    if ( comments_open() || get_comments_number() ) {
        comments_template();
    }
endwhile;

get_footer();
