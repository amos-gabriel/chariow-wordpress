<?php
/**
 * Chariow Discounts API - Wrapper for discounts endpoints.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Discounts API Class
 */
class Chariow_Store_Manager_Api_Discounts {

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
	 * List all discounts.
	 *
	 * @param array $args {
	 *     Optional. Query parameters.
	 *
	 *     @type string $cursor   Pagination cursor.
	 *     @type int    $per_page Number of items per page (default: 20).
	 * }
	 * @return array|WP_Error Discounts list or WP_Error on failure.
	 */
	public function list( $args = array() ) {
		$defaults = array(
			'per_page' => 20,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		return $this->client->get( '/discounts', $args );
	}

	/**
	 * Get a single discount by ID.
	 *
	 * @param string $discount_id Discount ID.
	 * @return array|WP_Error Discount data or WP_Error on failure.
	 */
	public function get( $discount_id ) {
		if ( empty( $discount_id ) ) {
			return new WP_Error( 'invalid_discount_id', __( 'Discount ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->get( "/discounts/{$discount_id}" );
	}

	/**
	 * Create a new discount.
	 *
	 * @param array $data Discount data.
	 * @return array|WP_Error Created discount data or WP_Error on failure.
	 */
	public function create( $data ) {
		if ( empty( $data ) ) {
			return new WP_Error( 'invalid_data', __( 'Discount data is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->post( '/discounts', $data );
	}

	/**
	 * Update an existing discount.
	 *
	 * @param string $discount_id Discount ID.
	 * @param array  $data        Discount data to update.
	 * @return array|WP_Error Updated discount data or WP_Error on failure.
	 */
	public function update( $discount_id, $data ) {
		if ( empty( $discount_id ) ) {
			return new WP_Error( 'invalid_discount_id', __( 'Discount ID is required.', 'chariow-store-manager' ) );
		}
		
		if ( empty( $data ) ) {
			return new WP_Error( 'invalid_data', __( 'Discount data is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->put( "/discounts/{$discount_id}", $data );
	}

	/**
	 * Delete a discount.
	 *
	 * @param string $discount_id Discount ID.
	 * @return array|WP_Error Response or WP_Error on failure.
	 */
	public function delete( $discount_id ) {
		if ( empty( $discount_id ) ) {
			return new WP_Error( 'invalid_discount_id', __( 'Discount ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->delete( "/discounts/{$discount_id}" );
	}
}
