<?php
/**
 * Access to a quote request without a client account (secret link from the emails).
 *
 * Guest requests (user_id NULL) get a link with a key derived from the request and the
 * site salt. Opening it stores the key in a cookie for that one request, so the portal
 * detail, document downloads, quote acceptance and payment work without logging in.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Guest_Access {

	public const QUERY_ARG     = 'bma_key';
	public const COOKIE_PREFIX = 'bma_gk_';

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'init', array( self::class, 'capture_key' ), 5 );
		add_action( 'template_redirect', array( self::class, 'prevent_caching' ), 1 );
	}

	/**
	 * Pages opened with a guest key show private data: keep them out of page caches.
	 */
	public static function prevent_caching(): void {
		$request_id = absint( $_GET['request'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( is_user_logged_in() || ! self::has_access( $request_id ) ) {
			return;
		}

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		nocache_headers();
	}

	/**
	 * Secret key of a request.
	 *
	 * @param object $request Request row.
	 */
	public static function key( object $request ): string {
		return substr( hash_hmac( 'sha256', 'guest|' . (int) $request->id . '|' . $request->request_number, wp_salt( 'auth' ) ), 0, 32 );
	}

	/**
	 * Whether the request belongs to no account.
	 *
	 * @param object|null $request Request row.
	 */
	public static function is_guest_request( ?object $request ): bool {
		return $request && empty( $request->user_id );
	}

	/**
	 * Portal detail link for a request; guest requests get the secret key.
	 *
	 * @param int $request_id Request ID.
	 */
	public static function portal_url( int $request_id ): string {
		$url     = add_query_arg( 'request', $request_id, BMA_AJ_I18n::page_url( 'ugyfelportal' ) );
		$request = BMA_AJ_Quote_Requests::get( $request_id );

		return self::is_guest_request( $request ) ? add_query_arg( self::QUERY_ARG, self::key( $request ), $url ) : $url;
	}

	/**
	 * Store a valid key from the link in a cookie, then drop it from the URL.
	 */
	public static function capture_key(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET[ self::QUERY_ARG ] ) || empty( $_GET['request'] ) || wp_doing_ajax() ) {
			return;
		}

		$request_id = absint( $_GET['request'] );
		$key        = sanitize_text_field( wp_unslash( $_GET[ self::QUERY_ARG ] ) );
		// phpcs:enable

		if ( self::key_matches( $request_id, $key ) ) {
			setcookie(
				self::COOKIE_PREFIX . $request_id,
				$key,
				array(
					'expires'  => time() + YEAR_IN_SECONDS,
					'path'     => '/',
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
			$_COOKIE[ self::COOKIE_PREFIX . $request_id ] = $key;
		}

		wp_safe_redirect( remove_query_arg( self::QUERY_ARG ) );
		exit;
	}

	/**
	 * Whether the visitor opened this guest request's secret link.
	 *
	 * @param int $request_id Request ID.
	 */
	public static function has_access( int $request_id ): bool {
		$cookie = self::COOKIE_PREFIX . $request_id;
		if ( ! $request_id || empty( $_COOKIE[ $cookie ] ) ) {
			return false;
		}

		return self::key_matches( $request_id, sanitize_text_field( wp_unslash( $_COOKIE[ $cookie ] ) ) );
	}

	/**
	 * Compare a key with the request's own.
	 *
	 * @param int    $request_id Request ID.
	 * @param string $key        Given key.
	 */
	private static function key_matches( int $request_id, string $key ): bool {
		$request = BMA_AJ_Quote_Requests::get( $request_id );
		if ( ! self::is_guest_request( $request ) || '' === $key ) {
			return false;
		}

		return hash_equals( self::key( $request ), $key );
	}
}
