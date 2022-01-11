(function ($) {
    'use strict';

    /**
     *Apply Colorpicker for color setting fields
     */
    $(document).ready(function () {
        $('.woo-color-field').wpColorPicker();
        if( $( ".wpqrp_chosen_select" ).length ) {
			$(".wpqrp_chosen_select").select2();
		}
    });

    $(document).on('click','.woocommerce_variation',function () {
        $(document).find("select.wpqrp_chosen_select").select2();
    });

    /**
     * This click event are use for add new fields of quantity range.
     */
    $(document).on('click', '.add-quantity-range-price-field-btn', function () {

        var value_id = $(this).attr('value-id');
        var table_id = "#as-table-" + value_id;
        var data_valid = true;
        // Get last filed row ID.
        var row_id = $(table_id + " tbody tr").last().attr('row-number');
        var options_group = $(this).closest('.options_group');
        $(options_group).find('.as-table tbody tr').each(function () {
            var min_valid = check_field_values($(this).find('.wqap-min-quantity'));
            var max_valid = check_field_values($(this).find('.wqap-max-quantity'));
            var price_valid = check_field_values($(this).find('.wqap-pricing'));
            if (!min_valid && data_valid) {
                data_valid = false
            }
            if (!max_valid && data_valid) {
                data_valid = false
            }
            if (!price_valid && data_valid) {
                data_valid = false
            }
            if (!data_valid) {
                alert("Please enter all field values");
                return false;
            }
        });
        if (data_valid) {
            // Ajax event for get new filed HTML
            var data = {
                'action': 'woo_get_new_quantity_range_price_fields',
                'row_id': ++row_id,   // We pass row_id value!
                'value_id': value_id	// We pass value_id value!
            };

            // We can also pass the url value separately from ajaxurl for AJAX implementations
            jQuery.post(as_woo_pricing.ajax_url, data, function (response) {
                // Append new filed HTML responce in product
                $(table_id + " tbody").append(response);
                $(".wpqrp-role-"+row_id).select2();
                $(".wpqrp-pricing-type-"+row_id).select2();
            });
        }

    });

    /**
     * This click event are use for delete field row of quantity range.
     */
    $(document).on('click', '.delete-quantity', function () {

        // Get value ID if quantity range fileds.
        var value_id = $(this).attr('value-id');

        // Get row ID if quantity range fileds.
        var row_id = $(this).attr('data-id');

        // Get Current variation panel box details.
        var is_variation = $(this).closest(".woocommerce_variation");

        // Confirm condition of delete filed row
        if (confirm('Are you sure? you want to delete that quantity range price?')) {

            // Remove row of range price from product
            $("#as-table-" + value_id + " .as-fileds-row-" + row_id).remove();

            if (is_variation != "") {
                // For unable variation save & cancel buttons 
                is_variation.addClass('variation-needs-update');
                $(".save-variation-changes").prop("disabled", false);
                $(".cancel-variation-changes").prop("disabled", false);
            }
        }

    });

    /**
     * This focusout event are use for validation of quantity minimum and maximum values options.
     */
    $(document).on('focusout', '.wqap-min-quantity,.wqap-max-quantity', function () {
        var current_value = $(this);

        //This is number of current row.
        var row_number = $(this).closest('.as-fileds-row').attr('row-number');
        var current_table = $(this).closest('.as-table');
        //This condition for check class have or not
        if ($(this).hasClass('wqap-min-quantity')) {
            var max_val = $(current_table).find('.as-fileds-row-' + row_number + ' .wqap-max-quantity').val();
            //Check minimum quantity value with maximum quantity value.
            if (parseInt(max_val) <= parseInt(current_value.val()) && max_val != "" && max_val != -1 ) {
                alert("You need to enter smallest and different value compare with maximum quantity value " + max_val);
                current_value.val('');
                current_value.focus();
                return false;
            }
        } else {
            var min_val = $(current_table).find('.as-fileds-row-' + row_number + ' .wqap-min-quantity').val();
            //Check maximum quantity value with minimum quantity value.
            if (parseInt(min_val) >= parseInt(current_value.val()) && min_val != "" && current_value.val() != -1) {
                alert("You need to enter biggest and different value compare with minimum quantity value " + min_val);
                current_value.val('');
                current_value.focus();
                return false;
            }
        }
        var options_group = $(this).closest('.options_group');
        $(options_group).find('.as-table tbody tr').each(function () {
            if ($(this).attr('row-number') != row_number) {
                if (parseInt($(this).find('.wqap-min-quantity').val()) <= parseInt(current_value.val()) && parseInt($(this).find('.wqap-max-quantity').val()) >= parseInt(current_value.val())) {
                    alert("Minimum Quantity : " + current_value.val() + " - This quantity already added in Quantity Range Pricing");
                    current_value.val('');
                    current_value.focus();
                    return false;
                }
            }
        });
    });

    /**
     * This change event are use for enable / disable quantity range pricing options.
     */
    $(document).on('change', '.enable_simple_product_wqrp input', function () {

        // Get ID of as-box div
        var div_id = $(this).attr('data-id');

        var enable_wqrp = $(this).prop('checked');

        if (enable_wqrp == true) {
            $('.as-box-' + div_id).attr('style', 'display:block !important');
        } else {
            $('.as-box-' + div_id).attr('style', 'display:none !important');
        }


    });

    /**
     * This change event are use for enable / disable quantity range pricing options.
     */
    $(document).on('change', '#product-type', function () {

        // Get select type of product
        var select_type = $(this).val();
        // Get post id of product
        var post_ID = $("#post_ID").val();

        if ( select_type == "simple" ) {
            var enable_wqrp = $('input[name="woo-qrp-enable-' + post_ID + '"]').prop('checked');

            if ( enable_wqrp == true ) {
                $( '.as-box-' + post_ID ).attr('style', 'display:block !important');
            } else {
                $( '.as-box-' + post_ID ).attr('style', 'display:none !important');
            }
        }

    });

    /*
     * This function use check field value.
     */
    function check_field_values(filed_data) {
        if ( filed_data.val() == "" || filed_data.val() == 0 ) {
            $(filed_data).css('border', '1px solid red');
            return false;
        } else {
            $(filed_data).css('border', '1px solid #DDDDDD');
            return true;
        }
    }

})(jQuery);
