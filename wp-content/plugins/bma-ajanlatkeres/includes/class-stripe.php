<?php
/**
 * Stripe Checkout integration.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Stripe {

	/**
	 * Whether Stripe secret key is configured.
	 */
	public static function is_configured(): bool {
		return (bool) self::get_secret_key();
	}

	/**
	 * Publishable key (env constant overrides WP option).
	 */
	public static function get_publishable_key(): string {
		if ( defined( 'BMA_STRIPE_PUBLISHABLE_KEY' ) && BMA_STRIPE_PUBLISHABLE_KEY ) {
			return BMA_STRIPE_PUBLISHABLE_KEY;
		}

		return (string) get_option( 'bma_stripe_publishable_key', '' );
	}

	/**
	 * Secret key (env constant overrides WP option).
	 */
	public static function get_secret_key(): string {
		if ( defined( 'BMA_STRIPE_SECRET_KEY' ) && BMA_STRIPE_SECRET_KEY ) {
			return BMA_STRIPE_SECRET_KEY;
		}

		return (string) get_option( 'bma_stripe_secret_key', '' );
	}

	/**
	 * Webhook signing secret (env constant overrides WP option).
	 */
	public static function get_webhook_secret(): string {
		if ( defined( 'BMA_STRIPE_WEBHOOK_SECRET' ) && BMA_STRIPE_WEBHOOK_SECRET ) {
			return BMA_STRIPE_WEBHOOK_SECRET;
		}

		return (string) get_option( 'bma_stripe_webhook_secret', '' );
	}

	/**
	 * Create Checkout Session via Stripe API.
	 *
	 * @param object $quote      Quote row.
	 * @param int    $request_id Request ID.
	 * @return array<string, string>|WP_Error
	 */
	public static function create_checkout_session( object $quote, int $request_id ) {
		$secret = self::get_secret_key();
		if ( ! $secret ) {
			return new WP_Error( 'stripe_config', __( 'A Stripe nincs konfigurálva.', 'bma-ajanlatkeres' ) );
		}

		$currency = strtolower( $quote->currency ?: 'eur' );
		if ( ! in_array( strtoupper( $currency ), array( 'EUR', 'RON' ), true ) ) {
			$currency = 'eur';
		}

		$amount_cents = (int) round( (float) $quote->gross_amount * 100 );
		if ( $amount_cents < 1 ) {
			return new WP_Error( 'invalid_amount', __( 'Érvénytelen összeg.', 'bma-ajanlatkeres' ) );
		}

		$request = BMA_AJ_Quote_Requests::get( $request_id );

		$success_url = add_query_arg(
			array(
				'bma_stripe_return' => '1',
				'request'           => $request_id,
				'quote'             => $quote->id,
				'session_id'        => '{CHECKOUT_SESSION_ID}',
			),
			home_url( '/fizetes/' )
		);

		$cancel_url = add_query_arg(
			array(
				'request' => $request_id,
				'quote'   => $quote->id,
			),
			home_url( '/fizetes/' )
		);

		$body = array(
			'mode'                                            => 'payment',
			'success_url'                                     => $success_url,
			'cancel_url'                                      => $cancel_url,
			'client_reference_id'                             => (string) $quote->id,
			'metadata[quote_id]'                              => (string) $quote->id,
			'metadata[request_id]'                            => (string) $request_id,
			'metadata[quote_number]'                          => (string) $quote->quote_number,
			'line_items[0][quantity]'                         => '1',
			'line_items[0][price_data][currency]'             => $currency,
			'line_items[0][price_data][unit_amount]'          => (string) $amount_cents,
			'line_items[0][price_data][product_data][name]'   => sprintf(
				/* translators: %s: quote number */
				__( 'BMA ajánlat %s', 'bma-ajanlatkeres' ),
				$quote->quote_number
			),
			'line_items[0][price_data][product_data][metadata][quote_id]' => (string) $quote->id,
		);

		if ( $request && ! empty( $request->client_email ) && is_email( $request->client_email ) ) {
			$body['customer_email'] = $request->client_email;
		}

		if ( get_option( 'bma_stripe_invoice_creation', '1' ) === '1' ) {
			$body['invoice_creation[enabled]'] = 'true';
		}

		if ( get_option( 'bma_stripe_automatic_tax', '0' ) === '1' ) {
			$body['automatic_tax[enabled]'] = 'true';
		}

		$response = wp_remote_post(
			'https://api.stripe.com/v1/checkout/sessions',
			array(
				'headers' => array(
					'Authorization'  => 'Bearer ' . $secret,
					'Idempotency-Key'  => 'bma-quote-' . (int) $quote->id . '-' . gmdate( 'Ymd' ),
				),
				'body'    => $body,
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $code >= 400 || empty( $data['url'] ) ) {
			$msg = $data['error']['message'] ?? __( 'Stripe hiba.', 'bma-ajanlatkeres' );
			return new WP_Error( 'stripe_error', $msg );
		}

		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . 'bma_quotes',
			array( 'stripe_session_id' => sanitize_text_field( $data['id'] ?? '' ) ),
			array( 'id' => (int) $quote->id )
		);

		return array(
			'id'  => $data['id'],
			'url' => $data['url'],
		);
	}

	/**
	 * Verify session is paid.
	 *
	 * @param string $session_id Stripe session ID.
	 */
	public static function verify_session_paid( string $session_id ): bool {
		$secret = self::get_secret_key();
		if ( ! $secret || ! $session_id ) {
			return false;
		}

		$response = wp_remote_get(
			'https://api.stripe.com/v1/checkout/sessions/' . rawurlencode( $session_id ),
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret,
				),
				'timeout' => 20,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return ( $data['payment_status'] ?? '' ) === 'paid';
	}

	/**
	 * Handle Stripe webhook.
	 *
	 * @param WP_REST_Request $request Request.
	 */
	public static function handle_webhook( WP_REST_Request $request ): WP_REST_Response {
		$payload = $request->get_body();
		$sig     = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
		$secret  = self::get_webhook_secret();

		if ( ! $secret ) {
			return new WP_REST_Response( array( 'error' => 'Webhook not configured' ), 503 );
		}

		if ( ! $sig || ! self::verify_webhook_signature( $payload, $sig, $secret ) ) {
			return new WP_REST_Response( array( 'error' => 'Invalid signature' ), 400 );
		}

		$event = json_decode( $payload, true );
		$type  = $event['type'] ?? '';

		if ( in_array( $type, array( 'checkout.session.completed', 'checkout.session.async_payment_succeeded' ), true ) ) {
			self::mark_quote_paid_from_session( $event['data']['object'] ?? array() );
		}

		return new WP_REST_Response( array( 'received' => true ), 200 );
	}

	/**
	 * Mark quote paid from checkout session payload.
	 *
	 * @param array<string, mixed> $session Stripe session object.
	 */
	private static function mark_quote_paid_from_session( array $session ): void {
		$quote_id = absint( $session['metadata']['quote_id'] ?? 0 );
		if ( ! $quote_id || ( $session['payment_status'] ?? '' ) !== 'paid' ) {
			return;
		}

		BMA_AJ_Quotes::mark_paid( $quote_id, 'stripe', $session['id'] ?? '' );
	}

	/**
	 * Verify webhook signature (simplified).
	 *
	 * @param string $payload Payload.
	 * @param string $sig     Signature header.
	 * @param string $secret  Webhook secret.
	 */
	private static function verify_webhook_signature( string $payload, string $sig, string $secret ): bool {
		$parts = array();
		foreach ( explode( ',', $sig ) as $part ) {
			list( $k, $v ) = array_pad( explode( '=', trim( $part ), 2 ), 2, '' );
			$parts[ $k ]   = $v;
		}

		if ( empty( $parts['t'] ) || empty( $parts['v1'] ) ) {
			return false;
		}

		$signed   = $parts['t'] . '.' . $payload;
		$expected = hash_hmac( 'sha256', $signed, $secret );
		return hash_equals( $expected, $parts['v1'] );
	}
}
