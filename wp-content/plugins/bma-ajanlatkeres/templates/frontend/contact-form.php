<?php
/**
 * Contact page: intro, details, form, quote/portal CTAs, OpenStreetMap.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$phone   = BMA_AJ_Company::get( 'contact_phone' );
$email   = BMA_AJ_Company::get( 'contact_email' );
$addr    = BMA_AJ_Company::get( 'contact_address' );
$hours   = BMA_AJ_Company::get( 'opening_hours' );
$company = BMA_AJ_Company::get( 'company_name' );
$cui     = BMA_AJ_Company::get( 'cui' );
$lat     = BMA_AJ_Company::get( 'map_lat' );
$lng     = BMA_AJ_Company::get( 'map_lng' );
$zoom    = BMA_AJ_Company::get( 'map_zoom' ) ?: '15';

$addr_one_line = preg_replace( '/\s+/', ' ', str_replace( array( "\r\n", "\n", "\r" ), ', ', $addr ) );
$directions_url = sprintf(
	'https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=;%s%%2C%s#map=%s/%s/%s',
	rawurlencode( $lat ),
	rawurlencode( $lng ),
	rawurlencode( $zoom ),
	rawurlencode( $lat ),
	rawurlencode( $lng )
);

$topics = array(
	'general'     => __( 'Általános érdeklődés', 'bma-ajanlatkeres' ),
	'design'      => __( 'Tervezés', 'bma-ajanlatkeres' ),
	'permit'      => __( 'Engedélyeztetés', 'bma-ajanlatkeres' ),
	'docs'        => __( 'Műszaki dokumentáció', 'bma-ajanlatkeres' ),
	'utility'     => __( 'Közmű csatlakozás koordináció', 'bma-ajanlatkeres' ),
	'existing'    => __( 'Meglévő ajánlatkérés vagy projekt', 'bma-ajanlatkeres' ),
	'other'       => __( 'Egyéb', 'bma-ajanlatkeres' ),
);

$status      = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$privacy_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'adatvedelem' ) : home_url( '/adatvedelem/' ) );
?>
<section class="bma-section bma-page-contact" aria-labelledby="bma-contact-page-title">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php BMA_AJ_Content::e( 'contact.eyebrow' ); ?></p>
		<h1 id="bma-contact-page-title"><?php BMA_AJ_Content::e( 'contact.title' ); ?></h1>
		<p class="bma-section-intro">
			<?php BMA_AJ_Content::e( 'contact.intro_1' ); ?>
		</p>
		<p class="bma-section-intro">
			<?php BMA_AJ_Content::e( 'contact.intro_2' ); ?>
		</p>
		<p class="bma-section-cta bma-section-cta--left">
			<a class="bma-btn bma-btn-accent" href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ) ); ?>">
				<?php BMA_AJ_Content::e( 'contact.cta_quote' ); ?>
			</a>
		</p>

		<div class="bma-contact-layout">
			<div class="bma-contact-layout-info">
				<section class="bma-contact-details" aria-labelledby="bma-contact-details-title">
					<p class="bma-section-eyebrow"><?php BMA_AJ_Content::e( 'contact.details_eyebrow' ); ?></p>
					<h2 id="bma-contact-details-title"><?php echo esc_html( $company ); ?></h2>
					<ul class="bma-contact-details-list">
						<?php if ( $addr ) : ?>
							<li><strong><?php esc_html_e( 'Cím', 'bma-ajanlatkeres' ); ?></strong><span><?php echo nl2br( esc_html( $addr ) ); ?></span></li>
						<?php endif; ?>
						<?php if ( $phone ) : ?>
							<li>
								<strong><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?></strong>
								<span><a href="<?php echo esc_url( BMA_AJ_Company::phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></span>
							</li>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<li>
								<strong><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?></strong>
								<span><a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></span>
							</li>
						<?php endif; ?>
						<?php if ( $hours ) : ?>
							<li><strong><?php esc_html_e( 'Nyitvatartás', 'bma-ajanlatkeres' ); ?></strong><span><?php echo esc_html( $hours ); ?></span></li>
						<?php endif; ?>
						<?php if ( $cui ) : ?>
							<li><strong>CUI</strong><span><?php echo esc_html( $cui ); ?></span></li>
						<?php endif; ?>
					</ul>
					<?php if ( shortcode_exists( 'bma_social_links' ) ) : ?>
						<div class="bma-contact-details-social"><?php echo do_shortcode( '[bma_social_links]' ); ?></div>
					<?php endif; ?>
				</section>
			</div>

			<div class="bma-contact-layout-form">
				<?php if ( 'sent' === $status ) : ?>
					<div class="bma-notice bma-notice-success bma-contact-success" role="status">
						<h2><?php esc_html_e( 'Köszönjük az üzenetét.', 'bma-ajanlatkeres' ); ?></h2>
						<p><?php esc_html_e( 'Megkaptuk a megkeresését. A megadott elérhetőségek egyikén felvesszük Önnel a kapcsolatot.', 'bma-ajanlatkeres' ); ?></p>
					</div>
				<?php elseif ( 'error' === $status ) : ?>
					<div class="bma-notice bma-notice-error" role="alert">
						<?php esc_html_e( 'Az üzenet küldése nem sikerült. Ellenőrizze a kötelező mezőket, és próbálja újra.', 'bma-ajanlatkeres' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( 'sent' !== $status ) : ?>
					<p class="bma-section-eyebrow"><?php BMA_AJ_Content::e( 'contact.form_eyebrow' ); ?></p>
					<h2><?php BMA_AJ_Content::e( 'contact.form_title' ); ?></h2>
					<p class="bma-form-hint">
						<?php BMA_AJ_Content::e( 'contact.form_hint' ); ?>
					</p>

					<form class="bma-form bma-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
						<?php wp_nonce_field( 'bma_contact_form', 'bma_contact_nonce' ); ?>
						<input type="hidden" name="action" value="bma_contact_submit">
						<input type="hidden" name="bma_form_ts" value="<?php echo esc_attr( (string) time() ); ?>">

						<?php /* Honeypot – leave empty. */ ?>
						<div class="bma-hp" aria-hidden="true">
							<label for="bma_website"><?php esc_html_e( 'Weboldal', 'bma-ajanlatkeres' ); ?></label>
							<input type="text" id="bma_website" name="bma_website" value="" tabindex="-1" autocomplete="off">
						</div>

						<div class="bma-form-field">
							<label for="contact_name"><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input type="text" id="contact_name" name="name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Teljes név', 'bma-ajanlatkeres' ); ?>">
						</div>
						<div class="bma-form-field">
							<label for="contact_email"><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input type="email" id="contact_email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'nev@ceg.ro', 'bma-ajanlatkeres' ); ?>">
						</div>
						<div class="bma-form-field">
							<label for="contact_phone"><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?></label>
							<input type="tel" id="contact_phone" name="phone" autocomplete="tel" placeholder="<?php esc_attr_e( '+40 …', 'bma-ajanlatkeres' ); ?>">
						</div>
						<div class="bma-form-field">
							<label for="contact_topic"><?php esc_html_e( 'Téma', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<select id="contact_topic" name="topic" required data-bma-topic data-quote-topics="design,permit,docs,utility">
							<option value=""><?php esc_html_e( 'Válasszon témát…', 'bma-ajanlatkeres' ); ?></option>
							<?php foreach ( $topics as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						</div>
						<div class="bma-form-field">
							<label for="contact_message"><?php esc_html_e( 'Üzenet', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<textarea id="contact_message" name="message" rows="5" required placeholder="<?php esc_attr_e( 'Röviden írja le a kérdését vagy a projektjét…', 'bma-ajanlatkeres' ); ?>"></textarea>
						</div>

						<div class="bma-contact-quote-hint" data-bma-quote-hint hidden>
							<p><strong><?php esc_html_e( 'Konkrét projektre kérne ajánlatot?', 'bma-ajanlatkeres' ); ?></strong></p>
							<p><?php esc_html_e( 'Az ajánlatkérőben kiválaszthatja a szükséges részfeladatokat és dokumentumokat is feltölthet.', 'bma-ajanlatkeres' ); ?></p>
							<p>
								<a class="bma-btn bma-btn-outline" href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ) ); ?>">
									<?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?>
								</a>
							</p>
						</div>

						<div class="bma-form-field">
							<label class="bma-checkbox-label">
								<input type="checkbox" name="privacy" value="1" required>
								<?php
								printf(
									/* translators: %s: privacy policy URL */
									wp_kses(
										__( 'Elolvastam és elfogadom az <a href="%s">adatvédelmi tájékoztatóban</a> foglaltakat.', 'bma-ajanlatkeres' ),
										array( 'a' => array( 'href' => array() ) )
									),
									esc_url( $privacy_url )
								);
								?>
							</label>
						</div>
						<button type="submit" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Üzenet küldése', 'bma-ajanlatkeres' ); ?></button>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<div class="bma-contact-cta-grid">
			<section class="bma-contact-cta-block" aria-labelledby="bma-contact-quote-title">
				<h2 id="bma-contact-quote-title"><?php BMA_AJ_Content::e( 'contact.quote_title' ); ?></h2>
				<p><?php BMA_AJ_Content::e( 'contact.quote_body' ); ?></p>
				<ul class="bma-contact-cta-list">
					<li><?php BMA_AJ_Content::e( 'contact.quote_li_1' ); ?></li>
					<li><?php BMA_AJ_Content::e( 'contact.quote_li_2' ); ?></li>
					<li><?php BMA_AJ_Content::e( 'contact.quote_li_3' ); ?></li>
					<li><?php BMA_AJ_Content::e( 'contact.quote_li_4' ); ?></li>
					<li><?php BMA_AJ_Content::e( 'contact.quote_li_5' ); ?></li>
				</ul>
				<p>
					<a class="bma-btn bma-btn-accent" href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ) ); ?>">
						<?php BMA_AJ_Content::e( 'contact.cta_quote' ); ?>
					</a>
				</p>
			</section>

			<section class="bma-contact-cta-block" aria-labelledby="bma-contact-portal-title">
				<h2 id="bma-contact-portal-title"><?php BMA_AJ_Content::e( 'contact.portal_title' ); ?></h2>
				<p><?php BMA_AJ_Content::e( 'contact.portal_body' ); ?></p>
				<p>
					<a class="bma-btn bma-btn-outline" href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' ) ) ); ?>">
						<?php BMA_AJ_Content::e( 'contact.portal_cta' ); ?>
					</a>
				</p>
			</section>
		</div>

		<section class="bma-contact-map-section" aria-labelledby="bma-contact-map-title"
			data-bma-map
			data-lat="<?php echo esc_attr( $lat ); ?>"
			data-lng="<?php echo esc_attr( $lng ); ?>"
			data-zoom="<?php echo esc_attr( $zoom ); ?>"
			data-label="<?php echo esc_attr( $company ); ?>"
			data-address="<?php echo esc_attr( $addr_one_line ); ?>">
			<p class="bma-section-eyebrow"><?php BMA_AJ_Content::e( 'contact.map_eyebrow' ); ?></p>
			<h2 id="bma-contact-map-title"><?php BMA_AJ_Content::e( 'contact.map_title' ); ?></h2>
			<p class="bma-contact-map-address">
				<strong><?php echo esc_html( $company ); ?></strong><br>
				<?php echo esc_html( $addr_one_line ); ?>
			</p>
			<p class="bma-contact-map-actions">
				<a class="bma-btn bma-btn-accent" href="<?php echo esc_url( $directions_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Útvonaltervezés', 'bma-ajanlatkeres' ); ?>
				</a>
				<button type="button" class="bma-btn bma-btn-outline" data-bma-map-load>
					<?php esc_html_e( 'Térkép betöltése', 'bma-ajanlatkeres' ); ?>
				</button>
			</p>
			<p class="bma-contact-map-consent-note" data-bma-map-note>
				<?php esc_html_e( 'A térkép OpenStreetMap csempéket tölt be. A megjelenítéshez kattintson a „Térkép betöltése” gombra, vagy fogadja el a cookie beállításokban a funkcionális térképet.', 'bma-ajanlatkeres' ); ?>
			</p>
			<div class="bma-contact-map" data-bma-map-canvas hidden role="presentation"></div>
		</section>
	</div>
</section>
