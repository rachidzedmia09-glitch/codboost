(function ($) {
    'use strict';

    if (!window.codboostBundleData || !window.codboostBundleData.offer) {
        return;
    }

    const data = window.codboostBundleData;
    const overlay = $('<div>', { class: 'codboost-overlay', 'aria-hidden': 'true' });
    const popup = $('<div>', { class: 'codboost-popup', role: 'dialog', 'aria-modal': 'true' });
    const closeBtn = $('<button>', { class: 'codboost-close-btn', type: 'button', 'aria-label': 'Fermer' }).html('&times;');

    function formatPrice(amount) {
        const price = parseFloat(amount);
        if (Number.isNaN(price)) {
            return '';
        }

        return price.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + '\u00a0' + data.offer.currency;
    }

    function buildPopup() {
        const header = $('<div>', { class: 'codboost-popup-header' });
        const badge = $('<span>', { class: 'codboost-badge' }).text(data.settings.title);
        const title = $('<h3>').text(data.settings.subtitle);
        const messageText = data.offer.message ? $('<p>').text(data.offer.message) : null;

        header.append(badge, title);
        if (messageText) {
            header.append(messageText);
        }

        const body = $('<div>', { class: 'codboost-popup-body' });
        const productColumn = $('<div>', { class: 'codboost-popup-product' });
        if (data.offer.image) {
            productColumn.append($('<img>', { src: data.offer.image, alt: data.offer.target_name }));
        }
        productColumn.append($('<h4>').text(data.offer.target_name));

        const pricingColumn = $('<div>', { class: 'codboost-popup-pricing' });
        const strings = data.strings || {};
        const original = (data.offer.base_regular || data.offer.base_price || 0) + (data.offer.target_regular || data.offer.target_price || 0);
        const economy = Math.max(original - data.offer.total_price, 0);

        const totalLine = $('<div>', { class: 'codboost-total' }).text(formatPrice(data.offer.total_price));
        pricingColumn.append(totalLine);

        if (original > 0) {
            const originLine = $('<p>', { class: 'codboost-original' }).text(formatPrice(original));
            const label = strings.original ? `${strings.original} : ` : 'Valeur initiale : ';
            originLine.prepend($('<span>', { class: 'screen-reader-text' }).text(label));
            originLine.css({ textDecoration: 'line-through', opacity: 0.6, margin: 0 });
            pricingColumn.append(originLine);
        }

        if (economy > 0) {
            const savingsText = strings.savings ? strings.savings.replace('%s', formatPrice(economy)) : 'Vous économisez ' + formatPrice(economy);
            const savings = $('<p>', { class: 'codboost-economy' }).text(savingsText);
            savings.css({ fontWeight: 600, margin: 0 });
            pricingColumn.append(savings);
        }

        pricingColumn.append($('<p>').text(data.price_copy || 'Profitez d\'un tarif exclusif pour compléter votre panier.'));

        const actions = $('<div>', { class: 'codboost-popup-actions' });
        const acceptBtn = $('<button>', { class: 'codboost-btn codboost-btn-primary', type: 'button' }).text(data.settings.accept_label);
        const declineBtn = $('<button>', { class: 'codboost-btn codboost-btn-ghost', type: 'button' }).text(data.settings.decline_label);

        actions.append(acceptBtn, declineBtn);
        pricingColumn.append(actions);

        body.append(productColumn, pricingColumn);

        popup.append(closeBtn, header, body);
        overlay.append(popup);
        $('body').append(overlay);

        const accent = data.settings.accent_color;
        overlay.find('.codboost-badge, .codboost-btn-primary').css('background', accent);
        overlay.find('.codboost-btn-primary').css('box-shadow', `0 12px 30px ${hexToRgba(accent, 0.25)}`);
        overlay.find('.codboost-btn-ghost').css({ color: accent, 'border-color': hexToRgba(accent, 0.4) });
        overlay.find('.codboost-popup-pricing .codboost-total').css('color', accent);

        acceptBtn.on('click', function () {
            processBaseProduct(true);
        });

        declineBtn.on('click', function () {
            processBaseProduct(false);
        });
        closeBtn.on('click', function () {
            closePopup(false);
        });

        overlay.on('click', function (event) {
            if (event.target === this) {
                closePopup(false);
            }
        });
    }

    function hexToRgba(hex, alpha) {
        const sanitized = hex.replace('#', '');
        if (sanitized.length !== 6) {
            return `rgba(39, 68, 114, ${alpha})`;
        }
        const bigint = parseInt(sanitized, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    let currentQuantity = 1;
    let triggerButton = null;
    let formData = null;
    let isProcessing = false;
    let form = null;

    function openPopup() {
        overlay.attr('aria-hidden', 'false').addClass('is-visible');
        $('body').addClass('codboost-popup-open');
    }

    function closePopup(skipAction) {
        overlay.attr('aria-hidden', 'true').removeClass('is-visible');
        $('body').removeClass('codboost-popup-open');

        if (!skipAction && !isProcessing) {
            processBaseProduct(false);
        }
    }

    function addOfferToCart() {
        return $.ajax({
            url: data.ajax_url,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'codboost_add_bundle_offer',
                nonce: data.nonce,
                base_product_id: data.offer.base_id,
                quantity: currentQuantity
            }
        }).done(function (response) {
            if (!response || !response.success) {
                return;
            }

            $(document.body).trigger('added_to_cart', [response.data.fragments, response.data.cart_hash, triggerButton]);
        });
    }

    function processBaseProduct(includeOffer) {
        if (isProcessing) {
            return;
        }

        const endpoint = window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url
            ? window.wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
            : null;

        if (!endpoint) {
            closePopup(true);
            if (form && form.length) {
                form.off('submit.codboost');
                form.get(0).submit();
            }
            return;
        }

        isProcessing = true;
        setButtonsState(true);

        $.ajax({
            url: endpoint,
            method: 'POST',
            dataType: 'json',
            data: formData
        }).done(function (response) {
            if (!response) {
                isProcessing = false;
                setButtonsState(false);
                closePopup(true);
                return;
            }

            if (response.error && response.product_url) {
                isProcessing = false;
                setButtonsState(false);
                closePopup(true);
                window.location.href = response.product_url;
                return;
            }

            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, triggerButton]);

            if (includeOffer) {
                addOfferToCart().always(function () {
                    isProcessing = false;
                    setButtonsState(false);
                    closePopup(true);
                });
            } else {
                isProcessing = false;
                setButtonsState(false);
                closePopup(true);
            }
        }).fail(function () {
            isProcessing = false;
            setButtonsState(false);
            closePopup(true);
        });
    }

    function setButtonsState(disabled) {
        overlay.find('.codboost-btn').prop('disabled', disabled);
    }

    $(document).ready(function () {
        buildPopup();

        form = $('form.cart');
        if (!form.length) {
            return;
        }

        form.on('click', '.single_add_to_cart_button', function () {
            triggerButton = $(this);
            currentQuantity = parseInt(form.find('input.qty').val(), 10) || 1;
        });

        form.on('submit.codboost', function (event) {
            event.preventDefault();

            if (overlay.hasClass('is-visible') || isProcessing) {
                return;
            }

            if (!triggerButton) {
                triggerButton = form.find('.single_add_to_cart_button');
            }

            currentQuantity = parseInt(form.find('input.qty').val(), 10) || 1;
            formData = form.serialize();

            openPopup();
        });
    });
})(jQuery);
