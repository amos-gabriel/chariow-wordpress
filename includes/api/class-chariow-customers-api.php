<?php
/**
 * Chariow Customers API - Wrapper for customers endpoints.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Customers API Class
 */
class Chariow_Store_Manager_Api_Customers {

	/**
	 * API client instance.
	 *
	 * @var Chariow_Store_Manager_Api_Client
	 */
	private $client;

	/**
	 * Constructor.
	 *
	 * @param Chariow_Store_Manager_Api_Client $client API client instance.
	 */
	public function __construct( $client ) {
		$this->client = $client;
	}

	/**
	 * List all customers.
	 *
	 * @param array $args {
	 *     Optional. Query parameters.
	 *
	 *     @type string $cursor   Pagination cursor.
	 *     @type int    $per_page Number of items per page (default: 20).
	 * }
	 * @return array|WP_Error Customers list or WP_Error on failure.
	 */
	public function list( $args = array() ) {
		$defaults = array(
			'per_page' => 20,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		return $this->client->get( '/customers', $args );
	}

	/**
	 * Get a single customer by ID.
	 *
	 * @param string $customer_id Customer ID.
	 * @return array|WP_Error Customer data or WP_Error on failure.
	 */
	public function get( $customer_id ) {
		if ( empty( $customer_id ) ) {
			return new WP_Error( 'invalid_customer_id', __( 'Customer ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->get( "/customers/{$customer_id}" );
	}

	/**
	 * Create a new customer.
	 *
	 * @param array $data Customer data.
	 * @return array|WP_Error Created customer data or WP_Error on failure.
	 */
	public function create( $data ) {
		if ( empty( $data ) ) {
			return new WP_Error( 'invalid_data', __( 'Customer data is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->post( '/customers', $data );
	}

	/**
	 * Update an existing customer.
	 *
	 * @param string $customer_id Customer ID.
	 * @param array  $data        Customer data to update.
	 * @return array|WP_Error Updated customer data or WP_Error on failure.
	 */
	public function update( $customer_id, $data ) {
		if ( empty( $customer_id ) ) {
			return new WP_Error( 'invalid_customer_id', __( 'Customer ID is required.', 'chariow-store-manager' ) );
		}
		
		if ( empty( $data ) ) {
			return new WP_Error( 'invalid_data', __( 'Customer data is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->put( "/customers/{$customer_id}", $data );
	}
}
