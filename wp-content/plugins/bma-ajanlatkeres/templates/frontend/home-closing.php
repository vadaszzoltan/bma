<?php
/**
 * Homepage closing CTA (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$svc_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) );
$con_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'kapcsolat' ) : home_url( '/kapcsolat/' ) );
?>
<section class="closing" aria-labelledby="bma-closing-title">
	<div class="closing-inner">
		<div>
			<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Következő lépés', 'bma-ajanlatkeres' ); ?></p>
			<h2 class="h2" id="bma-closing-title"><?php esc_html_e( 'Kérjen ajánlatot a projektjéhez', 'bma-ajanlatkeres' ); ?></h2>
			<p>
				<?php esc_html_e( 'Válassza ki a szükséges szolgáltatásokat, adja meg a projekt alapadatait, és töltse fel a rendelkezésére álló dokumentumokat. Ezek alapján pontosabb ajánlatot tudunk készíteni.', 'bma-ajanlatkeres' ); ?>
			</p>
		</div>
		<div class="closing-actions">
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
			<a href="<?php echo esc_url( $con_url ); ?>" class="btn btn--ghost-light"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></a>
			<span class="tiny" style="color:#8fa2c4;text-align:center">
				<?php esc_html_e( 'Ha még nem tudja pontosan, mire van szüksége, hívjon minket.', 'bma-ajanlatkeres' ); ?>
			</span>
		</div>
	</div>
</section>
