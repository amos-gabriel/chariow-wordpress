<?php
/**
 * Settings page template.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'chariow_settings_group' );
		do_settings_sections( 'chariow-store-settings' );
		
		// Only show submit button if API key is not from environment
		$api_key_source = Chariow_Store_Manager_Helper::get_api_key_source();
		if ( 'environment' !== $api_key_source ) {
			submit_button();
		}
		?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'Environment Variable Configuration', 'chariow-store-manager' ); ?></h2>
	<p><?php esc_html_e( 'Alternatively, you can set your API key via environment variable for better security:', 'chariow-store-manager' ); ?></p>
	<pre>define( 'CHARIOW_API_KEY', 'your_api_key_here' );</pre>
	<p><?php esc_html_e( 'Add this to your wp-config.php file or set it as a server environment variable.', 'chariow-store-manager' ); ?></p>

	<hr>

	<h2><?php esc_html_e( 'Documentation', 'chariow-store-manager' ); ?></h2>
	<ul>
		<li><a href="https://chariow.dev/en/introduction/overview" target="_blank"><?php esc_html_e( 'Chariow Documentation', 'chariow-store-manager' ); ?></a></li>
		<li><a href="https://chariow.dev/api-reference/introduction" target="_blank"><?php esc_html_e( 'API Reference', 'chariow-store-manager' ); ?></a></li>
		<li><a href="https://app.chariow.com/settings/api" target="_blank"><?php esc_html_e( 'Generate API Keys', 'chariow-store-manager' ); ?></a></li>
	</ul>
</div>

<script>
jQuery(document).ready(function($) {
	$('#chariow-test-connection').on('click', function() {
		var $button = $(this);
		var $status = $('#chariow-connection-status');
		
		$button.prop('disabled', true).text('<?php esc_html_e( 'Testing...', 'chariow-store-manager' ); ?>');
		$status.html('');
		
		$.ajax({
			url: chariowAdmin.ajaxUrl,
			type: 'GET',
			data: {
				action: 'chariow_test_connection',
				nonce: chariowAdmin.nonce
			},
			success: function(response) {
				if (response.success) {
					$status.html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>');
				} else {
					var msg = response.data.message;
					if (response.data.status) {
						msg += ' (HTTP ' + response.data.status + ')';
					}
					$status.html('<div class="notice notice-error inline"><p>' + msg + '</p></div>');
					console.error('Chariow Connection Error:', response.data);
				}
			},
			error: function() {
				$status.html('<div class="notice notice-error inline"><p><?php esc_html_e( 'Connection test failed.', 'chariow-store-manager' ); ?></p></div>');
			},
			complete: function() {
				$button.prop('disabled', false).text('<?php esc_html_e( 'Test Connection', 'chariow-store-manager' ); ?>');
			}
		});
	});
});
</script>
