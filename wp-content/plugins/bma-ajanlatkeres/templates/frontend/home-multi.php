<?php
/**
 * Homepage multi-service callout (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$svc_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) );
$con_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'kapcsolat' ) : home_url( '/kapcsolat/' ) );
?>
<section class="bma-section bma-section--bg bma-home-multi" aria-labelledby="bma-multi-title">
	<div class="bma-container">
		<div class="multi">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
				<h2 class="h2" id="bma-multi-title"><?php esc_html_e( 'Egy projekt ritkán csak egyetlen feladatból áll', 'bma-ajanlatkeres' ); ?></h2>
				<p style="margin:18px 0 0;font-size:18px;line-height:1.6;color:#4a5a7d">
					<?php esc_html_e( 'Egy építési vagy fejlesztési projekt során a tervezés, az engedélyeztetés, a közműcsatlakozások és a műszaki dokumentáció gyakran egymásra épül.', 'bma-ajanlatkeres' ); ?>
				</p>
				<p style="margin:14px 0 0;font-size:18px;line-height:1.6;color:#4a5a7d">
					<?php esc_html_e( 'Nem szükséges minden műszaki részletet előre ismernie. Adja meg a rendelkezésére álló információkat és dokumentumokat, mi pedig ezek alapján felmérjük a projektet és elkészítjük az ajánlatot.', 'bma-ajanlatkeres' ); ?>
				</p>
				<div style="display:flex;gap:14px;margin-top:28px;flex-wrap:wrap">
					<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Szolgáltatások kiválasztása', 'bma-ajanlatkeres' ); ?></a>
					<a href="<?php echo esc_url( $con_url ); ?>" class="btn btn--outline"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></a>
				</div>
			</div>
			<div class="stackcard">
				<div class="stackcard-head"><?php esc_html_e( 'Példa: új lakóépület – összeállított ajánlatkérés', 'bma-ajanlatkeres' ); ?></div>
				<div class="stackrow"><span class="stackrow-num">1</span><span class="stackrow-main"><strong><?php esc_html_e( 'Vízbekötési terv', 'bma-ajanlatkeres' ); ?></strong><span><?php esc_html_e( 'Tervezés', 'bma-ajanlatkeres' ); ?></span></span></div>
				<div class="stackrow"><span class="stackrow-num">2</span><span class="stackrow-main"><strong><?php esc_html_e( 'Szennyvízbekötési terv', 'bma-ajanlatkeres' ); ?></strong><span><?php esc_html_e( 'Tervezés', 'bma-ajanlatkeres' ); ?></span></span></div>
				<div class="stackrow"><span class="stackrow-num">3</span><span class="stackrow-main"><strong><?php esc_html_e( 'Építési engedély előkészítés', 'bma-ajanlatkeres' ); ?></strong><span><?php esc_html_e( 'Engedélyeztetés', 'bma-ajanlatkeres' ); ?></span></span></div>
				<div class="stackrow"><span class="stackrow-num">4</span><span class="stackrow-main"><strong><?php esc_html_e( 'Vízmű egyeztetés', 'bma-ajanlatkeres' ); ?></strong><span><?php esc_html_e( 'Közmű-koordináció', 'bma-ajanlatkeres' ); ?></span></span></div>
				<div class="stackcard-foot">
					<span class="small muted"><?php esc_html_e( '4 tétel · 1 ajánlatkérés', 'bma-ajanlatkeres' ); ?></span>
					<span class="badge badge--tint"><?php esc_html_e( 'Egy ajánlatban', 'bma-ajanlatkeres' ); ?></span>
				</div>
			</div>
		</div>
	</div>
</section>
