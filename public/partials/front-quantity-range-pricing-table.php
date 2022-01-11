<?php
/**
 * Admin quantity wish pricing fronted table.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/public/partials
 */

?>
<table class='variation_quantity_table'>
	<thead>
	<tr>
		<th><?php echo $heading_quantity; ?></th>
		<th><?php echo $heading_price; ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$as_quantity_rage_values = Woo_Products_Quantity_Range_Pricing_Admin::woo_quantity_value_sorting_by_order( $as_quantity_rage_values );
	// Display all quantity price values in table.
	$valid_data_count = 0;
	foreach ( $as_quantity_rage_values as $as_quantity_rage_value ) {
		$data_show = false;
		if( empty( $as_quantity_rage_value['role'] ) ) { $as_quantity_rage_value['role'] = ''; }
		$roles = $as_quantity_rage_value['role'];
		if( ! empty( $roles ) ) {
			if( is_user_logged_in() ) {
				$user_info = get_userdata( get_current_user_id() );
				foreach ( $user_info->roles as $role ) {
					if( in_array( $role, $roles ) ) {
						$data_show = true;
						$valid_data_count++;
					}
				}
			}else{
				$user_roles = get_option( 'as_wpqrp_user_role', '' );
				foreach ( $user_roles as $role ) {
					if( in_array( $role, $roles ) ) {
						$data_show = true;
						$valid_data_count++;
					}
				}
			}
		} else {
			$data_show = true;
			$valid_data_count++;
		}

		if ( ! empty( $as_quantity_rage_value['min_qty'] ) && $as_quantity_rage_value['max_qty'] && $as_quantity_rage_value['price'] && $data_show != false ) {

			$type  = $as_quantity_rage_value['type'];
			$price = $as_quantity_rage_value['price']; ?>
			<tr>
				<td>
					<?php
					echo esc_attr( $as_quantity_rage_value['min_qty'] );
					if ( $as_quantity_rage_value['max_qty'] == - 1 ) {
						echo ' ' . $label_or_more;
					} else {
						echo esc_attr( ' - ' . $as_quantity_rage_value['max_qty'] );
					}
					?>
				</td>
				<td>

					<?php
					// get woocommerce price decimal separator.
					$decimal_separator = wc_get_price_decimal_separator();

					// get woocommerce price thousand separator.
					$thousand_separator = wc_get_price_thousand_separator();

					// get woocommerce price decimals.
					$decimals = wc_get_price_decimals();

					$price = str_replace( $decimal_separator, "#", $price );
					$price = str_replace( $thousand_separator, "", $price );
					$price = str_replace( "#", ".", $price );

					switch ( $type ) {

						case 'percentage':
							$label_discount 		= apply_filters( 'wpqrp_label_discount',  __( ' Discount ', 'woo-products-quantity-range-pricing' ) );
							$price_display 			= number_format( ( $final_price - ( ( $final_price * $price ) / 100 ) ), $decimals, $decimal_separator, $thousand_separator );
							echo esc_attr( $currency . $price_display . ' ( ' . $price . ' % ' . $label_discount .' )' );
							break;

						case 'price':
							$label_discount 		= apply_filters( 'wpqrp_label_discount', __( ' Discount ', 'woo-products-quantity-range-pricing' ) );
							$price_display 			= number_format( ( $final_price - $price ), $decimals, $decimal_separator, $thousand_separator );
							echo esc_attr( $currency . $price_display . ' ( ' . $currency . $price . $label_discount . ' ) ' );
							break;

						case 'fixed':
							$label_selling_pricing 	= apply_filters( 'wpqrp_label_selling_pricing',  __( '( Selling price )', 'woo-products-quantity-range-pricing' ) );
							$price_display 			= number_format( $price, $decimals, $decimal_separator, $thousand_separator );
							echo $currency . $price_display . $label_selling_pricing;
							break;	

					}
					?>

				</td>
			</tr>

			<?php
		}
	}
	// End foreach.
	if( $valid_data_count == 0 ) {
		?>
		<style>
		.variation_quantity_table{ display: none; }
		</style>
		<?php
	}
	?>

	</tbody>
</table>

