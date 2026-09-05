<?php
/**
 * Service items (activities) and service links.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Service_Items {

	/**
	 * Get active items for a service (many-to-many links + legacy service_id).
	 *
	 * @param int $service_id Service post ID.
	 * @return array<int, object>
	 */
	public static function get_for_service( int $service_id ): array {
		global $wpdb;
		$items = $wpdb->prefix . 'bma_service_items';
		$links = $wpdb->prefix . 'bma_service_item_services';

		// Items are linked to the default-language service post.
		$lookup_id = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::source_post_id( $service_id ) : $service_id;
		if ( $lookup_id <= 0 ) {
			$lookup_id = $service_id;
		}

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT i.*, l.sort_order AS link_sort
				FROM {$items} i
				INNER JOIN {$links} l ON l.item_id = i.id
				WHERE i.is_active = 1 AND l.service_id = %d
				ORDER BY l.sort_order ASC, i.name ASC",
				$lookup_id
			)
		);

		if ( ! empty( $rows ) ) {
			return array_map( array( self::class, 'localize_item' ), $rows );
		}

		// Legacy fallback before link migration.
		$legacy = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *, sort_order AS link_sort FROM {$items}
				WHERE service_id = %d AND is_active = 1
				ORDER BY sort_order ASC, name ASC",
				$lookup_id
			)
		);

		return is_array( $legacy ) ? array_map( array( self::class, 'localize_item' ), $legacy ) : array();
	}

	/**
	 * Apply frontend language overlay to a service item row.
	 *
	 * @param object $row Item row.
	 */
	public static function localize_item( object $row ): object {
		$id = (int) ( $row->id ?? 0 );
		if ( ! $id || ! class_exists( 'BMA_AJ_I18n' ) ) {
			return $row;
		}
		$name = BMA_AJ_I18n::overlay( 'service_items', (string) $id, 'name' );
		if ( '' !== $name ) {
			$row->name = $name;
		}
		$short = BMA_AJ_I18n::overlay( 'service_items', (string) $id, 'short_description' );
		if ( '' !== $short ) {
			$row->short_description = $short;
		}
		$choose = BMA_AJ_I18n::overlay( 'service_items', (string) $id, 'choose_if' );
		if ( '' !== $choose ) {
			$row->choose_if = $choose;
		}
		return $row;
	}

	/**
	 * Get one item by ID.
	 *
	 * @param int $item_id Item ID.
	 */
	public static function get( int $item_id ): ?object {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}bma_service_items WHERE id = %d",
				$item_id
			)
		);
		return $row ? self::localize_item( $row ) : null;
	}

	/**
	 * Service IDs linked to an item.
	 *
	 * @param int $item_id Item ID.
	 * @return int[]
	 */
	public static function get_service_ids( int $item_id ): array {
		global $wpdb;
		$links = $wpdb->prefix . 'bma_service_item_services';
		$ids   = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT service_id FROM {$links} WHERE item_id = %d ORDER BY sort_order ASC, service_id ASC",
				$item_id
			)
		);

		if ( empty( $ids ) ) {
			$legacy = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT service_id FROM {$wpdb->prefix}bma_service_items WHERE id = %d",
					$item_id
				)
			);
			return $legacy ? array( $legacy ) : array();
		}

		return array_map( 'intval', $ids );
	}

	/**
	 * Replace service links for an item.
	 *
	 * @param int   $item_id     Item ID.
	 * @param int[] $service_ids Service post IDs.
	 */
	public static function set_service_ids( int $item_id, array $service_ids ): void {
		global $wpdb;
		$links = $wpdb->prefix . 'bma_service_item_services';
		$items = $wpdb->prefix . 'bma_service_items';

		$service_ids = array_values( array_unique( array_filter( array_map( 'absint', $service_ids ) ) ) );

		$wpdb->delete( $links, array( 'item_id' => $item_id ) );

		foreach ( $service_ids as $i => $service_id ) {
			$wpdb->insert(
				$links,
				array(
					'item_id'    => $item_id,
					'service_id' => $service_id,
					'sort_order' => $i,
				)
			);
		}

		$primary = $service_ids[0] ?? 0;
		$wpdb->update( $items, array( 'service_id' => $primary ), array( 'id' => $item_id ) );
	}

	/**
	 * Field IDs assigned to an item.
	 *
	 * @param int $item_id Item ID.
	 * @return int[]
	 */
	public static function get_field_ids( int $item_id ): array {
		global $wpdb;
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT field_id FROM {$wpdb->prefix}bma_field_assignments
				WHERE entity_type = 'service_item' AND entity_id = %d
				ORDER BY sort_order ASC, field_id ASC",
				$item_id
			)
		);
		return array_map( 'intval', $ids );
	}

	/**
	 * Replace field assignments for an item.
	 *
	 * @param int   $item_id   Item ID.
	 * @param int[] $field_ids Dynamic field IDs.
	 */
	public static function set_field_ids( int $item_id, array $field_ids ): void {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_field_assignments';

		$field_ids = array_values( array_unique( array_filter( array_map( 'absint', $field_ids ) ) ) );

		$wpdb->delete(
			$table,
			array(
				'entity_type' => 'service_item',
				'entity_id'   => $item_id,
			)
		);

		foreach ( $field_ids as $i => $field_id ) {
			$wpdb->insert(
				$table,
				array(
					'field_id'    => $field_id,
					'entity_type' => 'service_item',
					'entity_id'   => $item_id,
					'sort_order'  => $i,
				)
			);
		}
	}

	/**
	 * Create or update a service item.
	 *
	 * @param array<string, mixed> $data Item data.
	 * @return int|WP_Error Item ID.
	 */
	public static function save( array $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_service_items';

		$name = sanitize_text_field( (string) ( $data['name'] ?? '' ) );
		if ( '' === $name ) {
			return new WP_Error( 'name_required', __( 'A név kötelező.', 'bma-ajanlatkeres' ) );
		}

		$service_ids = array_map( 'absint', (array) ( $data['service_ids'] ?? array() ) );
		$service_ids = array_values( array_unique( array_filter( $service_ids ) ) );
		if ( empty( $service_ids ) ) {
			return new WP_Error( 'service_required', __( 'Legalább egy szolgáltatást ki kell választani.', 'bma-ajanlatkeres' ) );
		}

		$row = array(
			'service_id'        => $service_ids[0],
			'name'              => $name,
			'slug'              => sanitize_title( $name ),
			'short_description' => sanitize_textarea_field( (string) ( $data['short_description'] ?? '' ) ),
			'choose_if'         => sanitize_textarea_field( (string) ( $data['choose_if'] ?? '' ) ),
			'is_active'         => ! empty( $data['is_active'] ) ? 1 : 0,
			'is_required'       => ! empty( $data['is_required'] ) ? 1 : 0,
			'sort_order'        => absint( $data['sort_order'] ?? 0 ),
		);

		$item_id = absint( $data['id'] ?? 0 );
		if ( $item_id ) {
			$wpdb->update( $table, $row, array( 'id' => $item_id ) );
		} else {
			$row['is_active'] = 1;
			$wpdb->insert( $table, $row );
			$item_id = (int) $wpdb->insert_id;
			if ( ! $item_id ) {
				return new WP_Error( 'insert_failed', __( 'Nem sikerült létrehozni az elemet.', 'bma-ajanlatkeres' ) );
			}
		}

		self::set_service_ids( $item_id, $service_ids );
		self::set_field_ids( $item_id, array_map( 'absint', (array) ( $data['field_ids'] ?? array() ) ) );

		return $item_id;
	}

	/**
	 * Delete item and its links/assignments.
	 *
	 * @param int $item_id Item ID.
	 */
	public static function delete( int $item_id ): void {
		global $wpdb;
		if ( ! $item_id ) {
			return;
		}

		$wpdb->delete( $wpdb->prefix . 'bma_service_item_services', array( 'item_id' => $item_id ) );
		$wpdb->delete(
			$wpdb->prefix . 'bma_field_assignments',
			array(
				'entity_type' => 'service_item',
				'entity_id'   => $item_id,
			)
		);
		$wpdb->delete( $wpdb->prefix . 'bma_service_items', array( 'id' => $item_id ) );
	}

	/**
	 * All items with service names for admin list.
	 *
	 * @return array<int, object>
	 */
	public static function get_all_admin(): array {
		global $wpdb;
		$items = $wpdb->prefix . 'bma_service_items';
		$links = $wpdb->prefix . 'bma_service_item_services';
		$posts = $wpdb->posts;

		$rows = $wpdb->get_results(
			"SELECT i.*,
				GROUP_CONCAT(DISTINCT p.post_title ORDER BY p.post_title SEPARATOR ', ') AS service_names
			FROM {$items} i
			LEFT JOIN {$links} l ON l.item_id = i.id
			LEFT JOIN {$posts} p ON p.ID = COALESCE(l.service_id, i.service_id) AND p.post_type = 'bma_service'
			GROUP BY i.id
			ORDER BY i.name ASC"
		);

		return is_array( $rows ) ? $rows : array();
	}
}
