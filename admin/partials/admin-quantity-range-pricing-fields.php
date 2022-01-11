<?php
/**
 * Admin quantity wish pricing option fields.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin/partials
 */

?>

<tr class="as-fileds-row-<?php echo esc_attr( $number ); ?> as-fileds-row" row-number="<?php echo esc_attr( $number ); ?>" >
	<td>
		<input placeholder="<?php esc_attr_e( 'Min Quantity', 'woo-products-quantity-range-pricing' ); ?>"
		       value="<?php echo esc_attr( $min_quantity ); ?>" class="wqap-min-quantity" type="number"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][min_qty]"
		       value="" min="0"/>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'Max Quantity', 'woo-products-quantity-range-pricing' ); ?>"
		       value="<?php echo esc_attr( $max_quantity ); ?>" class="wqap-max-quantity" type="number"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][max_qty]"
		       value="" min="-1"/>
	</td>
	<td>
		<select class="pricing-type wpqrp-pricing-type-<?php echo esc_attr( $number ); ?> wpqrp_chosen_select"
		        name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][type]"
				onload="function(){ alert('ff'); $('.wpqrp-pricing-type-<?php echo esc_attr( $number ); ?>').select2(); }" >

			<?php
			foreach ( $type_values as $key => $value ) :
				?>
				<option
					value="<?php echo esc_attr( $value ); ?>" <?php if ( $type == $value ) { echo 'selected'; } ?> ><?php echo esc_attr( $key ); ?></option>
			<?php endforeach; ?>
		</select>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'Enter Price ', 'woocommerce' ); ?>" class="as_pricing wqap-pricing wc_input_price"
		       value="<?php echo esc_attr( $price ); ?>" type="text"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][price]"
		       value=""
		       min="0"/>
	</td>
	<td>
		<select class="wpqrp-select-role wpqrp-role-<?php echo esc_attr( $number ); ?> wpqrp_chosen_select"
				name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][role][]" multiple>
			<?php foreach ( $wp_roles->roles as $key=>$value ): ?>
				<?php if( in_array( $key, $wpqrp_roles ) ) { $selected = 'selected'; } else { $selected = ''; } ?>
				<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value['name']; ?></option>
			<?php endforeach; ?>
		</select>
	</td>
	<td>
		<?php if ( $number > 0 ) { ?>
			<a type="button" class="button dashicons dashicons-trash delete-quantity"
			   data-id="<?php echo esc_attr( $number ); ?>"
			   value-id="<?php echo esc_attr( $value_id ); ?>"></a>
		<?php } ?>
	</td>
</tr>