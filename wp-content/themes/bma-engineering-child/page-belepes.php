<?php
/** Template Name: Bejelentkezés */
get_header();
?>
<section class="bma-section">
	<div class="bma-container">
		<h1><?php esc_html_e( 'Bejelentkezés', 'bma-engineering' ); ?></h1>
		<?php wp_login_form( array( 'redirect' => home_url( '/ugyfelportal/' ) ) ); ?>
		<p><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Elfelejtett jelszó', 'bma-engineering' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
