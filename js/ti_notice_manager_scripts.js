/**
 * Main scripts file for the welcome notice
 */

/* global tiAboutNotice */

(function ($) {
    $(document).ready(function () {
        $(document).on('click', '.notice.ti-about-notice .notice-dismiss', function () {
            jQuery.ajax({
                async: true,
                type: 'POST',
                data: {
                    action: 'ti_about_dismiss_welcome_notice',
                    nonce: tiAboutNotice.dismissNonce
                },
                url: tiAboutNotice.ajaxurl,
                success: function ( response ) {
                    console.log(response);
                    $(' .ti-about-notice ').fadeOut();
                }
            });
        });
    });
})(jQuery);