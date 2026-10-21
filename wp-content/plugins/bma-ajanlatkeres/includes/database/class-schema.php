<?php
/**
 * Database schema.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Schema {

	/**
	 * Bump when maybe_upgrade() gains a step; skips the column checks on every other request.
	 */
	public const DB_VERSION = '2';

	/**
	 * Create custom database tables.
	 */
	public static function create_tables(): void {
		global $wpdb;

		$charset = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$tables = array(
			"CREATE TABLE {$wpdb->prefix}bma_service_items (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				service_id bigint(20) unsigned NOT NULL,
				name varchar(255) NOT NULL,
				slug varchar(255) NOT NULL,
				short_description text,
				choose_if text,
				item_type varchar(20) NOT NULL DEFAULT 'item',
				auto_rule text,
				is_active tinyint(1) NOT NULL DEFAULT 1,
				is_required tinyint(1) NOT NULL DEFAULT 0,
				sort_order int NOT NULL DEFAULT 0,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY service_id (service_id)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_service_item_services (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				item_id bigint(20) unsigned NOT NULL,
				service_id bigint(20) unsigned NOT NULL,
				sort_order int NOT NULL DEFAULT 0,
				PRIMARY KEY (id),
				UNIQUE KEY item_service (item_id, service_id),
				KEY service_id (service_id)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_dynamic_fields (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				field_key varchar(100) NOT NULL,
				field_type varchar(50) NOT NULL,
				admin_label varchar(255) NOT NULL,
				client_label varchar(255) NOT NULL,
				description text,
				placeholder varchar(255) DEFAULT '',
				is_required tinyint(1) NOT NULL DEFAULT 0,
				validation_rules longtext,
				options longtext,
				placement varchar(20) NOT NULL DEFAULT 'item',
				conditions text,
				sort_order int NOT NULL DEFAULT 0,
				is_active tinyint(1) NOT NULL DEFAULT 1,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY field_key (field_key)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_field_assignments (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				field_id bigint(20) unsigned NOT NULL,
				entity_type varchar(50) NOT NULL,
				entity_id bigint(20) unsigned NOT NULL,
				sort_order int NOT NULL DEFAULT 0,
				PRIMARY KEY (id),
				KEY field_id (field_id),
				KEY entity (entity_type, entity_id)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_statuses (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				slug varchar(100) NOT NULL,
				color varchar(20) DEFAULT '#666666',
				sort_order int NOT NULL DEFAULT 0,
				client_visible tinyint(1) NOT NULL DEFAULT 1,
				send_email tinyint(1) NOT NULL DEFAULT 0,
				email_template_slug varchar(100) DEFAULT '',
				is_default tinyint(1) NOT NULL DEFAULT 0,
				PRIMARY KEY (id),
				UNIQUE KEY slug (slug)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_quote_requests (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				request_number varchar(50) NOT NULL,
				user_id bigint(20) unsigned DEFAULT NULL,
				client_name varchar(255) NOT NULL,
				client_email varchar(255) NOT NULL,
				client_phone varchar(50) NOT NULL,
				company_name varchar(255) DEFAULT '',
				tax_number varchar(50) DEFAULT '',
				billing_address text,
				client_note text,
				shared_field_values longtext,
				flow_version tinyint unsigned NOT NULL DEFAULT 1,
				client_type varchar(30) NOT NULL DEFAULT '',
				design_phase varchar(30) NOT NULL DEFAULT '',
				addons longtext,
				status_id bigint(20) unsigned NOT NULL,
				admin_note text,
				privacy_accepted tinyint(1) NOT NULL DEFAULT 0,
				submitted_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY request_number (request_number),
				KEY user_id (user_id),
				KEY status_id (status_id)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_quote_request_services (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				request_id bigint(20) unsigned NOT NULL,
				service_id bigint(20) unsigned NOT NULL,
				service_name varchar(255) NOT NULL,
				selected_items longtext,
				item_phases longtext,
				field_values longtext,
				sort_order int NOT NULL DEFAULT 0,
				PRIMARY KEY (id),
				KEY request_id (request_id)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_status_history (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				request_id bigint(20) unsigned NOT NULL,
				old_status_id bigint(20) unsigned DEFAULT NULL,
				new_status_id bigint(20) unsigned NOT NULL,
				changed_by bigint(20) unsigned DEFAULT NULL,
				note text,
				changed_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY request_id (request_id)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_quotes (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				request_id bigint(20) unsigned NOT NULL,
				quote_number varchar(50) NOT NULL,
				net_amount decimal(12,2) NOT NULL DEFAULT 0,
				tax_amount decimal(12,2) NOT NULL DEFAULT 0,
				gross_amount decimal(12,2) NOT NULL DEFAULT 0,
				currency varchar(10) NOT NULL DEFAULT 'HUF',
				valid_until date DEFAULT NULL,
				note text,
				status varchar(50) NOT NULL DEFAULT 'draft',
				file_path varchar(500) DEFAULT '',
				created_by bigint(20) unsigned DEFAULT NULL,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				sent_at datetime DEFAULT NULL,
				PRIMARY KEY (id),
				UNIQUE KEY quote_number (quote_number),
				KEY request_id (request_id)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_documents (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				request_id bigint(20) unsigned NOT NULL,
				service_item_id bigint(20) unsigned NOT NULL DEFAULT 0,
				title varchar(255) NOT NULL,
				doc_type varchar(50) NOT NULL DEFAULT 'other',
				file_path varchar(500) NOT NULL,
				file_name varchar(255) NOT NULL,
				mime_type varchar(100) DEFAULT '',
				file_size bigint(20) unsigned DEFAULT 0,
				uploaded_by bigint(20) unsigned DEFAULT NULL,
				uploader_type varchar(20) NOT NULL DEFAULT 'client',
				client_visible tinyint(1) NOT NULL DEFAULT 1,
				uploaded_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY request_id (request_id),
				KEY service_item_id (service_item_id)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_email_templates (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				slug varchar(100) NOT NULL,
				name varchar(255) NOT NULL,
				subject varchar(255) NOT NULL,
				body longtext NOT NULL,
				is_active tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (id),
				UNIQUE KEY slug (slug)
			) $charset;",

			"CREATE TABLE {$wpdb->prefix}bma_security_log (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				event_type varchar(50) NOT NULL,
				user_id bigint(20) unsigned DEFAULT NULL,
				ip_address varchar(45) DEFAULT '',
				message text,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY event_type (event_type)
			) $charset;",
		);

		foreach ( $tables as $sql ) {
			dbDelta( $sql );
		}

		delete_option( 'bma_aj_db_version' );
		self::maybe_upgrade();
	}

	/**
	 * Add columns introduced after initial release.
	 */
	public static function maybe_upgrade(): void {
		if ( get_option( 'bma_aj_db_version' ) === self::DB_VERSION ) {
			return;
		}

		global $wpdb;

		$requests = $wpdb->prefix . 'bma_quote_requests';
		$quotes   = $wpdb->prefix . 'bma_quotes';
		$items    = $wpdb->prefix . 'bma_service_items';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$req_cols = wp_list_pluck( $wpdb->get_results( "SHOW COLUMNS FROM {$requests}" ), 'Field' );
		if ( ! in_array( 'shared_field_values', $req_cols, true ) ) {
			$wpdb->query( "ALTER TABLE {$requests} ADD COLUMN shared_field_values longtext NULL AFTER client_note" );
		}

		$req_additions = array(
			'flow_version' => 'tinyint unsigned NOT NULL DEFAULT 1',
			'client_type'  => "varchar(30) NOT NULL DEFAULT ''",
			'design_phase' => "varchar(30) NOT NULL DEFAULT ''",
			'addons'       => 'longtext NULL',
		);
		foreach ( $req_additions as $col => $def ) {
			if ( ! in_array( $col, $req_cols, true ) ) {
				$wpdb->query( "ALTER TABLE {$requests} ADD COLUMN {$col} {$def}" );
			}
		}

		$req_services = $wpdb->prefix . 'bma_quote_request_services';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$rs_cols = wp_list_pluck( $wpdb->get_results( "SHOW COLUMNS FROM {$req_services}" ), 'Field' );
		if ( $rs_cols && ! in_array( 'item_phases', $rs_cols, true ) ) {
			$wpdb->query( "ALTER TABLE {$req_services} ADD COLUMN item_phases longtext NULL AFTER selected_items" );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$quote_cols = wp_list_pluck( $wpdb->get_results( "SHOW COLUMNS FROM {$quotes}" ), 'Field' );
		$additions  = array(
			'payment_status'    => "varchar(50) NOT NULL DEFAULT 'unpaid'",
			'payment_method'    => "varchar(50) NOT NULL DEFAULT ''",
			'stripe_session_id' => "varchar(255) NOT NULL DEFAULT ''",
			'paid_at'           => 'datetime DEFAULT NULL',
		);
		foreach ( $additions as $col => $def ) {
			if ( ! in_array( $col, $quote_cols, true ) ) {
				$wpdb->query( "ALTER TABLE {$quotes} ADD COLUMN {$col} {$def}" );
			}
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$item_cols = wp_list_pluck( $wpdb->get_results( "SHOW COLUMNS FROM {$items}" ), 'Field' );
		if ( ! in_array( 'choose_if', $item_cols, true ) ) {
			$wpdb->query( "ALTER TABLE {$items} ADD COLUMN choose_if text NULL AFTER short_description" );
		}
		if ( $item_cols && ! in_array( 'item_type', $item_cols, true ) ) {
			$wpdb->query( "ALTER TABLE {$items} ADD COLUMN item_type varchar(20) NOT NULL DEFAULT 'item' AFTER choose_if" );
		}
		if ( $item_cols && ! in_array( 'auto_rule', $item_cols, true ) ) {
			$wpdb->query( "ALTER TABLE {$items} ADD COLUMN auto_rule text NULL AFTER item_type" );
		}

		$fields = $wpdb->prefix . 'bma_dynamic_fields';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$field_cols = wp_list_pluck( $wpdb->get_results( "SHOW COLUMNS FROM {$fields}" ), 'Field' );
		if ( $field_cols && ! in_array( 'placement', $field_cols, true ) ) {
			$wpdb->query( "ALTER TABLE {$fields} ADD COLUMN placement varchar(20) NOT NULL DEFAULT 'item' AFTER options" );
		}
		if ( $field_cols && ! in_array( 'conditions', $field_cols, true ) ) {
			$wpdb->query( "ALTER TABLE {$fields} ADD COLUMN conditions text NULL AFTER placement" );
		}

		self::ensure_item_service_links();
		self::ensure_document_item_column();

		update_option( 'bma_aj_db_version', self::DB_VERSION, true );
	}

	/**
	 * Add service_item_id to documents for per-item deliverables.
	 */
	private static function ensure_document_item_column(): void {
		global $wpdb;
		$docs = $wpdb->prefix . 'bma_documents';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$cols = wp_list_pluck( $wpdb->get_results( "SHOW COLUMNS FROM {$docs}" ), 'Field' );
		if ( ! in_array( 'service_item_id', $cols, true ) ) {
			$wpdb->query( "ALTER TABLE {$docs} ADD COLUMN service_item_id bigint(20) unsigned NOT NULL DEFAULT 0 AFTER request_id" );
			$wpdb->query( "ALTER TABLE {$docs} ADD KEY service_item_id (service_item_id)" );
		}
	}

	/**
	 * Ensure many-to-many service↔item links exist and migrate legacy service_id rows.
	 */
	private static function ensure_item_service_links(): void {
		global $wpdb;

		$links = $wpdb->prefix . 'bma_service_item_services';
		$items = $wpdb->prefix . 'bma_service_items';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $links ) );
		if ( $exists !== $links ) {
			$charset = $wpdb->get_charset_collate();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta(
				"CREATE TABLE {$links} (
					id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					item_id bigint(20) unsigned NOT NULL,
					service_id bigint(20) unsigned NOT NULL,
					sort_order int NOT NULL DEFAULT 0,
					PRIMARY KEY (id),
					UNIQUE KEY item_service (item_id, service_id),
					KEY service_id (service_id)
				) {$charset};"
			);
		}

		// Migrate legacy single service_id into junction where missing.
		$wpdb->query(
			"INSERT IGNORE INTO {$links} (item_id, service_id, sort_order)
			SELECT i.id, i.service_id, 0
			FROM {$items} i
			WHERE i.service_id > 0
			AND NOT EXISTS (
				SELECT 1 FROM {$links} l WHERE l.item_id = i.id AND l.service_id = i.service_id
			)"
		);

		self::maybe_copy_service_fields_to_items();
	}

	/**
	 * One-time: copy legacy service-level field assignments onto that service's items.
	 */
	private static function maybe_copy_service_fields_to_items(): void {
		if ( get_option( 'bma_aj_fields_migrated_to_items' ) ) {
			return;
		}

		global $wpdb;
		$assign = $wpdb->prefix . 'bma_field_assignments';
		$links  = $wpdb->prefix . 'bma_service_item_services';

		$service_assigns = $wpdb->get_results(
			"SELECT field_id, entity_id AS service_id, sort_order
			FROM {$assign}
			WHERE entity_type = 'service'"
		);

		if ( is_array( $service_assigns ) ) {
			foreach ( $service_assigns as $row ) {
				$item_ids = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT item_id FROM {$links} WHERE service_id = %d",
						(int) $row->service_id
					)
				);
				foreach ( $item_ids as $item_id ) {
					$exists = (int) $wpdb->get_var(
						$wpdb->prepare(
							"SELECT id FROM {$assign}
							WHERE field_id = %d AND entity_type = 'service_item' AND entity_id = %d
							LIMIT 1",
							(int) $row->field_id,
							(int) $item_id
						)
					);
					if ( $exists ) {
						continue;
					}
					$wpdb->insert(
						$assign,
						array(
							'field_id'    => (int) $row->field_id,
							'entity_type' => 'service_item',
							'entity_id'   => (int) $item_id,
							'sort_order'  => (int) $row->sort_order,
						)
					);
				}
			}
		}

		update_option( 'bma_aj_fields_migrated_to_items', '1', false );
	}
}
