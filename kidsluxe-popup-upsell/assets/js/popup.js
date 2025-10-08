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

    const body = document.body;

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

        if (body) {
            body.classList.add('klpu-modal-open');
        }
        focusModal();
    }

    function closeModal() {
        modal.classList.remove('is-active');
        modal.setAttribute('aria-hidden', 'true');
        window.setTimeout(() => {
            modal.hidden = true;
        }, 200);
        if (body) {
            body.classList.remove('klpu-modal-open');
        }
        if (elements.accept) {
            elements.accept.classList.remove('is-busy');
            elements.accept.removeAttribute('aria-busy');
            elements.accept.disabled = false;
        }
        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }
        currentProduct = null;
    }

    function normalizeProductId(value) {
        const id = parseInt(value, 10);
        return Number.isNaN(id) ? null : id;
    }

    function findProductIdFromElement(element) {
        if (!element) {
            return null;
        }

        const directAttr = element.getAttribute('data-klpu-product') || (element.dataset ? element.dataset.klpuProduct : null);
        const directId = normalizeProductId(directAttr);
        if (directId) {
            return directId;
        }

        const directProductAttr = element.getAttribute('data-product_id') || (element.dataset ? element.dataset.productId : null);
        const directProductId = normalizeProductId(directProductAttr);
        if (directProductId) {
            return directProductId;
        }

        const contexts = [];
        const productContainer = element.closest('.product, [data-klpu-product], form');
        if (productContainer) {
            contexts.push(productContainer);
        }

        if (element.closest) {
            const markerContext = element.closest('.klpu-marker[data-klpu-product]');
            if (markerContext) {
                contexts.push(markerContext);
            }
        }

        contexts.push(document);

        for (const context of contexts) {
            if (!context) {
                continue;
            }

            if (context !== document) {
                const contextAttr = context.getAttribute && context.getAttribute('data-klpu-product');
                const contextId = normalizeProductId(contextAttr);
                if (contextId) {
                    return contextId;
                }
            }

            const marker = context.querySelector ? context.querySelector('.klpu-marker[data-klpu-product]') : null;
            if (marker) {
                const markerId = normalizeProductId(marker.getAttribute('data-klpu-product') || (marker.dataset ? marker.dataset.klpuProduct : null));
                if (markerId) {
                    return markerId;
                }
            }

            if (context.matches && context.matches('[data-product_id]')) {
                const contextProductId = normalizeProductId(context.getAttribute('data-product_id'));
                if (contextProductId) {
                    return contextProductId;
                }
            }

            if (context.dataset && context.dataset.productId) {
                const datasetId = normalizeProductId(context.dataset.productId);
                if (datasetId) {
                    return datasetId;
                }
            }

            if (context.querySelector) {
                const productIdInput = context.querySelector('input[name="product_id"]');
                const addToCartInput = context.querySelector('input[name="add-to-cart"]');
                const fallbackId = normalizeProductId(productIdInput && productIdInput.value ? productIdInput.value : null)
                    || normalizeProductId(addToCartInput && addToCartInput.value ? addToCartInput.value : null);
                if (fallbackId) {
                    return fallbackId;
                }
            }
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
        elements.accept.classList.add('is-busy');

        if (!globals.ajaxUrl) {
            closeModal();
            return;
        }

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
