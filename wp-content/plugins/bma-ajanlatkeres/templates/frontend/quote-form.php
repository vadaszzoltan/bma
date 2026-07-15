<?php
defined( 'ABSPATH' ) || exit;
$submitted = isset( $_GET['submitted'] ) && isset( $_GET['number'] );
$items     = BMA_AJ_Cart::get_items();

if ( $submitted ) :
	?>
	<section class="bma-section bma-confirmation" role="status" aria-live="polite">
		<div class="bma-container">
			<h1><?php esc_html_e( 'Köszönjük ajánlatkérését!', 'bma-ajanlatkeres' ); ?></h1>
			<p><?php printf( esc_html__( 'Azonosító: %s', 'bma-ajanlatkeres' ), esc_html( sanitize_text_field( wp_unslash( $_GET['number'] ) ) ) ); ?></p>
			<p><?php esc_html_e( 'Visszaigazoló e-mailt küldtünk a megadott címre.', 'bma-ajanlatkeres' ); ?></p>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( home_url( '/ugyfelportal/' ) ); ?>" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></a>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return;
endif;

if ( empty( $items ) ) :
	?>
	<section class="bma-section">
		<div class="bma-container">
			<p><?php esc_html_e( 'Az ajánlatkérési kosár üres.', 'bma-ajanlatkeres' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</section>
	<?php
	return;
endif;
?>
<section class="bma-section bma-quote-submit" aria-labelledby="bma-submit-title">
	<div class="bma-container">
		<h1 id="bma-submit-title"><?php esc_html_e( 'Ajánlatkérés beküldése', 'bma-ajanlatkeres' ); ?></h1>

		<form id="bma-quote-submit-form" class="bma-form" novalidate>
			<fieldset>
				<legend><?php esc_html_e( 'Kapcsolattartói adatok', 'bma-ajanlatkeres' ); ?></legend>
				<div class="bma-form-field">
					<label for="bma_name"><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
					<input type="text" id="bma_name" name="name" required value="<?php echo esc_attr( is_user_logged_in() ? wp_get_current_user()->display_name : '' ); ?>">
				</div>
				<div class="bma-form-field">
					<label for="bma_email"><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
					<input type="email" id="bma_email" name="email" required value="<?php echo esc_attr( is_user_logged_in() ? wp_get_current_user()->user_email : '' ); ?>">
				</div>
				<div class="bma-form-field">
					<label for="bma_phone"><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
					<input type="tel" id="bma_phone" name="phone" required>
				</div>
				<div class="bma-form-field">
					<label for="bma_company"><?php esc_html_e( 'Cég neve', 'bma-ajanlatkeres' ); ?></label>
					<input type="text" id="bma_company" name="company">
				</div>
				<div class="bma-form-field">
					<label for="bma_tax"><?php esc_html_e( 'Adószám', 'bma-ajanlatkeres' ); ?></label>
					<input type="text" id="bma_tax" name="tax_number">
				</div>
				<div class="bma-form-field">
					<label for="bma_billing"><?php esc_html_e( 'Számlázási cím', 'bma-ajanlatkeres' ); ?></label>
					<textarea id="bma_billing" name="billing_address" rows="2"></textarea>
				</div>
				<div class="bma-form-field">
					<label for="bma_note"><?php esc_html_e( 'Megjegyzés', 'bma-ajanlatkeres' ); ?></label>
					<textarea id="bma_note" name="note" rows="3"></textarea>
				</div>
			</fieldset>

			<?php if ( ! is_user_logged_in() ) : ?>
				<div class="bma-form-field">
					<label class="bma-checkbox-label">
						<input type="checkbox" name="create_account" value="1" id="bma_create_account">
						<?php esc_html_e( 'Szeretnék ügyfélfiókot létrehozni az ajánlatkérések követéséhez.', 'bma-ajanlatkeres' ); ?>
					</label>
				</div>
				<p class="bma-login-hint"><?php printf( esc_html__( 'Már van fiókja? %s', 'bma-ajanlatkeres' ), '<a href="' . esc_url( wp_login_url( home_url( '/ajanlatkeres/' ) ) ) . '">' . esc_html__( 'Bejelentkezés', 'bma-ajanlatkeres' ) . '</a>' ); ?></p>
			<?php endif; ?>

			<div class="bma-form-field">
				<label class="bma-checkbox-label">
					<input type="checkbox" name="privacy" value="1" required>
					<?php esc_html_e( 'Elfogadom az adatvédelmi tájékoztatót.', 'bma-ajanlatkeres' ); ?> <span class="required">*</span>
				</label>
			</div>

			<div id="bma-form-errors" class="bma-form-errors" role="alert" hidden></div>

			<button type="submit" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Ajánlatkérés beküldése', 'bma-ajanlatkeres' ); ?></button>
		</form>
	</div>
</section>
