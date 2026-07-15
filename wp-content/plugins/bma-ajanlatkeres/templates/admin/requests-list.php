<?php
defined( 'ABSPATH' ) || exit;
$requests = BMA_AJ_Quote_Requests::get_list( array(
	'search'   => sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ),
	'status_id'=> absint( $_GET['status'] ?? 0 ),
	'page'     => absint( $_GET['paged'] ?? 1 ),
) );
$statuses = BMA_AJ_Statuses::get_all();
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Ajánlatkérések', 'bma-ajanlatkeres' ); ?></h1>

	<form method="get">
		<input type="hidden" name="page" value="bma-requests">
		<p class="search-box">
			<input type="search" name="s" value="<?php echo esc_attr( $_GET['s'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Keresés…', 'bma-ajanlatkeres' ); ?>">
			<select name="status">
				<option value=""><?php esc_html_e( 'Minden státusz', 'bma-ajanlatkeres' ); ?></option>
				<?php foreach ( $statuses as $st ) : ?>
					<option value="<?php echo esc_attr( $st->id ); ?>" <?php selected( $_GET['status'] ?? '', $st->id ); ?>><?php echo esc_html( $st->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" class="button"><?php esc_html_e( 'Szűrés', 'bma-ajanlatkeres' ); ?></button>
		</p>
	</form>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Azonosító', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Ügyfél', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Dátum', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Státusz', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Műveletek', 'bma-ajanlatkeres' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $requests ) ) : ?>
				<tr><td colspan="6"><?php esc_html_e( 'Nincs találat.', 'bma-ajanlatkeres' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $requests as $req ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $req->request_number ); ?></strong></td>
						<td><?php echo esc_html( $req->client_name ); ?></td>
						<td><?php echo esc_html( $req->client_email ); ?></td>
						<td><?php echo esc_html( mysql2date( 'Y.m.d H:i', $req->submitted_at ) ); ?></td>
						<td><span class="bma-status-badge" style="background:<?php echo esc_attr( $req->status_color ); ?>"><?php echo esc_html( $req->status_name ); ?></span></td>
						<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=bma-requests&action=view&id=' . $req->id ) ); ?>"><?php esc_html_e( 'Megtekintés', 'bma-ajanlatkeres' ); ?></a></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
