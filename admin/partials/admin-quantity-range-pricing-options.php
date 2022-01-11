<?php
/**
 * Admin quantity wish pricing options.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin/partials
 */

?>
<div class="enable_simple_product_wqrp">
	<label><?php echo esc_attr_e( 'Enable Product Quantity Range Pricing facility?', 'woo-products-quantity-range-pricing' ); ?></label>
	<label class="switch">
		<input type="checkbox" <?php if ( 'on' === $woo_qrp_enable ) { echo 'checked'; } ?>
		       name="woo-qrp-enable-<?php echo esc_attr( $value_id ); ?>" data-id="<?php echo esc_attr( $value_id ); ?>">
		<div class="slider round"></div>
	</label>
</div>
<div class="options_group pricing  <?php echo esc_attr_e( $simple_show_class ); ?> as-box as-box-<?php echo esc_attr( $value_id ); ?>">
	<div class="heading-area">
		<label
			class="as-label"><?php echo esc_attr_e( 'Quantity Range Pricing', 'woo-products-quantity-range-pricing' ); ?></label>
		<input type="button" class="button button-primary button-large add-quantity-range-price-field-btn"
		       value-id="<?php echo esc_attr( $value_id ); ?>" value="Add New Price">
	</div>
	<table id="as-table-<?php echo esc_attr( $value_id ); ?>" class="as-table">
		<thead>
		<tr>
			<td>
				<label
					class="as-label"><?php echo esc_attr_e( 'Min Quantity', 'woo-products-quantity-range-pricing' ); ?></label>
			</td>
			<td>
				<label
					class="as-label"><?php echo esc_attr_e( 'Max Quantity', 'woo-products-quantity-range-pricing' ); ?></label>
			</td>
			<td>
				<label class="as-label"><?php echo esc_attr_e( 'Price type', 'woo-products-quantity-range-pricing' ); ?></label>
			</td>
			<td>
				<label class="as-label"><?php echo esc_attr_e( 'Price', 'woo-products-quantity-range-pricing' ); ?></label>
			</td>
			<td>
				<label class="as-label"><?php echo esc_attr_e( 'User Role', 'woo-products-quantity-range-pricing' ); ?></label>
			</td>
			<td>
				<label class="as-label"><?php echo esc_attr_e( 'Action', 'woo-products-quantity-range-pricing' ); ?></label>
			</td>
		</tr>
		</thead>
		<tbody>
		<?php echo $quantity_wish_pricing_fields; ?>
		</tbody>
	</table>
	<div class="wqrp-note">
		<span
			class="wqrp-red"><?php echo esc_attr_e( 'Note 1 : Setting Max quantity to -1 will be taken as maximum quantity.', 'woo-products-quantity-range-pricing' ); ?></span></br>
		<span
			class="wqrp-red"><?php echo esc_attr_e( 'Note 2 : User Role field null means that quantity range apply for all users.', 'woo-products-quantity-range-pricing' ); ?></span></br>
		<span
			class="wqrp-green"><?php echo esc_attr_e( '( E.G :  Min quantity =  20 and Max quantity = -1 means it apply to More then 20 quantity )', 'woo-products-quantity-range-pricing' ); ?></span>
	</div>
</div>
