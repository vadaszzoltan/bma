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
		add_filter( 'login_url', array( self::class, 'frontend_login_url' ), 10, 3 );
		add_filter( 'logout_redirect', array( self::class, 'frontend_logout_redirect' ), 10, 3 );
		add_action( 'login_init', array( self::class, 'redirect_wp_login_get' ) );
		add_action( 'wp_login_failed', array( self::class, 'redirect_failed_login' ) );
		add_filter( 'authenticate', array( self::class, 'redirect_empty_login' ), 99, 3 );
		add_filter( 'show_admin_bar', array( self::class, 'hide_frontend_admin_bar' ) );
	}

	/**
	 * Hide WP admin bar on the public site for all logged-in users.
	 *
	 * @param bool $show Whether to show the bar.
	 */
	public static function hide_frontend_admin_bar( bool $show ): bool {
		if ( is_admin() ) {
			return $show;
		}
		return false;
	}

	/**
	 * Point login links to the branded frontend page.
	 *
	 * @param string $login_url    Default login URL.
	 * @param string $redirect     Redirect after login.
	 * @param bool   $force_reauth Force reauth.
	 */
	public static function frontend_login_url( string $login_url, string $redirect, bool $force_reauth ): string {
		$url = home_url( '/belepes/' );
		if ( $redirect ) {
			$url = add_query_arg( 'redirect_to', $redirect, $url );
		}
		if ( $force_reauth ) {
			$url = add_query_arg( 'reauth', '1', $url );
		}
		return $url;
	}

	/**
	 * After logout, land on frontend login.
	 *
	 * @param string           $redirect_to Redirect URL.
	 * @param string           $requested   Requested redirect.
	 * @param WP_User|WP_Error $user        User.
	 */
	public static function frontend_logout_redirect( string $redirect_to, string $requested, $user ): string {
		return add_query_arg( 'login', 'loggedout', home_url( '/belepes/' ) );
	}

	/**
	 * Send GET visitors away from wp-login.php to the frontend form.
	 * POST still hits wp-login.php so authentication works.
	 */
	public static function redirect_wp_login_get(): void {
		if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
			return;
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login';
		$allowed = array( 'logout', 'postpass', 'confirmaction', 'rp', 'resetpass', 'lostpassword', 'retrievepassword' );
		if ( in_array( $action, $allowed, true ) ) {
			return;
		}

		$redirect = isset( $_REQUEST['redirect_to'] )
			? wp_unslash( $_REQUEST['redirect_to'] )
			: home_url( '/ugyfelportal/' );
		$redirect = wp_validate_redirect( $redirect, home_url( '/ugyfelportal/' ) );

		wp_safe_redirect(
			add_query_arg(
				array(
					'redirect_to' => $redirect,
				),
				home_url( '/belepes/' )
			)
		);
		exit;
	}

	/**
	 * Failed login → frontend page with error flag.
	 *
	 * @param string $username Username.
	 */
	public static function redirect_failed_login( string $username ): void {
		if ( empty( $_POST['wp-submit'] ) ) {
			return;
		}

		$redirect = isset( $_POST['redirect_to'] )
			? wp_unslash( $_POST['redirect_to'] )
			: home_url( '/ugyfelportal/' );
		$redirect = wp_validate_redirect( $redirect, home_url( '/ugyfelportal/' ) );

		wp_safe_redirect(
			add_query_arg(
				array(
					'login'       => 'failed',
					'redirect_to' => $redirect,
				),
				home_url( '/belepes/' )
			)
		);
		exit;
	}

	/**
	 * Empty credentials → frontend page.
	 *
	 * @param WP_User|WP_Error|null $user     User.
	 * @param string                $username Username.
	 * @param string                $password Password.
	 * @return WP_User|WP_Error|null
	 */
	public static function redirect_empty_login( $user, string $username, string $password ) {
		if ( empty( $_POST['wp-submit'] ) ) {
			return $user;
		}

		if ( '' !== trim( $username ) && '' !== $password ) {
			return $user;
		}

		$redirect = isset( $_POST['redirect_to'] )
			? wp_unslash( $_POST['redirect_to'] )
			: home_url( '/ugyfelportal/' );
		$redirect = wp_validate_redirect( $redirect, home_url( '/ugyfelportal/' ) );

		wp_safe_redirect(
			add_query_arg(
				array(
					'login'       => 'empty',
					'redirect_to' => $redirect,
				),
				home_url( '/belepes/' )
			)
		);
		exit;
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
	 * @param string           $redirect_to Redirect URL.
	 * @param string           $requested   Requested redirect.
	 * @param WP_User|WP_Error $user        User object or error (failed login / reauth).
	 */
	public static function client_login_redirect( string $redirect_to, string $requested, $user ): string {
		if ( ! $user instanceof WP_User ) {
			return $redirect_to;
		}

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
