<?php
/**
 * 404 – oldal nem található
 *
 * @package BMA_Engineering
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="bma-section bma-error-page" aria-labelledby="bma-error-title">
	<div class="bma-container bma-container-narrow">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Hiba 404', 'bma-engineering' ); ?></p>
		<p class="bma-error-code" aria-hidden="true">404</p>
		<h1 id="bma-error-title"><?php esc_html_e( 'Az oldal nem található', 'bma-engineering' ); ?></h1>
		<p class="bma-section-intro">
			<?php esc_html_e( 'A keresett oldal nem létezik, áthelyezték, vagy elírta a címet. Válasszon az alábbi lehetőségek közül.', 'bma-engineering' ); ?>
		</p>
		<div class="bma-form-actions">
			<a class="bma-btn bma-btn-accent bma-btn-lg" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Főoldal', 'bma-engineering' ); ?>
			</a>
			<a class="bma-btn" href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>">
				<?php esc_html_e( 'Szolgáltatások', 'bma-engineering' ); ?>
			</a>
			<a class="bma-btn" href="<?php echo esc_url( home_url( '/kapcsolat/' ) ); ?>">
				<?php esc_html_e( 'Kapcsolat', 'bma-engineering' ); ?>
			</a>
		</div>
	</div>
</section>
<?php
get_footer();
