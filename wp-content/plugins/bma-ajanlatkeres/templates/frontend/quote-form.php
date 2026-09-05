<?php
/**
 * Quote form – step 03 (project + contact + review) and step 04 (confirmation).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$partial_steps = BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/flow-steps.php';
$home_url      = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
$svc_url       = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' );
$cart_url      = function_exists( 'bma_page_url' ) ? bma_page_url( 'ajanlatkosar' ) : home_url( '/ajanlatkosar/' );
$portal_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' );
$privacy_url   = function_exists( 'bma_page_url' ) ? bma_page_url( 'adatvedelem' ) : home_url( '/adatvedelem/' );

$confirm_number = '';
if ( isset( $_GET['submitted'] ) ) {
	$confirm_number = BMA_AJ_Quote_Requests::confirmation_number_from_request();
	if ( '' === $confirm_number && ! empty( $_GET['number'] ) ) {
		// Legacy redirects without token still show the number once (cart already cleared).
		$confirm_number = sanitize_text_field( wp_unslash( $_GET['number'] ) );
	}
}

if ( $confirm_number ) :
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
			<h1><?php esc_html_e( 'Visszaigazolás', 'bma-ajanlatkeres' ); ?></h1>
			<?php
			$current_step = 4;
			include $partial_steps;
			?>
		</div>
	</section>

	<div class="appbody">
		<div class="bma-container">
			<div class="done-wrap" role="status" aria-live="polite">
				<div class="done-ic" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="38" height="38" fill="none" stroke="#ee7643" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 12.5l2.6 2.6L16 9.5"></path></svg>
				</div>
				<p class="eyebrow"><?php esc_html_e( 'Beküldve', 'bma-ajanlatkeres' ); ?></p>
				<h2 class="h2"><?php esc_html_e( 'Köszönjük, megkaptuk az ajánlatkérését', 'bma-ajanlatkeres' ); ?></h2>
				<div class="done-id"><?php echo esc_html( $confirm_number ); ?></div>
				<p class="small muted" style="margin:0">
					<?php esc_html_e( 'Az azonosítót e-mailben is elküldtük. Erre hivatkozva bármikor tud érdeklődni.', 'bma-ajanlatkeres' ); ?>
				</p>
				<div class="nextsteps">
					<div class="nextstep">
						<b><?php esc_html_e( 'Most', 'bma-ajanlatkeres' ); ?></b>
						<strong style="display:block;font-size:16px;margin-bottom:6px"><?php esc_html_e( 'Visszaigazolás', 'bma-ajanlatkeres' ); ?></strong>
						<span class="small muted"><?php esc_html_e( 'E-mailben elküldtük a kérés összefoglalóját és a feltöltött dokumentumok listáját.', 'bma-ajanlatkeres' ); ?></span>
					</div>
					<div class="nextstep">
						<b><?php esc_html_e( '[X] munkanapon belül', 'bma-ajanlatkeres' ); ?></b>
						<strong style="display:block;font-size:16px;margin-bottom:6px"><?php esc_html_e( 'Felmérés', 'bma-ajanlatkeres' ); ?></strong>
						<span class="small muted"><?php esc_html_e( 'Átnézzük a megadott adatokat, és ha szükséges, egyeztetünk a hiányzó részletekről.', 'bma-ajanlatkeres' ); ?></span>
					</div>
					<div class="nextstep">
						<b><?php esc_html_e( 'Ezután', 'bma-ajanlatkeres' ); ?></b>
						<strong style="display:block;font-size:16px;margin-bottom:6px"><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></strong>
						<span class="small muted"><?php esc_html_e( 'Az elkészült ajánlat az ügyfélportálon és e-mailben is elérhető lesz.', 'bma-ajanlatkeres' ); ?></span>
					</div>
				</div>
				<div class="btnrow" style="display:flex;gap:12px;justify-content:center;margin-top:34px;flex-wrap:wrap">
					<a href="<?php echo esc_url( $portal_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ügyfélportál megnyitása', 'bma-ajanlatkeres' ); ?></a>
					<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--outline"><?php esc_html_e( 'Új ajánlatkérés', 'bma-ajanlatkeres' ); ?></a>
				</div>
			</div>
		</div>
	</div>
	<?php
	return;
endif;

$summary_items = BMA_AJ_Cart::get_summary_items();
$item_count    = count( $summary_items );
$draft_values  = BMA_AJ_Cart::get_field_values();
$project_fields = BMA_AJ_Dynamic_Fields::exclude_field_types(
	BMA_AJ_Cart::get_merged_service_fields(),
	array( 'file', 'hidden' )
);
$file_fields = BMA_AJ_Dynamic_Fields::only_field_types(
	BMA_AJ_Cart::get_merged_fields(),
	array( 'file' )
);
$submit_token = BMA_AJ_Quote_Requests::create_submit_token();
$form_error   = isset( $_GET['bma_error'] ) ? sanitize_text_field( wp_unslash( $_GET['bma_error'] ) ) : '';

$user       = is_user_logged_in() ? wp_get_current_user() : null;
$name_val   = $user ? (string) $user->display_name : '';
$email_val  = $user ? (string) $user->user_email : '';
$count_label = ( 1 === $item_count )
	? __( '1 tétel', 'bma-ajanlatkeres' )
	: sprintf(
		/* translators: %d: item count */
		__( '%d tétel', 'bma-ajanlatkeres' ),
		$item_count
	);

