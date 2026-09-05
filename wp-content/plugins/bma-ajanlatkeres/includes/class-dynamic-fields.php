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
	 * Get fields for service and selected items (deduplicated by field_key).
	 *
	 * Service-level assignments still apply for backward compatibility.
	 * Item-level assignments are the preferred configuration path.
	 * If the same field is required in any assignment, it stays required.
	 *
	 * @param int   $service_id     Service post ID.
	 * @param int[] $selected_items Selected item IDs.
	 * @return array<int, object>
	 */
	public static function get_fields_for_context( int $service_id, array $selected_items = array() ): array {
		global $wpdb;
		$table  = $wpdb->prefix . 'bma_dynamic_fields';
		$assign = $wpdb->prefix . 'bma_field_assignments';

		$conditions = array( "( a.entity_type = 'service' AND a.entity_id = %d )" );
		$values     = array( $service_id );

		$selected_items = array_filter( array_map( 'absint', $selected_items ) );
		if ( ! empty( $selected_items ) ) {
			$item_placeholders = implode( ',', array_fill( 0, count( $selected_items ), '%d' ) );
			$conditions[]      = "( a.entity_type = 'service_item' AND a.entity_id IN ({$item_placeholders}) )";
			$values            = array_merge( $values, $selected_items );
		}

		$where = implode( ' OR ', $conditions );

		$sql = $wpdb->prepare(
			"SELECT f.*, a.entity_type, a.entity_id, a.sort_order AS assign_order
			FROM {$table} f
			INNER JOIN {$assign} a ON a.field_id = f.id
			WHERE f.is_active = 1
			AND ( {$where} )
			ORDER BY a.sort_order ASC, f.sort_order ASC",
			$values
		);

		$results = $wpdb->get_results( $sql );
		if ( ! is_array( $results ) ) {
			return array();
		}

		return self::dedupe_fields( $results );
	}

	/**
	 * Deduplicate fields by field_key; merge required flag.
	 *
	 * @param array<int, object> $fields Field rows.
	 * @return array<int, object>
	 */
	public static function dedupe_fields( array $fields ): array {
		$seen   = array();
		$merged = array();

		foreach ( $fields as $field ) {
			$key = (string) $field->field_key;
			if ( ! isset( $seen[ $key ] ) ) {
				$seen[ $key ] = count( $merged );
				$merged[]     = $field;
				continue;
			}
			$idx = $seen[ $key ];
			if ( ! empty( $field->is_required ) ) {
				$merged[ $idx ]->is_required = 1;
			}
		}

		return $merged;
	}

	/**
	 * All active field definitions (field library).
	 *
	 * @return array<int, object>
	 */
	public static function get_all_active(): array {
		global $wpdb;
		$rows = $wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}bma_dynamic_fields WHERE is_active = 1 ORDER BY sort_order ASC, admin_label ASC"
		);
		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * Create a field definition (no assignment).
	 *
	 * @param array<string, mixed> $data Field data.
	 * @return int|WP_Error
	 */
	public static function create_field( array $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_dynamic_fields';

		$key = sanitize_key( (string) ( $data['field_key'] ?? '' ) );
		if ( '' === $key ) {
			return new WP_Error( 'key_required', __( 'A mező kulcs kötelező.', 'bma-ajanlatkeres' ) );
		}

		$exists = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT id FROM {$table} WHERE field_key = %s LIMIT 1", $key )
		);
		if ( $exists ) {
			return new WP_Error( 'key_exists', __( 'Ez a mező kulcs már létezik.', 'bma-ajanlatkeres' ) );
		}

		$type = sanitize_text_field( (string) ( $data['field_type'] ?? 'text' ) );
		if ( ! in_array( $type, self::FIELD_TYPES, true ) ) {
			$type = 'text';
		}

		$wpdb->insert(
			$table,
			array(
				'field_key'    => $key,
				'field_type'   => $type,
				'admin_label'  => sanitize_text_field( (string) ( $data['admin_label'] ?? $key ) ),
				'client_label' => sanitize_text_field( (string) ( $data['client_label'] ?? $key ) ),
				'description'  => sanitize_textarea_field( (string) ( $data['description'] ?? '' ) ),
				'placeholder'  => sanitize_text_field( (string) ( $data['placeholder'] ?? '' ) ),
				'is_required'  => ! empty( $data['is_required'] ) ? 1 : 0,
				'options'      => wp_json_encode( array_values( array_filter( array_map( 'trim', (array) ( $data['options'] ?? array() ) ) ) ) ),
				'sort_order'   => absint( $data['sort_order'] ?? 0 ),
				'is_active'    => 1,
			)
		);

		$id = (int) $wpdb->insert_id;
		return $id ?: new WP_Error( 'insert_failed', __( 'Nem sikerült létrehozni a mezőt.', 'bma-ajanlatkeres' ) );
	}

	/**
	 * Sanitize unified cart/checkout field submission.
	 *
	 * @param array        $raw    Raw POST field_values.
	 * @param array        $fields Field objects from get_merged_fields().
	 * @return array<string, mixed>
	 */
	public static function sanitize_cart_submission( array $raw, array $fields ): array {
		$clean = array();
		foreach ( $fields as $field ) {
			$key = $field->field_key;
			if ( ! isset( $raw[ $key ] ) ) {
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

	/**
	 * Get human-readable label for field key.
	 *
	 * @param string $field_key Field key.
	 */
	public static function get_label_for_key( string $field_key ): string {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_dynamic_fields';
		$label = $wpdb->get_var(
			$wpdb->prepare( "SELECT client_label FROM {$table} WHERE field_key = %s LIMIT 1", $field_key )
		);
		return $label ?: $field_key;
	}

	/**
	 * Get service items for a service.
	 *
	 * @param int $service_id Service ID.
	 */
	public static function get_service_items( int $service_id ): array {
		return BMA_AJ_Service_Items::get_for_service( $service_id );
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
		if ( class_exists( 'BMA_AJ_I18n' ) ) {
			$tr = BMA_AJ_I18n::overlay( 'dynamic_fields', (string) (int) $field->id, 'client_label' );
			if ( '' !== $tr ) {
				$label = esc_html( $tr );
			}
			$desc = BMA_AJ_I18n::overlay( 'dynamic_fields', (string) (int) $field->id, 'description' );
			if ( '' !== $desc ) {
				$field->description = $desc;
			}
			$ph = BMA_AJ_I18n::overlay( 'dynamic_fields', (string) (int) $field->id, 'placeholder' );
			if ( '' !== $ph ) {
				$field->placeholder = $ph;
			}
		}
		$req   = $field->is_required ? ' required' : '';
		$desc  = $field->description ? '<p class="bma-field-desc">' . esc_html( $field->description ) . '</p>' : '';
		$ph    = esc_attr( $field->placeholder );

		$options = json_decode( $field->options ?? '[]', true ) ?: array();
		if ( class_exists( 'BMA_AJ_I18n' ) ) {
			$lang    = BMA_AJ_I18n::current_lang();
			$overlay = get_option( 'bma_i18n_overlay', array() );
			$opts_tr = $overlay['dynamic_fields'][ (string) (int) $field->id ][ $lang ]['options'] ?? null;
			if ( is_array( $opts_tr ) && ! empty( $opts_tr ) ) {
				$options = $opts_tr;
			}
		}

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

	/**
	 * Render all fields HTML for a service context.
	 *
	 * @param int   $service_id     Service post ID.
	 * @param int[] $selected_items Selected item IDs.
	 */
	public static function render_fields_html( int $service_id, array $selected_items = array() ): string {
		$fields = self::get_fields_for_context( $service_id, $selected_items );
		$html   = '';

		foreach ( $fields as $field ) {
			$html .= self::render_field( $field );
		}

		return $html;
	}
}
