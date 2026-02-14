<?php
/**
 * Chariow API Client - Base class for all API interactions.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow API Client Class
 */
class Chariow_Store_Manager_Api_Client {

	/**
	 * API base URL.
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * API key.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Rate limit information.
	 *
	 * @var array
	 */
	private $rate_limit = array(
		'limit'     => null,
		'remaining' => null,
		'reset'     => null,
	);

	/**
	 * API endpoint instances.
	 *
	 * @var array
	 */
	private $endpoints = array();

	/**
	 * Constructor.
	 *
	 * @param string $api_key Optional API key. If not provided, will use chariow_get_api_key().
	 */
	public function __construct( $api_key = '' ) {
		$this->base_url = CHARIOW_API_BASE_URL;
		$this->api_key  = ! empty( $api_key ) ? $api_key : chariow_get_api_key();
	}

	/**
	 * Get Store API instance.
	 *
	 * @return Chariow_Store_Manager_Api_Store
	 */
	public function store() {
		if ( ! isset( $this->endpoints['store'] ) ) {
			require_once CHARIOW_STORE_MANAGER_PATH . 'includes/api/class-chariow-store-api.php';
			$this->endpoints['store'] = new Chariow_Store_Manager_Api_Store( $this );
		}
		return $this->endpoints['store'];
	}

	/**
	 * Get Products API instance.
	 *
	 * @return Chariow_Store_Manager_Api_Products
	 */
	public function products() {
		if ( ! isset( $this->endpoints['products'] ) ) {
			require_once CHARIOW_STORE_MANAGER_PATH . 'includes/api/class-chariow-products-api.php';
			$this->endpoints['products'] = new Chariow_Store_Manager_Api_Products( $this );
		}
		return $this->endpoints['products'];
	}

	/**
	 * Get Checkout API instance.
	 *
	 * @return Chariow_Store_Manager_Api_Checkout
	 */
	public function checkout() {
		if ( ! isset( $this->endpoints['checkout'] ) ) {
			require_once CHARIOW_STORE_MANAGER_PATH . 'includes/api/class-chariow-checkout-api.php';
			$this->endpoints['checkout'] = new Chariow_Store_Manager_Api_Checkout( $this );
		}
		return $this->endpoints['checkout'];
	}

	/**
	 * Get Sales API instance.
	 *
	 * @return Chariow_Store_Manager_Api_Sales
	 */
	public function sales() {
		if ( ! isset( $this->endpoints['sales'] ) ) {
			require_once CHARIOW_STORE_MANAGER_PATH . 'includes/api/class-chariow-sales-api.php';
			$this->endpoints['sales'] = new Chariow_Store_Manager_Api_Sales( $this );
		}
		return $this->endpoints['sales'];
	}

	/**
	 * Get Customers API instance.
	 *
	 * @return Chariow_Store_Manager_Api_Customers
	 */
	public function customers() {
		if ( ! isset( $this->endpoints['customers'] ) ) {
			require_once CHARIOW_STORE_MANAGER_PATH . 'includes/api/class-chariow-customers-api.php';
			$this->endpoints['customers'] = new Chariow_Store_Manager_Api_Customers( $this );
		}
		return $this->endpoints['customers'];
	}

	/**
	 * Get Licenses API instance.
	 *
	 * @return Chariow_Store_Manager_Api_Licenses
	 */
	public function licenses() {
		if ( ! isset( $this->endpoints['licenses'] ) ) {
			require_once CHARIOW_STORE_MANAGER_PATH . 'includes/api/class-chariow-licenses-api.php';
			$this->endpoints['licenses'] = new Chariow_Store_Manager_Api_Licenses( $this );
		}
		return $this->endpoints['licenses'];
	}

	/**
	 * Get Discounts API instance.
	 *
	 * @return Chariow_Store_Manager_Api_Discounts
	 */
	public function discounts() {
		if ( ! isset( $this->endpoints['discounts'] ) ) {
			require_once CHARIOW_STORE_MANAGER_PATH . 'includes/api/class-chariow-discounts-api.php';
			$this->endpoints['discounts'] = new Chariow_Store_Manager_Api_Discounts( $this );
		}
		return $this->endpoints['discounts'];
	}

	/**
	 * Get Pulses API instance.
	 *
	 * @return Chariow_Store_Manager_Api_Pulses
	 */
	public function pulses() {
		if ( ! isset( $this->endpoints['pulses'] ) ) {
			require_once CHARIOW_STORE_MANAGER_PATH . 'includes/api/class-chariow-pulses-api.php';
			$this->endpoints['pulses'] = new Chariow_Store_Manager_Api_Pulses( $this );
		}
		return $this->endpoints['pulses'];
	}

	/**
	 * Make a GET request to the API.
	 *
	 * @param string $endpoint The API endpoint (without base URL).
	 * @param array  $args     Optional query parameters.
	 * @return array|WP_Error Response array or WP_Error on failure.
	 */
	public function get( $endpoint, $args = array() ) {
		return $this->request( 'GET', $endpoint, $args );
	}

