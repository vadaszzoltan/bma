<?php
/**
 * Extra WordPress configuration – loaded via docker volume mount.
 * In production, merge these settings into wp-config.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_POST_REVISIONS', 5 );
define( 'AUTOMATIC_UPDATER_DISABLED', false );

if ( getenv( 'WP_DEBUG' ) === '1' ) {
	if ( ! defined( 'WP_DEBUG' ) ) {
		define( 'WP_DEBUG', true );
	}
	if ( ! defined( 'WP_DEBUG_LOG' ) ) {
		define( 'WP_DEBUG_LOG', true );
	}
	if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
		define( 'WP_DEBUG_DISPLAY', false );
	}
} elseif ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
	define( 'WP_DEBUG_LOG', false );
	define( 'WP_DEBUG_DISPLAY', false );
}

// SMTP via wp_mail – configured by BMA plugin settings.
define( 'BMA_SMTP_HOST', getenv( 'SMTP_HOST' ) ?: '' );
define( 'BMA_SMTP_PORT', getenv( 'SMTP_PORT' ) ?: 587 );
define( 'BMA_SMTP_USER', getenv( 'SMTP_USER' ) ?: '' );
define( 'BMA_SMTP_PASSWORD', getenv( 'SMTP_PASSWORD' ) ?: '' );

// Stripe – prefer env vars in Docker/production; WP admin options are fallback.
define( 'BMA_STRIPE_PUBLISHABLE_KEY', getenv( 'BMA_STRIPE_PUBLISHABLE_KEY' ) ?: '' );
define( 'BMA_STRIPE_SECRET_KEY', getenv( 'BMA_STRIPE_SECRET_KEY' ) ?: '' );
define( 'BMA_STRIPE_WEBHOOK_SECRET', getenv( 'BMA_STRIPE_WEBHOOK_SECRET' ) ?: '' );
