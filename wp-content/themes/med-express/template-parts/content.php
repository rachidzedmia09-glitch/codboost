<?php
/**
 * Template part for displaying posts.
 *
 * @package MedExpress
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card card--post' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <a class="card__thumbnail" href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail( 'large' ); ?>
        </a>
    <?php endif; ?>

    <div class="card__body">
        <header class="entry-header">
            <?php
            if ( is_singular() ) {
                the_title( '<h1 class="entry-title">', '</h1>' );
            } else {
                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            }
            ?>
            <div class="entry-meta">
                <span class="entry-meta__item"><?php echo esc_html( get_the_date() ); ?></span>
                <span class="entry-meta__item"><?php esc_html_e( 'by', 'med-express' ); ?> <?php the_author_posts_link(); ?></span>
            </div>
        </header>

        <div class="entry-content">
            <?php
            if ( is_singular() ) {
                the_content();
                wp_link_pages(
                    array(
                        'before' => '<div class="page-links">' . __( 'Pages:', 'med-express' ),
                        'after'  => '</div>',
                    )
                );
            } else {
                the_excerpt();
            }
            ?>
        </div>

        <?php if ( ! is_singular() ) : ?>
            <footer class="entry-footer">
                <a class="button button--text" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Continue Reading', 'med-express' ); ?></a>
            </footer>
        <?php endif; ?>
    </div>
</article>
