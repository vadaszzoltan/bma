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
$con_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'kapcsolat' ) : home_url( '/' );
?>
<section class="bma-section bma-section--bg bma-home-about" aria-labelledby="bma-about-title">
	<div class="bma-container">
		<div class="about">
			<div>
				<p class="eyebrow"><?php BMA_AJ_Content::e( 'home.about.eyebrow' ); ?></p>
				<h2 class="h2" id="bma-about-title"><?php echo esc_html( $company ?: 'BMA Engineering' ); ?></h2>
				<p style="margin:18px 0 0;font-size:18px;line-height:1.6;color:#4a5a7d">
					<?php BMA_AJ_Content::e( 'home.about.body_1' ); ?>
				</p>
				<p style="margin:14px 0 0;font-size:18px;line-height:1.6;color:#4a5a7d">
					<?php BMA_AJ_Content::e( 'home.about.body_2' ); ?>
				</p>
				<div style="margin-top:26px">
					<a href="<?php echo esc_url( $con_url ); ?>" class="btn btn--outline"><?php BMA_AJ_Content::e( 'home.about.cta' ); ?></a>
				</div>
			</div>
			<div class="factbox">
				<div class="factrow"><span><?php esc_html_e( 'Székhely', 'bma-ajanlatkeres' ); ?></span><strong><?php BMA_AJ_Content::e( 'home.about.seat' ); ?></strong></div>
				<div class="factrow"><span><?php esc_html_e( 'CUI', 'bma-ajanlatkeres' ); ?></span><strong><?php echo esc_html( $cui ?: '40593519' ); ?></strong></div>
				<div class="factrow"><span><?php esc_html_e( 'Munkaterületek', 'bma-ajanlatkeres' ); ?></span><strong><?php BMA_AJ_Content::e( 'home.about.areas' ); ?></strong></div>
				<div class="factrow"><span><?php esc_html_e( 'Nyelvek', 'bma-ajanlatkeres' ); ?></span><strong><?php BMA_AJ_Content::e( 'home.about.languages' ); ?></strong></div>
				<div class="factrow"><span><?php esc_html_e( 'Nyitvatartás', 'bma-ajanlatkeres' ); ?></span><strong><?php echo esc_html( $hours ?: __( 'Hétfő–Péntek, 08:00–17:00', 'bma-ajanlatkeres' ) ); ?></strong></div>
			</div>
		</div>
	</div>
</section>
