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

<header class="bma-site-header" role="banner">
	<div class="bma-header-inner">
		<div class="bma-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bma-logo-placeholder" aria-label="<?php esc_attr_e( 'BMA Engineering főoldal', 'bma-engineering' ); ?>">[LOGÓ PLACEHOLDER]</a>
			<?php endif; ?>
		</div>
		<nav class="bma-main-nav" role="navigation" aria-label="<?php esc_attr_e( 'Fő navigáció', 'bma-engineering' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'bma_fallback_menu',
				)
			);
			?>
		</nav>
		<div class="bma-header-actions">
			<a href="<?php echo esc_url( home_url( '/ajanlatkosar/' ) ); ?>" class="bma-cart-link" aria-label="<?php esc_attr_e( 'Ajánlatkérési kosár', 'bma-engineering' ); ?>">
				<?php esc_html_e( 'Kosár', 'bma-engineering' ); ?>
				<?php if ( class_exists( 'BMA_AJ_Cart' ) ) : ?>
					<span class="bma-cart-count">(<?php echo esc_html( (string) BMA_AJ_Cart::count() ); ?>)</span>
				<?php endif; ?>
			</a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( home_url( '/ugyfelportal/' ) ); ?>"><?php esc_html_e( 'Ügyfélportál', 'bma-engineering' ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Bejelentkezés', 'bma-engineering' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</header>

<main id="main-content" class="bma-site-main" tabindex="-1">
