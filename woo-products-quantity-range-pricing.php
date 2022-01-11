<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              akashsoni.com
 * @since             1.0.0
 * @package           Woo_Products_Quantity_Range_Pricing
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Products Quantity Range Pricing
 * Plugin URI:        woo-products-quantity-range-pricing
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.1.0
 * Author:            Akash Soni
 * Author URI:        akashsoni.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-products-quantity-range-pricing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-products-quantity-range-pricing -activator.php
 */
function activate_woo_products_quantity_range_pricing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-products-quantity-range-pricing-activator.php';
	Woo_Products_Quantity_Range_Pricing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-products-quantity-range-pricing -deactivator.php
 */
function deactivate_woo_products_quantity_range_pricing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-products-quantity-range-pricing-deactivator.php';
	Woo_Products_Quantity_Range_Pricing_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_products_quantity_range_pricing' );
register_deactivation_hook( __FILE__, 'deactivate_woo_products_quantity_range_pricing' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-products-quantity-range-pricing.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_products_quantity_range_pricing() {

	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$plugin = new Woo_Products_Quantity_Range_Pricing();
		$plugin->run();
	} else {
        if( is_admin() ) {
            as_woocommerce_activation_notice();
        }
	}

}

/**
 * Show notice message on admin plugin page.
 *
 * Callback function for admin_notices(action).
 *
 * @since  1.0.0
 * @access public
 */
function as_woocommerce_activation_notice() {
	?>
	<div class="error">
		<p>
			<?php echo '<strong> Woo Products Quantity Range Pricing </strong> requires <a target="_blank" href="https://wordpress.org/plugins/woocommerce/">Woocommerce</a> to be installed & activated!' ; ?>
		</p>
	</div>
	<?php
}


run_woo_products_quantity_range_pricing();
