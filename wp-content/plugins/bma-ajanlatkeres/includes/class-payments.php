<?php
/**
 * Quote acceptance and payment flow.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Payments {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'wp_ajax_bma_accept_quote', array( self::class, 'ajax_accept_quote' ) );
		add_action( 'wp_ajax_nopriv_bma_accept_quote', array( self::class, 'ajax_accept_quote' ) );
		add_action( 'wp_ajax_bma_create_stripe_session', array( self::class, 'ajax_create_stripe_session' ) );
		add_action( 'wp_ajax_nopriv_bma_create_stripe_session', array( self::class, 'ajax_create_stripe_session' ) );
		add_action( 'init', array( self::class, 'handle_stripe_return' ) );
		add_action( 'rest_api_init', array( self::class, 'register_webhook' ) );
	}

	/**
	 * Accept quote and redirect to payment page.
	 */
	public static function ajax_accept_quote(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$request_id = absint( $_POST['request_id'] ?? 0 );
		$quote_id   = absint( $_POST['quote_id'] ?? 0 );

		if ( ! BMA_AJ_Roles::can_access_request( $request_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Nincs jogosultsága.', 'bma-ajanlatkeres' ) ) );
		}

		$quote = self::get_quote( $quote_id, $request_id );
		if ( ! $quote ) {
			wp_send_json_error( array( 'message' => __( 'Ajánlat nem található.', 'bma-ajanlatkeres' ) ) );
		}

		BMA_AJ_Quotes::update_status( (int) $quote->id, 'accepted' );

		wp_send_json_success(
			array(
				'redirect' => home_url( '/fizetes/?request=' . $request_id . '&quote=' . $quote_id ),
			)
		);
	}

	/**
	 * Create Stripe Checkout session.
	 */
	public static function ajax_create_stripe_session(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$request_id = absint( $_POST['request_id'] ?? 0 );
		$quote_id   = absint( $_POST['quote_id'] ?? 0 );

		if ( ! BMA_AJ_Roles::can_access_request( $request_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Nincs jogosultsága.', 'bma-ajanlatkeres' ) ) );
		}

		$quote = self::get_quote( $quote_id, $request_id );
		if ( ! $quote ) {
			wp_send_json_error( array( 'message' => __( 'Ajánlat nem található.', 'bma-ajanlatkeres' ) ) );
		}

		$session = BMA_AJ_Stripe::create_checkout_session( $quote, $request_id );
		if ( is_wp_error( $session ) ) {
			wp_send_json_error( array( 'message' => $session->get_error_message() ) );
		}

		wp_send_json_success( array( 'redirect' => $session['url'] ) );
	}

	/**
	 * Handle return from Stripe Checkout.
	 */
	public static function handle_stripe_return(): void {
		if ( empty( $_GET['bma_stripe_return'] ) || empty( $_GET['session_id'] ) ) {
			return;
		}

		$request_id = absint( $_GET['request'] ?? 0 );
		$quote_id   = absint( $_GET['quote'] ?? 0 );
		$session_id = sanitize_text_field( wp_unslash( $_GET['session_id'] ) );

		if ( ! $request_id || ! $quote_id || ! BMA_AJ_Roles::can_access_request( $request_id ) ) {
			return;
		}

		if ( BMA_AJ_Stripe::verify_session_paid( $session_id ) ) {
			BMA_AJ_Quotes::mark_paid( $quote_id, 'stripe', $session_id );
			wp_safe_redirect( home_url( '/fizetes/?request=' . $request_id . '&quote=' . $quote_id . '&paid=1' ) );
			exit;
		}
	}

	/**
	 * Stripe webhook endpoint.
	 */
	public static function register_webhook(): void {
		register_rest_route(
			'bma/v1',
			'/stripe-webhook',
			array(
				'methods'             => 'POST',
				'callback'            => array( 'BMA_AJ_Stripe', 'handle_webhook' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get quote belonging to request.
	 *
	 * @param int $quote_id   Quote ID.
	 * @param int $request_id Request ID.
	 */
	public static function get_quote( int $quote_id, int $request_id ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_quotes';

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE id = %d AND request_id = %d",
				$quote_id,
				$request_id
			)
		);

		return $row ?: null;
	}

	/**
	 * Format amount with currency.
	 *
	 * @param float  $amount   Amount.
	 * @param string $currency Currency code.
	 */
	public static function format_money( float $amount, string $currency ): string {
		$currency = strtoupper( $currency );
		$decimals = 'RON' === $currency ? 2 : 2;
		return number_format( $amount, $decimals, ',', ' ' ) . ' ' . $currency;
	}

	/**
	 * Get quote PDF download URL if available.
	 *
	 * @param object $quote Quote row.
	 */
	public static function get_quote_pdf_url( object $quote ): ?string {
		if ( ! empty( $quote->file_path ) && file_exists( $quote->file_path ) ) {
			global $wpdb;
			$doc = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT id FROM {$wpdb->prefix}bma_documents WHERE request_id = %d AND doc_type = 'quote' ORDER BY id DESC LIMIT 1",
					$quote->request_id
				)
			);
			if ( $doc ) {
				return BMA_AJ_Documents::get_download_url( (int) $doc->id );
			}
		}
		return null;
	}
}
