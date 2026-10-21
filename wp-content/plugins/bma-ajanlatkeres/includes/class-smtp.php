<?php
/**
 * SMTP configuration for wp_mail.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_SMTP {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'phpmailer_init', array( self::class, 'configure' ) );
		// wp_mail() validates the sender before phpmailer_init, and its default
		// (wordpress@<host>) is invalid on hosts without a dot, such as localhost.
		add_filter(
			'wp_mail_from',
			static function ( string $from ): string {
				return is_email( $from ) ? $from : (string) get_option( 'bma_smtp_from', 'noreply@bma-engineering.hu' );
			}
		);
	}

	/**
	 * Configure PHPMailer.
	 *
	 * @param PHPMailer $phpmailer PHPMailer instance.
	 */
	public static function configure( $phpmailer ): void {
		$host = get_option( 'bma_smtp_host', defined( 'BMA_SMTP_HOST' ) ? BMA_SMTP_HOST : '' );
		if ( ! $host ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host       = $host;
		$phpmailer->Port       = (int) get_option( 'bma_smtp_port', defined( 'BMA_SMTP_PORT' ) ? BMA_SMTP_PORT : 587 );
		$phpmailer->SMTPAuth   = (bool) get_option( 'bma_smtp_auth', false );
		$phpmailer->Username   = get_option( 'bma_smtp_user', defined( 'BMA_SMTP_USER' ) ? BMA_SMTP_USER : '' );
		$phpmailer->Password   = get_option( 'bma_smtp_password', defined( 'BMA_SMTP_PASSWORD' ) ? BMA_SMTP_PASSWORD : '' );
		// Plain SMTP on other ports (e.g. a local MailHog on 1025).
		$secure                = array( 465 => 'ssl', 587 => 'tls' );
		$phpmailer->SMTPSecure = $secure[ $phpmailer->Port ] ?? '';
		$phpmailer->SMTPAutoTLS = '' !== $phpmailer->SMTPSecure;

		$from     = get_option( 'bma_smtp_from', 'noreply@bma-engineering.hu' );
		$from_name = get_option( 'bma_smtp_from_name', 'BMA Engineering' );
		$phpmailer->setFrom( $from, $from_name );
	}
}
