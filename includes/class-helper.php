<?php
/**
 * Helper functions for the Chariow Store Manager plugin.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Chariow Helper Class
 */
class Chariow_Store_Manager_Helper {

	/**
	 * Get the API key from WordPress options or environment variable.
	 *
	 * @return string The API key or empty string if not set.
	 */
	public static function get_api_key() {
		return chariow_get_api_key();
	}

	/**
	 * Check if the API key is configured.
	 *
	 * @return bool True if API key is set, false otherwise.
	 */
	public static function is_api_key_configured() {
		$api_key = self::get_api_key();
		return ! empty( $api_key );
	}

	/**
	 * Get the source of the API key (options or environment).
	 *
	 * @return string 'options', 'environment', or 'not_set'.
	 */
	public static function get_api_key_source() {
		$api_key_option = get_option( 'chariow_api_key', '' );
		
		if ( ! empty( $api_key_option ) ) {
			return 'options';
		}
		
		if ( defined( 'CHARIOW_API_KEY' ) || getenv( 'CHARIOW_API_KEY' ) || isset( $_ENV['CHARIOW_API_KEY'] ) ) {
			return 'environment';
		}
		
		return 'not_set';
	}

	/**
	 * Format API error response for display.
	 *
	 * @param array|WP_Error $response The API response or WP_Error.
	 * @return string Formatted error message.
	 */
	public static function format_error( $response ) {
		if ( is_wp_error( $response ) ) {
			return $response->get_error_message();
		}

		if ( isset( $response['message'] ) ) {
			$message = $response['message'];
			
			if ( isset( $response['errors'] ) && ! empty( $response['errors'] ) ) {
				$errors = $response['errors'];
				if ( is_array( $errors ) ) {
					$error_messages = array();
					foreach ( $errors as $field => $field_errors ) {
						if ( is_array( $field_errors ) ) {
							$error_messages[] = implode( ', ', $field_errors );
						} else {
							$error_messages[] = $field_errors;
						}
					}
					$message .= ': ' . implode( '; ', $error_messages );
				}
			}
			
			return $message;
		}

		return __( 'Unknown error occurred', 'chariow-store-manager' );
	}

	/**
	 * Log debug messages if WP_DEBUG is enabled.
	 *
	 * @param string $message The message to log.
	 * @param mixed  $data    Optional data to log.
	 */
	public static function log( $message, $data = null ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[Chariow Store Manager] ' . $message );
			if ( null !== $data ) {
				error_log( print_r( $data, true ) );
			}
		}
	}

	/**
	 * Sanitize API response data.
	 *
	 * @param mixed $data The data to sanitize.
	 * @return mixed Sanitized data.
	 */
	public static function sanitize_response( $data ) {
		if ( is_array( $data ) ) {
			return array_map( array( __CLASS__, 'sanitize_response' ), $data );
		}
		
		if ( is_string( $data ) ) {
			return sanitize_text_field( $data );
		}
		
		return $data;
	}

	/**
	 * Check if response is successful.
	 *
	 * @param array $response The API response.
	 * @return bool True if successful, false otherwise.
	 */
	public static function is_success( $response ) {
		if ( is_wp_error( $response ) ) {
			return false;
		}
		
		return isset( $response['message'] ) && 'success' === $response['message'];
	}

	/**
	 * Extract data from successful response.
	 *
	 * @param array $response The API response.
	 * @return mixed|null The data or null if not found.
	 */
	public static function get_response_data( $response ) {
		if ( self::is_success( $response ) && isset( $response['data'] ) ) {
			return $response['data'];
		}
		
		return null;
	}

	/**
	 * Format price for display.
	 *
	 * @param array $price Price array from API.
	 * @return string Formatted price.
	 */
	public static function format_price( $price ) {
		if ( isset( $price['formatted'] ) ) {
			return $price['formatted'];
		}
		
		if ( isset( $price['value'] ) && isset( $price['currency'] ) ) {
			return $price['currency'] . ' ' . number_format( $price['value'], 2 );
		}
		
		return '';
	}
}
