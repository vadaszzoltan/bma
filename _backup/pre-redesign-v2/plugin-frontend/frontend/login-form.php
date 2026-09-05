<?php
defined( 'ABSPATH' ) || exit;

$redirect_to = isset( $_GET['redirect_to'] )
	? wp_unslash( $_GET['redirect_to'] )
	: home_url( '/ugyfelportal/' );
$redirect_to = wp_validate_redirect( $redirect_to, home_url( '/ugyfelportal/' ) );

$login_status = sanitize_key( wp_unslash( $_GET['login'] ?? '' ) );

if ( is_user_logged_in() ) {
	?>
	<section class="bma-section bma-login-page">
		<div class="bma-container bma-container-narrow">
			<p class="bma-section-eyebrow"><?php esc_html_e( 'Ügyfélterület', 'bma-ajanlatkeres' ); ?></p>
			<h1><?php esc_html_e( 'Már be van jelentkezve', 'bma-ajanlatkeres' ); ?></h1>
			<p class="bma-section-intro"><?php esc_html_e( 'Folytathatja az ügyfélportálon.', 'bma-ajanlatkeres' ); ?></p>
			<a href="<?php echo esc_url( $redirect_to ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Tovább', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</section>
	<?php
	return;
}
?>
<section class="bma-section bma-login-page" aria-labelledby="bma-login-title">
	<div class="bma-container bma-container-narrow">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Ügyfélterület', 'bma-ajanlatkeres' ); ?></p>
		<h1 id="bma-login-title"><?php esc_html_e( 'Bejelentkezés', 'bma-ajanlatkeres' ); ?></h1>
		<p class="bma-section-intro"><?php esc_html_e( 'Jelentkezzen be ügyfélfiókjával az ajánlatkérések és dokumentumok megtekintéséhez.', 'bma-ajanlatkeres' ); ?></p>

		<?php if ( 'failed' === $login_status ) : ?>
			<div class="bma-form-errors" role="alert">
				<?php esc_html_e( 'Hibás felhasználónév vagy jelszó. Kérjük, próbálja újra.', 'bma-ajanlatkeres' ); ?>
			</div>
		<?php elseif ( 'empty' === $login_status ) : ?>
			<div class="bma-form-errors" role="alert">
				<?php esc_html_e( 'Adja meg a felhasználónevet és a jelszót.', 'bma-ajanlatkeres' ); ?>
			</div>
		<?php elseif ( 'loggedout' === $login_status ) : ?>
			<div class="bma-notice" role="status">
				<?php esc_html_e( 'Sikeresen kijelentkezett.', 'bma-ajanlatkeres' ); ?>
			</div>
		<?php endif; ?>

		<div class="bma-login-card">
			<?php
			wp_login_form(
				array(
					'redirect'       => $redirect_to,
					'form_id'        => 'bma-loginform',
					'label_username' => __( 'E-mail vagy felhasználónév', 'bma-ajanlatkeres' ),
					'label_password' => __( 'Jelszó', 'bma-ajanlatkeres' ),
					'label_remember' => __( 'Emlékezzen rám', 'bma-ajanlatkeres' ),
					'label_log_in'   => __( 'Bejelentkezés', 'bma-ajanlatkeres' ),
					'remember'       => true,
					'value_remember' => true,
				)
			);
			?>
			<p class="bma-login-links">
				<a href="<?php echo esc_url( wp_lostpassword_url( home_url( '/belepes/' ) ) ); ?>"><?php esc_html_e( 'Elfelejtett jelszó', 'bma-ajanlatkeres' ); ?></a>
				<span aria-hidden="true"> · </span>
				<a href="<?php echo esc_url( home_url( '/ajanlatkeres/' ) ); ?>"><?php esc_html_e( 'Új ajánlatkérés', 'bma-ajanlatkeres' ); ?></a>
			</p>
		</div>
	</div>
</section>
