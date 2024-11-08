<?php
/**
 * Hanldes CPT API functions.
 *
 * @link       https://checkview.io
 * @since      1.0.0
 *
 * @package    CheckView
 * @subpackage CheckView/includes/API
 */

/**
 * Fired for the plugin Forms API registeration and hadling CURD.
 *
 * This class defines all code necessary to run for handling CheckView Form API CURD operations.
 *
 * @since      1.0.0
 * @package    CheckView
 * @subpackage CheckView/includes/API
 * @author     CheckView <checkview> https://checkview.io/
 */
class CheckView_Api {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The woohelper of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      bool/class    $woo_helper    The woo helper of this plugin.
	 */
	private $woo_helper;
	/**
	 * Store errors to display if the JWT Token is wrong
	 *
	 * @var WP_Error
	 */
	public $jwt_error = null;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of the plugin.
	 * @param    string $version    The version of this plugin.
	 * @param    class  $woo_helper The woohelper class.
	 */
	public function __construct( $plugin_name, $version, $woo_helper = '' ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->woo_helper  = $woo_helper;
	}
	/**
	 * Registers the rest api routes for our forms and related data.
	 *
	 * Registers the rest api routes for our forms and related data.
	 *
	 * @since    1.0.0
	 */
	public function checkview_register_rest_route() {
		register_rest_route(
			'checkview/v1',
			'/forms/formslist',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'checkview_get_available_forms_list' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);
		register_rest_route(
			'checkview/v1',
			'/forms/registerformtest',
			array(
				'methods'             => array( 'PUT', 'POST', 'GET' ),
				'callback'            => array( $this, 'checkview_register_form_test' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
					'frm_id'           => array(
						'required' => true,
					),
					'pg_id'            => array(
						'required' => true,
					),
					'type'             => array(
						'required' => true,
					),
					'send_to'          => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/forms/formstestresults',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'checkview_get_available_forms_test_results' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'uid'              => array(
						'required' => true,
					),
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/forms/deleteformstest',
			array(
				'methods'             => array( 'DELETE', 'PUT', 'GET', 'POST' ),
				'callback'            => array( $this, 'checkview_delete_forms_test_results' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'uid'              => array(
						'required' => true,
					),
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/orders',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'checkview_get_available_orders' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token'                    => array(
						'required' => false,
					),
					'checkview_order_last_modified_since' => array(
						'required' => false,
					),
					'checkview_order_last_modified_until' => array(
						'required' => false,
					),
					'checkview_order_id_after'            => array(
						'required' => false,
					),
					'checkview_order_id_before'           => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/order',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'checkview_get_available_order_details' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token'   => array(
						'required' => false,
					),
					'checkview_order_id' => array(
						'required' => true,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/products',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'checkview_get_available_products' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token'       => array(
						'required' => false,
					),
					'checkview_keyword'      => array(
						'required' => false,
					),
					'checkview_product_type' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/shippingdetails',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'checkview_get_available_shipping_details' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/deleteorders',
			array(
				'methods'             => array( 'DELETE', 'PUT', 'GET', 'POST' ),
				'callback'            => array( $this, 'checkview_delete_orders' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
					'order_id'         => array(
						'required' => true,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/activegateways',
			array(
				'methods'             => array( 'PUT', 'GET' ),
				'callback'            => array( $this, 'checkview_get_active_payment_gateways' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/cartdetails',
			array(
				'methods'             => array( 'GET' ),
				'callback'            => array( $this, 'checkview_get_cart_details' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/createtestcustomer',
			array(
				'methods'             => array( 'POST' ),
				'callback'            => array( $this, 'checkview_create_test_customer' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/gettestcustomer',
			array(
				'methods'             => array( 'GET' ),
				'callback'            => array( $this, 'checkview_get_test_customer_credentials' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/getstorelocations',
			array(
				'methods'             => array( 'GET' ),
				'callback'            => array( $this, 'checkview_get_store_locations' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'checkview/v1',
			'/store/getstoretestproduct',
			array(
				'methods'             => array( 'GET' ),
				'callback'            => array( $this, 'checkview_get_store_test_product' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			)
		);
		// stats.
		register_rest_route(
			'checkview/v1',
			'/site-info',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'checkview_saas_get_site_info' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
				),
			),
		);

		register_rest_route(
			'checkview/v1',
			'/plugin-version',
			array(
				'callback'            => array( $this, 'checkview_saas_get_plugin_version' ),
				'permission_callback' => array( $this, 'checkview_get_items_permissions_check' ),
				'args'                => array(
					'_checkview_token' => array(
						'required' => false,
					),
					'_plugin_slug'     => array(
						'required' => true,
					),
				),
			)
		);
	} // end checkview_register_rest_route
	/**
	 * Retrieves the available orders.
	 *
	 * @param WP_REST_Request $request wp request object.
	 * @return WP_REST_Response/json
	 */
	public function checkview_get_available_orders( WP_REST_Request $request ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		global $wpdb;
		$orders                              = get_transient( 'checkview_store_orders_transient' );
		$checkview_order_last_modified_since = $request->get_param( 'checkview_order_last_modified_since' );
		$checkview_order_last_modified_since = isset( $checkview_order_last_modified_since ) ? sanitize_text_field( $checkview_order_last_modified_since ) : '';

		$checkview_order_last_modified_until = $request->get_param( 'checkview_order_last_modified_until' );
		$checkview_order_last_modified_until = isset( $checkview_order_last_modified_until ) ? sanitize_text_field( $checkview_order_last_modified_until ) : '';

		$checkview_order_id_after = $request->get_param( 'checkview_order_id_after' );
		$checkview_order_id_after = isset( $checkview_order_id_after ) ? sanitize_text_field( $checkview_order_id_after ) : '';

		$checkview_order_id_before = $request->get_param( 'checkview_order_id_before' );
		$checkview_order_id_before = isset( $checkview_order_id_before ) ? sanitize_text_field( $checkview_order_id_before ) : '';
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}

		if ( '' !== $orders && null !== $orders && false !== $orders && empty( $checkview_order_id_before ) && empty( $checkview_order_id_after ) && empty( $checkview_order_last_modified_until ) && empty( $checkview_order_last_modified_since ) ) {
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Successfully retrieved the orders.', 'checkview' ),
					'body_response' => $orders,
				)
			);
			wp_die();
		}
		$orders = array();
		if ( ! is_admin() ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$per_page = -1;

		$params = array();

		$args = array(
			'limit'          => -1,
			'payment_method' => 'checkview',
			'meta_query'     => array(
				array(
					'relation' => 'AND', // Use 'AND' for both conditions to apply.
					array(
						'key'     => 'payment_made_by', // Meta key for payment method.
						'value'   => 'checkview', // Replace with your actual payment gateway ID.
						'compare' => '=', // Use '=' for exact match.
					),
				),
			),
		);
		if ( empty( $orders ) && ! empty( $checkview_order_last_modified_until ) && ! empty( $checkview_order_last_modified_since ) ) {

			$args['date_before'] = $checkview_order_last_modified_until;
			$args['date_after']  = $checkview_order_last_modified_since;

		}

		if ( empty( $orders ) && ! empty( $checkview_order_id_before ) ) {
			$args['date_before'] = $checkview_order_id_before;
		}

		if ( empty( $orders ) && ! empty( $checkview_order_id_after ) ) {
			$args['date_after'] = $checkview_order_id_after;
		}

		if ( empty( $orders ) ) {
			$wc_orders = wc_get_orders( $args );
			$orders    = array();
			if ( $wc_orders ) {
				foreach ( $wc_orders as $order ) {
					$order_object                 = new WC_Order( $order->id );
					$order_details['order_id']    = $order->id;
					$order_details['customer_id'] = $order_object->get_customer_id();
					$orders[]                     = $order_details;

				}
			}
		}

		if ( $orders && ! empty( $orders ) && false !== $orders && '' !== $orders ) {
			set_transient( 'checkview_store_orders_transient', $orders, 12 * HOUR_IN_SECONDS );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Successfully retrieved the orders.', 'checkview' ),
					'body_response' => $orders,
				)
			);
		} else {
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'There was a technical error while processing your request.', 'checkview' ),
					'body_response' => $orders,
				)
			);
		}
		wp_die();
	}

	/**
	 * Retrieves the available order details by id.
	 *
	 * @param WP_REST_Request $request wp request object.
	 * @return WP_REST_Response/json
	 */
	public function checkview_get_available_order_details( WP_REST_Request $request ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		global $wpdb;
		$checkview_order_id = $request->get_param( 'checkview_order_id' );
		$checkview_order_id = isset( $checkview_order_id ) ? intval( $checkview_order_id ) : '';
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}

		$order_details = array();
		if ( ! is_admin() ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get the order object.
		$order = wc_get_order( $checkview_order_id );

		$order_details = array();

		if ( $order ) {
			// Get order key.
			$order_details['order_key'] = $order->get_order_key();

			// Get Order Totals.
			$order_details['formatted_order_total']   = $order->get_formatted_order_total();
			$order_details['cart_tax']                = $order->get_cart_tax();
			$order_details['currency']                = $order->get_currency();
			$order_details['discount_tax']            = $order->get_discount_tax();
			$order_details['discount_to_display']     = $order->get_discount_to_display();
			$order_details['discount_total']          = $order->get_discount_total();
			$order_details['total_fees']              = $order->get_total_fees();
			$order_details['shipping_tax']            = $order->get_shipping_tax();
			$order_details['shipping_total']          = $order->get_shipping_total();
			$order_details['subtotal']                = $order->get_subtotal();
			$order_details['subtotal_to_display']     = $order->get_subtotal_to_display();
			$order_details['tax_totals']              = $order->get_tax_totals();
			$order_details['taxes']                   = $order->get_taxes();
			$order_details['total']                   = $order->get_total();
			$order_details['total_discount']          = $order->get_total_discount();
			$order_details['total_tax']               = $order->get_total_tax();
			$order_details['total_refunded']          = $order->get_total_refunded();
			$order_details['total_tax_refunded']      = $order->get_total_tax_refunded();
			$order_details['total_shipping_refunded'] = $order->get_total_shipping_refunded();
			$order_details['item_count_refunded']     = $order->get_item_count_refunded();

			// Get and Loop Over Order Items.
			foreach ( $order->get_items() as $item_id => $item ) {
				$product_id      = $item->get_product_id();
				$variation_id    = $item->get_variation_id();
				$product         = $item->get_product(); // see link above to get $product info.
				$product_name    = $item->get_name();
				$quantity        = $item->get_quantity();
				$subtotal        = $item->get_subtotal();
				$total           = $item->get_total();
				$tax             = $item->get_subtotal_tax();
				$tax_class       = $item->get_tax_class();
				$tax_status      = $item->get_tax_status();
				$allmeta         = $item->get_meta_data();
				$somemeta        = $item->get_meta( '_whatever', true );
				$item_type       = $item->get_type(); // e.g. "line_item", "fee".
				$items_details[] = array(
					'product_id'   => $product_id,
					'variation_id' => $variation_id,
					'product_name' => $product_name,
					'quantity'     => $quantity,
					'subtotal'     => $subtotal,
					'total'        => $total,
					'tax'          => $tax,
					'tax_class'    => $tax_class,
					'tax_status'   => $tax_status,
					'allmeta'      => $allmeta,
					'somemeta'     => $somemeta,
					'item_type'    => $item_type,
				);
			}
			$order_details['items']              = $items_details;
			$order_details['downloadable_items'] = $order->get_downloadable_items();
			$order_details['coupon_codes']       = $order->get_coupon_codes();
			// Get Order Shipping.
			$order_details['shipping_method']     = $order->get_shipping_method();
			$order_details['shipping_methods']    = $order->get_shipping_methods();
			$order_details['shipping_to_display'] = $order->get_shipping_to_display();

			// Get Order Dates.
			$order_details['date_created']   = $order->get_date_created();
			$order_details['date_modified']  = $order->get_date_modified();
			$order_details['date_completed'] = $order->get_date_completed();
			$order_details['date_paid']      = $order->get_date_paid();

			// Get Order User, Billing & Shipping Addresses.
			$order_details['customer_id']                  = $order->get_customer_id();
			$order_details['user_id']                      = $order->get_user_id();
			$order_details['user']                         = $order->get_user();
			$order_details['customer_ip_address']          = $order->get_customer_ip_address();
			$order_details['customer_user_agent']          = $order->get_customer_user_agent();
			$order_details['created_via']                  = $order->get_created_via();
			$order_details['customer_note']                = $order->get_customer_note();
			$order_details['billing_first_name']           = $order->get_billing_first_name();
			$order_details['billing_last_name']            = $order->get_billing_last_name();
			$order_details['billing_company']              = $order->get_billing_company();
			$order_details['billing_address_1']            = $order->get_billing_address_1();
			$order_details['billing_address_2']            = $order->get_billing_address_2();
			$order_details['billing_city']                 = $order->get_billing_city();
			$order_details['billing_state']                = $order->get_billing_state();
			$order_details['billing_postcode']             = $order->get_billing_postcode();
			$order_details['billing_country']              = $order->get_billing_country();
			$order_details['billing_email']                = $order->get_billing_email();
			$order_details['billing_phone']                = $order->get_billing_phone();
			$order_details['shipping_first_name']          = $order->get_shipping_first_name();
			$order_details['shipping_last_name']           = $order->get_shipping_last_name();
			$order_details['shipping_company']             = $order->get_shipping_company();
			$order_details['shipping_address_1']           = $order->get_shipping_address_1();
			$order_details['shipping_address_2']           = $order->get_shipping_address_2();
			$order_details['shipping_city']                = $order->get_shipping_city();
			$order_details['shipping_state']               = $order->get_shipping_state();
			$order_details['shipping_postcode']            = $order->get_shipping_postcode();
			$order_details['shipping_country']             = $order->get_shipping_country();
			$order_details['address']                      = $order->get_address();
			$order_details['shipping_address_map_url']     = $order->get_shipping_address_map_url();
			$order_details['formatted_billing_full_name']  = $order->get_formatted_billing_full_name();
			$order_details['formatted_shipping_full_name'] = $order->get_formatted_shipping_full_name();
			$order_details['formatted_billing_address']    = $order->get_formatted_billing_address();
			$order_details['formatted_shipping_address']   = $order->get_formatted_shipping_address();

			// Get Order Payment Details.
			$order_details['payment_method']       = $order->get_payment_method();
			$order_details['payment_method_title'] = $order->get_payment_method_title();
			$order_details['transaction_id']       = $order->get_transaction_id();

			// Get Order URLs.
			$order_details['checkout_payment_url']        = $order->get_checkout_payment_url();
			$order_details['checkout_order_received_url'] = $order->get_checkout_order_received_url();
			$order_details['cancel_order_url']            = $order->get_cancel_order_url();
			$order_details['cancel_order_url_raw']        = $order->get_cancel_order_url_raw();
			$order_details['cancel_endpoint']             = $order->get_cancel_endpoint();
			$order_details['view_order_url']              = $order->get_view_order_url();
			$order_details['edit_order_url']              = $order->get_edit_order_url();

			// Get Order Status.
			$order_details['status'] = $order->get_status();

			// Get Thank You Page URL.
			$order_details['thank_you_page_url'] = $order->get_checkout_order_received_url();
		}

		if ( $order_details && ! empty( $order_details ) && false !== $order_details && '' !== $order_details ) {
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Successfully retrieved the order details.', 'checkview' ),
					'body_response' => $order_details,
				)
			);
		} else {
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'There was a technical issue while processing your request.', 'checkview' ),
					'body_response' => array(),
				)
			);
		}
		wp_die();
	}
	/**
	 * Retrieves the available WooCommerce Products.
	 *
	 * @param WP_REST_Request $request wp request object.
	 * @return WP_REST_Response/json
	 */
	public function checkview_get_available_products( WP_REST_Request $request ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		global $wpdb;
		$products               = get_transient( 'checkview_store_products_transient' );
		$checkview_keyword      = $request->get_param( 'checkview_keyword' );
		$checkview_product_type = $request->get_param( 'checkview_product_type' );
		$checkview_keyword      = isset( $checkview_keyword ) ? sanitize_text_field( $checkview_keyword ) : null;
		$checkview_product_type = isset( $checkview_product_type ) ? sanitize_text_field( $checkview_product_type ) : null;
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		if ( '' !== $products && null !== $products && false !== $products ) {
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Successfully retrieved the products.', 'checkview' ),
					'body_response' => $products,
				)
			);
			wp_die();
		}
		$products = array();
		if ( ! is_admin() ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => -1,
		);
		if ( ! empty( $checkview_keyword ) && null !== $checkview_keyword ) {

			$args['s'] = $checkview_keyword;

		}

		if ( ! empty( $checkview_product_type ) && null !== $checkview_product_type ) {

			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => $checkview_keyword,
				),
			);

		}
		$loop = new WP_Query( $args );

		$products = array();

		if ( ! empty( $loop->posts ) ) {

			foreach ( $loop->posts as $post ) {
				// Initialize an array to store variations.
				$variations = array();

				// Get product object.
				$product = wc_get_product( $post );
				// Check if the product is variable.
				if ( $product && $product->is_type( 'variable' ) ) {
					// Get variations.
					$product_variations = $product->get_available_variations();

					foreach ( $product_variations as $variation ) {
						// Collect variation data.
						$variations[] = array(
							'id'         => $variation['variation_id'],
							'attributes' => $variation['attributes'],
							// You can add more variation data here if needed.
						);
					}
				}
				$products[] = array(
					'id'         => $post->ID,
					'name'       => $post->post_title,
					'slug'       => $post->post_name,
					'url'        => get_permalink( $post->ID ),
					'thumb_url'  => get_the_post_thumbnail_url( $post->ID ),
					'variations' => $variations,
				);

			}
		}
		if ( $products && ! empty( $products ) && false !== $products && '' !== $products ) {
			$products['store_url']     = wc_get_page_permalink( 'shop' );
			$products['cart_url']      = wc_get_cart_url();
			$products['checkout_url']  = wc_get_checkout_url();
			$products['myaccount_url'] = wc_get_page_permalink( 'myaccount' );
			set_transient( 'checkview_store_products_transient', $products, 12 * HOUR_IN_SECONDS );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Successfully retrieved the products.', 'checkview' ),
					'body_response' => $products,
				)
			);
		} else {
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'There was a technical issue while processing your request.', 'checkview' ),
					'body_response' => $products,
				)
			);
		}
		wp_die();
	}

	/**
	 * Retrieves shipping details.
	 *
	 * @return WP_REST_Response
	 */
	public function checkview_get_available_shipping_details() {

		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		global $wpdb;
		$shipping_details = get_transient( 'checkview_store_shipping_transient' );
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		if ( '' !== $shipping_details && null !== $shipping_details && false !== $shipping_details ) {
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Successfully retrieved the shipping details.', 'checkview' ),
					'body_response' => $shipping_details,
				)
			);
			wp_die();
		}
		$country_class                   = new WC_Countries();
		$country_list                    = $country_class->get_shipping_countries();
		$default_zone                    = new WC_Shipping_Zone( 0 );
		$default_zone_formatted_location = $default_zone->get_formatted_location();
		$default_zone_shipping_methods   = $default_zone->get_shipping_methods();

		$shipping_details = array(
			'default_methods' => array(),
			'zones'           => array(),
		);

		if ( ! empty( $default_zone_shipping_methods ) ) {
			foreach ( $default_zone_shipping_methods as $method ) {
				if ( 'yes' === $method->enabled ) {
					$shipping_details['default_methods'][] = $method->id;
				}
			}
		}

		$shipping_zones = new WC_Shipping_Zones();
		$zones          = $shipping_zones->get_zones();

		if ( ! empty( $zones ) ) {
			foreach ( $zones as $zone ) {

				$obj = array(
					'countries'   => array(),
					'postalCodes' => array(),
					'states'      => array(),
					'methods'     => array(),
				);

				if ( ! empty( $zone['zone_locations'] ) ) {
					foreach ( $zone['zone_locations'] as $location ) {
						if ( 'country' === $location->type ) {
							$obj['countries'][] = $location->code;
						} elseif ( 'postcode' === $location->type ) {
							$obj['postalCodes'][] = $location->code;
						} elseif ( 'state' === $location->type ) {
							$p               = explode( ':', $location->code );
							$obj['states'][] = array(
								'country' => $p[0],
								'state'   => $p[1],
							);
						}
					}
				}

				if ( ! empty( $zone['shipping_methods'] ) ) {
					foreach ( $zone['shipping_methods'] as $method ) {
						if ( 'yes' === $method->enabled ) {
							$obj['methods'][] = $method->id;
						}
					}
				}

				if ( ! empty( $obj['methods'] ) ) {
					$shipping_details['zones'][] = $obj;
				}
			}
		}
		if ( $shipping_details && ( isset( $shipping_details['methods'] ) || isset( $shipping_details['zones'] ) ) ) {
			set_transient( 'checkview_store_shipping_transient', $shipping_details, 12 * HOUR_IN_SECONDS );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Successfully retrieved the shipping details.', 'checkview' ),
					'body_response' => $shipping_details,
				)
			);
		} else {
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'There was a technical error while processing your request.', 'checkview' ),
					'body_response' => $shipping_details,
				)
			);
		}
		wp_die();
	}
	/**
	 * Deletes all the avaiable test orders made by SaaS.
	 *
	 * @param WP_REST_Request $request wp request object.
	 * @return WP_REST_Response/WP_Error/json
	 */
	public function checkview_delete_orders( WP_REST_Request $request ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		global $wpdb;
		$order_id = $request->get_param( 'order_id' );
		$order_id = isset( $order_id ) ? intval( $order_id ) : null;
		if ( null === $order_id || empty( $order_id ) ) {
			$results = $this->woo_helper->delete_orders_from_backend();
		} else {
			try {
				$order_object = new WC_Order( $order_id );
				$customer_id  = $order_object->get_customer_id();

				// Delete order.
				if ( $order_object ) {
					$order_object->delete( true );
					delete_transient( 'checkview_store_orders_transient' );
				}

				$order_object = null;
				$current_user = get_user_by( 'id', $customer_id );
				// Delete customer if available.
				if ( $customer_id && isset( $current_user->roles ) && ! in_array( 'administrator', $current_user->roles ) ) {
					$customer = new WC_Customer( $customer_id );

					if ( ! function_exists( 'wp_delete_user' ) ) {
						require_once ABSPATH . 'wp-admin/includes/user.php';
					}

					$res      = $customer->delete( true );
					$customer = null;
				}
				$results = true;
			} catch ( \Exception $e ) {
				if ( ! class_exists( 'Checkview_Admin_Logs' ) ) {
					/**
					 * The class responsible for defining all actions that occur in the admin area.
					 */
					require_once CHECKVIEW_ADMIN_DIR . '/class-checkview-admin-logs.php';
				}
				Checkview_Admin_Logs::add( 'api-logs', 'API failed to delete customer.' );
			}
		}
		if ( $results ) {
			return new WP_REST_Response(
				array(
					'status'   => 200,
					'response' => esc_html__( 'Successfully removed the results.', 'checkview' ),
				)
			);
			wp_die();
		} else {
			Checkview_Admin_Logs::add( 'api-logs', 'Empty records.' );
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
			wp_die();
		}
	}

	/**
	 * List Cart details.
	 *
	 * @return WP_REST_Response/WP_Error/json
	 */
	public function checkview_get_cart_details() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		$url = home_url( 'wp-json/wc/store/v1/cart' ); // WooCommerce cart endpoint.
		$url = get_rest_url() . 'wc/store/v1/cart';
		// Retrieve the current cookies.
		$cookies = array();
		foreach ( $_COOKIE as $name => $value ) {
			$cookies[] = $name . '=' . $value;
		}
		// Add the cookies to the request headers.
		if ( ! empty( $cookies ) ) {
			$headers['Cookie'] = implode( '; ', $cookies );
		}
		// Make the remote GET request.
		$response = wp_safe_remote_get(
			$url,
			array(
				'headers' => $headers,
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $error_message );
			return new WP_Error(
				400,
				esc_html__( 'There was a technical issue while processing your request', 'checkview' ),
			);
			wp_die();
		} else {
			$body         = wp_remote_retrieve_body( $response );
			$cart_details = json_decode( $body, true );
			foreach ( $cart_details['items'] as &$item ) {
				$item['name'] = html_entity_decode( $item['name'], ENT_COMPAT, 'UTF-8' );
			}
			// Do something with $cart_details.
		}
		if ( $cart_details ) {
			return new WP_REST_Response(
				array(
					'status'   => 200,
					'response' => esc_html__( 'Successfully retrieved the results.', 'checkview' ),
					'body'     => $cart_details,
				)
			);
			wp_die();
		} else {
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
			wp_die();
		}
	}

	/**
	 * List active payment gateways.
	 *
	 * @return WP_REST_Response/WP_Error/json
	 */
	public function checkview_get_active_payment_gateways() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		$active_gateways = $this->woo_helper->get_active_payment_gateways();
		if ( $active_gateways ) {
			return new WP_REST_Response(
				array(
					'status'   => 200,
					'response' => esc_html__( 'Successfully retrieved the results.', 'checkview' ),
					'body'     => $active_gateways,
				)
			);
			wp_die();
		} else {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'No results.' );
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
			wp_die();
		}
	}

	/**
	 * Creates the test customer.
	 *
	 * @return WP_REST_Response/WP_Error/json
	 */
	public function checkview_create_test_customer() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		$customer = $this->woo_helper->checkview_create_test_customer();
		if ( $customer ) {
			return new WP_REST_Response(
				array(
					'status'   => 200,
					'response' => esc_html__( 'Successfully created the customer.', 'checkview' ),
					'body'     => 'Credentials will be provided on request.',
				)
			);
			wp_die();
		} else {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Failed to create the customer.' );
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
			wp_die();
		}
	}

	/**
	 * Retrieves the credentials for the test customer.
	 *
	 * @return WP_REST_Response/WP_Error/json
	 */
	public function checkview_get_test_customer_credentials() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		$customer = $this->woo_helper->checkview_get_test_credentials();
		if ( $customer ) {
			return new WP_REST_Response(
				array(
					'status'   => 200,
					'response' => esc_html__( 'Successfully retrieved the customer.', 'checkview' ),
					'body'     => $customer,
				)
			);
			wp_die();
		} else {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Failed to retrieve the customer.' );
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
			wp_die();
		}
	}

	/**
	 * Retrieves the store locations.
	 *
	 * @return WP_REST_Response/json
	 */
	public function checkview_get_store_locations() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		$selling_locations = array();

		// Get selling and shipping countries.
		$selling_locations  = WC()->countries->get_allowed_countries();
		$shipping_locations = WC()->countries->get_shipping_countries();

		// Initialize final array to store countries with states.
		$locations_with_states = array();

		// Add states to selling locations.
		$selling_locations_with_states = checkview_add_states_to_locations( $selling_locations );

		// Add states to shipping locations.
		$shipping_locations_with_states = checkview_add_states_to_locations( $shipping_locations );

		// If you want to merge both selling and shipping locations:.
		$store_locations['selling_locations']  = $selling_locations_with_states;
		$store_locations['shipping_locations'] = $shipping_locations_with_states;
		// Output or return your final array as needed.
		// For example, to print:.
		if ( ! empty( $selling_locations ) || ! empty( $shipping_locations ) ) {
			return new WP_REST_Response(
				array(
					'status'   => 200,
					'response' => esc_html__( 'Successfully retrieved the store locations.', 'checkview' ),
					'body'     => $store_locations,
				)
			);
			wp_die();
		} else {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Failed to retrieve the store locations.' );
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
			wp_die();
		}
	}

	/**
	 * Retrieves the store's test product details.
	 *
	 * @return WP_REST_Response/json
	 */
	public function checkview_get_store_test_product() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			Checkview_Admin_Logs::add( 'api-logs', 'WooCommerce not found.' );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Dependency not found.', 'checkview' ),
					'body_response' => false,
				)
			);
		}
		if ( isset( $this->jwt_error ) && null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		$product                              = $this->woo_helper->checkview_get_test_product();
		$product_details['checkview_product'] = $product ? get_permalink( $product->get_id() ) : false;
		if ( ! empty( $product_details ) && false !== $product_details['checkview_product'] ) {
			return new WP_REST_Response(
				array(
					'status'   => 200,
					'response' => esc_html__( 'Successfully retrieved the test product.', 'checkview' ),
					'body'     => $product_details,
				)
			);
			wp_die();
		} else {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Failed to retrieve the test product.' );
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
			wp_die();
		}
	}

	/**
	 * Retrieves the available forms.
	 *
	 * @return WP_REST_Response/json
	 */
	public function checkview_get_available_forms_list() {
		global $wpdb;
		$forms_list = get_transient( 'checkview_forms_list_transient' );
		if ( null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		if ( '' !== $forms_list && null !== $forms_list && false !== $forms_list ) {
			// return new WP_REST_Response(
			// array(
			// 'status'        => 200,
			// 'response'      => esc_html__( 'Successfully retrieved the forms list.', 'checkview' ),
			// 'body_response' => $forms_list,
			// )
			// );
			// wp_die();
		}
		$forms = array();
		if ( ! is_admin() ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
			$tablename = $wpdb->prefix . 'gf_form';
			$results   = $wpdb->get_results( $wpdb->prepare( 'Select * from ' . $tablename . ' where is_active=%d and is_trash=%d order by ID ASC', 1, 0 ) );
			if ( $results ) {
				foreach ( $results as $row ) {
					$forms['GravityForms'][ $row->id ] = array(
						'ID'   => $row->id,
						'Name' => $row->title,
					);
					$tablename                         = $wpdb->prefix . 'gf_addon_feed';
					$addons                            = $wpdb->get_results( $wpdb->prepare( 'Select * from ' . $tablename . ' where is_active=%d and form_id=%d', 1, $row->id ) );
					foreach ( $addons as $addon ) {
						$forms['GravityForms'][ $row->id ]['addons'][] = $addon->addon_slug;
					}
					// WPDBPREPARE.
					$form_pages = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID FROM {$wpdb->prefix}posts 
						WHERE 1=1 
						AND (
							post_content LIKE %s 
							OR post_content LIKE %s 
							OR post_content LIKE %s  
							OR post_content LIKE %s
						) 
						AND post_status = 'publish' 
						AND post_type NOT IN ('kadence_wootemplate', 'revision')",
							'%wp:gravityforms/form {"formId":"' . $row->id . '"%',
							'%[gravityform id="' . $row->id . '"%',
							'%[gravityform id=' . $row->id . '%',
							'%[gravityform id=' . $row->id . '%'
						)
					);
					if ( $form_pages ) {
						foreach ( $form_pages as $form_page ) {

							if ( 'wp_block' === $form_page->post_type ) {

								$wp_block_pages = checkview_get_wp_block_pages( $form_page->ID );
								if ( $wp_block_pages ) {
									foreach ( $wp_block_pages as $wp_block_page ) {
										$forms['GravityForms'][ $row->id ]['pages'][] = array(
											'ID'  => $wp_block_page->ID,
											'url' => checkview_must_ssl_url( get_the_permalink( $wp_block_page->ID ) ),
										);
									}
								}
							} else {
								$forms['GravityForms'][ $row->id ]['pages'][] = array(
									'ID'  => $form_page->ID,
									'url' => checkview_must_ssl_url( get_the_permalink( $form_page->ID ) ),
								);
							}
						}
					}
				}
			}
		} // For Gravity Form

		if ( is_plugin_active( 'fluentform/fluentform.php' ) ) {
			$tablename = $wpdb->prefix . 'fluentform_forms';
			$results   = $wpdb->get_results( $wpdb->prepare( 'Select * from ' . $tablename . ' where status=%s order by ID ASC', 'published' ) );
			if ( $results ) {
				foreach ( $results as $row ) {
					$forms['FluentForms'][ $row->id ] = array(
						'ID'   => $row->id,
						'Name' => $row->title,
					);
					// WPDBPREPARE.
					$form_pages = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID FROM {$wpdb->prefix}posts 
						WHERE 1=1 
						AND (
							post_content LIKE %s 
							OR post_content LIKE %s 
							OR post_content LIKE %s 
							OR post_content LIKE %s
						) 
						AND post_status = 'publish' 
						AND post_type NOT IN ('kadence_wootemplate', 'revision')",
							'%wp:fluentfom/guten-block {"formId":"' . $row->id . '"%',
							'%[fluentform id="' . $row->id . '"%',
							'%[fluentform id=' . $row->id . '%',
							'%[fluentform id=' . $row->id . '%'
						)
					);
					foreach ( $form_pages as $form_page ) {

						if ( ! empty( $form_page->post_type ) && 'wp_block' === $form_page->post_type ) {

							$wp_block_pages = checkview_get_wp_block_pages( $form_page->ID );
							if ( $wp_block_pages ) {
								foreach ( $wp_block_pages as $wp_block_page ) {
									$forms['FluentForms'][ $row->id ]['pages'][] = array(
										'ID'  => $wp_block_page->ID,
										'url' => checkview_must_ssl_url( get_the_permalink( $wp_block_page->ID ) ),
									);
								}
							}
						} else {
							$forms['FluentForms'][ $row->id ]['pages'][] = array(
								'ID'  => $form_page->ID,
								'url' => checkview_must_ssl_url( get_the_permalink( $form_page->ID ) ),
							);
						}
					}
				}
			}
		} // FLUENT FORMS.
		if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {
			$tablename = $wpdb->prefix . 'nf3_forms';
			$results   = $wpdb->get_results( $wpdb->prepare( 'Select * from ' . $tablename . ' order by ID ASC' ) );
			if ( $results ) {
				foreach ( $results as $row ) {
					$forms['NinjaForms'][ $row->id ] = array(
						'ID'   => $row->id,
						'Name' => $row->title,
					);
					// WPDBPREPARE.
					$form_pages = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM {$wpdb->prefix}posts 
						WHERE 1=1 
						AND (
							post_content LIKE %s 
							OR post_content LIKE %s 
							OR post_content LIKE %s 
							OR post_content LIKE %s
						) 
						AND post_status = 'publish' 
						AND post_type NOT IN ('kadence_wootemplate', 'revision')",
							'%wp:ninja-forms/form {\"formID\":' . $row->id . '%',
							'%[ninja_form id="' . $row->id . '"]%',
							'%[ninja_form id=' . $row->id . ']%',
							'%[ninja_form id=\'' . $row->id . '\']%'
						)
					);
					if ( $form_pages ) {
						foreach ( $form_pages as $form_page ) {
							if ( 'wp_block' === $form_page->post_type ) {
								$wp_block_pages = checkview_get_wp_block_pages( $form_page->ID );
								if ( $wp_block_pages ) {
									foreach ( $wp_block_pages as $wp_block_page ) {
										$forms['NinjaForms'][ $row->id ]['pages'][] = array(
											'ID'  => $wp_block_page->ID,
											'url' => checkview_must_ssl_url( get_the_permalink( $wp_block_page->ID ) ),
										);
									}
								}
							} else {
								$forms['NinjaForms'][ $row->id ]['pages'][] = array(
									'ID'  => $form_page->ID,
									'url' => checkview_must_ssl_url( get_the_permalink( $form_page->ID ) ),
								);
							}
						}
					}
				}
			}
		} // NINJA FORMS

		if ( is_plugin_active( 'wpforms/wpforms.php' ) || is_plugin_active( 'wpforms-lite/wpforms.php' ) ) {
			$args    = array(
				'post_type'   => 'wpforms',
				'post_status' => 'publish',
				'order'       => 'ASC',
				'orderby'     => 'ID',
				'numberposts' => -1,
			);
			$results = get_posts( $args );
			if ( $results ) {
				foreach ( $results as $row ) {
					$forms['WpForms'][ $row->ID ] = array(
						'ID'   => $row->ID,
						'Name' => $row->post_title,
					);
					$form_location                = get_post_meta( $row->ID, 'wpforms_form_locations', true );
					if ( $form_location ) {
						foreach ( $form_location as $form_page ) {
							if ( ! empty( checkview_must_ssl_url( get_the_permalink( $form_page['id'] ) ) ) ) {
								$forms['WpForms'][ $row->ID ]['pages'][] = array(
									'ID'  => $form_page['id'],
									'url' => checkview_must_ssl_url( get_the_permalink( $form_page['id'] ) ),
								);
							}
						}
					}
				}
			}
		} // WP Forms.

		if ( is_plugin_active( 'formidable/formidable.php' ) ) {
			$tablename = $wpdb->prefix . 'frm_forms';
			$results   = $wpdb->get_results( $wpdb->prepare( 'Select * from ' . $tablename . ' where 1=%d and status=%s', 1, 'published' ) );
			if ( $results ) {
				foreach ( $results as $row ) {
					$forms['Formidable'][ $row->id ] = array(
						'ID'   => $row->id,
						'Name' => $row->name,
					);

					// WPDBPREPARE.
					$form_pages = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID FROM {$wpdb->prefix}posts 
						WHERE 1=1 
						AND (
							post_content LIKE %s 
							OR post_content LIKE %s
						) 
						AND post_status = 'publish' 
						AND post_type NOT IN ('kadence_wootemplate', 'revision')",
							'%[formidable id=\"' . $row->id . '\"%',
							'%[formidable id=' . $row->id . ']%'
						)
					);
					if ( $form_pages ) {
						foreach ( $form_pages as $form_page ) {

							if ( ! empty( $form_page->post_type ) && 'wp_block' === $form_page->post_type ) {

								$wp_block_pages = checkview_get_wp_block_pages( $form_page->ID );
								if ( $wp_block_pages ) {
									foreach ( $wp_block_pages as $wp_block_page ) {
										$forms['Formidable'][ $row->id ]['pages'][] = array(
											'ID'  => $wp_block_page->ID,
											'url' => checkview_must_ssl_url( get_the_permalink( $wp_block_page->ID ) ),
										);
									}
								}
							} else {
								$forms['Formidable'][ $row->id ]['pages'][] = array(
									'ID'  => $form_page->ID,
									'url' => checkview_must_ssl_url( get_the_permalink( $form_page->ID ) ),
								);
							}
						}
					}
				}
			}
		} // Formidable.

		// wpcf7_contact_form.
		if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			$args    = array(
				'post_type'   => 'wpcf7_contact_form',
				'post_status' => 'publish',
				'order'       => 'ASC',
				'orderby'     => 'ID',
				'numberposts' => -1,
			);
			$results = get_posts( $args );
			if ( $results ) {
				foreach ( $results as $row ) {
					$hash                     = substr( get_post_meta( $row->ID, '_hash', true ), 0, absint( 7 ) );
					$forms['CF7'][ $row->ID ] = array(
						'ID'   => $hash,
						'Name' => $row->post_title,
					);

					$form_pages = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID FROM {$wpdb->prefix}posts
						WHERE 1=1
						AND (
							(post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s)
							AND post_status = %s
							AND post_type NOT IN (%s, %s)
						)",
							'%wp:contact-form-7/contact-form-selector {"id":"' . $hash . '%',
							'%[contact-form-7 id="' . $hash . '%',
							'%[contact-form-7 id=' . $hash . '%',
							'%[contact-form-7 id=' . $hash . '%',
							'publish',
							'kadence_wootemplate',
							'revision'
						)
					);
					if ( $form_pages ) {
						foreach ( $form_pages as $form_page ) {
							if ( ! empty( $form_page->post_type ) && 'wp_block' === $form_page->post_type ) {

								$wp_block_pages = checkview_get_wp_block_pages( $form_page->ID );
								if ( $wp_block_pages ) {
									foreach ( $wp_block_pages as $wp_block_page ) {
										if ( ! empty( checkview_must_ssl_url( get_the_permalink( $wp_block_page->ID ) ) ) ) {
											$forms['CF7'][ $row->ID ]['pages'][] = array(
												'ID'  => $wp_block_page->ID,
												'url' => checkview_must_ssl_url( get_the_permalink( $wp_block_page->ID ) ),
											);
										}
									}
								}
							} elseif ( ! empty( checkview_must_ssl_url( get_the_permalink( $form_page->ID ) ) ) ) {
									$forms['CF7'][ $row->ID ]['pages'][] = array(
										'ID'  => $form_page->ID,
										'url' => checkview_must_ssl_url( get_the_permalink( $form_page->ID ) ),
									);
							}
						}
					}
				}
			}
		}

		if ( is_plugin_active( 'ws-form/ws-form.php' ) || is_plugin_active( 'ws-form-pro/ws-form.php' ) ) {
			$tablename = $wpdb->prefix . 'wsf_form';
			$results   = $wpdb->get_results( $wpdb->prepare( 'Select * from ' . $tablename . ' where status=%s order by id ASC', 'publish' ) );
			if ( $results ) {
				foreach ( $results as $row ) {
					$forms['WSForms'][ $row->id ] = array(
						'ID'   => $row->id,
						'Name' => $row->label,
					);
					// WPDBPREPARE.
					$form_pages = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID FROM {$wpdb->prefix}posts 
						WHERE 1=1 
						AND (
							post_content LIKE %s 
							OR post_content LIKE %s 
							OR post_content LIKE %s 
							OR post_content LIKE %s
						) 
						AND post_status = 'publish' 
						AND post_type NOT IN ('kadence_wootemplate', 'revision')",
							'%wp:wsf-block/form-add {"form_id":"' . $row->id . '"%',
							'%[ws_form id="' . $row->id . '"%',
							'%[ws_form id=' . $row->id . '%',
							'%[ws_form id=' . $row->id . '%'
						)
					);
					foreach ( $form_pages as $form_page ) {

						if ( ! empty( $form_page->post_type ) && 'wp_block' === $form_page->post_type ) {

							$wp_block_pages = checkview_get_wp_block_pages( $form_page->ID );
							if ( $wp_block_pages ) {
								foreach ( $wp_block_pages as $wp_block_page ) {
									$forms['WSForms'][ $row->id ]['pages'][] = array(
										'ID'  => $wp_block_page->ID,
										'url' => checkview_must_ssl_url( get_the_permalink( $wp_block_page->ID ) ),
									);
								}
							}
						} else {
							$forms['WSForms'][ $row->id ]['pages'][] = array(
								'ID'  => $form_page->ID,
								'url' => checkview_must_ssl_url( get_the_permalink( $form_page->ID ) ),
							);
						}
					}
				}
			}
		} // WSF FORMS.
		if ( $forms && ! empty( $forms ) && false !== $forms && '' !== $forms ) {
			set_transient( 'checkview_forms_list_transient', $forms, 12 * HOUR_IN_SECONDS );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => esc_html__( 'Successfully retrieved the forms list.', 'checkview' ),
					'body_response' => $forms,
				)
			);
		} else {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'No forms to show.' );
			return new WP_REST_Response(
				array(
					'status'   => 400,
					'response' => esc_html__( 'An error occurred while processing your request.', 'checkview' ),
				)
			);
		}
		wp_die();
	}

	/**
	 * Reterieves all the avaiable test results for forms.
	 *
	 * @param WP_REST_Request $request the request param with the API call.
	 * @return WP_REST_Response/WP_Error/json
	 */
	public function checkview_get_available_forms_test_results( WP_REST_Request $request ) {
		global $wpdb;
		$uid = $request->get_param( 'uid' );
		$uid = isset( $uid ) ? sanitize_text_field( $uid ) : null;

		$results = array();
		if ( '' === $uid || null === $uid ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Insuficient data.', 'checkview' ),
			);
			wp_die();
		} else {
			$tablename = $wpdb->prefix . 'cv_entry';
			$result    = $wpdb->get_row( $wpdb->prepare( 'Select * from ' . $tablename . ' where uid=%s', $uid ) );
			$tablename = $wpdb->prefix . 'cv_entry_meta';
			$rows      = $wpdb->get_results( $wpdb->prepare( 'Select * from ' . $tablename . ' where uid=%s order by id ASC', $uid ) );
			if ( $rows ) {
				foreach ( $rows as $row ) {
					if ( 'gravityforms' === strtolower( $result->form_type ) ) {
						$results[] = array(
							'field_id'    => 'input_' . $row->form_id . '_' . str_replace( '.', '_', $row->meta_key ),
							'field_value' => $row->meta_value,
						);

					} elseif ( 'cf7' === strtolower( $result->form_type ) ) {
						$value = $row->meta_value;
						if ( strpos( $value, 'htt' ) !== false ) {
							$value = html_entity_decode( $value );
						}
						$results[] = array(
							'field_id'    => '',
							'field_name'  => $row->meta_key,
							'field_value' => $value,
						);
					} else {

						$results[] = array(
							'field_id'    => $row->meta_key,
							'field_value' => maybe_unserialize( $row->meta_value ),
						);
					}
				}
				if ( ! empty( $results ) && false !== $results ) {
					return new WP_REST_Response(
						array(
							'status'        => 200,
							'response'      => esc_html__( 'Successfully retrieved the results.', 'checkview' ),
							'body_response' => $results,
						)
					);
				} else {
					// Log the detailed error for internal use.
					Checkview_Admin_Logs::add( 'api-logs', 'Failed to retrieve the results.' );
					return new WP_Error(
						400,
						esc_html__( 'An error occurred while processing your request.', 'checkview' ),
					);
				}
				wp_die();
			} else {
				// Log the detailed error for internal use.
				Checkview_Admin_Logs::add( 'api-logs', 'Failed to retrieve the results.' );
				return new WP_Error(
					400,
					esc_html__( 'An error occurred while processing your request.', 'checkview' ),
				);
				wp_die();
			}
		}
	}

	/**
	 * Registers form test to be validated.
	 *
	 * @param WP_REST_Request $request Object with the API call.
	 * @return WP_REST_Response/WP_Error
	 */
	public function checkview_register_form_test( WP_REST_Request $request ) {
		$frm_id  = $request->get_param( 'frm_id' );
		$frm_id  = isset( $frm_id ) ? intval( $frm_id ) : '';
		$pg_id   = $request->get_param( 'pg_id' );
		$pg_id   = isset( $pg_id ) ? intval( $pg_id ) : '';
		$type    = $request->get_param( 'type' );
		$type    = isset( $type ) ? sanitize_text_field( $type ) : '';
		$send_to = $request->get_param( 'send_to' );
		$send_to = isset( $send_to ) ? sanitize_text_field( $send_to ) : '';

		if ( ! empty( $frm_id ) && ! empty( $pg_id ) && ! empty( $type ) && ! empty( $send_to ) ) {
			$args['form_id'] = $frm_id;
			$args['page_id'] = $pg_id;
			$args['type']    = $type;
			$args['send_to'] = $send_to;
			$cf_test         = get_option( 'CF_TEST_' . $args['page_id'], '' );
			update_option( 'CF_TEST_' . $args['page_id'], wp_json_encode( $args ) );
			return new WP_REST_Response(
				array(
					'status'        => 200,
					'response'      => 'success',
					'body_response' => esc_html__( 'Check Form Test Successfully Added.', 'checkview' ),
				)
			);
			wp_die();
		} else {
			Checkview_Admin_Logs::add( 'api-logs', sanitize_text_field( 'Details to register form test are not correct.' ) );
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
			wp_die();
		}
	}

	/**
	 * Deletes all the avaiable test results for forms.
	 *
	 * @param WP_REST_Request $request the request param with the API call.
	 * @return WP_REST_Response/WP_Error/json
	 */
	public function checkview_delete_forms_test_results( WP_REST_Request $request ) {
		global $wpdb;
		$uid = $request->get_param( 'uid' );
		$uid = isset( $uid ) ? sanitize_text_field( $uid ) : null;

		$error   = array(
			'status'  => 'error',
			'code'    => 400,
			'message' => esc_html__( 'No Result Found', 'checkview' ),
		);
		$results = array();
		if ( '' === $uid || null === $uid || false === $uid ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Empty UID.' );
			return new WP_Error(
				400,
				esc_html__( 'Insuficient data.', 'checkview' ),
			);
			wp_die();
		} else {
			$tablename = $wpdb->prefix . 'cv_entry';
			$result    = $wpdb->delete( $tablename, array( 'uid' => $uid ) );
			$tablename = $wpdb->prefix . 'cv_entry_meta';
			$rows      = $wpdb->delete( $tablename, array( 'uid' => $uid ) );
			if ( $rows ) {
				return new WP_REST_Response(
					array(
						'status'        => 200,
						'response'      => esc_html__( 'Successfully removed the results.', 'checkview' ),
						'body_response' => $results,
					)
				);
				wp_die();
			} else {
				Checkview_Admin_Logs::add( 'api-logs', sanitize_text_field( 'Failed to remove the results.' ) );
				return new WP_Error(
					400,
					esc_html__( 'An error occurred while processing your request.', 'checkview' ),
				);
				wp_die();
			}
		}
	}

	/**
	 * Returns site info.
	 *
	 * @return WP_Rest_Response forms details.
	 */
	public function checkview_saas_get_site_info() {
		// Get all plugins.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();

		$plugin_list = array();
		foreach ( $plugins as $plugin_file => $plugin_data ) {
			$plugin_list[] = array(
				'name'        => $plugin_data['Name'],
				'version'     => $plugin_data['Version'],
				'plugin_file' => $plugin_file,
				'active'      => is_plugin_active( $plugin_file ),
			);
		}

		// Get active theme.
		// Get all themes.
		$themes     = wp_get_themes();
		$theme_list = array();
		foreach ( $themes as $stylesheet => $theme ) {
			$theme_list[] = array(
				'name'       => $theme->get( 'Name' ),
				'version'    => $theme->get( 'Version' ),
				'stylesheet' => $stylesheet,
				'active'     => $theme->get_stylesheet() === get_stylesheet(),
			);
		}

		// Get WordPress core version.
		global $wp_version;
		$core_info = array(
			'version' => $wp_version,
		);

		// Combine all data.
		$response = array(
			'plugins' => $plugin_list,
			'themes'  => $theme_list,
			'core'    => $core_info,
		);
		if ( $response ) {
				return new WP_REST_Response(
					array(
						'status'        => 200,
						'response'      => esc_html__( 'Successfully retrieved the site info.', 'checkview' ),
						'body_response' => $response,
					)
				);
		} else {
			Checkview_Admin_Logs::add( 'api-logs', sanitize_text_field( 'Failed to retrieve the site info.' ) );
			return new WP_Error(
				400,
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
			);
		}
	}

	/**
	 * Get the plugin version.
	 *
	 * @param WP_REST_Request $request WP_Request object.
	 * @return Json/WP Error
	 */
	public function checkview_saas_get_plugin_version( WP_REST_Request $request ) {
		if ( null !== $this->jwt_error ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', $this->jwt_error );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
			);
			wp_die();
		}
		// Get all plugins.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		// Get the plugin slug from the request parameters.
		$plugin_slug = $request->get_param( '_plugin_slug' );
		$plugin_slug = isset( $plugin_slug ) ? sanitize_text_field( $plugin_slug ) : '';
		if ( empty( $plugin_slug ) ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Missing plugin slug.' );
			return new WP_Error(
				'missing_param',
				esc_html__( 'Invalid request.', 'checkview' ),
				array( 'status' => 400 )
			);
		}

		// Format the slug to match the format used in the plugins directory.
		$plugin_slug = sanitize_text_field( $plugin_slug );
		$plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
		if ( in_array( $plugin_slug, array( 'recaptcha', 'turnstile', 'akismet' ), true ) ) {
			$plugin_folder = 'gravityforms' . $plugin_slug;
			$plugin_file   = $plugin_folder . '/' . $plugin_slug . '.php';
		} elseif ( 'gravityforms-zero-spam' === $plugin_slug ) {
			$plugin_folder = 'gravity-forms-zero-spam';
			$plugin_file   = $plugin_folder . '/' . $plugin_slug . '.php';
		} elseif ( 'hcaptcha-for-forms-and-more' === $plugin_slug ) {
			$plugin_folder = $plugin_slug;
			$plugin_slug   = 'hcaptcha';
			$plugin_file   = $plugin_folder . '/' . $plugin_slug . '.php';
		}
		if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file );
			$version     = $plugin_data['Version'];

			return new WP_REST_Response(
				array(
					'plugin_slug' => $plugin_slug,
					'version'     => $version,
				),
				200
			);
		} else {
			Checkview_Admin_Logs::add( 'api-logs', sanitize_text_field( 'Plugin not found.' ) );
			return new WP_Error(
				'error',
				esc_html__( 'An error occurred while processing your request.', 'checkview' ),
				array(
					'status' => 404,
				)
			);
		}
	}

	/**
	 * Validates Token.
	 *
	 * @param \WP_REST_Request $request request data with the api call.
	 * @return json/array
	 */
	public function checkview_get_items_permissions_check( \WP_REST_Request $request ) {
		// Wanted to Add JWT AUTH could not add because of limited time.
		// set no cache header.
		nocache_headers();
		// Get the Authorization header.
		$auth_header = $request->get_header( 'Authorization' );
		$auth_header = isset( $auth_header ) ? sanitize_text_field( $auth_header ) : '';
		// checking for JWT token.
		if ( empty( $auth_header ) ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Empty Auth header.' );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
				''
			);
			wp_die();
		}
		// Check if the request is made over HTTPS.
		if ( ! is_ssl() ) {
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Insecure request blocked.' );
			return new WP_Error(
				'insecure_request',
				esc_html__( 'Invalid request.', 'checkview' ),
				array( 'status' => 400 )
			);
		}
		$nonce_token = checkview_validate_jwt_token( $auth_header );
		// checking for JWT token.
		if ( ! isset( $nonce_token ) || empty( $nonce_token ) || is_wp_error( $nonce_token ) ) {
			$this->jwt_error = $nonce_token;
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Invalid token.' );
			return new WP_Error(
				400,
				esc_html__( 'Invalid request.', 'checkview' ),
				''
			);
			wp_die();
		}
		if ( ! checkview_is_valid_uuid( $nonce_token ) ) {
			// Nonce already used, return an error response.
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'Invalid nonce format.' );
			return new WP_Error(
				403,
				esc_html__( 'Invalid request.', 'checkview' ),
				''
			);
			wp_die();
		}
		global $wpdb;
		$cv_used_nonces = $wpdb->prefix . 'cv_used_nonces';
		// Check if the nonce exists.
		$nonce_exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $cv_used_nonces WHERE nonce = %s",
				$nonce_token
			)
		);

		if ( $nonce_exists ) {
			// Nonce already used, return an error response.
			// Log the detailed error for internal use.
			Checkview_Admin_Logs::add( 'api-logs', 'This nonce has already been used.' );
			return new WP_Error(
				403,
				esc_html__( 'Invalid request.', 'checkview' ),
				''
			);
			wp_die();
		} else {
			// Store the nonce in the database.
			$response = $wpdb->insert( $cv_used_nonces, array( 'nonce' => $nonce_token ) );
			if ( is_wp_error( $response ) ) {
				Checkview_Admin_Logs::add( 'api-logs', 'Not able to add nonce.' );
				return new WP_Error(
					'error',
					esc_html__(
						'An error occurred while processing your request.',
						'checkview'
					),
					array(
						'status' => 404,
					)
				);
			}
		}
		return array(
			'code' => 'jwt_auth_valid_token',
			'data' => array(
				'status' => 200,
			),
		);
	}
}
