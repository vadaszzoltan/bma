<?php
defined( 'ABSPATH' ) || exit;

$services   = get_posts(
	array(
		'post_type'      => BMA_AJ_Service_CPT::POST_TYPE,
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);
$all_fields = BMA_AJ_Dynamic_Fields::get_all_active();
$edit_id    = absint( $_GET['edit'] ?? 0 );
$edit_item  = $edit_id ? BMA_AJ_Service_Items::get( $edit_id ) : null;
$edit_svcs  = $edit_item ? BMA_AJ_Service_Items::get_service_ids( (int) $edit_item->id ) : array();
$edit_flds  = $edit_item ? BMA_AJ_Service_Items::get_field_ids( (int) $edit_item->id ) : array();

if ( isset( $_POST['bma_item_delete'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_item_nonce'] ?? '' ) ), 'bma_item_delete' ) ) {
	$delete_id = absint( $_POST['item_id'] ?? 0 );
	if ( $delete_id ) {
		BMA_AJ_Service_Items::delete( $delete_id );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Elem törölve.', 'bma-ajanlatkeres' ) . '</p></div>';
	}
}

if ( isset( $_POST['bma_item_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_item_nonce'] ) ), 'bma_item_save' ) ) {
	$field_ids = array_map( 'absint', (array) ( $_POST['field_ids'] ?? array() ) );

	// Optional inline new field creation, then attach to this item.
	if ( ! empty( $_POST['new_field_key'] ) && ! empty( $_POST['new_field_label'] ) ) {
		$options_raw = (string) wp_unslash( $_POST['new_field_options'] ?? '' );
		$created     = BMA_AJ_Dynamic_Fields::create_field(
			array(
				'field_key'    => sanitize_key( wp_unslash( $_POST['new_field_key'] ) ),
				'field_type'   => sanitize_text_field( wp_unslash( $_POST['new_field_type'] ?? 'text' ) ),
				'admin_label'  => sanitize_text_field( wp_unslash( $_POST['new_field_label'] ) ),
				'client_label' => sanitize_text_field( wp_unslash( $_POST['new_field_label'] ) ),
				'is_required'  => ! empty( $_POST['new_field_is_required'] ),
				'options'      => array_filter( array_map( 'trim', explode( "\n", $options_raw ) ) ),
			)
		);
		if ( is_wp_error( $created ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html( $created->get_error_message() ) . '</p></div>';
		} else {
			$field_ids[] = (int) $created;
			$all_fields  = BMA_AJ_Dynamic_Fields::get_all_active();
		}
	}

	$result = BMA_AJ_Service_Items::save(
		array(
			'id'                => absint( $_POST['item_id'] ?? 0 ),
			'name'              => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
			'short_description' => sanitize_textarea_field( wp_unslash( $_POST['short_description'] ?? '' ) ),
			'choose_if'         => sanitize_textarea_field( wp_unslash( $_POST['choose_if'] ?? '' ) ),
			'sort_order'        => absint( $_POST['sort_order'] ?? 0 ),
			'is_required'       => ! empty( $_POST['is_required'] ),
			'is_active'         => ! empty( $_POST['is_active'] ),
			'service_ids'       => array_map( 'absint', (array) ( $_POST['service_ids'] ?? array() ) ),
			'field_ids'         => $field_ids,
			'item_type'         => sanitize_key( wp_unslash( $_POST['item_type'] ?? 'item' ) ),
			'auto_rule'         => wp_unslash( $_POST['auto_rule'] ?? '' ), // Validated as JSON in save().
		)
	);

	if ( is_wp_error( $result ) ) {
		echo '<div class="notice notice-error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
	} else {
		$msg = absint( $_POST['item_id'] ?? 0 )
			? __( 'Elem frissítve.', 'bma-ajanlatkeres' )
			: __( 'Elem létrehozva.', 'bma-ajanlatkeres' );
		echo '<div class="notice notice-success"><p>' . esc_html( $msg ) . '</p></div>';
		$edit_id   = (int) $result;
		$edit_item = BMA_AJ_Service_Items::get( $edit_id );
		$edit_svcs = BMA_AJ_Service_Items::get_service_ids( $edit_id );
		$edit_flds = BMA_AJ_Service_Items::get_field_ids( $edit_id );
	}
}

$items = BMA_AJ_Service_Items::get_all_admin();
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Szolgáltatáselemek', 'bma-ajanlatkeres' ); ?></h1>
	<p class="description">
		<?php esc_html_e( 'Egy szolgáltatáselem több szolgáltatáshoz is kapcsolható. A dinamikus mezőket az elemen állítsa be; ha ugyanaz a mező több elemnél is szerepel, az ügyfél csak egyszer tölti ki.', 'bma-ajanlatkeres' ); ?>
	</p>

	<h2><?php echo $edit_item ? esc_html__( 'Elem szerkesztése', 'bma-ajanlatkeres' ) : esc_html__( 'Új elem', 'bma-ajanlatkeres' ); ?></h2>
	<form method="post">
		<?php wp_nonce_field( 'bma_item_save', 'bma_item_nonce' ); ?>
		<?php if ( $edit_item ) : ?>
			<input type="hidden" name="item_id" value="<?php echo esc_attr( (string) $edit_item->id ); ?>">
		<?php endif; ?>
		<table class="form-table" role="presentation">
			<tr>
				<th><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="text" name="name" class="regular-text" required value="<?php echo esc_attr( $edit_item->name ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Megjelenik ezeknél a szolgáltatásoknál', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<?php if ( empty( $services ) ) : ?>
						<p><?php esc_html_e( 'Előbb hozzon létre legalább egy szolgáltatást.', 'bma-ajanlatkeres' ); ?></p>
					<?php else : ?>
						<fieldset style="max-height:220px;overflow:auto;border:1px solid #c3c4c7;padding:8px 12px;">
							<?php foreach ( $services as $svc ) : ?>
								<label style="display:block;margin:4px 0;">
									<input type="checkbox" name="service_ids[]" value="<?php echo esc_attr( (string) $svc->ID ); ?>" <?php checked( in_array( (int) $svc->ID, $edit_svcs, true ) ); ?>>
									<?php echo esc_html( $svc->post_title ); ?>
								</label>
							<?php endforeach; ?>
						</fieldset>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Típus', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<select name="item_type">
						<option value="item" <?php selected( $edit_item->item_type ?? 'item', 'item' ); ?>><?php esc_html_e( 'Tervezési elem', 'bma-ajanlatkeres' ); ?></option>
						<option value="addon" <?php selected( $edit_item->item_type ?? 'item', 'addon' ); ?>><?php esc_html_e( 'Kiegészítő', 'bma-ajanlatkeres' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Kiegészítő szolgáltatás nélkül: általános kiegészítő (minden ajánlatkérésben). Szolgáltatáshoz rendelve: annak engedélyeztetése.', 'bma-ajanlatkeres' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Automatikus kijelölés (kiegészítő)', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<textarea name="auto_rule" rows="3" class="large-text code" placeholder='{"any":[{"field":"urbanisztikai_bizonylat","values":["nem"]}]}'><?php echo esc_textarea( (string) ( $edit_item->auto_rule ?? '' ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'JSON. Bármelyik feltétel teljesül, a kiegészítő bekerül. Mező: egy mező kulcsa, vagy _phase (tervfázis), _addon (kiválasztott kiegészítő). "locked": true esetén nem vehető ki. Üres: csak kézzel választható.', 'bma-ajanlatkeres' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Leírás', 'bma-ajanlatkeres' ); ?></th>
				<td><textarea name="short_description" rows="2" class="large-text"><?php echo esc_textarea( $edit_item->short_description ?? '' ); ?></textarea></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Ezt válassza, ha…', 'bma-ajanlatkeres' ); ?></th>
				<td><textarea name="choose_if" rows="2" class="large-text"><?php echo esc_textarea( $edit_item->choose_if ?? '' ); ?></textarea></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Sorrend', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="number" name="sort_order" value="<?php echo esc_attr( (string) ( $edit_item->sort_order ?? 0 ) ); ?>" min="0"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Kötelező elem', 'bma-ajanlatkeres' ); ?></th>
				<td><label><input type="checkbox" name="is_required" value="1" <?php checked( ! empty( $edit_item->is_required ) ); ?>> <?php esc_html_e( 'Automatikusan kijelölve', 'bma-ajanlatkeres' ); ?></label></td>
			</tr>
			<?php if ( $edit_item ) : ?>
			<tr>
				<th><?php esc_html_e( 'Aktív', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="checkbox" name="is_active" value="1" <?php checked( ! isset( $edit_item->is_active ) || $edit_item->is_active ); ?>></td>
			</tr>
			<?php endif; ?>
			<tr>
				<th><?php esc_html_e( 'Dinamikus mezők', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<p class="description"><?php esc_html_e( 'Az itt bejelölt mezők jelennek meg, ha az ügyfél ezt az elemet választja. Azonos mezőkulcs több elemnél is csak egyszer kerül kitöltésre.', 'bma-ajanlatkeres' ); ?></p>
					<?php if ( empty( $all_fields ) ) : ?>
						<p><?php esc_html_e( 'Még nincs mező a könyvtárban. Hozzon létre egyet alább, vagy a Dinamikus mezők menüben.', 'bma-ajanlatkeres' ); ?></p>
					<?php else : ?>
						<fieldset style="max-height:280px;overflow:auto;border:1px solid #c3c4c7;padding:8px 12px;margin-bottom:12px;">
							<?php foreach ( $all_fields as $field ) : ?>
								<label style="display:block;margin:4px 0;">
									<input type="checkbox" name="field_ids[]" value="<?php echo esc_attr( (string) $field->id ); ?>" <?php checked( in_array( (int) $field->id, $edit_flds, true ) ); ?>>
									<strong><?php echo esc_html( $field->client_label ); ?></strong>
									<code><?php echo esc_html( $field->field_key ); ?></code>
									<span style="color:#646970;">(<?php echo esc_html( $field->field_type ); ?>)</span>
								</label>
							<?php endforeach; ?>
						</fieldset>
					<?php endif; ?>

					<details style="margin-top:8px;">
						<summary><strong><?php esc_html_e( 'Új dinamikus mező létrehozása ehhez az elemhez', 'bma-ajanlatkeres' ); ?></strong></summary>
						<table class="form-table" role="presentation">
							<tr>
								<th><?php esc_html_e( 'Mező kulcs', 'bma-ajanlatkeres' ); ?></th>
								<td><input type="text" name="new_field_key" pattern="[a-z0-9_]+" class="regular-text" placeholder="pl. projekt_helyszin"></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Címke', 'bma-ajanlatkeres' ); ?></th>
								<td><input type="text" name="new_field_label" class="regular-text"></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Típus', 'bma-ajanlatkeres' ); ?></th>
								<td>
									<select name="new_field_type">
										<?php foreach ( BMA_AJ_Dynamic_Fields::FIELD_TYPES as $type ) : ?>
											<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Opciók (soronként)', 'bma-ajanlatkeres' ); ?></th>
								<td><textarea name="new_field_options" rows="3" class="large-text"></textarea></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Kötelező', 'bma-ajanlatkeres' ); ?></th>
								<td><input type="checkbox" name="new_field_is_required" value="1"></td>
							</tr>
						</table>
					</details>
				</td>
			</tr>
		</table>
		<p>
			<button type="submit" class="button button-primary"><?php echo $edit_item ? esc_html__( 'Mentés', 'bma-ajanlatkeres' ) : esc_html__( 'Elem hozzáadása', 'bma-ajanlatkeres' ); ?></button>
			<?php if ( $edit_item ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=bma-service-items' ) ); ?>" class="button"><?php esc_html_e( 'Új elem', 'bma-ajanlatkeres' ); ?></a>
			<?php endif; ?>
		</p>
	</form>

	<h2><?php esc_html_e( 'Meglévő elemek', 'bma-ajanlatkeres' ); ?></h2>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Elem', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Típus', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Mezők', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Aktív', 'bma-ajanlatkeres' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $items as $item ) : ?>
				<?php
				$field_count = count( BMA_AJ_Service_Items::get_field_ids( (int) $item->id ) );
				?>
				<tr>
					<td><strong><?php echo esc_html( $item->name ); ?></strong></td>
					<td><?php echo esc_html( $item->service_names ?: '—' ); ?></td>
					<td><?php echo 'addon' === ( $item->item_type ?? 'item' ) ? esc_html__( 'Kiegészítő', 'bma-ajanlatkeres' ) : esc_html__( 'Elem', 'bma-ajanlatkeres' ); ?></td>
					<td><?php echo esc_html( (string) $field_count ); ?></td>
					<td><?php echo ! empty( $item->is_active ) ? '✓' : '–'; ?></td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=bma-service-items&edit=' . $item->id ) ); ?>"><?php esc_html_e( 'Szerkesztés', 'bma-ajanlatkeres' ); ?></a>
						<form method="post" style="display:inline" onsubmit="return confirm('<?php echo esc_js( __( 'Biztosan törli?', 'bma-ajanlatkeres' ) ); ?>');">
							<?php wp_nonce_field( 'bma_item_delete', 'bma_item_nonce' ); ?>
							<input type="hidden" name="item_id" value="<?php echo esc_attr( (string) $item->id ); ?>">
							<button type="submit" name="bma_item_delete" class="button-link-delete"><?php esc_html_e( 'Törlés', 'bma-ajanlatkeres' ); ?></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
