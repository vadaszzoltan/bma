<section class="bma-section bma-login-prompt">
	<div class="bma-container">
		<h1><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></h1>
		<p><?php esc_html_e( 'Az ügyfélportál eléréséhez bejelentkezés szükséges.', 'bma-ajanlatkeres' ); ?></p>
		<a href="<?php echo esc_url( wp_login_url( home_url( '/ugyfelportal/' ) ) ); ?>" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Bejelentkezés', 'bma-ajanlatkeres' ); ?></a>
		<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="bma-link"><?php esc_html_e( 'Elfelejtett jelszó', 'bma-ajanlatkeres' ); ?></a>
	</div>
</section>
