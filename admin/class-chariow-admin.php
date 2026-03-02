<?php
/**
 * Chariow Admin - WordPress admin interface controller.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Admin Class
 */
class Chariow_Admin {

	/**
	 * Initialize the admin functionality.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_ajax_chariow_test_connection', array( $this, 'ajax_test_connection' ) );
	}

	/**
	 * Add admin menu pages.
	 */
	public function add_admin_menu() {
		// Main menu page
		add_menu_page(
			__( 'Chariow Store Manager', 'chariow-store-manager' ),
			__( 'Chariow Store', 'chariow-store-manager' ),
			'manage_options',
			'chariow-store-manager',
			array( $this, 'render_dashboard_page' ),
			'dashicons-store',
			30
		);

		// Dashboard (same as main page)
		add_submenu_page(
			'chariow-store-manager',
			__( 'Dashboard', 'chariow-store-manager' ),
			__( 'Dashboard', 'chariow-store-manager' ),
			'manage_options',
			'chariow-store-manager',
			array( $this, 'render_dashboard_page' )
		);

		// Products page
		add_submenu_page(
			'chariow-store-manager',
			__( 'Products', 'chariow-store-manager' ),
			__( 'Products', 'chariow-store-manager' ),
			'manage_options',
			'chariow-store-products',
			array( $this, 'render_products_page' )
		);

		// Sales page
		add_submenu_page(
			'chariow-store-manager',
			__( 'Sales', 'chariow-store-manager' ),
			__( 'Sales', 'chariow-store-manager' ),
			'manage_options',
			'chariow-store-sales',
			array( $this, 'render_sales_page' )
		);

		// Customers page
		add_submenu_page(
			'chariow-store-manager',
			__( 'Customers', 'chariow-store-manager' ),
			__( 'Customers', 'chariow-store-manager' ),
			'manage_options',
			'chariow-store-customers',
			array( $this, 'render_customers_page' )
		);

		// Licenses page
		add_submenu_page(
			'chariow-store-manager',
			__( 'Licenses', 'chariow-store-manager' ),
			__( 'Licenses', 'chariow-store-manager' ),
			'manage_options',
			'chariow-store-licenses',
			array( $this, 'render_licenses_page' )
		);

		// Discounts page
		add_submenu_page(
			'chariow-store-manager',
			__( 'Discounts', 'chariow-store-manager' ),
			__( 'Discounts', 'chariow-store-manager' ),
			'manage_options',
			'chariow-store-discounts',
			array( $this, 'render_discounts_page' )
		);

		// Settings page
		add_submenu_page(
			'chariow-store-manager',
			__( 'Settings', 'chariow-store-manager' ),
			__( 'Settings', 'chariow-store-manager' ),
			'manage_options',
			'chariow-store-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'chariow_settings_group',
			'chariow_api_key',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		add_settings_section(
			'chariow_api_section',
			__( 'API Configuration', 'chariow-store-manager' ),
			array( $this, 'render_api_section' ),
			'chariow-store-settings'
		);

		add_settings_field(
			'chariow_api_key',
			__( 'API Key', 'chariow-store-manager' ),
			array( $this, 'render_api_key_field' ),
			'chariow-store-settings',
			'chariow_api_section'
		);
	}

	/**
	 * Render API section description.
	 */
	public function render_api_section() {
		echo '<p>' . esc_html__( 'Enter your Chariow API key to connect your store. You can generate API keys in your Chariow dashboard.', 'chariow-store-manager' ) . '</p>';
		echo '<p><a href="https://app.chariow.com/settings/api" target="_blank">' . esc_html__( 'Get your API key →', 'chariow-store-manager' ) . '</a></p>';
	}

	/**
	 * Render API key input field.
	 */
	public function render_api_key_field() {
		$api_key        = get_option( 'chariow_api_key', '' );
		$api_key_source = Chariow_Store_Manager_Helper::get_api_key_source();
		
		?>
		<input 
			type="text" 
			name="chariow_api_key" 
			id="chariow_api_key" 
			value="<?php echo esc_attr( $api_key ); ?>" 
			class="regular-text"
			<?php echo ( 'environment' === $api_key_source ) ? 'readonly' : ''; ?>
		/>
		<?php
		
		if ( 'environment' === $api_key_source ) {
			echo '<p class="description">' . esc_html__( 'API key is set via CHARIOW_API_KEY environment variable.', 'chariow-store-manager' ) . '</p>';
		} else {
			echo '<p class="description">' . esc_html__( 'Your Chariow API key (starts with sk_live_ or sk_test_).', 'chariow-store-manager' ) . '</p>';
		}
		
		// Test connection button
		if ( Chariow_Store_Manager_Helper::is_api_key_configured() ) {
			echo '<p><button type="button" id="chariow-test-connection" class="button button-secondary">' . esc_html__( 'Test Connection', 'chariow-store-manager' ) . '</button></p>';
			echo '<div id="chariow-connection-status"></div>';
		}
	}

	/**
	 * Render dashboard page.
	 */
	public function render_dashboard_page() {
		include CHARIOW_STORE_MANAGER_PATH . 'admin/views/dashboard.php';
	}

	/**
	 * Render settings page.
	 */
	public function render_settings_page() {
		include CHARIOW_STORE_MANAGER_PATH . 'admin/views/settings.php';
	}

	/**
	 * Render products page.
	 */
	public function render_products_page() {
		include CHARIOW_STORE_MANAGER_PATH . 'admin/views/products.php';
	}

	/**
	 * Render sales page.
	 */
	public function render_sales_page() {
		include CHARIOW_STORE_MANAGER_PATH . 'admin/views/sales.php';
	}

	/**
	 * Render customers page.
	 */
	public function render_customers_page() {
		include CHARIOW_STORE_MANAGER_PATH . 'admin/views/customers.php';
	}

	/**
	 * Render licenses page.
	 */
	public function render_licenses_page() {
		include CHARIOW_STORE_MANAGER_PATH . 'admin/views/licenses.php';
	}

	/**
	 * Render discounts page.
	 */
	public function render_discounts_page() {
		include CHARIOW_STORE_MANAGER_PATH . 'admin/views/discounts.php';
	}

	/**
	 * Enqueue admin styles and scripts.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_styles( $hook ) {
		// Only load on our plugin pages
		if ( strpos( $hook, 'chariow' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'chariow-admin',
			CHARIOW_STORE_MANAGER_URL . 'admin/css/admin.css',
			array(),
			CHARIOW_STORE_MANAGER_VERSION
		);

		wp_enqueue_script(
			'chariow-admin',
			CHARIOW_STORE_MANAGER_URL . 'admin/js/admin.js',
			array( 'jquery' ),
			CHARIOW_STORE_MANAGER_VERSION,
			true
		);

		wp_localize_script(
			'chariow-admin',
			'chariowAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'chariow_test_connection' ),
			)
		);
	}

	/**
	 * AJAX handler for testing API connection.
	 */
	public function ajax_test_connection() {
		// Verify nonce
		check_ajax_referer( 'chariow_test_connection', 'nonce' );

		// Check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to perform this action.', 'chariow-store-manager' ),
			) );
		}

		// Test connection
		$api    = chariow_api();
		$result = $api->test_connection();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => sprintf(
					__( 'Connection failed: %s', 'chariow-store-manager' ),
					$result->get_error_message()
				),
				'status'  => $result->get_error_data()['status'] ?? 0,
				'details' => $result->get_error_data(),
			) );
		} elseif ( ! empty( $result ) ) {
			$store_name = $result['data']['name'] ?? '';
			$message    = __( 'Connection successful! Your API key is working correctly.', 'chariow-store-manager' );
			
			if ( $store_name ) {
				$message = sprintf(
					__( 'Connection successful! Connected to: <strong>%s</strong>', 'chariow-store-manager' ),
					esc_html( $store_name )
				);
			}

			wp_send_json_success( array(
				'message' => $message,
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Connection failed. Please check your API key.', 'chariow-store-manager' ),
				'status'  => 401,
			) );
		}
	}
}
