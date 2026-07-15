<?php
/**
 * Security utilities.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Security {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_filter( 'authenticate', array( self::class, 'check_brute_force' ), 30, 3 );
		add_action( 'wp_login_failed', array( self::class, 'log_failed_login' ) );
	}

	/**
	 * Log security event.
	 *
	 * @param string $event_type Event type.
	 * @param int    $user_id    User ID.
	 * @param string $message    Message.
	 */
	public static function log( string $event_type, ?int $user_id, string $message = '' ): void {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_security_log';

		$wpdb->insert(
			$table,
			array(
				'event_type' => sanitize_key( $event_type ),
				'user_id'    => $user_id,
				'ip_address' => self::get_ip(),
				'message'    => sanitize_text_field( $message ),
			)
		);
	}

	/**
	 * Get client IP.
	 */
	private static function get_ip(): string {
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return sanitize_text_field( wp_unslash( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] )[0] ) );
		}
		return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	}

	/**
	 * Brute force check.
	 *
	 * @param WP_User|WP_Error|null $user     User or error.
	 * @param string                $username Username.
	 * @param string                $password Password.
	 */
	public static function check_brute_force( $user, string $username, string $password ) {
		if ( empty( $username ) ) {
			return $user;
		}

		$key      = 'bma_login_attempts_' . md5( self::get_ip() . $username );
		$attempts = (int) get_transient( $key );

		if ( $attempts >= 5 ) {
			return new WP_Error( 'too_many_attempts', __( 'Túl sok sikertelen bejelentkezési kísérlet. Próbálja újra 15 perc múlva.', 'bma-ajanlatkeres' ) );
		}

		return $user;
	}

	/**
	 * Log failed login.
	 *
	 * @param string $username Username.
	 */
	public static function log_failed_login( string $username ): void {
		$key      = 'bma_login_attempts_' . md5( self::get_ip() . $username );
		$attempts = (int) get_transient( $key );
		set_transient( $key, $attempts + 1, 15 * MINUTE_IN_SECONDS );

		self::log( 'login_failed', null, 'User: ' . $username );
	}
}
