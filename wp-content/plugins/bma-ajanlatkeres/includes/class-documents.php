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
	}

	/**
	 * Register rewrite for secure downloads.
	 */
	public static function register_download_endpoint(): void {
		add_rewrite_rule( '^bma-download/([0-9]+)/?$', 'index.php?bma_doc_id=$matches[1]', 'top' );
		add_rewrite_tag( '%bma_doc_id%', '([0-9]+)' );
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
	 * @param array  $file       $_FILES entry.
	 * @param int    $request_id Request ID.
	 * @param string $doc_type   Document type.
	 * @param string $uploader   client|admin.
	 * @return array|WP_Error
	 */
	public static function handle_upload( array $file, int $request_id, string $doc_type = 'other', string $uploader = 'client' ) {
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

		$wpdb->insert(
			$table,
			array(
				'request_id'      => $request_id,
				'title'           => sanitize_text_field( $file['name'] ),
				'doc_type'        => sanitize_text_field( $doc_type ),
				'file_path'       => $dest,
				'file_name'       => $safe_name,
				'mime_type'       => sanitize_mime_type( $file['type'] ),
				'file_size'       => absint( $file['size'] ),
				'uploaded_by'     => get_current_user_id() ?: null,
				'uploader_type'   => $uploader,
				'client_visible'  => 'admin' === $uploader ? 1 : 1,
			)
		);

		$doc_id = (int) $wpdb->insert_id;

		if ( 'admin' === $uploader ) {
			BMA_AJ_Email_Templates::send( 'document_uploaded', $request_id );
		}

		return array(
			'id'   => $doc_id,
			'path' => $dest,
			'name' => $safe_name,
		);
	}

	/**
	 * Get documents for request.
	 *
	 * @param int  $request_id Request ID.
	 * @param bool $client_only Only client-visible.
	 */
	public static function get_for_request( int $request_id, bool $client_only = false ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_documents';

		$sql = "SELECT * FROM {$table} WHERE request_id = %d";
		if ( $client_only ) {
			$sql .= ' AND client_visible = 1';
		}
		$sql .= ' ORDER BY uploaded_at DESC';

		return $wpdb->get_results( $wpdb->prepare( $sql, $request_id ) );
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
