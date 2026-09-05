<?php
defined( 'ABSPATH' ) || exit;
global $wpdb;

$message = '';
$error   = '';

if ( isset( $_POST['bma_status_create_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_status_create_nonce'] ) ), 'bma_status_create' ) ) {
	$result = BMA_AJ_Statuses::create(
		array(
			'name'                => sanitize_text_field( wp_unslash( $_POST['new_name'] ?? '' ) ),
			'color'               => sanitize_hex_color( wp_unslash( $_POST['new_color'] ?? '#2271b1' ) ),
			'sort_order'          => absint( $_POST['new_sort_order'] ?? 99 ),
			'client_visible'      => ! empty( $_POST['new_client_visible'] ),
			'send_email'          => ! empty( $_POST['new_send_email'] ),
			'email_template_slug' => sanitize_text_field( wp_unslash( $_POST['new_email_template_slug'] ?? '' ) ),
		)
	);
	if ( is_wp_error( $result ) ) {
		$error = $result->get_error_message();
	} else {
		$message = __( 'Új státusz létrehozva.', 'bma-ajanlatkeres' );
	}
}

if ( isset( $_POST['bma_status_delete_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_status_delete_nonce'] ) ), 'bma_status_delete' ) ) {
	$delete_id = absint( $_POST['delete_status_id'] ?? 0 );
	$result    = BMA_AJ_Statuses::delete( $delete_id );
	if ( is_wp_error( $result ) ) {
		$error = $result->get_error_message();
	} else {
		$message = __( 'Státusz törölve.', 'bma-ajanlatkeres' );
	}
}

if ( isset( $_POST['bma_status_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_status_nonce'] ) ), 'bma_status_save' ) ) {
	$table    = $wpdb->prefix . 'bma_statuses';
	$statuses = BMA_AJ_Statuses::get_all();
	foreach ( $statuses as $st ) {
		if ( isset( $_POST['status'][ $st->id ] ) ) {
			$data = wp_unslash( $_POST['status'][ $st->id ] );
			$wpdb->update(
				$table,
				array(
					'name'                => sanitize_text_field( $data['name'] ?? $st->name ),
					'color'               => sanitize_hex_color( $data['color'] ?? $st->color ),
					'sort_order'          => absint( $data['sort_order'] ?? $st->sort_order ),
					'client_visible'      => ! empty( $data['client_visible'] ) ? 1 : 0,
					'send_email'          => ! empty( $data['send_email'] ) ? 1 : 0,
					'email_template_slug' => sanitize_text_field( $data['email_template_slug'] ?? '' ),
				),
				array( 'id' => $st->id )
			);
		}
	}
	$message = __( 'Mentve.', 'bma-ajanlatkeres' );
}