	/**
	 * Make a POST request to the API.
	 *
	 * @param string $endpoint The API endpoint (without base URL).
	 * @param array  $data     Request body data.
	 * @return array|WP_Error Response array or WP_Error on failure.
	 */
	public function post( $endpoint, $data = array() ) {
		return $this->request( 'POST', $endpoint, array(), $data );
	}

	/**
	 * Make a PUT request to the API.
	 *
	 * @param string $endpoint The API endpoint (without base URL).
	 * @param array  $data     Request body data.
	 * @return array|WP_Error Response array or WP_Error on failure.
	 */
	public function put( $endpoint, $data = array() ) {
		return $this->request( 'PUT', $endpoint, array(), $data );
	}

	/**
	 * Make a DELETE request to the API.
	 *
	 * @param string $endpoint The API endpoint (without base URL).
	 * @return array|WP_Error Response array or WP_Error on failure.
	 */
	public function delete( $endpoint ) {
		return $this->request( 'DELETE', $endpoint );
	}

	/**
	 * Make an HTTP request to the Chariow API.
	 *
	 * @param string $method   HTTP method (GET, POST, PUT, DELETE).
	 * @param string $endpoint API endpoint.
	 * @param array  $query    Query parameters.
	 * @param array  $body     Request body.
	 * @return array|WP_Error Response array or WP_Error on failure.
	 */
	private function request( $method, $endpoint, $query = array(), $body = array() ) {
		// Check if API key is set
		if ( empty( $this->api_key ) ) {
			return new WP_Error(
				'no_api_key',
				__( 'Chariow API key is not configured. Please set it in the plugin settings or via CHARIOW_API_KEY environment variable.', 'chariow-store-manager' )
			);
		}

		// Build URL
		$url = $this->base_url . '/' . ltrim( $endpoint, '/' );
		
		// Add query parameters
		if ( ! empty( $query ) ) {
			$url = add_query_arg( $query, $url );
		}

		// Prepare headers
		$headers = array(
			'Authorization' => 'Bearer ' . $this->api_key,
			'Content-Type'  => 'application/json',
			'Accept'        => 'application/json',
		);

		// Prepare request arguments
		$args = array(
			'method'  => $method,
			'headers' => $headers,
			'timeout' => 30,
		);

		// Add body for POST/PUT requests
		if ( in_array( $method, array( 'POST', 'PUT' ), true ) && ! empty( $body ) ) {
			$args['body'] = wp_json_encode( $body );
		}

		// Log request in debug mode
		Chariow_Store_Manager_Helper::log( "API Request: {$method} {$url}" );

		// Make the request
		$response = wp_remote_request( $url, $args );

		// Check for errors
		if ( is_wp_error( $response ) ) {
			Chariow_Store_Manager_Helper::log( 'API Error', $response );
			return $response;
		}

		// Get response code and body
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Update rate limit information
		$this->update_rate_limit( $response );

		// Log response in debug mode
		Chariow_Store_Manager_Helper::log( "API Response: {$response_code}" );

		// Decode JSON response
		$decoded = json_decode( $response_body, true );

		// Handle non-200 responses
		if ( $response_code < 200 || $response_code >= 300 ) {
			$error_message = isset( $decoded['message'] ) ? $decoded['message'] : __( 'API request failed', 'chariow-store-manager' );
			
			return new WP_Error(
				'api_error',
				$error_message,
				array(
					'status'   => $response_code,
					'response' => $decoded,
				)
			);
		}

		return $decoded;
	}

	/**
	 * Update rate limit information from response headers.
	 *
	 * @param array $response WordPress HTTP API response.
	 */
	private function update_rate_limit( $response ) {
		$headers = wp_remote_retrieve_headers( $response );

		if ( isset( $headers['X-RateLimit-Limit'] ) ) {
			$this->rate_limit['limit'] = (int) $headers['X-RateLimit-Limit'];
		}

		if ( isset( $headers['X-RateLimit-Remaining'] ) ) {
			$this->rate_limit['remaining'] = (int) $headers['X-RateLimit-Remaining'];
		}

		if ( isset( $headers['X-RateLimit-Reset'] ) ) {
			$this->rate_limit['reset'] = (int) $headers['X-RateLimit-Reset'];
		}
	}

	/**
	 * Get current rate limit information.
	 *
	 * @return array Rate limit information.
	 */
	public function get_rate_limit() {
		return $this->rate_limit;
	}

	/**
	 * Check if API key is configured.
	 *
	 * @return bool True if API key is set, false otherwise.
	 */
	public function is_configured() {
		return ! empty( $this->api_key );
	}

	/**
	 * Test API connection.
	 *
	 * @return bool|WP_Error True if connection successful, WP_Error on failure.
	 */
	public function test_connection() {
		$response = $this->store()->get();
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		return Chariow_Store_Manager_Helper::is_success( $response );
	}
}
