<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @package MedExpress
 */
?>
<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'med-express' ); ?></h1>
    </header>

    <div class="page-content">
        <?php if ( is_search() ) : ?>
            <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'med-express' ); ?></p>
            <?php get_search_form(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'Ready to publish your first post? Get started by adding new content from your dashboard.', 'med-express' ); ?></p>
        <?php endif; ?>
    </div>
</section>
