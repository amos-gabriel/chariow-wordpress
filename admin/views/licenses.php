<?php
/**
 * Licenses page template.
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
		<h1><?php esc_html_e( 'Licenses', 'chariow-store-manager' ); ?></h1>
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

// Get licenses from API
$api      = chariow_api();
$cursor   = isset( $_GET['cursor'] ) ? sanitize_text_field( $_GET['cursor'] ) : null;
$response = $api->licenses()->list( array( 'cursor' => $cursor ) );

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Licenses', 'chariow-store-manager' ); ?></h1>
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
		$licenses   = $data['data'] ?? array();
		$pagination = $data['pagination'] ?? array();
		?>
		
		<table class="wp-list-table widefat fixed striped table-view-list licenses">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-key"><?php esc_html_e( 'License Key', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-product"><?php esc_html_e( 'Product', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-activations"><?php esc_html_e( 'Activations', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-status"><?php esc_html_e( 'Status', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-date"><?php esc_html_e( 'Date', 'chariow-store-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $licenses ) ) : ?>
					<tr>
						<td colspan="5"><?php esc_html_e( 'No licenses found.', 'chariow-store-manager' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $licenses as $license ) : ?>
						<tr>
							<td class="column-key">
								<code><?php echo esc_html( $license['key'] ); ?></code>
							</td>
							<td class="column-product">
								<?php echo esc_html( $license['product']['name'] ?? 'N/A' ); ?>
							</td>
							<td class="column-activations">
								<?php echo esc_html( $license['activations_count'] ?? 0 ); ?> / <?php echo esc_html( $license['activations_limit'] ?? '∞' ); ?>
							</td>
							<td class="column-status">
								<span class="chariow-status chariow-status-<?php echo esc_attr( strtolower( $license['status'] ?? 'unknown' ) ); ?>">
									<?php echo esc_html( ucfirst( $license['status'] ?? 'unknown' ) ); ?>
								</span>
							</td>
							<td class="column-date">
								<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license['created_at'] ) ) ); ?>
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
		<?php
	}
	?>
</div>
