<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin
 * @author     Akash Soni <soniakashc@gmail.com>
 */
class Woo_Products_Quantity_Range_Pricing_Admin {

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
	 * @param    string $plugin_name The name of this plugin.
	 * @param    string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$woo_qrp_enable = get_option( 'woo_qrp_enable', false );

		if ( $woo_qrp_enable ) {

			// Add quantity pricing fields for simple products.
			add_action( 'woocommerce_product_options_general_product_data', array(
				$this,
				'woo_simple_product_quantity_range_prices_fields',
			) );

			// Save quantity pricing fields values for simple products.
			add_action( 'woocommerce_process_product_meta', array(
				$this,
				'woo_quantity_range_prices_fields_values_save',
			) );

			// Add quantity pricing fields for Variable products.
			add_action( 'woocommerce_product_after_variable_attributes', array(
				$this,
				'woo_variable_product_quantity_range_prices_fields',
			), 10, 3 );

			// Save quantity pricing fields values for variation products..
			add_action( 'woocommerce_save_product_variation', array(
				$this,
				'woo_variable_quantity_range_prices_fields_values_save',
			), 10, 2 );

			// Ajax action for get new quantity range price fiels HTML.
			add_action( 'wp_ajax_woo_get_new_quantity_range_price_fields', array(
				$this,
				'woo_get_new_quantity_range_price_fields',
			) );

            // Add the fields to the "presenters" taxonomy, using our callback function
            add_action( 'product_cat_edit_form_fields', array(
                $this,
                'presenters_taxonomy_custom_fields'
            ), 99, 2 );
            add_action( 'product_tag_edit_form_fields', array(
                $this,
                'presenters_taxonomy_custom_fields'
            ), 99, 2 );

            //Save the changes made on the product taxonomy, using our callback function
            add_action( 'edited_product_cat', array(
                $this,
                'save_taxonomy_custom_fields'
            ), 99, 2 );
            add_action( 'edited_product_tag', array(
                $this,
                'save_taxonomy_custom_fields'
            ), 99, 2 );


		}
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-products-quantity-range-pricing-admin.css', array(), $this->version, 'all' );

