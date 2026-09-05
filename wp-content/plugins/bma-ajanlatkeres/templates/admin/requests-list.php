<?php
defined( 'ABSPATH' ) || exit;

$page        = absint( $_GET['paged'] ?? 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$result      = BMA_AJ_Quote_Requests::get_list(
	array(
		'search'     => sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ),
		'status_id'  => absint( $_GET['status'] ?? 0 ),
		'service_id' => absint( $_GET['service_id'] ?? 0 ),
		'date_from'  => sanitize_text_field( wp_unslash( $_GET['date_from'] ?? '' ) ),
		'date_to'    => sanitize_text_field( wp_unslash( $_GET['date_to'] ?? '' ) ),
		'page'       => $page,
		'per_page'   => 20,
	)
);
$requests  = $result['items'];
$total     = $result['total'];
$pages     = $result['pages'];
$statuses  = BMA_AJ_Statuses::get_all();
$services  = BMA_AJ_Service_CPT::get_active_services();
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Ajánlatkérések', 'bma-ajanlatkeres' ); ?></h1>

	<form method="get" class="bma-admin-filters">
		<input type="hidden" name="page" value="bma-requests">
		<p class="search-box" style="float:none;margin:1em 0;display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end">
			<label>
				<span class="screen-reader-text"><?php esc_html_e( 'Keresés', 'bma-ajanlatkeres' ); ?></span>
				<input type="search" name="s" value="<?php echo esc_attr( wp_unslash( $_GET['s'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'Keresés…', 'bma-ajanlatkeres' ); ?>">
			</label>
			<label>
				<span><?php esc_html_e( 'Státusz', 'bma-ajanlatkeres' ); ?></span><br>
				<select name="status">
					<option value=""><?php esc_html_e( 'Minden státusz', 'bma-ajanlatkeres' ); ?></option>
					<?php foreach ( $statuses as $st ) : ?>
						<option value="<?php echo esc_attr( (string) $st->id ); ?>" <?php selected( (string) ( $_GET['status'] ?? '' ), (string) $st->id ); ?>><?php echo esc_html( $st->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label>
				<span><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></span><br>
				<select name="service_id">
					<option value=""><?php esc_html_e( 'Minden szolgáltatás', 'bma-ajanlatkeres' ); ?></option>
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( (string) $svc->ID ); ?>" <?php selected( absint( $_GET['service_id'] ?? 0 ), (int) $svc->ID ); ?>><?php echo esc_html( $svc->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label>
				<span><?php esc_html_e( 'Dátumtól', 'bma-ajanlatkeres' ); ?></span><br>
				<input type="date" name="date_from" value="<?php echo esc_attr( wp_unslash( $_GET['date_from'] ?? '' ) ); ?>">
			</label>
			<label>
				<span><?php esc_html_e( 'Dátumig', 'bma-ajanlatkeres' ); ?></span><br>
				<input type="date" name="date_to" value="<?php echo esc_attr( wp_unslash( $_GET['date_to'] ?? '' ) ); ?>">
			</label>
			<button type="submit" class="button"><?php esc_html_e( 'Szűrés', 'bma-ajanlatkeres' ); ?></button>
		</p>
	</form>

	<p><?php echo esc_html( sprintf( /* translators: %d count */ __( 'Találatok: %d', 'bma-ajanlatkeres' ), $total ) ); ?></p>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Azonosító', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Ügyfél', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Dátum', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Státusz', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Műveletek', 'bma-ajanlatkeres' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $requests ) ) : ?>
				<tr><td colspan="8"><?php esc_html_e( 'Nincs találat.', 'bma-ajanlatkeres' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $requests as $req ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $req->request_number ); ?></strong></td>
						<td><?php echo esc_html( $req->client_name ); ?></td>
						<td><?php echo esc_html( $req->client_email ); ?></td>
						<td><?php echo esc_html( $req->services_label ); ?></td>
						<td><?php echo esc_html( mysql2date( 'Y.m.d H:i', $req->submitted_at ) ); ?></td>
						<td><span class="bma-status-badge" style="background:<?php echo esc_attr( $req->status_color ); ?>"><?php echo esc_html( $req->status_name ); ?></span></td>
						<td><?php echo esc_html( $req->quote_label ); ?></td>
						<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=bma-requests&action=view&id=' . $req->id ) ); ?>"><?php esc_html_e( 'Megtekintés', 'bma-ajanlatkeres' ); ?></a></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<?php if ( $pages > 1 ) : ?>
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'current'   => $page,
							'total'     => $pages,
							'prev_text' => '&laquo;',
							'next_text' => '&raquo;',
						)
					)
				);
				?>
			</div>
		</div>
	<?php endif; ?>
</div>
