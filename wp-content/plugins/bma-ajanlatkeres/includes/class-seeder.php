<?php
/**
 * Demo content seeder on activation.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Seeder {

	/**
	 * Run seeder.
	 */
	public static function run(): void {
		if ( get_option( 'bma_aj_seeded' ) ) {
			return;
		}

		self::seed_services();
		self::seed_references();
		self::seed_fields();
		self::seed_settings();
		self::seed_page_content();

		update_option( 'bma_aj_seeded', 1 );
	}

	/**
	 * Seed services with items.
	 */
	private static function seed_services(): void {
		global $wpdb;

		$services = array(
			array(
				'title'   => 'Tervezés',
				'slug'    => 'tervezes',
				'excerpt' => 'Komplex műszaki tervezési szolgáltatások lakossági és üzleti projektekhez.',
				'content' => 'A BMA Engineering tervezési csomagjai a projekt igényeihez igazodnak. A tervezés során figyelembe vesszük a hatályos jogszabályokat, a helyszíni adottságokat és az ügyfél elvárásait.',
				'items'   => array(
					array( 'name' => 'Vízbekötési terv', 'slug' => 'vizbekotesi-terv', 'desc' => 'Ivóvíz hálózatra csatlakozás tervezése.' ),
					array( 'name' => 'Gázbekötési terv', 'slug' => 'gazbekotesi-terv', 'desc' => 'Gázellátás biztonságos csatlakoztatása.' ),
					array( 'name' => 'Szennyvízbekötési terv', 'slug' => 'szennyvizbekotesi-terv', 'desc' => 'Szennyvíz elvezetés tervezése.' ),
					array( 'name' => 'Elektromos hálózat tervezése', 'slug' => 'elektromos-halozat', 'desc' => 'Erős- és gyengeáramú hálózattervezés.' ),
				),
			),
			array(
				'title'   => 'Engedélyeztetés',
				'slug'    => 'engedelyeztetes',
				'excerpt' => 'Hatósági engedélyezési eljárások támogatása és dokumentáció összeállítása.',
				'content' => 'Segítünk az engedélyeztetéshez szükséges dokumentáció összeállításában és benyújtásában. A folyamat átlátható, lépésről lépésre követhető.',
				'items'   => array(
					array( 'name' => 'Építési engedély előkészítés', 'slug' => 'epitesi-engedely', 'desc' => 'Építési engedélyhez szükséges dokumentumok.' ),
					array( 'name' => 'Használatbavételi eljárás', 'slug' => 'hasznalatbavetel', 'desc' => 'Használatbavételi engedély támogatás.' ),
				),
			),
			array(
				'title'   => 'Műszaki dokumentáció',
				'slug'    => 'muszaki-dokumentacio',
				'excerpt' => 'Műszaki leírások, jegyzőkönyvek és átadási dokumentáció készítése.',
				'content' => 'Elkészítjük a projekt teljes műszaki dokumentációját, amely megfelel a hatósági és szerződéses követelményeknek.',
				'items'   => array(),
			),
		);

		$items_table = $wpdb->prefix . 'bma_service_items';
		$order       = 0;

		foreach ( $services as $svc ) {
			$post_id = wp_insert_post(
				array(
					'post_title'   => $svc['title'],
					'post_name'    => $svc['slug'],
					'post_excerpt' => $svc['excerpt'],
					'post_content' => $svc['content'],
					'post_status'  => 'publish',
					'post_type'    => BMA_AJ_Service_CPT::POST_TYPE,
				)
			);

			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

			update_post_meta( $post_id, '_bma_is_active', '1' );
			update_post_meta( $post_id, '_bma_sort_order', $order++ );
			update_post_meta( $post_id, '_bma_use_placeholder', '1' );
			update_post_meta( $post_id, '_bma_short_description', $svc['excerpt'] );
			update_post_meta( $post_id, '_bma_client_info', 'A beküldés után munkatársunk felveszi Önnel a kapcsolatot a részletek egyeztetéséhez.' );

			$item_order = 0;
			foreach ( $svc['items'] as $item ) {
				$wpdb->insert(
					$items_table,
					array(
						'service_id'        => $post_id,
						'name'              => $item['name'],
						'slug'              => $item['slug'],
						'short_description' => $item['desc'],
						'is_active'         => 1,
						'is_required'       => 0,
						'sort_order'        => $item_order++,
					)
				);
			}
		}
	}

	/**
	 * Seed references.
	 */
	private static function seed_references(): void {
		$refs = array(
			array(
				'title'   => 'Lakóépület bővítés – [REFERENCIA LEÍRÁSA]',
				'excerpt' => 'Családi ház bővítéséhez kapcsolódó műszaki tervezés és engedélyeztetés.',
				'type'    => 'Lakossági',
				'location'=> '[Helyszín]',
				'year'    => '[Év]',
			),
			array(
				'title'   => 'Irodaház felújítás – [REFERENCIA LEÍRÁSA]',
				'excerpt' => 'Üzleti ingatlan műszaki dokumentációja és tervezési támogatás.',
				'type'    => 'Üzleti',
				'location'=> '[Helyszín]',
				'year'    => '[Év]',
			),
		);

		foreach ( $refs as $ref ) {
			$id = wp_insert_post(
				array(
					'post_title'   => $ref['title'],
					'post_excerpt' => $ref['excerpt'],
					'post_content' => 'A projekt során a BMA Engineering teljes körű tervezési és dokumentációs támogatást biztosított. [REFERENCIA LEÍRÁSA]',
					'post_status'  => 'publish',
					'post_type'    => BMA_AJ_Reference_CPT::POST_TYPE,
				)
			);

			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_bma_project_type', $ref['type'] );
				update_post_meta( $id, '_bma_location', $ref['location'] );
				update_post_meta( $id, '_bma_year', $ref['year'] );
			}
		}
	}

	/**
	 * Seed dynamic fields.
	 */
	private static function seed_fields(): void {
		global $wpdb;
		$fields_table = $wpdb->prefix . 'bma_dynamic_fields';
		$assign_table = $wpdb->prefix . 'bma_field_assignments';

		$tervezes_posts = get_posts(
			array(
				'name'        => 'tervezes',
				'post_type'   => BMA_AJ_Service_CPT::POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => 1,
			)
		);
		$tervezes = $tervezes_posts[0] ?? null;
		if ( ! $tervezes ) {
			return;
		}

		$fields = array(
			array( 'key' => 'alapterulet', 'type' => 'number', 'admin' => 'Alapterület (m²)', 'client' => 'Alapterület (m²)', 'required' => 1 ),
			array( 'key' => 'szintek_szama', 'type' => 'number', 'admin' => 'Szintek száma', 'client' => 'Szintek száma', 'required' => 1 ),
			array( 'key' => 'projekt_helyszin', 'type' => 'address', 'admin' => 'Projekt helyszíne', 'client' => 'Projekt helyszíne', 'required' => 1 ),
			array( 'key' => 'helyrajzi_szam', 'type' => 'text', 'admin' => 'Helyrajzi szám', 'client' => 'Helyrajzi szám', 'required' => 0 ),
			array( 'key' => 'megjegyzes', 'type' => 'textarea', 'admin' => 'Megjegyzés', 'client' => 'További megjegyzés', 'required' => 0 ),
		);

		foreach ( $fields as $i => $f ) {
			$wpdb->insert(
				$fields_table,
				array(
					'field_key'     => $f['key'],
					'field_type'    => $f['type'],
					'admin_label'   => $f['admin'],
					'client_label'  => $f['client'],
					'is_required'   => $f['required'],
					'sort_order'    => $i,
					'is_active'     => 1,
				)
			);

			$field_id = (int) $wpdb->insert_id;
			$wpdb->insert(
				$assign_table,
				array(
					'field_id'    => $field_id,
					'entity_type' => 'service',
					'entity_id'   => $tervezes->ID,
					'sort_order'  => $i,
				)
			);
		}
	}

	/**
	 * Seed default settings.
	 */
	private static function seed_settings(): void {
		update_option( 'bma_admin_email', get_option( 'admin_email' ) );
		update_option( 'bma_contact_phone', '[KAPCSOLATI ADAT – Telefon]' );
		update_option( 'bma_contact_email', '[KAPCSOLATI ADAT – E-mail]' );
		update_option( 'bma_contact_address', '[KAPCSOLATI ADAT – Cím]' );
	}

	/**
	 * Add shortcodes to pages.
	 */
	private static function seed_page_content(): void {
		$page_content = array(
			'fooldal'        => "[bma_home_hero]\n[bma_home_services]\n[bma_home_process]\n[bma_home_references]\n[bma_contact_info]",
			'szolgaltatasok' => '[bma_services_list]',
			'referenciak'    => '[bma_references_list]',
			'kapcsolat'      => "[bma_contact_info]\n[bma_contact_form]\n[bma_social_links]\n\n[TÉRKÉP PLACEHOLDER]",
			'ajanlatkosar'   => '[bma_quote_cart]',
			'ajanlatkeres'   => '[bma_quote_form]',
			'ugyfelportal'   => '[bma_client_portal]',
			'adatvedelem'    => '[ADATVÉDELMI TÁJÉKOZTATÓ SZÖVEGE]',
			'cookie-tajekoztato' => '[COOKIE TÁJÉKOZTATÓ SZÖVEGE]',
		);

		foreach ( $page_content as $slug => $content ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				wp_update_post(
					array(
						'ID'           => $page->ID,
						'post_content' => $content,
					)
				);
			}
		}
	}
}
