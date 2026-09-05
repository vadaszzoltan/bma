<?php
/**
 * Plugin activation.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Activator {

	/**
	 * Run on plugin activation.
	 */
	public static function activate(): void {
		require_once BMA_AJ_PLUGIN_DIR . 'includes/class-autoloader.php';
		BMA_AJ_Autoloader::register();

		require_once BMA_AJ_PLUGIN_DIR . 'includes/database/class-schema.php';
		require_once BMA_AJ_PLUGIN_DIR . 'includes/database/class-schema.php';
		BMA_AJ_Schema::maybe_upgrade();

		require_once BMA_AJ_PLUGIN_DIR . 'includes/class-roles.php';
		require_once BMA_AJ_PLUGIN_DIR . 'includes/class-statuses.php';
		require_once BMA_AJ_PLUGIN_DIR . 'includes/class-email-templates.php';
		require_once BMA_AJ_PLUGIN_DIR . 'includes/class-seeder.php';
		require_once BMA_AJ_PLUGIN_DIR . 'includes/class-service-cpt.php';
		require_once BMA_AJ_PLUGIN_DIR . 'includes/class-reference-cpt.php';

		BMA_AJ_Schema::create_tables();
		BMA_AJ_Roles::setup();
		BMA_AJ_Statuses::seed_defaults();
		BMA_AJ_Email_Templates::seed_defaults();

		self::create_pages();
		BMA_AJ_Seeder::run();
		self::flush_rewrites();

		update_option( 'bma_aj_version', BMA_AJ_VERSION );
	}

	/**
	 * Create required WordPress pages.
	 */
	private static function create_pages(): void {
		$pages = array(
			'fooldal'           => array( 'title' => 'Főoldal', 'template' => 'front-page.php' ),
			'szolgaltatasok'    => array( 'title' => 'Szolgáltatások', 'template' => 'page-szolgaltatasok.php' ),
			'referenciak'       => array( 'title' => 'Referenciák', 'template' => 'page-referenciak.php' ),
			'kapcsolat'         => array( 'title' => 'Kapcsolat', 'template' => 'page-kapcsolat.php' ),
			'ajanlatkeres'      => array( 'title' => 'Ajánlatkérés', 'template' => 'page-ajanlatkeres.php' ),
			'ajanlatkosar'      => array( 'title' => 'Ajánlatkérési kosár', 'template' => 'page-ajanlatkosar.php' ),
			'ugyfelportal'      => array( 'title' => 'Ügyfélportál', 'template' => 'page-ugyfelportal.php' ),
			'adatvedelem'       => array( 'title' => 'Adatvédelmi tájékoztató', 'template' => 'page-adatvedelem.php' ),
			'cookie-tajekoztato'=> array( 'title' => 'Cookie tájékoztató', 'template' => 'page-cookie.php' ),
			'aszf'              => array( 'title' => 'Általános felhasználási feltételek', 'template' => 'page-aszf.php' ),
			'belepes'           => array( 'title' => 'Bejelentkezés', 'template' => 'page-belepes.php' ),
			'fizetes'           => array( 'title' => 'Fizetés', 'template' => 'page-fizetes.php' ),
		);

		$page_ids = array();

		foreach ( $pages as $slug => $data ) {
			$existing = get_page_by_path( $slug );
			if ( $existing ) {
				$page_ids[ $slug ] = $existing->ID;
				continue;
			}

			$id = wp_insert_post(
				array(
					'post_title'   => $data['title'],
					'post_name'    => $slug,
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => '',
				)
			);

			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_wp_page_template', $data['template'] );
				$page_ids[ $slug ] = $id;
			}
		}

		if ( ! empty( $page_ids['fooldal'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $page_ids['fooldal'] );
		}

		update_option( 'bma_aj_page_ids', $page_ids );
	}

	/**
	 * Flush rewrite rules.
	 */
	private static function flush_rewrites(): void {
		BMA_AJ_Service_CPT::register();
		BMA_AJ_Reference_CPT::register();
		flush_rewrite_rules();
	}
}
