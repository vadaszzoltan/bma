<?php
defined( 'ABSPATH' ) || exit;
global $wpdb;
$fields_table = $wpdb->prefix . 'bma_dynamic_fields';
$assign_table = $wpdb->prefix . 'bma_field_assignments';
$edit_id      = absint( $_GET['edit'] ?? 0 );
$edit_field   = $edit_id ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$fields_table} WHERE id = %d", $edit_id ) ) : null;

if ( isset( $_POST['bma_field_delete'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_field_nonce'] ?? '' ) ), 'bma_field_delete' ) ) {
	$delete_id = absint( $_POST['field_id'] ?? 0 );
	if ( $delete_id ) {
		$wpdb->delete( $assign_table, array( 'field_id' => $delete_id ) );
		$wpdb->delete( $fields_table, array( 'id' => $delete_id ) );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Mező törölve.', 'bma-ajanlatkeres' ) . '</p></div>';
	}
}

if ( isset( $_POST['bma_field_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_field_nonce'] ) ), 'bma_field_save' ) ) {
	$options_raw = (string) wp_unslash( $_POST['options'] ?? '' );
	$data        = array(
		'field_key'    => sanitize_key( wp_unslash( $_POST['field_key'] ?? '' ) ),
		'field_type'   => sanitize_text_field( wp_unslash( $_POST['field_type'] ?? 'text' ) ),
		'admin_label'  => sanitize_text_field( wp_unslash( $_POST['admin_label'] ?? '' ) ),
		'client_label' => sanitize_text_field( wp_unslash( $_POST['client_label'] ?? '' ) ),
		'is_required'  => ! empty( $_POST['is_required'] ) ? 1 : 0,
		'is_active'    => ! empty( $_POST['is_active'] ) ? 1 : 0,
		'options'      => wp_json_encode( array_values( array_filter( array_map( 'trim', explode( "\n", $options_raw ) ) ) ) ),
		'description'  => sanitize_textarea_field( wp_unslash( $_POST['description'] ?? '' ) ),
		'placeholder'  => sanitize_text_field( wp_unslash( $_POST['placeholder'] ?? '' ) ),
		'placement'    => in_array( $_POST['placement'] ?? '', array( 'project', 'documents', 'item', 'addons' ), true ) ? sanitize_key( $_POST['placement'] ) : 'item',
		'conditions'   => trim( (string) wp_unslash( $_POST['conditions'] ?? '' ) ),
		'sort_order'   => absint( $_POST['sort_order'] ?? 0 ),
	);
	$conditions_ok = '' === $data['conditions'] || is_array( json_decode( $data['conditions'], true ) );
	if ( '' === $data['conditions'] ) {
		$data['conditions'] = null;
	}

	$update_id = absint( $_POST['field_id'] ?? 0 );
	if ( ! $conditions_ok ) {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'A feltételek mező nem érvényes JSON.', 'bma-ajanlatkeres' ) . '</p></div>';
	} elseif ( $update_id ) {
		unset( $data['field_key'] );
		$wpdb->update( $fields_table, $data, array( 'id' => $update_id ) );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Mező frissítve.', 'bma-ajanlatkeres' ) . '</p></div>';
	} else {
		$result = BMA_AJ_Dynamic_Fields::create_field(
			array(
				'field_key'    => $data['field_key'],
				'field_type'   => $data['field_type'],
				'admin_label'  => $data['admin_label'],
				'client_label' => $data['client_label'],
				'is_required'  => $data['is_required'],
				'options'      => array_filter( array_map( 'trim', explode( "\n", $options_raw ) ) ),
				'description'  => $data['description'],
				'placeholder'  => $data['placeholder'],
			)
		);
		if ( is_wp_error( $result ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
		} else {
			$wpdb->update( $fields_table, array_intersect_key( $data, array_flip( array( 'placement', 'conditions', 'sort_order' ) ) ), array( 'id' => $result ) );
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Mező létrehozva. Rendelje hozzá egy szolgáltatáselemhez a Szolgáltatáselemek menüben.', 'bma-ajanlatkeres' ) . '</p></div>';
		}
	}
}

