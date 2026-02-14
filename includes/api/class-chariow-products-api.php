<?php
/**
 * Chariow Products API - Wrapper for products endpoints.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Products API Class
 */
class Chariow_Store_Manager_Api_Products {

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
	 * List all products.
	 *
	 * @param array $args {
	 *     Optional. Query parameters.
	 *
	 *     @type string $cursor   Pagination cursor.
	 *     @type int    $per_page Number of items per page (default: 20).
	 * }
	 * @return array|WP_Error Products list or WP_Error on failure.
	 */
	public function list( $args = array() ) {
		$defaults = array(
			'per_page' => 20,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		return $this->client->get( '/products', $args );
	}

	/**
	 * Get a single product by ID.
	 *
	 * @param string $product_id Product ID.
	 * @return array|WP_Error Product data or WP_Error on failure.
	 */
	public function get( $product_id ) {
		if ( empty( $product_id ) ) {
			return new WP_Error( 'invalid_product_id', __( 'Product ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->get( "/products/{$product_id}" );
	}

	/**
	 * Create a new product.
	 *
	 * @param array $data Product data.
	 * @return array|WP_Error Created product data or WP_Error on failure.
	 */
	public function create( $data ) {
		if ( empty( $data ) ) {
			return new WP_Error( 'invalid_data', __( 'Product data is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->post( '/products', $data );
	}

	/**
	 * Update an existing product.
	 *
	 * @param string $product_id Product ID.
	 * @param array  $data       Product data to update.
	 * @return array|WP_Error Updated product data or WP_Error on failure.
	 */
	public function update( $product_id, $data ) {
		if ( empty( $product_id ) ) {
			return new WP_Error( 'invalid_product_id', __( 'Product ID is required.', 'chariow-store-manager' ) );
		}
		
		if ( empty( $data ) ) {
			return new WP_Error( 'invalid_data', __( 'Product data is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->put( "/products/{$product_id}", $data );
	}

	/**
	 * Delete a product.
	 *
	 * @param string $product_id Product ID.
	 * @return array|WP_Error Response or WP_Error on failure.
	 */
	public function delete( $product_id ) {
		if ( empty( $product_id ) ) {
			return new WP_Error( 'invalid_product_id', __( 'Product ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->delete( "/products/{$product_id}" );
	}
}
