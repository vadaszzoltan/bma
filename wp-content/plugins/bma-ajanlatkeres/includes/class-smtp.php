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
		$phpmailer->SMTPSecure = $phpmailer->Port === 465 ? 'ssl' : 'tls';

		$from     = get_option( 'bma_smtp_from', 'noreply@bma-engineering.hu' );
		$from_name = get_option( 'bma_smtp_from_name', 'BMA Engineering' );
		$phpmailer->setFrom( $from, $from_name );
	}
}
