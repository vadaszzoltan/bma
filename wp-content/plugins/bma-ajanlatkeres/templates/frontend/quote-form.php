<?php
defined( 'ABSPATH' ) || exit;
$submitted = isset( $_GET['submitted'] ) && isset( $_GET['number'] );
$items     = BMA_AJ_Cart::get_items();
$fields    = BMA_AJ_Cart::get_merged_fields();

if ( $submitted ) :
	?>
	<section class="bma-section bma-confirmation" role="status" aria-live="polite">
		<div class="bma-container bma-container-narrow">
			<div class="bma-success-card">
				<p class="bma-section-eyebrow"><?php esc_html_e( 'Sikeres beküldés', 'bma-ajanlatkeres' ); ?></p>
				<h1><?php esc_html_e( 'Köszönjük ajánlatkérését!', 'bma-ajanlatkeres' ); ?></h1>
				<p class="bma-request-number"><?php printf( esc_html__( 'Azonosító: %s', 'bma-ajanlatkeres' ), esc_html( sanitize_text_field( wp_unslash( $_GET['number'] ) ) ) ); ?></p>
				<p><?php esc_html_e( 'Visszaigazoló e-mailt küldtünk a megadott címre. Munkatársunk hamarosan feldolgozza kérését.', 'bma-ajanlatkeres' ); ?></p>
				<div class="bma-form-actions">
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' ) ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></a>
					<?php endif; ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bma-btn"><?php esc_html_e( 'Vissza a főoldalra', 'bma-ajanlatkeres' ); ?></a>
				</div>
			</div>
		</div>
	</section>
	<?php
	return;
endif;

if ( empty( $items ) ) :
	?>
	<section class="bma-section">
		<div class="bma-container">
			<p class="bma-section-eyebrow"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
			<h1><?php esc_html_e( 'A kosár üres', 'bma-ajanlatkeres' ); ?></h1>
			<p class="bma-section-intro"><?php esc_html_e( 'Az ajánlatkéréshez először válasszon szolgáltatást.', 'bma-ajanlatkeres' ); ?></p>
			<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</section>
	<?php
	return;
