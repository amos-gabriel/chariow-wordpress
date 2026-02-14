<?php
/**
 * Chariow Checkout API - Wrapper for checkout endpoints.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Checkout API Class
 */
class Chariow_Store_Manager_Api_Checkout {

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
	 * Initialize a checkout session.
	 *
	 * @param array $data Checkout data (product_id, customer info, etc.).
	 * @return array|WP_Error Checkout session data or WP_Error on failure.
	 */
	public function init( $data ) {
		if ( empty( $data ) ) {
			return new WP_Error( 'invalid_data', __( 'Checkout data is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->post( '/checkout', $data );
	}

	/**
	 * Get checkout session details.
	 *
	 * @param string $session_id Checkout session ID.
	 * @return array|WP_Error Checkout session data or WP_Error on failure.
	 */
	public function get( $session_id ) {
		if ( empty( $session_id ) ) {
			return new WP_Error( 'invalid_session_id', __( 'Checkout session ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->get( "/checkout/{$session_id}" );
	}
}
