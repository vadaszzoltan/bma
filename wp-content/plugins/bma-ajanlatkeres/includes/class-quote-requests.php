<?php
/**
 * Quote request handling.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Quote_Requests {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'wp_ajax_bma_submit_quote_request', array( self::class, 'ajax_submit' ) );
		add_action( 'wp_ajax_nopriv_bma_submit_quote_request', array( self::class, 'ajax_submit' ) );
	}

	/**
	 * Get request by ID.
	 *
	 * @param int $id Request ID.
	 */
	public static function get( int $id ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quote_requests';

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ) );
		return $row ?: null;
	}

	/**
	 * Get services for request.
	 *
	 * @param int $request_id Request ID.
	 */
	public static function get_services( int $request_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quote_request_services';

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE request_id = %d ORDER BY sort_order ASC",
				$request_id
			)
		);

		foreach ( $rows as $row ) {
			$row->selected_items = json_decode( $row->selected_items ?? '[]', true ) ?: array();
			$row->field_values   = json_decode( $row->field_values ?? '{}', true ) ?: array();
		}

		return $rows;
	}

	/**
	 * Generate unique request number.
	 */
	public static function generate_number(): string {
		return 'BMA-' . gmdate( 'Ymd' ) . '-' . strtoupper( wp_generate_password( 6, false ) );
	}

	/**
	 * Submit quote request from cart.
	 *
	 * @param array<string, mixed> $contact Contact data.
	 * @param bool                 $create_account Create user account.
	 * @return int|WP_Error Request ID or error.
	 */
	public static function submit( array $contact, bool $create_account = false ) {
		global $wpdb;

		$cart = BMA_AJ_Cart::get_items();
		if ( empty( $cart ) ) {
			return new WP_Error( 'empty_cart', __( 'Az ajánlatkérési kosár üres.', 'bma-ajanlatkeres' ) );
		}

		$email = sanitize_email( $contact['email'] ?? '' );
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', __( 'Érvénytelen e-mail cím.', 'bma-ajanlatkeres' ) );
		}

		$user_id = get_current_user_id();

		if ( $create_account && ! $user_id ) {
			$existing = get_user_by( 'email', $email );
			if ( $existing ) {
				return new WP_Error( 'email_exists', __( 'Ehhez az e-mail címhez már tartozik fiók. Kérjük, jelentkezzen be.', 'bma-ajanlatkeres' ) );
			}

			$password = wp_generate_password( 12, true );
			$user_id  = wp_create_user( $email, $password, $email );

			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}

			wp_update_user(
				array(
					'ID'           => $user_id,
					'display_name' => sanitize_text_field( $contact['name'] ?? '' ),
					'role'         => BMA_AJ_Roles::ROLE_CLIENT,
				)
			);

			wp_new_user_notification( $user_id, null, 'both' );
			BMA_AJ_Analytics::track_event( 'account_created' );
		}

		$requests_table = $wpdb->prefix . 'bma_quote_requests';
		$services_table = $wpdb->prefix . 'bma_quote_request_services';

		$request_number = self::generate_number();
		$status_id      = BMA_AJ_Statuses::get_default_id();

		$inserted = $wpdb->insert(
			$requests_table,
			array(
				'request_number'  => $request_number,
				'user_id'         => $user_id ?: null,
				'client_name'     => sanitize_text_field( $contact['name'] ?? '' ),
				'client_email'    => $email,
				'client_phone'    => sanitize_text_field( $contact['phone'] ?? '' ),
				'company_name'    => sanitize_text_field( $contact['company'] ?? '' ),
				'tax_number'      => sanitize_text_field( $contact['tax_number'] ?? '' ),
				'billing_address' => sanitize_textarea_field( $contact['billing_address'] ?? '' ),
				'client_note'     => sanitize_textarea_field( $contact['note'] ?? '' ),
				'status_id'       => $status_id,
				'privacy_accepted'=> ! empty( $contact['privacy'] ) ? 1 : 0,
			)
		);

		if ( ! $inserted ) {
			return new WP_Error( 'db_error', __( 'Hiba történt a mentés során.', 'bma-ajanlatkeres' ) );
		}

		$request_id = (int) $wpdb->insert_id;
		$sort       = 0;

		foreach ( $cart as $item ) {
			$wpdb->insert(
				$services_table,
				array(
					'request_id'     => $request_id,
					'service_id'     => (int) $item['service_id'],
					'service_name'   => sanitize_text_field( $item['service_name'] ),
					'selected_items' => wp_json_encode( $item['selected_items'] ?? array() ),
					'field_values'   => wp_json_encode( $item['field_values'] ?? array() ),
					'sort_order'     => $sort++,
				)
			);
		}

		BMA_AJ_Statuses::change_status( $request_id, $status_id, __( 'Ajánlatkérés beküldve.', 'bma-ajanlatkeres' ) );

		BMA_AJ_Email_Templates::send( 'request_confirmation', $request_id );
		BMA_AJ_Email_Templates::send( 'request_admin_notify', $request_id );

		BMA_AJ_Cart::clear();
		BMA_AJ_Analytics::track_event( 'quote_request_submitted', array( 'request_id' => $request_id ) );
		BMA_AJ_Security::log( 'quote_submitted', $user_id, 'Request: ' . $request_number );

		return $request_id;
	}

	/**
	 * AJAX submit handler.
	 */
	public static function ajax_submit(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		if ( empty( $_POST['privacy'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Az adatvédelmi hozzájárulás kötelező.', 'bma-ajanlatkeres' ) ) );
		}

		$contact = array(
			'name'            => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
			'email'           => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
			'phone'           => sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) ),
			'company'         => sanitize_text_field( wp_unslash( $_POST['company'] ?? '' ) ),
			'tax_number'      => sanitize_text_field( wp_unslash( $_POST['tax_number'] ?? '' ) ),
			'billing_address' => sanitize_textarea_field( wp_unslash( $_POST['billing_address'] ?? '' ) ),
			'note'            => sanitize_textarea_field( wp_unslash( $_POST['note'] ?? '' ) ),
			'privacy'         => true,
		);

		if ( empty( $contact['name'] ) || empty( $contact['email'] ) || empty( $contact['phone'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Kérjük, töltse ki a kötelező mezőket.', 'bma-ajanlatkeres' ) ) );
		}

		$create_account = ! empty( $_POST['create_account'] );
		$result         = self::submit( $contact, $create_account );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		$request = self::get( $result );

		wp_send_json_success(
			array(
				'request_id'     => $result,
				'request_number' => $request->request_number,
				'redirect'       => home_url( '/ajanlatkeres/?submitted=1&number=' . urlencode( $request->request_number ) ),
			)
		);
	}

	/**
	 * Get requests for user.
	 *
	 * @param int $user_id User ID.
	 */
	public static function get_for_user( int $user_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quote_requests';

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT r.*, s.name as status_name, s.color as status_color
				FROM {$table} r
				LEFT JOIN {$wpdb->prefix}bma_statuses s ON s.id = r.status_id
				WHERE r.user_id = %d
				ORDER BY r.submitted_at DESC",
				$user_id
			)
		);
	}

	/**
	 * Admin list with filters.
	 *
	 * @param array<string, mixed> $args Query args.
	 */
	public static function get_list( array $args = array() ): array {
		global $wpdb;

		$table   = $wpdb->prefix . 'bma_quote_requests';
		$where   = array( '1=1' );
		$values  = array();

		if ( ! empty( $args['status_id'] ) ) {
			$where[]  = 'r.status_id = %d';
			$values[] = absint( $args['status_id'] );
		}

		if ( ! empty( $args['search'] ) ) {
			$where[]  = '(r.request_number LIKE %s OR r.client_name LIKE %s OR r.client_email LIKE %s)';
			$like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$values[] = $like;
			$values[] = $like;
			$values[] = $like;
		}

		$page     = max( 1, absint( $args['page'] ?? 1 ) );
		$per_page = absint( $args['per_page'] ?? 20 );
		$offset   = ( $page - 1 ) * $per_page;

		$where_sql = implode( ' AND ', $where );

		$sql = "SELECT r.*, s.name as status_name, s.color as status_color
			FROM {$table} r
			LEFT JOIN {$wpdb->prefix}bma_statuses s ON s.id = r.status_id
			WHERE {$where_sql}
			ORDER BY r.submitted_at DESC
			LIMIT %d OFFSET %d";

		$values[] = $per_page;
		$values[] = $offset;

		if ( ! empty( $values ) ) {
			return $wpdb->get_results( $wpdb->prepare( $sql, $values ) );
		}

		return $wpdb->get_results( $sql );
	}
}
