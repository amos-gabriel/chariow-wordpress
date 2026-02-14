<?php
/**
 * Chariow Sales API - Wrapper for sales endpoints.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Sales API Class
 */
class Chariow_Store_Manager_Api_Sales {

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
	 * List all sales.
	 *
	 * @param array $args {
	 *     Optional. Query parameters.
	 *
	 *     @type string $cursor   Pagination cursor.
	 *     @type int    $per_page Number of items per page (default: 20).
	 * }
	 * @return array|WP_Error Sales list or WP_Error on failure.
	 */
	public function list( $args = array() ) {
		$defaults = array(
			'per_page' => 20,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		return $this->client->get( '/sales', $args );
	}

	/**
	 * Get a single sale by ID.
	 *
	 * @param string $sale_id Sale ID.
	 * @return array|WP_Error Sale data or WP_Error on failure.
	 */
	public function get( $sale_id ) {
		if ( empty( $sale_id ) ) {
			return new WP_Error( 'invalid_sale_id', __( 'Sale ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->get( "/sales/{$sale_id}" );
	}

	/**
	 * Update a sale.
	 *
	 * @param string $sale_id Sale ID.
	 * @param array  $data    Sale data to update.
	 * @return array|WP_Error Updated sale data or WP_Error on failure.
	 */
	public function update( $sale_id, $data ) {
		if ( empty( $sale_id ) ) {
			return new WP_Error( 'invalid_sale_id', __( 'Sale ID is required.', 'chariow-store-manager' ) );
		}
		
		if ( empty( $data ) ) {
			return new WP_Error( 'invalid_data', __( 'Sale data is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->put( "/sales/{$sale_id}", $data );
	}
}
