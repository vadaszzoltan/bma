<?php
/**
 * Service catalog (quote request v2): services, items, add-ons and fields.
 *
 * Single source of truth for the structure agreed with the client
 * (BMA_ajanlatkeres_struktura.docx). sync() is idempotent: it creates what is
 * missing and refreshes seeded texts, but keeps rows edited in the admin
 * (matched by slug / field key) otherwise intact.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Catalog {

	/** Bump to re-run sync() on existing sites. */
	public const VERSION = '2.0.0';

	public const OPTION = 'bma_aj_catalog_version';

	/**
	 * Old service slug => new service slug ('' = services list page).
	 *
	 * @return array<string, string>
	 */
	public static function legacy_redirects(): array {
		return array(
			'tervezes'             => 'epuletgepeszet',
			'kozmu-koordinacio'    => 'bekotesek',
			'engedelyeztetes'      => '',
			'muszaki-dokumentacio' => '',
		);
	}

	/**
	 * RO/EN slugs of the v1 services (their Polylang links are not always intact).
	 *
	 * @return array<string, string[]>
	 */
	public static function legacy_translated_slugs(): array {
		return array(
			'tervezes'             => array( 'proiectare', 'design' ),
			'engedelyeztetes'      => array( 'autorizare', 'permitting' ),
			'muszaki-dokumentacio' => array( 'documentatie-tehnica', 'technical-documentation' ),
			'kozmu-koordinacio'    => array( 'coordonare-utilitati', 'utility-coordination' ),
		);
	}

	/**
	 * Services with their items and permit add-on.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function services(): array {
		return array(
			'epuletgepeszet'         => array(
				'title'          => 'Épületgépészeti tervezés',
				'short'          => 'Épületgépészet',
				'excerpt'        => 'Az épületen belüli rendszerek tervezése: víz-csatorna, fűtés, szellőzés és klíma, elektromos hálózat, belső gáz.',
				'content'        => '<p>Megtervezzük az épület gépészeti rendszereit, az engedélyes tervtől a kivitelezési tervig. Egy ajánlatkérésben több rendszert is kérhet, a tervfázist pedig csak egyszer kell megadnia.</p>',
				'choose_summary' => 'Ha az épület víz-, fűtési, szellőző-, elektromos vagy gázrendszeréhez kell terv, ezt a szolgáltatást válassza.',
				'when_needed'    => "új lakó- vagy üzleti épület építésekor\nmeglévő épület felújításakor, bővítésekor vagy funkcióváltásakor\nha az építési engedélyhez vagy a kivitelezéshez gépészeti terv kell",
				'needs_required' => "hasznos alapterület és szintek száma\népítészeti alaprajzok (DWG vagy PDF) – ezek nélkül nem tudunk érdemi ajánlatot adni\nfűtésnél a tervezett hőforrás (gáz, hőszivattyú, távhő, fa vagy pellet)\nbelső gáznál: van-e már gázbekötés",
				'needs_optional' => "urbanisztikai bizonylat\nmeglévő tervek",
				'needs_later'    => '',
				'has_phase'      => true,
				'related'        => array( 'bekotesek', 'tuzvedelem' ),
				'items'          => array(
					'viz-csatorna'        => array( 'Víz-csatorna', 'Belső vízellátás, szennyvíz- és csapadékvíz-elvezetés', 'az épületben vízvételi helyek, fürdőszobák, konyha készülnek vagy változnak.' ),
					'futes'               => array( 'Fűtés', 'Hőforrás, hőközpont, fűtési rendszer', 'új fűtési rendszer készül, vagy a meglévőt cseréli, bővíti.' ),
					'szellozes-klima'     => array( 'Szellőzés, klíma és hűtés', 'Légtechnika, klimatizálás, hűtési rendszerek', 'szellőző-, klíma- vagy hűtőrendszer kerül az épületbe, vagy ezt előírás követeli meg.' ),
					'elektromos-halozat'  => array( 'Elektromos hálózat', 'Erősáram, gyengeáram, világítás', 'új villamos hálózat készül, vagy a meglévőt átalakítja, bővíti.' ),
					'belso-gazhalozat'    => array( 'Belső gázhálózat', 'Épületen belüli gázellátás', 'gázkészüléket (kazánt, tűzhelyet) köt be, vagy a belső gázvezeték változik.' ),
				),
				'permit'         => array( 'Engedélyeztetés – Épületgépészet', 'A szükséges engedélyek köre az urbanisztikai bizonylatban szerepel, ezt az ajánlatban pontosítjuk.' ),
			),
			'tuzvedelem'             => array(
				'title'          => 'Tűzvédelmi rendszerek tervezése',
				'short'          => 'Tűzvédelem',
				'excerpt'        => 'Tűzoltó vízellátás, sprinkler, hő- és füstelvezetés, tűzjelzés tervezése, a tűzvédelmi engedélyig (ISU).',
				'content'        => '<p>Megtervezzük azokat a rendszereket, amelyek tűz esetén jeleznek, oltanak és elvezetik a füstöt. Segítünk eldönteni, mi kötelező az épülete esetében, és ha kéri, a tűzvédelmi engedélyt (ISU) is intézzük.</p>',
				'choose_summary' => 'Ha az épülethez tűzvédelmi rendszer vagy tűzvédelmi engedély (ISU) kell, ezt a szolgáltatást válassza.',
				'when_needed'    => "közösségi, kereskedelmi, ipari vagy többszintes lakóépületnél\nha a hatóság vagy az urbanisztikai bizonylat tűzvédelmi engedélyt ír elő\nfunkcióváltásnál vagy bővítésnél",
				'needs_required' => "alapterület, épületmagasság, funkció\nalaprajzok",
				'needs_optional' => 'tűzvédelmi forgatókönyv (ha nincs, beszerezzük)',
				'needs_later'    => '',
				'has_phase'      => true,
				'related'        => array( 'kozmuhalozatok', 'epuletgepeszet' ),
				'items'          => array(
					'tuzolto-vizellatas'  => array( 'Tűzoltó vízellátás', 'Belső és külső hidránsok, tűzivíz-tározó, tűzoltó szivattyúk', 'az épületben vagy a telken tűzcsapokra, tűzivíz-tározóra van szükség.' ),
					'sprinkler'           => array( 'Sprinkler / automatikus oltórendszer', 'Automatikus oltóberendezések', 'nagyobb alapterületű vagy tűzveszélyes funkciójú épületről van szó.' ),
					'ho-es-fustelvezetes' => array( 'Hő- és füstelvezetés', 'Természetes és gépi füstelvezetés', 'lépcsőházak, folyosók vagy nagy terek füstmentesítése szükséges.' ),
					'tuzjelzo-rendszer'   => array( 'Tűzjelző rendszer', 'Tűzérzékelés és -jelzés', 'az épületben automatikus tűzjelzés kötelező vagy kívánatos.' ),
				),
				'permit'         => array( 'Engedélyeztetés – Tűzvédelem (ISU)', 'Tűzvédelmi engedély (ISU). A szükséges engedélyek köre az urbanisztikai bizonylatban szerepel, ezt az ajánlatban pontosítjuk.' ),
			),
			'kozmuhalozatok'         => array(
				'title'          => 'Közműhálózatok tervezése',
				'short'          => 'Közműhálózatok',
				'excerpt'        => 'Telken és településen futó hálózatok: víz, szennyvíz, csapadékvíz, tűzivíz, tisztítás és átemelők.',
				'content'        => '<p>Megtervezzük az épületeken kívüli hálózatokat és létesítményeket: telken belüli vezetékeket, utcai és települési hálózatokat, víz- és szennyvíztisztítást, átemelőket.</p>',
				'choose_summary' => 'Ha telken vagy településen futó víz-, szennyvíz-, csapadékvíz- vagy tűzivízhálózat kell, ezt a szolgáltatást válassza.',
				'when_needed'    => "lakópark, ipari park vagy telephely közművesítésénél\nutcai vagy települési hálózat építésénél, bővítésénél (önkormányzati beruházások)\nha saját víz- vagy szennyvíztisztításra van szükség",
				'needs_required' => "becsült hálózathossz\nhelyszínrajz vagy nyomvonal\nellátott fogyasztók vagy lakosegyenérték száma",
				'needs_optional' => "a csatlakozási pont ismerete\ntopográfiai felmérés\ngeotechnikai (geológiai) tanulmány",
				'needs_later'    => 'Ami nincs meg, azt beszerezzük – a költsége az ajánlatban külön tételként szerepel.',
				'has_phase'      => true,
				'related'        => array( 'bekotesek', 'tuzvedelem' ),
				'items'          => array(
					'vizhalozat'                => array( 'Vízhálózat', 'Ivóvízellátó hálózat', 'több épületet, utcát vagy településrészt kell vízzel ellátni.' ),
					'viztisztitas-vizkezeles'   => array( 'Víztisztítás és vízkezelés', 'Ivóvíz-tisztítás, vízkezelő berendezések, víztisztító állomások', 'a vízforrás (pl. kút) vizét kezelni vagy tisztítani kell.' ),
					'szennyvizhalozat'          => array( 'Szennyvízhálózat', 'Szennyvízgyűjtő hálózat', 'a szennyvíz elvezetését több épületre vagy utcára kell megoldani.' ),
					'csapadekviz-halozat'       => array( 'Csapadékvíz-hálózat', 'Csapadékvíz-elvezetés, -gyűjtés', 'utak, parkolók, tetők esővizét kell elvezetni vagy gyűjteni.' ),
					'tuzivizhalozat'            => array( 'Tűzivízhálózat', 'Külső tűzivíz-ellátó hálózat', 'a területen külső tűzcsapokra van szükség.' ),
					'atemelok-szivattyutelepek' => array( 'Átemelők és szivattyútelepek', 'Szennyvíz-átemelők, nyomásfokozók', 'a terep miatt a szennyvíz nem folyik gravitációsan, vagy a víznyomás kevés.' ),
					'szennyviztisztitas'        => array( 'Szennyvíztisztítás és előtisztítás', 'Tisztítóművek, olaj- és zsírfogók', 'nincs közcsatorna, vagy a szennyvíz előtisztítást igényel (pl. konyha, parkoló).' ),
				),
				'permit'         => array( 'Engedélyeztetés – Közműhálózatok', 'A szükséges engedélyek köre az urbanisztikai bizonylatban szerepel, ezt az ajánlatban pontosítjuk.' ),
			),
			'bekotesek'              => array(
				'title'          => 'Bekötések',
				'short'          => 'Bekötések',
				'excerpt'        => 'Az épület csatlakozása a közműhálózatra: víz, szennyvíz, csapadékvíz és tűzivíz.',
				'content'        => '<p>Elkészítjük a bekötési terveket, amelyekkel az épület a közműhálózatra csatlakozhat. A bekötésekhez egyetlen dokumentáció készül, itt nem kell tervfázist választania.</p><p>A bekötés tervezéséhez urbanisztikai bizonylat szükséges. Ha még nincs, a beszerzését vállaljuk.</p>',
				'choose_summary' => 'Ha az épületet rá kell kötni a víz-, szennyvíz-, csapadékvíz- vagy tűzivízhálózatra, ezt a szolgáltatást válassza.',
				'when_needed'    => "új épületnél, amely még nincs rákötve a közműre\nha a meglévő bekötés kevés, vagy át kell helyezni",
				'needs_required' => "helyszínrajz a telekkel és az épülettel\na várható fogyasztás vagy a csatlakozó épület jellege",
				'needs_optional' => 'a meglévő hálózat helye és a szolgáltató neve (ha nem tudja, beszerezzük)',
				'needs_later'    => '',
				'has_phase'      => false,
				'related'        => array( 'epuletgepeszet', 'kozmuhalozatok' ),
				'items'          => array(
					'vizbekotes'          => array( 'Vízbekötés', 'Csatlakozás a közüzemi vízhálózatra', 'új vízbekötés kell, vagy a meglévőt bővíti, áthelyezi.' ),
					'szennyvizbekotes'    => array( 'Szennyvízbekötés', 'Csatlakozás a közüzemi szennyvízhálózatra', 'az épületet rá kell kötni a csatornára.' ),
					'csapadekviz-bekotes' => array( 'Csapadékvíz-bekötés', 'Csatlakozás a csapadékvíz-hálózatra', 'a telek esővizét a csapadékvíz-hálózatba kell vezetni.' ),
					'tuzivizbekotes'      => array( 'Tűzivízbekötés', 'Csatlakozás a tűzivíz-hálózatra', 'az épületben tűzcsap vagy oltórendszer lesz.' ),
				),
				'permit'         => array( 'Engedélyeztetés – Bekötések', 'A szükséges engedélyek köre az urbanisztikai bizonylatban szerepel, ezt az ajánlatban pontosítjuk.' ),
			),
		);
	}

	/**
	 * General add-ons (any project). auto_rule: see BMA_AJ_Quote_Flow::rule_matches().
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function general_addons(): array {
		$no = static fn( string $field ) => array( 'any' => array( array( 'field' => $field, 'values' => array( 'nem' ) ) ) );

		return array(
			'urbanisztikai-bizonylat-beszerzese' => array( 'Urbanisztikai bizonylat beszerzése', 'Az önkormányzat által kiadott dokumentum (certificat de urbanism), amely felsorolja a szükséges engedélyeket.', $no( 'urbanisztikai_bizonylat' ) ),
			'tuzvedelmi-forgatokonyv-beszerzese' => array( 'Tűzvédelmi forgatókönyv beszerzése', 'A tűzvédelmi tervezés alapja (scenariu de securitate la incendiu).', $no( 'tuzvedelmi_forgatokonyv' ) ),
			'topografiai-felmeres-beszerzese'    => array( 'Topográfiai felmérés beszerzése', 'A telek és a terep pontos felmérése, a hálózatok nyomvonalához.', $no( 'topografiai_felmeres' ) ),
			'geotechnikai-tanulmany-beszerzese'  => array( 'Geotechnikai (geológiai) tanulmány beszerzése', 'A talaj vizsgálata, a vezetékek és műtárgyak tervezéséhez.', $no( 'geotechnikai_tanulmany' ) ),
			'kozmuszolgaltatoi-adatok-beszerzese' => array(
				'Közműszolgáltatói adatok beszerzése',
				'A meglévő hálózat helye és a csatlakozási pont, a szolgáltatótól.',
				array(
					'any' => array(
						array( 'field' => 'csatlakozasi_pont_ismert', 'values' => array( 'nem' ) ),
						array( 'field' => 'meglevo_halozat_ismert', 'values' => array( 'nem' ) ),
					),
				),
			),
			'terv-ellenorzes'                    => array(
				'Terv-ellenőrzés jogosult ellenőrrel',
				'A kivitelezési terveket jogosult tervellenőrnek (verificator) kell ellenőriznie. A kivitelezési tervekhez kötelező.',
				array(
					'any'    => array( array( 'field' => '_phase', 'values' => array( 'pt_dde', 'dtac_pt' ) ) ),
					'locked' => true,
				),
			),
			'tervezoi-muvezetes'                 => array( 'Tervezői művezetés a kivitelezés alatt', 'A tervező a kivitelezés alatt ellenőrzi, hogy a terv szerint épül-e.', null ),
			'helyszini-felmeres'                 => array( 'Helyszíni felmérés', 'Kimegyünk a helyszínre, és felmérjük a meglévő állapotot.', null ),
			'muszaki-szakvelemeny'               => array( 'Műszaki szakvélemény, meglévő rendszer felmérése', 'Írásos vélemény egy meglévő rendszer állapotáról és a teendőkről.', null ),
			'tanacsadas'                         => array( 'Nem tudom pontosan, mire van szükségem – tanácsadást kérek', 'Együtt átbeszéljük a projektet, és javasoljuk, mire van szüksége.', null ),
			'egyedi-keres'                       => array( 'Egyéb, egyedi kérés', 'Írja le, mire lenne még szüksége.', null ),
		);
	}

	/**
	 * Dynamic fields. placement: project (step 1), documents (step 5, everyone),
	 * item (step 5, per selected item), addons (step 4).
	 * conditions: {field, values} visibility; required_services: required only
	 * when an item of these services is selected; one_of: not required when one
	 * of these fields has a value; soft_required: warn but let the visitor go on.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function fields(): array {
		$yes_no = array( 'igen|Igen', 'nem|Nem' );
		$gm     = array( 'viz-csatorna', 'futes', 'szellozes-klima', 'elektromos-halozat', 'belso-gazhalozat' );
		$tv     = array( 'tuzolto-vizellatas', 'sprinkler', 'ho-es-fustelvezetes', 'tuzjelzo-rendszer' );
		$kh     = array( 'vizhalozat', 'viztisztitas-vizkezeles', 'szennyvizhalozat', 'csapadekviz-halozat', 'tuzivizhalozat', 'atemelok-szivattyutelepek', 'szennyviztisztitas' );
		$bk     = array( 'vizbekotes', 'szennyvizbekotes', 'csapadekviz-bekotes', 'tuzivizbekotes' );

		return array(
			// Step 1 – project.
			'telepules'                    => array( 'type' => 'text', 'label' => 'Település', 'placeholder' => 'pl. Székelyudvarhely', 'required' => 1, 'placement' => 'project' ),
			'cim_vagy_hrsz'                => array( 'type' => 'text', 'label' => 'Cím vagy telekkönyvi szám', 'placeholder' => 'pl. Kossuth u. 12., vagy CF 12345', 'desc' => 'Elég az egyik.', 'required' => 1, 'placement' => 'project' ),
			'epulet_funkcio'               => array( 'type' => 'select', 'label' => 'Az épület vagy létesítmény funkciója', 'required' => 1, 'placement' => 'project', 'options' => array( 'lakoepulet|Lakóépület', 'tobblakasos|Többlakásos lakóépület', 'iroda|Iroda', 'kereskedelmi|Kereskedelmi', 'ipari|Ipari, raktár', 'kozepulet|Középület', 'kozmu|Közmű-létesítmény', 'egyeb|Egyéb' ) ),
			'projekt_jellege'              => array( 'type' => 'radio', 'label' => 'A projekt jellege', 'required' => 1, 'placement' => 'project', 'options' => array( 'uj|Új építés', 'felujitas|Felújítás', 'bovites|Bővítés' ) ),
			'alapterulet'                  => array( 'type' => 'number', 'label' => 'Hasznos alapterület (m²)', 'placeholder' => 'pl. 160', 'desc' => 'Ha nem pontos, becsülje meg.', 'required' => 0, 'placement' => 'project', 'conditions' => array( 'required_services' => array( 'epuletgepeszet', 'tuzvedelem' ) ) ),
			'szintek_szama'                => array( 'type' => 'number', 'label' => 'Szintek száma', 'placeholder' => 'pl. 2', 'desc' => 'Pince, földszint és emeletek együtt.', 'required' => 0, 'placement' => 'project', 'conditions' => array( 'required_services' => array( 'epuletgepeszet' ) ) ),
			'hatarido'                     => array( 'type' => 'date', 'label' => 'Várható határidő', 'desc' => 'Mikorra lenne szüksége a tervekre?', 'required' => 0, 'placement' => 'project' ),

			// Step 5 – documents for everyone.
			'urbanisztikai_bizonylat'      => array( 'type' => 'radio', 'label' => 'Rendelkezik urbanisztikai bizonylattal?', 'desc' => 'Ha nincs, nem akadály: a beszerzését felvesszük a kiegészítők közé.', 'required' => 1, 'placement' => 'documents', 'options' => array( 'igen|Igen', 'nem|Nem', 'folyamatban|Folyamatban' ) ),
			'urbanisztikai_bizonylat_fajl' => array( 'type' => 'file', 'label' => 'Töltse fel az urbanisztikai bizonylatot', 'required' => 0, 'placement' => 'documents', 'conditions' => array( 'field' => 'urbanisztikai_bizonylat', 'values' => array( 'igen' ) ) ),
			'meglevo_dokumentumok'         => array( 'type' => 'file', 'label' => 'Meglévő tervek, egyéb dokumentumok', 'required' => 0, 'placement' => 'documents' ),

			// Step 5 – per item.
			'alaprajz'                     => array( 'type' => 'file', 'label' => 'Építészeti alaprajzok (DWG vagy PDF)', 'desc' => 'Ezek nélkül nem tudunk érdemi ajánlatot adni.', 'required' => 0, 'placement' => 'item', 'items' => array_merge( $gm, $tv ), 'conditions' => array( 'soft_required' => true ) ),
			'hoforras'                     => array( 'type' => 'select', 'label' => 'Tervezett hőforrás', 'required' => 1, 'placement' => 'item', 'items' => array( 'futes' ), 'options' => array( 'gaz|Gáz', 'hoszivattyu|Hőszivattyú', 'tavho|Távhő', 'fa_pellet|Fa vagy pellet', 'nem_tudom|Még nem tudom' ) ),
			'gazbekotes_megvan'            => array( 'type' => 'radio', 'label' => 'Van már gázbekötés?', 'required' => 1, 'placement' => 'item', 'items' => array( 'belso-gazhalozat' ), 'options' => array( 'igen|Igen', 'nem|Nem', 'nem_tudom|Nem tudom' ) ),
			'epuletmagassag'               => array( 'type' => 'number', 'label' => 'Épületmagasság (m)', 'placeholder' => 'pl. 12', 'required' => 1, 'placement' => 'item', 'items' => $tv ),
			'tuzvedelmi_forgatokonyv'      => array( 'type' => 'radio', 'label' => 'Van tűzvédelmi forgatókönyv?', 'required' => 1, 'placement' => 'item', 'items' => $tv, 'options' => $yes_no ),
			'tuzvedelmi_forgatokonyv_fajl' => array( 'type' => 'file', 'label' => 'Tűzvédelmi forgatókönyv feltöltése', 'required' => 0, 'placement' => 'item', 'items' => $tv, 'conditions' => array( 'field' => 'tuzvedelmi_forgatokonyv', 'values' => array( 'igen' ) ) ),
			'halozat_hossz'                => array( 'type' => 'number', 'label' => 'Becsült hálózathossz (m)', 'placeholder' => 'pl. 850', 'required' => 1, 'placement' => 'item', 'items' => $kh ),
			'nyomvonal'                    => array( 'type' => 'textarea', 'label' => 'Helyszínrajz vagy nyomvonal leírása', 'placeholder' => 'pl. a Fő utca mentén, a 12-es számtól a patakig', 'desc' => 'Ha van helyszínrajza, töltse fel lent.', 'required' => 1, 'placement' => 'item', 'items' => $kh, 'conditions' => array( 'one_of' => array( 'nyomvonal_fajl' ) ) ),
			'nyomvonal_fajl'               => array( 'type' => 'file', 'label' => 'Helyszínrajz vagy nyomvonalrajz', 'required' => 0, 'placement' => 'item', 'items' => $kh ),
			'fogyasztok_szama'             => array( 'type' => 'number', 'label' => 'Ellátott fogyasztók vagy lakosegyenérték száma', 'placeholder' => 'pl. 120', 'required' => 1, 'placement' => 'item', 'items' => $kh ),
			'csatlakozasi_pont_ismert'     => array( 'type' => 'radio', 'label' => 'Ismert a csatlakozási pont?', 'required' => 1, 'placement' => 'item', 'items' => $kh, 'options' => $yes_no ),
			'topografiai_felmeres'         => array( 'type' => 'radio', 'label' => 'Van topográfiai felmérés?', 'required' => 1, 'placement' => 'item', 'items' => $kh, 'options' => $yes_no ),
			'topografiai_felmeres_fajl'    => array( 'type' => 'file', 'label' => 'Topográfiai felmérés feltöltése', 'required' => 0, 'placement' => 'item', 'items' => $kh, 'conditions' => array( 'field' => 'topografiai_felmeres', 'values' => array( 'igen' ) ) ),
			'geotechnikai_tanulmany'       => array( 'type' => 'radio', 'label' => 'Van geotechnikai (geológiai) tanulmány?', 'required' => 1, 'placement' => 'item', 'items' => $kh, 'options' => $yes_no ),
			'geotechnikai_tanulmany_fajl'  => array( 'type' => 'file', 'label' => 'Geotechnikai tanulmány feltöltése', 'required' => 0, 'placement' => 'item', 'items' => $kh, 'conditions' => array( 'field' => 'geotechnikai_tanulmany', 'values' => array( 'igen' ) ) ),
			'helyszinrajz_fajl'            => array( 'type' => 'file', 'label' => 'Helyszínrajz a telekkel és az épülettel', 'required' => 1, 'placement' => 'item', 'items' => $bk ),
			'varhato_fogyasztas'           => array( 'type' => 'textarea', 'label' => 'Várható fogyasztás vagy a csatlakozó épület jellege', 'placeholder' => 'pl. családi ház, 4 fő', 'required' => 1, 'placement' => 'item', 'items' => $bk ),
			'meglevo_halozat_ismert'       => array( 'type' => 'radio', 'label' => 'Ismert a meglévő hálózat helye és a szolgáltató?', 'required' => 1, 'placement' => 'item', 'items' => $bk, 'options' => $yes_no ),
			'szolgaltato_neve'             => array( 'type' => 'text', 'label' => 'A szolgáltató neve', 'placeholder' => 'pl. Aquaserv', 'required' => 0, 'placement' => 'item', 'items' => $bk, 'conditions' => array( 'field' => 'meglevo_halozat_ismert', 'values' => array( 'igen' ) ) ),

			// Step 4 – add-on details.
			'egyedi_keres_leiras'          => array( 'type' => 'textarea', 'label' => 'Írja le, mire lenne még szüksége', 'required' => 1, 'placement' => 'addons', 'conditions' => array( 'field' => '_addon', 'values' => array( 'egyedi-keres' ) ) ),
		);
	}

	/**
	 * v1 field keys no longer asked in the v2 flow (kept for old requests' labels).
	 *
	 * @return string[]
	 */
	public static function retired_fields(): array {
		return array(
			'projekt_helyszin', 'helyrajzi_szam', 'epitesi_fazis', 'projekt_megjegyzes', 'tervrajz_feltoltes',
			'vizigeny', 'gaz_tipusa', 'teljesitmenyigeny', 'dokumentum_celja', 'forras_dokumentum', 'prioritas', 'kapcsolattarto_email',
		);
	}

	/**
	 * Run sync once per catalog version.
	 */
	public static function maybe_sync(): void {
		if ( get_option( self::OPTION ) === self::VERSION ) {
			return;
		}
		self::sync();
		update_option( self::OPTION, self::VERSION );
	}

	/**
	 * Create / refresh the v2 structure; retire v1 services.
	 */
	public static function sync(): void {
		global $wpdb;

		BMA_AJ_Schema::maybe_upgrade();

		$items_table = $wpdb->prefix . 'bma_service_items';
		$links_table = $wpdb->prefix . 'bma_service_item_services';
		$service_ids = array();
		$item_ids    = array();
		$order       = 0;

		foreach ( self::services() as $slug => $svc ) {
			$post_id = self::upsert_service( $slug, $svc, $order++ );
			if ( ! $post_id ) {
				continue;
			}
			$service_ids[ $slug ] = $post_id;

			$sort = 0;
			foreach ( $svc['items'] as $item_slug => $item ) {
				$item_ids[ $item_slug ] = self::upsert_item(
					$post_id,
					array(
						'slug'              => $item_slug,
						'name'              => $item[0],
						'short_description' => $item[1],
						'choose_if'         => $item[2],
						'item_type'         => 'item',
						'auto_rule'         => null,
					),
					$sort++
				);
			}

			self::upsert_item(
				$post_id,
				array(
					'slug'              => 'engedelyeztetes-' . $slug,
					'name'              => $svc['permit'][0],
					'short_description' => $svc['permit'][1],
					'choose_if'         => '',
					'item_type'         => 'addon',
					'auto_rule'         => null,
				),
				100
			);
		}

		$sort = 0;
		foreach ( self::general_addons() as $slug => $addon ) {
			self::upsert_item(
				0,
				array(
					'slug'              => $slug,
					'name'              => $addon[0],
					'short_description' => $addon[1],
					'choose_if'         => '',
					'item_type'         => 'addon',
					'auto_rule'         => $addon[2] ? wp_json_encode( $addon[2] ) : null,
				),
				$sort++
			);
		}

		foreach ( self::services() as $slug => $svc ) {
			if ( empty( $service_ids[ $slug ] ) ) {
				continue;
			}
			$related = array();
			foreach ( $svc['related'] as $rel ) {
				if ( ! empty( $service_ids[ $rel ] ) ) {
					$related[] = $service_ids[ $rel ];
				}
			}
			update_post_meta( $service_ids[ $slug ], '_bma_related_services', $related );
		}

		self::sync_fields( $item_ids );
		self::retire_legacy_services( $service_ids );
		self::migrate_page_content();
	}

	/**
	 * v1 defaults of site texts that changed with v2 (BMA_AJ_Content keys).
	 *
	 * @return array<string, string>
	 */
	private static function legacy_content_defaults(): array {
		return array(
			'home.hero.eyebrow' => 'Tervezés · Engedélyeztetés · Közmű-koordináció',
			'home.hero.lead' => 'Válassza ki, mire van szüksége a projektjéhez, adja meg a rendelkezésére álló adatokat, és kérjen személyre szabott ajánlatot. A folyamat minden lépését egy helyen követheti.',
			'home.services.lead' => 'Négy szolgáltatási terület, azon belül konkrét részfeladatokkal. Az ajánlatkérésbe bármennyi tétel bekerülhet.',
			'home.multi.body_1' => 'Egy építési vagy fejlesztési projekt során a tervezés, az engedélyeztetés, a közműcsatlakozások és a műszaki dokumentáció gyakran egymásra épül.',
			'home.multi.cta_primary' => 'Szolgáltatások kiválasztása',
			'home.multi.stack_1_title' => 'Vízbekötési terv',
			'home.multi.stack_1_sub' => 'Tervezés',
			'home.multi.stack_2_title' => 'Szennyvízbekötési terv',
			'home.multi.stack_2_sub' => 'Tervezés',
			'home.multi.stack_3_title' => 'Építési engedély előkészítés',
			'home.multi.stack_3_sub' => 'Engedélyeztetés',
			'home.multi.stack_4_title' => 'Vízmű egyeztetés',
			'home.multi.stack_4_sub' => 'Közmű-koordináció',
			'home.multi.stack_foot' => '4 tétel · 1 ajánlatkérés',
			'home.benefits.2_text' => 'Egy ajánlatkérésben több tervezési, engedélyeztetési, közmű-koordinációs és dokumentációs feladat is összeállítható.',
			'home.benefits.4_title' => 'Kevesebb felesleges egyeztetés',
			'home.benefits.4_text' => 'Már az ajánlatkérés során megadhatók a projekt legfontosabb adatai, így az ajánlatkészítés célzottabban indulhat el.',
			'home.process.1_title' => 'Szolgáltatások kiválasztása',
			'home.process.1_text' => 'Válassza ki a projektjéhez szükséges szolgáltatásokat és azokon belül a konkrét részfeladatokat.',
			'home.process.2_title' => 'Projekt adatai',
			'home.process.2_text' => 'Töltse ki a kiválasztott tételekhez tartozó adatokat, és töltse fel a meglévő tervrajzokat.',
			'home.process.3_title' => 'Beküldés',
			'home.process.3_text' => 'Ellenőrizze az összeállítást, adja meg a kapcsolattartási adatait, és küldje el a kérést.',
			'home.refs.lead' => 'Válogatás megvalósult tervezési, engedélyeztetési és közmű-koordinációs munkáinkból.',
			'home.about.body_1' => 'A BMA Engineering mérnöki tervezési, engedélyeztetési, közmű-koordinációs és műszaki dokumentációs feladatokban támogatja ügyfeleit.',
			'home.closing.body' => 'Válassza ki a szükséges szolgáltatásokat, adja meg a projekt alapadatait, és töltse fel a rendelkezésére álló dokumentumokat. Ezek alapján pontosabb ajánlatot tudunk készíteni.',
			'services.lead' => 'Válassza ki az Ön projektjéhez kapcsolódó szolgáltatási területet. Egy ajánlatkérésben több szolgáltatást és azokon belül több részfeladatot is megjelölhet.',
			'services.side_1' => 'Jelölje be a szükséges tételeket – bármennyit.',
			'services.side_2' => 'Adja meg a projekt adatait és a meglévő dokumentumokat.',
		);
	}

	/**
	 * Drop stored site texts that still hold the v1 default (or its RO/EN
	 * translation), so the new default shows. Texts edited in wp-admin stay.
	 */
	private static function migrate_page_content(): void {
		$old = self::legacy_content_defaults();

		$translations = array( 'hu' => array() );
		foreach ( array( 'ro' => 'ro_RO', 'en' => 'en_US' ) as $lang => $locale ) {
			$file = BMA_AJ_PLUGIN_DIR . 'languages/bma-ajanlatkeres-' . $locale . '.json';
			$map  = file_exists( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$translations[ $lang ] = is_array( $map ) ? $map : array();
		}
		$is_old = static function ( string $key, $value, string $lang ) use ( $old, $translations ): bool {
			if ( ! isset( $old[ $key ] ) || ! is_string( $value ) ) {
				return false;
			}
			return $value === $old[ $key ] || $value === (string) ( $translations[ $lang ][ $old[ $key ] ][0] ?? "\0" );
		};

		$posts = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'meta_key'       => BMA_AJ_Content::META_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'fields'         => 'ids',
				'lang'           => '',
			)
		);
		foreach ( $posts as $post_id ) {
			$stored  = BMA_AJ_Content::stored_for_post( (int) $post_id );
			$lang    = BMA_AJ_Content::lang_for_post( (int) $post_id );
			$changed = false;
			foreach ( $stored as $key => $value ) {
				if ( $is_old( (string) $key, $value, $lang ) ) {
					unset( $stored[ $key ] );
					$changed = true;
				}
			}
			if ( $changed ) {
				update_post_meta( (int) $post_id, BMA_AJ_Content::META_KEY, $stored );
			}
		}

		$legacy = BMA_AJ_Content::legacy_stored();
		foreach ( $legacy as $lang => $values ) {
			foreach ( (array) $values as $key => $value ) {
				if ( $is_old( (string) $key, $value, (string) $lang ) ) {
					unset( $legacy[ $lang ][ $key ] );
				}
			}
		}
		update_option( BMA_AJ_Content::OPTION, $legacy );
	}

	/**
	 * Create or update a service post by slug.
	 *
	 * @param string               $slug  Post slug.
	 * @param array<string, mixed> $svc   Catalog row.
	 * @param int                  $order Sort order.
	 */
	private static function upsert_service( string $slug, array $svc, int $order ): int {
		$existing = get_posts(
			array(
				'post_type'      => BMA_AJ_Service_CPT::POST_TYPE,
				'name'           => $slug,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'lang'           => '',
			)
		);
		$post_id = $existing ? (int) $existing[0]->ID : 0;

		if ( ! $post_id ) {
			$post_id = (int) wp_insert_post(
				array(
					'post_title'   => $svc['title'],
					'post_name'    => $slug,
					'post_excerpt' => $svc['excerpt'],
					'post_content' => $svc['content'],
					'post_status'  => 'publish',
					'post_type'    => BMA_AJ_Service_CPT::POST_TYPE,
				)
			);
			if ( ! $post_id ) {
				return 0;
			}
			if ( function_exists( 'pll_set_post_language' ) ) {
				pll_set_post_language( $post_id, 'hu' );
			}
			// Texts are only seeded on creation; later edits in wp-admin win.
			update_post_meta( $post_id, '_bma_short_description', $svc['excerpt'] );
			update_post_meta( $post_id, '_bma_short_title', $svc['short'] );
			update_post_meta( $post_id, '_bma_choose_summary', $svc['choose_summary'] );
			update_post_meta( $post_id, '_bma_when_needed', $svc['when_needed'] );
			update_post_meta( $post_id, '_bma_needs_required', $svc['needs_required'] );
			update_post_meta( $post_id, '_bma_needs_optional', $svc['needs_optional'] );
			update_post_meta( $post_id, '_bma_needs_later', $svc['needs_later'] );
			update_post_meta( $post_id, '_bma_use_placeholder', '0' );
		}

		update_post_meta( $post_id, '_bma_is_active', '1' );
		update_post_meta( $post_id, '_bma_sort_order', $order );
		update_post_meta( $post_id, '_bma_no_design_phase', $svc['has_phase'] ? '0' : '1' );

		return $post_id;
	}

	/**
	 * Create or update an item / add-on by slug.
	 *
	 * @param int                  $service_id Service post ID (0 = general add-on).
	 * @param array<string, mixed> $row        Columns.
	 * @param int                  $sort       Sort order.
	 */
	private static function upsert_item( int $service_id, array $row, int $sort ): int {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_service_items';

		$id = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT id FROM {$table} WHERE slug = %s AND service_id = %d LIMIT 1", $row['slug'], $service_id )
		);

		$data = array(
			'service_id'  => $service_id,
			'slug'        => $row['slug'],
			'item_type'   => $row['item_type'],
			'auto_rule'   => $row['auto_rule'],
			'is_active'   => 1,
			'is_required' => 0,
			'sort_order'  => $sort,
		);

		if ( $id ) {
			$wpdb->update( $table, $data, array( 'id' => $id ) );
		} else {
			$data['name']              = $row['name'];
			$data['short_description'] = $row['short_description'];
			$data['choose_if']         = $row['choose_if'];
			$wpdb->insert( $table, $data );
			$id = (int) $wpdb->insert_id;
		}

		if ( $id && $service_id ) {
			$wpdb->query(
				$wpdb->prepare(
					"INSERT IGNORE INTO {$wpdb->prefix}bma_service_item_services (item_id, service_id, sort_order) VALUES (%d, %d, %d)",
					$id,
					$service_id,
					$sort
				)
			);
		}

		return $id;
	}

	/**
	 * Create / refresh field definitions and item assignments.
	 *
	 * @param array<string, int> $item_ids Item slug => ID.
	 */
	private static function sync_fields( array $item_ids ): void {
		global $wpdb;
		$fields = $wpdb->prefix . 'bma_dynamic_fields';
		$assign = $wpdb->prefix . 'bma_field_assignments';
		$sort   = 0;

		foreach ( self::fields() as $key => $def ) {
			// Texts are refreshed too: sync() only runs when VERSION changes, and some
			// keys existed in v1 with other labels and options.
			$data = array(
				'admin_label'  => $def['label'],
				'client_label' => $def['label'],
				'description'  => $def['desc'] ?? '',
				'placeholder'  => $def['placeholder'] ?? '',
				'field_type'  => $def['type'],
				'is_required' => (int) $def['required'],
				'options'     => wp_json_encode( $def['options'] ?? array() ),
				'placement'   => $def['placement'],
				'conditions'  => ! empty( $def['conditions'] ) ? wp_json_encode( $def['conditions'] ) : null,
				'sort_order'  => $sort++,
				'is_active'   => 1,
			);

			$field_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$fields} WHERE field_key = %s", $key ) );
			if ( $field_id ) {
				$wpdb->update( $fields, $data, array( 'id' => $field_id ) );
			} else {
				$data['field_key'] = $key;
				$wpdb->insert( $fields, $data );
				$field_id = (int) $wpdb->insert_id;
			}

			if ( ! $field_id ) {
				continue;
			}

			// Assignments are exactly the catalog's: drop v1 links of reused keys.
			$wanted = array_values( array_filter( array_map( static fn( $slug ) => $item_ids[ $slug ] ?? 0, $def['items'] ?? array() ) ) );
			$keep   = $wanted ? ' AND NOT ( entity_type = \'service_item\' AND entity_id IN (' . implode( ',', array_map( 'intval', $wanted ) ) . ') )' : '';
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$assign} WHERE field_id = %d{$keep}", $field_id ) );

			foreach ( $def['items'] ?? array() as $i => $item_slug ) {
				$item_id = $item_ids[ $item_slug ] ?? 0;
				if ( ! $item_id ) {
					continue;
				}
				$exists = (int) $wpdb->get_var(
					$wpdb->prepare(
						"SELECT id FROM {$assign} WHERE field_id = %d AND entity_type = 'service_item' AND entity_id = %d",
						$field_id,
						$item_id
					)
				);
				if ( ! $exists ) {
					$wpdb->insert(
						$assign,
						array(
							'field_id'    => $field_id,
							'entity_type' => 'service_item',
							'entity_id'   => $item_id,
							'sort_order'  => $sort,
						)
					);
				}
			}
		}

		$retired = self::retired_fields();
		$in      = implode( ',', array_fill( 0, count( $retired ), '%s' ) );
		// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		$wpdb->query( $wpdb->prepare( "UPDATE {$fields} SET is_active = 0 WHERE field_key IN ({$in})", $retired ) );
	}

	/**
	 * Deactivate v1 services (all languages) and point them at their successor.
	 *
	 * @param array<string, int> $service_ids New slug => post ID.
	 */
	private static function retire_legacy_services( array $service_ids ): void {
		global $wpdb;

		$translated = self::legacy_translated_slugs();
		foreach ( self::legacy_redirects() as $old_slug => $new_slug ) {
			$posts = get_posts(
				array(
					'post_type'      => BMA_AJ_Service_CPT::POST_TYPE,
					'post_name__in'  => array_merge( array( $old_slug ), $translated[ $old_slug ] ?? array() ),
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'lang'           => '',
				)
			);
			if ( ! $posts ) {
				continue;
			}
			$old_id  = 0;
			$targets = array();
			foreach ( $posts as $post ) {
				$targets[] = (int) $post->ID;
				if ( $post->post_name === $old_slug ) {
					$old_id = (int) $post->ID;
				}
			}
			if ( $old_id && function_exists( 'pll_get_post_translations' ) ) {
				$targets = array_merge( $targets, array_map( 'intval', pll_get_post_translations( $old_id ) ) );
			}
			foreach ( array_unique( $targets ) as $pid ) {
				update_post_meta( $pid, '_bma_is_active', '0' );
				update_post_meta( $pid, '_bma_redirect_to', $new_slug ? (int) ( $service_ids[ $new_slug ] ?? 0 ) : 0 );
			}
			if ( ! $old_id ) {
				continue;
			}

			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}bma_service_items i
					INNER JOIN {$wpdb->prefix}bma_service_item_services l ON l.item_id = i.id
					SET i.is_active = 0 WHERE l.service_id = %d",
					$old_id
				)
			);
		}
	}
}
