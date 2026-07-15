<?php
defined( 'ABSPATH' ) || exit;
global $wpdb;
$statuses = BMA_AJ_Statuses::get_all();

if ( isset( $_POST['bma_status_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_status_nonce'] ) ), 'bma_status_save' ) ) {
	$table = $wpdb->prefix . 'bma_statuses';
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
	$statuses = BMA_AJ_Statuses::get_all();
	echo '<div class="notice notice-success"><p>' . esc_html__( 'Mentve.', 'bma-ajanlatkeres' ) . '</p></div>';
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Státuszok', 'bma-ajanlatkeres' ); ?></h1>
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
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $statuses as $st ) : ?>
					<tr>
						<td><input type="text" name="status[<?php echo esc_attr( $st->id ); ?>][name]" value="<?php echo esc_attr( $st->name ); ?>"></td>
						<td><input type="color" name="status[<?php echo esc_attr( $st->id ); ?>][color]" value="<?php echo esc_attr( $st->color ); ?>"></td>
						<td><input type="number" name="status[<?php echo esc_attr( $st->id ); ?>][sort_order]" value="<?php echo esc_attr( $st->sort_order ); ?>" min="0"></td>
						<td><input type="checkbox" name="status[<?php echo esc_attr( $st->id ); ?>][client_visible]" value="1" <?php checked( $st->client_visible, 1 ); ?>></td>
						<td><input type="checkbox" name="status[<?php echo esc_attr( $st->id ); ?>][send_email]" value="1" <?php checked( $st->send_email, 1 ); ?>></td>
						<td><input type="text" name="status[<?php echo esc_attr( $st->id ); ?>][email_template_slug]" value="<?php echo esc_attr( $st->email_template_slug ); ?>"></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Mentés', 'bma-ajanlatkeres' ); ?></button></p>
	</form>
</div>
