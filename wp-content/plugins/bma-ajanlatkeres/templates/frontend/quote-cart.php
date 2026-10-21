<?php
/**
 * Quote cart – step 02 (item-specific fields).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$summary_items = BMA_AJ_Cart::get_summary_items();
$draft_values  = BMA_AJ_Cart::get_field_values();
$item_count    = count( $summary_items );
$partial_steps = BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/flow-steps.php';

$home_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
$svc_url     = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' );
$quote_url   = function_exists( 'bma_page_url' ) ? bma_page_url( 'ajanlatkeres' ) : home_url( '/ajanlatkeres/' );
$cart_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'ajanlatkosar' ) : home_url( '/ajanlatkosar/' );
$count_label = ( 1 === $item_count )
	? __( '1 tétel kiválasztva', 'bma-ajanlatkeres' )
	: sprintf(
		/* translators: %d: selected item count */
		__( '%d tétel kiválasztva', 'bma-ajanlatkeres' ),
		$item_count
	);
?>
<section class="pagehead">
	<div class="pagehead-inner pagehead-inner--wizard">
		<p class="crumb">
			<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
			/
			<a href="<?php echo esc_url( $svc_url ); ?>"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></a>
			/ <?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?>
		</p>
		<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
		<h1><?php esc_html_e( 'Kosár', 'bma-ajanlatkeres' ); ?></h1>
		<p class="pagehead-lead">
			<?php esc_html_e( 'Ellenőrizze a kiválasztott tételeket, és adja meg a tételenkénti adatokat. A projekt közös adatait a következő lépésben kérjük.', 'bma-ajanlatkeres' ); ?>
		</p>
		<?php
		$current_step = 2;
		include $partial_steps;
		?>
	</div>
</section>

<div class="appbody bma-quote-cart">
	<?php if ( empty( $summary_items ) ) : ?>
		<div class="bma-container" style="max-width:720px;margin:0 auto">
			<div class="panel summary">
				<div class="sum-empty" role="status">
					<?php esc_html_e( 'A kosár üres. Válasszon szolgáltatást a folytatáshoz.', 'bma-ajanlatkeres' ); ?>
				</div>
				<div class="sum-foot">
					<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary" style="width:100%;justify-content:center">
						<?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?>
					</a>
				</div>
			</div>
		</div>
	<?php else : ?>
		<form
			method="post"
			action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			class="wiz-grid"
			id="bma-quote-cart-form"
		>
			<input type="hidden" name="action" value="bma_save_cart_fields">
			<?php wp_nonce_field( 'bma_aj_public', 'nonce' ); ?>

			<div>
				<div class="panel">
					<div class="panel-head">
						<h2><?php esc_html_e( 'Tételenkénti adatok', 'bma-ajanlatkeres' ); ?></h2>
						<span class="small muted"><?php echo esc_html( $count_label ); ?></span>
					</div>
					<div class="panel-body">
						<?php foreach ( $summary_items as $line ) : ?>
							<?php
							$item_id = (int) $line['item_id'];
							$fields  = BMA_AJ_Dynamic_Fields::exclude_field_types(
								BMA_AJ_Dynamic_Fields::get_fields_for_item( $item_id ),
								array( 'file', 'hidden' )
							);
							?>
							<div class="itemform" data-item-id="<?php echo esc_attr( (string) $item_id ); ?>" data-cart-key="<?php echo esc_attr( (string) $line['cart_key'] ); ?>">
								<div class="itemform-head">
									<span class="badge badge--tint"><?php echo esc_html( $line['service_name'] ); ?></span>
									<strong><?php echo esc_html( $line['name'] ); ?></strong>
									<button
										type="submit"
										form="bma-remove-item-<?php echo esc_attr( (string) $item_id ); ?>"
										class="sum-del bma-remove-cart"
										style="margin-left:auto"
										data-item-id="<?php echo esc_attr( (string) $item_id ); ?>"
										aria-label="<?php esc_attr_e( 'Tétel eltávolítása', 'bma-ajanlatkeres' ); ?>"
									>×</button>
								</div>
								<?php if ( $fields ) : ?>
									<div class="itemform-body">
										<div class="form-grid">
											<?php foreach ( $fields as $field ) : ?>
												<?php
												$val = $draft_values[ $field->field_key ] ?? '';
												echo BMA_AJ_Dynamic_Fields::render_field( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													$field,
													$val,
													array(
														'skin'      => 'v2',
														'id_suffix' => '_item_' . $item_id,
													)
												);
												?>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="btnrow" style="display:flex;gap:12px;margin-top:24px;flex-wrap:wrap">
					<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--outline"><?php esc_html_e( 'Vissza a szolgáltatásokhoz', 'bma-ajanlatkeres' ); ?></a>
					<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Tovább az adatokhoz', 'bma-ajanlatkeres' ); ?></button>
				</div>
			</div>

			<aside class="panel summary">
				<div class="summary-head">
					<h3><?php esc_html_e( 'Kiválasztott tételek', 'bma-ajanlatkeres' ); ?></h3>
					<span class="summary-count"><?php echo esc_html( (string) $item_count ); ?></span>
				</div>
				<?php foreach ( $summary_items as $line ) : ?>
					<div class="sum-item">
						<span>
							<strong><?php echo esc_html( $line['name'] ); ?></strong>
							<span class="hint"><?php echo esc_html( $line['service_name'] ); ?></span>
						</span>
					</div>
				<?php endforeach; ?>
				<div class="sum-foot">
					<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--outline btn--sm" style="width:100%;justify-content:center">
						<?php esc_html_e( 'Kiválasztás módosítása', 'bma-ajanlatkeres' ); ?>
					</a>
				</div>
			</aside>
		</form>

		<?php /* No-JS remove forms (one per line) */ ?>
		<div hidden>
			<?php foreach ( $summary_items as $line ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="bma-remove-item-<?php echo esc_attr( (string) $line['item_id'] ); ?>">
					<input type="hidden" name="action" value="bma_remove_cart_line">
					<input type="hidden" name="item_id" value="<?php echo esc_attr( (string) $line['item_id'] ); ?>">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $cart_url ); ?>">
					<?php wp_nonce_field( 'bma_aj_public', 'nonce' ); ?>
				</form>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
