<?php
/**
 * Main plugin bootstrap.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Get instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize plugin modules.
	 */
	public function init(): void {
		load_plugin_textdomain( 'bma-ajanlatkeres', false, dirname( BMA_AJ_PLUGIN_BASENAME ) . '/languages' );

		BMA_AJ_Roles::init();
		BMA_AJ_Service_CPT::init();
		BMA_AJ_Reference_CPT::init();
		BMA_AJ_Statuses::init();
		BMA_AJ_Email_Templates::init();
		BMA_AJ_Documents::init();
		BMA_AJ_Cart::init();
		BMA_AJ_Quote_Requests::init();
		BMA_AJ_Quotes::init();
		BMA_AJ_Security::init();
		BMA_AJ_SMTP::init();
		BMA_AJ_Shortcodes::init();
		BMA_AJ_Frontend::init();
		BMA_AJ_Admin::init();
		BMA_AJ_REST::init();
		BMA_AJ_Analytics::init();
		BMA_AJ_Cookie_Consent::init();

		add_action( 'admin_post_bma_contact_submit', array( $this, 'handle_contact_form' ) );
		add_action( 'admin_post_nopriv_bma_contact_submit', array( $this, 'handle_contact_form' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Public assets.
	 */
	public function enqueue_public_assets(): void {
		wp_enqueue_style(
			'bma-aj-public',
			BMA_AJ_PLUGIN_URL . 'assets/css/public.css',
			array(),
			BMA_AJ_VERSION
		);

		wp_enqueue_script(
			'bma-aj-public',
			BMA_AJ_PLUGIN_URL . 'assets/js/public.js',
			array( 'jquery' ),
			BMA_AJ_VERSION,
			true
		);

		wp_localize_script(
			'bma-aj-public',
			'bmaAj',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'restUrl'   => rest_url( 'bma/v1/' ),
				'nonce'     => wp_create_nonce( 'bma_aj_public' ),
				'restNonce' => wp_create_nonce( 'wp_rest' ),
				'strings'   => array(
					'confirmRemove' => __( 'Biztosan eltávolítja ezt a szolgáltatást?', 'bma-ajanlatkeres' ),
					'uploadError'   => __( 'A fájl feltöltése sikertelen.', 'bma-ajanlatkeres' ),
					'required'      => __( 'Ez a mező kötelező.', 'bma-ajanlatkeres' ),
				),
			)
		);
	}

	/**
	 * Admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( string $hook ): void {
		if ( strpos( $hook, 'bma-' ) === false && strpos( $hook, 'bma_' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'bma-aj-admin',
			BMA_AJ_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			BMA_AJ_VERSION
		);

		wp_enqueue_script(
			'bma-aj-admin',
			BMA_AJ_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			BMA_AJ_VERSION,
			true
		);
	}

	/**
	 * Handle contact form submission.
	 */
	public function handle_contact_form(): void {
		if ( ! isset( $_POST['bma_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_contact_nonce'] ) ), 'bma_contact_form' ) ) {
			wp_die( esc_html__( 'Érvénytelen kérés.', 'bma-ajanlatkeres' ) );
		}

		$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

		if ( $name && is_email( $email ) && $message ) {
			wp_mail(
				get_option( 'bma_admin_email', get_option( 'admin_email' ) ),
				sprintf( '[BMA Kapcsolat] %s', $name ),
				sprintf( "Név: %s\nE-mail: %s\n\n%s", $name, $email, $message ),
				array( 'Reply-To: ' . $email )
			);
		}

		wp_safe_redirect( add_query_arg( 'contact', 'sent', wp_get_referer() ?: home_url( '/kapcsolat/' ) ) );
		exit;
	}
}
