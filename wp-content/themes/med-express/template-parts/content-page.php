<?php
/**
 * Template part for displaying page content.
 *
 * @package MedExpress
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card card--page' ); ?>>
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header>

    <div class="entry-content">
        <?php
        the_content();
        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . __( 'Pages:', 'med-express' ),
                'after'  => '</div>',
            )
        );
        ?>
    </div>
</article>
