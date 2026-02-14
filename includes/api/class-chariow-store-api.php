<?php
/**
 * Chariow Store API - Wrapper for store endpoints.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Store API Class
 */
class Chariow_Store_Manager_Api_Store {

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
	 * Get store information.
	 *
	 * @return array|WP_Error Store data or WP_Error on failure.
	 */
	public function get() {
		return $this->client->get( '/store' );
	}
}
