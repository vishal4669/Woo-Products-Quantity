<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/public
 * @author     Akash Soni <soniakashc@gmail.com>
 */
class Woo_Products_Quantity_Range_Pricing_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$woo_qrp_enable = get_option( 'woo_qrp_enable', false );

		if ( $woo_qrp_enable ) {

			// This action use for apply quantity range price in cart items.
			add_action( 'woocommerce_before_calculate_totals', array(
				$this,
				'woo_product_quantity_range_prices_apply_in_cart',
			) );

			// This action use for show list of quality range price details on product detail page.
			add_action( 'woocommerce_before_add_to_cart_form', array(
				$this,
				'wc_show_quantity_range_price_list_table',
			) );

			// Ajax action for victor user get product quantity range price table.
			add_action( 'wp_ajax_nopriv_wc_get_select_variation_quantity_pricing_table', array(
				$this,
				'wc_show_quantity_range_price_list_table',
			) );

			// Ajax action for login user get product quantity range price table.
			add_action( 'wp_ajax_wc_get_select_variation_quantity_pricing_table', array(
				$this,
				'wc_show_quantity_range_price_list_table',
			) );

		}

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Products_Quantity_Range_Pricing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Products_Quantity_Range_Pricing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-products-quantity-range-pricing-public.css', array(), $this->version, 'all' );

		$custom_inline_style = '.variation_quantity_table {
			border: 1px solid #eee;
			display:' . get_option( 'as_table_display', 'none' ) . '
		}

		.variation_quantity_table tr th:nth-child(1) {
			width: 120px;
		}

		.variation_quantity_table thead tr th {
			background: ' . get_option( 'as_title_bg_color', '#4D82B1' ) . ';
			color: ' . get_option( 'as_title_text_color', '#ffffff' ) . ';
		}

		.variation_quantity_table tr td {
			background: ' . get_option( 'as_price_bg_color', '#eeeeee' ) . ';
			color: ' . get_option( 'as_price_text_color', '#000000' ) . ';
		}

		.variation_quantity_table tr td:last-child, .variation_quantity_table tr th:last-child {
			border-right-width: ' . get_option( 'as_border_size', '1' ) . 'px !important;
		}

		.variation_quantity_table tr:last-child td {
			border-bottom-width: ' . get_option( 'as_border_size', '1' ) . 'px !important;
		}

		.variation_quantity_table tr td, .variation_quantity_table tr th {
			border: ' . get_option( 'as_border_size', '1' ) . 'px ' . get_option( 'as_border_style', 'solid' ) . ' ' . get_option( 'as_border_color', '#4D82B1' ) . ';
			text-align: ' . get_option( 'as_text_align', 'left' ) . ';
			padding: 5px 10px !important;
			border-bottom-width: 0;
			border-right-width: 0;
		}';

		wp_add_inline_style( $this->plugin_name, $custom_inline_style );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Products_Quantity_Range_Pricing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Products_Quantity_Range_Pricing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-products-quantity-range-pricing-public.js', array( 'jquery' ), $this->version, false );

		// Create as_woo_pricing object ajax url variable in frontend side for ajax request.
		wp_localize_script( $this->plugin_name, 'as_woo_pricing',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);

	}

	/**
	 * Apply items quantity range price check and add in cart.
	 *
	 * Callback function for woocommerce_before_calculate_totals (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    object $cart_object This is oject of cart items.
	 */
	public function woo_product_quantity_range_prices_apply_in_cart( $cart_object ) {

		foreach ( $cart_object->cart_contents as $key => $value ) {
			$taxonomy_use = false;
			$term_item_id = '';
			if ( ! empty( $value['variation_id'] ) ) {
				$item_id          = $value['variation_id'];
				$woo_qrp_enable          = get_post_meta( $item_id, '_as_quantity_range_pricing_enable', true );
				if( empty( $woo_qrp_enable ) ) {
					$item_id       = $value['product_id'];
				}
			} else {
				$item_id       = $value['product_id'];				
			}
			$woo_qrp_enable          = get_post_meta( $item_id, '_as_quantity_range_pricing_enable', true );
			$as_values = $this->wc_get_quantity_range_price_list( $item_id );
			if ( $as_values ) {
				$as_quantity_rage_values = $as_values['range_prices'];
				$final_price = $as_values['price'];
				$as_quantity_rage_values = Woo_Products_Quantity_Range_Pricing_Admin::woo_quantity_value_sorting_by_order( $as_quantity_rage_values );
				if ( ! empty( $as_quantity_rage_values ) ) {
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
									}
								}
							}else{
								$user_roles = get_option( 'as_wpqrp_user_role', '' );
								foreach ( $user_roles as $role ) {
									if( in_array( $role, $roles ) ) {
										$data_show = true;
									}
								}
							}
						} else {
							$data_show = true;
						}
						if ( ! empty( $as_quantity_rage_value['min_qty'] ) && $as_quantity_rage_value['max_qty'] && $as_quantity_rage_value['price'] && $data_show != false ) {

							if ( ( $as_quantity_rage_value['min_qty'] <= $value['quantity'] && $value['quantity'] <= $as_quantity_rage_value['max_qty'] ) || ( $as_quantity_rage_value['min_qty'] <= $value['quantity'] && $as_quantity_rage_value['max_qty'] == - 1 ) ) {

								$type  = $as_quantity_rage_value['type'];
								$price = $as_quantity_rage_value['price'];

                            	// get woocommerce decimal separator.
								$decimal_separator = wc_get_price_decimal_separator();
                            	// get woocommerce thousand separator.
								$thousand_separator = wc_get_price_thousand_separator();
								$price = str_replace( $decimal_separator, "#", $price );
								$price = str_replace( $thousand_separator, "", $price );
								$price = str_replace( "#", ".", $price );

								if ( ! empty( $woo_qrp_enable ) ) {
									switch ( $type ) {

										case 'percentage':
										$value['data']->set_price( $final_price - ( ( $final_price * $price ) / 100 ) );
										break;

										case 'price':
										$value['data']->set_price( $final_price - $price );
										break;

										case 'fixed':
										$value['data']->set_price( $price );
										break;
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Create quantity rage price list table on product detail page.
	 *
	 * Callback function for wp_ajax_nopriv_{action_name} , wp_ajax_{action_name} And
	 * woocommerce_before_add_to_cart_form(action).
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function wc_show_quantity_range_price_list_table() {
		
		$heading_quantity 		= apply_filters( 'wpqrp_heading_quantity', __( 'Quantity', 'woo-products-quantity-range-pricing' ) );
		$heading_price 			= apply_filters( 'wpqrp_heading_price', __( 'Price', 'woo-products-quantity-range-pricing' ) );
		$label_or_more 			= apply_filters( 'wpqrp_label_or_more', __( 'or more', 'woo-products-quantity-range-pricing' ) );
		$as_values = $this->wc_get_quantity_range_price_list();
		if ( $as_values ) {
			$as_quantity_rage_values = $as_values['range_prices'];
			$final_price = $as_values['price'];
			$currency  = get_woocommerce_currency_symbol();
			if ( ! empty( $as_quantity_rage_values ) ) {
				include 'partials/front-quantity-range-pricing-table.php';
			}
		}		
		if ( isset( $_POST['variation_id'] ) && isset( $_POST['variation_call'] )) {
			die();
		}

	}

	/**
	 * Create quantity rage price list table on product detail page.
	 *
	 * Callback function for wp_ajax_nopriv_{action_name} , wp_ajax_{action_name} And
	 * woocommerce_before_add_to_cart_form(action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    int $product_id This is id of product. 
	 */
	public function wc_get_quantity_range_price_list( $product_id = '' ) {
		global $wpdb, $product, $woocommerce;
		$data_html = $item_id   = $term_item_id ='';
		
		$taxonomy_use = false;
		if ( isset( $_POST['variation_id'] ) ) {
			$item_id          = $_POST['variation_id'];
		} else {
			if ( $product_id ) {
				$item_id = $product_id;
			} else {
				if( version_compare( $woocommerce->version, '3.0', "<=" ) ) {
					$item_id = $product->id;
				} else {
					$item_id = $product->get_id();
				}		
			}
			
			$as_woo_qrp_enable       = get_post_meta( $item_id, '_as_quantity_range_pricing_enable', true );
			if( empty( $as_woo_qrp_enable ) ) {
				$taxonomy_use = true;
			}
		}
		$regular_price = get_post_meta( $item_id, '_regular_price', true );
		$sale_price    = get_post_meta( $item_id, '_sale_price', true );
		if( $taxonomy_use ) {
            //Get all product terms
			$product_terms     = wp_get_object_terms( $item_id, 'product_cat' );
			$product_tag_terms = wp_get_object_terms( $item_id, 'product_tag' );
            // Check product have at taxonomy terms and product shipping added or not.

			if ( ! empty( $product_terms ) ) {

                // Check Product term have any error.
				if ( ! is_wp_error( $product_terms ) ) {

                    // Looping of product terms.
					foreach ( $product_terms as $term ) {

						$waps_category_enable       = get_term_meta( $term->term_id, '_as_quantity_range_pricing_enable', true );
						$waps_category_products       = get_term_meta( $term->term_id, '_as_quantity_range_category_products', true );

						if ( ! empty( $waps_category_products ) && ! empty( $waps_category_enable ) ) {
							if ( in_array( $item_id, $waps_category_products ) ) {
								$term_item_id = $term->term_id;
								break;
							}
						}

					}
				}
			}

			if( ! empty( $product_tag_terms ) && empty( $term_item_id ) ) {
                // Check Product term have any error.

				if ( ! is_wp_error( $product_tag_terms ) ) {

                    // Looping of product terms.
					foreach ( $product_tag_terms as $term ) {

						$waps_category_enable       = get_term_meta( $term->term_id, '_as_quantity_range_pricing_enable', true );
						$waps_category_products       = get_term_meta( $term->term_id, '_as_quantity_range_category_products', true );
						if ( ! empty( $waps_category_products ) && ! empty( $waps_category_enable ) ) {
							if ( in_array( $item_id, $waps_category_products ) ) {
								$term_item_id = $term->term_id;
								break;
							}
						}

					}
				}
			}
		}

		if ( ! empty( $sale_price ) ) {
			$final_price = $sale_price;
		} else {
			$final_price = $regular_price;
		}
		if( $taxonomy_use && ! empty( $term_item_id )) {
			$as_quantity_rage_values = get_term_meta($term_item_id, '_as_quantity_range_pricing_values', true);
			$as_woo_qrp_enable = get_term_meta($term_item_id, '_as_quantity_range_pricing_enable', true);        
		}else{
			$as_quantity_rage_values = get_post_meta($item_id, '_as_quantity_range_pricing_values', true);
			$as_woo_qrp_enable = get_post_meta($item_id, '_as_quantity_range_pricing_enable', true);
		}

		if ( 'on' === $as_woo_qrp_enable && ! empty( $final_price ) ) { 
			return array( 'price' => $final_price, 'range_prices'=> $as_quantity_rage_values );
		}
		return false;
	}

}
