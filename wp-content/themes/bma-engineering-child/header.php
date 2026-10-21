<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="bma-skip-link" href="#main-content"><?php esc_html_e( 'Ugrás a tartalomhoz', 'bma-engineering' ); ?></a>

<?php
$phone = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'contact_phone' ) : (string) get_option( 'bma_contact_phone', '' );
$email = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'contact_email' ) : (string) get_option( 'bma_contact_email', '' );
$hours = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'opening_hours' ) : (string) get_option( 'bma_opening_hours', '' );
if ( class_exists( 'BMA_AJ_I18n' ) ) {
	$hours_tr = BMA_AJ_I18n::overlay( 'options', 'bma_opening_hours' );
	if ( '' !== $hours_tr ) {
		$hours = $hours_tr;
	}
}
?>

<div class="topbar bma-topbar">
	<div class="topbar-inner bma-topbar-inner">
		<div class="bma-topbar-contacts">
			<?php if ( $phone ) : ?>
				<a class="bma-topbar-item" href="<?php echo esc_url( class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::phone_href( $phone ) : 'tel:' . preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<a class="bma-topbar-item" href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
			<?php endif; ?>
			<?php if ( $hours ) : ?>
				<span class="bma-topbar-item"><?php echo esc_html( $hours ); ?></span>
			<?php endif; ?>
		</div>
		<div class="bma-topbar-social">
			<?php if ( shortcode_exists( 'bma_social_links' ) ) : ?>
				<?php echo do_shortcode( '[bma_social_links]' ); ?>
			<?php endif; ?>
		</div>
		<?php bma_render_langswitch(); ?>
	</div>
</div>

<header class="siteheader bma-site-header" role="banner">
	<div class="header-inner bma-header-inner" id="bma-header-inner">
		<button
			type="button"
			class="burger"
			id="bma-burger"
			aria-label="<?php esc_attr_e( 'Menü megnyitása', 'bma-engineering' ); ?>"
			aria-expanded="false"
			aria-controls="bma-mobile-nav"
		>
			<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#0e2356" stroke-width="1.9" stroke-linecap="round" aria-hidden="true">
				<path d="M4 7h16M4 12h16M4 17h16"></path>
			</svg>
		</button>
		<div class="logo bma-logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bma-logo-block" aria-label="<?php esc_attr_e( 'BMA Engineering főoldal', 'bma-engineering' ); ?>">
				<span class="bma-logo-placeholder-box" aria-hidden="true">[LOGÓ]</span>
			</a>
		</div>

		<nav id="bma-primary-nav" class="nav bma-main-nav" role="navigation" aria-label="<?php esc_attr_e( 'Fő navigáció', 'bma-engineering' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'bma-menu',
					'fallback_cb'    => 'bma_fallback_menu',
				)
			);
			?>
		</nav>

		<div class="header-actions bma-header-actions">
			<?php
			if ( function_exists( 'bma_page_url' ) ) {
				$quote_url  = bma_page_url( 'ajanlatkeres' );
				$portal_url = bma_page_url( 'ugyfelportal' );
				$login_url  = bma_page_url( 'belepes' );
			} elseif ( class_exists( 'BMA_AJ_I18n' ) ) {
				$quote_url  = BMA_AJ_I18n::page_url( 'ajanlatkeres' );
				$portal_url = BMA_AJ_I18n::page_url( 'ugyfelportal' );
				$login_url  = BMA_AJ_I18n::page_url( 'belepes' );
			} else {
				$quote_url  = home_url( '/ajanlatkeres/' );
				$portal_url = home_url( '/ugyfelportal/' );
				$login_url  = home_url( '/belepes/' );
			}
			?>
			<a href="<?php echo esc_url( $quote_url ); ?>" class="cart-btn bma-header-cta">
				<span class="bma-header-cta-label"><?php esc_html_e( 'Ajánlatkérés', 'bma-engineering' ); ?></span>
			</a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( $portal_url ); ?>" class="bma-header-login"><?php esc_html_e( 'Ügyfélportál', 'bma-engineering' ); ?></a>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="bma-header-logout"><?php esc_html_e( 'Kijelentkezés', 'bma-engineering' ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( $login_url ?: wp_login_url( $portal_url ) ); ?>" class="btn btn--outline btn--sm bma-header-login"><?php esc_html_e( 'Belépés', 'bma-engineering' ); ?></a>
			<?php endif; ?>
		</div>
	</div>

	<div
		id="bma-mobile-nav"
		class="bma-mobile-nav"
		hidden
		aria-hidden="true"
	>
		<div class="bma-mobile-nav-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobil menü', 'bma-engineering' ); ?>">
			<nav class="bma-mobile-nav-menu" aria-label="<?php esc_attr_e( 'Fő navigáció', 'bma-engineering' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'bma-menu bma-mobile-menu',
						'fallback_cb'    => 'bma_fallback_menu',
					)
				);
				?>
			</nav>
			<div class="bma-mobile-nav-actions">
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( $portal_url ); ?>" class="bma-mobile-nav-link"><?php esc_html_e( 'Ügyfélportál', 'bma-engineering' ); ?></a>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="bma-mobile-nav-link"><?php esc_html_e( 'Kijelentkezés', 'bma-engineering' ); ?></a>
				<?php else : ?>
					<a href="<?php echo esc_url( $login_url ?: wp_login_url( $portal_url ) ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Belépés', 'bma-engineering' ); ?></a>
				<?php endif; ?>
			</div>
			<div class="bma-mobile-nav-lang">
				<?php bma_render_langswitch(); ?>
			</div>
		</div>
	</div>
</header>

<main id="main-content" class="bma-site-main" tabindex="-1">