if ( empty( $summary_items ) ) :
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
			<h1><?php esc_html_e( 'Adatok és beküldés', 'bma-ajanlatkeres' ); ?></h1>
			<?php
			$current_step = 3;
			include $partial_steps;
			?>
		</div>
	</section>
	<div class="appbody">
		<div class="bma-container" style="max-width:720px;margin:0 auto">
			<div class="panel summary">
				<div class="sum-empty" role="status">
					<?php esc_html_e( 'A kosár üres. Az ajánlatkéréshez először válasszon szolgáltatást.', 'bma-ajanlatkeres' ); ?>
				</div>
				<div class="sum-foot">
					<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary" style="width:100%;justify-content:center">
						<?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php
	return;
endif;
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
		<h1><?php esc_html_e( 'Adatok és beküldés', 'bma-ajanlatkeres' ); ?></h1>
		<p class="pagehead-lead">
			<?php esc_html_e( 'Adja meg a projekt közös adatait és a kapcsolattartási információkat, majd ellenőrizze a kiválasztást a beküldés előtt.', 'bma-ajanlatkeres' ); ?>
		</p>
		<?php
		$current_step = 3;
		include $partial_steps;
		?>
	</div>
</section>

<div class="appbody bma-quote-submit">
	<form
		id="bma-quote-submit-form"
		class="wiz-grid"
		method="post"
		action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
		enctype="multipart/form-data"
		novalidate
	>
		<input type="hidden" name="action" value="bma_submit_quote_request">
		<input type="hidden" name="bma_submit_token" value="<?php echo esc_attr( $submit_token ); ?>">
		<?php wp_nonce_field( 'bma_aj_public', 'nonce' ); ?>

		<?php // Persist cart-step item field drafts as hidden inputs so AJAX/no-JS submit includes them. ?>
		<?php foreach ( $draft_values as $fkey => $fval ) : ?>
			<?php if ( is_array( $fval ) ) : ?>
				<?php foreach ( $fval as $multi ) : ?>
					<input type="hidden" name="field_values[<?php echo esc_attr( (string) $fkey ); ?>][]" value="<?php echo esc_attr( (string) $multi ); ?>">
				<?php endforeach; ?>
			<?php else : ?>
				<input type="hidden" name="field_values[<?php echo esc_attr( (string) $fkey ); ?>]" value="<?php echo esc_attr( (string) $fval ); ?>">
			<?php endif; ?>
		<?php endforeach; ?>

		<div>
			<div class="panel" style="margin-bottom:24px">
				<div class="panel-head">
					<h2><?php esc_html_e( 'Projekt alapadatai', 'bma-ajanlatkeres' ); ?></h2>
					<span class="small muted"><?php esc_html_e( 'Minden kiválasztott tételre vonatkozik', 'bma-ajanlatkeres' ); ?></span>
				</div>
				<div class="panel-body">
					<div class="form-grid">
						<?php foreach ( $project_fields as $field ) : ?>
							<?php
							$val = $draft_values[ $field->field_key ] ?? '';
							echo BMA_AJ_Dynamic_Fields::render_field( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$field,
								$val,
								array( 'skin' => 'v2' )
							);
							?>
						<?php endforeach; ?>

						<div class="full">
							<label class="field-label" for="bma_note"><?php esc_html_e( 'További megjegyzés', 'bma-ajanlatkeres' ); ?></label>
							<textarea class="input" id="bma_note" name="note" rows="3" placeholder="<?php esc_attr_e( 'Bármi, ami segít pontosabb ajánlatot adni', 'bma-ajanlatkeres' ); ?>"></textarea>
						</div>

						<?php if ( $file_fields ) : ?>
							<?php foreach ( $file_fields as $field ) : ?>
								<?php
								echo BMA_AJ_Dynamic_Fields::render_field( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									$field,
									'',
									array(
										'skin'       => 'v2',
										'full_width' => true,
									)
								);
								?>
							<?php endforeach; ?>
						<?php else : ?>
							<div class="full">
								<label class="field-label"><?php esc_html_e( 'Tervrajzok, dokumentumok', 'bma-ajanlatkeres' ); ?></label>
								<div class="drop">
									<svg class="icon" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block" aria-hidden="true"><path d="M12 16V4M8 8l4-4 4 4"></path><path d="M4 16v3h16v-3"></path></svg>
									<strong><?php esc_html_e( 'Húzza ide a fájlokat, vagy tallózzon', 'bma-ajanlatkeres' ); ?></strong>
									<span class="small muted"><?php esc_html_e( 'Jelenleg nincs fájlmező hozzárendelve a kiválasztott tételekhez.', 'bma-ajanlatkeres' ); ?></span>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="panel" style="margin-bottom:24px">
				<div class="panel-head"><h2><?php esc_html_e( 'Kapcsolattartás', 'bma-ajanlatkeres' ); ?></h2></div>
				<div class="panel-body">
					<div class="form-grid">
						<div>
							<label class="field-label" for="bma_name"><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input class="input" type="text" id="bma_name" name="name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Teljes név', 'bma-ajanlatkeres' ); ?>" value="<?php echo esc_attr( $name_val ); ?>">
						</div>
						<div>
							<label class="field-label" for="bma_company"><?php esc_html_e( 'Cég (opcionális)', 'bma-ajanlatkeres' ); ?></label>
							<input class="input" type="text" id="bma_company" name="company" autocomplete="organization" placeholder="<?php esc_attr_e( 'Cégnév', 'bma-ajanlatkeres' ); ?>">
						</div>
						<div>
							<label class="field-label" for="bma_email"><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input class="input" type="email" id="bma_email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'nev@example.com', 'bma-ajanlatkeres' ); ?>" value="<?php echo esc_attr( $email_val ); ?>">
						</div>
						<div>
							<label class="field-label" for="bma_phone"><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input class="input" type="tel" id="bma_phone" name="phone" required autocomplete="tel" placeholder="<?php esc_attr_e( '+40 …', 'bma-ajanlatkeres' ); ?>">
						</div>
					</div>

					<?php if ( ! is_user_logged_in() ) : ?>
						<label class="bma-checkrow" style="display:flex;gap:12px;align-items:flex-start;margin-top:22px">
							<input type="checkbox" name="create_account" value="1" id="bma_create_account">
							<span class="small"><?php esc_html_e( 'Hozzon létre ügyfélfiókot, hogy egy helyen kövesse a kérés státuszát, az ajánlatot és a dokumentumokat.', 'bma-ajanlatkeres' ); ?></span>
						</label>
					<?php endif; ?>

					<label class="bma-checkrow" style="display:flex;gap:12px;align-items:flex-start;margin-top:14px">
						<input type="checkbox" name="privacy" value="1" id="bma_privacy" required>
						<span class="small">
							<?php
							echo wp_kses(
								sprintf(
									/* translators: %s: privacy policy URL */
									__( 'Elolvastam és elfogadom az <a href="%s" target="_blank" rel="noopener noreferrer">adatkezelési tájékoztatót</a>.', 'bma-ajanlatkeres' ),
									esc_url( $privacy_url )
								),
								array(
									'a' => array(
										'href'   => array(),
										'target' => array(),
										'rel'    => array(),
									),
								)
							);
							?>
							<span class="required">*</span>
						</span>
					</label>
				</div>
			</div>

			<div class="panel">
				<div class="panel-head">
					<h2><?php esc_html_e( 'Ellenőrzés', 'bma-ajanlatkeres' ); ?></h2>
					<span class="small muted"><?php echo esc_html( $count_label ); ?></span>
				</div>
				<div class="panel-body">
					<?php foreach ( $summary_items as $line ) : ?>
						<div class="rev-row" data-item-id="<?php echo esc_attr( (string) $line['item_id'] ); ?>">
							<span class="badge badge--soft"><?php echo esc_html( $line['service_name'] ); ?></span>
							<strong style="font-size:16px"><?php echo esc_html( $line['name'] ); ?></strong>
							<button
								type="submit"
								form="bma-remove-item-<?php echo esc_attr( (string) $line['item_id'] ); ?>"
								class="sum-del bma-remove-cart"
								style="margin-left:auto"
								data-item-id="<?php echo esc_attr( (string) $line['item_id'] ); ?>"
								aria-label="<?php esc_attr_e( 'Tétel eltávolítása', 'bma-ajanlatkeres' ); ?>"
							>×</button>
						</div>
					<?php endforeach; ?>

					<?php if ( $project_fields || $draft_values ) : ?>
						<div style="margin-top:20px;padding-top:18px;border-top:1px solid var(--bma-border)">
							<?php
							$review_keys = array();
							foreach ( $project_fields as $field ) {
								$key = (string) $field->field_key;
								$val = $draft_values[ $key ] ?? '';
								$formatted = BMA_AJ_Quote_Requests::format_field_value( $val );
								if ( '' === trim( $formatted ) ) {
									continue;
								}
								$review_keys[ $key ] = true;
								?>
								<div class="rev-row">
									<span class="small muted" style="width:210px"><?php echo esc_html( $field->client_label ); ?></span>
									<strong style="font-size:16px"><?php echo esc_html( $formatted ); ?></strong>
								</div>
								<?php
							}
							foreach ( $draft_values as $key => $val ) {
								if ( isset( $review_keys[ $key ] ) ) {
									continue;
								}
								$formatted = BMA_AJ_Quote_Requests::format_field_value( $val );
								if ( '' === trim( $formatted ) ) {
									continue;
								}
								?>
								<div class="rev-row">
									<span class="small muted" style="width:210px"><?php echo esc_html( BMA_AJ_Dynamic_Fields::get_label_for_key( (string) $key ) ); ?></span>
									<strong style="font-size:16px"><?php echo esc_html( $formatted ); ?></strong>
								</div>
								<?php
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div id="bma-form-errors" class="bma-form-errors" role="alert" <?php echo $form_error ? '' : 'hidden'; ?> style="margin-top:16px">
				<?php echo $form_error ? esc_html( $form_error ) : ''; ?>
			</div>

			<div class="btnrow" style="display:flex;gap:12px;margin-top:24px;flex-wrap:wrap">
				<a href="<?php echo esc_url( $cart_url ); ?>" class="btn btn--outline"><?php esc_html_e( 'Vissza', 'bma-ajanlatkeres' ); ?></a>
				<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés beküldése', 'bma-ajanlatkeres' ); ?></button>
			</div>
		</div>

		<aside class="panel summary">
			<div class="summary-head">
				<h3><?php esc_html_e( 'Összegzés', 'bma-ajanlatkeres' ); ?></h3>
				<span class="summary-count"><?php echo esc_html( (string) $item_count ); ?></span>
			</div>
			<div class="sum-empty" style="padding-bottom:0">
				<?php esc_html_e( 'Az ajánlatkérés beküldése után e-mailben visszaigazolást küldünk, és [X] munkanapon belül visszajelzünk.', 'bma-ajanlatkeres' ); ?>
			</div>
			<div class="sum-foot" style="margin-top:20px">
				<span class="small muted"><?php esc_html_e( 'Az ajánlatkérés díjmentes és nem jár kötelezettséggel.', 'bma-ajanlatkeres' ); ?></span>
			</div>
		</aside>
	</form>

	<div hidden>
		<?php foreach ( $summary_items as $line ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="bma-remove-item-<?php echo esc_attr( (string) $line['item_id'] ); ?>">
				<input type="hidden" name="action" value="bma_remove_cart_line">
				<input type="hidden" name="item_id" value="<?php echo esc_attr( (string) $line['item_id'] ); ?>">
				<input type="hidden" name="redirect_to" value="<?php echo esc_url( function_exists( 'bma_page_url' ) ? bma_page_url( 'ajanlatkeres' ) : home_url( '/ajanlatkeres/' ) ); ?>">
				<?php wp_nonce_field( 'bma_aj_public', 'nonce' ); ?>
			</form>
		<?php endforeach; ?>
	</div>
</div>
