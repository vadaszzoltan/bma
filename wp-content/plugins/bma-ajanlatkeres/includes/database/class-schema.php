<?php
/**
 * Database schema.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Schema {

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
				is_active tinyint(1) NOT NULL DEFAULT 1,
				is_required tinyint(1) NOT NULL DEFAULT 0,
				sort_order int NOT NULL DEFAULT 0,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
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
				KEY request_id (request_id)
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
	}
}
