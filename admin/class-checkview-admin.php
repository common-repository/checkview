<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://checkview.io
 * @since      1.0.0
 *
 * @package    Checkview
 * @subpackage Checkview/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Checkview
 * @subpackage Checkview/admin
 * @author     Check View <support@checkview.io>
 */
class Checkview_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_action(
			'wp',
			array( $this, 'checkview_schedule_nonce_cleanup' )
		);

		add_action(
			'checkview_nonce_cleanup_cron',
			array( $this, 'checkview_delete_expired_nonces' )
		);
	}

	/**
	 * Deletes expired nonces.
	 *
	 * @return void
	 */
	public function checkview_delete_expired_nonces() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'used_nonces';

		// Define the expiration period (e.g., 24 hours).
		$expiration = gmdate( 'Y-m-d H:i:s', strtotime( '-24 hours' ) );

		// Delete nonces older than the expiration time.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $table_name WHERE used_at < %s",
				$expiration
			)
		);
	}

	/**
	 * Schedules nonce cleanup process.
	 *
	 * @return void
	 */
	public function checkview_schedule_nonce_cleanup() {
		if ( ! wp_next_scheduled( 'checkview_nonce_cleanup_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'checkview_nonce_cleanup_cron' );
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
		 * defined in Checkview_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Checkview_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$screen = get_current_screen();
		if ( 'checkview-options' !== $screen->base && 'settings_page_checkview-options' !== $screen->base ) {
			return;
		}
		wp_enqueue_style(
			$this->plugin_name,
			CHECKVIEW_ADMIN_ASSETS . 'css/checkview-admin.css',
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name . 'external',
			'https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css',
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name . '-swal',
			CHECKVIEW_ADMIN_ASSETS . 'css/checkview-swal2.css',
			array(),
			$this->version,
			'all'
		);
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
		 * defined in Checkview_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Checkview_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$screen = get_current_screen();
		if ( 'checkview-options' !== $screen->base && 'settings_page_checkview-options' !== $screen->base ) {
			return;
		}
		wp_enqueue_script(
			$this->plugin_name,
			CHECKVIEW_ADMIN_ASSETS . 'js/checkview-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);

		wp_enqueue_script(
			'checkview-sweetalert2.js',
			'https://cdn.jsdelivr.net/npm/sweetalert2@9',
			array( 'jquery' ),
			$this->version,
			true
		);
		if ( isset( $_GET['tab'] ) ) {
			$tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		} else {
			$tab = '';
		}

		$user_id = get_current_user_id();
		wp_localize_script(
			$this->plugin_name,
			'checkview_ajax_obj',
			array(
				'ajaxurl'                         => admin_url( 'admin-ajax.php' ),
				'user_id'                         => $user_id,
				'blog_id'                         => get_current_blog_id(),
				'tab'                             => $tab,
				'checkview_create_token_security' => wp_create_nonce( 'create-token-' . $user_id ),
			)
		);
	}

	/**
	 * Loads Form Test and helper classes.
	 *
	 * @return void
	 */
	public function checkview_init_current_test() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		// Current Vsitor IP.
		$visitor_ip = checkview_get_visitor_ip();
		// Check view Bot IP.
		$cv_bot_ip = checkview_get_api_ip();
		// $visitor_ip = $cv_bot_ip;
		// skip if visitor ip not equal to CV Bot IP.
		if ( is_array( $cv_bot_ip ) && ! in_array( $visitor_ip, $cv_bot_ip ) ) {
			return;
		}
		// if clean talk plugin active whitelist check form API IP.
		if ( is_plugin_active( 'cleantalk-spam-protect/cleantalk.php' ) ) {
			checkview_whitelist_api_ip();
		}

		$cv_test_id = isset( $_REQUEST['checkview_test_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['checkview_test_id'] ) ) : '';

		$referrer_url = sanitize_url( wp_get_raw_referer(), array( 'http', 'https' ) );

		// If not Ajax submission and found test_id.
		if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'admin-ajax.php' ) === false && '' !== $cv_test_id ) {
			// Create session for later use when form submit VIA AJAX.
			checkview_create_cv_session( $visitor_ip, $cv_test_id );
			update_option( $visitor_ip, 'checkview-saas', true );
		}

		// If submit VIA AJAX.
		if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'admin-ajax.php' ) !== false ) {
			$referer_url_query = wp_parse_url( $referrer_url, PHP_URL_QUERY );
			$qry_str           = array();
			if ( $referer_url_query ) {
				parse_str( $referer_url_query, $qry_str );
			}
			if ( isset( $qry_str['checkview_test_id'] ) ) {
				$cv_test_id = $qry_str['checkview_test_id'];
			}
		}
		if ( ! empty( $cv_test_id ) && ! checkview_is_valid_uuid( $cv_test_id ) ) {
			return;
		}
		if ( $cv_test_id && '' !== $cv_test_id ) {
			setcookie( 'checkview_test_id', $cv_test_id, time() + 6600, COOKIEPATH, COOKIE_DOMAIN );
		}

		if ( $cv_test_id && '' !== $cv_test_id ) {
			setcookie( 'checkview_test_id' . $cv_test_id, $cv_test_id, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
		}

		$cv_session = checkview_get_cv_session( $visitor_ip, $cv_test_id );
		$send_to    = CHECKVIEW_EMAIL;
		// stop if session not found.
		if ( ! empty( $cv_session ) ) {

			$test_key = $cv_session[0]['test_key'];

			$test_form = get_option( $test_key, '' );

			if ( ! empty( $test_form ) ) {
				$test_form = json_decode( $test_form, true );
			}

			if ( isset( $test_form['send_to'] ) && '' !== $test_form['send_to'] ) {
				$send_to = $test_form['send_to'];
			}

			if ( ! defined( 'CV_TEST_ID' ) ) {
				define( 'CV_TEST_ID', $cv_test_id );
			}
		}
		if ( ! defined( 'TEST_EMAIL' ) ) {
			define( 'TEST_EMAIL', $send_to );
		}
		delete_transient( 'checkview_forms_test_transient' );
		delete_transient( 'checkview_store_orders_transient' );
		if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
			require_once CHECKVIEW_INC_DIR . 'formhelpers/class-checkview-gforms-helper.php';
		}
		if ( is_plugin_active( 'fluentform/fluentform.php' ) ) {
			require_once CHECKVIEW_INC_DIR . 'formhelpers/class-checkview-fluent-forms-helper.php';
		}
		if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {
			require_once CHECKVIEW_INC_DIR . 'formhelpers/class-checkview-ninja-forms-helper.php';
		}
		if ( is_plugin_active( 'wpforms/wpforms.php' ) || is_plugin_active( 'wpforms-lite/wpforms.php' ) ) {
			require_once CHECKVIEW_INC_DIR . 'formhelpers/class-checkview-wpforms-helper.php';
		}
		if ( is_plugin_active( 'formidable/formidable.php' ) ) {
			require_once CHECKVIEW_INC_DIR . 'formhelpers/class-checkview-formidable-helper.php';
		}

		if ( is_plugin_active( 'ws-form/ws-form.php' ) || is_plugin_active( 'ws-form-pro/ws-form.php' ) ) {
			require_once CHECKVIEW_INC_DIR . 'formhelpers/class-checkview-wsf-helper.php';
		}
	}
}
