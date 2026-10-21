<?php
/**
 * Contact info shortcode – homepage contact block (§7.9).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_front_page() ) {
	return;
}

$company = BMA_AJ_Company::get( 'company_name' );
$phone   = BMA_AJ_Company::get( 'contact_phone' );
$email   = BMA_AJ_Company::get( 'contact_email' );
$addr    = BMA_AJ_Company::get( 'contact_address' );
$hours   = BMA_AJ_Company::get( 'opening_hours' );
?>
<section class="bma-section bma-contact-band" aria-labelledby="bma-contact-info-title">
	<div class="bma-container bma-contact-band-inner">
		<div class="bma-contact-band-copy">
			<p class="bma-section-eyebrow bma-section-eyebrow--light"><?php BMA_AJ_Content::e( 'home.contact.eyebrow' ); ?></p>
			<h2 id="bma-contact-info-title"><?php echo esc_html( $company ?: 'BMA Engineering' ); ?></h2>
			<?php if ( $addr ) : ?>
				<p><?php echo nl2br( esc_html( $addr ) ); ?></p>
			<?php endif; ?>
			<p>
				<?php if ( $phone ) : ?>
					<a href="<?php echo esc_url( BMA_AJ_Company::phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
				<?php endif; ?>
				<?php if ( $phone && $email ) : ?> · <?php endif; ?>
				<?php if ( $email ) : ?>
					<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
				<?php endif; ?>
			</p>
			<?php if ( $hours ) : ?>
				<p><?php echo esc_html( sprintf( /* translators: %s hours */ __( 'Nyitvatartás: %s', 'bma-ajanlatkeres' ), $hours ) ); ?></p>
			<?php endif; ?>
			<?php if ( shortcode_exists( 'bma_social_links' ) ) : ?>
				<div class="bma-contact-band-social"><?php echo do_shortcode( '[bma_social_links]' ); ?></div>
			<?php endif; ?>
		</div>
		<div class="bma-contact-band-actions">
			<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php BMA_AJ_Content::e( 'home.contact.cta_primary' ); ?></a>
			<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'kapcsolat' ) : home_url( '/kapcsolat/' ) ) ); ?>" class="bma-btn bma-btn-ghost"><?php BMA_AJ_Content::e( 'home.contact.cta_secondary' ); ?></a>
		</div>
	</div>
</section>
