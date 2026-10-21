<?php
/**
 * Homepage multi-service callout (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$quote_url = BMA_AJ_I18n::page_url( 'ajanlatkeres' );
$con_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'kapcsolat' ) : home_url( '/' );
?>
<section class="bma-section bma-section--bg bma-home-multi" aria-labelledby="bma-multi-title">
	<div class="bma-container">
		<div class="multi">
			<div>
				<p class="eyebrow"><?php BMA_AJ_Content::e( 'home.multi.eyebrow' ); ?></p>
				<h2 class="h2" id="bma-multi-title"><?php BMA_AJ_Content::e( 'home.multi.title' ); ?></h2>
				<p style="margin:18px 0 0;font-size:18px;line-height:1.6;color:#4a5a7d">
					<?php BMA_AJ_Content::e( 'home.multi.body_1' ); ?>
				</p>
				<p style="margin:14px 0 0;font-size:18px;line-height:1.6;color:#4a5a7d">
					<?php BMA_AJ_Content::e( 'home.multi.body_2' ); ?>
				</p>
				<div class="btnrow" style="display:flex;gap:14px;margin-top:28px;flex-wrap:wrap">
					<a href="<?php echo esc_url( $quote_url ); ?>" class="btn btn--primary"><?php BMA_AJ_Content::e( 'home.multi.cta_primary' ); ?></a>
					<a href="<?php echo esc_url( $con_url ); ?>" class="btn btn--outline"><?php BMA_AJ_Content::e( 'home.multi.cta_secondary' ); ?></a>
				</div>
			</div>
			<div class="stackcard">
				<div class="stackcard-head"><?php BMA_AJ_Content::e( 'home.multi.stack_title' ); ?></div>
				<div class="stackrow"><span class="stackrow-num">1</span><span class="stackrow-main"><strong><?php BMA_AJ_Content::e( 'home.multi.stack_1_title' ); ?></strong><span><?php BMA_AJ_Content::e( 'home.multi.stack_1_sub' ); ?></span></span></div>
				<div class="stackrow"><span class="stackrow-num">2</span><span class="stackrow-main"><strong><?php BMA_AJ_Content::e( 'home.multi.stack_2_title' ); ?></strong><span><?php BMA_AJ_Content::e( 'home.multi.stack_2_sub' ); ?></span></span></div>
				<div class="stackrow"><span class="stackrow-num">3</span><span class="stackrow-main"><strong><?php BMA_AJ_Content::e( 'home.multi.stack_3_title' ); ?></strong><span><?php BMA_AJ_Content::e( 'home.multi.stack_3_sub' ); ?></span></span></div>
				<div class="stackrow"><span class="stackrow-num">4</span><span class="stackrow-main"><strong><?php BMA_AJ_Content::e( 'home.multi.stack_4_title' ); ?></strong><span><?php BMA_AJ_Content::e( 'home.multi.stack_4_sub' ); ?></span></span></div>
				<div class="stackcard-foot">
					<span class="small muted"><?php BMA_AJ_Content::e( 'home.multi.stack_foot' ); ?></span>
					<span class="badge badge--tint"><?php BMA_AJ_Content::e( 'home.multi.stack_badge' ); ?></span>
				</div>
			</div>
		</div>
	</div>
</section>
