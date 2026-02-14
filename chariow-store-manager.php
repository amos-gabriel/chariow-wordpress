<?php
/**
 * Plugin Name: Chariow Store Manager
 * Plugin URI: https://chariow.dev
 * Description: Complete WordPress wrapper for the Chariow.dev API - Manage your digital store, products, sales, customers, licenses, and discounts directly from WordPress.
 * Version: 1.0.0
 * Author: Amos Gabriel
 * Author URI: https://www.linkedin.com/in/amos-gabrieldev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chariow-store-manager
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'CHARIOW_STORE_MANAGER_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 */
define( 'CHARIOW_STORE_MANAGER_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'CHARIOW_STORE_MANAGER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Chariow API base URL.
 */
define( 'CHARIOW_API_BASE_URL', 'https://api.chariow.com/v1' );

/**
 * Autoloader for plugin classes.
 */
spl_autoload_register( function ( $class ) {
	// Project-specific namespace prefix
	$prefix = 'Chariow_Store_Manager_';

	// Base directory for the namespace prefix
	$base_dir = CHARIOW_STORE_MANAGER_PATH . 'includes/';

	// Does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// No, move to the next registered autoloader
		return;
	}

	// Get the relative class name
	$relative_class = substr( $class, $len );

	// Replace namespace separators with directory separators in the relative class name
	// and append with .php
	$file = $base_dir . 'class-' . str_replace( '_', '-', strtolower( $relative_class ) ) . '.php';

	// If the file exists, require it
	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/**
 * The code that runs during plugin activation.
 */
function activate_chariow_store_manager() {
	// Set default options
	add_option( 'chariow_api_key', '' );
	add_option( 'chariow_store_manager_version', CHARIOW_STORE_MANAGER_VERSION );
	
	// Flush rewrite rules
	flush_rewrite_rules();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_chariow_store_manager() {
	// Flush rewrite rules
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'activate_chariow_store_manager' );
register_deactivation_hook( __FILE__, 'deactivate_chariow_store_manager' );

/**
 * Initialize the plugin.
 */
function run_chariow_store_manager() {
	// Load admin functionality
	if ( is_admin() ) {
		require_once CHARIOW_STORE_MANAGER_PATH . 'admin/class-chariow-admin.php';
		$admin = new Chariow_Admin();
		$admin->init();
	}
}

add_action( 'plugins_loaded', 'run_chariow_store_manager' );

/**
 * Get the Chariow API client instance.
 * 
 * This is the main entry point for developers to access the Chariow API.
 * 
 * @return Chariow_Store_Manager_Api_Client|null The API client instance or null if not configured.
 */
function chariow_api() {
	static $api_client = null;
	
	if ( null === $api_client ) {
		require_once CHARIOW_STORE_MANAGER_PATH . 'includes/class-api-client.php';
		$api_client = new Chariow_Store_Manager_Api_Client();
	}
	
	return $api_client;
}

/**
 * Get the API key from WordPress options or environment variable.
 * 
 * @return string The API key or empty string if not set.
 */
function chariow_get_api_key() {
	// First, check WordPress options
	$api_key = get_option( 'chariow_api_key', '' );
	
	// If not set in options, check environment variable
	if ( empty( $api_key ) && defined( 'CHARIOW_API_KEY' ) ) {
		$api_key = CHARIOW_API_KEY;
	}
	
	// Also check $_ENV and getenv() as fallback
	if ( empty( $api_key ) ) {
		$api_key = getenv( 'CHARIOW_API_KEY' ) ?: ( $_ENV['CHARIOW_API_KEY'] ?? '' );
	}
	
	return $api_key;
}

/**
 * Shortcode to display a Chariow checkout button.
 * 
 * Usage: [chariow_checkout product_id="prd_abc123" label="Buy Now" class="my-custom-class"]
 * 
 * @param array $atts Shortcode attributes.
 * @return string HTML button.
 */
function chariow_checkout_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'product_id' => '',
		'label'      => __( 'Buy Now', 'chariow-store-manager' ),
		'class'      => 'chariow-button',
	), $atts, 'chariow_checkout' );

	if ( empty( $atts['product_id'] ) ) {
		return '';
	}

	$api = chariow_api();
	if ( ! $api || ! $api->is_configured() ) {
		return '<!-- Chariow API not configured -->';
	}

	// For now, we'll implement a direct link to Chariow checkout or a redirect.
	// Since we don't want to make an API call on every page load for performance,
	// we'll handle the checkout initiation via a redirect or a simple URL if possible.
	// Chariow typically uses a session URL.
	
	$checkout_url = add_query_arg( array(
		'chariow_checkout' => $atts['product_id']
	), home_url( '/' ) );

	return sprintf(
		'<a href="%s" class="%s" data-product-id="%s">%s</a>',
		esc_url( $checkout_url ),
		esc_attr( $atts['class'] ),
		esc_attr( $atts['product_id'] ),
		esc_html( $atts['label'] )
	);
}
add_shortcode( 'chariow_checkout', 'chariow_checkout_shortcode' );

/**
 * Handle checkout redirect.
 */
function chariow_handle_checkout_redirect() {
	if ( isset( $_GET['chariow_checkout'] ) ) {
		$product_id = sanitize_text_field( $_GET['chariow_checkout'] );
		$api = chariow_api();
		
		if ( $api && $api->is_configured() ) {
			$response = $api->checkout()->init( array(
				'product_id' => $product_id,
				'cancel_url' => home_url( '/' ),
				'success_url' => home_url( '/success' ),
			) );

			if ( Chariow_Store_Manager_Helper::is_success( $response ) ) {
				$data = Chariow_Store_Manager_Helper::get_response_data( $response );
				if ( isset( $data['url'] ) ) {
					wp_redirect( $data['url'] );
					exit;
				}
			}
		}
		
		// If fails, redirect home
		wp_redirect( home_url( '/' ) );
		exit;
	}
}
add_action( 'init', 'chariow_handle_checkout_redirect' );