endif;
?>
<section class="bma-section bma-quote-submit" aria-labelledby="bma-submit-title">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
		<h1 id="bma-submit-title"><?php esc_html_e( 'Adatok megadása', 'bma-ajanlatkeres' ); ?></h1>

		<nav class="bma-flow-steps" aria-label="<?php esc_attr_e( 'Ajánlatkérési lépések', 'bma-ajanlatkeres' ); ?>">
			<span class="bma-flow-step is-done"><?php esc_html_e( '1. Szolgáltatások', 'bma-ajanlatkeres' ); ?></span>
			<span class="bma-flow-step is-done"><?php esc_html_e( '2. Kosár', 'bma-ajanlatkeres' ); ?></span>
			<span class="bma-flow-step is-current"><?php esc_html_e( '3. Adatok', 'bma-ajanlatkeres' ); ?></span>
		</nav>

		<p class="bma-section-intro"><?php esc_html_e( 'A kiválasztott szolgáltatásokhoz szükséges adatokat egyszer, itt adhatja meg. Ismételt kitöltés nem szükséges.', 'bma-ajanlatkeres' ); ?></p>

		<div class="bma-checkout-layout">
			<aside class="bma-checkout-services">
				<p class="bma-tile-eyebrow"><?php esc_html_e( 'Összegzés', 'bma-ajanlatkeres' ); ?></p>
				<h2><?php esc_html_e( 'Kiválasztott szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
				<ul class="bma-checkout-service-list">
					<?php foreach ( $items as $item ) : ?>
						<li>
							<strong><?php echo esc_html( BMA_AJ_Cart::service_title_for_display( (int) ( $item['service_id'] ?? 0 ) ) ?: (string) ( $item['service_name'] ?? '' ) ); ?></strong>
							<?php if ( ! empty( $item['selected_items'] ) ) : ?>
								<ul>
									<?php foreach ( $item['selected_items'] as $item_id ) : ?>
										<?php
										$name = BMA_AJ_Cart::item_name_for_display( (int) $item_id );
										if ( $name ) :
											?>
											<li><?php echo esc_html( $name ); ?></li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<p><a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ajanlatkosar' ) : home_url( '/ajanlatkosar/' ) ) ); ?>" class="bma-link"><?php esc_html_e( 'Kosár módosítása', 'bma-ajanlatkeres' ); ?></a></p>
			</aside>

			<form id="bma-quote-submit-form" class="bma-form bma-checkout-form" enctype="multipart/form-data" novalidate>
				<?php if ( $fields ) : ?>
					<fieldset>
						<legend><?php esc_html_e( 'Projekt adatok', 'bma-ajanlatkeres' ); ?></legend>
						<div id="bma-checkout-fields">
							<?php foreach ( $fields as $field ) : ?>
								<?php echo BMA_AJ_Dynamic_Fields::render_field( $field ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endforeach; ?>
						</div>
					</fieldset>
				<?php endif; ?>

				<fieldset>
					<legend><?php esc_html_e( 'Kapcsolattartói adatok', 'bma-ajanlatkeres' ); ?></legend>
					<div class="bma-form-row">
						<div class="bma-form-field">
							<label for="bma_name"><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input type="text" id="bma_name" name="name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Teljes név', 'bma-ajanlatkeres' ); ?>" value="<?php echo esc_attr( is_user_logged_in() ? wp_get_current_user()->display_name : '' ); ?>">
						</div>
						<div class="bma-form-field">
							<label for="bma_email"><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
							<input type="email" id="bma_email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'nev@ceg.ro', 'bma-ajanlatkeres' ); ?>" value="<?php echo esc_attr( is_user_logged_in() ? wp_get_current_user()->user_email : '' ); ?>">
						</div>
					</div>
					<div class="bma-form-field">
						<label for="bma_phone"><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
						<input type="tel" id="bma_phone" name="phone" required autocomplete="tel" placeholder="<?php esc_attr_e( '+40 7XX XXX XXX', 'bma-ajanlatkeres' ); ?>">
					</div>
					<div class="bma-form-row">
						<div class="bma-form-field">
							<label for="bma_company"><?php esc_html_e( 'Cég neve', 'bma-ajanlatkeres' ); ?></label>
							<input type="text" id="bma_company" name="company" autocomplete="organization" placeholder="<?php esc_attr_e( 'Cégnév, ha cégként kér ajánlatot', 'bma-ajanlatkeres' ); ?>">
						</div>
						<div class="bma-form-field">
							<label for="bma_tax"><?php esc_html_e( 'Adószám', 'bma-ajanlatkeres' ); ?></label>
							<input type="text" id="bma_tax" name="tax_number" placeholder="<?php esc_attr_e( 'CUI / adószám', 'bma-ajanlatkeres' ); ?>">
						</div>
					</div>
					<div class="bma-form-field">
						<label for="bma_billing"><?php esc_html_e( 'Számlázási cím', 'bma-ajanlatkeres' ); ?></label>
						<textarea id="bma_billing" name="billing_address" rows="2" placeholder="<?php esc_attr_e( 'Irányítószám, város, utca, házszám', 'bma-ajanlatkeres' ); ?>"></textarea>
					</div>
					<div class="bma-form-field">
						<label for="bma_note"><?php esc_html_e( 'Megjegyzés', 'bma-ajanlatkeres' ); ?></label>
						<textarea id="bma_note" name="note" rows="3" placeholder="<?php esc_attr_e( 'Egyéb fontos információ a projektről…', 'bma-ajanlatkeres' ); ?>"></textarea>
					</div>
				</fieldset>

				<?php if ( ! is_user_logged_in() ) : ?>
					<div class="bma-form-field">
						<label class="bma-checkbox-label">
							<input type="checkbox" name="create_account" value="1" id="bma_create_account">
							<?php esc_html_e( 'Szeretnék ügyfélfiókot létrehozni az ajánlatkérések követéséhez.', 'bma-ajanlatkeres' ); ?>
						</label>
					</div>
				<?php endif; ?>

				<div class="bma-form-field">
					<label class="bma-checkbox-label">
						<input type="checkbox" name="privacy" value="1" required>
						<?php
						$privacy_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'adatvedelem' ) : home_url( '/adatvedelem/' ) );
						echo wp_kses(
							sprintf(
								/* translators: %s: privacy policy URL */
								__( 'Elolvastam és elfogadom az <a href="%s" target="_blank" rel="noopener noreferrer">adatvédelmi tájékoztatóban</a> foglaltakat.', 'bma-ajanlatkeres' ),
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
					</label>
				</div>

				<div id="bma-form-errors" class="bma-form-errors" role="alert" hidden></div>

				<button type="submit" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Ajánlatkérés beküldése', 'bma-ajanlatkeres' ); ?></button>
			</form>
		</div>
	</div>
</section>
