<form class="bma-form bma-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<?php wp_nonce_field( 'bma_contact_form', 'bma_contact_nonce' ); ?>
	<input type="hidden" name="action" value="bma_contact_submit">
	<div class="bma-form-field">
		<label for="contact_name"><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
		<input type="text" id="contact_name" name="name" required>
	</div>
	<div class="bma-form-field">
		<label for="contact_email"><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
		<input type="email" id="contact_email" name="email" required>
	</div>
	<div class="bma-form-field">
		<label for="contact_message"><?php esc_html_e( 'Üzenet', 'bma-ajanlatkeres' ); ?> <span class="required">*</span></label>
		<textarea id="contact_message" name="message" rows="5" required></textarea>
	</div>
	<div class="bma-form-field">
		<label class="bma-checkbox-label">
			<input type="checkbox" name="privacy" value="1" required>
			<?php esc_html_e( 'Elfogadom az adatvédelmi tájékoztatót.', 'bma-ajanlatkeres' ); ?>
		</label>
	</div>
	<button type="submit" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Üzenet küldése', 'bma-ajanlatkeres' ); ?></button>
</form>
