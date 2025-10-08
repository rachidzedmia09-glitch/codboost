<?php
/**
 * Modal template for Kids-Luxe offer.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/kidsluxe/modal-offer.php
 */

defined( 'ABSPATH' ) || exit;

$title    = apply_filters( 'klpu_offer_title', KLPU_get_option( 'popup_title' ) );
$subtitle = apply_filters( 'klpu_offer_subtitle', KLPU_get_option( 'popup_subtitle' ) );
$accept   = KLPU_get_option( 'popup_accept_label' );
$decline  = KLPU_get_option( 'popup_decline_label' );
$price    = apply_filters( 'klpu_offer_price_label', __( 'Prix spécial :', 'kidsluxe-popup-upsell' ) );
$show_profit = KLPU_get_option( 'show_profit_notice', true );
$profit_notice = __( 'Inclut une marge Kids-Luxe.', 'kidsluxe-popup-upsell' );
?>
<div id="klpu-modal" class="klpu-modal" role="dialog" aria-modal="true" aria-labelledby="klpu-modal-title" aria-hidden="true" hidden>
    <div class="klpu-modal__overlay" data-klpu-close></div>
    <div class="klpu-modal__content" role="document">
        <button type="button" class="klpu-modal__close" aria-label="<?php esc_attr_e( 'Fermer le pop-up', 'kidsluxe-popup-upsell' ); ?>" data-klpu-close>
            <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true" focusable="false"><path d="M2 2l14 14m0-14L2 16" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="klpu-modal__body">
            <div class="klpu-offer__visual">
                <img src="" alt="" class="klpu-offer__image" data-klpu-image />
            </div>
            <div class="klpu-offer__details">
                <h2 id="klpu-modal-title" class="klpu-offer__title" data-klpu-title data-default-text="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></h2>
                <p class="klpu-offer__subtitle" data-klpu-subtitle data-default-text="<?php echo esc_attr( $subtitle ); ?>"><?php echo esc_html( $subtitle ); ?></p>
                <h3 class="klpu-offer__product" data-klpu-product-title></h3>
                <p class="klpu-offer__excerpt" data-klpu-excerpt></p>
                <p class="klpu-offer__price"><span class="klpu-offer__price-label" data-klpu-price-label data-default-text="<?php echo esc_attr( $price ); ?>"><?php echo esc_html( $price ); ?></span> <span data-klpu-price></span></p>
                <?php if ( $show_profit ) : ?>
                    <p class="klpu-offer__profit" data-klpu-profit data-default-text="<?php echo esc_attr( $profit_notice ); ?>"><?php echo esc_html( $profit_notice ); ?></p>
                <?php endif; ?>
                <div class="klpu-offer__actions">
                    <button type="button" class="klpu-button klpu-button--primary" data-klpu-accept data-default-text="<?php echo esc_attr( $accept ); ?>"><?php echo esc_html( $accept ); ?></button>
                    <button type="button" class="klpu-button klpu-button--ghost" data-klpu-decline data-default-text="<?php echo esc_attr( $decline ); ?>"><?php echo esc_html( $decline ); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="klpu-modal-template" hidden></div>
