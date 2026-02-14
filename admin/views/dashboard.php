<?php
/**
 * Dashboard page template.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Check if API is configured
$is_configured = Chariow_Store_Manager_Helper::is_api_key_configured();
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( ! $is_configured ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php esc_html_e( 'Chariow API is not configured yet.', 'chariow-store-manager' ); ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=chariow-store-settings' ) ); ?>">
					<?php esc_html_e( 'Configure now →', 'chariow-store-manager' ); ?>
				</a>
			</p>
		</div>
	<?php else : ?>
		<?php
		// Get store information
		$api    = chariow_api();
		$result = $api->store()->get();
		
		if ( is_wp_error( $result ) ) {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php esc_html_e( 'API Connection Error:', 'chariow-store-manager' ); ?></strong>
					<?php echo esc_html( $result->get_error_message() ); ?>
				</p>
			</div>
			<?php
		} elseif ( Chariow_Store_Manager_Helper::is_success( $result ) ) {
			$store_data = Chariow_Store_Manager_Helper::get_response_data( $result );
			?>
			<div class="notice notice-success">
				<p><?php esc_html_e( '✓ Connected to Chariow API', 'chariow-store-manager' ); ?></p>
			</div>

			<div class="chariow-dashboard-grid">
				<div class="chariow-card">
					<h2><?php esc_html_e( 'Store Information', 'chariow-store-manager' ); ?></h2>
					<?php if ( isset( $store_data['name'] ) ) : ?>
						<p><strong><?php esc_html_e( 'Store Name:', 'chariow-store-manager' ); ?></strong> <?php echo esc_html( $store_data['name'] ); ?></p>
					<?php endif; ?>
					<?php if ( isset( $store_data['id'] ) ) : ?>
						<p><strong><?php esc_html_e( 'Store ID:', 'chariow-store-manager' ); ?></strong> <?php echo esc_html( $store_data['id'] ); ?></p>
					<?php endif; ?>
					<?php if ( isset( $store_data['url'] ) ) : ?>
						<p><strong><?php esc_html_e( 'Store URL:', 'chariow-store-manager' ); ?></strong> <a href="<?php echo esc_url( $store_data['url'] ); ?>" target="_blank"><?php echo esc_html( $store_data['url'] ); ?></a></p>
					<?php endif; ?>
				</div>

				<div class="chariow-card">
					<h2><?php esc_html_e( 'Quick Actions', 'chariow-store-manager' ); ?></h2>
					<ul class="chariow-quick-actions">
						<li><a href="https://app.chariow.com" target="_blank" class="button button-primary"><?php esc_html_e( 'Open Chariow Dashboard', 'chariow-store-manager' ); ?></a></li>
						<li><a href="https://chariow.dev/api-reference/introduction" target="_blank" class="button"><?php esc_html_e( 'API Documentation', 'chariow-store-manager' ); ?></a></li>
						<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=chariow-store-settings' ) ); ?>" class="button"><?php esc_html_e( 'Plugin Settings', 'chariow-store-manager' ); ?></a></li>
					</ul>
				</div>
			</div>

			<div class="chariow-card">
				<h2><?php esc_html_e( 'Developer Usage', 'chariow-store-manager' ); ?></h2>
				<p><?php esc_html_e( 'Use the Chariow API in your WordPress code:', 'chariow-store-manager' ); ?></p>
				<pre><code>// Get API client instance
$api = chariow_api();

// List products
$products = $api->products()->list();

// Get a specific product
$product = $api->products()->get( 'prd_abc123' );

// Create a checkout session
$checkout = $api->checkout()->init( array(
    'product_id' => 'prd_abc123',
    'email' => 'customer@example.com'
) );

// Validate a license
$license = $api->licenses()->validate( 'license_key_here' );</code></pre>
				<p>
					<a href="<?php echo esc_url( plugin_dir_url( dirname( __DIR__ ) ) . 'README.md' ); ?>" target="_blank">
						<?php esc_html_e( 'View full documentation →', 'chariow-store-manager' ); ?>
					</a>
				</p>
			</div>
		<?php } ?>
	<?php endif; ?>
</div>
