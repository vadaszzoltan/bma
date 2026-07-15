<?php
/**
 * Admin interface.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Admin {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'register_menus' ) );
		add_action( 'admin_init', array( self::class, 'register_settings' ) );
	}

	/**
	 * Register admin menus.
	 */
	public static function register_menus(): void {
		add_menu_page(
			__( 'BMA Ajánlatkérés', 'bma-ajanlatkeres' ),
			__( 'BMA Ajánlatkérés', 'bma-ajanlatkeres' ),
			'bma_manage_requests',
			'bma-requests',
			array( self::class, 'render_requests_page' ),
			'dashicons-clipboard',
			30
		);

		add_submenu_page( 'bma-requests', __( 'Ajánlatkérések', 'bma-ajanlatkeres' ), __( 'Ajánlatkérések', 'bma-ajanlatkeres' ), 'bma_manage_requests', 'bma-requests', array( self::class, 'render_requests_page' ) );
		add_submenu_page( 'bma-requests', __( 'Státuszok', 'bma-ajanlatkeres' ), __( 'Státuszok', 'bma-ajanlatkeres' ), 'bma_manage_statuses', 'bma-statuses', array( self::class, 'render_statuses_page' ) );
		add_submenu_page( 'bma-requests', __( 'Dinamikus mezők', 'bma-ajanlatkeres' ), __( 'Dinamikus mezők', 'bma-ajanlatkeres' ), 'bma_manage_services', 'bma-fields', array( self::class, 'render_fields_page' ) );
		add_submenu_page( 'bma-requests', __( 'Szolgáltatáselemek', 'bma-ajanlatkeres' ), __( 'Szolgáltatáselemek', 'bma-ajanlatkeres' ), 'bma_manage_services', 'bma-service-items', array( self::class, 'render_service_items_page' ) );
		add_submenu_page( 'bma-requests', __( 'E-mail sablonok', 'bma-ajanlatkeres' ), __( 'E-mail sablonok', 'bma-ajanlatkeres' ), 'bma_manage_settings', 'bma-emails', array( self::class, 'render_emails_page' ) );
		add_submenu_page( 'bma-requests', __( 'Beállítások', 'bma-ajanlatkeres' ), __( 'Beállítások', 'bma-ajanlatkeres' ), 'bma_manage_settings', 'bma-settings', array( self::class, 'render_settings_page' ) );
	}

	/**
	 * Register settings.
	 */
	public static function register_settings(): void {
		register_setting( 'bma_settings', 'bma_admin_email' );
		register_setting( 'bma_settings', 'bma_contact_phone' );
		register_setting( 'bma_settings', 'bma_contact_email' );
		register_setting( 'bma_settings', 'bma_contact_address' );
		register_setting( 'bma_settings', 'bma_social_facebook' );
		register_setting( 'bma_settings', 'bma_social_linkedin' );
		register_setting( 'bma_settings', 'bma_social_instagram' );
		register_setting( 'bma_settings', 'bma_social_x' );
		register_setting( 'bma_settings', 'bma_social_youtube' );
		register_setting( 'bma_settings', 'bma_ga4_id' );
		register_setting( 'bma_settings', 'bma_gtm_id' );
		register_setting( 'bma_settings', 'bma_smtp_host' );
		register_setting( 'bma_settings', 'bma_smtp_port' );
		register_setting( 'bma_settings', 'bma_smtp_from' );
		register_setting( 'bma_settings', 'bma_smtp_from_name' );
	}

	/**
	 * Render requests list/detail page.
	 */
	public static function render_requests_page(): void {
		if ( ! current_user_can( 'bma_manage_requests' ) ) {
			wp_die( esc_html__( 'Nincs jogosultsága.', 'bma-ajanlatkeres' ) );
		}

		$action = sanitize_text_field( wp_unslash( $_GET['action'] ?? 'list' ) );
		$id     = absint( $_GET['id'] ?? 0 );

		if ( 'view' === $action && $id ) {
			self::handle_request_actions( $id );
			include BMA_AJ_PLUGIN_DIR . 'templates/admin/request-detail.php';
			return;
		}

		include BMA_AJ_PLUGIN_DIR . 'templates/admin/requests-list.php';
	}

	/**
	 * Handle POST actions on request detail.
	 *
	 * @param int $id Request ID.
	 */
	private static function handle_request_actions( int $id ): void {
		if ( ! isset( $_POST['bma_admin_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_admin_nonce'] ) ), 'bma_admin_request' ) ) {
			return;
		}

		if ( isset( $_POST['change_status'] ) ) {
			$new_status = absint( $_POST['status_id'] ?? 0 );
			$note       = sanitize_textarea_field( wp_unslash( $_POST['status_note'] ?? '' ) );
			BMA_AJ_Statuses::change_status( $id, $new_status, $note );
		}

		if ( isset( $_POST['save_admin_note'] ) ) {
			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . 'bma_quote_requests',
				array( 'admin_note' => sanitize_textarea_field( wp_unslash( $_POST['admin_note'] ?? '' ) ) ),
				array( 'id' => $id )
			);
		}

		if ( isset( $_POST['save_quote'] ) ) {
			BMA_AJ_Quotes::save(
				$id,
				array(
					'quote_number' => sanitize_text_field( wp_unslash( $_POST['quote_number'] ?? '' ) ),
					'net_amount'   => floatval( $_POST['net_amount'] ?? 0 ),
					'tax_amount'   => floatval( $_POST['tax_amount'] ?? 0 ),
					'gross_amount' => floatval( $_POST['gross_amount'] ?? 0 ),
					'currency'     => sanitize_text_field( wp_unslash( $_POST['currency'] ?? 'HUF' ) ),
					'valid_until'  => sanitize_text_field( wp_unslash( $_POST['valid_until'] ?? '' ) ),
					'note'         => sanitize_textarea_field( wp_unslash( $_POST['quote_note'] ?? '' ) ),
					'status'       => 'sent',
				)
			);
		}

		if ( ! empty( $_FILES['admin_document']['name'] ) ) {
			BMA_AJ_Documents::handle_upload(
				$_FILES['admin_document'],
				$id,
				sanitize_text_field( wp_unslash( $_POST['doc_type'] ?? 'other' ) ),
				'admin'
			);
		}
	}

	/**
	 * Render statuses page.
	 */
	public static function render_statuses_page(): void {
		include BMA_AJ_PLUGIN_DIR . 'templates/admin/statuses.php';
	}

	/**
	 * Render fields page.
	 */
	public static function render_fields_page(): void {
		include BMA_AJ_PLUGIN_DIR . 'templates/admin/fields.php';
	}

	/**
	 * Render service items page.
	 */
	public static function render_service_items_page(): void {
		include BMA_AJ_PLUGIN_DIR . 'templates/admin/service-items.php';
	}

	/**
	 * Render emails page.
	 */
	public static function render_emails_page(): void {
		include BMA_AJ_PLUGIN_DIR . 'templates/admin/emails.php';
	}

	/**
	 * Render settings page.
	 */
	public static function render_settings_page(): void {
		include BMA_AJ_PLUGIN_DIR . 'templates/admin/settings.php';
	}
}
