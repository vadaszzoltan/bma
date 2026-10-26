<?php
/**
 * Status management.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Statuses {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		// Admin handled in BMA_AJ_Admin.
	}

	/**
	 * Seed default statuses.
	 */
	public static function seed_defaults(): void {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_statuses';

		$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		if ( $count > 0 ) {
			return;
		}

		$defaults = array(
			array( 'name' => 'Beérkezett', 'slug' => 'beerkezett', 'color' => '#2271b1', 'sort_order' => 1, 'client_visible' => 1, 'send_email' => 1, 'email_template_slug' => 'status_change', 'is_default' => 1 ),
			array( 'name' => 'Feldolgozás alatt', 'slug' => 'feldolgozas_alatt', 'color' => '#996800', 'sort_order' => 2, 'client_visible' => 1, 'send_email' => 1, 'email_template_slug' => 'status_change', 'is_default' => 0 ),
			array( 'name' => 'További információ szükséges', 'slug' => 'tovabbi_info', 'color' => '#d63638', 'sort_order' => 3, 'client_visible' => 1, 'send_email' => 1, 'email_template_slug' => 'info_request', 'is_default' => 0 ),
			array( 'name' => 'Ajánlat készül', 'slug' => 'ajanlat_keszul', 'color' => '#7c3aed', 'sort_order' => 4, 'client_visible' => 1, 'send_email' => 0, 'email_template_slug' => '', 'is_default' => 0 ),
			array( 'name' => 'Ajánlat elkészült', 'slug' => 'ajanlat_elkeszult', 'color' => '#008a20', 'sort_order' => 5, 'client_visible' => 1, 'send_email' => 1, 'email_template_slug' => 'quote_uploaded', 'is_default' => 0 ),
			array( 'name' => 'Teljesítés alatt', 'slug' => 'teljesites_alatt', 'color' => '#007cba', 'sort_order' => 6, 'client_visible' => 1, 'send_email' => 0, 'email_template_slug' => '', 'is_default' => 0 ),
			array( 'name' => 'Dokumentumok feltöltve', 'slug' => 'dokumentumok_feltoltve', 'color' => '#00a32a', 'sort_order' => 7, 'client_visible' => 1, 'send_email' => 1, 'email_template_slug' => 'documents_ready', 'is_default' => 0 ),
			array( 'name' => 'Lezárva', 'slug' => 'lezarva', 'color' => '#50575e', 'sort_order' => 8, 'client_visible' => 1, 'send_email' => 0, 'email_template_slug' => '', 'is_default' => 0 ),
		);

		foreach ( $defaults as $status ) {
			$wpdb->insert( $table, $status );
		}
	}

	/**
	 * Get all statuses.
	 *
	 * @return array<int, object>
	 */
	public static function get_all(): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_statuses';

		return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY sort_order ASC" );
	}

	/**
	 * Get default status ID.
	 */
	public static function get_default_id(): int {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_statuses';

		$id = (int) $wpdb->get_var( "SELECT id FROM {$table} WHERE is_default = 1 LIMIT 1" );
		if ( ! $id ) {
			$id = (int) $wpdb->get_var( "SELECT id FROM {$table} ORDER BY sort_order ASC LIMIT 1" );
		}
		return $id;
	}

	/**
	 * Get status by ID.
	 *
	 * @param int $id Status ID.
	 */
	public static function get( int $id ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_statuses';

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ) );
		if ( ! $row ) {
			return null;
		}
		if ( class_exists( 'BMA_AJ_I18n' ) ) {
			$tr = BMA_AJ_I18n::overlay( 'statuses', (string) $id );
			if ( '' !== $tr ) {
				$row->name = $tr;
			}
		}
		return $row;
	}

	/**
	 * Change request status.
	 *
	 * @param int    $request_id Request ID.
	 * @param int    $new_status_id New status ID.
	 * @param string $note Optional note.
	 */
	public static function change_status( int $request_id, int $new_status_id, string $note = '' ): bool {
		global $wpdb;

		$requests_table = $wpdb->prefix . 'bma_quote_requests';
		$history_table  = $wpdb->prefix . 'bma_status_history';

		$request = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$requests_table} WHERE id = %d", $request_id ) );
		if ( ! $request ) {
			return false;
		}

		$old_status_id = (int) $request->status_id;
		if ( $old_status_id === $new_status_id ) {
			return true;
		}

		$wpdb->update(
			$requests_table,
			array( 'status_id' => $new_status_id ),
			array( 'id' => $request_id )
		);

		$wpdb->insert(
			$history_table,
			array(
				'request_id'    => $request_id,
				'old_status_id' => $old_status_id ?: null,
				'new_status_id' => $new_status_id,
				'changed_by'    => get_current_user_id() ?: null,
				'note'          => $note,
			)
		);

		$new_status = self::get( $new_status_id );
		if ( $new_status && $new_status->send_email && $new_status->email_template_slug ) {
			BMA_AJ_Email_Templates::send( $new_status->email_template_slug, $request_id );
		}

		return true;
	}

	/**
	 * Get status history for request.
	 *
	 * @param int $request_id Request ID.
	 */
	public static function get_history( int $request_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_status_history';

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT h.*, s.name as status_name, s.color as status_color
				FROM {$table} h
				LEFT JOIN {$wpdb->prefix}bma_statuses s ON s.id = h.new_status_id
				WHERE h.request_id = %d
				ORDER BY h.changed_at ASC",
				$request_id
			)
		);
	}

	/**
	 * Create a status.
	 *
	 * @param array<string, mixed> $data Status data.
	 */
	public static function create( array $data ): int|WP_Error {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_statuses';

		$name = sanitize_text_field( $data['name'] ?? '' );
		if ( '' === $name ) {
			return new WP_Error( 'bma_status_name', __( 'A státusz neve kötelező.', 'bma-ajanlatkeres' ) );
		}

		$slug = sanitize_title( $data['slug'] ?? $name );
		if ( '' === $slug ) {
			$slug = 'status-' . time();
		}

		$existing = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE slug = %s", $slug ) );
		if ( $existing ) {
			$slug .= '-' . wp_generate_password( 4, false );
		}

		$inserted = $wpdb->insert(
			$table,
			array(
				'name'                => $name,
				'slug'                => $slug,
				'color'               => sanitize_hex_color( $data['color'] ?? '#2271b1' ) ?: '#2271b1',
				'sort_order'          => absint( $data['sort_order'] ?? 99 ),
				'client_visible'      => ! empty( $data['client_visible'] ) ? 1 : 0,
				'send_email'          => ! empty( $data['send_email'] ) ? 1 : 0,
				'email_template_slug' => sanitize_text_field( $data['email_template_slug'] ?? '' ),
				'is_default'          => 0,
			)
		);

		if ( ! $inserted ) {
			return new WP_Error( 'bma_status_insert', __( 'A státusz mentése sikertelen.', 'bma-ajanlatkeres' ) );
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Delete status if unused (and not default).
	 *
	 * @param int $id Status ID.
	 * @return true|WP_Error
	 */
	public static function delete( int $id ): bool|WP_Error {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_statuses';
		$row   = self::get( $id );

		if ( ! $row ) {
			return new WP_Error( 'bma_status_missing', __( 'A státusz nem található.', 'bma-ajanlatkeres' ) );
		}
		if ( ! empty( $row->is_default ) ) {
			return new WP_Error( 'bma_status_default', __( 'Az alapértelmezett státusz nem törölhető.', 'bma-ajanlatkeres' ) );
		}

		$in_use = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->prefix}bma_quote_requests WHERE status_id = %d",
				$id
			)
		);
		if ( $in_use > 0 ) {
			return new WP_Error( 'bma_status_in_use', __( 'A státusz használatban van, ezért nem törölhető.', 'bma-ajanlatkeres' ) );
		}

		$wpdb->delete( $table, array( 'id' => $id ) );
		return true;
	}
}
