<?php
/**
 * Secure document management.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Documents {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'init', array( self::class, 'register_download_endpoint' ) );
		add_action( 'template_redirect', array( self::class, 'handle_download' ) );
		add_action( 'wp_ajax_bma_upload_document', array( self::class, 'ajax_upload' ) );
		add_action( 'wp_ajax_nopriv_bma_upload_document', array( self::class, 'ajax_upload' ) );
	}

	/**
	 * Register rewrite for secure downloads.
	 */
	public static function register_download_endpoint(): void {
		add_rewrite_rule( '^bma-download/([0-9]+)/?$', 'index.php?bma_doc_id=$matches[1]', 'top' );
		add_rewrite_tag( '%bma_doc_id%', '([0-9]+)' );
	}

	/**
	 * Get temp upload directory for cart session files.
	 */
	public static function get_temp_dir(): string {
		$cart_id = BMA_AJ_Cart::get_storage_id();
		if ( ! $cart_id ) {
			$cart_id = 'guest_' . wp_generate_password( 12, false );
		}

		$upload = wp_upload_dir();
		if ( ! empty( $upload['error'] ) ) {
			return '';
		}

		$base = $upload['basedir'] . '/bma-documents';
		$dir  = $base . '/temp/' . sanitize_file_name( $cart_id );

		if ( ! file_exists( $dir ) ) {
			if ( ! wp_mkdir_p( $dir ) ) {
				return '';
			}
			file_put_contents( $dir . '/.htaccess', "Order deny,allow\nDeny from all\n" );
			file_put_contents( $dir . '/index.php', '<?php // Silence is golden.' );
		}

		return $dir;
	}

	/**
	 * Extract nested $_FILES entry.
	 *
	 * @param string $parent Parent key (e.g. field_values).
	 * @param string $key    Field key.
	 * @return array<string, mixed>|null
	 */
	public static function extract_uploaded_file( string $parent, string $key ): ?array {
		if ( empty( $_FILES[ $parent ]['tmp_name'][ $key ] ) ) {
			return null;
		}

		return array(
			'name'     => $_FILES[ $parent ]['name'][ $key ],
			'type'     => $_FILES[ $parent ]['type'][ $key ],
			'tmp_name' => $_FILES[ $parent ]['tmp_name'][ $key ],
			'error'    => $_FILES[ $parent ]['error'][ $key ],
			'size'     => $_FILES[ $parent ]['size'][ $key ],
		);
	}

	/**
	 * Upload file to temp storage during cart flow.
	 *
	 * @param array  $file      $_FILES entry.
	 * @param string $field_key Field key.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function handle_temp_upload( array $file, string $field_key ) {
		if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'no_file', __( 'Nincs fájl.', 'bma-ajanlatkeres' ) );
		}

		if ( UPLOAD_ERR_OK !== (int) $file['error'] ) {
			return new WP_Error( 'upload_error', __( 'Fájl feltöltési hiba.', 'bma-ajanlatkeres' ) );
		}

		$allowed = array( 'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'dwg', 'zip' );
		$ext     = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

		if ( ! in_array( $ext, $allowed, true ) ) {
			return new WP_Error( 'invalid_type', __( 'Nem engedélyezett fájltípus.', 'bma-ajanlatkeres' ) );
		}

		$max_size = 20 * 1024 * 1024;
		if ( $file['size'] > $max_size ) {
			return new WP_Error( 'too_large', __( 'A fájl túl nagy (max. 20 MB).', 'bma-ajanlatkeres' ) );
		}

		$dir       = self::get_temp_dir();
		if ( ! $dir || ! is_writable( $dir ) ) {
			return new WP_Error( 'upload_dir', __( 'A feltöltési könyvtár nem írható. Kérjük, jelezze az üzemeltetőnek.', 'bma-ajanlatkeres' ) );
		}
		$safe_name = sanitize_file_name( wp_unique_filename( $dir, $file['name'] ) );
		$dest      = $dir . '/' . $safe_name;

		if ( ! move_uploaded_file( $file['tmp_name'], $dest ) ) {
			return new WP_Error( 'upload_failed', __( 'Feltöltés sikertelen.', 'bma-ajanlatkeres' ) );
		}

		return array(
			'field_key'     => sanitize_key( $field_key ),
			'file_name'     => $safe_name,
			'temp_path'     => $dest,
			'original_name' => sanitize_file_name( $file['name'] ),
			'mime_type'     => sanitize_mime_type( $file['type'] ),
			'file_size'     => absint( $file['size'] ),
		);
	}

	/**
	 * Promote temp cart file to permanent request document.
	 *
	 * @param array<string, mixed> $temp_doc   Temp document metadata.
	 * @param int                  $request_id Request ID.
	 * @param string               $doc_type   Document type.
	 */
	public static function promote_temp_to_request( array $temp_doc, int $request_id, string $doc_type = 'client_upload' ): ?int {
		if ( empty( $temp_doc['temp_path'] ) || ! file_exists( $temp_doc['temp_path'] ) ) {
			return null;
		}

		$dir       = self::get_upload_dir();
		$safe_name = sanitize_file_name( wp_unique_filename( $dir, $temp_doc['file_name'] ?? 'document' ) );
		$dest      = $dir . '/' . $safe_name;

		if ( ! rename( $temp_doc['temp_path'], $dest ) ) {
			if ( ! copy( $temp_doc['temp_path'], $dest ) ) {
				return null;
			}
			wp_delete_file( $temp_doc['temp_path'] );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'bma_documents';

		$wpdb->insert(
			$table,
			array(
				'request_id'     => $request_id,
				'title'          => sanitize_text_field( $temp_doc['original_name'] ?? $safe_name ),
				'doc_type'       => sanitize_text_field( $doc_type ),
				'file_path'      => $dest,
				'file_name'      => $safe_name,
				'mime_type'      => sanitize_mime_type( $temp_doc['mime_type'] ?? '' ),
				'file_size'      => absint( $temp_doc['file_size'] ?? 0 ),
				'uploaded_by'    => get_current_user_id() ?: null,
				'uploader_type'  => 'client',
				'client_visible' => 1,
			)
		);

		return (int) $wpdb->insert_id;
	}

	/**
	 * Get upload directory (protected).
	 */
	public static function get_upload_dir(): string {
		$upload = wp_upload_dir();
		$dir    = $upload['basedir'] . '/bma-documents';

		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
			file_put_contents( $dir . '/.htaccess', "Order deny,allow\nDeny from all\n" );
			file_put_contents( $dir . '/index.php', '<?php // Silence is golden.' );
		}

		return $dir;
	}

	/**
	 * Handle file upload.
	 *
	 * @param array  $file            $_FILES entry.
	 * @param int    $request_id      Request ID.
	 * @param string $doc_type        Document type.
	 * @param string $uploader        client|admin.
	 * @param int    $service_item_id Related service item (0 = general).
	 * @return array|WP_Error
	 */
	public static function handle_upload( array $file, int $request_id, string $doc_type = 'other', string $uploader = 'client', int $service_item_id = 0 ) {
		if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'no_file', __( 'Nincs fájl.', 'bma-ajanlatkeres' ) );
		}

		$allowed = array( 'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'dwg', 'zip' );
		$ext     = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

		if ( ! in_array( $ext, $allowed, true ) ) {
			return new WP_Error( 'invalid_type', __( 'Nem engedélyezett fájltípus.', 'bma-ajanlatkeres' ) );
		}

		$max_size = 20 * 1024 * 1024; // 20 MB.
		if ( $file['size'] > $max_size ) {
			return new WP_Error( 'too_large', __( 'A fájl túl nagy (max. 20 MB).', 'bma-ajanlatkeres' ) );
		}

		$dir       = self::get_upload_dir();
		$safe_name = sanitize_file_name( wp_unique_filename( $dir, $file['name'] ) );
		$dest      = $dir . '/' . $safe_name;

		if ( ! move_uploaded_file( $file['tmp_name'], $dest ) ) {
			return new WP_Error( 'upload_failed', __( 'Feltöltés sikertelen.', 'bma-ajanlatkeres' ) );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'bma_documents';

		$row = array(
			'request_id'     => $request_id,
			'title'          => sanitize_text_field( $file['name'] ),
			'doc_type'       => sanitize_text_field( $doc_type ),
			'file_path'      => $dest,
			'file_name'      => $safe_name,
			'mime_type'      => sanitize_mime_type( $file['type'] ),
			'file_size'      => absint( $file['size'] ),
			'uploaded_by'    => get_current_user_id() ?: null,
			'uploader_type'  => $uploader,
			'client_visible' => 1,
		);

		if ( self::has_service_item_column() ) {
			$row['service_item_id'] = absint( $service_item_id );
		}

		$wpdb->insert( $table, $row );

		$doc_id = (int) $wpdb->insert_id;

		if ( 'admin' === $uploader && 'quote' !== $doc_type ) {
			BMA_AJ_Email_Templates::send( 'document_uploaded', $request_id );
		}

		return array(
			'id'   => $doc_id,
			'path' => $dest,
			'name' => $safe_name,
		);
	}

	/**
	 * Whether service_item_id column exists.
	 */
	private static function has_service_item_column(): bool {
		global $wpdb;
		static $has = null;
		if ( null !== $has ) {
			return $has;
		}
		$table = $wpdb->prefix . 'bma_documents';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$cols = wp_list_pluck( $wpdb->get_results( "SHOW COLUMNS FROM {$table}" ), 'Field' );
		$has  = in_array( 'service_item_id', $cols, true );
		return $has;
	}

	/**
	 * Get documents for request.
	 *
	 * @param int    $request_id  Request ID.
	 * @param bool   $client_only Only client-visible.
	 * @param string $group       all|client|deliverable.
	 * @return array<int, object>
	 */
	public static function get_for_request( int $request_id, bool $client_only = false, string $group = 'all' ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_documents';

		$sql    = "SELECT * FROM {$table} WHERE request_id = %d";
		$params = array( $request_id );

		if ( $client_only ) {
			$sql .= ' AND client_visible = 1';
		}

		if ( 'client' === $group ) {
			$sql .= " AND uploader_type = 'client'";
		} elseif ( 'deliverable' === $group ) {
			$sql .= " AND uploader_type = 'admin' AND doc_type <> 'quote'";
		}

		$sql .= ' ORDER BY uploaded_at DESC';

		return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
	}

	/**
	 * Group deliverables by ordered service item.
	 *
	 * @param array<int, object> $ordered_items From get_ordered_items().
	 * @param array<int, object> $deliverables  Deliverable docs.
	 * @return array<int, array{item:object,docs:array<int,object>}>
	 */
	public static function group_by_ordered_item( array $ordered_items, array $deliverables ): array {
		$by_item = array();
		$general = array();

		foreach ( $deliverables as $doc ) {
			$iid = (int) ( $doc->service_item_id ?? 0 );
			if ( $iid > 0 ) {
				$by_item[ $iid ][] = $doc;
			} else {
				$general[] = $doc;
			}
		}

		$groups = array();
		foreach ( $ordered_items as $item ) {
			$iid      = (int) $item->id;
			$groups[] = array(
				'item' => $item,
				'docs' => $by_item[ $iid ] ?? array(),
			);
			unset( $by_item[ $iid ] );
		}

		// Orphan docs linked to items no longer on the request.
		foreach ( $by_item as $iid => $docs ) {
			$groups[] = array(
				'item' => (object) array(
					'id'           => $iid,
					'name'         => sprintf( /* translators: %d item id */ __( 'Tétel #%d', 'bma-ajanlatkeres' ), $iid ),
					'service_name' => '',
				),
				'docs' => $docs,
			);
		}

		if ( ! empty( $general ) ) {
			$groups[] = array(
				'item' => (object) array(
					'id'           => 0,
					'name'         => __( 'Általános dokumentumok', 'bma-ajanlatkeres' ),
					'service_name' => '',
				),
				'docs' => $general,
			);
		}

		return $groups;
	}

	/**
	 * Human label for document type.
	 *
	 * @param string $doc_type Type slug.
	 */
	public static function type_label( string $doc_type ): string {
		$labels = array(
			'technical'     => __( 'Műszaki dokumentum', 'bma-ajanlatkeres' ),
			'plan'          => __( 'Terv', 'bma-ajanlatkeres' ),
			'handover'      => __( 'Átadási dokumentum', 'bma-ajanlatkeres' ),
			'quote'         => __( 'Ajánlat', 'bma-ajanlatkeres' ),
			'client_upload' => __( 'Ügyfél feltöltés', 'bma-ajanlatkeres' ),
			'other'         => __( 'Egyéb', 'bma-ajanlatkeres' ),
		);
		return $labels[ $doc_type ] ?? $doc_type;
	}

	/**
	 * Handle secure download.
	 */
	public static function handle_download(): void {
		$doc_id = absint( get_query_var( 'bma_doc_id' ) );
		if ( ! $doc_id ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'bma_documents';
		$doc   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $doc_id ) );

		if ( ! $doc || ! file_exists( $doc->file_path ) ) {
			wp_die( esc_html__( 'A dokumentum nem található.', 'bma-ajanlatkeres' ), 404 );
		}

		if ( ! BMA_AJ_Roles::can_access_request( (int) $doc->request_id ) ) {
			BMA_AJ_Security::log( 'unauthorized_download', get_current_user_id(), 'Doc ID: ' . $doc_id );
			wp_die( esc_html__( 'Nincs jogosultsága a letöltéshez.', 'bma-ajanlatkeres' ), 403 );
		}

		BMA_AJ_Analytics::track_event( 'document_download', array( 'doc_id' => $doc_id ) );

		header( 'Content-Type: ' . ( $doc->mime_type ?: 'application/octet-stream' ) );
		header( 'Content-Disposition: attachment; filename="' . basename( $doc->file_name ) . '"' );
		header( 'Content-Length: ' . filesize( $doc->file_path ) );
		readfile( $doc->file_path );
		exit;
	}

	/**
	 * Get download URL.
	 *
	 * @param int $doc_id Document ID.
	 */
	public static function get_download_url( int $doc_id ): string {
		return home_url( '/bma-download/' . $doc_id . '/' );
	}

	/**
	 * Delete a document (file + DB row). Admin only; must belong to request when given.
	 *
	 * @param int $doc_id     Document ID.
	 * @param int $request_id Optional request ID to verify ownership.
	 * @return true|WP_Error
	 */
	public static function delete( int $doc_id, int $request_id = 0 ) {
		if ( ! current_user_can( 'bma_manage_documents' ) && ! current_user_can( 'bma_manage_requests' ) ) {
			return new WP_Error( 'forbidden', __( 'Nincs jogosultsága a törléshez.', 'bma-ajanlatkeres' ) );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'bma_documents';
		$doc   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $doc_id ) );

		if ( ! $doc ) {
			return new WP_Error( 'not_found', __( 'A dokumentum nem található.', 'bma-ajanlatkeres' ) );
		}

		if ( $request_id > 0 && (int) $doc->request_id !== $request_id ) {
			return new WP_Error( 'mismatch', __( 'A dokumentum nem ehhez az ajánlatkéréshez tartozik.', 'bma-ajanlatkeres' ) );
		}

		if ( ! empty( $doc->file_path ) && file_exists( $doc->file_path ) ) {
			wp_delete_file( $doc->file_path );
		}

		$deleted = $wpdb->delete( $table, array( 'id' => $doc_id ), array( '%d' ) );
		if ( false === $deleted ) {
			return new WP_Error( 'db_error', __( 'A dokumentum törlése sikertelen.', 'bma-ajanlatkeres' ) );
		}

		BMA_AJ_Security::log( 'document_deleted', get_current_user_id(), 'Doc ID: ' . $doc_id . ', request: ' . (int) $doc->request_id );

		return true;
	}

	/**
	 * AJAX upload handler.
	 */
	public static function ajax_upload(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$request_id = absint( $_POST['request_id'] ?? 0 );

		if ( ! BMA_AJ_Roles::can_access_request( $request_id ) && ! current_user_can( 'bma_manage_documents' ) ) {
			wp_send_json_error( array( 'message' => __( 'Nincs jogosultsága.', 'bma-ajanlatkeres' ) ) );
		}

		if ( empty( $_FILES['document'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Nincs fájl.', 'bma-ajanlatkeres' ) ) );
		}

		$uploader = current_user_can( 'bma_manage_documents' ) ? 'admin' : 'client';
		$result   = self::handle_upload( $_FILES['document'], $request_id, sanitize_text_field( wp_unslash( $_POST['doc_type'] ?? 'other' ) ), $uploader );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( $result );
	}
}