$statuses = BMA_AJ_Statuses::get_all();
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Státuszok', 'bma-ajanlatkeres' ); ?></h1>

	<?php if ( $message ) : ?>
		<div class="notice notice-success"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>
	<?php if ( $error ) : ?>
		<div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
	<?php endif; ?>

	<form method="post">
		<?php wp_nonce_field( 'bma_status_save', 'bma_status_nonce' ); ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?></th>
					<th><?php esc_html_e( 'Szín', 'bma-ajanlatkeres' ); ?></th>
					<th><?php esc_html_e( 'Sorrend', 'bma-ajanlatkeres' ); ?></th>
					<th><?php esc_html_e( 'Ügyfél látható', 'bma-ajanlatkeres' ); ?></th>
					<th><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?></th>
					<th><?php esc_html_e( 'Sablon slug', 'bma-ajanlatkeres' ); ?></th>
					<th><?php esc_html_e( 'Művelet', 'bma-ajanlatkeres' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $statuses as $st ) : ?>
					<tr>
						<td><input type="text" name="status[<?php echo esc_attr( (string) $st->id ); ?>][name]" value="<?php echo esc_attr( $st->name ); ?>"></td>
						<td><input type="color" name="status[<?php echo esc_attr( (string) $st->id ); ?>][color]" value="<?php echo esc_attr( $st->color ); ?>"></td>
						<td><input type="number" name="status[<?php echo esc_attr( (string) $st->id ); ?>][sort_order]" value="<?php echo esc_attr( (string) $st->sort_order ); ?>" min="0"></td>
						<td><input type="checkbox" name="status[<?php echo esc_attr( (string) $st->id ); ?>][client_visible]" value="1" <?php checked( (int) $st->client_visible, 1 ); ?>></td>
						<td><input type="checkbox" name="status[<?php echo esc_attr( (string) $st->id ); ?>][send_email]" value="1" <?php checked( (int) $st->send_email, 1 ); ?>></td>
						<td><input type="text" name="status[<?php echo esc_attr( (string) $st->id ); ?>][email_template_slug]" value="<?php echo esc_attr( $st->email_template_slug ); ?>"></td>
						<td>
							<?php if ( empty( $st->is_default ) ) : ?>
								<button type="submit" form="bma-status-delete-<?php echo esc_attr( (string) $st->id ); ?>" class="button-link-delete" onclick="return confirm('<?php echo esc_js( __( 'Biztosan törli ezt a státuszt?', 'bma-ajanlatkeres' ) ); ?>');">
									<?php esc_html_e( 'Törlés', 'bma-ajanlatkeres' ); ?>
								</button>
							<?php else : ?>
								<em><?php esc_html_e( 'Alapértelmezett', 'bma-ajanlatkeres' ); ?></em>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Mentés', 'bma-ajanlatkeres' ); ?></button></p>
	</form>

	<?php foreach ( $statuses as $st ) : ?>
		<?php if ( empty( $st->is_default ) ) : ?>
			<form id="bma-status-delete-<?php echo esc_attr( (string) $st->id ); ?>" method="post" style="display:none">
				<?php wp_nonce_field( 'bma_status_delete', 'bma_status_delete_nonce' ); ?>
				<input type="hidden" name="delete_status_id" value="<?php echo esc_attr( (string) $st->id ); ?>">
			</form>
		<?php endif; ?>
	<?php endforeach; ?>

	<hr>
	<h2><?php esc_html_e( 'Új státusz', 'bma-ajanlatkeres' ); ?></h2>
	<form method="post">
		<?php wp_nonce_field( 'bma_status_create', 'bma_status_create_nonce' ); ?>
		<table class="form-table">
			<tr>
				<th><label for="new_name"><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?></label></th>
				<td><input type="text" id="new_name" name="new_name" class="regular-text" required></td>
			</tr>
			<tr>
				<th><label for="new_color"><?php esc_html_e( 'Szín', 'bma-ajanlatkeres' ); ?></label></th>
				<td><input type="color" id="new_color" name="new_color" value="#2271b1"></td>
			</tr>
			<tr>
				<th><label for="new_sort_order"><?php esc_html_e( 'Sorrend', 'bma-ajanlatkeres' ); ?></label></th>
				<td><input type="number" id="new_sort_order" name="new_sort_order" value="99" min="0"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Beállítások', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<label><input type="checkbox" name="new_client_visible" value="1" checked> <?php esc_html_e( 'Ügyfél látható', 'bma-ajanlatkeres' ); ?></label><br>
					<label><input type="checkbox" name="new_send_email" value="1"> <?php esc_html_e( 'E-mail küldése', 'bma-ajanlatkeres' ); ?></label>
				</td>
			</tr>
			<tr>
				<th><label for="new_email_template_slug"><?php esc_html_e( 'Sablon slug', 'bma-ajanlatkeres' ); ?></label></th>
				<td><input type="text" id="new_email_template_slug" name="new_email_template_slug" class="regular-text" placeholder="status_change"></td>
			</tr>
		</table>
		<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Státusz létrehozása', 'bma-ajanlatkeres' ); ?></button></p>
	</form>
</div>
