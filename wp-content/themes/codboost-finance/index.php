<?php
/**
 * Main template file for Codboost Finance Portal.
 *
 * @package CodboostFinance
 */

declare( strict_types=1 );

get_header();
?>

<main class="site-wrapper">
    <section class="site-hero">
        <h1><?php echo esc_html__( 'Codboost Finance Control Center', 'codboost-finance' ); ?></h1>
        <p><?php echo esc_html__( 'Monitor balances, track every transaction, and collaborate with your team through a unified financial cockpit.', 'codboost-finance' ); ?></p>
        <?php if ( is_user_logged_in() ) :
            $dashboard_id   = (int) get_option( 'codboost_finance_dashboard_page' );
            $dashboard_link = $dashboard_id ? get_permalink( $dashboard_id ) : admin_url( 'admin.php?page=codboost-money-manager' );
        ?>
            <a class="cta" href="<?php echo esc_url( $dashboard_link ); ?>">
                <?php echo esc_html__( 'Open my dashboard', 'codboost-finance' ); ?>
            </a>
        <?php else : ?>
            <a class="cta" href="<?php echo esc_url( wp_login_url() ); ?>">
                <?php echo esc_html__( 'Login to manage finances', 'codboost-finance' ); ?>
            </a>
        <?php endif; ?>
    </section>

    <section class="site-content">
        <?php
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                the_content();
            }
        } else {
            echo '<p>' . esc_html__( 'Create a page and assign the “Codboost Money Manager Dashboard” template to display financial analytics.', 'codboost-finance' ) . '</p>';
        }
        ?>
    </section>

    <footer class="site-footer">
        <?php echo esc_html__( 'Codboost — Let’s take your brand to the next level.', 'codboost-finance' ); ?>
    </footer>
</main>

<?php
get_footer();
