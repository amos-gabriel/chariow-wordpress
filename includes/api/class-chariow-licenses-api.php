<?php
/**
 * Chariow Licenses API - Wrapper for licenses endpoints.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Licenses API Class
 */
class Chariow_Store_Manager_Api_Licenses {

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
	 * List all licenses.
	 *
	 * @param array $args {
	 *     Optional. Query parameters.
	 *
	 *     @type string $cursor   Pagination cursor.
	 *     @type int    $per_page Number of items per page (default: 20).
	 * }
	 * @return array|WP_Error Licenses list or WP_Error on failure.
	 */
	public function list( $args = array() ) {
		$defaults = array(
			'per_page' => 20,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		return $this->client->get( '/licenses', $args );
	}

	/**
	 * Get a single license by ID.
	 *
	 * @param string $license_id License ID.
	 * @return array|WP_Error License data or WP_Error on failure.
	 */
	public function get( $license_id ) {
		if ( empty( $license_id ) ) {
			return new WP_Error( 'invalid_license_id', __( 'License ID is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->get( "/licenses/{$license_id}" );
	}

	/**
	 * Activate a license.
	 *
	 * @param string $license_key License key to activate.
	 * @param array  $data        Activation data (e.g., instance_id, domain).
	 * @return array|WP_Error Activation response or WP_Error on failure.
	 */
	public function activate( $license_key, $data = array() ) {
		if ( empty( $license_key ) ) {
			return new WP_Error( 'invalid_license_key', __( 'License key is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->post( "/licenses/{$license_key}/activate", $data );
	}

	/**
	 * Deactivate a license.
	 *
	 * @param string $license_key License key to deactivate.
	 * @param array  $data        Deactivation data (e.g., instance_id).
	 * @return array|WP_Error Deactivation response or WP_Error on failure.
	 */
	public function deactivate( $license_key, $data = array() ) {
		if ( empty( $license_key ) ) {
			return new WP_Error( 'invalid_license_key', __( 'License key is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->post( "/licenses/{$license_key}/deactivate", $data );
	}

	/**
	 * Validate a license.
	 *
	 * @param string $license_key License key to validate.
	 * @return array|WP_Error Validation response or WP_Error on failure.
	 */
	public function validate( $license_key ) {
		if ( empty( $license_key ) ) {
			return new WP_Error( 'invalid_license_key', __( 'License key is required.', 'chariow-store-manager' ) );
		}
		
		return $this->client->get( "/licenses/{$license_key}/validate" );
	}
}
