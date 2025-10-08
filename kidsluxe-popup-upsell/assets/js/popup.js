(function () {
    if (typeof window === 'undefined' || typeof document === 'undefined') {
        return;
    }

    const modal = document.getElementById('klpu-modal');
    if (!modal) {
        return;
    }

    const globals = window.KLPUGlobals || {};
    const products = Array.isArray(window.KLPUProducts) ? window.KLPUProducts : [];
    const productMap = new Map();

    products.forEach((product) => {
        productMap.set(parseInt(product.productId, 10), product);
    });

    let currentProduct = null;
    let lastFocusedElement = null;

    const selectors = {
        addToCart: '.add_to_cart_button, .single_add_to_cart_button',
        buyNow: globals.buyNow || '',
    };

    function getText(key, fallback = '') {
        if (!globals.texts || typeof globals.texts[key] === 'undefined') {
            return fallback;
        }
        return globals.texts[key];
    }

    const elements = {
        overlay: modal.querySelector('.klpu-modal__overlay'),
        closeBtn: modal.querySelector('.klpu-modal__close'),
        image: modal.querySelector('[data-klpu-image]'),
        title: modal.querySelector('[data-klpu-title]'),
        subtitle: modal.querySelector('[data-klpu-subtitle]'),
        productTitle: modal.querySelector('[data-klpu-product-title]'),
        excerpt: modal.querySelector('[data-klpu-excerpt]'),
        priceLabel: modal.querySelector('[data-klpu-price-label]'),
        price: modal.querySelector('[data-klpu-price]'),
        profit: modal.querySelector('[data-klpu-profit]'),
        accept: modal.querySelector('[data-klpu-accept]'),
        decline: modal.querySelector('[data-klpu-decline]'),
    };

    function setTextContent(element, value) {
        if (!element) {
            return;
        }
        element.textContent = value || '';
    }

    function setHTMLContent(element, value) {
        if (!element) {
            return;
        }
        element.innerHTML = value || '';
    }

    function trapFocus(event) {
        if (!modal.classList.contains('is-active')) {
            return;
        }

        const focusable = modal.querySelectorAll(
            'a[href], button:not([disabled]), textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select, [tabindex]:not([tabindex="-1"])'
        );

        if (!focusable.length) {
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.key === 'Tab') {
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    }

    function onEscape(event) {
        if (event.key === 'Escape' && modal.classList.contains('is-active')) {
            closeModal();
        }
    }

    function focusModal() {
        const focusable = modal.querySelectorAll(
            'button:not([disabled]), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (focusable.length) {
            focusable[0].focus();
        }
    }

    function openModal(product) {
        currentProduct = product;
        lastFocusedElement = document.activeElement;

        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('is-active');

        if (elements.image) {
            elements.image.src = product.offerImage || '';
            elements.image.alt = product.offerName || '';
        }

        setTextContent(elements.productTitle, product.offerName);
        setTextContent(elements.excerpt, product.offerExcerpt);
        setHTMLContent(elements.price, product.priceDisplay);

        if (elements.priceLabel) {
            const fallbackPriceLabel = elements.priceLabel.getAttribute('data-default-text') || elements.priceLabel.textContent;
            elements.priceLabel.textContent = getText('priceLabel', fallbackPriceLabel);
        }

        if (elements.title) {
            const fallbackTitle = elements.title.getAttribute('data-default-text') || elements.title.textContent;
            elements.title.textContent = getText('title', fallbackTitle);
        }

        if (elements.subtitle) {
            const fallbackSubtitle = elements.subtitle.getAttribute('data-default-text') || elements.subtitle.textContent;
            elements.subtitle.textContent = getText('subtitle', fallbackSubtitle);
        }

        if (elements.accept) {
            const fallbackAccept = elements.accept.getAttribute('data-default-text') || elements.accept.textContent;
            elements.accept.textContent = getText('accept', fallbackAccept);
        }

        if (elements.decline) {
            const fallbackDecline = elements.decline.getAttribute('data-default-text') || elements.decline.textContent;
            elements.decline.textContent = getText('decline', fallbackDecline);
        }

        if (elements.profit && globals.showProfit === false) {
            elements.profit.hidden = true;
        } else if (elements.profit && globals.showProfit !== false) {
            elements.profit.hidden = false;
            const fallbackProfit = elements.profit.getAttribute('data-default-text') || elements.profit.textContent;
            elements.profit.textContent = getText('profitLabel', fallbackProfit);
        }

        document.body.style.overflow = 'hidden';
        focusModal();
    }

    function closeModal() {
        modal.classList.remove('is-active');
        modal.setAttribute('aria-hidden', 'true');
        window.setTimeout(() => {
            modal.hidden = true;
        }, 200);
        document.body.style.overflow = '';
        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }
        currentProduct = null;
    }

    function findProductIdFromElement(element) {
        if (!element) {
            return null;
        }

        const attr = element.getAttribute('data-klpu-product');
        if (attr) {
            return parseInt(attr, 10);
        }

        const closest = element.closest('[data-klpu-product]');
        if (closest && closest.getAttribute('data-klpu-product')) {
            return parseInt(closest.getAttribute('data-klpu-product'), 10);
        }

        const form = element.closest('form');
        if (form) {
            const input = form.querySelector('input[name="add-to-cart"]');
            if (input && input.value) {
                return parseInt(input.value, 10);
            }
        }

        const marker = document.querySelector('.klpu-marker[data-klpu-product]');
        if (marker) {
            return parseInt(marker.getAttribute('data-klpu-product'), 10);
        }

        if (element.dataset && element.dataset.productId) {
            return parseInt(element.dataset.productId, 10);
        }

        return null;
    }

    function shouldTrigger(product, context) {
        if (!product || !Array.isArray(product.triggers)) {
            return false;
        }
        return product.triggers.includes(context);
    }

    function handleTrigger(event, context) {
        const productId = findProductIdFromElement(event.target);
        if (!productId || !productMap.has(productId)) {
            return;
        }

        const product = productMap.get(productId);
        if (!shouldTrigger(product, context)) {
            return;
        }

        window.setTimeout(() => {
            openModal(product);
        }, 120);
    }

    function handleAccept() {
        if (!currentProduct || !elements.accept) {
            return;
        }

        elements.accept.disabled = true;
        elements.accept.setAttribute('aria-busy', 'true');

        const formData = new FormData();
        formData.append('action', 'klpu_add_offer');
        formData.append('nonce', globals.nonce || '');
        formData.append('product_id', currentProduct.offerProductId);
        formData.append('profit_type', currentProduct.profitType);
        formData.append('profit_value', currentProduct.profitValue);

        fetch(globals.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
            headers: {
                'X-WP-Nonce': globals.nonce || '',
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data && data.success && data.data) {
                    if (globals.fragments !== false && data.data.fragments) {
                        Object.keys(data.data.fragments).forEach((selector) => {
                            const fragmentElement = document.querySelector(selector);
                            if (fragmentElement) {
                                fragmentElement.innerHTML = data.data.fragments[selector];
                            }
                        });
                    }
                }
            })
            .catch(() => {})
            .finally(() => {
                elements.accept.disabled = false;
                elements.accept.removeAttribute('aria-busy');
                closeModal();
            });
    }

    if (elements.overlay) {
        elements.overlay.addEventListener('click', closeModal);
    }
    if (elements.closeBtn) {
        elements.closeBtn.addEventListener('click', closeModal);
    }
    if (elements.decline) {
        elements.decline.addEventListener('click', closeModal);
    }
    if (elements.accept) {
        elements.accept.addEventListener('click', handleAccept);
    }

    document.addEventListener('keydown', trapFocus);
    document.addEventListener('keydown', onEscape);

    document.addEventListener('click', (event) => {
        const addToCartTarget = event.target.closest(selectors.addToCart);
        if (addToCartTarget) {
            handleTrigger({ target: addToCartTarget }, 'add_to_cart');
        }

        if (selectors.buyNow) {
            const buyNowTarget = event.target.closest(selectors.buyNow);
            if (buyNowTarget) {
                handleTrigger({ target: buyNowTarget }, 'buy_now');
            }
        }
    });
})();
