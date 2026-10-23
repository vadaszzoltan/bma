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
		add_action( 'admin_init', array( self::class, 'handle_demo_reseed' ) );
	}

	/**
	 * Reload demo content from settings page.
	 */
	public static function handle_demo_reseed(): void {
		if ( ! isset( $_POST['bma_reseed_demo'] ) || ! current_user_can( 'bma_manage_settings' ) ) {
			return;
		}

		check_admin_referer( 'bma_reseed_demo', 'bma_reseed_nonce' );

		require_once BMA_AJ_PLUGIN_DIR . 'includes/class-seeder.php';
		BMA_AJ_Seeder::reseed();

		add_settings_error(
			'bma_settings',
			'bma_reseeded',
			__( 'A demo tartalom sikeresen betöltve. Frissítse a főoldalt a böngészőben.', 'bma-ajanlatkeres' ),
			'success'
		);
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
		register_setting( 'bma_settings', 'bma_company_name' );
		register_setting( 'bma_settings', 'bma_company_cui' );
		register_setting( 'bma_settings', 'bma_contact_phone' );
		register_setting( 'bma_settings', 'bma_contact_email' );
		register_setting( 'bma_settings', 'bma_contact_address' );
		register_setting( 'bma_settings', 'bma_opening_hours' );
		register_setting( 'bma_settings', 'bma_response_days', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'bma_settings', 'bma_lead_time_weeks', array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'bma_settings', 'bma_map_lat' );
		register_setting( 'bma_settings', 'bma_map_lng' );
		register_setting( 'bma_settings', 'bma_map_zoom' );
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
		register_setting( 'bma_settings', 'bma_stripe_publishable_key' );
		register_setting( 'bma_settings', 'bma_stripe_secret_key' );
		register_setting( 'bma_settings', 'bma_stripe_webhook_secret' );
		register_setting( 'bma_settings', 'bma_stripe_invoice_creation' );
		register_setting( 'bma_settings', 'bma_stripe_automatic_tax' );
		register_setting( 'bma_settings', 'bma_bank_beneficiary' );
		register_setting( 'bma_settings', 'bma_bank_iban' );
		register_setting( 'bma_settings', 'bma_bank_bic' );
		register_setting( 'bma_settings', 'bma_bank_name' );
		register_setting( 'bma_settings', 'bma_default_currency' );
		register_setting(
			'bma_settings',
			BMA_AJ_Appearance::OPTION_COLORS,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'BMA_AJ_Appearance', 'sanitize_colors' ),
				'default'           => BMA_AJ_Appearance::default_colors(),
			)
		);
		register_setting(
			'bma_settings',
			BMA_AJ_Appearance::OPTION_FONTS,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'BMA_AJ_Appearance', 'sanitize_fonts' ),
				'default'           => BMA_AJ_Appearance::default_fonts(),
			)
		);
		register_setting(
			'bma_settings',
			BMA_AJ_Appearance::OPTION_RADIUS,
			array(
				'type'              => 'integer',
				'sanitize_callback' => static function ( $v ) {
					return max( 0, min( 24, absint( $v ) ) );
				},
				'default'           => 10,
			)
		);
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
			$result = BMA_AJ_Quotes::save(
				$id,
				array(
					'id'           => absint( $_POST['quote_id'] ?? 0 ),
					'net_amount'   => wp_unslash( $_POST['net_amount'] ?? 0 ),
					'tax_amount'   => wp_unslash( $_POST['tax_amount'] ?? 0 ),
					'gross_amount' => wp_unslash( $_POST['gross_amount'] ?? 0 ),
					'currency'     => sanitize_text_field( wp_unslash( $_POST['currency'] ?? 'EUR' ) ),
					'valid_until'  => sanitize_text_field( wp_unslash( $_POST['valid_until'] ?? '' ) ),
					'note'         => sanitize_textarea_field( wp_unslash( $_POST['quote_note'] ?? '' ) ),
					'status'       => 'sent',
				)
			);

			if ( is_wp_error( $result ) ) {
				add_settings_error( 'bma_quote', 'bma_quote_error', $result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'bma_quote', 'bma_quote_saved', __( 'Ajánlat elmentve és elküldve az ügyfélnek.', 'bma-ajanlatkeres' ), 'success' );
			}
		}

		if ( isset( $_POST['mark_bank_paid'] ) ) {
			$quote = BMA_AJ_Quotes::get_latest_for_request( $id );
			if ( $quote && 'bank_pending' === ( $quote->payment_status ?? '' ) ) {
				BMA_AJ_Quotes::mark_paid( (int) $quote->id, 'bank_transfer' );
			}
		}

		if ( isset( $_POST['delete_document'] ) ) {
			$doc_id = absint( $_POST['document_id'] ?? 0 );
			$result = BMA_AJ_Documents::delete( $doc_id, $id );
			if ( is_wp_error( $result ) ) {
				add_settings_error( 'bma_quote', 'bma_doc_delete_error', $result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'bma_quote', 'bma_doc_deleted', __( 'Dokumentum törölve.', 'bma-ajanlatkeres' ), 'success' );
			}
		}

		if ( ! empty( $_FILES['admin_deliverable']['name'] ) ) {
			$item_id = absint( $_POST['deliverable_item_id'] ?? 0 );
			$result  = BMA_AJ_Documents::handle_upload(
				$_FILES['admin_deliverable'],
				$id,
				sanitize_text_field( wp_unslash( $_POST['deliverable_type'] ?? 'plan' ) ),
				'admin',
				$item_id
			);
			if ( is_wp_error( $result ) ) {
				add_settings_error( 'bma_quote', 'bma_doc_error', $result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'bma_quote', 'bma_doc_saved', __( 'Elkészült terv / dokumentum feltöltve.', 'bma-ajanlatkeres' ), 'success' );
			}
		}

		// Legacy field name support.
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
