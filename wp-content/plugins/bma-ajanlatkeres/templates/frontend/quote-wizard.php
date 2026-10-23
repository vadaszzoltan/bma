<?php
/**
 * Quote request v2 – 7-step wizard on one page.
 *
 * All steps live in one multipart form. Without JS every step is shown at once;
 * quote-wizard.js turns it into a step-by-step flow. Rules are enforced again
 * on the server (BMA_AJ_Quote_Flow).
 *
 * Preselection: ?service=<post id>&items[]=<item id>
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$cat         = BMA_AJ_Quote_Flow::catalog();
$fields      = BMA_AJ_Quote_Flow::fields();
$phases      = BMA_AJ_Quote_Flow::phases();
$home_url    = BMA_AJ_I18n::page_url( 'fooldal' );
$privacy_url = BMA_AJ_I18n::page_url( 'adatvedelem' );
$form_error  = isset( $_GET['bma_error'] ) ? sanitize_text_field( wp_unslash( $_GET['bma_error'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

// Preselected group (shown first) and items.
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$pre_service = isset( $_GET['service'] ) ? BMA_AJ_I18n::source_post_id( absint( $_GET['service'] ) ) : 0;
$pre_items   = isset( $_GET['items'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['items'] ) ) : array();
// phpcs:enable
$services = $cat['services'];
if ( $pre_service && isset( $services[ $pre_service ] ) ) {
	$services = array( $pre_service => $services[ $pre_service ] ) + $services;
}

$user      = is_user_logged_in() ? wp_get_current_user() : null;
$name_val  = $user ? (string) $user->display_name : '';
$email_val = $user ? (string) $user->user_email : '';

$steps = array(
	1 => __( 'Projekt', 'bma-ajanlatkeres' ),
	2 => __( 'Tervfázis', 'bma-ajanlatkeres' ),
	3 => __( 'Szolgáltatások', 'bma-ajanlatkeres' ),
	4 => __( 'Kiegészítők', 'bma-ajanlatkeres' ),
	5 => __( 'Dokumentumok', 'bma-ajanlatkeres' ),
	6 => __( 'Kapcsolattartás', 'bma-ajanlatkeres' ),
	7 => __( 'Összegzés', 'bma-ajanlatkeres' ),
);

// Data for quote-wizard.js (labels for the summary, add-on rules).
$js_items = array();
foreach ( $cat['items'] as $id => $row ) {
	$js_items[ $id ] = array(
		'name'     => (string) $row['item']->name,
		'service'  => (int) $row['service'],
		'slug'     => $row['slug'],
		'hasPhase' => (bool) $row['has_phase'],
	);
}
$js_services = array();
foreach ( $cat['services'] as $sid => $svc ) {
	$js_services[ $sid ] = array(
		'short'  => $svc['short'],
		'permit' => $svc['permit'] ? (int) $svc['permit']->id : 0,
	);
}
$js_addons = array();
foreach ( $cat['addons'] as $id => $addon ) {
	$rule             = BMA_AJ_Service_Items::auto_rule( $addon );
	$js_addons[ $id ] = array(
		'name'   => (string) $addon->name,
		'slug'   => (string) $addon->slug,
		'rule'   => $rule ? $rule : null,
		'locked' => ! empty( $rule['locked'] ),
	);
}
$js_data = array(
	'items'       => (object) $js_items,
	'services'    => (object) $js_services,
	'addons'      => (object) $js_addons,
	'phases'      => array_map( static fn( $p ) => $p['label'], $phases ),
	'clientTypes' => BMA_AJ_Quote_Flow::client_types(),
	'strings'     => array(
		'required'      => __( 'Ez a mező kötelező.', 'bma-ajanlatkeres' ),
		'missing'       => __( 'Néhány kötelező adat hiányzik. A pirossal jelölt mezőket kérjük, töltse ki.', 'bma-ajanlatkeres' ),
		'noItems'       => __( 'Kérjük, válasszon legalább egy szolgáltatást.', 'bma-ajanlatkeres' ),
		'noPhase'       => __( 'Kérjük, válassza ki, milyen tervre van szüksége.', 'bma-ajanlatkeres' ),
		'badEmail'      => __( 'Érvénytelen e-mail cím.', 'bma-ajanlatkeres' ),
		'softFloorplan' => __( 'Az alaprajzok nélkül nem tudunk érdemi ajánlatot adni. Ha nincs meg, írja meg a megjegyzésben, és keresünk.', 'bma-ajanlatkeres' ),
		'auto'          => __( 'automatikusan hozzáadva', 'bma-ajanlatkeres' ),
		'noPhaseShort'  => __( 'tervfázis nélkül', 'bma-ajanlatkeres' ),
		'none'          => __( 'Nincs megadva', 'bma-ajanlatkeres' ),
		'sending'       => __( 'Küldés…', 'bma-ajanlatkeres' ),
		'sendError'     => __( 'A beküldés nem sikerült. Kérjük, próbálja újra.', 'bma-ajanlatkeres' ),
		'itemCount'     => __( '%d elem', 'bma-ajanlatkeres' ),
		'summaryEmpty'  => __( 'Még nem választott elemet.', 'bma-ajanlatkeres' ),
	),
);

$panel_head = static function ( int $n, string $title, string $lead = '' ): void {
	?>
	<div class="panel-head wiz-head">
		<div>
			<p class="eyebrow"><?php echo esc_html( sprintf( '%02d', $n ) ); ?></p>
			<h2><?php echo esc_html( $title ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="small muted wiz-lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
};

/**
 * Render one dynamic field with the data attributes quote-wizard.js reads.
 */
