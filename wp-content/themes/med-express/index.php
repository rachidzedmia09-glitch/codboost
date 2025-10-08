<?php
/**
 * Main template file.
 *
 * @package MedExpress
 */

get_header();
?>
<div class="container container--narrow">
    <?php if ( have_posts() ) : ?>
        <?php if ( is_home() && ! is_front_page() ) : ?>
            <header class="page-header">
                <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
            </header>
        <?php endif; ?>

        <?php
        while ( have_posts() ) :
            the_post();
            get_template_part( 'template-parts/content', get_post_type() );
        endwhile;

        the_posts_pagination(
            array(
                'prev_text' => __( 'Previous', 'med-express' ),
                'next_text' => __( 'Next', 'med-express' ),
            )
        );
        ?>
    <?php else : ?>
        <?php get_template_part( 'template-parts/content', 'none' ); ?>
    <?php endif; ?>
</div>
<?php
get_footer();
