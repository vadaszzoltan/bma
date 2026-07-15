<?php
defined( 'ABSPATH' ) || exit;
global $wpdb;
$table    = $wpdb->prefix . 'bma_service_items';
$services = get_posts( array( 'post_type' => BMA_AJ_Service_CPT::POST_TYPE, 'posts_per_page' => -1, 'post_status' => 'publish' ) );

if ( isset( $_POST['bma_item_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_item_nonce'] ) ), 'bma_item_save' ) ) {
	$wpdb->insert(
		$table,
		array(
			'service_id'        => absint( $_POST['service_id'] ?? 0 ),
			'name'              => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
			'slug'              => sanitize_title( wp_unslash( $_POST['name'] ?? '' ) ),
			'short_description' => sanitize_textarea_field( wp_unslash( $_POST['short_description'] ?? '' ) ),
			'is_active'         => 1,
			'is_required'       => ! empty( $_POST['is_required'] ) ? 1 : 0,
			'sort_order'        => absint( $_POST['sort_order'] ?? 0 ),
		)
	);
	echo '<div class="notice notice-success"><p>' . esc_html__( 'Elem létrehozva.', 'bma-ajanlatkeres' ) . '</p></div>';
}

$items = $wpdb->get_results(
	"SELECT i.*, p.post_title as service_name FROM {$table} i
	LEFT JOIN {$wpdb->posts} p ON p.ID = i.service_id
	ORDER BY i.service_id, i.sort_order ASC"
);
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Szolgáltatáselemek', 'bma-ajanlatkeres' ); ?></h1>

	<form method="post">
		<?php wp_nonce_field( 'bma_item_save', 'bma_item_nonce' ); ?>
		<table class="form-table">
			<tr><th><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></th><td>
				<select name="service_id" required>
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( $svc->ID ); ?>"><?php echo esc_html( $svc->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="name" class="regular-text" required></td></tr>
			<tr><th><?php esc_html_e( 'Leírás', 'bma-ajanlatkeres' ); ?></th><td><textarea name="short_description" rows="2" class="large-text"></textarea></td></tr>
			<tr><th><?php esc_html_e( 'Sorrend', 'bma-ajanlatkeres' ); ?></th><td><input type="number" name="sort_order" value="0" min="0"></td></tr>
			<tr><th><?php esc_html_e( 'Kötelező', 'bma-ajanlatkeres' ); ?></th><td><input type="checkbox" name="is_required" value="1"></td></tr>
		</table>
		<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Elem hozzáadása', 'bma-ajanlatkeres' ); ?></button></p>
	</form>

	<table class="wp-list-table widefat fixed striped">
		<thead><tr><th>Szolgáltatás</th><th>Elem</th><th>Leírás</th><th>Aktív</th></tr></thead>
		<tbody>
			<?php foreach ( $items as $item ) : ?>
				<tr>
					<td><?php echo esc_html( $item->service_name ); ?></td>
					<td><?php echo esc_html( $item->name ); ?></td>
					<td><?php echo esc_html( $item->short_description ); ?></td>
					<td><?php echo $item->is_active ? '✓' : '–'; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
