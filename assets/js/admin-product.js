(function ($) {
    'use strict';

    const toggleFields = () => {
        const isEnabled = $('#codboost_bundle_enabled').is(':checked');
        const group = $('#codboost_bundle_data .codboost-bundle-dependent-group');

        if (isEnabled) {
            $('.codboost-bundle-dependent').removeClass('hidden');
            group.removeClass('hidden');
            $('#codboost_bundle_data').removeClass('codboost-panel-disabled');
        } else {
            $('.codboost-bundle-dependent').addClass('hidden');
            group.addClass('hidden');
            $('#codboost_bundle_data').addClass('codboost-panel-disabled');
        }
    };

    $(document).ready(() => {
        toggleFields();
        $(document.body).on('change', '#codboost_bundle_enabled', toggleFields);
        $(document.body).on('woocommerce-product-type-change', toggleFields);
    });
})(jQuery);
