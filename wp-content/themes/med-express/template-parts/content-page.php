<?php
/**
 * Template part for displaying page content.
 *
 * @package MedExpress
 */

$is_elementor_page = function_exists( 'medexpress_is_elementor_page' ) ? medexpress_is_elementor_page( get_the_ID() ) : false;

if ( $is_elementor_page ) :
    ?>
    <div class="entry entry--page entry--elementor">
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
    <?php
    return;
endif;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
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
