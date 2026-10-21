<?php
/**
 * Homepage benefits (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$benefits = array(
	array(
		'title' => BMA_AJ_Content::get( 'home.benefits.1_title' ),
		'text'  => BMA_AJ_Content::get( 'home.benefits.1_text' ),
		'icon'  => '<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h4l3 7 4-14 3 7h4"></path></svg>',
	),
	array(
		'title' => BMA_AJ_Content::get( 'home.benefits.2_title' ),
		'text'  => BMA_AJ_Content::get( 'home.benefits.2_text' ),
		'icon'  => '<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h7l2 2h7v12H4z"></path></svg>',
	),
	array(
		'title' => BMA_AJ_Content::get( 'home.benefits.3_title' ),
		'text'  => BMA_AJ_Content::get( 'home.benefits.3_text' ),
		'icon'  => '<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path><path d="M9 13h6M9 17h4"></path></svg>',
	),
	array(
		'title' => BMA_AJ_Content::get( 'home.benefits.4_title' ),
		'text'  => BMA_AJ_Content::get( 'home.benefits.4_text' ),
		'icon'  => '<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"></circle><path d="M9 12.5l2 2 4-4.5"></path></svg>',
	),
);
?>
<section class="bma-section bma-section--bg bma-home-benefits" aria-labelledby="bma-benefits-title">
	<div class="bma-container">
		<p class="eyebrow"><?php BMA_AJ_Content::e( 'home.benefits.eyebrow' ); ?></p>
		<h2 class="h2" id="bma-benefits-title" style="margin-bottom:34px"><?php BMA_AJ_Content::e( 'home.benefits.title' ); ?></h2>
		<div class="benefits">
			<?php foreach ( $benefits as $benefit ) : ?>
				<div class="benefit">
					<span class="benefit-ic">
						<?php echo $benefit['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed inline SVG. ?>
					</span>
					<div>
						<h3><?php echo esc_html( $benefit['title'] ); ?></h3>
						<p><?php echo esc_html( $benefit['text'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
