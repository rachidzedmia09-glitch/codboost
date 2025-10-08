(function ($) {
    'use strict';

    $(function () {
        // Smooth scroll for internal anchor links.
        $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').on('click', function (event) {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                const target = $(this.hash);
                if (target.length) {
                    event.preventDefault();
                    $('html, body').animate({ scrollTop: target.offset().top - 80 }, 700);
                }
            }
        });

        // FAQ accordion behaviour for details elements in unsupported browsers.
        const faqItems = document.querySelectorAll('.faq__item');
        faqItems.forEach(function (item) {
            const summary = item.querySelector('summary');
            if (!summary) {
                return;
            }

            summary.addEventListener('click', function () {
                faqItems.forEach(function (other) {
                    if (other !== item) {
                        other.removeAttribute('open');
                    }
                });
            });
        });
    });
})(jQuery);
