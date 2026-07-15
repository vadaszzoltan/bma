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
	 * Create or update quote.
	 *
	 * @param int                  $request_id Request ID.
	 * @param array<string, mixed> $data       Quote data.
	 */
	public static function save( int $request_id, array $data ): int|WP_Error {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quotes';

		if ( ! current_user_can( 'bma_manage_quotes' ) ) {
			return new WP_Error( 'forbidden', __( 'Nincs jogosultsága.', 'bma-ajanlatkeres' ) );
		}

		$net   = floatval( $data['net_amount'] ?? 0 );
		$tax   = floatval( $data['tax_amount'] ?? 0 );
		$gross = floatval( $data['gross_amount'] ?? ( $net + $tax ) );

		$quote_data = array(
			'request_id'   => $request_id,
			'quote_number' => sanitize_text_field( $data['quote_number'] ?? 'AJ-' . wp_generate_password( 8, false ) ),
			'net_amount'   => $net,
			'tax_amount'   => $tax,
			'gross_amount' => $gross,
			'currency'     => sanitize_text_field( $data['currency'] ?? 'HUF' ),
			'valid_until'  => sanitize_text_field( $data['valid_until'] ?? '' ) ?: null,
			'note'         => sanitize_textarea_field( $data['note'] ?? '' ),
			'status'       => sanitize_text_field( $data['status'] ?? 'sent' ),
			'created_by'   => get_current_user_id(),
		);

		if ( ! empty( $data['id'] ) ) {
			$wpdb->update( $table, $quote_data, array( 'id' => absint( $data['id'] ) ) );
			$quote_id = absint( $data['id'] );
		} else {
			$wpdb->insert( $table, $quote_data );
			$quote_id = (int) $wpdb->insert_id;
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
