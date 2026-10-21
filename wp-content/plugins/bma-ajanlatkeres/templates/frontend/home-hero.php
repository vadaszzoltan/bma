<?php
/**
 * Homepage hero + value strip (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$svc_url  = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/' );
$quote_url = BMA_AJ_I18n::page_url( 'ajanlatkeres' );
$ref_url  = function_exists( 'bma_page_url' ) ? bma_page_url( 'referenciak' ) : home_url( '/' );
$services = BMA_AJ_Service_CPT::get_active_services();

$chip_icons = array(
	'<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16v16H4z"></path><path d="M4 10h16M10 4v16"></path></svg>',
	'<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path><path d="M9.5 14l2 2 3.5-4"></path></svg>',
	'<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"></path></svg>',
	'<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v6M12 15v6"></path><circle cx="12" cy="12" r="3"></circle><path d="M5 8l3 2M19 8l-3 2M5 16l3-2M19 16l-3-2"></path></svg>',
);
?>
<section class="hero" aria-labelledby="bma-hero-title">
	<?php
	$hero = BMA_AJ_Media_Seeder::get_hero_html( 'full' );
	if ( $hero ) {
		echo '<div class="hero-media" aria-hidden="true">' . $hero . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
	<svg class="hero-blueprint" viewBox="0 0 1440 620" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
		<defs>
			<pattern id="bma-hero-grid" width="40" height="40" patternUnits="userSpaceOnUse">
				<path d="M40 0H0V40" fill="none" stroke="rgba(255,255,255,.07)" stroke-width="1"></path>
			</pattern>
		</defs>
		<rect width="1440" height="620" fill="url(#bma-hero-grid)"></rect>
		<g fill="none" stroke="rgba(160,190,240,.30)" stroke-width="1.5">
			<path d="M860 120h420v300H860z"></path>
			<path d="M860 250h180v170H860z"></path>
			<path d="M1040 120v130h240"></path>
			<path d="M1120 250v170"></path>
			<circle cx="1040" cy="250" r="6"></circle>
			<circle cx="1120" cy="420" r="6"></circle>
			<path d="M860 460h420"></path>
			<path d="M860 452v16M1280 452v16"></path>
			<path d="M1320 120v300M1312 120h16M1312 420h16"></path>
		</g>
		<g fill="none" stroke="rgba(238,118,67,.55)" stroke-width="2">
			<path d="M860 250h180"></path>
			<path d="M1040 250v170"></path>
		</g>
	</svg>
	<div class="hero-inner">
		<p class="hero-eyebrow"><?php BMA_AJ_Content::e( 'home.hero.eyebrow' ); ?></p>
		<h1 id="bma-hero-title"><?php BMA_AJ_Content::e( 'home.hero.title' ); ?></h1>
		<p class="hero-lead">
			<?php BMA_AJ_Content::e( 'home.hero.lead' ); ?>
		</p>
		<div class="hero-cta">
			<a href="<?php echo esc_url( $quote_url ); ?>" class="btn btn--primary"><?php BMA_AJ_Content::e( 'home.hero.cta_primary' ); ?></a>
			<a href="<?php echo esc_url( $ref_url ); ?>" class="btn btn--ghost-light"><?php BMA_AJ_Content::e( 'home.hero.cta_secondary' ); ?></a>
		</div>
		<?php if ( $services ) : ?>
			<div class="hero-quick">
				<p class="hero-quick-label"><?php BMA_AJ_Content::e( 'home.hero.quick_label' ); ?></p>
				<div class="quick-chips">
					<?php foreach ( array_slice( $services, 0, 4 ) as $i => $service ) : ?>
						<a class="quick-chip" href="<?php echo esc_url( add_query_arg( 'service', (int) $service->ID, $quote_url ) ); ?>">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed inline SVG markup.
							echo $chip_icons[ $i ] ?? $chip_icons[0];
							echo esc_html( $service->post_title );
							?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		<div class="hero-facts">
			<span class="hero-fact">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
				<?php BMA_AJ_Content::e( 'home.hero.fact_1' ); ?>
			</span>
			<span class="hero-fact">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20V7l8-4 8 4v13z"></path><path d="M9 20v-6h6v6"></path></svg>
				<?php BMA_AJ_Content::e( 'home.hero.fact_2' ); ?>
			</span>
			<span class="hero-fact">
				<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"></circle><path d="M12 7v5l3 2"></path></svg>
				<?php BMA_AJ_Content::e( 'home.hero.fact_3' ); ?>
			</span>
		</div>
	</div>
</section>

<div class="valuestrip">
	<div class="valuestrip-inner">
		<div class="valuestrip-item">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h9"></path></svg>
			<span><?php BMA_AJ_Content::e( 'home.hero.value_1' ); ?></span>
		</div>
		<div class="valuestrip-item">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path></svg>
			<span><?php BMA_AJ_Content::e( 'home.hero.value_2' ); ?></span>
		</div>
		<div class="valuestrip-item">
			<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"></circle><path d="M9 12.5l2 2 4-4.5"></path></svg>
			<span><?php BMA_AJ_Content::e( 'home.hero.value_3' ); ?></span>
		</div>
	</div>
</div>
