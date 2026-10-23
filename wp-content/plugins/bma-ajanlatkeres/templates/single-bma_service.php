<?php
/**
 * Single service template (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$service_id   = (int) get_the_ID();
	$service_name = get_the_title();
	$items        = BMA_AJ_Dynamic_Fields::get_service_items( $service_id );
	$item_count   = count( $items );
	$short_desc   = get_post_meta( $service_id, '_bma_short_description', true ) ?: get_the_excerpt();
	$source_id    = BMA_AJ_I18n::source_post_id( $service_id );
	$has_phase    = '1' !== get_post_meta( $source_id, '_bma_no_design_phase', true );
	$quote_url    = BMA_AJ_I18n::page_url( 'ajanlatkeres' );
	$addons       = BMA_AJ_Quote_Flow::topical_addons( $service_id );
	$related      = BMA_AJ_Service_CPT::get_related_services( $service_id );

	$home_url     = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
	$services_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/' );
	$contact_url  = function_exists( 'bma_page_url' ) ? bma_page_url( 'kapcsolat' ) : home_url( '/' );

	$needs_required = BMA_AJ_Service_CPT::lines_from_meta( (string) get_post_meta( $service_id, '_bma_needs_required', true ) );
	$needs_optional = BMA_AJ_Service_CPT::lines_from_meta( (string) get_post_meta( $service_id, '_bma_needs_optional', true ) );
	$needs_later    = BMA_AJ_Service_CPT::lines_from_meta( (string) get_post_meta( $service_id, '_bma_needs_later', true ) );
	$show_needs     = $needs_required || $needs_optional || $needs_later;
	$faq_pairs      = BMA_AJ_Service_CPT::faq_from_meta( (string) get_post_meta( $service_id, '_bma_faq', true ) );

	$items_label = sprintf(
		/* translators: %d: number of selectable service items */
		__( '%d választható részfeladat', 'bma-ajanlatkeres' ),
		$item_count
	);

	$check_svg = '<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5l5 5L20 6.5"></path></svg>';
	?>
	<article <?php post_class( 'bma-single-service' ); ?>>
		<section class="pagehead">
			<div class="pagehead-inner pagehead-inner--split">
				<div>
					<p class="crumb">
						<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
						/
						<a href="<?php echo esc_url( $services_url ); ?>"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></a>
						/ <?php echo esc_html( $service_name ); ?>
					</p>
					<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></p>
					<h1><?php echo esc_html( $service_name ); ?></h1>
					<?php if ( $short_desc ) : ?>
						<p class="pagehead-lead"><?php echo esc_html( $short_desc ); ?></p>
					<?php endif; ?>
					<div class="btnrow" style="display:flex;gap:14px;margin-top:30px;flex-wrap:wrap">
						<a href="<?php echo esc_url( add_query_arg( 'service', $service_id, $quote_url ) ); ?>" class="btn btn--primary">
							<?php esc_html_e( 'Ajánlatkérés indítása ezzel a szolgáltatással', 'bma-ajanlatkeres' ); ?>
						</a>
						<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--ghost-light"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></a>
					</div>
				</div>
				<div class="sidecard">
					<h3><?php esc_html_e( 'Röviden', 'bma-ajanlatkeres' ); ?></h3>
					<ul>
						<li><?php echo $check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $items_label ); ?></span></li>
						<li><?php echo $check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Lakossági és üzleti projektekhez', 'bma-ajanlatkeres' ); ?></span></li>
						<li><?php echo $check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( BMA_AJ_Company::fill_response_days( __( 'Ajánlat [X] munkanapon belül', 'bma-ajanlatkeres' ) ) ); ?></span></li>
						<li><?php echo $check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Tipikus átfutás: [X–Y] hét', 'bma-ajanlatkeres' ); ?></span></li>
					</ul>
					<span class="tiny" style="color:#8fa2c4;display:block">
						<?php esc_html_e( 'A pontos határidő a projekt méretétől és a szolgáltatói egyeztetésektől függ.', 'bma-ajanlatkeres' ); ?>
					</span>
				</div>
			</div>
		</section>

		<?php if ( $items ) : ?>
			<section class="bma-section" id="bma-service-items" aria-labelledby="bma-service-items-title">
				<div class="bma-container">
					<p class="eyebrow"><?php esc_html_e( 'Részfeladatok', 'bma-ajanlatkeres' ); ?></p>
					<h2 class="h2" id="bma-service-items-title" style="margin-bottom:14px">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: service title */
								__( 'Mit tartalmaz a(z) %s?', 'bma-ajanlatkeres' ),
								$service_name
							)
						);
						?>
					</h2>
					<p class="lead" style="margin-bottom:32px">
						<?php esc_html_e( 'Válassza ki azokat a tételeket, amelyekre a projektjéhez szüksége van. Bármelyik kombinálható a többi szolgáltatási területtel.', 'bma-ajanlatkeres' ); ?>
					</p>
					<form method="get" action="<?php echo esc_url( $quote_url ); ?>" class="bma-detail-select">
					<input type="hidden" name="service" value="<?php echo esc_attr( (string) $service_id ); ?>">
					<div class="tasklist">
						<?php foreach ( $items as $item ) : ?>
							<?php $choose = isset( $item->choose_if ) ? (string) $item->choose_if : ''; ?>
							<label class="taskcard">
								<input type="checkbox" class="visually-hidden" name="items[]" value="<?php echo esc_attr( (string) $item->id ); ?>">
								<h3><?php echo esc_html( $item->name ); ?></h3>
								<?php if ( ! empty( $item->short_description ) ) : ?>
									<p><?php echo esc_html( $item->short_description ); ?></p>
								<?php endif; ?>
								<?php if ( $choose ) : ?>
									<div class="when">
										<b><?php esc_html_e( 'Ezt válassza, ha:', 'bma-ajanlatkeres' ); ?></b>
										<span><?php echo esc_html( $choose ); ?></span>
									</div>
								<?php endif; ?>
								<div class="taskcard-foot">
									<span class="box" aria-hidden="true"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"></path></svg></span>
									<span class="small muted"><?php esc_html_e( 'Kijelölés', 'bma-ajanlatkeres' ); ?></span>
								</div>
							</label>
						<?php endforeach; ?>
					</div>
					<p class="small muted" style="margin:18px 0">
						<?php
						echo esc_html(
							$has_phase
								? __( 'Minden elem kérhető engedélyes és kivitelezési tervként is.', 'bma-ajanlatkeres' )
								: __( 'A bekötési tervek egyetlen fázisban készülnek.', 'bma-ajanlatkeres' )
						);
						?>
					</p>
					<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés indítása ezzel a szolgáltatással', 'bma-ajanlatkeres' ); ?></button>
					</form>
					<script>
					document.querySelectorAll('.bma-detail-select .taskcard input').forEach(function (i) {
						i.addEventListener('change', function () { i.closest('.taskcard').classList.toggle('is-sel', i.checked); });
					});
					</script>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $show_needs ) : ?>
			<section class="bma-section bma-section--bg" aria-labelledby="bma-needs-title">
				<div class="bma-container">
					<p class="eyebrow"><?php esc_html_e( 'Előkészítés', 'bma-ajanlatkeres' ); ?></p>
					<h2 class="h2" id="bma-needs-title" style="margin-bottom:14px"><?php esc_html_e( 'Mire lesz szükség?', 'bma-ajanlatkeres' ); ?></h2>
					<p class="lead" style="margin-bottom:32px">
						<?php esc_html_e( 'Nem baj, ha nincs meg minden. Az ajánlatkéréshez elég annyi, amennyi rendelkezésére áll – a többit menet közben pótoljuk.', 'bma-ajanlatkeres' ); ?>
					</p>
					<div class="need">
						<?php if ( $needs_required ) : ?>
							<div class="need-card">
								<h3><?php esc_html_e( 'Kötelező', 'bma-ajanlatkeres' ); ?></h3>
								<ul>
									<?php foreach ( $needs_required as $line ) : ?>
										<li><?php echo esc_html( $line ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<?php if ( $needs_optional ) : ?>
							<div class="need-card">
								<h3><?php esc_html_e( 'Ha rendelkezésre áll', 'bma-ajanlatkeres' ); ?></h3>
								<ul>
									<?php foreach ( $needs_optional as $line ) : ?>
										<li><?php echo esc_html( $line ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<?php if ( $needs_later ) : ?>
							<div class="need-card">
								<h3><?php esc_html_e( 'Később is pótolható', 'bma-ajanlatkeres' ); ?></h3>
								<ul>
									<?php foreach ( $needs_later as $line ) : ?>
										<li><?php echo esc_html( $line ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $addons ) : ?>
			<section class="bma-section" aria-labelledby="bma-addons-title">
				<div class="bma-container">
					<p class="eyebrow"><?php esc_html_e( 'Kiegészítők', 'bma-ajanlatkeres' ); ?></p>
					<h2 class="h2" id="bma-addons-title" style="margin-bottom:14px"><?php esc_html_e( 'Ezeket is intézzük', 'bma-ajanlatkeres' ); ?></h2>
					<p class="lead" style="margin-bottom:28px"><?php esc_html_e( 'Amit nem tud csatolni, azt beszerezzük. A beszerzés költsége és ideje az ajánlatban külön tételként szerepel.', 'bma-ajanlatkeres' ); ?></p>
					<div class="need">
						<?php foreach ( $addons as $addon ) : ?>
							<div class="need-card">
								<h3><?php echo esc_html( $addon->name ); ?></h3>
								<p class="small muted" style="margin:0"><?php echo esc_html( $addon->short_description ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section class="bma-section" aria-labelledby="bma-how-title">
			<div class="bma-container">
				<p class="eyebrow"><?php esc_html_e( 'Folyamat', 'bma-ajanlatkeres' ); ?></p>
				<h2 class="h2" id="bma-how-title" style="margin-bottom:34px"><?php esc_html_e( 'Hogyan zajlik?', 'bma-ajanlatkeres' ); ?></h2>
				<div class="process">
					<div class="step step--active">
						<div class="step-num">01</div>
						<h3><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></h3>
						<p><?php esc_html_e( 'Kiválasztja a részfeladatokat, megadja a projekt adatait, és feltölti a meglévő dokumentumokat.', 'bma-ajanlatkeres' ); ?></p>
					</div>
					<div class="step step--active">
						<div class="step-num">02</div>
						<h3><?php esc_html_e( 'Felmérés és ajánlat', 'bma-ajanlatkeres' ); ?></h3>
						<p><?php esc_html_e( 'Átnézzük az adatokat, szükség esetén egyeztetünk, majd elkészítjük az ajánlatot.', 'bma-ajanlatkeres' ); ?></p>
					</div>
					<div class="step step--active">
						<div class="step-num">03</div>
						<h3><?php esc_html_e( 'Tervezés', 'bma-ajanlatkeres' ); ?></h3>
						<p><?php esc_html_e( 'Az elfogadott ajánlat után elkészülnek a tervek, a szükséges szolgáltatói egyeztetésekkel.', 'bma-ajanlatkeres' ); ?></p>
					</div>
					<div class="step">
						<div class="step-num">04</div>
						<h3><?php esc_html_e( 'Átadás', 'bma-ajanlatkeres' ); ?></h3>
						<p><?php esc_html_e( 'A kész terveket az ügyfélportálon adjuk át, a kapcsolódó dokumentumokkal együtt.', 'bma-ajanlatkeres' ); ?></p>
					</div>
				</div>
			</div>
		</section>

		<?php if ( $related ) : ?>
			<section class="bma-section bma-section--bg" aria-labelledby="bma-related-title">
				<div class="bma-container">
					<p class="eyebrow"><?php esc_html_e( 'Kapcsolódó', 'bma-ajanlatkeres' ); ?></p>
					<h2 class="h2" id="bma-related-title" style="margin-bottom:26px"><?php esc_html_e( 'Egyéb szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
					<div class="svc-grid">
						<?php foreach ( $related as $rel ) : ?>
							<a class="svc-card" href="<?php echo esc_url( function_exists( 'bma_post_url' ) ? bma_post_url( (int) $rel->ID ) : get_permalink( $rel ) ); ?>">
								<h3><?php echo esc_html( get_the_title( $rel ) ); ?></h3>
								<p><?php echo esc_html( get_post_meta( $rel->ID, '_bma_short_description', true ) ?: $rel->post_excerpt ); ?></p>
								<span class="svc-foot"><?php esc_html_e( 'Részletek →', 'bma-ajanlatkeres' ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $faq_pairs ) : ?>
			<section class="bma-section bma-section--bg" aria-labelledby="bma-faq-title">
				<div class="bma-container" style="max-width:900px">
					<p class="eyebrow"><?php esc_html_e( 'Gyakori kérdések', 'bma-ajanlatkeres' ); ?></p>
					<h2 class="h2" id="bma-faq-title" style="margin-bottom:26px"><?php esc_html_e( 'Amit sokan kérdeznek', 'bma-ajanlatkeres' ); ?></h2>
					<div class="faq">
						<?php foreach ( $faq_pairs as $i => $pair ) : ?>
							<details class="faq-item"<?php echo 0 === $i ? ' open' : ''; ?>>
								<summary class="faq-q"><?php echo esc_html( $pair['q'] ); ?></summary>
								<p class="faq-a"><?php echo esc_html( $pair['a'] ); ?></p>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section class="closing">
			<div class="closing-inner">
				<div>
					<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Következő lépés', 'bma-ajanlatkeres' ); ?></p>
					<h2 class="h2">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: service title */
								__( 'Kérjen ajánlatot: %s', 'bma-ajanlatkeres' ),
								$service_name
							)
						);
						?>
					</h2>
					<p><?php esc_html_e( 'Adja meg a projekt alapadatait, válassza ki a szükséges terveket, és töltse fel, ami megvan. Az ajánlatkérés díjmentes és nem jár kötelezettséggel.', 'bma-ajanlatkeres' ); ?></p>
				</div>
				<div class="closing-actions">
					<a href="<?php echo esc_url( add_query_arg( 'service', $service_id, $quote_url ) ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
					<a href="<?php echo esc_url( $services_url ); ?>" class="btn btn--ghost-light"><?php esc_html_e( 'Összes szolgáltatás', 'bma-ajanlatkeres' ); ?></a>
				</div>
			</div>
		</section>
	</article>
	<?php
endwhile;

get_footer();
