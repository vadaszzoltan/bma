<?php
/**
 * PSR-4 style autoloader for BMA plugin classes.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Autoloader {

	/**
	 * Register autoloader.
	 */
	public static function register(): void {
		spl_autoload_register( array( self::class, 'load' ) );
	}

	/**
	 * Load class file.
	 *
	 * @param string $class Class name.
	 */
	public static function load( string $class ): void {
		if ( strpos( $class, 'BMA_AJ_' ) !== 0 ) {
			return;
		}

		$relative = strtolower( str_replace( array( 'BMA_AJ_', '_' ), array( '', '-' ), $class ) );
		$relative = 'class-' . $relative . '.php';

		$paths = array(
			BMA_AJ_PLUGIN_DIR . 'includes/' . $relative,
			BMA_AJ_PLUGIN_DIR . 'includes/admin/' . $relative,
			BMA_AJ_PLUGIN_DIR . 'includes/frontend/' . $relative,
			BMA_AJ_PLUGIN_DIR . 'includes/database/' . $relative,
		);

		foreach ( $paths as $path ) {
			if ( file_exists( $path ) ) {
				require_once $path;
				return;
			}
		}
	}
}
