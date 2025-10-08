(function ($) {
    'use strict';

    function openPopup() {
        var $popup = $('#codboost-marketing-popup');
        if (!$popup.length || $popup.hasClass('is-visible')) {
            return;
        }

        $popup.attr('aria-hidden', 'false');
        $popup.addClass('is-visible');
    }

    $(document).ready(function () {
        if (!window.codboostMarketingPopup || !window.codboostMarketingPopup.enabled) {
            return;
        }

        var delay = parseInt(window.codboostMarketingPopup.delay, 10) || 0;

        window.setTimeout(openPopup, delay);

        $(document).on('click', '.codboost-popup-close', function (event) {
            event.preventDefault();
            $('#codboost-marketing-popup').removeClass('is-visible').attr('aria-hidden', 'true');
        });
    });
})(jQuery);
