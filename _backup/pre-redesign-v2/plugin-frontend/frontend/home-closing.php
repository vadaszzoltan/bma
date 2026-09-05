<?php
/**
 * Homepage – closing CTA (§7.8).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="bma-section bma-home-closing" aria-labelledby="bma-closing-title">
	<div class="bma-container bma-home-closing-inner">
		<h2 id="bma-closing-title"><?php esc_html_e( 'Kérjen ajánlatot a projektjéhez', 'bma-ajanlatkeres' ); ?></h2>
		<p>
			<?php esc_html_e( 'Válassza ki a szükséges szolgáltatásokat és részfeladatokat, adja meg a projekt alapadatait, és töltse fel a rendelkezésére álló dokumentumokat. Ezek alapján pontosabb ajánlatot tudunk készíteni.', 'bma-ajanlatkeres' ); ?>
		</p>
		<p class="bma-home-closing-actions">
			<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg">
				<?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?>
			</a>
		</p>
		<p class="bma-home-closing-alt">
			<?php esc_html_e( 'Ha még nem tudja pontosan, mely szolgáltatásokra van szüksége, vegye fel velünk a kapcsolatot.', 'bma-ajanlatkeres' ); ?>
		</p>
		<p>
			<a href="<?php echo esc_url( home_url( '/kapcsolat/' ) ); ?>" class="bma-btn bma-btn-outline">
				<?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?>
			</a>
		</p>
	</div>
</section>