        // Import select2 css on admin side.
        wp_enqueue_style( 'wc-enhanced-select' );
	}

	/**
	 * Register the JavaScript for the admin area.
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

		//Import colopicker js on admin side option page.
		wp_enqueue_style( 'wp-color-picker' );

        //Import select2 js on admin side taxonomy page.
        wp_enqueue_script( 'wc-enhanced-select' );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-products-quantity-range-pricing-admin.js', array(
			'jquery',
			'wp-color-picker',
		), $this->version, false );

		// Create as_woo_pricing object ajax url variable in admin side for ajax request.
		wp_localize_script( $this->plugin_name, 'as_woo_pricing',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);

	}


    /**
     * This function provide add shipping data facility in taxonomy product edit page.
     *
     * @since  1.0.0
     * @return string
     */
    public function presenters_taxonomy_custom_fields( $tag ) {

        $value_id                   = $tag->term_id;
        $wpp_wpqrp_category_products = get_term_meta( $value_id, '_as_quantity_range_category_products', true );
        $screen                     = get_current_screen();
        $taxonomy_slug              = $screen->taxonomy;
        $args                       = array(
            'post_type'      => 'product',
            'posts_per_page' => - 1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_tag',
                    'field'    => 'term_id',
                    'terms'    => array( $value_id )
                )
            )
        );
        if ( 'product_tag' === $taxonomy_slug ) {
            $args['tax_query'][0]['taxonomy'] = 'product_tag';
        } elseif ( 'product_cat' === $taxonomy_slug ) {
            $args['tax_query'][0]['taxonomy'] = 'product_cat';
        }
        // Get all product of this terms.
        $wpqrp_products     = get_posts( $args );
        $data_have         = false;
        $simple_show_class = '';

        if ( empty( $woo_qrp_enable ) ) {
            echo '<style>.wpqrp-box-' . esc_attr( $value_id ) . '{ display:none !important; }</style>';
        }

        echo '<table class="wpqrp-form-table"><tr><td><div class="options_group wpqrp-box">';
        ob_start();

        $this->woo_product_quantity_range_prices_fields( $value_id, 'term' );

        $wpqrp_shiiping_fields = ob_get_contents();
        ob_end_clean();
        echo $wpqrp_shiiping_fields;
        echo '<div class="wpqrp-select-category-product-box as-box-' . $value_id . '">';
        esc_attr_e( 'Select Product :', 'woo-advanced-product-shipping' );
        echo '<select class="wpqrp_chosen_select wpqrp-select-products" name="woo-quantity-range-shpping-product-' . $value_id . '[]" multiple >';
        foreach ( $wpqrp_products as $wpqrp_product ) {
            $selected = '';
            if ( in_array( $wpqrp_product->ID, $wpp_wpqrp_category_products ) ) {
                $selected = 'selected';
            }
            echo '<option value="' . $wpqrp_product->ID . '" ' . $selected . ' >' . $wpqrp_product->post_title . '</option>';
        }
        echo '</select>';
        echo '<div class="wpqrp-note">
                    <span class="wpqrp-red">';
        esc_attr_e( 'Note : Select which product apply this term quantity range price. Please do not add one product in multiple terms.', 'woo-advanced-product-shipping' );
        echo '</span>                
                  </div>';
        echo '</div>';
        echo '</div>';

        echo '</td></tr></table>';

    }
	/**
	 * Add quantity range price fields in product.
	 *
	 * Callback function for woocommerce_product_after_variable_attributes (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    int $value_id uniqe id of table.
	 * @param    string $product_type type of product simaple/variation.
	 */
	public function woo_product_quantity_range_prices_fields( $value_id, $product_type ) {

		if( 'term' === $product_type ) {
            // Get quantity range pricing values in product.
            $as_quantity_rage_values = get_term_meta( $value_id, '_as_quantity_range_pricing_values', true );
            // Get quantity range pricing enable / disable status.
            $woo_qrp_enable    = get_term_meta( $value_id, '_as_quantity_range_pricing_enable', true );
        } else {
            // Get quantity range pricing values in product.
            $as_quantity_rage_values = get_post_meta( $value_id, '_as_quantity_range_pricing_values', true );
            // Get quantity range pricing enable / disable status.
            $woo_qrp_enable    = get_post_meta( $value_id, '_as_quantity_range_pricing_enable', true );
        }

		$wqrp_style        = '';
		$simple_show_class = '';
        $quantity_value    = true;
		if ( empty( $woo_qrp_enable ) ) {
			echo '<style>.as-box-' . esc_attr( $value_id ) . '{ display:none !important; }</style>';
		}

		if ( 'simple' === $product_type ) {
			$simple_show_class = 'show_if_simple';
		}

		ob_start();
		if ( ! empty( $as_quantity_rage_values ) ) {
			$number                  = 0;
			$as_quantity_rage_values = $this->woo_quantity_value_sorting_by_order( $as_quantity_rage_values );
			foreach ( $as_quantity_rage_values as $as_quantity_rage_value ) {
				if ( ! empty( $as_quantity_rage_value['min_qty'] ) && ! empty( $as_quantity_rage_value['max_qty'] ) ) {
                    if( empty( $as_quantity_rage_value['role'] ) ) { $as_quantity_rage_value['role'] = ''; }
					$this->woo_quantity_range_prices_fields_html( $as_quantity_rage_value['min_qty'], $as_quantity_rage_value['max_qty'], $as_quantity_rage_value['type'], $as_quantity_rage_value['price'], $as_quantity_rage_value['role'], $number, $value_id );
					$number ++;
                    $quantity_value = false;
				}
			}

		}
		if( $quantity_value ){
			$this->woo_quantity_range_prices_fields_html( '', '', 'percentage', 0, '', 0, $value_id );
		}

		$quantity_wish_pricing_fields = ob_get_contents();
		ob_end_clean();

		include 'partials/admin-quantity-range-pricing-options.php';

	}

    public function save_taxonomy_custom_fields( $term_id ) {

        if ( isset( $_POST[ 'as_woo_pricing_' . $term_id ] ) ):

            // Save quantity range pricing values in product.
            update_term_meta( $term_id, '_as_quantity_range_pricing_values', $_POST[ 'as_woo_pricing_' . $term_id ] );

        endif;

        // Save quantity range pricing facility status in product.
        update_term_meta( $term_id, '_as_quantity_range_pricing_enable', $_POST[ 'woo-qrp-enable-' . $term_id ] );

        update_term_meta( $term_id, '_as_quantity_range_category_products', $_POST[ 'woo-quantity-range-shpping-product-' . $term_id ] );
    }
	/**
	 * Return quantity range price fields HTML in simple product.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param number $min_quantity Product minimum quantity.
	 * @param number $max_quantity Product maximum quantity.
	 * @param string $type Product price type.
	 * @param number $price Product quantity range price.
	 * @param number $number Product quantity range price filed number.
	 * @param int $value_id Product unique ID.
	 */
	public function woo_quantity_range_prices_fields_html( $min_quantity = 0, $max_quantity = 0, $type = 'percentage', $price = 0, $wpqrp_roles = '', $number = 0, $value_id ) {
        global $wp_roles;
		/**
		 * This function is provided HTML of quantity range price field.
		 */
		$type_values = array(
			'Percentage discount' => 'percentage',
			'Price discount'      => 'price',
			'Selling price'       => 'fixed',
		);
		// Include HTML of quantity range pricing fileds.
		include 'partials/admin-quantity-range-pricing-fields.php';
	}

	/**
	 * Add quantity range price fields in simple product.
	 *
	 * Callback function for woocommerce_product_options_general_product_data (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function woo_simple_product_quantity_range_prices_fields() {

		/**
		 * This function is provided quantity range price fields on admin product page.
		 *
		 * This price fields for simple product pricing.
		 */

		global $post;

		$this->woo_product_quantity_range_prices_fields( $post->ID, 'simple' );

	}

	/**
	 * Save quantity range price fields values of simple product.
	 *
	 * Callback function for woocommerce_process_product_meta (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    number $post_id Product ID.
	 */
	public function woo_quantity_range_prices_fields_values_save( $post_id ) {

		// Check quantity pricing values havent
		if ( isset( $_POST[ 'as_woo_pricing_' . $post_id ] ) ):

			// Save quantity range pricing values in product.
			update_post_meta( $post_id, '_as_quantity_range_pricing_values', $_POST[ 'as_woo_pricing_' . $post_id ] );

		endif;

		// Save quantity range pricing facility status in product.
		update_post_meta( $post_id, '_as_quantity_range_pricing_enable', $_POST[ 'woo-qrp-enable-' . $post_id ] );
	}

	/**
	 * Save quantity range price fields values of variation product.
	 *
	 * Callback function for woocommerce_save_product_variation (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    number $variation_id Variation ID.
	 */
	public function woo_variable_quantity_range_prices_fields_values_save( $variation_id ) {

		// Check quantity pricing values havent.
		if ( isset( $_POST[ 'as_woo_pricing_' . $variation_id ] ) ):

			// Save quantity range pricing values in product.
			update_post_meta( $variation_id, '_as_quantity_range_pricing_values', $_POST[ 'as_woo_pricing_' . $variation_id ] );

		endif;

		// Save quantity range pricing facility status in product.
		update_post_meta( $variation_id, '_as_quantity_range_pricing_enable', $_POST[ 'woo-qrp-enable-' . $variation_id ] );

	}

	/**
	 * Get new quantity range price fields HTML.
	 *
	 * Callback function for wp_ajax_nopriv_woo_get_new_quantity_range_price_fields (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function woo_get_new_quantity_range_price_fields() {

		$row_id   = $_POST['row_id'];
		$value_id = $_POST['value_id'];

		$this->woo_quantity_range_prices_fields_html( '', '', 'percentage', 0, '', $row_id, $value_id );

		die();

	}

	/**
	 * Add quantity range price fields in variable product.
	 *
	 * Callback function for woocommerce_product_after_variable_attributes (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    boolean $loop This is callback value true/false.
	 * @param    array $variation_data This array is variation post meta data.
	 * @param    array $variation This array is variation post data.
	 */
	public function woo_variable_product_quantity_range_prices_fields( $loop, $variation_data, $variation ) {

		/**
		 * This function is provided quantity range price fields on admin product page.
		 *
		 * This price fields for variable product pricing.
		 */

		$this->woo_product_quantity_range_prices_fields( $variation->ID, 'variation' );

	}

	/**
	 * This function are shorting the array of quantity range pricing values.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return array
	 *
	 * @param    array $as_quantity_rage_values This array is quantity range pricing values.
	 */
	public static function woo_quantity_value_sorting_by_order( $as_quantity_rage_values ) {

		$sortArray = array();
		
		if( $as_quantity_rage_values ){
			foreach ( $as_quantity_rage_values as $as_quantity_rage_value ) {
				foreach ( $as_quantity_rage_value as $key => $value ) {
					if ( ! isset( $sortArray[ $key ] ) ) {
						$sortArray[ $key ] = array();
					}
					$sortArray[ $key ][] = $value;
				}
			}

			$orderby = "min_qty";

			array_multisort( $sortArray[ $orderby ], SORT_ASC, $as_quantity_rage_values );
		}




		return $as_quantity_rage_values;

	}

}
