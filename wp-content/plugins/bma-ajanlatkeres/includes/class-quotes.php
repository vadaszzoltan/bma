<?php
/**
 * Quote (offer) management.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Quotes {

	public const STATUSES = array( 'draft', 'sent', 'viewed', 'accepted', 'expired', 'revoked' );

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'wp_ajax_bma_save_quote', array( self::class, 'ajax_save' ) );
	}

	/**
	 * Update quote status.
	 *
	 * @param int    $quote_id Quote ID.
	 * @param string $status   Status slug.
	 */
	public static function update_status( int $quote_id, string $status ): void {
		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . 'bma_quotes',
			array( 'status' => sanitize_text_field( $status ) ),
			array( 'id' => $quote_id )
		);
	}

	/**
	 * Mark quote as paid.
	 *
	 * @param int    $quote_id   Quote ID.
	 * @param string $method     Payment method.
	 * @param string $session_id Stripe session ID.
	 */
	public static function mark_paid( int $quote_id, string $method, string $session_id = '' ): void {
		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . 'bma_quotes',
			array(
				'status'            => 'accepted',
				'payment_status'    => 'paid',
				'payment_method'    => sanitize_text_field( $method ),
				'stripe_session_id' => sanitize_text_field( $session_id ),
				'paid_at'           => current_time( 'mysql' ),
			),
			array( 'id' => $quote_id )
		);
	}

	/**
	 * Human-readable quote / payment label for lists.
	 *
	 * @param object|null $quote Quote row.
	 */
	public static function display_label( ?object $quote ): string {
		if ( ! $quote ) {
			return __( 'Nincs ajánlat', 'bma-ajanlatkeres' );
		}

		$payment = (string) ( $quote->payment_status ?? '' );
		if ( 'paid' === $payment ) {
			return __( 'Fizetve', 'bma-ajanlatkeres' );
		}
		if ( 'bank_pending' === $payment ) {
			return __( 'Banki utalás folyamatban', 'bma-ajanlatkeres' );
		}

		switch ( (string) $quote->status ) {
			case 'draft':
				return __( 'Piszkozat', 'bma-ajanlatkeres' );
			case 'sent':
			case 'viewed':
				return __( 'Ajánlat elérhető', 'bma-ajanlatkeres' );
			case 'accepted':
				return __( 'Elfogadva', 'bma-ajanlatkeres' );
			case 'expired':
				return __( 'Lejárt', 'bma-ajanlatkeres' );
			case 'revoked':
				return __( 'Visszavonva', 'bma-ajanlatkeres' );
			default:
				return (string) $quote->status;
		}
	}

	/**
	 * Mark bank transfer selected.
	 *
	 * @param int $quote_id Quote ID.
	 */
	public static function mark_bank_pending( int $quote_id ): void {
		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . 'bma_quotes',
			array(
				'status'         => 'accepted',
				'payment_status' => 'bank_pending',
				'payment_method' => 'bank_transfer',
			),
			array( 'id' => $quote_id )
		);
	}

	/**
	 * Get latest quote for request.
	 *
	 * @param int $request_id Request ID.
	 */
	public static function get_latest_for_request( int $request_id ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quotes';

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE request_id = %d ORDER BY created_at DESC LIMIT 1",
				$request_id
			)
		);

		return $row ?: null;
	}

	/**
	 * Get all quotes for request.
	 *
	 * @param int $request_id Request ID.
	 */
	public static function get_for_request( int $request_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quotes';

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE request_id = %d ORDER BY created_at DESC",
				$request_id
			)
		);
	}

	/**
	 * Generate unique quote number: Oferta-BMA-{NR}-{YYYY-MM-DD}.
	 */
	public static function generate_number(): string {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quotes';
		$date  = wp_date( 'Y-m-d' );

		$max = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(quote_number, '-', 3), '-', -1) AS UNSIGNED))
				FROM {$table}
				WHERE quote_number LIKE %s",
				$wpdb->esc_like( 'Oferta-BMA-' ) . '%'
			)
		);

		$nr = max( 1, $max + 1 );

		do {
			$candidate = sprintf( 'Oferta-BMA-%d-%s', $nr, $date );
			$exists    = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$table} WHERE quote_number = %s",
					$candidate
				)
			);
			++$nr;
		} while ( $exists > 0 );

		return $candidate;
	}

	/**
	 * Create or update quote.
	 *
	 * @param int                  $request_id Request ID.
	 * @param array<string, mixed> $data       Quote data.
	 */
	public static function save( int $request_id, array $data ): int|WP_Error {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quotes';

		if ( ! current_user_can( 'bma_manage_quotes' ) && ! current_user_can( 'bma_manage_requests' ) ) {
			return new WP_Error( 'forbidden', __( 'Nincs jogosultsága.', 'bma-ajanlatkeres' ) );
		}

		$net   = self::parse_amount( $data['net_amount'] ?? 0 );
		$tax   = self::parse_amount( $data['tax_amount'] ?? 0 );
		$gross = self::parse_amount( $data['gross_amount'] ?? 0 );
		if ( $gross <= 0 && ( $net > 0 || $tax > 0 ) ) {
			$gross = round( $net + $tax, 2 );
		}

		$quote_id = absint( $data['id'] ?? 0 );
		if ( ! $quote_id ) {
			$existing = self::get_latest_for_request( $request_id );
			if ( $existing ) {
				$quote_id = (int) $existing->id;
			}
		}

		if ( $quote_id ) {
			$current = $wpdb->get_row(
				$wpdb->prepare( "SELECT quote_number FROM {$table} WHERE id = %d", $quote_id )
			);
			$quote_number = ( $current && ! empty( $current->quote_number ) )
				? (string) $current->quote_number
				: self::generate_number();
		} else {
			$quote_number = self::generate_number();
		}

		$quote_data = array(
			'request_id'   => $request_id,
			'quote_number' => $quote_number,
			'net_amount'   => number_format( $net, 2, '.', '' ),
			'tax_amount'   => number_format( $tax, 2, '.', '' ),
			'gross_amount' => number_format( $gross, 2, '.', '' ),
			'currency'     => sanitize_text_field( $data['currency'] ?? get_option( 'bma_default_currency', 'EUR' ) ),
			'valid_until'  => sanitize_text_field( $data['valid_until'] ?? '' ) ?: null,
			'note'         => sanitize_textarea_field( $data['note'] ?? '' ),
			'status'       => sanitize_text_field( $data['status'] ?? 'sent' ),
			'created_by'   => get_current_user_id(),
		);

		if ( $quote_id ) {
			// Do not overwrite creator on update.
			unset( $quote_data['created_by'] );
			$updated = $wpdb->update( $table, $quote_data, array( 'id' => $quote_id ) );
			if ( false === $updated ) {
				return new WP_Error(
					'db_error',
					__( 'Az ajánlat mentése sikertelen.', 'bma-ajanlatkeres' ) . ( $wpdb->last_error ? ' ' . $wpdb->last_error : '' )
				);
			}
		} else {
			$inserted = $wpdb->insert( $table, $quote_data );
			if ( ! $inserted ) {
				return new WP_Error(
					'db_error',
					__( 'Az ajánlat mentése sikertelen.', 'bma-ajanlatkeres' ) . ( $wpdb->last_error ? ' ' . $wpdb->last_error : '' )
				);
			}
			$quote_id = (int) $wpdb->insert_id;
		}

		if ( ! $quote_id ) {
			return new WP_Error( 'db_error', __( 'Az ajánlat mentése sikertelen.', 'bma-ajanlatkeres' ) );
		}

		if ( ! empty( $_FILES['quote_pdf']['name'] ) ) {
			$file = BMA_AJ_Documents::handle_upload( $_FILES['quote_pdf'], $request_id, 'quote', 'admin' );
			if ( ! is_wp_error( $file ) ) {
				$wpdb->update( $table, array( 'file_path' => $file['path'] ), array( 'id' => $quote_id ) );
			}
		}

		if ( 'sent' === $quote_data['status'] ) {
			$wpdb->update( $table, array( 'sent_at' => current_time( 'mysql' ) ), array( 'id' => $quote_id ) );
			BMA_AJ_Email_Templates::send( 'quote_uploaded', $request_id );

			$status = BMA_AJ_Statuses::get_all();
			foreach ( $status as $s ) {
				if ( 'ajanlat_elkeszult' === $s->slug ) {
					BMA_AJ_Statuses::change_status( $request_id, (int) $s->id );
					break;
				}
			}
		}

		return $quote_id;
	}

	/**
	 * Parse money input (accepts "1234,56" and "1.234,56").
	 *
	 * @param mixed $value Raw amount.
	 */
	public static function parse_amount( $value ): float {
		if ( is_numeric( $value ) ) {
			return round( (float) $value, 2 );
		}

		$raw = trim( (string) $value );
		if ( '' === $raw ) {
			return 0.0;
		}

		$raw = str_replace( array( ' ', "\xc2\xa0" ), '', $raw );
		if ( preg_match( '/^\d{1,3}(\.\d{3})+(,\d+)?$/', $raw ) ) {
			$raw = str_replace( '.', '', $raw );
			$raw = str_replace( ',', '.', $raw );
		} elseif ( str_contains( $raw, ',' ) && ! str_contains( $raw, '.' ) ) {
			$raw = str_replace( ',', '.', $raw );
		} else {
			$raw = str_replace( ',', '', $raw );
		}

		return round( (float) $raw, 2 );
	}

	/**
	 * AJAX save handler.
	 */
	public static function ajax_save(): void {
		check_ajax_referer( 'bma_aj_admin', 'nonce' );

		$request_id = absint( $_POST['request_id'] ?? 0 );
		$data       = wp_unslash( $_POST );

		$result = self::save( $request_id, $data );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'quote_id' => $result ) );
	}
}
