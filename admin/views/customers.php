<?php
/**
 * Customers page template.
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
		<h1><?php esc_html_e( 'Customers', 'chariow-store-manager' ); ?></h1>
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

// Get customers from API
$api      = chariow_api();
$cursor   = isset( $_GET['cursor'] ) ? sanitize_text_field( $_GET['cursor'] ) : null;
$response = $api->customers()->list( array( 'cursor' => $cursor ) );

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Customers', 'chariow-store-manager' ); ?></h1>
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
		$customers   = $data['data'] ?? array();
		$pagination = $data['pagination'] ?? array();
		?>
		
		<table class="wp-list-table widefat fixed striped table-view-list customers">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-name"><?php esc_html_e( 'Name', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-email"><?php esc_html_e( 'Email', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-orders"><?php esc_html_e( 'Sales', 'chariow-store-manager' ); ?></th>
					<th scope="col" class="manage-column column-date"><?php esc_html_e( 'Joined', 'chariow-store-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $customers ) ) : ?>
					<tr>
						<td colspan="4"><?php esc_html_e( 'No customers found.', 'chariow-store-manager' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $customers as $customer ) : ?>
						<tr>
							<td class="column-name">
								<strong><a href="https://app.chariow.com/customers/<?php echo esc_attr( $customer['id'] ); ?>" target="_blank"><?php echo esc_html( $customer['name'] ?? 'N/A' ); ?></a></strong>
							</td>
							<td class="column-email">
								<?php echo esc_html( $customer['email'] ); ?>
							</td>
							<td class="column-orders">
								<?php echo esc_html( $customer['sales_count'] ?? 0 ); ?>
							</td>
							<td class="column-date">
								<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $customer['created_at'] ) ) ); ?>
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
