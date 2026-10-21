<?php
/**
 * Homepage closing CTA (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$quote_url = BMA_AJ_I18n::page_url( 'ajanlatkeres' );
$con_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'kapcsolat' ) : home_url( '/' );
?>
<section class="closing" aria-labelledby="bma-closing-title">
	<div class="closing-inner">
		<div>
			<p class="eyebrow eyebrow--light"><?php BMA_AJ_Content::e( 'home.closing.eyebrow' ); ?></p>
			<h2 class="h2" id="bma-closing-title"><?php BMA_AJ_Content::e( 'home.closing.title' ); ?></h2>
			<p>
				<?php BMA_AJ_Content::e( 'home.closing.body' ); ?>
			</p>
		</div>
		<div class="closing-actions">
			<a href="<?php echo esc_url( $quote_url ); ?>" class="btn btn--primary"><?php BMA_AJ_Content::e( 'home.closing.cta_primary' ); ?></a>
			<a href="<?php echo esc_url( $con_url ); ?>" class="btn btn--ghost-light"><?php BMA_AJ_Content::e( 'home.closing.cta_secondary' ); ?></a>
			<span class="tiny" style="color:#8fa2c4;text-align:center">
				<?php BMA_AJ_Content::e( 'home.closing.tip' ); ?>
			</span>
		</div>
	</div>
</section>
