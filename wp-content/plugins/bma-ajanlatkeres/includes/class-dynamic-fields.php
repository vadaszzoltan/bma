<?php
/**
 * Dynamic fields management.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Dynamic_Fields {

	public const FIELD_TYPES = array(
		'text', 'textarea', 'number', 'email', 'tel', 'date',
		'select', 'radio', 'checkbox', 'multiselect', 'file', 'address', 'hidden',
	);

	/**
	 * Get fields for service and selected items.
	 *
	 * @param int   $service_id     Service post ID.
	 * @param int[] $selected_items Selected item IDs.
	 * @return array<int, object>
	 */
	public static function get_fields_for_context( int $service_id, array $selected_items = array() ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_dynamic_fields';
		$assign = $wpdb->prefix . 'bma_field_assignments';

		$entity_ids = array_merge( array( $service_id ), $selected_items );
		$placeholders = implode( ',', array_fill( 0, count( $entity_ids ), '%d' ) );

		$sql = $wpdb->prepare(
			"SELECT f.*, a.entity_type, a.entity_id, a.sort_order as assign_order
			FROM {$table} f
			INNER JOIN {$assign} a ON a.field_id = f.id
			WHERE f.is_active = 1
			AND (
				(a.entity_type = 'service' AND a.entity_id = %d)
				OR (a.entity_type = 'service_item' AND a.entity_id IN ($placeholders))
			)
			ORDER BY a.sort_order ASC, f.sort_order ASC",
			array_merge( array( $service_id ), $selected_items )
		);

		return $wpdb->get_results( $sql );
	}

	/**
	 * Get service items for a service.
	 *
	 * @param int $service_id Service ID.
	 */
	public static function get_service_items( int $service_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_service_items';

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE service_id = %d AND is_active = 1 ORDER BY sort_order ASC",
				$service_id
			)
		);
	}

	/**
	 * Render field HTML.
	 *
	 * @param object $field Field object.
	 * @param mixed  $value Current value.
	 */
	public static function render_field( object $field, $value = '' ): string {
		$id    = esc_attr( 'bma_field_' . $field->field_key );
		$name  = esc_attr( 'field_values[' . $field->field_key . ']' );
		$label = esc_html( $field->client_label );
		$req   = $field->is_required ? ' required' : '';
		$desc  = $field->description ? '<p class="bma-field-desc">' . esc_html( $field->description ) . '</p>' : '';
		$ph    = esc_attr( $field->placeholder );

		$options = json_decode( $field->options ?? '[]', true ) ?: array();

		ob_start();
		echo '<div class="bma-form-field bma-field-type-' . esc_attr( $field->field_type ) . '">';
		if ( 'hidden' !== $field->field_type ) {
			echo '<label for="' . $id . '">' . $label;
			if ( $field->is_required ) {
				echo ' <span class="required">*</span>';
			}
			echo '</label>';
		}

		switch ( $field->field_type ) {
			case 'textarea':
				echo '<textarea id="' . $id . '" name="' . $name . '" placeholder="' . $ph . '"' . $req . '>' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'number':
				echo '<input type="number" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" placeholder="' . $ph . '"' . $req . '>';
				break;
			case 'email':
				echo '<input type="email" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" placeholder="' . $ph . '"' . $req . '>';
				break;
			case 'tel':
				echo '<input type="tel" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" placeholder="' . $ph . '"' . $req . '>';
				break;
			case 'date':
				echo '<input type="date" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '"' . $req . '>';
				break;
			case 'select':
				echo '<select id="' . $id . '" name="' . $name . '"' . $req . '>';
				echo '<option value="">' . esc_html__( 'Válasszon…', 'bma-ajanlatkeres' ) . '</option>';
				foreach ( $options as $opt ) {
					echo '<option value="' . esc_attr( $opt ) . '" ' . selected( $value, $opt, false ) . '>' . esc_html( $opt ) . '</option>';
				}
				echo '</select>';
				break;
			case 'radio':
				foreach ( $options as $i => $opt ) {
					$rid = $id . '_' . $i;
					echo '<label class="bma-radio-label"><input type="radio" id="' . esc_attr( $rid ) . '" name="' . $name . '" value="' . esc_attr( $opt ) . '" ' . checked( $value, $opt, false ) . $req . '> ' . esc_html( $opt ) . '</label>';
				}
				break;
			case 'checkbox':
				echo '<label class="bma-checkbox-label"><input type="checkbox" id="' . $id . '" name="' . $name . '" value="1" ' . checked( $value, '1', false ) . '> ' . esc_html( $field->placeholder ?: $label ) . '</label>';
				break;
			case 'multiselect':
				$values = is_array( $value ) ? $value : array();
				foreach ( $options as $i => $opt ) {
					$cid = $id . '_' . $i;
					echo '<label class="bma-checkbox-label"><input type="checkbox" id="' . esc_attr( $cid ) . '" name="' . $name . '[]" value="' . esc_attr( $opt ) . '" ' . checked( in_array( $opt, $values, true ), true, false ) . '> ' . esc_html( $opt ) . '</label>';
				}
				break;
			case 'file':
				echo '<input type="file" id="' . $id . '" name="' . $name . '" class="bma-file-input" data-field-key="' . esc_attr( $field->field_key ) . '"' . $req . '>';
				break;
			case 'address':
				echo '<textarea id="' . $id . '" name="' . $name . '" placeholder="' . esc_attr__( 'Irányítószám, város, utca, házszám', 'bma-ajanlatkeres' ) . '"' . $req . '>' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'hidden':
				echo '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '">';
				break;
			default:
				echo '<input type="text" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" placeholder="' . $ph . '"' . $req . '>';
		}

		echo $desc;
		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Sanitize submitted field values.
	 *
	 * @param array $raw            Raw POST data.
	 * @param int   $service_id     Service ID.
	 * @param int[] $selected_items Selected items.
	 * @return array<string, mixed>
	 */
	public static function sanitize_submission( array $raw, int $service_id, array $selected_items ): array {
		$fields = self::get_fields_for_context( $service_id, $selected_items );
		$clean  = array();

		foreach ( $fields as $field ) {
			$key = $field->field_key;
			if ( ! isset( $raw[ $key ] ) ) {
				if ( $field->is_required && 'file' !== $field->field_type ) {
					continue;
				}
				continue;
			}

			$val = $raw[ $key ];

			switch ( $field->field_type ) {
				case 'email':
					$clean[ $key ] = sanitize_email( $val );
					break;
				case 'number':
					$clean[ $key ] = floatval( $val );
					break;
				case 'textarea':
				case 'address':
					$clean[ $key ] = sanitize_textarea_field( $val );
					break;
				case 'multiselect':
					$clean[ $key ] = array_map( 'sanitize_text_field', (array) $val );
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $val );
			}
		}

		return $clean;
	}
}
