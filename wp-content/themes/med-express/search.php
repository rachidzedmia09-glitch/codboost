<?php
/**
 * Search results template.
 *
 * @package MedExpress
 */

get_header();

if ( function_exists( 'medexpress_elementor_render_location' ) && medexpress_elementor_render_location( array( 'search', 'archive', 'archive-post' ) ) ) {
    get_footer();
    return;
}
?>
<div class="site-container">
    <header class="page-header">
        <h1 class="page-title">
            <?php printf( esc_html__( 'Search Results for: %s', 'med-express' ), '<span>' . get_search_query() . '</span>' ); ?>
        </h1>
    </header>

    <?php if ( have_posts() ) : ?>
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
