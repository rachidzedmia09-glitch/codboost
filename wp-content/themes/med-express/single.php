<?php
/**
 * The template for displaying all single posts.
 *
 * @package MedExpress
 */

get_header();
?>
<div class="container container--narrow">
    <?php
    while ( have_posts() ) :
        the_post();
        get_template_part( 'template-parts/content', get_post_type() );

        the_post_navigation(
            array(
                'prev_text' => __( 'Previous Post', 'med-express' ),
                'next_text' => __( 'Next Post', 'med-express' ),
            )
        );

        if ( comments_open() || get_comments_number() ) {
            comments_template();
        }
    endwhile;
    ?>
</div>
<?php
get_footer();
