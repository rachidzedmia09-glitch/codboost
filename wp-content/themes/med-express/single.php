<?php
/**
 * Template for displaying single posts.
 *
 * @package MedExpress
 */

get_header();

if ( function_exists( 'medexpress_elementor_render_location' ) && medexpress_elementor_render_location( array( 'single-post', 'single' ) ) ) {
    get_footer();
    return;
}

while ( have_posts() ) :
    the_post();
    get_template_part( 'template-parts/content', get_post_type() );
    the_post_navigation(
        array(
            'prev_text' => __( '<span class="nav-subtitle">Previous</span> <span class="nav-title">%title</span>', 'med-express' ),
            'next_text' => __( '<span class="nav-subtitle">Next</span> <span class="nav-title">%title</span>', 'med-express' ),
        )
    );

    if ( comments_open() || get_comments_number() ) {
        comments_template();
    }
endwhile;

get_footer();
