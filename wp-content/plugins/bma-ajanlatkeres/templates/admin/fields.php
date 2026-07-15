<?php
defined( 'ABSPATH' ) || exit;
global $wpdb;
$fields_table = $wpdb->prefix . 'bma_dynamic_fields';
$assign_table = $wpdb->prefix . 'bma_field_assignments';
$services     = get_posts( array( 'post_type' => BMA_AJ_Service_CPT::POST_TYPE, 'posts_per_page' => -1, 'post_status' => 'publish' ) );

if ( isset( $_POST['bma_field_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_field_nonce'] ) ), 'bma_field_save' ) ) {
	$wpdb->insert(
		$fields_table,
		array(
			'field_key'    => sanitize_key( wp_unslash( $_POST['field_key'] ?? '' ) ),
			'field_type'   => sanitize_text_field( wp_unslash( $_POST['field_type'] ?? 'text' ) ),
			'admin_label'  => sanitize_text_field( wp_unslash( $_POST['admin_label'] ?? '' ) ),
			'client_label' => sanitize_text_field( wp_unslash( $_POST['client_label'] ?? '' ) ),
			'is_required'  => ! empty( $_POST['is_required'] ) ? 1 : 0,
			'options'      => wp_json_encode( array_filter( array_map( 'trim', explode( "\n", wp_unslash( $_POST['options'] ?? '' ) ) ) ) ),
			'is_active'    => 1,
		)
	);
	$field_id = (int) $wpdb->insert_id;
	if ( $field_id && ! empty( $_POST['service_id'] ) ) {
		$wpdb->insert(
			$assign_table,
			array(
				'field_id'    => $field_id,
				'entity_type' => 'service',
				'entity_id'   => absint( $_POST['service_id'] ),
			)
		);
	}
	echo '<div class="notice notice-success"><p>' . esc_html__( 'Mező létrehozva.', 'bma-ajanlatkeres' ) . '</p></div>';
}

$fields = $wpdb->get_results( "SELECT * FROM {$fields_table} ORDER BY sort_order ASC" );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Dinamikus mezők', 'bma-ajanlatkeres' ); ?></h1>

	<h2><?php esc_html_e( 'Új mező', 'bma-ajanlatkeres' ); ?></h2>
	<form method="post">
		<?php wp_nonce_field( 'bma_field_save', 'bma_field_nonce' ); ?>
		<table class="form-table">
			<tr><th><?php esc_html_e( 'Mező kulcs', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="field_key" required pattern="[a-z0-9_]+"></td></tr>
			<tr><th><?php esc_html_e( 'Típus', 'bma-ajanlatkeres' ); ?></th><td>
				<select name="field_type">
					<?php foreach ( BMA_AJ_Dynamic_Fields::FIELD_TYPES as $type ) : ?>
						<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><?php esc_html_e( 'Admin label', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="admin_label" class="regular-text" required></td></tr>
			<tr><th><?php esc_html_e( 'Ügyfél label', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="client_label" class="regular-text" required></td></tr>
			<tr><th><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></th><td>
				<select name="service_id">
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( $svc->ID ); ?>"><?php echo esc_html( $svc->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><?php esc_html_e( 'Opciók (soronként)', 'bma-ajanlatkeres' ); ?></th><td><textarea name="options" rows="4" class="large-text"></textarea></td></tr>
			<tr><th><?php esc_html_e( 'Kötelező', 'bma-ajanlatkeres' ); ?></th><td><input type="checkbox" name="is_required" value="1"></td></tr>
		</table>
		<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Mező létrehozása', 'bma-ajanlatkeres' ); ?></button></p>
	</form>

	<h2><?php esc_html_e( 'Meglévő mezők', 'bma-ajanlatkeres' ); ?></h2>
	<table class="wp-list-table widefat fixed striped">
		<thead><tr><th>Key</th><th>Label</th><th>Típus</th><th>Kötelező</th></tr></thead>
		<tbody>
			<?php foreach ( $fields as $f ) : ?>
				<tr>
					<td><?php echo esc_html( $f->field_key ); ?></td>
					<td><?php echo esc_html( $f->client_label ); ?></td>
					<td><?php echo esc_html( $f->field_type ); ?></td>
					<td><?php echo $f->is_required ? '✓' : '–'; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
