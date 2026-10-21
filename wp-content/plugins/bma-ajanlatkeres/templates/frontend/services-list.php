<?php
/**
 * Services list with multi-service item selection (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$services      = BMA_AJ_Service_CPT::get_active_services();
$cart_sel      = BMA_AJ_Cart::get_selection_map();
$summary_items = BMA_AJ_Cart::get_summary_items();
$cart_url      = function_exists( 'bma_page_url' ) ? bma_page_url( 'ajanlatkosar' ) : home_url( '/' );
$home_url      = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
$contact_url   = function_exists( 'bma_page_url' ) ? bma_page_url( 'kapcsolat' ) : home_url( '/' );
$partial_card  = BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/select-card.php';
$partial_sum   = BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/summary-panel.php';
$partial_steps = BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/flow-steps.php';

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
	<div class="pagehead-inner" style="padding-top:0">
		<?php
		$current_step = 1;
		include $partial_steps;
		?>
	</div>
</section>

<form
	class="appbody bma-selection-page"
	method="post"
	action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
	data-bma-selection-root
	data-selection-mode="list"
>
	<input type="hidden" name="action" value="bma_save_selection">
	<?php wp_nonce_field( 'bma_aj_public', 'nonce' ); ?>

	<div class="wiz-grid">
		<div>
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
				$items = BMA_AJ_Dynamic_Fields::get_service_items( $sid );
				$sel   = $cart_sel[ (string) $sid ] ?? array();
				$desc  = get_post_meta( $sid, '_bma_short_description', true ) ?: $service->post_excerpt;
				$plink = function_exists( 'bma_post_url' ) ? bma_post_url( $sid ) : get_permalink( $service );
				?>
				<input type="hidden" name="service_ids[]" value="<?php echo esc_attr( (string) $sid ); ?>">
				<div class="panel sv-panel" id="svc-<?php echo esc_attr( (string) $sid ); ?>" data-service-id="<?php echo esc_attr( (string) $sid ); ?>">
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
						<a href="<?php echo esc_url( $plink ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?></a>
					</div>
					<?php if ( $items ) : ?>
						<div class="sv-tasks" role="group" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: service title */ __( '%s részfeladatai', 'bma-ajanlatkeres' ), $service->post_title ) ); ?>">
							<?php
							foreach ( $items as $item ) {
								$service_id   = $sid;
								$service_name = $service->post_title;
								$checked      = in_array( (int) $item->id, array_map( 'intval', $sel ), true );
								include $partial_card;
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<div class="helpcard">
				<div>
					<h3><?php esc_html_e( 'Nem találja, amire szüksége van?', 'bma-ajanlatkeres' ); ?></h3>
					<p><?php esc_html_e( 'Írja le röviden a projektet, és segítünk összeállítani a szükséges tételeket.', 'bma-ajanlatkeres' ); ?></p>
				</div>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--outline"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></a>
			</div>
		</div>

		<?php
		$show_nojs_submit = true;
		$cta_label        = __( 'Tovább az ajánlatkéréshez', 'bma-ajanlatkeres' );
		include $partial_sum;
		?>
	</div>
</form>
