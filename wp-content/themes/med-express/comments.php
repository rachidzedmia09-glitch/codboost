<?php
/**
 * Comments template.
 *
 * @package MedExpress
 */

if ( post_password_required() ) {
    return;
}
?>
<div id="comments" class="comments-area container container--narrow">
    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            if ( 1 === (int) $comment_count ) {
                printf( esc_html__( 'One thought on “%1$s”', 'med-express' ), '<span>' . get_the_title() . '</span>' );
            } else {
                printf( esc_html__( '%1$s thoughts on “%2$s”', 'med-express' ), number_format_i18n( $comment_count ), '<span>' . get_the_title() . '</span>' );
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'      => 'ol',
                    'short_ping' => true,
                    'avatar_size'=> 64,
                )
            );
            ?>
        </ol>

        <?php the_comments_navigation(); ?>

    <?php endif; ?>

    <?php
    if ( ! comments_open() && get_comments_number() ) :
        ?>
        <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'med-express' ); ?></p>
    <?php endif; ?>

    <?php
    comment_form(
        array(
            'class_submit' => 'button button--primary',
        )
    );
    ?>
</div>