$fields       = $wpdb->get_results( "SELECT * FROM {$fields_table} ORDER BY sort_order ASC, admin_label ASC" );
$edit_options = $edit_field ? implode( "\n", json_decode( $edit_field->options ?? '[]', true ) ?: array() ) : '';
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Dinamikus mezők', 'bma-ajanlatkeres' ); ?></h1>
	<p class="description">
		<?php esc_html_e( 'Ez a mezőkönyvtár. A megjelenést a Szolgáltatáselemek szerkesztőjében állítja be. Azonos mezőkulcs több elemnél is csak egyszer jelenik meg az ajánlatkérésben.', 'bma-ajanlatkeres' ); ?>
	</p>

	<h2><?php echo $edit_field ? esc_html__( 'Mező szerkesztése', 'bma-ajanlatkeres' ) : esc_html__( 'Új mező', 'bma-ajanlatkeres' ); ?></h2>
	<form method="post">
		<?php wp_nonce_field( 'bma_field_save', 'bma_field_nonce' ); ?>
		<?php if ( $edit_field ) : ?>
			<input type="hidden" name="field_id" value="<?php echo esc_attr( (string) $edit_field->id ); ?>">
		<?php endif; ?>
		<table class="form-table" role="presentation">
			<tr>
				<th><?php esc_html_e( 'Mező kulcs', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<input type="text" name="field_key" required pattern="[a-z0-9_]+" value="<?php echo esc_attr( $edit_field->field_key ?? '' ); ?>" <?php echo $edit_field ? 'readonly' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Egyedi azonosító. Ha két szolgáltatáselem ugyanazt a kulcsot használja, az ügyfél csak egyszer tölti ki.', 'bma-ajanlatkeres' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Típus', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<select name="field_type">
						<?php foreach ( BMA_AJ_Dynamic_Fields::FIELD_TYPES as $type ) : ?>
							<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $edit_field->field_type ?? '', $type ); ?>><?php echo esc_html( $type ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Admin label', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="text" name="admin_label" class="regular-text" required value="<?php echo esc_attr( $edit_field->admin_label ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Ügyfél label', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="text" name="client_label" class="regular-text" required value="<?php echo esc_attr( $edit_field->client_label ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Opciók (soronként)', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<textarea name="options" rows="4" class="large-text"><?php echo esc_textarea( $edit_options ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Soronként egy. Formátum: ertek|Felirat (pl. igen|Igen). Az érték maradjon változatlan, a felirat szabadon írható.', 'bma-ajanlatkeres' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Segédszöveg', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="text" name="description" class="large-text" value="<?php echo esc_attr( $edit_field->description ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Minta (placeholder)', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="text" name="placeholder" class="regular-text" value="<?php echo esc_attr( $edit_field->placeholder ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Hol jelenik meg', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<select name="placement">
						<?php
						$placements = array(
							'project'   => __( '1. lépés: projekt adatai', 'bma-ajanlatkeres' ),
							'addons'    => __( '4. lépés: kiegészítők', 'bma-ajanlatkeres' ),
							'documents' => __( '5. lépés: dokumentumok (mindig)', 'bma-ajanlatkeres' ),
							'item'      => __( '5. lépés: a hozzárendelt elemeknél', 'bma-ajanlatkeres' ),
						);
						foreach ( $placements as $value => $label ) :
							?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $edit_field->placement ?? 'item', $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Feltételek', 'bma-ajanlatkeres' ); ?></th>
				<td>
					<textarea name="conditions" rows="2" class="large-text code" placeholder='{"field":"urbanisztikai_bizonylat","values":["igen"]}'><?php echo esc_textarea( (string) ( $edit_field->conditions ?? '' ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'JSON, nem kötelező. field + values: csak akkor látszik, ha a másik mező értéke ezek egyike. required_services: ezeknél a szolgáltatásoknál kötelező. one_of: elég, ha a felsorolt mezők egyike ki van töltve. soft_required: hiányánál csak figyelmeztet.', 'bma-ajanlatkeres' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Sorrend', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="number" name="sort_order" min="0" value="<?php echo esc_attr( (string) ( $edit_field->sort_order ?? 0 ) ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Kötelező', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="checkbox" name="is_required" value="1" <?php checked( ! empty( $edit_field->is_required ) ); ?>></td>
			</tr>
			<?php if ( $edit_field ) : ?>
			<tr>
				<th><?php esc_html_e( 'Aktív', 'bma-ajanlatkeres' ); ?></th>
				<td><input type="checkbox" name="is_active" value="1" <?php checked( ! isset( $edit_field->is_active ) || $edit_field->is_active ); ?>></td>
			</tr>
			<?php endif; ?>
		</table>
		<p>
			<button type="submit" class="button button-primary"><?php echo $edit_field ? esc_html__( 'Mentés', 'bma-ajanlatkeres' ) : esc_html__( 'Mező létrehozása', 'bma-ajanlatkeres' ); ?></button>
			<?php if ( $edit_field ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=bma-fields' ) ); ?>" class="button"><?php esc_html_e( 'Mégse', 'bma-ajanlatkeres' ); ?></a>
			<?php endif; ?>
		</p>
	</form>

	<h2><?php esc_html_e( 'Meglévő mezők', 'bma-ajanlatkeres' ); ?></h2>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th>Key</th>
				<th>Label</th>
				<th><?php esc_html_e( 'Típus', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Használat', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Kötelező', 'bma-ajanlatkeres' ); ?></th>
				<th><?php esc_html_e( 'Aktív', 'bma-ajanlatkeres' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $fields as $f ) : ?>
				<?php
				$usage = (int) $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM {$assign_table} WHERE field_id = %d AND entity_type = 'service_item'",
						$f->id
					)
				);
				?>
				<tr>
					<td><code><?php echo esc_html( $f->field_key ); ?></code></td>
					<td><?php echo esc_html( $f->client_label ); ?></td>
					<td><?php echo esc_html( $f->field_type ); ?></td>
					<td><?php echo esc_html( sprintf( /* translators: %d: number of service items */ _n( '%d szolgáltatáselem', '%d szolgáltatáselem', $usage, 'bma-ajanlatkeres' ), $usage ) ); ?></td>
					<td><?php echo $f->is_required ? '✓' : '–'; ?></td>
					<td><?php echo $f->is_active ? '✓' : '–'; ?></td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=bma-fields&edit=' . $f->id ) ); ?>"><?php esc_html_e( 'Szerkesztés', 'bma-ajanlatkeres' ); ?></a>
						<form method="post" style="display:inline" onsubmit="return confirm('<?php echo esc_js( __( 'Biztosan törli?', 'bma-ajanlatkeres' ) ); ?>');">
							<?php wp_nonce_field( 'bma_field_delete', 'bma_field_nonce' ); ?>
							<input type="hidden" name="field_id" value="<?php echo esc_attr( (string) $f->id ); ?>">
							<button type="submit" name="bma_field_delete" class="button-link-delete"><?php esc_html_e( 'Törlés', 'bma-ajanlatkeres' ); ?></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
