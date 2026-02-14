<?php
/**
 * Products page template.
 *
 * @package Chariow_Store_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Check if API is configured
$is_configured = Chariow_Store_Manager_Helper::is_api_key_configured();

if ( ! $is_configured ) {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Products', 'chariow-store-manager' ); ?></h1>
		<div class="notice notice-warning">
			<p>
				<?php esc_html_e( 'Chariow API is not configured yet.', 'chariow-store-manager' ); ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=chariow-store-settings' ) ); ?>">
					<?php esc_html_e( 'Configure now →', 'chariow-store-manager' ); ?>
				</a>
			</p>
		</div>
	</div>
	<?php
	return;
}

// Get products from API
$api      = chariow_api();
$cursor   = isset( $_GET['cursor'] ) ? sanitize_text_field( $_GET['cursor'] ) : null;
$response = $api->products()->list( array( 'cursor' => $cursor ) );

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Products', 'chariow-store-manager' ); ?></h1>
	<a href="https://app.chariow.com/products/create" target="_blank" class="page-title-action"><?php esc_html_e( 'Add New in Chariow', 'chariow-store-manager' ); ?></a>
	<hr class="wp-header-end">

	<?php
	if ( is_wp_error( $response ) ) {
		?>
		<div class="notice notice-error">
			<p><?php echo esc_html( Chariow_Store_Manager_Helper::format_error( $response ) ); ?></p>
		</div>
		<?php
	} elseif ( Chariow_Store_Manager_Helper::is_success( $response ) ) {
		$data       = Chariow_Store_Manager_Helper::get_response_data( $response );
		$products   = $data['data'] ?? array();
		$pagination = $data['pagination'] ?? array();
		?>
		
		<table class="wp-list-table widefat fixed striped table-view-list products">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-name column-primary"><?php esc_html_e( 'Name', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-price"><?php esc_html_e( 'Price', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-status"><?php esc_html_e( 'Type', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-date"><?php esc_html_e( 'ID', 'chariow-store-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $products ) ) : ?>
					<tr>
						<td colspan="4"><?php esc_html_e( 'No products found.', 'chariow-store-manager' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $products as $product ) : ?>
						<tr>
							<td class="column-name column-primary">
								<strong><a href="https://app.chariow.com/products/<?php echo esc_attr( $product['id'] ); ?>" target="_blank"><?php echo esc_html( $product['name'] ); ?></a></strong>
								<div class="row-actions">
									<span class="edit"><a href="https://app.chariow.com/products/<?php echo esc_attr( $product['id'] ); ?>/edit" target="_blank"><?php esc_html_e( 'Edit in Chariow', 'chariow-store-manager' ); ?></a> | </span>
									<span class="view"><a href="<?php echo esc_url( $product['url'] ?? '#' ); ?>" target="_blank"><?php esc_html_e( 'View Page', 'chariow-store-manager' ); ?></a></span>
								</div>
							</td>
							<td class="column-price">
								<?php echo esc_html( Chariow_Store_Manager_Helper::format_price( $product['price'] ) ); ?>
							</td>
							<td class="column-type">
								<?php echo esc_html( ucfirst( $product['type'] ?? 'digital' ) ); ?>
							</td>
							<td class="column-id">
								<code><?php echo esc_html( $product['id'] ); ?></code>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<div class="tablenav bottom">
			<div class="tablenav-pages">
				<?php if ( ! empty( $pagination['prev_cursor'] ) ) : ?>
					<a class="button" href="<?php echo esc_url( add_query_arg( 'cursor', $pagination['prev_cursor'] ) ); ?>"><?php esc_html_e( '« Previous', 'chariow-store-manager' ); ?></a>
				<?php endif; ?>
				
				<?php if ( ! empty( $pagination['next_cursor'] ) ) : ?>
					<a class="button" href="<?php echo esc_url( add_query_arg( 'cursor', $pagination['next_cursor'] ) ); ?>"><?php esc_html_e( 'Next »', 'chariow-store-manager' ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<div class="chariow-card" style="margin-top: 20px;">
			<h3><?php esc_html_e( 'How to use these products on your site', 'chariow-store-manager' ); ?></h3>
			<p><?php esc_html_e( 'You can use the following shortcodes to display checkout buttons for your products:', 'chariow-store-manager' ); ?></p>
			<code>[chariow_checkout product_id="PRODUCT_ID" label="Buy Now"]</code>
		</div>
		<?php
	}
	?>
</div>
