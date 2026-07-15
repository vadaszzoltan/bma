<?php
/**
 * User roles and capabilities.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Roles {

	public const ROLE_CLIENT = 'bma_client';

	/**
	 * Setup roles on activation.
	 */
	public static function setup(): void {
		remove_role( self::ROLE_CLIENT );

		add_role(
			self::ROLE_CLIENT,
			__( 'BMA Ügyfél', 'bma-ajanlatkeres' ),
			array(
				'read'                   => true,
				'bma_view_own_requests'  => true,
				'bma_download_own_docs'  => true,
			)
		);

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$caps = array(
				'bma_manage_services',
				'bma_manage_requests',
				'bma_manage_quotes',
				'bma_manage_documents',
				'bma_manage_statuses',
				'bma_manage_settings',
				'bma_view_all_requests',
				'bma_download_all_docs',
			);
			foreach ( $caps as $cap ) {
				$admin->add_cap( $cap );
			}
		}
	}

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'admin_init', array( self::class, 'block_admin_for_clients' ) );
		add_filter( 'login_redirect', array( self::class, 'client_login_redirect' ), 10, 3 );
	}

	/**
	 * Redirect clients away from wp-admin.
	 */
	public static function block_admin_for_clients(): void {
		if ( ! is_user_logged_in() || wp_doing_ajax() ) {
			return;
		}

		$user = wp_get_current_user();
		if ( in_array( self::ROLE_CLIENT, (array) $user->roles, true ) && ! defined( 'DOING_AJAX' ) ) {
			wp_safe_redirect( home_url( '/ugyfelportal/' ) );
			exit;
		}
	}

	/**
	 * Redirect clients to portal after login.
	 *
	 * @param string  $redirect_to Redirect URL.
	 * @param string  $requested   Requested redirect.
	 * @param WP_User $user        User object.
	 */
	public static function client_login_redirect( string $redirect_to, string $requested, WP_User $user ): string {
		if ( in_array( self::ROLE_CLIENT, (array) $user->roles, true ) ) {
			return home_url( '/ugyfelportal/' );
		}
		return $redirect_to;
	}

	/**
	 * Check if user can access a quote request.
	 *
	 * @param int      $request_id Request ID.
	 * @param int|null $user_id    User ID.
	 */
	public static function can_access_request( int $request_id, ?int $user_id = null ): bool {
		if ( current_user_can( 'bma_view_all_requests' ) ) {
			return true;
		}

		$user_id = $user_id ?? get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'bma_quote_requests';

		$owner = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT user_id FROM {$table} WHERE id = %d",
				$request_id
			)
		);

		return $owner === $user_id;
	}
}
