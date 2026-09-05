<?php
/**
 * Sticky selection summary + unsaved modal markup.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$cta_label = $cta_label ?? __( 'Tovább az ajánlatkéréshez', 'bma-ajanlatkeres' );
$mode      = $mode ?? 'list'; // list | detail
?>
<aside class="bma-selection-bar" data-bma-selection-bar data-mode="<?php echo esc_attr( $mode ); ?>" hidden>
	<div class="bma-container bma-selection-bar-inner">
		<div class="bma-selection-bar-copy">
			<p class="bma-selection-count" data-bma-selection-count></p>
			<p class="bma-selection-names" data-bma-selection-names></p>
			<p class="bma-selection-dirty" data-bma-selection-dirty hidden>
				<?php esc_html_e( 'A kiválasztott elemeket még nem adta hozzá az ajánlatkéréshez. A folytatás előtt kattintson a gombra.', 'bma-ajanlatkeres' ); ?>
			</p>
			<p class="bma-selection-saved" data-bma-selection-saved hidden role="status">
				<?php esc_html_e( 'Hozzáadva az ajánlatkéréshez', 'bma-ajanlatkeres' ); ?>
			</p>
		</div>
		<div class="bma-selection-bar-actions">
			<button type="button" class="bma-btn bma-btn-accent bma-btn-lg" data-bma-selection-cta>
				<?php echo esc_html( $cta_label ); ?>
			</button>
			<?php if ( 'detail' === $mode ) : ?>
				<a class="bma-btn bma-btn-outline" href="<?php echo esc_url( home_url( '/ajanlatkosar/' ) ); ?>" data-bma-cart-link hidden>
					<?php esc_html_e( 'Ajánlatkérési kosár', 'bma-ajanlatkeres' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</aside>

<dialog class="bma-unsaved-dialog" data-bma-unsaved-dialog>
	<form method="dialog" class="bma-unsaved-dialog-inner">
		<h2><?php esc_html_e( 'A kiválasztott elemeket még nem adta hozzá az ajánlatkéréshez', 'bma-ajanlatkeres' ); ?></h2>
		<p><?php esc_html_e( 'Ha most továbblép, a jelenlegi kiválasztásai elveszhetnek.', 'bma-ajanlatkeres' ); ?></p>
		<div class="bma-unsaved-dialog-actions">
			<button type="button" class="bma-btn bma-btn-accent" data-bma-unsaved-add><?php esc_html_e( 'Hozzáadás és továbblépés', 'bma-ajanlatkeres' ); ?></button>
			<button type="button" class="bma-btn bma-btn-outline" data-bma-unsaved-leave><?php esc_html_e( 'Továbblépés hozzáadás nélkül', 'bma-ajanlatkeres' ); ?></button>
			<button type="button" class="bma-btn" data-bma-unsaved-stay><?php esc_html_e( 'Maradok ezen az oldalon', 'bma-ajanlatkeres' ); ?></button>
		</div>
	</form>
</dialog>
