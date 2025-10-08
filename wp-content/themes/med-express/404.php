<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package MedExpress
 */

get_header();
?>
<div class="container container--narrow">
    <section class="error-404 not-found">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e( 'Oops! That page can’t be found.', 'med-express' ); ?></h1>
        </header>

        <div class="page-content">
            <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'med-express' ); ?></p>
            <?php get_search_form(); ?>
        </div>
    </section>
</div>
<?php
get_footer();
