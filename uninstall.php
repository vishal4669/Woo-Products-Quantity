<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//This function call for delete tha plugin data.
uninstall_woo_products_quantity_range_pricing();

/**
 * This function are delete all plugin data pligin delete time.
 */
function uninstall_woo_products_quantity_range_pricing() {

	delete_post_meta_by_key( '_as_quantity_range_pricing_values' );
	delete_post_meta_by_key( '_as_quantity_range_pricing_enable' );
	$plugin_options = array(
		'woo_qrp_enable',
		'as_border_size',
		'as_border_style',
		'as_border_color',
		'as_table_display',
		'as_text_align',
		'as_title_bg_color',
		'as_title_text_color',
		'as_price_bg_color',
		'as_price_text_color',
	);
	foreach ( $plugin_options as $plugin_option ) {
		delete_option( $plugin_option );
	}

}
