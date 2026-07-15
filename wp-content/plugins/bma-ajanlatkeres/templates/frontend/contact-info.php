<section class="bma-contact-info" aria-labelledby="bma-contact-info-title">
	<h2 id="bma-contact-info-title"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></h2>
	<p><strong><?php esc_html_e( 'BMA Engineering', 'bma-ajanlatkeres' ); ?></strong></p>
	<p><?php echo esc_html( get_option( 'bma_contact_address', '[KAPCSOLATI ADAT – Cím]' ) ); ?></p>
	<p><?php esc_html_e( 'Telefon:', 'bma-ajanlatkeres' ); ?> <?php echo esc_html( get_option( 'bma_contact_phone', '[KAPCSOLATI ADAT – Telefon]' ) ); ?></p>
	<p><?php esc_html_e( 'E-mail:', 'bma-ajanlatkeres' ); ?> <a href="mailto:<?php echo esc_attr( get_option( 'bma_contact_email', '' ) ); ?>"><?php echo esc_html( get_option( 'bma_contact_email', '[KAPCSOLATI ADAT – E-mail]' ) ); ?></a></p>
</section>