$render = static function ( object $field, string $wrap_class = '' ): void {
	$data = array( 'required' => (int) $field->is_required );
	if ( $field->item_ids ) {
		$data['items'] = array_values( $field->item_ids );
	}
	if ( $field->cond ) {
		$data['cond'] = $field->cond;
	}
	echo BMA_AJ_Dynamic_Fields::render_field( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$field,
		'',
		array(
			'skin'       => 'v2',
			'data'       => $data,
			'wrap_class' => $wrap_class,
			'multiple'   => 'file' === $field->field_type,
			'error_slot' => true,
		)
	);
};
?>
<section class="pagehead">
	<div class="pagehead-inner pagehead-inner--wizard">
		<p class="crumb">
			<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
			/ <?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?>
		</p>
		<h1><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></h1>
		<p class="pagehead-lead">
			<?php esc_html_e( 'Néhány perc alatt végigmegyünk a projektjén. Amit nem tud, azt hagyja üresen vagy jelölje, hogy nincs meg: segítünk pótolni. Az ajánlatkérés díjmentes és nem jár kötelezettséggel.', 'bma-ajanlatkeres' ); ?>
		</p>
		<nav class="steps steps--7" aria-label="<?php esc_attr_e( 'Ajánlatkérési lépések', 'bma-ajanlatkeres' ); ?>" data-wiz-steps hidden>
			<?php foreach ( $steps as $n => $label ) : ?>
				<button type="button" class="stepchip" data-wiz-goto="<?php echo esc_attr( (string) $n ); ?>" disabled>
					<b><?php echo esc_html( sprintf( '%02d', $n ) ); ?></b><span><?php echo esc_html( $label ); ?></span>
				</button>
			<?php endforeach; ?>
		</nav>
	</div>
</section>

