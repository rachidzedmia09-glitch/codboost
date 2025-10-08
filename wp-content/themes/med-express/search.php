<?php
/**
 * The template for displaying search results pages.
 *
 * @package MedExpress
 */

get_header();
?>
<div class="container container--narrow">
    <?php if ( have_posts() ) : ?>
        <header class="page-header">
            <h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'med-express' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
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
