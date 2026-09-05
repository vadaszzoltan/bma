<?php
/**
 * Homepage about (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$company = BMA_AJ_Company::get( 'company_name' );
$cui     = BMA_AJ_Company::get( 'cui' );
$hours   = BMA_AJ_Company::get( 'opening_hours' );
$con_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'kapcsolat' ) : home_url( '/kapcsolat/' ) );
?>
<section class="bma-section bma-section--bg bma-home-about" aria-labelledby="bma-about-title">
	<div class="bma-container">
		<div class="about">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Rólunk', 'bma-ajanlatkeres' ); ?></p>
				<h2 class="h2" id="bma-about-title"><?php echo esc_html( $company ?: 'BMA Engineering' ); ?></h2>
				<p style="margin:18px 0 0;font-size:18px;line-height:1.6;color:#4a5a7d">
					<?php esc_html_e( 'A BMA Engineering mérnöki tervezési, engedélyeztetési, közmű-koordinációs és műszaki dokumentációs feladatokban támogatja ügyfeleit.', 'bma-ajanlatkeres' ); ?>
				</p>
				<p style="margin:14px 0 0;font-size:18px;line-height:1.6;color:#4a5a7d">
					<?php esc_html_e( 'Munkánk során arra törekszünk, hogy a projekt műszaki követelményei, a szükséges dokumentációk és a kapcsolódó egyeztetések egy átlátható és követhető folyamat részeként legyenek kezelhetők.', 'bma-ajanlatkeres' ); ?>
				</p>
				<div style="margin-top:26px">
					<a href="<?php echo esc_url( $con_url ); ?>" class="btn btn--outline"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></a>
				</div>
			</div>
			<div class="factbox">
				<div class="factrow"><span><?php esc_html_e( 'Székhely', 'bma-ajanlatkeres' ); ?></span><strong><?php echo wp_kses_post( __( 'Odorheiu Secuiesc,<br>Hargita, Románia', 'bma-ajanlatkeres' ) ); ?></strong></div>
				<div class="factrow"><span><?php esc_html_e( 'CUI', 'bma-ajanlatkeres' ); ?></span><strong><?php echo esc_html( $cui ?: '40593519' ); ?></strong></div>
				<div class="factrow"><span><?php esc_html_e( 'Munkaterületek', 'bma-ajanlatkeres' ); ?></span><strong><?php echo wp_kses_post( __( 'Lakossági és üzleti<br>projektek', 'bma-ajanlatkeres' ) ); ?></strong></div>
				<div class="factrow"><span><?php esc_html_e( 'Nyelvek', 'bma-ajanlatkeres' ); ?></span><strong><?php esc_html_e( '[MAGYAR / ROMÁN]', 'bma-ajanlatkeres' ); ?></strong></div>
				<div class="factrow"><span><?php esc_html_e( 'Nyitvatartás', 'bma-ajanlatkeres' ); ?></span><strong><?php echo esc_html( $hours ?: __( 'Hétfő–Péntek, 08:00–17:00', 'bma-ajanlatkeres' ) ); ?></strong></div>
			</div>
		</div>
	</div>
</section>
