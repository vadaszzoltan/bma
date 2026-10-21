<?php
/**
 * Services overview (quote request v2: selection happens in the quote wizard).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$services      = BMA_AJ_Service_CPT::get_active_services();
$quote_url     = BMA_AJ_I18n::page_url( 'ajanlatkeres' );
$home_url      = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
$contact_url   = function_exists( 'bma_page_url' ) ? bma_page_url( 'kapcsolat' ) : home_url( '/' );

$card_icons = array(
	'<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z"></path><path d="M4 10h16M10 4v16"></path><circle cx="15" cy="15" r="1.6"></circle></svg>',
	'<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path><path d="M9.5 14l2 2 3.5-4"></path></svg>',
	'<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v16H5z"></path><path d="M8 9h8M8 13h8M8 17h5"></path></svg>',
	'<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M12 2v6M12 16v6M4.5 7l3.2 2M19.5 7l-3.2 2M4.5 17l3.2-2M19.5 17l-3.2-2"></path></svg>',
);
?>
<section class="pagehead">
	<div class="pagehead-inner pagehead-inner--split">
		<div>
			<p class="crumb">
				<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
				/ <?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?>
			</p>
			<p class="eyebrow eyebrow--light"><?php BMA_AJ_Content::e( 'services.eyebrow' ); ?></p>
			<h1><?php BMA_AJ_Content::e( 'services.title' ); ?></h1>
			<p class="pagehead-lead">
				<?php BMA_AJ_Content::e( 'services.lead' ); ?>
			</p>
		</div>
		<div class="sidecard">
			<h3><?php BMA_AJ_Content::e( 'services.side_title' ); ?></h3>
			<ul>
				<li>
					<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5l5 5L20 6.5"></path></svg>
					<span><?php BMA_AJ_Content::e( 'services.side_1' ); ?></span>
				</li>
				<li>
					<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5l5 5L20 6.5"></path></svg>
					<span><?php BMA_AJ_Content::e( 'services.side_2' ); ?></span>
				</li>
				<li>
					<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5l5 5L20 6.5"></path></svg>
					<span><?php BMA_AJ_Content::e( 'services.side_3' ); ?></span>
				</li>
			</ul>
			<span class="tiny" style="color:#8fa2c4;display:block">
				<?php BMA_AJ_Content::e( 'services.side_note' ); ?>
			</span>
		</div>
	</div>
</section>

<div class="appbody bma-services-overview">
	<div class="bma-container">
		<?php if ( $services ) : ?>
			<nav class="jump" aria-label="<?php esc_attr_e( 'Szolgáltatási területek', 'bma-ajanlatkeres' ); ?>">
				<?php foreach ( $services as $i => $service ) : ?>
					<a href="#svc-<?php echo esc_attr( (string) $service->ID ); ?>">
						<span class="jump-n"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php echo esc_html( $service->post_title ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php foreach ( $services as $i => $service ) : ?>
			<?php
			$sid   = (int) $service->ID;
			$items = BMA_AJ_Service_Items::get_for_service( $sid );
			$desc  = get_post_meta( $sid, '_bma_short_description', true ) ?: $service->post_excerpt;
			$plink = function_exists( 'bma_post_url' ) ? bma_post_url( $sid ) : get_permalink( $service );
			?>
			<div class="panel sv-panel" id="svc-<?php echo esc_attr( (string) $sid ); ?>">
				<div class="sv-head">
					<span class="sv-head-ic">
						<?php echo $card_icons[ $i ] ?? $card_icons[0]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<div>
						<h2><?php echo esc_html( $service->post_title ); ?></h2>
						<?php if ( $desc ) : ?>
							<p><?php echo esc_html( $desc ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( $items ) : ?>
					<ul class="svc-tasks sv-list">
						<?php foreach ( $items as $item ) : ?>
							<li><?php echo esc_html( $item->name ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="sv-actions">
					<a href="<?php echo esc_url( $plink ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'service', $sid, $quote_url ) ); ?>" class="btn btn--primary btn--sm"><?php esc_html_e( 'Ajánlatkérés ezzel', 'bma-ajanlatkeres' ); ?></a>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="panel sv-panel">
			<div class="sv-head"><div><h2><?php esc_html_e( 'Engedélyes vagy kivitelezési terv?', 'bma-ajanlatkeres' ); ?></h2></div></div>
			<div class="sv-phases">
				<?php foreach ( array_slice( BMA_AJ_Quote_Flow::phases(), 0, 3 ) as $phase ) : ?>
					<div>
						<strong><?php echo esc_html( $phase['label'] ); ?></strong>
						<p class="small muted"><?php echo esc_html( $phase['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="panel sv-panel">
			<div class="sv-head"><div><h2><?php esc_html_e( 'Ezeket is intézzük', 'bma-ajanlatkeres' ); ?></h2></div></div>
			<ul class="svc-tasks sv-list">
				<?php foreach ( BMA_AJ_Service_Items::get_general_addons() as $addon ) : ?>
					<li><?php echo esc_html( $addon->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="helpcard">
			<div>
				<h3><?php esc_html_e( 'Nem tudja, mire van szüksége?', 'bma-ajanlatkeres' ); ?></h3>
				<p><?php esc_html_e( 'Indítsa el az ajánlatkérést, és a kiegészítőknél válassza a tanácsadást, vagy írjon nekünk.', 'bma-ajanlatkeres' ); ?></p>
			</div>
			<a href="<?php echo esc_url( $quote_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></a>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--outline"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</div>
</div>
