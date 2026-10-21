<?php
/**
 * Plugin Name:       BMA Ajánlatkérés
 * Plugin URI:        https://bma-engineering.hu
 * Description:       Ajánlatkérési rendszer, ügyfélportál, dokumentum- és státuszkezelés a BMA Engineering weboldalhoz.
 * Version:           2.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            BMA Engineering
 * Text Domain:       bma-ajanlatkeres
 * Domain Path:       /languages
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

define( 'BMA_AJ_VERSION', '2.1.0' );
define( 'BMA_AJ_PLUGIN_FILE', __FILE__ );
define( 'BMA_AJ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BMA_AJ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BMA_AJ_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once BMA_AJ_PLUGIN_DIR . 'includes/class-autoloader.php';
BMA_AJ_Autoloader::register();

require_once BMA_AJ_PLUGIN_DIR . 'includes/class-activator.php';
require_once BMA_AJ_PLUGIN_DIR . 'includes/class-deactivator.php';

register_activation_hook( __FILE__, array( 'BMA_AJ_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BMA_AJ_Deactivator', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		BMA_AJ_Plugin::instance()->init();
	}
);
