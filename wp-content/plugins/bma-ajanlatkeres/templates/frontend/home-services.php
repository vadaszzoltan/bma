<?php
/**
 * Homepage services grid (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$services = BMA_AJ_Service_CPT::get_active_services();
$svc_url  = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/' );

$card_icons = array(
	'<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z"></path><path d="M4 10h16M10 4v16"></path><circle cx="15" cy="15" r="1.6"></circle></svg>',
	'<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path><path d="M9.5 14l2 2 3.5-4"></path></svg>',
	'<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v16H5z"></path><path d="M8 9h8M8 13h8M8 17h5"></path></svg>',
	'<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M12 2v6M12 16v6M4.5 7l3.2 2M19.5 7l-3.2 2M4.5 17l3.2-2M19.5 17l-3.2-2"></path></svg>',
);
?>
<section class="bma-section bma-home-services" aria-labelledby="bma-services-title">
	<div class="bma-container">
		<div class="sec-head">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></p>
				<h2 class="h2" id="bma-services-title"><?php esc_html_e( 'Miben tudunk segíteni?', 'bma-ajanlatkeres' ); ?></h2>
				<p class="lead" style="margin-top:14px">
					<?php esc_html_e( 'Négy szolgáltatási terület, azon belül konkrét részfeladatokkal. Az ajánlatkérésbe bármennyi tétel bekerülhet.', 'bma-ajanlatkeres' ); ?>
				</p>
			</div>
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Összes szolgáltatás', 'bma-ajanlatkeres' ); ?></a>
		</div>
		<div class="svc-grid">
			<?php foreach ( array_slice( $services, 0, 4 ) as $i => $service ) : ?>
				<?php
				$items = class_exists( 'BMA_AJ_Service_Items' )
					? BMA_AJ_Service_Items::get_for_service( (int) $service->ID )
					: array();
				$desc  = get_post_meta( $service->ID, '_bma_short_description', true ) ?: $service->post_excerpt;
				$link  = function_exists( 'bma_post_url' ) ? bma_post_url( (int) $service->ID ) : get_permalink( $service );
				?>
				<article class="svc-card">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed inline SVG markup.
					echo $card_icons[ $i ] ?? $card_icons[0];
					?>
					<h3><?php echo esc_html( $service->post_title ); ?></h3>
					<p><?php echo esc_html( $desc ); ?></p>
					<?php if ( $items ) : ?>
						<ul class="svc-tasks">
							<?php foreach ( array_slice( $items, 0, 4 ) as $item ) : ?>
								<li><?php echo esc_html( $item->name ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<div class="svc-foot">
						<a href="<?php echo esc_url( $link ); ?>">
							<?php esc_html_e( 'Részletek és kiválasztás →', 'bma-ajanlatkeres' ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
