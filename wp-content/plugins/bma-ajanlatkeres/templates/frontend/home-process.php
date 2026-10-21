<?php
/**
 * Homepage process steps (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$svc_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/' );
$steps   = array(
	array(
		'num'    => '01',
		'title'  => BMA_AJ_Content::get( 'home.process.1_title' ),
		'text'   => BMA_AJ_Content::get( 'home.process.1_text' ),
		'active' => true,
	),
	array(
		'num'    => '02',
		'title'  => BMA_AJ_Content::get( 'home.process.2_title' ),
		'text'   => BMA_AJ_Content::get( 'home.process.2_text' ),
		'active' => true,
	),
	array(
		'num'    => '03',
		'title'  => BMA_AJ_Content::get( 'home.process.3_title' ),
		'text'   => BMA_AJ_Content::get( 'home.process.3_text' ),
		'active' => true,
	),
	array(
		'num'    => '04',
		'title'  => BMA_AJ_Content::get( 'home.process.4_title' ),
		'text'   => BMA_AJ_Content::get( 'home.process.4_text' ),
		'active' => false,
	),
);
?>
<section class="bma-section bma-home-process" aria-labelledby="bma-process-title">
	<div class="bma-container">
		<p class="eyebrow"><?php BMA_AJ_Content::e( 'home.process.eyebrow' ); ?></p>
		<h2 class="h2" id="bma-process-title" style="margin-bottom:34px"><?php BMA_AJ_Content::e( 'home.process.title' ); ?></h2>
		<div class="process">
			<?php foreach ( $steps as $step ) : ?>
				<div class="step<?php echo ! empty( $step['active'] ) ? ' step--active' : ''; ?>">
					<div class="step-num"><?php echo esc_html( $step['num'] ); ?></div>
					<h3><?php echo esc_html( $step['title'] ); ?></h3>
					<p><?php echo esc_html( $step['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<div style="margin-top:34px">
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary"><?php BMA_AJ_Content::e( 'home.process.cta' ); ?></a>
		</div>
	</div>
</section>
