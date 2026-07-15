<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1><?php esc_html_e( 'BMA beállítások', 'bma-ajanlatkeres' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'bma_settings' ); ?>
		<h2><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th><?php esc_html_e( 'Admin értesítési e-mail', 'bma-ajanlatkeres' ); ?></th><td><input type="email" name="bma_admin_email" value="<?php echo esc_attr( get_option( 'bma_admin_email' ) ); ?>" class="regular-text"></td></tr>
			<tr><th><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_contact_phone" value="<?php echo esc_attr( get_option( 'bma_contact_phone' ) ); ?>" class="regular-text"></td></tr>
			<tr><th><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?></th><td><input type="email" name="bma_contact_email" value="<?php echo esc_attr( get_option( 'bma_contact_email' ) ); ?>" class="regular-text"></td></tr>
			<tr><th><?php esc_html_e( 'Cím', 'bma-ajanlatkeres' ); ?></th><td><textarea name="bma_contact_address" rows="2" class="large-text"><?php echo esc_textarea( get_option( 'bma_contact_address' ) ); ?></textarea></td></tr>
		</table>

		<h2><?php esc_html_e( 'Közösségi média', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th>Facebook</th><td><input type="url" name="bma_social_facebook" value="<?php echo esc_url( get_option( 'bma_social_facebook' ) ); ?>" class="regular-text"></td></tr>
			<tr><th>LinkedIn</th><td><input type="url" name="bma_social_linkedin" value="<?php echo esc_url( get_option( 'bma_social_linkedin' ) ); ?>" class="regular-text"></td></tr>
			<tr><th>Instagram</th><td><input type="url" name="bma_social_instagram" value="<?php echo esc_url( get_option( 'bma_social_instagram' ) ); ?>" class="regular-text"></td></tr>
			<tr><th>X</th><td><input type="url" name="bma_social_x" value="<?php echo esc_url( get_option( 'bma_social_x' ) ); ?>" class="regular-text"></td></tr>
			<tr><th>YouTube</th><td><input type="url" name="bma_social_youtube" value="<?php echo esc_url( get_option( 'bma_social_youtube' ) ); ?>" class="regular-text"></td></tr>
		</table>

		<h2><?php esc_html_e( 'Analitika', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th>GA4 ID</th><td><input type="text" name="bma_ga4_id" value="<?php echo esc_attr( get_option( 'bma_ga4_id' ) ); ?>" class="regular-text" placeholder="G-XXXXXXXX"></td></tr>
			<tr><th>GTM ID</th><td><input type="text" name="bma_gtm_id" value="<?php echo esc_attr( get_option( 'bma_gtm_id' ) ); ?>" class="regular-text" placeholder="GTM-XXXXXXX"></td></tr>
		</table>

		<h2><?php esc_html_e( 'SMTP', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th>Host</th><td><input type="text" name="bma_smtp_host" value="<?php echo esc_attr( get_option( 'bma_smtp_host' ) ); ?>" class="regular-text"></td></tr>
			<tr><th>Port</th><td><input type="number" name="bma_smtp_port" value="<?php echo esc_attr( get_option( 'bma_smtp_port', 587 ) ); ?>"></td></tr>
			<tr><th>Feladó e-mail</th><td><input type="email" name="bma_smtp_from" value="<?php echo esc_attr( get_option( 'bma_smtp_from' ) ); ?>" class="regular-text"></td></tr>
			<tr><th>Feladó név</th><td><input type="text" name="bma_smtp_from_name" value="<?php echo esc_attr( get_option( 'bma_smtp_from_name', 'BMA Engineering' ) ); ?>" class="regular-text"></td></tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
