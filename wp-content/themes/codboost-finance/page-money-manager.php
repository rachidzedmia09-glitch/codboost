<?php
/**
 * Template Name: Codboost Money Manager Dashboard
 * Description: Displays the Codboost Money Manager interface for authenticated users.
 *
 * @package CodboostFinance
 */

declare( strict_types=1 );

global $post;

get_header();
?>

<main class="site-wrapper">
    <section class="site-hero">
        <h1><?php the_title(); ?></h1>
        <p><?php echo esc_html__( 'A secure workspace to capture income, monitor expenses, and keep track of Codboost financial health.', 'codboost-finance' ); ?></p>
    </section>

    <section class="site-content money-manager-wrapper">
        <?php
        while ( have_posts() ) {
            the_post();
            the_content();
        }
        ?>
    </section>
</main>

<?php
get_footer();
