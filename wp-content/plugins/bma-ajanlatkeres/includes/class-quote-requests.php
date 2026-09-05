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

		$shared_fields = $contact['shared_field_values'] ?? array();
		unset( $contact['shared_field_values'] );
		$shared_documents = $contact['shared_documents'] ?? array();
		unset( $contact['shared_documents'] );

		$insert_data = array(
			'request_number'   => $request_number,
			'user_id'          => $user_id ?: null,
			'client_name'      => sanitize_text_field( $contact['name'] ?? '' ),
			'client_email'     => $email,
			'client_phone'     => sanitize_text_field( $contact['phone'] ?? '' ),
			'company_name'     => sanitize_text_field( $contact['company'] ?? '' ),
			'tax_number'       => sanitize_text_field( $contact['tax_number'] ?? '' ),
			'billing_address'  => sanitize_textarea_field( $contact['billing_address'] ?? '' ),
			'client_note'      => sanitize_textarea_field( $contact['note'] ?? '' ),
			'status_id'        => $status_id,
			'privacy_accepted' => ! empty( $contact['privacy'] ) ? 1 : 0,
		);

		if ( self::column_exists( $requests_table, 'shared_field_values' ) ) {
			$insert_data['shared_field_values'] = wp_json_encode( $shared_fields );
		}

		$inserted = $wpdb->insert( $requests_table, $insert_data );

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
					'field_values'   => wp_json_encode( array() ),
					'sort_order'     => $sort++,
				)
			);
		}

		if ( ! empty( $shared_documents ) && is_array( $shared_documents ) ) {
			foreach ( $shared_documents as $doc ) {
				BMA_AJ_Documents::promote_temp_to_request( $doc, $request_id, 'client_upload' );
			}
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
	 * AJAX submit handler (supports multipart for file uploads).
	 */
	public static function ajax_submit(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		if ( empty( $_POST['privacy'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Az adatvédelmi hozzájárulás kötelező.', 'bma-ajanlatkeres' ) ) );
		}

		$field_values_raw = isset( $_POST['field_values'] ) ? wp_unslash( $_POST['field_values'] ) : array();
		if ( ! is_array( $field_values_raw ) ) {
			$field_values_raw = array();
		}

		$merged_fields = BMA_AJ_Cart::get_merged_fields();
		$documents     = array();

		foreach ( $merged_fields as $field ) {
			if ( 'file' !== $field->field_type ) {
				continue;
			}
			$file = BMA_AJ_Documents::extract_uploaded_file( 'field_values', $field->field_key );
			if ( ! $file ) {
				if ( $field->is_required ) {
					wp_send_json_error(
						array(
							'message' => sprintf(
								__( 'Kötelező fájl hiányzik: %s', 'bma-ajanlatkeres' ),
								$field->client_label
							),
						)
					);
				}
				continue;
			}
			$result = BMA_AJ_Documents::handle_temp_upload( $file, $field->field_key );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}
			$documents[] = $result;
			$field_values_raw[ $field->field_key ] = $result['original_name'];
		}

		$shared_fields = BMA_AJ_Dynamic_Fields::sanitize_cart_submission( $field_values_raw, $merged_fields );

		$contact = array(
			'name'                 => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
			'email'                => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
			'phone'                => sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) ),
			'company'              => sanitize_text_field( wp_unslash( $_POST['company'] ?? '' ) ),
			'tax_number'           => sanitize_text_field( wp_unslash( $_POST['tax_number'] ?? '' ) ),
			'billing_address'      => sanitize_textarea_field( wp_unslash( $_POST['billing_address'] ?? '' ) ),
			'note'                 => sanitize_textarea_field( wp_unslash( $_POST['note'] ?? '' ) ),
			'privacy'              => true,
			'shared_field_values'  => $shared_fields,
			'shared_documents'     => $documents,
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
				'redirect'       => add_query_arg(
					array(
						'submitted' => '1',
						'number'    => $request->request_number,
					),
					class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ajanlatkeres' ) : home_url( '/ajanlatkeres/' )
				),
			)
		);
	}

	/**
	 * Get shared field values for a request.
	 *
	 * @param object $request Request row.
	 * @return array<string, mixed>
	 */
	public static function get_shared_fields( object $request ): array {
		if ( empty( $request->shared_field_values ) ) {
			return array();
		}
		$decoded = json_decode( $request->shared_field_values, true );
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Format a stored field value for display.
	 *
	 * @param mixed $value Raw value.
	 */
	public static function format_field_value( $value ): string {
		if ( is_array( $value ) ) {
			$value = array_filter( array_map( 'strval', $value ), static fn( $v ) => '' !== trim( $v ) );
			return implode( ', ', $value );
		}
		return (string) $value;
	}

	/**
	 * Labeled rows for client contact + shared dynamic fields (admin / emails).
	 *
	 * @param object $request Request row.
	 * @return array<int, array{label:string,value:string}>
	 */
	public static function get_client_data_rows( object $request ): array {
		$rows = array(
			array(
				'label' => __( 'Név', 'bma-ajanlatkeres' ),
				'value' => (string) ( $request->client_name ?? '' ),
			),
			array(
				'label' => __( 'E-mail', 'bma-ajanlatkeres' ),
				'value' => (string) ( $request->client_email ?? '' ),
			),
			array(
				'label' => __( 'Telefon', 'bma-ajanlatkeres' ),
				'value' => (string) ( $request->client_phone ?? '' ),
			),
			array(
				'label' => __( 'Cég', 'bma-ajanlatkeres' ),
				'value' => (string) ( $request->company_name ?? '' ),
			),
			array(
				'label' => __( 'Adószám', 'bma-ajanlatkeres' ),
				'value' => (string) ( $request->tax_number ?? '' ),
			),
			array(
				'label' => __( 'Számlázási cím', 'bma-ajanlatkeres' ),
				'value' => (string) ( $request->billing_address ?? '' ),
			),
			array(
				'label' => __( 'Megjegyzés', 'bma-ajanlatkeres' ),
				'value' => (string) ( $request->client_note ?? '' ),
			),
		);

		foreach ( self::get_shared_fields( $request ) as $key => $val ) {
			$formatted = self::format_field_value( $val );
			if ( '' === trim( $formatted ) ) {
				continue;
			}
			$rows[] = array(
				'label' => BMA_AJ_Dynamic_Fields::get_label_for_key( (string) $key ),
				'value' => $formatted,
			);
		}

		return array_values(
			array_filter(
				$rows,
				static fn( array $row ) => '' !== trim( $row['value'] )
			)
		);
	}

	/**
	 * Ordered service items for a request (for per-item deliverables).
	 *
	 * @param int $request_id Request ID.
	 * @return array<int, object{id:int,name:string,service_id:int,service_name:string}>
	 */
	public static function get_ordered_items( int $request_id ): array {
		$services = self::get_services( $request_id );
		$ordered  = array();
		$seen     = array();

		global $wpdb;
		foreach ( $services as $svc ) {
			$item_ids = array_map( 'absint', (array) ( $svc->selected_items ?? array() ) );
			foreach ( $item_ids as $item_id ) {
				if ( ! $item_id || isset( $seen[ $item_id ] ) ) {
					continue;
				}
				$seen[ $item_id ] = true;
				$name             = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT name FROM {$wpdb->prefix}bma_service_items WHERE id = %d",
						$item_id
					)
				);
				$ordered[] = (object) array(
					'id'           => $item_id,
					'name'         => $name ?: sprintf( /* translators: %d */ __( 'Tétel #%d', 'bma-ajanlatkeres' ), $item_id ),
					'service_id'   => (int) ( $svc->service_id ?? 0 ),
					'service_name' => (string) ( $svc->service_name ?? '' ),
				);
			}
		}

		return $ordered;
	}

	/**
	 * Check if DB column exists.
	 *
	 * @param string $table  Table name.
	 * @param string $column Column name.
	 */
	private static function column_exists( string $table, string $column ): bool {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$cols = $wpdb->get_results( "SHOW COLUMNS FROM {$table}" );
		foreach ( $cols as $col ) {
			if ( $col->Field === $column ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get requests for user (with services + latest quote summary).
	 *
	 * @param int $user_id User ID.
	 * @return array<int, object>
	 */
	public static function get_for_user( int $user_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quote_requests';

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT r.*, s.name as status_name, s.color as status_color, s.slug as status_slug
				FROM {$table} r
				LEFT JOIN {$wpdb->prefix}bma_statuses s ON s.id = r.status_id
				WHERE r.user_id = %d
				ORDER BY r.submitted_at DESC",
				$user_id
			)
		);

		return self::enrich_list_rows( $rows ?: array() );
	}

	/**
	 * Portal dashboard counters for a user.
	 *
	 * @param int $user_id User ID.
	 * @return array<string, int>
	 */
	public static function get_portal_stats( int $user_id ): array {
		$requests = self::get_for_user( $user_id );
		$stats    = array(
			'active'      => 0,
			'new_quotes'  => 0,
			'documents'   => 0,
			'payment_due' => 0,
			'total'       => count( $requests ),
		);

		foreach ( $requests as $req ) {
			if ( 'lezarva' !== (string) ( $req->status_slug ?? '' ) ) {
				++$stats['active'];
			}
			$qstatus = (string) ( $req->quote_status ?? '' );
			$pstatus = (string) ( $req->payment_status ?? '' );
			if ( in_array( $qstatus, array( 'sent', 'viewed' ), true ) && 'paid' !== $pstatus ) {
				++$stats['new_quotes'];
			}
			if ( 'paid' !== $pstatus && in_array( $pstatus, array( 'bank_pending' ), true ) ) {
				++$stats['payment_due'];
			} elseif ( 'paid' !== $pstatus && 'accepted' === $qstatus ) {
				++$stats['payment_due'];
			}
			$stats['documents'] += (int) ( $req->client_doc_count ?? 0 );
		}

		return $stats;
	}

	/**
	 * Admin list with filters.
	 *
	 * @param array<string, mixed> $args Query args.
	 * @return array{items: array<int, object>, total: int, pages: int}
	 */
	public static function get_list( array $args = array() ): array {
		global $wpdb;

		$table   = $wpdb->prefix . 'bma_quote_requests';
		$where   = array( '1=1' );
		$values  = array();
		$join    = '';

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

		if ( ! empty( $args['date_from'] ) ) {
			$where[]  = 'DATE(r.submitted_at) >= %s';
			$values[] = sanitize_text_field( $args['date_from'] );
		}

		if ( ! empty( $args['date_to'] ) ) {
			$where[]  = 'DATE(r.submitted_at) <= %s';
			$values[] = sanitize_text_field( $args['date_to'] );
		}

		if ( ! empty( $args['service_id'] ) ) {
			$join    .= " INNER JOIN {$wpdb->prefix}bma_quote_request_services rs ON rs.request_id = r.id ";
			$where[]  = 'rs.service_id = %d';
			$values[] = absint( $args['service_id'] );
		}

		$page     = max( 1, absint( $args['page'] ?? 1 ) );
		$per_page = absint( $args['per_page'] ?? 20 );
		$offset   = ( $page - 1 ) * $per_page;

		$where_sql = implode( ' AND ', $where );

		$count_sql = "SELECT COUNT(DISTINCT r.id) FROM {$table} r {$join}
			LEFT JOIN {$wpdb->prefix}bma_statuses s ON s.id = r.status_id
			WHERE {$where_sql}";

		if ( ! empty( $values ) ) {
			$total = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $values ) );
		} else {
			$total = (int) $wpdb->get_var( $count_sql );
		}

		$sql = "SELECT DISTINCT r.*, s.name as status_name, s.color as status_color, s.slug as status_slug
			FROM {$table} r
			{$join}
			LEFT JOIN {$wpdb->prefix}bma_statuses s ON s.id = r.status_id
			WHERE {$where_sql}
			ORDER BY r.submitted_at DESC
			LIMIT %d OFFSET %d";

		$list_values   = $values;
		$list_values[] = $per_page;
		$list_values[] = $offset;

		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $list_values ) );

		return array(
			'items' => self::enrich_list_rows( $rows ?: array() ),
			'total' => $total,
			'pages' => max( 1, (int) ceil( $total / max( 1, $per_page ) ) ),
		);
	}

	/**
	 * Attach service names + latest quote fields to request rows.
	 *
	 * @param array<int, object> $rows Request rows.
	 * @return array<int, object>
	 */
	public static function enrich_list_rows( array $rows ): array {
		if ( empty( $rows ) ) {
			return array();
		}

		global $wpdb;
		$ids = array_map(
			static function ( $row ) {
				return (int) $row->id;
			},
			$rows
		);
		$id_list = implode( ',', array_map( 'absint', $ids ) );

		$svc_rows = $wpdb->get_results(
			"SELECT request_id, service_name FROM {$wpdb->prefix}bma_quote_request_services
			WHERE request_id IN ({$id_list}) ORDER BY sort_order ASC"
		);
		$services_map = array();
		foreach ( $svc_rows as $svc ) {
			$services_map[ (int) $svc->request_id ][] = $svc->service_name;
		}

		$quote_rows = $wpdb->get_results(
			"SELECT q.* FROM {$wpdb->prefix}bma_quotes q
			INNER JOIN (
				SELECT request_id, MAX(id) AS max_id FROM {$wpdb->prefix}bma_quotes
				WHERE request_id IN ({$id_list}) GROUP BY request_id
			) latest ON latest.max_id = q.id"
		);
		$quotes_map = array();
		foreach ( $quote_rows as $q ) {
			$quotes_map[ (int) $q->request_id ] = $q;
		}

		$doc_rows = $wpdb->get_results(
			"SELECT request_id, COUNT(*) AS cnt FROM {$wpdb->prefix}bma_documents
			WHERE request_id IN ({$id_list}) AND client_visible = 1
			AND uploader_type = 'admin' AND doc_type <> 'quote'
			GROUP BY request_id"
		);
		$docs_map = array();
		foreach ( $doc_rows as $d ) {
			$docs_map[ (int) $d->request_id ] = (int) $d->cnt;
		}

		foreach ( $rows as $row ) {
			$rid = (int) $row->id;
			$row->service_names     = $services_map[ $rid ] ?? array();
			$row->services_label    = $row->service_names ? implode( ', ', $row->service_names ) : '—';
			$row->client_doc_count  = $docs_map[ $rid ] ?? 0;
			$quote                  = $quotes_map[ $rid ] ?? null;
			$row->quote_status      = $quote->status ?? '';
			$row->payment_status    = $quote->payment_status ?? '';
			$row->quote_label       = BMA_AJ_Quotes::display_label( $quote );
			if ( empty( $row->status_slug ) && ! empty( $row->status_id ) ) {
				$st = BMA_AJ_Statuses::get( (int) $row->status_id );
				$row->status_slug = $st->slug ?? '';
			}
		}

		return $rows;
	}
}