<div class="appbody bma-wiz-body">
	<form
		id="bma-quote-wizard"
		class="wiz-grid bma-wiz"
		method="post"
		action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
		enctype="multipart/form-data"
		data-edit="<?php esc_attr_e( 'Szerkesztés', 'bma-ajanlatkeres' ); ?>"
		novalidate
	>
		<input type="hidden" name="action" value="bma_submit_quote_v2">
		<input type="hidden" name="bma_submit_token" value="<?php echo esc_attr( BMA_AJ_Quote_Requests::create_submit_token() ); ?>">
		<input type="hidden" name="addons_js" value="">
		<?php wp_nonce_field( 'bma_aj_public', 'nonce' ); ?>
		<script type="application/json" id="bma-wiz-data"><?php echo wp_json_encode( $js_data ); ?></script>

		<div class="wiz-main">
			<div id="bma-wiz-alert" class="bma-form-errors" role="alert" <?php echo $form_error ? '' : 'hidden'; ?>>
				<?php echo esc_html( $form_error ); ?>
			</div>

			<?php /* ---------------- 01 Projekt ---------------- */ ?>
			<fieldset class="panel wiz-step" data-step="1">
				<?php $panel_head( 1, __( 'Meséljen a projektről', 'bma-ajanlatkeres' ), __( 'Ezek az adatok minden kiválasztott szolgáltatásra érvényesek.', 'bma-ajanlatkeres' ) ); ?>
				<div class="panel-body">
					<div class="bma-form-field full" data-bma-field="client_type" data-required="1">
						<span class="field-label"><?php esc_html_e( 'Ki a megrendelő?', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></span>
						<div class="optcards optcards--3" role="radiogroup">
							<?php foreach ( BMA_AJ_Quote_Flow::client_types() as $key => $label ) : ?>
								<label class="optcard">
									<input type="radio" name="client_type" value="<?php echo esc_attr( $key ); ?>">
									<strong><?php echo esc_html( $label ); ?></strong>
								</label>
							<?php endforeach; ?>
						</div>
						<p class="bma-field-desc small muted"><?php esc_html_e( 'Ez alapján mutatjuk a projektjéhez illő tervtípusokat.', 'bma-ajanlatkeres' ); ?></p>
						<p class="bma-field-error" hidden></p>
					</div>
					<div class="form-grid">
						<?php foreach ( $fields['project'] as $field ) : ?>
							<?php $render( $field ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</fieldset>

			<?php /* ---------------- 02 Tervfázis ---------------- */ ?>
			<fieldset class="panel wiz-step" data-step="2">
				<?php $panel_head( 2, __( 'Milyen tervre van szüksége?', 'bma-ajanlatkeres' ), __( 'Egyszer válassza ki, ez minden elemre érvényes lesz. Ha egy elemhez másik fázis kell, a következő lépésben eltérhet tőle.', 'bma-ajanlatkeres' ) ); ?>
				<div class="panel-body">
					<p class="bma-info-box wiz-nophase-note" data-wiz-nophase-note hidden>
						<?php esc_html_e( 'A bekötésekhez nem kell tervfázist választania, lépjen tovább.', 'bma-ajanlatkeres' ); ?>
					</p>
					<div class="bma-form-field full" data-bma-field="design_phase">
						<div class="optcards optcards--2" role="radiogroup">
							<?php foreach ( $phases as $key => $phase ) : ?>
								<label class="optcard optcard--desc" <?php echo $phase['only'] ? 'data-only="' . esc_attr( implode( ',', $phase['only'] ) ) . '"' : ''; ?>>
									<input type="radio" name="design_phase" value="<?php echo esc_attr( $key ); ?>">
									<strong><?php echo esc_html( $phase['label'] ); ?></strong>
									<span class="hint"><?php echo esc_html( $phase['desc'] ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<p class="bma-field-desc small muted"><?php esc_html_e( 'Nem biztos benne? Válassza az „Engedélyes + kivitelezési terv” lehetőséget. Az ajánlatban külön soron látja a kettőt.', 'bma-ajanlatkeres' ); ?></p>
						<p class="bma-field-error" hidden></p>
					</div>
				</div>
			</fieldset>

			<?php /* ---------------- 03 Szolgáltatások ---------------- */ ?>
			<fieldset class="panel wiz-step" data-step="3">
				<?php $panel_head( 3, __( 'Mit tervezzünk?', 'bma-ajanlatkeres' ), __( 'Válassza ki a szükséges elemeket. Bármennyi kombinálható, több főcsoportból is.', 'bma-ajanlatkeres' ) ); ?>
				<div class="bma-form-field" data-bma-field="items"><p class="bma-field-error wiz-pad" hidden></p></div>
				<?php foreach ( $services as $sid => $svc ) : ?>
					<div class="area wiz-group" data-service="<?php echo esc_attr( (string) $sid ); ?>">
						<div class="area-top">
							<h3 class="h4"><?php echo esc_html( $svc['title'] ); ?></h3>
						</div>
						<?php if ( ! $svc['has_phase'] ) : ?>
							<p class="small muted"><?php esc_html_e( 'A bekötési tervek egyetlen fázisban készülnek.', 'bma-ajanlatkeres' ); ?></p>
						<?php endif; ?>
						<div class="tasks tasks--wiz">
							<?php foreach ( $svc['items'] as $item ) : ?>
								<?php
								$iid     = (int) $item->id;
								$checked = in_array( $iid, $pre_items, true );
								?>
								<div class="wiz-task">
									<label class="task<?php echo $checked ? ' is-sel' : ''; ?>">
										<input type="checkbox" class="visually-hidden" name="items[]" value="<?php echo esc_attr( (string) $iid ); ?>" <?php checked( $checked ); ?>>
										<span class="box" aria-hidden="true">
											<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"></path></svg>
										</span>
										<span>
											<strong><?php echo esc_html( $item->name ); ?></strong>
											<?php if ( ! empty( $item->short_description ) ) : ?>
												<span class="hint"><?php echo esc_html( $item->short_description ); ?></span>
											<?php endif; ?>
											<?php if ( ! empty( $item->choose_if ) ) : ?>
												<span class="task-when">
													<b><?php esc_html_e( 'Ezt válassza, ha:', 'bma-ajanlatkeres' ); ?></b>
													<span><?php echo esc_html( $item->choose_if ); ?></span>
												</span>
											<?php endif; ?>
										</span>
									</label>
									<?php if ( $svc['has_phase'] ) : ?>
										<div class="wiz-override" data-wiz-override>
											<label class="bma-checkbox-label small">
												<input type="checkbox" data-wiz-override-toggle>
												<?php esc_html_e( 'Ehhez az elemhez más tervfázis kell', 'bma-ajanlatkeres' ); ?>
											</label>
											<select class="input" name="item_phases[<?php echo esc_attr( (string) $iid ); ?>]" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: item name */ __( '%s tervfázisa', 'bma-ajanlatkeres' ), $item->name ) ); ?>">
												<option value=""><?php esc_html_e( 'Válasszon…', 'bma-ajanlatkeres' ); ?></option>
												<?php foreach ( $phases as $key => $phase ) : ?>
													<option value="<?php echo esc_attr( $key ); ?>" <?php echo $phase['only'] ? 'data-only="' . esc_attr( implode( ',', $phase['only'] ) ) . '"' : ''; ?>><?php echo esc_html( $phase['label'] ); ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</fieldset>

			<?php /* ---------------- 04 Kiegészítők ---------------- */ ?>
			<fieldset class="panel wiz-step" data-step="4">
				<?php $panel_head( 4, __( 'Kér még valamit a tervek mellé?', 'bma-ajanlatkeres' ), __( 'Ez a lépés nem kötelező.', 'bma-ajanlatkeres' ) ); ?>
				<div class="area" data-wiz-permits>
					<h3 class="h4"><?php esc_html_e( 'Engedélyeztetés', 'bma-ajanlatkeres' ); ?></h3>
					<div class="tasks tasks--wiz">
						<?php foreach ( $cat['services'] as $sid => $svc ) : ?>
							<?php if ( $svc['permit'] ) : ?>
								<?php $permit = $svc['permit']; ?>
								<label class="task" data-addon="<?php echo esc_attr( (string) $permit->id ); ?>" data-service="<?php echo esc_attr( (string) $sid ); ?>">
									<input type="checkbox" class="visually-hidden" name="addons[]" value="<?php echo esc_attr( (string) $permit->id ); ?>">
									<span class="box" aria-hidden="true"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"></path></svg></span>
									<span>
										<strong><?php echo esc_html( $permit->name ); ?></strong>
										<span class="hint"><?php echo esc_html( $permit->short_description ); ?></span>
									</span>
								</label>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="area">
					<h3 class="h4"><?php esc_html_e( 'Ezeket is intézzük Önnek', 'bma-ajanlatkeres' ); ?></h3>
					<p class="small muted"><?php esc_html_e( 'Amit nem tud csatolni, azt beszerezzük. A beszerzés költsége és ideje az ajánlatban külön tételként szerepel.', 'bma-ajanlatkeres' ); ?></p>
					<div class="tasks tasks--wiz">
						<?php foreach ( $cat['general'] as $aid ) : ?>
							<?php $addon = $cat['addons'][ $aid ]; ?>
							<label class="task" data-addon="<?php echo esc_attr( (string) $aid ); ?>">
								<input type="checkbox" class="visually-hidden" name="addons[]" value="<?php echo esc_attr( (string) $aid ); ?>">
								<span class="box" aria-hidden="true"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"></path></svg></span>
								<span>
									<strong><?php echo esc_html( $addon->name ); ?></strong>
									<span class="hint"><?php echo esc_html( $addon->short_description ); ?></span>
									<span class="badge badge--tint wiz-auto" data-wiz-auto hidden><?php esc_html_e( 'Automatikusan hozzáadva, mert jelezte, hogy ez még nincs meg.', 'bma-ajanlatkeres' ); ?></span>
									<span class="badge badge--soft wiz-auto" data-wiz-locked hidden><?php esc_html_e( 'A kivitelezési tervekhez jogszabály írja elő.', 'bma-ajanlatkeres' ); ?></span>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
					<div class="form-grid wiz-addon-fields">
						<?php foreach ( $fields['addons'] as $field ) : ?>
							<?php $render( $field ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</fieldset>

			<?php /* ---------------- 05 Dokumentumok ---------------- */ ?>
			<fieldset class="panel wiz-step" data-step="5">
				<?php $panel_head( 5, __( 'Ami segít pontos ajánlatot adni', 'bma-ajanlatkeres' ), __( 'A lista a kiválasztott szolgáltatásokhoz igazodik. Ha valami nincs meg, jelölje „Nem”-mel: nem akadályozza az ajánlatkérést, a beszerzését felvesszük a kiegészítők közé.', 'bma-ajanlatkeres' ) ); ?>
				<div class="area">
					<div class="form-grid">
						<?php foreach ( $fields['documents'] as $field ) : ?>
							<?php $render( $field, 'full' ); ?>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
				// Each item field is shown once, under the first group that uses it.
				$shown = array();
				foreach ( $cat['services'] as $sid => $svc ) :
					$group_items = array_map( static fn( $i ) => (int) $i->id, $svc['items'] );
					$group       = array();
					foreach ( $fields['item'] as $field ) {
						if ( ! isset( $shown[ $field->field_key ] ) && array_intersect( $field->item_ids, $group_items ) ) {
							$group[]                    = $field;
							$shown[ $field->field_key ] = true;
						}
					}
					if ( ! $group ) {
						continue;
					}
					?>
					<div class="area wiz-docgroup" data-service="<?php echo esc_attr( (string) $sid ); ?>">
						<h3 class="h4"><?php echo esc_html( $svc['short'] ); ?></h3>
						<div class="form-grid">
							<?php foreach ( $group as $field ) : ?>
								<?php $render( $field, in_array( $field->field_type, array( 'radio', 'file', 'textarea' ), true ) ? 'full' : '' ); ?>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</fieldset>

			<?php /* ---------------- 06 Kapcsolattartás ---------------- */ ?>
			<fieldset class="panel wiz-step" data-step="6">
				<?php $panel_head( 6, __( 'Hova küldjük az ajánlatot?', 'bma-ajanlatkeres' ) ); ?>
				<div class="panel-body">
					<div class="form-grid">
						<div class="bma-form-field" data-bma-field="name" data-required="1">
							<label class="field-label" for="bma_name"><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input class="input" type="text" id="bma_name" name="name" autocomplete="name" placeholder="<?php esc_attr_e( 'Teljes név', 'bma-ajanlatkeres' ); ?>" value="<?php echo esc_attr( $name_val ); ?>">
							<p class="bma-field-error" hidden></p>
						</div>
						<div class="bma-form-field" data-bma-field="company" data-required="0" data-required-for="ceg,onkormanyzat">
							<label class="field-label" for="bma_company"><?php esc_html_e( 'Cégnév', 'bma-ajanlatkeres' ); ?> <span class="required" data-wiz-company-star hidden>*</span></label>
							<input class="input" type="text" id="bma_company" name="company" autocomplete="organization">
							<p class="bma-field-desc small muted"><?php esc_html_e( 'Cégnél és önkormányzatnál kötelező.', 'bma-ajanlatkeres' ); ?></p>
							<p class="bma-field-error" hidden></p>
						</div>
						<div class="bma-form-field" data-bma-field="email" data-required="1" data-type="email">
							<label class="field-label" for="bma_email"><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input class="input" type="email" id="bma_email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'nev@example.com', 'bma-ajanlatkeres' ); ?>" value="<?php echo esc_attr( $email_val ); ?>">
							<p class="bma-field-error" hidden></p>
						</div>
						<div class="bma-form-field" data-bma-field="phone" data-required="1">
							<label class="field-label" for="bma_phone"><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input class="input" type="tel" id="bma_phone" name="phone" autocomplete="tel" placeholder="<?php esc_attr_e( '+40 …', 'bma-ajanlatkeres' ); ?>">
							<p class="bma-field-error" hidden></p>
						</div>
						<div class="bma-form-field">
							<label class="field-label" for="bma_tax"><?php esc_html_e( 'Adószám', 'bma-ajanlatkeres' ); ?></label>
							<input class="input" type="text" id="bma_tax" name="tax_number">
						</div>
						<div class="bma-form-field">
							<label class="field-label" for="bma_billing"><?php esc_html_e( 'Számlázási cím', 'bma-ajanlatkeres' ); ?></label>
							<input class="input" type="text" id="bma_billing" name="billing_address" autocomplete="street-address">
						</div>
						<div class="bma-form-field full">
							<label class="field-label" for="bma_note"><?php esc_html_e( 'Megjegyzés', 'bma-ajanlatkeres' ); ?></label>
							<textarea class="input" id="bma_note" name="note" rows="3" placeholder="<?php esc_attr_e( 'Bármi, ami segít pontosabb ajánlatot adni', 'bma-ajanlatkeres' ); ?>"></textarea>
						</div>
					</div>

					<?php if ( ! is_user_logged_in() ) : ?>
						<label class="bma-checkrow">
							<input type="checkbox" name="create_account" value="1">
							<span class="small"><?php esc_html_e( 'Hozzon létre ügyfélfiókot, hogy egy helyen kövesse a kérés státuszát, az ajánlatot és a dokumentumokat.', 'bma-ajanlatkeres' ); ?></span>
						</label>
					<?php endif; ?>

					<div class="bma-form-field" data-bma-field="privacy" data-required="1">
						<label class="bma-checkrow">
							<input type="checkbox" name="privacy" value="1">
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
						<p class="bma-field-error" hidden></p>
					</div>
				</div>
			</fieldset>

			<?php /* ---------------- 07 Összegzés ---------------- */ ?>
			<fieldset class="panel wiz-step" data-step="7">
				<?php $panel_head( 7, __( 'Ellenőrizze, és küldje el', 'bma-ajanlatkeres' ), __( 'Minden blokk mellett ott a Szerkesztés gomb, amely a megfelelő lépésre visz.', 'bma-ajanlatkeres' ) ); ?>
				<div class="panel-body">
					<div id="bma-wiz-review" class="wiz-review" aria-live="polite"></div>
					<button type="submit" class="btn btn--primary wiz-submit"><?php esc_html_e( 'Ajánlatkérés beküldése', 'bma-ajanlatkeres' ); ?></button>
					<p class="small muted"><?php echo esc_html( BMA_AJ_Company::fill_response_days( __( 'A beküldés után e-mailben visszaigazolást küldünk, és [X] munkanapon belül jelentkezünk.', 'bma-ajanlatkeres' ) ) ); ?></p>
				</div>
			</fieldset>

			<div class="wiz-nav" data-wiz-nav hidden>
				<button type="button" class="btn btn--outline" data-wiz-prev><?php esc_html_e( 'Vissza', 'bma-ajanlatkeres' ); ?></button>
				<button type="button" class="btn btn--primary" data-wiz-next><?php esc_html_e( 'Tovább', 'bma-ajanlatkeres' ); ?></button>
			</div>
		</div>

		<aside class="panel summary wiz-aside">
			<div class="summary-head">
				<h3><?php esc_html_e( 'Összegzés', 'bma-ajanlatkeres' ); ?></h3>
				<span class="summary-count" data-wiz-count>0</span>
			</div>
			<div data-wiz-mini>
				<div class="sum-empty"><?php esc_html_e( 'Még nem választott elemet.', 'bma-ajanlatkeres' ); ?></div>
			</div>
			<div class="sum-foot">
				<span class="small muted"><?php esc_html_e( 'Az ajánlatkérés díjmentes és nem jár kötelezettséggel.', 'bma-ajanlatkeres' ); ?></span>
			</div>
		</aside>
	</form>
</div>
