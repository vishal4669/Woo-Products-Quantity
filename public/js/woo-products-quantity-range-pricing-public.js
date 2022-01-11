(function ($) {
    'use strict';

    // This Change event for get variaton quantity range price table on product detail page.
    $(document).on('change', '.variation_id', function () {

        var variation_id = $('.variation_id').val();      

        if ( variation_id != "" ) {

            var data = {
                'action': 'wc_get_select_variation_quantity_pricing_table',
                'variation_id': variation_id, // We pass variation ID.
                'variation_call': 1 // We pass ajax status variation.
            };

            jQuery.post(as_woo_pricing.ajax_url, data, function (response) {
                $(".variation_quantity_table").remove();
                if( response != 0 ){
                    $(".variations").before(response);
                }
            });

        } else {

            $(".variation_quantity_table").remove();

        }
        
    });

    jQuery('.variations_form').attr('data-product_variations');
})(jQuery);
