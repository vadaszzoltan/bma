<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1><?php esc_html_e( 'BMA beállítások', 'bma-ajanlatkeres' ); ?></h1>
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bma_settings' );
		$colors  = BMA_AJ_Appearance::get_colors();
		$fonts   = BMA_AJ_Appearance::get_fonts();
		$catalog = BMA_AJ_Appearance::font_catalog();
		$labels  = array(
			'primary'      => __( 'Elsődleges', 'bma-ajanlatkeres' ),
			'primary_dark' => __( 'Elsődleges sötét', 'bma-ajanlatkeres' ),
			'accent'       => __( 'Kiemelés', 'bma-ajanlatkeres' ),
			'text'         => __( 'Szöveg', 'bma-ajanlatkeres' ),
			'muted'        => __( 'Halvány szöveg', 'bma-ajanlatkeres' ),
			'border'       => __( 'Keret', 'bma-ajanlatkeres' ),
			'bg'           => __( 'Oldal háttér', 'bma-ajanlatkeres' ),
			'surface'      => __( 'Kártya / felület', 'bma-ajanlatkeres' ),
			'footer_bg'    => __( 'Lábléc háttér', 'bma-ajanlatkeres' ),
			'footer_text'  => __( 'Lábléc szöveg', 'bma-ajanlatkeres' ),
			'danger'       => __( 'Hiba / veszély', 'bma-ajanlatkeres' ),
			'success'      => __( 'Siker', 'bma-ajanlatkeres' ),
			'focus'        => __( 'Fókusz gyűrű', 'bma-ajanlatkeres' ),
		);
		?>

		<h2><?php esc_html_e( 'Megjelenés', 'bma-ajanlatkeres' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Színpaletta és betűtípusok – azonnal érvényesülnek a frontenden. A CSS design tokeneken keresztül hat a témára és a bővítményre.', 'bma-ajanlatkeres' ); ?></p>
		<table class="form-table" role="presentation">
			<?php foreach ( $labels as $key => $label ) : ?>
				<tr>
					<th scope="row"><label for="bma_color_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
					<td>
						<input
							type="text"
							class="bma-color-field"
							id="bma_color_<?php echo esc_attr( $key ); ?>"
							name="<?php echo esc_attr( BMA_AJ_Appearance::OPTION_COLORS ); ?>[<?php echo esc_attr( $key ); ?>]"
							value="<?php echo esc_attr( $colors[ $key ] ); ?>"
							data-default-color="<?php echo esc_attr( BMA_AJ_Appearance::default_colors()[ $key ] ); ?>"
						>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<th scope="row"><label for="bma_font_heading"><?php esc_html_e( 'Címsor betűtípus', 'bma-ajanlatkeres' ); ?></label></th>
				<td>
					<select id="bma_font_heading" name="<?php echo esc_attr( BMA_AJ_Appearance::OPTION_FONTS ); ?>[heading]">
						<?php foreach ( $catalog as $slug => $font ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $fonts['heading'], $slug ); ?>><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="bma_font_body"><?php esc_html_e( 'Törzsszöveg betűtípus', 'bma-ajanlatkeres' ); ?></label></th>
				<td>
					<select id="bma_font_body" name="<?php echo esc_attr( BMA_AJ_Appearance::OPTION_FONTS ); ?>[body]">
						<?php foreach ( $catalog as $slug => $font ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $fonts['body'], $slug ); ?>><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="bma_appearance_radius"><?php esc_html_e( 'Saroklekerekítés (px)', 'bma-ajanlatkeres' ); ?></label></th>
				<td>
					<input type="number" min="0" max="24" id="bma_appearance_radius" name="<?php echo esc_attr( BMA_AJ_Appearance::OPTION_RADIUS ); ?>" value="<?php echo esc_attr( (string) BMA_AJ_Appearance::get_radius() ); ?>">
				</td>
			</tr>
		</table>

		<h2><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th><?php esc_html_e( 'Cégnév', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_company_name" value="<?php echo esc_attr( get_option( 'bma_company_name', BMA_AJ_Company::defaults()['company_name'] ) ); ?>" class="regular-text"></td></tr>
			<tr><th>CUI</th><td><input type="text" name="bma_company_cui" value="<?php echo esc_attr( get_option( 'bma_company_cui', BMA_AJ_Company::defaults()['cui'] ) ); ?>" class="regular-text" placeholder="40593519"></td></tr>
			<tr><th><?php esc_html_e( 'Admin értesítési e-mail', 'bma-ajanlatkeres' ); ?></th><td><input type="email" name="bma_admin_email" value="<?php echo esc_attr( get_option( 'bma_admin_email' ) ); ?>" class="regular-text" placeholder="office@bmaeng.ro"></td></tr>
			<tr><th><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_contact_phone" value="<?php echo esc_attr( get_option( 'bma_contact_phone' ) ); ?>" class="regular-text" placeholder="+40 728 205 982"></td></tr>
			<tr><th><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?></th><td><input type="email" name="bma_contact_email" value="<?php echo esc_attr( get_option( 'bma_contact_email' ) ); ?>" class="regular-text" placeholder="office@bmaeng.ro"></td></tr>
			<tr><th><?php esc_html_e( 'Cím', 'bma-ajanlatkeres' ); ?></th><td><textarea name="bma_contact_address" rows="3" class="large-text" placeholder="Str. Raza Soarelui 11&#10;535600 Odorheiu Secuiesc"><?php echo esc_textarea( get_option( 'bma_contact_address' ) ); ?></textarea></td></tr>
			<tr><th><?php esc_html_e( 'Nyitvatartás', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_opening_hours" value="<?php echo esc_attr( get_option( 'bma_opening_hours', BMA_AJ_Company::defaults()['opening_hours'] ) ); ?>" class="regular-text" placeholder="Hétfő–Péntek, 08:00–17:00"></td></tr>
			<tr><th><?php esc_html_e( 'Válaszidő ajánlatkérésre (munkanap)', 'bma-ajanlatkeres' ); ?></th><td><input type="number" min="0" max="60" name="bma_response_days" value="<?php echo esc_attr( BMA_AJ_Company::response_days() ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'A weboldal szövegeiben a [X] helyére kerül (pl. „Válasz [X] munkanapon belül”), minden nyelven.', 'bma-ajanlatkeres' ); ?></p></td></tr>
			<tr><th><?php esc_html_e( 'Térkép – szélesség (lat)', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_map_lat" value="<?php echo esc_attr( get_option( 'bma_map_lat', BMA_AJ_Company::defaults()['map_lat'] ) ); ?>" class="regular-text" placeholder="46.3047462"></td></tr>
			<tr><th><?php esc_html_e( 'Térkép – hosszúság (lng)', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_map_lng" value="<?php echo esc_attr( get_option( 'bma_map_lng', BMA_AJ_Company::defaults()['map_lng'] ) ); ?>" class="regular-text" placeholder="25.2950261"></td></tr>
			<tr><th><?php esc_html_e( 'Térkép – zoom', 'bma-ajanlatkeres' ); ?></th><td><input type="number" min="1" max="20" name="bma_map_zoom" value="<?php echo esc_attr( get_option( 'bma_map_zoom', BMA_AJ_Company::defaults()['map_zoom'] ) ); ?>" class="small-text"></td></tr>
		</table>

		<?php
		$footer_langs = array(
			'hu' => 'Magyar',
			'ro' => 'Română',
			'en' => 'English',
		);
		$footer_fields = array(
			'footer.nav_heading' => __( 'Navigáció cím', 'bma-ajanlatkeres' ),
			'footer.cta_heading' => __( 'Ajánlatkérés cím', 'bma-ajanlatkeres' ),
			'footer.cta_blurb'   => __( 'Ajánlatkérés szöveg', 'bma-ajanlatkeres' ),
			'footer.cta_button'  => __( 'Gomb', 'bma-ajanlatkeres' ),
		);
		$footer_lang = isset( $_GET['bma_footer_lang'] ) ? sanitize_key( wp_unslash( $_GET['bma_footer_lang'] ) ) : 'hu'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $footer_langs[ $footer_lang ] ) ) {
			$footer_lang = 'hu';
		}
		$footer_stored = get_option( BMA_AJ_Content::FOOTER_OPTION, array() );
		if ( ! is_array( $footer_stored ) ) {
			$footer_stored = array();
		}
		?>
		<h2><?php esc_html_e( 'Lábléc szövegek', 'bma-ajanlatkeres' ); ?></h2>
		<p class="description"><?php esc_html_e( 'A láblécnek nincs saját oldala; a szövegek itt szerkeszthetők. A mezők a jelenlegi tartalommal vannak kitöltve.', 'bma-ajanlatkeres' ); ?></p>
		<p>
			<?php foreach ( $footer_langs as $code => $label ) : ?>
				<?php if ( $code !== 'hu' ) : ?> · <?php endif; ?>
				<?php if ( $footer_lang === $code ) : ?>
					<strong><?php echo esc_html( $label ); ?></strong>
				<?php else : ?>
					<a href="<?php echo esc_url( add_query_arg( 'bma_footer_lang', $code ) ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</p>
		<?php
		foreach ( array( 'hu', 'ro', 'en' ) as $code ) {
			if ( $code === $footer_lang || empty( $footer_stored[ $code ] ) || ! is_array( $footer_stored[ $code ] ) ) {
				continue;
			}
			foreach ( $footer_stored[ $code ] as $fkey => $fval ) {
				if ( ! is_string( $fval ) || $fval === '' ) {
					continue;
				}
				printf(
					'<input type="hidden" name="%s[%s][%s]" value="%s" />' . "\n",
					esc_attr( BMA_AJ_Content::FOOTER_OPTION ),
					esc_attr( $code ),
					esc_attr( $fkey ),
					esc_attr( $fval )
				);
			}
		}
		?>
		<table class="form-table">
			<?php foreach ( $footer_fields as $fkey => $flabel ) : ?>
				<?php
				$fval = '';
				if ( ! empty( $footer_stored[ $footer_lang ][ $fkey ] ) && is_string( $footer_stored[ $footer_lang ][ $fkey ] ) ) {
					$fval = $footer_stored[ $footer_lang ][ $fkey ];
				} else {
					$legacy = BMA_AJ_Content::legacy_stored();
					if ( ! empty( $legacy[ $footer_lang ][ $fkey ] ) && is_string( $legacy[ $footer_lang ][ $fkey ] ) ) {
						$fval = $legacy[ $footer_lang ][ $fkey ];
					} else {
						$fval = BMA_AJ_Content::default_for( $fkey, $footer_lang );
					}
				}
				$is_blurb = ( 'footer.cta_blurb' === $fkey );
				?>
				<tr>
					<th><?php echo esc_html( $flabel ); ?> <span class="description">(<?php echo esc_html( $footer_langs[ $footer_lang ] ); ?>)</span></th>
					<td>
						<?php if ( $is_blurb ) : ?>
							<textarea class="large-text" rows="3" name="<?php echo esc_attr( BMA_AJ_Content::FOOTER_OPTION . '[' . $footer_lang . '][' . $fkey . ']' ); ?>"><?php echo esc_textarea( $fval ); ?></textarea>
						<?php else : ?>
							<input type="text" class="large-text" name="<?php echo esc_attr( BMA_AJ_Content::FOOTER_OPTION . '[' . $footer_lang . '][' . $fkey . ']' ); ?>" value="<?php echo esc_attr( $fval ); ?>">
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<h2><?php esc_html_e( 'Közösségi média', 'bma-ajanlatkeres' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Csak valós profil-URL-t adjon meg. Üres mező nem jelenik meg a frontenden.', 'bma-ajanlatkeres' ); ?></p>
		<table class="form-table">
			<tr><th>Facebook</th><td><input type="url" name="bma_social_facebook" value="<?php echo esc_url( get_option( 'bma_social_facebook' ) ); ?>" class="regular-text" placeholder="https://www.facebook.com/…"></td></tr>
			<tr><th>LinkedIn</th><td><input type="url" name="bma_social_linkedin" value="<?php echo esc_url( get_option( 'bma_social_linkedin' ) ); ?>" class="regular-text" placeholder="https://www.linkedin.com/company/…"></td></tr>
			<tr><th>Instagram</th><td><input type="url" name="bma_social_instagram" value="<?php echo esc_url( get_option( 'bma_social_instagram' ) ); ?>" class="regular-text" placeholder="https://www.instagram.com/…"></td></tr>
			<tr><th>X</th><td><input type="url" name="bma_social_x" value="<?php echo esc_url( get_option( 'bma_social_x' ) ); ?>" class="regular-text" placeholder="https://x.com/…"></td></tr>
			<tr><th>YouTube</th><td><input type="url" name="bma_social_youtube" value="<?php echo esc_url( get_option( 'bma_social_youtube' ) ); ?>" class="regular-text" placeholder="https://www.youtube.com/@…"></td></tr>
		</table>

		<h2><?php esc_html_e( 'Analitika', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th>GA4 ID</th><td><input type="text" name="bma_ga4_id" value="<?php echo esc_attr( get_option( 'bma_ga4_id' ) ); ?>" class="regular-text" placeholder="G-XXXXXXXX"></td></tr>
			<tr><th>GTM ID</th><td><input type="text" name="bma_gtm_id" value="<?php echo esc_attr( get_option( 'bma_gtm_id' ) ); ?>" class="regular-text" placeholder="GTM-XXXXXXX"></td></tr>
		</table>

		<h2><?php esc_html_e( 'SMTP', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th>Host</th><td><input type="text" name="bma_smtp_host" value="<?php echo esc_attr( get_option( 'bma_smtp_host' ) ); ?>" class="regular-text"></td></tr>
			<tr><th>Port</th><td><input type="number" name="bma_smtp_port" value="<?php echo esc_attr( get_option( 'bma_smtp_port', 587 ) ); ?>"></td></tr>
			<tr><th>Feladó e-mail</th><td><input type="email" name="bma_smtp_from" value="<?php echo esc_attr( get_option( 'bma_smtp_from' ) ); ?>" class="regular-text"></td></tr>
			<tr><th>Feladó név</th><td><input type="text" name="bma_smtp_from_name" value="<?php echo esc_attr( get_option( 'bma_smtp_from_name', 'BMA Engineering' ) ); ?>" class="regular-text"></td></tr>
		</table>

		<h2><?php esc_html_e( 'Stripe fizetés', 'bma-ajanlatkeres' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Kártyás fizetéshez Stripe API kulcsok. Webhook URL:', 'bma-ajanlatkeres' ); ?> <code><?php echo esc_html( rest_url( 'bma/v1/stripe-webhook' ) ); ?></code></p>
		<?php if ( defined( 'BMA_STRIPE_SECRET_KEY' ) && BMA_STRIPE_SECRET_KEY ) : ?>
			<p class="description"><strong><?php esc_html_e( 'A Stripe kulcsok környezeti változóból töltődnek (.env / Docker). Az alábbi mezők csak tartalék.', 'bma-ajanlatkeres' ); ?></strong></p>
		<?php endif; ?>
		<table class="form-table">
			<?php if ( defined( 'BMA_STRIPE_PUBLISHABLE_KEY' ) && BMA_STRIPE_PUBLISHABLE_KEY ) : ?>
			<tr><th><?php esc_html_e( 'Publishable key', 'bma-ajanlatkeres' ); ?></th><td><code><?php echo esc_html( substr( BMA_STRIPE_PUBLISHABLE_KEY, 0, 12 ) ); ?>…</code></td></tr>
			<tr><th><?php esc_html_e( 'Secret key', 'bma-ajanlatkeres' ); ?></th><td><code>sk_…<?php esc_html_e( '(környezeti változó)', 'bma-ajanlatkeres' ); ?></code></td></tr>
			<?php else : ?>
			<tr><th><?php esc_html_e( 'Publishable key', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_stripe_publishable_key" value="<?php echo esc_attr( get_option( 'bma_stripe_publishable_key' ) ); ?>" class="large-text" autocomplete="off"></td></tr>
			<tr><th><?php esc_html_e( 'Secret key', 'bma-ajanlatkeres' ); ?></th><td><input type="password" name="bma_stripe_secret_key" value="<?php echo esc_attr( get_option( 'bma_stripe_secret_key' ) ); ?>" class="large-text" autocomplete="off"></td></tr>
			<?php endif; ?>
			<?php if ( ! ( defined( 'BMA_STRIPE_WEBHOOK_SECRET' ) && BMA_STRIPE_WEBHOOK_SECRET ) ) : ?>
			<tr><th><?php esc_html_e( 'Webhook secret', 'bma-ajanlatkeres' ); ?></th><td><input type="password" name="bma_stripe_webhook_secret" value="<?php echo esc_attr( get_option( 'bma_stripe_webhook_secret' ) ); ?>" class="large-text" autocomplete="off"></td></tr>
			<?php else : ?>
			<tr><th><?php esc_html_e( 'Webhook secret', 'bma-ajanlatkeres' ); ?></th><td><code>whsec_…<?php esc_html_e( '(környezeti változó)', 'bma-ajanlatkeres' ); ?></code></td></tr>
			<?php endif; ?>
			<tr><th><?php esc_html_e( 'Stripe számla létrehozása', 'bma-ajanlatkeres' ); ?></th><td>
				<input type="hidden" name="bma_stripe_invoice_creation" value="0">
				<label><input type="checkbox" name="bma_stripe_invoice_creation" value="1" <?php checked( get_option( 'bma_stripe_invoice_creation', '1' ), '1' ); ?>> <?php esc_html_e( 'Checkout után Stripe számla (Invoicing)', 'bma-ajanlatkeres' ); ?></label>
			</td></tr>
			<tr><th><?php esc_html_e( 'Automatikus adó (Stripe Tax)', 'bma-ajanlatkeres' ); ?></th><td>
				<input type="hidden" name="bma_stripe_automatic_tax" value="0">
				<label><input type="checkbox" name="bma_stripe_automatic_tax" value="1" <?php checked( get_option( 'bma_stripe_automatic_tax', '0' ), '1' ); ?>> <?php esc_html_e( 'Stripe Tax automatikus ÁFA számítás (előbb engedélyezze a Stripe Dashboardon)', 'bma-ajanlatkeres' ); ?></label>
			</td></tr>
		</table>

		<h2><?php esc_html_e( 'Banki utalás', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th><?php esc_html_e( 'Kedvezményezett', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_bank_beneficiary" value="<?php echo esc_attr( get_option( 'bma_bank_beneficiary' ) ); ?>" class="regular-text" placeholder="BMA ENGINEERING S.R.L."></td></tr>
			<tr><th>IBAN</th><td><input type="text" name="bma_bank_iban" value="<?php echo esc_attr( get_option( 'bma_bank_iban' ) ); ?>" class="regular-text" placeholder="RO00 BANK 0000 0000 0000 0000"></td></tr>
			<tr><th>BIC / SWIFT</th><td><input type="text" name="bma_bank_bic" value="<?php echo esc_attr( get_option( 'bma_bank_bic' ) ); ?>" class="regular-text" placeholder="ABCDEFGH"></td></tr>
			<tr><th><?php esc_html_e( 'Bank neve', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="bma_bank_name" value="<?php echo esc_attr( get_option( 'bma_bank_name' ) ); ?>" class="regular-text"></td></tr>
		</table>

		<h2><?php esc_html_e( 'Pénznem', 'bma-ajanlatkeres' ); ?></h2>
		<table class="form-table">
			<tr><th><?php esc_html_e( 'Alapértelmezett ajánlat pénznem', 'bma-ajanlatkeres' ); ?></th><td>
				<select name="bma_default_currency">
					<option value="EUR" <?php selected( get_option( 'bma_default_currency', 'EUR' ), 'EUR' ); ?>>EUR</option>
					<option value="RON" <?php selected( get_option( 'bma_default_currency' ), 'RON' ); ?>>RON</option>
				</select>
			</td></tr>
		</table>

		<?php submit_button(); ?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'Demo tartalom', 'bma-ajanlatkeres' ); ?></h2>
	<p><?php esc_html_e( 'Betölti a teszteléshez szükséges szolgáltatásokat, dinamikus mezőket, referenciákat és oldaltartalmakat. A meglévő demo adatok felülírásra kerülnek.', 'bma-ajanlatkeres' ); ?></p>
	<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Biztosan újratölti a demo tartalmat?', 'bma-ajanlatkeres' ) ); ?>');">
		<?php wp_nonce_field( 'bma_reseed_demo', 'bma_reseed_nonce' ); ?>
		<?php settings_errors( 'bma_settings' ); ?>
		<button type="submit" name="bma_reseed_demo" class="button button-secondary"><?php esc_html_e( 'Demo tartalom betöltése', 'bma-ajanlatkeres' ); ?></button>
	</form>
</div>
