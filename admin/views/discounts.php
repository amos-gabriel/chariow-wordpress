<?php
/**
 * Discounts page template.
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
		<h1><?php esc_html_e( 'Discounts', 'chariow-store-manager' ); ?></h1>
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

// Get discounts from API
$api      = chariow_api();
$cursor   = isset( $_GET['cursor'] ) ? sanitize_text_field( $_GET['cursor'] ) : null;
$response = $api->discounts()->list( array( 'cursor' => $cursor ) );

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Discounts', 'chariow-store-manager' ); ?></h1>
	<a href="https://app.chariow.com/discounts/create" target="_blank" class="page-title-action"><?php esc_html_e( 'Add New in Chariow', 'chariow-store-manager' ); ?></a>
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
		$discounts   = $data['data'] ?? array();
		$pagination = $data['pagination'] ?? array();
		?>
		
		<table class="wp-list-table widefat fixed striped table-view-list discounts">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-code"><?php esc_html_e( 'Code', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-type"><?php esc_html_e( 'Amount', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-usage"><?php esc_html_e( 'Usage', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-status"><?php esc_html_e( 'Status', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-date"><?php esc_html_e( 'Expires', 'chariow-store-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $discounts ) ) : ?>
					<tr>
						<td colspan="5"><?php esc_html_e( 'No discounts found.', 'chariow-store-manager' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $discounts as $discount ) : ?>
						<tr>
							<td class="column-code">
								<strong><a href="https://app.chariow.com/discounts/<?php echo esc_attr( $discount['id'] ); ?>" target="_blank"><?php echo esc_html( $discount['code'] ); ?></a></strong>
							</td>
							<td class="column-type">
								<?php 
								if ( 'percentage' === $discount['type'] ) {
									echo esc_html( $discount['value'] . '%' );
								} else {
									echo esc_html( Chariow_Store_Manager_Helper::format_price( $discount ) ); // Assuming discount object has price fields
								}
								?>
							</td>
							<td class="column-usage">
								<?php echo esc_html( $discount['usage_count'] ?? 0 ); ?> / <?php echo esc_html( $discount['usage_limit'] ?? '∞' ); ?>
							</td>
							<td class="column-status">
								<span class="chariow-status chariow-status-<?php echo esc_attr( strtolower( $discount['status'] ?? 'unknown' ) ); ?>">
									<?php echo esc_html( ucfirst( $discount['status'] ?? 'unknown' ) ); ?>
								</span>
							</td>
							<td class="column-date">
								<?php echo $discount['expires_at'] ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $discount['expires_at'] ) ) ) : esc_html__( 'Never', 'chariow-store-manager' ); ?>
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
