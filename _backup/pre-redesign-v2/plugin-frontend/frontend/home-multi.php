<?php
/**
 * Homepage – multi-service callout (§7.3).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="bma-section bma-home-multi" aria-labelledby="bma-multi-title">
	<div class="bma-container bma-home-multi-inner">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
		<h2 id="bma-multi-title"><?php esc_html_e( 'Egy projekt ritkán csak egyetlen feladatból áll', 'bma-ajanlatkeres' ); ?></h2>
		<p>
			<?php esc_html_e( 'Egy építési vagy fejlesztési projekt során a tervezés, az engedélyeztetés, a közműcsatlakozások és a műszaki dokumentáció gyakran egymásra épülnek.', 'bma-ajanlatkeres' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Az ajánlatkérés során ezért több szolgáltatást és azokon belül több részfeladatot is kiválaszthat. Nem szükséges minden műszaki részletet előre ismernie. Adja meg a rendelkezésére álló információkat és dokumentumokat, mi pedig ezek alapján felmérjük a projektet és elkészítjük az ajánlatot.', 'bma-ajanlatkeres' ); ?>
		</p>
		<p class="bma-section-cta bma-section-cta--left">
			<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-accent">
				<?php esc_html_e( 'Szolgáltatások kiválasztása', 'bma-ajanlatkeres' ); ?>
			</a>
		</p>
	</div>
</section>
