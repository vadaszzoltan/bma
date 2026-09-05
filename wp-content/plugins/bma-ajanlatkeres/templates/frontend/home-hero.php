<?php
/**
 * Homepage hero + value strip (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$svc_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) );
$ref_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'referenciak' ) : home_url( '/referenciak/' ) );
$services = BMA_AJ_Service_CPT::get_active_services();
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
		</g>
		<g fill="none" stroke="rgba(238,118,67,.55)" stroke-width="2">
			<path d="M860 250h180"></path>
			<path d="M1040 250v170"></path>
		</g>
	</svg>
	<div class="hero-inner">
		<p class="hero-eyebrow"><?php esc_html_e( 'Tervezés · Engedélyeztetés · Közmű-koordináció', 'bma-ajanlatkeres' ); ?></p>
		<h1 id="bma-hero-title"><?php echo wp_kses_post( __( 'Mérnöki tervezés <em>átlátható</em> folyamattal', 'bma-ajanlatkeres' ) ); ?></h1>
		<p class="hero-lead">
			<?php esc_html_e( 'Válassza ki, mire van szüksége a projektjéhez, adja meg a rendelkezésére álló adatokat, és kérjen személyre szabott ajánlatot. A folyamat minden lépését egy helyen követheti.', 'bma-ajanlatkeres' ); ?>
		</p>
		<div class="hero-cta">
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
			<a href="<?php echo esc_url( $ref_url ); ?>" class="btn btn--ghost-light"><?php esc_html_e( 'Referenciák', 'bma-ajanlatkeres' ); ?></a>
		</div>
		<?php if ( $services ) : ?>
			<div class="hero-quick">
				<p class="hero-quick-label"><?php esc_html_e( 'Gyors indítás – mire van szüksége?', 'bma-ajanlatkeres' ); ?></p>
				<div class="quick-chips">
					<?php foreach ( array_slice( $services, 0, 4 ) as $service ) : ?>
						<a class="quick-chip" href="<?php echo esc_url( get_permalink( $service ) ); ?>"><?php echo esc_html( $service->post_title ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		<div class="hero-facts">
			<span class="hero-fact"><?php esc_html_e( 'Odorheiu Secuiesc · Harghita', 'bma-ajanlatkeres' ); ?></span>
			<span class="hero-fact"><?php esc_html_e( 'Lakossági és üzleti projektek', 'bma-ajanlatkeres' ); ?></span>
			<span class="hero-fact"><?php esc_html_e( 'Válasz [X] munkanapon belül', 'bma-ajanlatkeres' ); ?></span>
		</div>
	</div>
</section>

<div class="valuestrip">
	<div class="valuestrip-inner">
		<div class="valuestrip-item"><span><?php esc_html_e( 'Egy ajánlatkérésben több szolgáltatás és részfeladat is összeállítható.', 'bma-ajanlatkeres' ); ?></span></div>
		<div class="valuestrip-item"><span><?php esc_html_e( 'A tervrajzok és dokumentumok az ajánlatkéréshez rendelve tölthetők fel.', 'bma-ajanlatkeres' ); ?></span></div>
		<div class="valuestrip-item"><span><?php esc_html_e( 'Az ügyfélportálon végigkövethető a kérés státusza és a dokumentumok.', 'bma-ajanlatkeres' ); ?></span></div>
	</div>
</div>
