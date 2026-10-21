<?php
/**
 * Demo content seeder on activation.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Seeder {

	/** @var array<string, int> Service slug => post ID */
	private static array $service_ids = array();

	/** @var array<string, int> Item slug => item row ID */
	private static array $item_ids = array();

	/**
	 * Run seeder once on first activation.
	 */
	public static function run( bool $force = false ): void {
		if ( ! $force && get_option( 'bma_aj_seeded' ) ) {
			return;
		}

		self::$service_ids = array();
		self::$item_ids    = array();

		self::seed_services();
		self::seed_references();
		self::seed_fields();
		self::seed_settings();
		self::seed_page_content();
		BMA_AJ_Media_Seeder::run();
		BMA_AJ_Catalog::sync();
		update_option( BMA_AJ_Catalog::OPTION, BMA_AJ_Catalog::VERSION );
		self::sync_references_content();

		update_option( 'bma_aj_seeded', 1 );
	}

	/**
	 * Clear and reload all demo content (admin / dev).
	 */
	public static function reseed(): void {
		self::clear_demo_content();
		self::ensure_pages();
		self::run( true );
	}

	/**
	 * Ensure required pages exist (for upgrades).
	 */
	public static function ensure_pages(): void {
		$pages = array(
			'fizetes'  => array(
				'title'    => 'Fizetés',
				'content'  => '[bma_payment]',
				'template' => 'page-fizetes.php',
			),
			'aszf'     => array(
				'title'    => 'Általános felhasználási feltételek',
				'content'  => '[bma_terms]',
				'template' => 'page-aszf.php',
			),
			'belepes'  => array(
				'title'    => 'Bejelentkezés',
				'content'  => '[bma_login]',
				'template' => 'page-belepes.php',
			),
		);

		foreach ( $pages as $slug => $data ) {
			if ( get_page_by_path( $slug ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_title'   => $data['title'],
					'post_name'    => $slug,
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => $data['content'],
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_wp_page_template', $data['template'] );
			}
		}
	}

	/**
	 * Sync legal / contact page shortcodes after content updates (idempotent).
	 */
	public static function sync_legal_page_content(): void {
		self::ensure_pages();

		$page_content = array(
			'adatvedelem'        => '[bma_privacy_policy]',
			'cookie-tajekoztato' => '[bma_cookie_policy]',
			'aszf'               => '[bma_terms]',
			'kapcsolat'          => '[bma_contact_form]',
			'belepes'            => '[bma_login]',
			'fooldal'            => "[bma_home_hero]\n[bma_home_services]\n[bma_home_multi]\n[bma_home_benefits]\n[bma_home_process]\n[bma_home_references]\n[bma_home_about]",
		);

		foreach ( $page_content as $slug => $content ) {
			$page = get_page_by_path( $slug );
			if ( ! $page ) {
				continue;
			}
			$update = array( 'ID' => $page->ID );
			$changed = false;
			if ( trim( (string) $page->post_content ) !== $content ) {
				$update['post_content'] = $content;
				$changed = true;
			}
			// Avoid raw shortcodes in SEO meta description.
			if ( '' === trim( (string) $page->post_excerpt ) || false !== strpos( (string) $page->post_excerpt, '[' ) ) {
				$excerpts = array(
					'adatvedelem'        => 'Adatvédelmi tájékoztató – BMA Engineering',
					'cookie-tajekoztato' => 'Cookie tájékoztató – BMA Engineering',
					'aszf'               => 'Általános felhasználási feltételek – BMA Engineering',
				);
				if ( isset( $excerpts[ $slug ] ) ) {
					$update['post_excerpt'] = $excerpts[ $slug ];
					$changed                = true;
				}
			}
			if ( $changed ) {
				wp_update_post( $update );
			}
		}

		if ( class_exists( 'BMA_AJ_Company' ) ) {
			$d = BMA_AJ_Company::defaults();
			if ( ! get_option( 'bma_map_lat' ) ) {
				update_option( 'bma_map_lat', $d['map_lat'] );
			}
			if ( ! get_option( 'bma_map_lng' ) ) {
				update_option( 'bma_map_lng', $d['map_lng'] );
			}
			if ( ! get_option( 'bma_map_zoom' ) ) {
				update_option( 'bma_map_zoom', $d['map_zoom'] );
			}
		}
	}

	/**
	 * Sync choose_if / choose_summary / when_needed without full reseed.
	 */
	public static function sync_service_selection_content(): void {
		global $wpdb;

		BMA_AJ_Schema::maybe_upgrade();

		$catalog = array(
			'tervezes'             => array(
				'choose_summary' => 'Ha új épületet, átalakítást vagy közműcsatlakozást tervez, és az ezekhez szükséges műszaki tervekre van szüksége, ezt a szolgáltatást válassza.',
				'when_needed'    => "új épület vagy ingatlan közműellátásának kialakításakor\nmeglévő épület átalakításakor vagy bővítésekor\núj víz-, gáz-, szennyvíz- vagy elektromos csatlakozás kialakításakor\nmeglévő hálózat módosításakor\nha engedélyezéshez vagy kivitelezéshez műszaki terv szükséges",
				'items'          => array(
					'vizbekotesi-terv'       => 'új vízcsatlakozásra van szüksége, vagy a meglévő vízhálózatot érintő változtatást tervez.',
					'gazbekotesi-terv'       => 'új gázcsatlakozást szeretne, vagy a meglévő rendszer módosítása miatt új tervre van szükség.',
					'szennyvizbekotesi-terv' => 'új szennyvízcsatlakozást alakít ki, vagy a meglévő rendszer módosítását tervezi.',
					'elektromos-halozat'     => 'új elektromos rendszer készül, meglévő rendszert alakít át, vagy a projekt miatt a villamos hálózat tervezésére van szükség.',
				),
			),
			'engedelyeztetes'      => array(
				'choose_summary' => 'Ha építési, használatbavételi vagy más kapcsolódó hatósági eljáráshoz dokumentációra és ügyintézési támogatásra van szüksége, ezt a szolgáltatást válassza.',
				'when_needed'    => "építési engedélyhez szükséges dokumentáció előkészítésekor\nhasználatbavételi eljárás indításakor vagy lezárásakor\nönkormányzati egyeztetés vagy jóváhagyás esetén",
				'items'          => array(
					'epitesi-engedely'         => 'új építéshez, bővítéshez vagy más engedélyköteles munkához építési engedélyezési dokumentációra van szüksége.',
					'hasznalatbavetel'          => 'az építési munkák elkészültek, és az ingatlan használatbavételéhez szükséges folyamatot szeretné elindítani vagy lezárni.',
					'onkormanyzati-egyeztetes' => 'a projekt megvalósításához önkormányzati dokumentációra, egyeztetésre vagy jóváhagyásra van szükség.',
				),
			),
			'muszaki-dokumentacio' => array(
				'choose_summary' => 'Ha a projekthez hivatalos műszaki leírásra, naplózásra vagy átadási dokumentációra van szüksége, ezt a szolgáltatást válassza.',
				'when_needed'    => "műszaki leírás vagy specifikáció készítésekor\népítési napló vezetésekor vagy zárásakor\nátadás-átvételi dokumentáció összeállításakor",
				'items'          => array(
					'muszaki-leiras'       => 'a projekthez hivatalos vagy részletes műszaki leírásra van szüksége.',
					'epitesi-naplo'        => 'a kivitelezés dokumentációjának részeként építési napló kezelésére van szüksége.',
					'atadasi-jegyzokonyv'  => 'elkészült munkát, rendszert vagy projektet kell dokumentált formában átadni.',
				),
			),
			'kozmu-koordinacio'    => array(
				'choose_summary' => 'Ha a közműcsatlakozásokhoz szolgáltatói ügyintézésre és koordinációra van szüksége, ezt a szolgáltatást válassza.',
				'when_needed'    => "új közműcsatlakozás indításakor\nmeglévő csatlakozás módosításakor\nha több szolgáltató ügyintézését egy helyen szeretné követni",
				'items'          => array(
					'vizmu-csatlakozas' => 'új víz- vagy szennyvízcsatlakozásra van szüksége, vagy a meglévő csatlakozást módosítaná.',
					'gazmu-csatlakozas' => 'új gázcsatlakozást szeretne kialakítani vagy meglévő csatlakozással kapcsolatban szolgáltatói ügyintézés szükséges.',
					'aramszolgaltato'   => 'új elektromos csatlakozásra, teljesítménybővítésre vagy más hálózati változtatásra van szüksége.',
				),
			),
		);

		$table = $wpdb->prefix . 'bma_service_items';

		foreach ( $catalog as $slug => $data ) {
			$page = get_page_by_path( $slug, OBJECT, BMA_AJ_Service_CPT::POST_TYPE );
			if ( ! $page ) {
				continue;
			}
			update_post_meta( $page->ID, '_bma_choose_summary', $data['choose_summary'] );
			update_post_meta( $page->ID, '_bma_when_needed', $data['when_needed'] );

			foreach ( $data['items'] as $item_slug => $choose_if ) {
				$wpdb->update(
					$table,
					array( 'choose_if' => $choose_if ),
					array(
						'service_id' => $page->ID,
						'slug'       => $item_slug,
					)
				);
			}
		}
	}

	/**
	 * Remove seeded CPT posts and related DB rows.
	 */
	public static function clear_demo_content(): void {
		global $wpdb;

		$posts = get_posts(
			array(
				'post_type'      => array( BMA_AJ_Service_CPT::POST_TYPE, BMA_AJ_Reference_CPT::POST_TYPE ),
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'fields'         => 'ids',
			)
		);

		foreach ( $posts as $post_id ) {
			wp_delete_post( (int) $post_id, true );
		}

		$wpdb->query( "DELETE FROM {$wpdb->prefix}bma_field_assignments" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}bma_dynamic_fields" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}bma_service_item_services" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}bma_service_items" );
	}

	/**
	 * Seed services with items.
	 */
	private static function seed_services(): void {
		global $wpdb;

		$services = array(
			array(
				'title'          => 'Tervezés',
				'slug'           => 'tervezes',
				'excerpt'        => 'Közmű- és épületgépészeti tervek lakossági és üzleti projektekhez, a helyszíni adottságok figyelembevételével.',
				'content'        => '<p>A tervezési szolgáltatásunk a projekt teljes műszaki előkészítését támogatja: a közműcsatlakozásoktól az elektromos hálózattervezésig. Minden esetben a hatályos jogszabályi előírásokat és az ügyfél igényeit vesszük alapul.</p><p>A folyamat során egyeztetünk a rendelkezésre álló dokumentumokról, és jelezzük, ha további műszaki adatra van szükség. Az ajánlatkérésben megadott adatok alapján előzetesen felmérjük a projekt összetettségét.</p>',
				'choose_summary' => 'Ha új épületet, átalakítást vagy közműcsatlakozást tervez, és az ezekhez szükséges műszaki tervekre van szüksége, ezt a szolgáltatást válassza.',
				'when_needed'    => "új épület vagy ingatlan közműellátásának kialakításakor\nmeglévő épület átalakításakor vagy bővítésekor\núj víz-, gáz-, szennyvíz- vagy elektromos csatlakozás kialakításakor\nmeglévő hálózat módosításakor\nha engedélyezéshez vagy kivitelezéshez műszaki terv szükséges",
				'client_info'    => 'Kérjük, adja meg a projekt alapadatait és töltse fel a rendelkezésre álló tervdokumentumokat (ha van). A beküldés után munkatársunk 1–2 munkanapon belül felveszi Önnel a kapcsolatot.',
				'admin_note'     => 'Tervezési projekteknél mindig ellenőrizni: helyrajzi szám, alapterület, szintek száma, meglévő közművek.',
				'items'          => array(
					array( 'slug' => 'vizbekotesi-terv', 'name' => 'Vízbekötési terv', 'desc' => 'Új vízbekötés vagy a meglévő vízellátás módosításához szükséges műszaki terv.', 'choose_if' => 'új vízcsatlakozásra van szüksége, vagy a meglévő vízhálózatot érintő változtatást tervez.', 'required' => 0 ),
					array( 'slug' => 'gazbekotesi-terv', 'name' => 'Gázbekötési terv', 'desc' => 'A gázellátás kialakításához vagy módosításához szükséges műszaki tervezés.', 'choose_if' => 'új gázcsatlakozást szeretne, vagy a meglévő rendszer módosítása miatt új tervre van szükség.', 'required' => 0 ),
					array( 'slug' => 'szennyvizbekotesi-terv', 'name' => 'Szennyvízbekötési terv', 'desc' => 'A szennyvíz elvezetéséhez és a szükséges hálózati csatlakozáshoz kapcsolódó műszaki terv.', 'choose_if' => 'új szennyvízcsatlakozást alakít ki, vagy a meglévő rendszer módosítását tervezi.', 'required' => 0 ),
					array( 'slug' => 'elektromos-halozat', 'name' => 'Elektromos hálózat tervezése', 'desc' => 'Erősáramú hálózattervezés, fogyasztási igények felmérése.', 'choose_if' => 'új elektromos rendszer készül, meglévő rendszert alakít át, vagy a projekt miatt a villamos hálózat tervezésére van szükség.', 'required' => 0 ),
				),
			),
			array(
				'title'          => 'Engedélyeztetés',
				'slug'           => 'engedelyeztetes',
				'excerpt'        => 'Építési és használatbavételi engedélyek előkészítése, hatósági egyeztetések támogatása.',
				'content'        => '<p>Az engedélyeztetési szolgáltatásunk célja, hogy a projekt dokumentációja megfeleljen a hatósági elvárásoknak. Segítünk az engedélyezési dosszié összeállításában, a benyújtás előkészítésében és a hiánypótlások kezelésében.</p><p>A folyamat átlátható: minden lépésről értesítjük, és az ügyfélportálon követhető a státusz.</p>',
				'choose_summary' => 'Ha építési, használatbavételi vagy más kapcsolódó hatósági eljáráshoz dokumentációra és ügyintézési támogatásra van szüksége, ezt a szolgáltatást válassza.',
				'when_needed'    => "építési engedélyhez szükséges dokumentáció előkészítésekor\nhasználatbavételi eljárás indításakor vagy lezárásakor\nönkormányzati egyeztetés vagy jóváhagyás esetén",
				'client_info'    => 'Töltse fel a meglévő terveket és műszaki dokumentumokat. Ha még nincs kész terv, jelezze a megjegyzés mezőben – segítünk a következő lépések meghatározásában.',
				'admin_note'     => 'Engedélyeztetésnél ellenőrizni: épület funkció, település, hatósági kör.',
				'items'          => array(
					array( 'slug' => 'epitesi-engedely', 'name' => 'Építési engedély előkészítés', 'desc' => 'Építési engedélyhez szükséges dokumentáció összeállítása.', 'choose_if' => 'új építéshez, bővítéshez vagy más engedélyköteles munkához építési engedélyezési dokumentációra van szüksége.', 'required' => 0 ),
					array( 'slug' => 'hasznalatbavetel', 'name' => 'Használatbavételi eljárás', 'desc' => 'Használatbavételi engedély és kapcsolódó jegyzőkönyvek.', 'choose_if' => 'az építési munkák elkészültek, és az ingatlan használatbavételéhez szükséges folyamatot szeretné elindítani vagy lezárni.', 'required' => 0 ),
					array( 'slug' => 'onkormanyzati-egyeztetes', 'name' => 'Önkormányzati egyeztetés', 'desc' => 'Településképi és önkormányzati előírások egyeztetése.', 'choose_if' => 'a projekt megvalósításához önkormányzati dokumentációra, egyeztetésre vagy jóváhagyásra van szükség.', 'required' => 0 ),
				),
			),
			array(
				'title'          => 'Műszaki dokumentáció',
				'slug'           => 'muszaki-dokumentacio',
				'excerpt'        => 'Műszaki leírások, jegyzőkönyvek, átadási dokumentáció és projekt záró anyagok készítése.',
				'content'        => '<p>Elkészítjük a projekt teljes műszaki dokumentációját, amely megfelel a hatósági és szerződéses követelményeknek. A dokumentumok egységes struktúrában, könnyen azonosítható formában készülnek.</p><p>Igény esetén a meglévő anyagokat is rendszerezzük és kiegészítjük a hiányzó részekkel.</p>',
				'choose_summary' => 'Ha a projekthez hivatalos műszaki leírásra, naplózásra vagy átadási dokumentációra van szüksége, ezt a szolgáltatást válassza.',
				'when_needed'    => "műszaki leírás vagy specifikáció készítésekor\népítési napló vezetésekor vagy zárásakor\nátadás-átvételi dokumentáció összeállításakor",
				'client_info'    => 'Adja meg a dokumentáció típusát és a határidőt. Csatolja a rendelkezésre álló forrásdokumentumokat.',
				'admin_note'     => 'Dokumentációs projekteknél rögzíteni: cél (hatóság/beruházó/belső), formátum.',
				'items'          => array(
					array( 'slug' => 'muszaki-leiras', 'name' => 'Műszaki leírás', 'desc' => 'Építési műszaki leírás és specifikációk.', 'choose_if' => 'a projekthez hivatalos vagy részletes műszaki leírásra van szüksége.', 'required' => 0 ),
					array( 'slug' => 'epitesi-naplo', 'name' => 'Építési napló', 'desc' => 'Építési napló vezetése és zárása.', 'choose_if' => 'a kivitelezés dokumentációjának részeként építési napló kezelésére van szüksége.', 'required' => 0 ),
					array( 'slug' => 'atadasi-jegyzokonyv', 'name' => 'Átadási jegyzőkönyv', 'desc' => 'Átadás-átvételi dokumentáció összeállítása.', 'choose_if' => 'elkészült munkát, rendszert vagy projektet kell dokumentált formában átadni.', 'required' => 0 ),
				),
			),
			array(
				'title'          => 'Közmű csatlakozás koordináció',
				'slug'           => 'kozmu-koordinacio',
				'excerpt'        => 'Szolgáltatói egyeztetések, csatlakozási igények és határidők koordinálása egy helyen.',
				'content'        => '<p>A közmű csatlakozás koordinációs szolgáltatás összefogja a víz-, gáz- és áramszolgáltatói folyamatokat. Összeállítjuk a szükséges beadványokat, nyomon követjük a szolgáltatói válaszokat, és tájékoztatjuk Önt a következő teendőkről.</p>',
				'choose_summary' => 'Ha a közműcsatlakozásokhoz szolgáltatói ügyintézésre és koordinációra van szüksége, ezt a szolgáltatást válassza.',
				'when_needed'    => "új közműcsatlakozás indításakor\nmeglévő csatlakozás módosításakor\nha több szolgáltató ügyintézését egy helyen szeretné követni",
				'client_info'    => 'Jelezze, mely közművek csatlakoztatására van szükség, és adja meg a kívánt határidőt.',
				'admin_note'     => 'Koordinációnál: szolgáltatók listája, már meglévő szerződések.',
				'items'          => array(
					array( 'slug' => 'vizmu-csatlakozas', 'name' => 'Vízmű egyeztetés', 'desc' => 'Ivóvíz és szennyvíz csatlakozási igény benyújtása.', 'choose_if' => 'új víz- vagy szennyvízcsatlakozásra van szüksége, vagy a meglévő csatlakozást módosítaná.', 'required' => 0 ),
					array( 'slug' => 'gazmu-csatlakozas', 'name' => 'Gázmű egyeztetés', 'desc' => 'Gázszolgáltatói csatlakozási folyamat indítása.', 'choose_if' => 'új gázcsatlakozást szeretne kialakítani vagy meglévő csatlakozással kapcsolatban szolgáltatói ügyintézés szükséges.', 'required' => 0 ),
					array( 'slug' => 'aramszolgaltato', 'name' => 'Áramszolgáltató egyeztetés', 'desc' => 'Teljesítményigény-bejelentés és csatlakozás koordinálása.', 'choose_if' => 'új elektromos csatlakozásra, teljesítménybővítésre vagy más hálózati változtatásra van szüksége.', 'required' => 0 ),
				),
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

			self::$service_ids[ $svc['slug'] ] = (int) $post_id;

			update_post_meta( $post_id, '_bma_is_active', '1' );
			update_post_meta( $post_id, '_bma_sort_order', $order++ );
			update_post_meta( $post_id, '_bma_use_placeholder', '0' );
			update_post_meta( $post_id, '_bma_short_description', $svc['excerpt'] );
			update_post_meta( $post_id, '_bma_client_info', $svc['client_info'] );
			update_post_meta( $post_id, '_bma_admin_note', $svc['admin_note'] );
			if ( ! empty( $svc['choose_summary'] ) ) {
				update_post_meta( $post_id, '_bma_choose_summary', $svc['choose_summary'] );
			}
			if ( ! empty( $svc['when_needed'] ) ) {
				update_post_meta( $post_id, '_bma_when_needed', $svc['when_needed'] );
			}

			$item_order = 0;
			foreach ( $svc['items'] as $item ) {
				$wpdb->insert(
					$items_table,
					array(
						'service_id'        => $post_id,
						'name'              => $item['name'],
						'slug'              => $item['slug'],
						'short_description' => $item['desc'],
						'choose_if'         => $item['choose_if'] ?? '',
						'is_active'         => 1,
						'is_required'       => ! empty( $item['required'] ) ? 1 : 0,
						'sort_order'        => $item_order,
					)
				);
				$item_id = (int) $wpdb->insert_id;
				self::$item_ids[ $item['slug'] ] = $item_id;
				if ( $item_id ) {
					$wpdb->insert(
						$wpdb->prefix . 'bma_service_item_services',
						array(
							'item_id'    => $item_id,
							'service_id' => $post_id,
							'sort_order' => $item_order,
						)
					);
				}
				++$item_order;
			}
		}

		self::seed_related_services();
	}

	/**
	 * Set related service links for detail pages.
	 */
	private static function seed_related_services(): void {
		$map = self::$service_ids;
		if ( empty( $map ) ) {
			return;
		}

		$relations = array(
			'tervezes'             => array( 'engedelyeztetes', 'kozmu-koordinacio' ),
			'engedelyeztetes'      => array( 'tervezes', 'muszaki-dokumentacio' ),
			'muszaki-dokumentacio' => array( 'tervezes', 'engedelyeztetes' ),
			'kozmu-koordinacio'    => array( 'tervezes', 'engedelyeztetes' ),
		);

		foreach ( $relations as $slug => $related_slugs ) {
			if ( empty( $map[ $slug ] ) ) {
				continue;
			}
			$ids = array();
			foreach ( $related_slugs as $rel_slug ) {
				if ( ! empty( $map[ $rel_slug ] ) ) {
					$ids[] = $map[ $rel_slug ];
				}
			}
			update_post_meta( $map[ $slug ], '_bma_related_services', $ids );
		}
	}

	/**
	 * Seed references.
	 */
	private static function seed_references(): void {
		foreach ( self::reference_catalog() as $ref ) {
			$id = wp_insert_post(
				array(
					'post_title'   => $ref['title'],
					'post_name'    => $ref['slug'],
					'post_excerpt' => $ref['excerpt'],
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => BMA_AJ_Reference_CPT::POST_TYPE,
				)
			);

			if ( $id && ! is_wp_error( $id ) ) {
				self::apply_reference_meta( (int) $id, $ref );
			}
		}
	}

	/**
	 * Spec §32–35 reference catalog.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function reference_catalog(): array {
		return array(
			array(
				'slug'                => 'ipari-csarnok-kozmu-koordinacio',
				'title'               => 'Ipari csarnok – közmű koordináció',
				'excerpt'             => 'Ipari beruházás közműcsatlakozásainak egyeztetése és dokumentálása, a szükséges műszaki és szolgáltatói folyamatok összehangolásával.',
				'category'            => 'Üzleti',
				'project_type'        => 'Ipari beruházás',
				'location'            => 'Románia',
				'year'                => '2024',
				'services'            => array( 'bekotesek', 'kozmuhalozatok' ),
				'project_description' => "Egy ipari csarnok kialakításához több közműcsatlakozással kapcsolatos feladatot kellett párhuzamosan kezelni. A projekt során össze kellett hangolni a beruházás műszaki igényeit, a rendelkezésre álló dokumentációt és az egyes közműszolgáltatók által kért információkat.\n\nA feladat egyik fontos része az volt, hogy a különálló víz-, gáz- és villamosenergia-csatlakozási folyamatok ne egymástól függetlenül haladjanak, hanem a teljes beruházás igényeihez igazodjanak.",
				'task'                => "A közműcsatlakozások előkészítése során meg kellett határozni a szükséges igényeket, össze kellett gyűjteni a rendelkezésre álló műszaki információkat, és elő kellett készíteni a szolgáltatói egyeztetésekhez szükséges dokumentációkat.\n\nA különböző közművek eltérő műszaki és adminisztratív követelményei miatt fontos volt a folyamatok összehangolása és a nyitott kérdések folyamatos követése.",
				'work_done'           => "a közműigények és rendelkezésre álló műszaki adatok áttekintése\na szükséges csatlakozások meghatározása\na szolgáltatói egyeztetések előkészítése\nvíz- és szennyvízcsatlakozással kapcsolatos egyeztetések\nvillamosenergia-ellátással kapcsolatos egyeztetések\nkapcsolódó műszaki dokumentációk kezelése\na folyamat során felmerülő további adatigények koordinációja",
				'solution'            => "A különböző közműcsatlakozásokat egy közös folyamat részeként kezeltük. A rendelkezésre álló dokumentáció áttekintése után összegyűjtöttük az egyes szolgáltatók felé szükséges információkat, majd ezek alapján lehetett elindítani és követni a kapcsolódó egyeztetéseket.\n\nA közös koordináció lehetővé tette, hogy a beruházás műszaki igényei és a szolgáltatói követelmények egy helyen legyenek áttekinthetők.",
				'result'              => 'A projekt számára szükséges közműcsatlakozások műszaki és adminisztratív feladatai rendezett folyamatba kerültek. A beruházó számára átláthatóvá vált, hogy az egyes közművek esetében milyen dokumentáció áll rendelkezésre, milyen egyeztetések történtek meg, és milyen további lépések szükségesek.',
				'cta_title'           => 'Hasonló projektet tervez?',
				'cta_text'            => 'Ha ipari vagy üzleti projektjéhez közműtervezésre vagy a csatlakozási folyamatok koordinációjára van szüksége, válassza ki a szükséges szolgáltatásokat, és küldje el a projekt alapadatait.',
			),
			array(
				'slug'                => 'uj-epites-teljes-engedelyeztetesi-csomag',
				'title'               => 'Új építés – teljes engedélyeztetési csomag',
				'excerpt'             => 'Új lakóépület építési engedélyének előkészítése és a kapcsolódó hatósági folyamat támogatása.',
				'category'            => 'Lakossági',
				'project_type'        => 'Új építés',
				'location'            => 'Románia',
				'year'                => '2023',
				'services'            => array( 'epuletgepeszet', 'bekotesek' ),
				'project_description' => "Egy új lakóépület megvalósításához az építési munkák megkezdése előtt össze kellett állítani az engedélyezési folyamathoz szükséges dokumentációt.\n\nA projekt több egymásra épülő tervezési és adminisztratív feladatot tartalmazott, ezért fontos volt, hogy a szükséges dokumentumok, műszaki tervek és egyeztetések megfelelő sorrendben készüljenek el.",
				'task'                => "A rendelkezésre álló projektadatok és dokumentumok alapján meg kellett határozni az engedélyezési folyamathoz szükséges további anyagokat, majd össze kellett állítani a benyújtáshoz szükséges dokumentációt.\n\nA folyamat során kezelni kellett a kapcsolódó egyeztetéseket és az esetlegesen felmerülő további dokumentációs igényeket is.",
				'work_done'           => "a rendelkezésre álló projektanyagok áttekintése\naz engedélyezéshez szükséges dokumentumok meghatározása\nkapcsolódó műszaki dokumentációk előkészítése\naz engedélyezési dokumentáció összeállítása\na benyújtás előkészítése\nkapcsolódó egyeztetések támogatása\naz eljárás során felmerülő további dokumentációs igények kezelése",
				'solution'            => "A projekt tervezési és engedélyezési feladatait egymásra épülő folyamatként kezeltük. Első lépésként áttekintettük a rendelkezésre álló anyagokat és meghatároztuk, milyen dokumentumok szükségesek az engedélyezési csomag összeállításához.\n\nAz elkészült dokumentációk összegyűjtése és ellenőrzése után az anyag előkészíthetővé vált a kapcsolódó engedélyezési eljáráshoz.",
				'result'              => 'Az új lakóépülethez szükséges engedélyezési dokumentáció rendezett formában került összeállításra, és a projekt tovább tudott lépni az engedélyezési folyamat következő szakaszába.',
				'cta_title'           => 'Új építést tervez?',
				'cta_text'            => 'Ha új épülethez tervezésre, engedélyeztetésre vagy kapcsolódó műszaki dokumentációra van szüksége, állítsa össze a szükséges szolgáltatásokat, és kérjen ajánlatot.',
			),
			array(
				'slug'                => 'irodahaz-felujitas-muszaki-dokumentacio',
				'title'               => 'Irodaház felújítás – műszaki dokumentáció',
				'excerpt'             => 'Üzleti ingatlan felújításához kapcsolódó műszaki leírások és átadási dokumentáció összeállítása.',
				'category'            => 'Üzleti',
				'project_type'        => 'Felújítás',
				'location'            => 'Románia',
				'year'                => '2024',
				'services'            => array( 'epuletgepeszet', 'tuzvedelem' ),
				'project_description' => "Egy meglévő irodaház felújításához a kivitelezési munkákhoz és a projekt lezárásához kapcsolódó műszaki dokumentációkat kellett egységes rendszerbe szervezni.\n\nA felújítás több különböző munkaterületet érintett, ezért fontos volt, hogy az elkészült műszaki anyagok követhető és egyértelmű formában dokumentálják az elvégzett munkákat.",
				'task'                => "A rendelkezésre álló műszaki információkat és dokumentumokat át kellett tekinteni, azonosítani kellett a hiányzó vagy kiegészítendő részeket, majd össze kellett állítani a szükséges műszaki és átadási dokumentációt.\n\nA cél egy olyan rendezett dokumentációs csomag kialakítása volt, amely a projekt lezárásakor is egyértelműen használható.",
				'work_done'           => "a rendelkezésre álló dokumentáció áttekintése\na szükséges műszaki anyagok meghatározása\nműszaki leírások összeállítása\na projekt során keletkezett dokumentumok rendszerezése\nátadási dokumentáció előkészítése\nátadás-átvételhez kapcsolódó dokumentumok összeállítása",
				'solution'            => "A meglévő és a projekt során létrejött dokumentumokat egységes struktúrába rendeztük. Az áttekintés során azonosítottuk azokat a részeket, amelyekhez további információra vagy kiegészítésre volt szükség.\n\nA dokumentáció kialakításakor arra törekedtünk, hogy az egyes anyagok egyértelműen kapcsolódjanak a projekt megfelelő műszaki feladataihoz és az átadási folyamathoz.",
				'result'              => 'A felújításhoz kapcsolódó műszaki dokumentáció rendezett, áttekinthető formában állt rendelkezésre, megkönnyítve a projekt lezárását és az elkészült munkák dokumentált átadását.',
				'cta_title'           => 'Felújításhoz van szüksége műszaki dokumentációra?',
				'cta_text'            => 'Ha meglévő épület felújításához, kivitelezéséhez vagy átadásához szükséges műszaki dokumentumokat kell elkészíteni vagy rendszerezni, küldje el a projekt alapadatait.',
			),
			array(
				'slug'                => 'csaladi-haz-bovites-kozmutervek',
				'title'               => 'Családi ház bővítés – közműtervek',
				'excerpt'             => 'Lakóingatlan bővítéséhez kapcsolódó víz-, gáz- és szennyvíztervezés, engedélyeztetési támogatással.',
				'category'            => 'Lakossági',
				'project_type'        => 'Bővítés',
				'location'            => 'Románia',
				'year'                => '2023',
				'services'            => array( 'epuletgepeszet', 'bekotesek' ),
				'project_description' => "Egy meglévő családi ház bővítése miatt a korábbi közműrendszereket és az ingatlan megváltozott igényeit is figyelembe kellett venni.\n\nA bővítés érintette a víz-, gáz- és szennyvízhálózat kialakítását, ezért a kapcsolódó terveket egymással és az épület módosított műszaki kialakításával összhangban kellett elkészíteni.",
				'task'                => "Fel kellett mérni, hogyan változnak az ingatlan közműigényei a bővítés következtében, és ezek alapján el kellett készíteni a szükséges közműterveket.\n\nA tervezési feladat mellett figyelembe kellett venni a kapcsolódó engedélyezési követelményeket és a rendelkezésre álló meglévő dokumentációt is.",
				'work_done'           => "a meglévő műszaki dokumentáció áttekintése\na bővítéshez kapcsolódó közműigények meghatározása\nvízbekötési és vízhálózati tervezés\ngázellátáshoz kapcsolódó tervezés\nszennyvízelvezetés tervezése\nkapcsolódó dokumentációk összeállítása\nengedélyezési folyamat támogatása",
				'solution'            => "A tervezés során a meglévő épület adottságaiból és a bővítés után várható igényekből indultunk ki.\n\nA különböző közműterveket egymással összehangolva készítettük elő annak érdekében, hogy azok a bővített épület műszaki kialakításához illeszkedjenek, és felhasználhatók legyenek a projekt következő szakaszaiban.",
				'result'              => 'A bővítéshez szükséges közműtervek és kapcsolódó dokumentációk egységesen rendelkezésre álltak, így a projekt tovább tudott lépni az engedélyezési és megvalósítási feladatokra.',
				'cta_title'           => 'Bővítést vagy átalakítást tervez?',
				'cta_text'            => 'Ha meglévő ingatlan bővítéséhez új közműtervekre vagy kapcsolódó engedélyeztetési támogatásra van szüksége, válassza ki a projektjéhez szükséges feladatokat.',
			),
		);
	}

	/**
	 * Apply structured reference meta (+ taxonomy + related services).
	 *
	 * @param int                  $post_id Post ID.
	 * @param array<string, mixed> $ref     Catalog row.
	 */
	public static function apply_reference_meta( int $post_id, array $ref ): void {
		update_post_meta( $post_id, '_bma_category', $ref['category'] );
		update_post_meta( $post_id, '_bma_project_type', $ref['project_type'] );
		update_post_meta( $post_id, '_bma_location', $ref['location'] );
		update_post_meta( $post_id, '_bma_year', $ref['year'] );
		update_post_meta( $post_id, '_bma_project_description', $ref['project_description'] );
		update_post_meta( $post_id, '_bma_task', $ref['task'] );
		update_post_meta( $post_id, '_bma_work_done', $ref['work_done'] );
		update_post_meta( $post_id, '_bma_solution', $ref['solution'] );
		update_post_meta( $post_id, '_bma_result', $ref['result'] );
		update_post_meta( $post_id, '_bma_cta_title', $ref['cta_title'] );
		update_post_meta( $post_id, '_bma_cta_text', $ref['cta_text'] );

		$term = term_exists( $ref['category'], BMA_AJ_Reference_CPT::TAXONOMY );
		if ( ! $term ) {
			$term = wp_insert_term( $ref['category'], BMA_AJ_Reference_CPT::TAXONOMY );
		}
		if ( ! is_wp_error( $term ) ) {
			$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
			wp_set_object_terms( $post_id, array( $term_id ), BMA_AJ_Reference_CPT::TAXONOMY, false );
		}

		$service_ids = array();
		foreach ( $ref['services'] as $slug ) {
			$svc = get_page_by_path( $slug, OBJECT, BMA_AJ_Service_CPT::POST_TYPE );
			if ( $svc ) {
				$service_ids[] = (int) $svc->ID;
			} elseif ( ! empty( self::$service_ids[ $slug ] ) ) {
				$service_ids[] = (int) self::$service_ids[ $slug ];
			}
		}
		update_post_meta( $post_id, '_bma_related_services', $service_ids );

		$thumb = (int) get_post_thumbnail_id( $post_id );
		if ( $thumb ) {
			update_post_meta( $post_id, '_bma_gallery', array( $thumb ) );
		}
	}

	/**
	 * Sync reference structured content without wiping other data.
	 */
	public static function sync_references_content(): void {
		foreach ( self::reference_catalog() as $ref ) {
			$post = get_page_by_path( $ref['slug'], OBJECT, BMA_AJ_Reference_CPT::POST_TYPE );
			if ( ! $post ) {
				$all = get_posts(
					array(
						'post_type'      => BMA_AJ_Reference_CPT::POST_TYPE,
						'posts_per_page' => -1,
						'post_status'    => 'any',
					)
				);
				foreach ( $all as $candidate ) {
					if ( $candidate->post_title === $ref['title'] ) {
						$post = $candidate;
						break;
					}
				}
			}
			if ( ! $post ) {
				$id = wp_insert_post(
					array(
						'post_title'   => $ref['title'],
						'post_name'    => $ref['slug'],
						'post_excerpt' => $ref['excerpt'],
						'post_content' => '',
						'post_status'  => 'publish',
						'post_type'    => BMA_AJ_Reference_CPT::POST_TYPE,
					)
				);
				if ( $id && ! is_wp_error( $id ) ) {
					self::apply_reference_meta( (int) $id, $ref );
				}
				continue;
			}

			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_name'    => $ref['slug'],
					'post_excerpt' => $ref['excerpt'],
					'post_content' => '',
				)
			);
			self::apply_reference_meta( (int) $post->ID, $ref );
		}
	}

	/**
	 * Seed dynamic fields and assignments.
	 */
	private static function seed_fields(): void {
		$field_defs = array(
			array(
				'key' => 'projekt_helyszin', 'type' => 'address', 'admin' => 'Projekt helyszíne', 'client' => 'Projekt helyszíne',
				'desc' => 'Irányítószám, település, utca, házszám', 'required' => 1, 'placeholder' => '',
			),
			array(
				'key' => 'helyrajzi_szam', 'type' => 'text', 'admin' => 'Helyrajzi szám', 'client' => 'Helyrajzi szám',
				'desc' => 'Ha rendelkezésre áll', 'required' => 0, 'placeholder' => 'pl. 12345/6',
			),
			array(
				'key' => 'alapterulet', 'type' => 'number', 'admin' => 'Alapterület (m²)', 'client' => 'Alapterület (m²)',
				'desc' => 'Az épület nettó alapterülete', 'required' => 1, 'placeholder' => 'pl. 120',
			),
			array(
				'key' => 'szintek_szama', 'type' => 'number', 'admin' => 'Szintek száma', 'client' => 'Szintek száma',
				'desc' => 'Pince, földszint, emelet együtt', 'required' => 1, 'placeholder' => 'pl. 2',
			),
			array(
				'key' => 'epitesi_fazis', 'type' => 'select', 'admin' => 'Építési fázis', 'client' => 'Építési fázis',
				'desc' => '', 'required' => 1, 'options' => array( 'Új építés', 'Felújítás', 'Bővítés', 'Átalakítás' ),
			),
			array(
				'key' => 'projekt_megjegyzes', 'type' => 'textarea', 'admin' => 'Megjegyzés', 'client' => 'További megjegyzés',
				'desc' => 'Egyéb fontos információ a projektről', 'required' => 0, 'placeholder' => '',
			),
			array(
				'key' => 'tervrajz_feltoltes', 'type' => 'file', 'admin' => 'Tervrajz / alaprajz', 'client' => 'Tervrajz vagy alaprajz feltöltése',
				'desc' => 'PDF, DWG, JPG – max. 20 MB', 'required' => 0, 'placeholder' => '',
			),
			array(
				'key' => 'vizigeny', 'type' => 'number', 'admin' => 'Vízigény (m³/nap)', 'client' => 'Becsült vízigény (m³/nap)',
				'desc' => 'Vízbekötési terv esetén', 'required' => 0, 'placeholder' => 'pl. 0.5',
			),
			array(
				'key' => 'gaz_tipusa', 'type' => 'select', 'admin' => 'Gáz típusa', 'client' => 'Gázellátás típusa',
				'desc' => 'Gázbekötési terv esetén', 'required' => 0, 'options' => array( 'Földgáz (központi)', 'PB-gáz', 'Tervezés alatt' ),
			),
			array(
				'key' => 'teljesitmenyigeny', 'type' => 'number', 'admin' => 'Teljesítményigény (kW)', 'client' => 'Teljesítményigény (kW)',
				'desc' => 'Elektromos hálózattervezés esetén', 'required' => 0, 'placeholder' => 'pl. 14',
			),
			array(
				'key' => 'epulet_funkcio', 'type' => 'select', 'admin' => 'Épület funkciója', 'client' => 'Épület funkciója',
				'desc' => '', 'required' => 1, 'options' => array( 'Lakóépület', 'Iroda', 'Kereskedelmi', 'Ipari', 'Egyéb' ),
			),
			array(
				'key' => 'hatarido', 'type' => 'date', 'admin' => 'Kívánt határidő', 'client' => 'Kívánt határidő',
				'desc' => 'Ha van preferált átadási dátum', 'required' => 0, 'placeholder' => '',
			),
			array(
				'key' => 'dokumentum_celja', 'type' => 'radio', 'admin' => 'Dokumentáció célja', 'client' => 'Dokumentáció célja',
				'desc' => '', 'required' => 1, 'options' => array( 'Hatósági benyújtás', 'Beruházói átadás', 'Belső használat' ),
			),
			array(
				'key' => 'forras_dokumentum', 'type' => 'file', 'admin' => 'Forrásdokumentum', 'client' => 'Meglévő dokumentum feltöltése',
				'desc' => 'PDF, DOC, ZIP – max. 20 MB', 'required' => 0, 'placeholder' => '',
			),
			array(
				'key' => 'prioritas', 'type' => 'radio', 'admin' => 'Prioritás', 'client' => 'Projekt prioritása',
				'desc' => '', 'required' => 0, 'options' => array( 'Normál', 'Sürgős', 'Rugalmas határidő' ),
			),
			array(
				'key' => 'kapcsolattarto_email', 'type' => 'email', 'admin' => 'Helyszíni kapcsolattartó e-mail', 'client' => 'Helyszíni kapcsolattartó e-mail',
				'desc' => 'Ha eltér a beküldő adataitól', 'required' => 0, 'placeholder' => 'kapcsolattarto@ceg.ro',
			),
		);

		$field_ids = array();
		foreach ( $field_defs as $i => $def ) {
			$field_ids[ $def['key'] ] = self::insert_field( $def, $i );
		}

		$assign_to_service_items = static function ( string $service_slug, array $keys ) use ( $field_ids ): void {
			if ( empty( self::$service_ids[ $service_slug ] ) ) {
				return;
			}
			$sid   = self::$service_ids[ $service_slug ];
			$items = BMA_AJ_Service_Items::get_for_service( $sid );
			foreach ( $items as $item ) {
				foreach ( $keys as $i => $key ) {
					if ( empty( $field_ids[ $key ] ) ) {
						continue;
					}
					self::assign_field( $field_ids[ $key ], 'service_item', (int) $item->id, $i );
				}
			}
		};

		// Shared fields on every item of the service (deduped at checkout by field_key).
		$assign_to_service_items( 'tervezes', array( 'projekt_helyszin', 'helyrajzi_szam', 'alapterulet', 'szintek_szama', 'epitesi_fazis', 'projekt_megjegyzes', 'tervrajz_feltoltes' ) );
		$assign_to_service_items( 'engedelyeztetes', array( 'projekt_helyszin', 'helyrajzi_szam', 'epulet_funkcio', 'hatarido', 'projekt_megjegyzes', 'forras_dokumentum' ) );
		$assign_to_service_items( 'muszaki-dokumentacio', array( 'projekt_helyszin', 'dokumentum_celja', 'hatarido', 'projekt_megjegyzes', 'forras_dokumentum' ) );
		$assign_to_service_items( 'kozmu-koordinacio', array( 'projekt_helyszin', 'helyrajzi_szam', 'hatarido', 'prioritas', 'kapcsolattarto_email', 'projekt_megjegyzes' ) );

		// Item-specific fields.
		self::assign_field( $field_ids['vizigeny'], 'service_item', self::$item_ids['vizbekotesi-terv'] ?? 0, 50 );
		self::assign_field( $field_ids['gaz_tipusa'], 'service_item', self::$item_ids['gazbekotesi-terv'] ?? 0, 50 );
		self::assign_field( $field_ids['teljesitmenyigeny'], 'service_item', self::$item_ids['elektromos-halozat'] ?? 0, 50 );
	}

	/**
	 * Insert a dynamic field definition.
	 *
	 * @param array<string, mixed> $def  Field definition.
	 * @param int                  $sort Sort order.
	 */
	private static function insert_field( array $def, int $sort ): int {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_dynamic_fields';

		$wpdb->insert(
			$table,
			array(
				'field_key'    => $def['key'],
				'field_type'   => $def['type'],
				'admin_label'  => $def['admin'],
				'client_label' => $def['client'],
				'description'  => $def['desc'] ?? '',
				'placeholder'  => $def['placeholder'] ?? '',
				'is_required'  => ! empty( $def['required'] ) ? 1 : 0,
				'options'      => ! empty( $def['options'] ) ? wp_json_encode( $def['options'] ) : '[]',
				'sort_order'   => $sort,
				'is_active'    => 1,
			)
		);

		return (int) $wpdb->insert_id;
	}

	/**
	 * Assign field to service or service item.
	 */
	private static function assign_field( int $field_id, string $entity_type, int $entity_id, int $sort ): void {
		if ( ! $field_id || ! $entity_id ) {
			return;
		}

		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix . 'bma_field_assignments',
			array(
				'field_id'    => $field_id,
				'entity_type' => $entity_type,
				'entity_id'   => $entity_id,
				'sort_order'  => $sort,
			)
		);
	}

	/**
	 * Seed default settings.
	 */
	private static function seed_settings(): void {
		update_option( 'bma_admin_email', get_option( 'admin_email' ) );
		BMA_AJ_Company::seed_options();
		update_option( 'bma_smtp_host', getenv( 'SMTP_HOST' ) ?: 'mailhog' );
		update_option( 'bma_smtp_port', getenv( 'SMTP_PORT' ) ?: 1025 );
		update_option( 'bma_smtp_from', 'noreply@bma-engineering.hu' );
		update_option( 'bma_smtp_from_name', 'BMA Engineering' );
		update_option( 'bma_default_currency', 'EUR' );
	}

	/**
	 * Add shortcodes to pages.
	 */
	private static function seed_page_content(): void {
		$page_content = array(
			'fooldal'             => "[bma_home_hero]\n[bma_home_services]\n[bma_home_multi]\n[bma_home_benefits]\n[bma_home_process]\n[bma_home_references]\n[bma_home_about]",
			'szolgaltatasok'      => '[bma_services_list]',
			'referenciak'         => '[bma_references_list]',
			'kapcsolat'           => '[bma_contact_form]',
			'ajanlatkosar'        => '[bma_quote_cart]',
			'ajanlatkeres'        => '[bma_quote_form]',
			'ugyfelportal'        => '[bma_client_portal]',
			'adatvedelem'         => '[bma_privacy_policy]',
			'cookie-tajekoztato'  => '[bma_cookie_policy]',
			'aszf'                => '[bma_terms]',
			'fizetes'             => '[bma_payment]',
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
