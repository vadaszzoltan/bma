<?php
/**
 * Cart summary panel (.summary).
 *
 * Expects optional:
 * - $summary_items (array from BMA_AJ_Cart::get_summary_items())
 * - $summary_title (string)
 * - $cart_url (string) – CTA destination
 * - $cta_label (string)
 * - $show_nojs_submit (bool) – show form submit for no-JS path
 * - $empty_note / $filled_note (string)
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$summary_items = isset( $summary_items ) && is_array( $summary_items )
	? $summary_items
	: ( class_exists( 'BMA_AJ_Cart' ) ? BMA_AJ_Cart::get_summary_items() : array() );

$item_count = count( $summary_items );
$is_empty   = ( $item_count === 0 );

$summary_title = $summary_title ?? __( 'Az Ön ajánlatkérése', 'bma-ajanlatkeres' );
$cta_label     = $cta_label ?? __( 'Tovább az ajánlatkéréshez', 'bma-ajanlatkeres' );
$cart_url      = $cart_url ?? ( function_exists( 'bma_page_url' ) ? bma_page_url( 'ajanlatkosar' ) : home_url( '/' ) );
$show_nojs     = ! empty( $show_nojs_submit );

$empty_note  = $empty_note ?? __( 'Legalább egy tételt válasszon ki a továbblépéshez.', 'bma-ajanlatkeres' );
$filled_note = $filled_note ?? __( 'A kiválasztás bármikor módosítható a beküldésig. Az ajánlatkérés nem jár kötelezettséggel.', 'bma-ajanlatkeres' );
$empty_copy  = $empty_copy ?? __( 'Még nem választott tételt. Jelölje be bal oldalon, mire van szüksége – bármennyi tétel egy ajánlatkérésbe kerülhet.', 'bma-ajanlatkeres' );
?>
<aside class="panel summary" data-bma-summary data-count="<?php echo esc_attr( (string) $item_count ); ?>">
	<div class="summary-head">
		<h3><?php echo esc_html( $summary_title ); ?></h3>
		<span class="summary-count" data-bma-summary-count><?php echo esc_html( (string) $item_count ); ?></span>
	</div>

	<div data-bma-summary-body>
		<?php if ( $is_empty ) : ?>
			<div class="sum-empty"><?php echo esc_html( $empty_copy ); ?></div>
		<?php else : ?>
			<?php foreach ( $summary_items as $line ) : ?>
				<div
					class="sum-item"
					data-item-id="<?php echo esc_attr( (string) ( $line['item_id'] ?? '' ) ); ?>"
					data-service-id="<?php echo esc_attr( (string) ( $line['service_id'] ?? '' ) ); ?>"
				>
					<span>
						<strong><?php echo esc_html( (string) ( $line['name'] ?? '' ) ); ?></strong>
						<span class="hint"><?php echo esc_html( (string) ( $line['service_name'] ?? '' ) ); ?></span>
					</span>
					<button
						type="button"
						class="sum-del"
						data-bma-sum-del
						aria-label="<?php esc_attr_e( 'Tétel eltávolítása', 'bma-ajanlatkeres' ); ?>"
					>&times;</button>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<div class="sum-foot">
		<a
			href="<?php echo esc_url( $cart_url ); ?>"
			class="btn btn--primary<?php echo $is_empty ? ' is-disabled' : ''; ?>"
			style="width:100%;justify-content:center"
			data-bma-summary-cta
			<?php echo $is_empty ? ' aria-disabled="true" tabindex="-1"' : ''; ?>
		><?php echo esc_html( $cta_label ); ?></a>
		<?php if ( $show_nojs ) : ?>
			<?php // Only for no-JS browsers – never shown alongside the link (avoids duplicate CTAs). ?>
			<noscript>
				<button
					type="submit"
					class="btn btn--primary"
					style="width:100%;justify-content:center;margin-top:10px"
					<?php disabled( $is_empty ); ?>
				><?php echo esc_html( $cta_label ); ?></button>
			</noscript>
		<?php endif; ?>
		<p class="sum-note" data-bma-summary-note><?php echo esc_html( $is_empty ? $empty_note : $filled_note ); ?></p>
	</div>
</aside>
