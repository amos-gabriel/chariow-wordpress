<?php
/**
 * Chariow Pulses API - Wrapper for pulses (webhooks) endpoints.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Pulses API Class
 */
class Chariow_Store_Manager_Api_Pulses {

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
	 * List all pulses.
	 *
	 * @param array $args {
	 *     Optional. Query parameters.
	 *
	 *     @type string $cursor   Pagination cursor.
	 *     @type int    $per_page Number of items per page (default: 20).
	 * }
	 * @return array|WP_Error Pulses list or WP_Error on failure.
	 */
	public function list( $args = array() ) {
		$defaults = array(
			'per_page' => 20,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		return $this->client->get( '/pulses', $args );
	}

	/**
	 * Get a single pulse by ID.
	 *
	 * @param string $pulse_id Pulse ID.
	 * @return array|WP_Error Pulse data or WP_Error on failure.
	 */
	public function get( $pulse_id ) {
		if ( empty( $pulse_id ) ) {
			return new WP_Error( 'invalid_pulse_id', __( 'Pulse ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->get( "/pulses/{$pulse_id}" );
	}
}
