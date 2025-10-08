<?php
/**
 * The template for displaying all pages.
 *
 * @package MedExpress
 */

get_header();

while ( have_posts() ) :
    the_post();
    get_template_part( 'template-parts/content', 'page' );

    if ( comments_open() || get_comments_number() ) {
        comments_template();
    }
endwhile;

get_footer();
