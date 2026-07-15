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
	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );
} else {
	define( 'WP_DEBUG', false );
	define( 'WP_DEBUG_LOG', false );
	define( 'WP_DEBUG_DISPLAY', false );
}

// SMTP via wp_mail – configured by BMA plugin settings.
define( 'BMA_SMTP_HOST', getenv( 'SMTP_HOST' ) ?: '' );
define( 'BMA_SMTP_PORT', getenv( 'SMTP_PORT' ) ?: 587 );
define( 'BMA_SMTP_USER', getenv( 'SMTP_USER' ) ?: '' );
define( 'BMA_SMTP_PASSWORD', getenv( 'SMTP_PASSWORD' ) ?: '' );
