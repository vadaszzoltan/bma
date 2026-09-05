<section class="bma-section bma-login-prompt">
	<div class="bma-container bma-container-narrow">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Ügyfélterület', 'bma-ajanlatkeres' ); ?></p>
		<h1><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></h1>
		<p class="bma-section-intro"><?php esc_html_e( 'Az ügyfélportál eléréséhez bejelentkezés szükséges.', 'bma-ajanlatkeres' ); ?></p>
		<div class="bma-form-actions">
			<a href="<?php echo esc_url( wp_login_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' ) ) ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Bejelentkezés', 'bma-ajanlatkeres' ); ?></a>
			<a href="<?php echo esc_url( wp_lostpassword_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'belepes' ) : home_url( '/belepes/' ) ) ) ); ?>" class="bma-link"><?php esc_html_e( 'Elfelejtett jelszó', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</div>
</section>
