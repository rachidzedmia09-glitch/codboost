<?php
/**
 * The template for displaying archive pages.
 *
 * @package MedExpress
 */

get_header();
?>
<div class="container container--narrow">
    <?php if ( have_posts() ) : ?>
        <header class="page-header">
            <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="archive-description">', '</div>' );
            ?>
        </header>

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
