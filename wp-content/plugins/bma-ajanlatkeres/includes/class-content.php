<?php
/**
 * Editable site marketing content (homepage, lists, contact, footer).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Content {

	public const OPTION        = 'bma_site_content';
	public const META_KEY      = '_bma_page_content';
	public const FOOTER_OPTION = 'bma_footer_content';

	/** @return array<string, array<string, mixed>> */
	public static function sections(): array {
		return array(
			'home_hero' => __( 'Főoldal – Hero', 'bma-ajanlatkeres' ),
			'home_services' => __( 'Főoldal – Szolgáltatások', 'bma-ajanlatkeres' ),
			'home_multi' => __( 'Főoldal – Multi', 'bma-ajanlatkeres' ),
			'home_benefits' => __( 'Főoldal – Előnyök', 'bma-ajanlatkeres' ),
			'home_process' => __( 'Főoldal – Folyamat', 'bma-ajanlatkeres' ),
			'home_refs' => __( 'Főoldal – Referenciák', 'bma-ajanlatkeres' ),
			'home_about' => __( 'Főoldal – Rólunk', 'bma-ajanlatkeres' ),
			'footer' => __( 'Lábléc', 'bma-ajanlatkeres' ),
			'services_list' => __( 'Szolgáltatás lista', 'bma-ajanlatkeres' ),
			'refs_list' => __( 'Referencia lista', 'bma-ajanlatkeres' ),
			'contact' => __( 'Kapcsolat oldal', 'bma-ajanlatkeres' ),
		);
	}

	/**
	 * Field schema.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function fields(): array {
		return array(
			'home.hero.eyebrow' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Épületgépészet · Tűzvédelem · Közműhálózatok · Bekötések',
			),
			'home.hero.title' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – cím (HTML: <em> engedélyezett)', 'bma-ajanlatkeres' ),
				'type'    => 'html',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => true,
				'default' => 'Mérnöki tervezés <em>átlátható</em> folyamattal',
			),
			'home.hero.lead' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – bevezető', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Adja meg a projekt adatait, válassza ki, milyen tervekre van szüksége, és kérjen személyre szabott ajánlatot. Amit nem tud csatolni, azt mi beszerezzük.',
			),
			'home.hero.cta_primary' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – elsődleges gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatkérés indítása',
			),
			'home.hero.cta_secondary' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – másodlagos gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Referenciák',
			),
			'home.hero.quick_label' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – gyors indítás címke', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Gyors indítás – mire van szüksége?',
			),
			'home.hero.fact_1' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – adat 1', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Odorheiu Secuiesc · Harghita',
			),
			'home.hero.fact_2' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – adat 2', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Lakossági és üzleti projektek',
			),
			'home.hero.fact_3' => array(
				'section' => 'home_hero',
				'label'   => __( 'Hero – adat 3', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Válasz [X] munkanapon belül',
			),
			'home.hero.value_1' => array(
				'section' => 'home_hero',
				'label'   => __( 'Értékcsík 1', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Egy ajánlatkérésben több szolgáltatás és részfeladat is összeállítható.',
			),
			'home.hero.value_2' => array(
				'section' => 'home_hero',
				'label'   => __( 'Értékcsík 2', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'A tervrajzok és dokumentumok az ajánlatkéréshez rendelve tölthetők fel.',
			),
			'home.hero.value_3' => array(
				'section' => 'home_hero',
				'label'   => __( 'Értékcsík 3', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Az ügyfélportálon végigkövethető a kérés státusza és a dokumentumok.',
			),
			'home.services.eyebrow' => array(
				'section' => 'home_services',
				'label'   => __( 'Szolgáltatások – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Szolgáltatások',
			),
			'home.services.title' => array(
				'section' => 'home_services',
				'label'   => __( 'Szolgáltatások – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Miben tudunk segíteni?',
			),
			'home.services.lead' => array(
				'section' => 'home_services',
				'label'   => __( 'Szolgáltatások – bevezető', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Négy tervezési terület, 20 konkrét elemmel. Egy ajánlatkérésben bármennyi kombinálható, engedélyes és kivitelezési tervként is.',
			),
			'home.services.cta' => array(
				'section' => 'home_services',
				'label'   => __( 'Szolgáltatások – gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Összes szolgáltatás',
			),
			'home.multi.eyebrow' => array(
				'section' => 'home_multi',
				'label'   => __( 'Multi – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatkérés',
			),
			'home.multi.title' => array(
				'section' => 'home_multi',
				'label'   => __( 'Multi – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Egy projekt ritkán csak egyetlen feladatból áll',
			),
			'home.multi.body_1' => array(
				'section' => 'home_multi',
				'label'   => __( 'Multi – szöveg 1', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Egy építési projektben az épület gépészete, a bekötések és a szükséges engedélyek egymásra épülnek. Egy ajánlatkérésben mindet kérheti, és amit még nem tud csatolni, azt mi beszerezzük.',
			),
			'home.multi.body_2' => array(
				'section' => 'home_multi',
				'label'   => __( 'Multi – szöveg 2', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Nem szükséges minden műszaki részletet előre ismernie. Adja meg a rendelkezésére álló információkat és dokumentumokat, mi pedig ezek alapján felmérjük a projektet és elkészítjük az ajánlatot.',
			),
			'home.multi.cta_primary' => array(
				'section' => 'home_multi',
				'label'   => __( 'Multi – elsődleges gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatkérés indítása',
			),
			'home.multi.cta_secondary' => array(
				'section' => 'home_multi',
				'label'   => __( 'Multi – másodlagos gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolat',
			),
			'home.multi.stack_title' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa kártya – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Példa: új lakóépület – összeállított ajánlatkérés',
			),
			'home.multi.stack_1_title' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa sor 1 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Víz-csatorna',
			),
			'home.multi.stack_1_sub' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa sor 1 – alcím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Épületgépészet',
			),
			'home.multi.stack_2_title' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa sor 2 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Fűtés',
			),
			'home.multi.stack_2_sub' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa sor 2 – alcím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Épületgépészet',
			),
			'home.multi.stack_3_title' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa sor 3 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Vízbekötés',
			),
			'home.multi.stack_3_sub' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa sor 3 – alcím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Bekötések',
			),
			'home.multi.stack_4_title' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa sor 4 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Urbanisztikai bizonylat beszerzése',
			),
			'home.multi.stack_4_sub' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa sor 4 – alcím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kiegészítő',
			),
			'home.multi.stack_foot' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa kártya – lábléc', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Engedélyes + kivitelezési terv · 4 tétel · 1 ajánlatkérés',
			),
			'home.multi.stack_badge' => array(
				'section' => 'home_multi',
				'label'   => __( 'Példa kártya – badge', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Egy ajánlatban',
			),
			'home.benefits.eyebrow' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előnyök – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Előnyök',
			),
			'home.benefits.title' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előnyök – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Átláthatóbb műszaki ügyintézés',
			),
			'home.benefits.1_title' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előny 1 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Átlátható folyamat',
			),
			'home.benefits.1_text' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előny 1 – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Az ajánlatkéréstől a dokumentumok átadásáig követhető, hol tart a folyamat, és milyen következő lépésekre van szükség.',
			),
			'home.benefits.2_title' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előny 2 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolódó szolgáltatások egy helyen',
			),
			'home.benefits.2_text' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előny 2 – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Egy ajánlatkérésben kérhet épületgépészeti, tűzvédelmi, közműhálózati és bekötési terveket, engedélyeztetéssel együtt.',
			),
			'home.benefits.3_title' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előny 3 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Dokumentumok rendezett kezelése',
			),
			'home.benefits.3_text' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előny 3 – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'A szükséges tervrajzok és műszaki dokumentumok az adott ajánlatkéréshez kapcsolódva tölthetők fel és kezelhetők.',
			),
			'home.benefits.4_title' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előny 4 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Nem kell mindent előre beszereznie',
			),
			'home.benefits.4_text' => array(
				'section' => 'home_benefits',
				'label'   => __( 'Előny 4 – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Amit nem tud csatolni, például az urbanisztikai bizonylatot vagy a topográfiai felmérést, azt mi beszerezzük.',
			),
			'home.process.eyebrow' => array(
				'section' => 'home_process',
				'label'   => __( 'Folyamat – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Folyamat',
			),
			'home.process.title' => array(
				'section' => 'home_process',
				'label'   => __( 'Folyamat – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Az ajánlatkérés menete',
			),
			'home.process.cta' => array(
				'section' => 'home_process',
				'label'   => __( 'Folyamat – gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatkérés indítása',
			),
			'home.process.1_title' => array(
				'section' => 'home_process',
				'label'   => __( 'Lépés 1 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Projekt és tervfázis',
			),
			'home.process.1_text' => array(
				'section' => 'home_process',
				'label'   => __( 'Lépés 1 – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Megadja a projekt alapadatait, és hogy engedélyes vagy kivitelezési tervre van szüksége.',
			),
			'home.process.2_title' => array(
				'section' => 'home_process',
				'label'   => __( 'Lépés 2 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Szolgáltatások',
			),
			'home.process.2_text' => array(
				'section' => 'home_process',
				'label'   => __( 'Lépés 2 – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kiválasztja a szükséges rendszereket és a kiegészítőket.',
			),
			'home.process.3_title' => array(
				'section' => 'home_process',
				'label'   => __( 'Lépés 3 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Dokumentumok és beküldés',
			),
			'home.process.3_text' => array(
				'section' => 'home_process',
				'label'   => __( 'Lépés 3 – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Feltölti, ami megvan. Ami hiányzik, azt jelzi, mi beszerezzük.',
			),
			'home.process.4_title' => array(
				'section' => 'home_process',
				'label'   => __( 'Lépés 4 – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Nyomon követés',
			),
			'home.process.4_text' => array(
				'section' => 'home_process',
				'label'   => __( 'Lépés 4 – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Az ügyfélportálon látja a státuszt, az elkészült ajánlatot és a feltöltött dokumentumokat.',
			),
			'home.refs.eyebrow' => array(
				'section' => 'home_refs',
				'label'   => __( 'Referenciák – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Projektek',
			),
			'home.refs.title' => array(
				'section' => 'home_refs',
				'label'   => __( 'Referenciák – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kiemelt referenciák',
			),
			'home.refs.lead' => array(
				'section' => 'home_refs',
				'label'   => __( 'Referenciák – bevezető', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Válogatás megvalósult épületgépészeti, tűzvédelmi és közműtervezési munkáinkból.',
			),
			'home.refs.cta' => array(
				'section' => 'home_refs',
				'label'   => __( 'Referenciák – gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Összes referencia',
			),
			'home.about.eyebrow' => array(
				'section' => 'home_about',
				'label'   => __( 'Rólunk – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Rólunk',
			),
			'home.about.body_1' => array(
				'section' => 'home_about',
				'label'   => __( 'Rólunk – szöveg 1', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'A BMA Engineering épületgépészeti, tűzvédelmi, közműhálózati és bekötési tervezésben, valamint a kapcsolódó engedélyeztetésben támogatja ügyfeleit.',
			),
			'home.about.body_2' => array(
				'section' => 'home_about',
				'label'   => __( 'Rólunk – szöveg 2', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Munkánk során arra törekszünk, hogy a projekt műszaki követelményei, a szükséges dokumentációk és a kapcsolódó egyeztetések egy átlátható és követhető folyamat részeként legyenek kezelhetők.',
			),
			'home.about.cta' => array(
				'section' => 'home_about',
				'label'   => __( 'Rólunk – gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolat',
			),
			'home.about.seat' => array(
				'section' => 'home_about',
				'label'   => __( 'Rólunk – székhely érték', 'bma-ajanlatkeres' ),
				'type'    => 'html',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => true,
				'default' => 'Odorheiu Secuiesc,<br>Hargita, Románia',
			),
			'home.about.areas' => array(
				'section' => 'home_about',
				'label'   => __( 'Rólunk – munkaterületek', 'bma-ajanlatkeres' ),
				'type'    => 'html',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => true,
				'default' => 'Lakossági és üzleti<br>projektek',
			),
			'home.about.languages' => array(
				'section' => 'home_about',
				'label'   => __( 'Rólunk – nyelvek', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => '[MAGYAR / ROMÁN]',
			),
			'home.closing.eyebrow' => array(
				'section' => 'home_closing',
				'label'   => __( 'Záró – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Következő lépés',
			),
			'home.closing.title' => array(
				'section' => 'home_closing',
				'label'   => __( 'Záró – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kérjen ajánlatot a projektjéhez',
			),
			'home.closing.body' => array(
				'section' => 'home_closing',
				'label'   => __( 'Záró – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Adja meg a projekt alapadatait, válassza ki a szükséges terveket, és töltse fel, ami megvan. Ezek alapján pontos ajánlatot készítünk.',
			),
			'home.closing.cta_primary' => array(
				'section' => 'home_closing',
				'label'   => __( 'Záró – elsődleges gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatkérés indítása',
			),
			'home.closing.cta_secondary' => array(
				'section' => 'home_closing',
				'label'   => __( 'Záró – másodlagos gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolat',
			),
			'home.closing.tip' => array(
				'section' => 'home_closing',
				'label'   => __( 'Záró – tipp', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ha még nem tudja pontosan, mire van szüksége, hívjon minket.',
			),
			'home.contact.eyebrow' => array(
				'section' => 'home_contact',
				'label'   => __( 'Főoldal kapcsolat – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolat',
			),
			'home.contact.cta_primary' => array(
				'section' => 'home_contact',
				'label'   => __( 'Főoldal kapcsolat – gomb 1', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatkérés indítása',
			),
			'home.contact.cta_secondary' => array(
				'section' => 'home_contact',
				'label'   => __( 'Főoldal kapcsolat – gomb 2', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolat',
			),
			'footer.nav_heading' => array(
				'section' => 'footer',
				'label'   => __( 'Lábléc – navigáció cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-engineering',
				'html'    => false,
				'default' => 'Navigáció',
			),
			'footer.cta_heading' => array(
				'section' => 'footer',
				'label'   => __( 'Lábléc – ajánlatkérés cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-engineering',
				'html'    => false,
				'default' => 'Ajánlatkérés',
			),
			'footer.cta_blurb' => array(
				'section' => 'footer',
				'label'   => __( 'Lábléc – ajánlatkérés szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-engineering',
				'html'    => false,
				'default' => 'Válassza ki a szükséges szolgáltatásokat, és néhány adat megadásával kérjen személyre szabott ajánlatot.',
			),
			'footer.cta_button' => array(
				'section' => 'footer',
				'label'   => __( 'Lábléc – gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-engineering',
				'html'    => false,
				'default' => 'Ajánlatkérés indítása',
			),
			'services.eyebrow' => array(
				'section' => 'services_list',
				'label'   => __( 'Szolgáltatás lista – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatkérés',
			),
			'services.title' => array(
				'section' => 'services_list',
				'label'   => __( 'Szolgáltatás lista – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Szolgáltatások',
			),
			'services.lead' => array(
				'section' => 'services_list',
				'label'   => __( 'Szolgáltatás lista – bevezető', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Négy tervezési terület, egy ajánlatkérésben kombinálhatók. Nézze meg, mit tartalmaznak, vagy indítsa el az ajánlatkérést, és ott válassza ki, mire van szüksége.',
			),
			'services.side_title' => array(
				'section' => 'services_list',
				'label'   => __( 'Oldalsó kártya – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Hogyan működik?',
			),
			'services.side_1' => array(
				'section' => 'services_list',
				'label'   => __( 'Oldalsó kártya – pont 1', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Indítsa el az ajánlatkérést, és ott válassza ki, mire van szüksége.',
			),
			'services.side_2' => array(
				'section' => 'services_list',
				'label'   => __( 'Oldalsó kártya – pont 2', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Feltölti, ami megvan. Ami hiányzik, azt jelzi, mi beszerezzük.',
			),
			'services.side_3' => array(
				'section' => 'services_list',
				'label'   => __( 'Oldalsó kártya – pont 3', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Visszajelzés [X] munkanapon belül, kötelezettség nélkül.',
			),
			'services.side_note' => array(
				'section' => 'services_list',
				'label'   => __( 'Oldalsó kártya – megjegyzés', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ha nem tudja pontosan, mire van szüksége, elég a projektet leírnia – segítünk összeállítani.',
			),
			'refs.eyebrow' => array(
				'section' => 'refs_list',
				'label'   => __( 'Referencia lista – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Projektek',
			),
			'refs.title' => array(
				'section' => 'refs_list',
				'label'   => __( 'Referencia lista – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Referenciák',
			),
			'refs.lead' => array(
				'section' => 'refs_list',
				'label'   => __( 'Referencia lista – bevezető', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Válogatás megvalósult tervezési, engedélyeztetési, közmű-koordinációs és dokumentációs munkáinkból. Szűrjön projekttípus vagy szolgáltatás szerint.',
			),
			'refs.closing_eyebrow' => array(
				'section' => 'refs_list',
				'label'   => __( 'Referencia záró – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Következő lépés',
			),
			'refs.closing_title' => array(
				'section' => 'refs_list',
				'label'   => __( 'Referencia záró – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Hasonló projektje van?',
			),
			'contact.eyebrow' => array(
				'section' => 'contact',
				'label'   => __( 'Kapcsolat – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolat',
			),
			'contact.title' => array(
				'section' => 'contact',
				'label'   => __( 'Kapcsolat – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolat',
			),
			'contact.intro_1' => array(
				'section' => 'contact',
				'label'   => __( 'Kapcsolat – bevezető 1', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Van egy projektje, amelyhez tervezésre, engedélyeztetésre, műszaki dokumentációra vagy közmű-koordinációra van szüksége?',
			),
			'contact.intro_2' => array(
				'section' => 'contact',
				'label'   => __( 'Kapcsolat – bevezető 2', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Vegye fel velünk a kapcsolatot, vagy ha már tudja, milyen szolgáltatást keres, indítsa el az online ajánlatkérést.',
			),
			'contact.cta_quote' => array(
				'section' => 'contact',
				'label'   => __( 'Kapcsolat – ajánlat gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatkérés indítása',
			),
			'contact.details_eyebrow' => array(
				'section' => 'contact',
				'label'   => __( 'Elérhetőségek – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Elérhetőségeink',
			),
			'contact.form_eyebrow' => array(
				'section' => 'contact',
				'label'   => __( 'Űrlap – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Írjon nekünk',
			),
			'contact.form_title' => array(
				'section' => 'contact',
				'label'   => __( 'Űrlap – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Kapcsolatfelvételi űrlap',
			),
			'contact.form_hint' => array(
				'section' => 'contact',
				'label'   => __( 'Űrlap – súgó', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ha általános kérdése van, vagy még nem tudja pontosan, melyik szolgáltatásra van szüksége, küldjön üzenetet. Konkrét projektre az ajánlatkérést javasoljuk.',
			),
			'contact.quote_title' => array(
				'section' => 'contact',
				'label'   => __( 'CTA ajánlat – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ajánlatot szeretne kérni?',
			),
			'contact.quote_body' => array(
				'section' => 'contact',
				'label'   => __( 'CTA ajánlat – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ha már van konkrét projektje, a kapcsolatfelvételi űrlap helyett érdemes az ajánlatkérési folyamatot használni.',
			),
			'contact.quote_li_1' => array(
				'section' => 'contact',
				'label'   => __( 'CTA ajánlat – pont 1', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'kiválaszthatja a szükséges szolgáltatásokat;',
			),
			'contact.quote_li_2' => array(
				'section' => 'contact',
				'label'   => __( 'CTA ajánlat – pont 2', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'megjelölheti a konkrét részfeladatokat;',
			),
			'contact.quote_li_3' => array(
				'section' => 'contact',
				'label'   => __( 'CTA ajánlat – pont 3', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'megadhatja a projekt alapadatait;',
			),
			'contact.quote_li_4' => array(
				'section' => 'contact',
				'label'   => __( 'CTA ajánlat – pont 4', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'feltöltheti a rendelkezésre álló terveket és dokumentumokat;',
			),
			'contact.quote_li_5' => array(
				'section' => 'contact',
				'label'   => __( 'CTA ajánlat – pont 5', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'egy ajánlatkérésben több szolgáltatást is összeállíthat.',
			),
			'contact.portal_title' => array(
				'section' => 'contact',
				'label'   => __( 'CTA portál – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Meglévő ajánlatkérése van?',
			),
			'contact.portal_body' => array(
				'section' => 'contact',
				'label'   => __( 'CTA portál – szöveg', 'bma-ajanlatkeres' ),
				'type'    => 'textarea',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Ha korábban ügyfélfiókot hozott létre, bejelentkezés után megtekintheti saját ajánlatkéréseit, azok aktuális státuszát, valamint az Ön számára feltöltött ajánlatokat és dokumentumokat.',
			),
			'contact.portal_cta' => array(
				'section' => 'contact',
				'label'   => __( 'CTA portál – gomb', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Bejelentkezés az ügyfélportálra',
			),
			'contact.map_eyebrow' => array(
				'section' => 'contact',
				'label'   => __( 'Térkép – felirat', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Helyszín',
			),
			'contact.map_title' => array(
				'section' => 'contact',
				'label'   => __( 'Térkép – cím', 'bma-ajanlatkeres' ),
				'type'    => 'text',
				'domain'  => 'bma-ajanlatkeres',
				'html'    => false,
				'default' => 'Hol talál minket?',
			),
		);
	}

	/**
	 * Section → logical page key (null = site-wide option / settings).
	 *
	 * @return array<string, string|null>
	 */
	public static function section_pages(): array {
		return array(
			'home_hero'      => 'fooldal',
			'home_services'  => 'fooldal',
			'home_multi'     => 'fooldal',
			'home_benefits'  => 'fooldal',
			'home_process'   => 'fooldal',
			'home_refs'      => 'fooldal',
			'home_about'     => 'fooldal',
			'services_list'  => 'szolgaltatasok',
			'refs_list'      => 'referenciak',
			'contact'        => 'kapcsolat',
			'footer'         => null,
		);
	}

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'add_meta_boxes_page', array( self::class, 'register_meta_boxes' ) );
		add_action( 'save_post_page', array( self::class, 'save_page_meta' ), 10, 2 );
		add_action( 'admin_init', array( self::class, 'register_footer_setting' ) );
	}

	/**
	 * Footer texts live under Beállítások (no dedicated page).
	 */
	public static function register_footer_setting(): void {
		register_setting(
			'bma_settings',
			self::FOOTER_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize_footer' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Resolve WP page ID for a logical key + language.
	 */
	public static function page_id( string $page_key, string $lang = '' ): int {
		if ( '' === $lang ) {
			$lang = self::lang();
		}
		$map_i18n = get_option( 'bma_aj_page_ids_i18n', array() );
		if ( is_array( $map_i18n ) && ! empty( $map_i18n[ $lang ][ $page_key ] ) ) {
			return (int) $map_i18n[ $lang ][ $page_key ];
		}
		$map = get_option( 'bma_aj_page_ids', array() );
		$pid = ! empty( $map[ $page_key ] ) ? (int) $map[ $page_key ] : 0;
		if ( $pid && function_exists( 'pll_get_post' ) ) {
			$tr = pll_get_post( $pid, $lang );
			if ( $tr ) {
				return (int) $tr;
			}
		}
		return $pid;
	}

	/**
	 * Which logical page a post belongs to (or '').
	 */
	public static function page_key_for_post( int $post_id ): string {
		if ( $post_id <= 0 ) {
			return '';
		}
		foreach ( array( 'fooldal', 'szolgaltatasok', 'referenciak', 'kapcsolat' ) as $key ) {
			$map = get_option( 'bma_aj_page_ids', array() );
			$hu  = ! empty( $map[ $key ] ) ? (int) $map[ $key ] : 0;
			if ( $hu && $hu === $post_id ) {
				return $key;
			}
			if ( $hu && function_exists( 'pll_get_post_translations' ) ) {
				$trs = pll_get_post_translations( $hu );
				if ( is_array( $trs ) && in_array( $post_id, array_map( 'intval', $trs ), true ) ) {
					return $key;
				}
			}
			$map_i18n = get_option( 'bma_aj_page_ids_i18n', array() );
			if ( is_array( $map_i18n ) ) {
				foreach ( $map_i18n as $pages ) {
					if ( is_array( $pages ) && ! empty( $pages[ $key ] ) && (int) $pages[ $key ] === $post_id ) {
						return $key;
					}
				}
			}
		}
		return '';
	}

	/**
	 * Language of a page post (hu|ro|en).
	 */
	public static function lang_for_post( int $post_id ): string {
		if ( $post_id && function_exists( 'pll_get_post_language' ) ) {
			$lang = pll_get_post_language( $post_id, 'slug' );
			if ( in_array( $lang, array( 'hu', 'ro', 'en' ), true ) ) {
				return $lang;
			}
		}
		return self::lang();
	}

	/**
	 * Register page meta boxes.
	 *
	 * @param \WP_Post $post Page being edited.
	 */
	public static function register_meta_boxes( $post ): void {
		if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
			return;
		}
		if ( ! self::page_key_for_post( (int) $post->ID ) ) {
			return;
		}
		add_meta_box(
			'bma_page_content',
			__( 'Oldal szövegei', 'bma-ajanlatkeres' ),
			array( self::class, 'render_meta_box' ),
			'page',
			'normal',
			'high'
		);
	}

	/**
	 * Meta box UI.
	 *
	 * @param \WP_Post $post Page.
	 */
	public static function render_meta_box( $post ): void {
		$page_key = self::page_key_for_post( (int) $post->ID );
		if ( '' === $page_key ) {
			return;
		}
		wp_nonce_field( 'bma_page_content_save', 'bma_page_content_nonce' );
		include BMA_AJ_PLUGIN_DIR . 'templates/admin/page-content-metabox.php';
	}

	/**
	 * Persist page marketing fields.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post.
	 */
	public static function save_page_meta( int $post_id, $post ): void {
		if ( ! isset( $_POST['bma_page_content_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_page_content_nonce'] ) ), 'bma_page_content_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
		$page_key = self::page_key_for_post( $post_id );
		if ( '' === $page_key ) {
			return;
		}
		$raw = isset( $_POST['bma_page_content'] ) && is_array( $_POST['bma_page_content'] )
			? wp_unslash( $_POST['bma_page_content'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();
		$out = self::sanitize_field_map( $raw, $page_key );
		update_post_meta( $post_id, self::META_KEY, $out );
	}

	/**
	 * Sanitize a flat field map for one page (or footer).
	 *
	 * @param mixed  $input    Raw values.
	 * @param string $page_key Logical page key, or '' for footer-only.
	 * @return array<string, string>
	 */
	public static function sanitize_field_map( $input, string $page_key = '' ): array {
		$out    = array();
		$fields = self::fields();
		$pages  = self::section_pages();
		if ( ! is_array( $input ) ) {
			return $out;
		}
		foreach ( $fields as $key => $meta ) {
			$section   = (string) ( $meta['section'] ?? '' );
			$section_pg = array_key_exists( $section, $pages ) ? $pages[ $section ] : null;
			if ( '' === $page_key ) {
				if ( null !== $section_pg ) {
					continue;
				}
			} elseif ( $section_pg !== $page_key ) {
				continue;
			}
			if ( ! array_key_exists( $key, $input ) ) {
				continue;
			}
			$raw = $input[ $key ];
			if ( ! is_string( $raw ) ) {
				continue;
			}
			$raw = trim( $raw );
			if ( $raw === '' ) {
				continue;
			}
			$out[ $key ] = ! empty( $meta['html'] ) ? wp_kses_post( $raw ) : sanitize_textarea_field( $raw );
		}
		return $out;
	}

	/**
	 * Sanitize footer option (language-keyed).
	 *
	 * @param mixed $input Raw.
	 * @return array<string, array<string, string>>
	 */
	public static function sanitize_footer( $input ): array {
		$existing = get_option( self::FOOTER_OPTION, array() );
		$out      = is_array( $existing ) ? $existing : array();
		if ( ! is_array( $input ) ) {
			return $out;
		}
		foreach ( array( 'hu', 'ro', 'en' ) as $lang ) {
			if ( ! isset( $input[ $lang ] ) || ! is_array( $input[ $lang ] ) ) {
				continue;
			}
			$out[ $lang ] = self::sanitize_field_map( $input[ $lang ], '' );
		}
		return $out;
	}

	/**
	 * Current language slug (hu|ro|en).
	 */
	public static function lang(): string {
		if ( class_exists( 'BMA_AJ_I18n' ) ) {
			$lang = BMA_AJ_I18n::current_lang();
			if ( in_array( $lang, array( 'hu', 'ro', 'en' ), true ) ) {
				return $lang;
			}
		}
		return 'hu';
	}

	/**
	 * Default / translated string for a field in a language.
	 */
	public static function default_for( string $key, string $lang = '' ): string {
		$fields  = self::fields();
		$meta    = $fields[ $key ] ?? null;
		$default = (string) ( $meta['default'] ?? '' );
		$domain  = (string) ( $meta['domain'] ?? 'bma-ajanlatkeres' );
		if ( '' === $lang ) {
			$lang = self::lang();
		}
		if ( 'hu' === $lang || $default === '' ) {
			return $default;
		}
		return translate( $default, $domain ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain,WordPress.WP.I18n.LowLevelTranslationFunction
	}

	/**
	 * Stored map for a page post.
	 *
	 * @return array<string, string>
	 */
	public static function stored_for_post( int $post_id ): array {
		$raw = get_post_meta( $post_id, self::META_KEY, true );
		return is_array( $raw ) ? $raw : array();
	}

	/**
	 * Legacy option map (migration fallback).
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function legacy_stored(): array {
		$raw = get_option( self::OPTION, array() );
		return is_array( $raw ) ? $raw : array();
	}

	/**
	 * Value for editor UI: always filled (stored or default).
	 */
	public static function edit_value( string $key, int $post_id = 0 ): string {
		$lang = $post_id ? self::lang_for_post( $post_id ) : self::lang();
		$fields = self::fields();
		$section = (string) ( $fields[ $key ]['section'] ?? '' );
		$page_key = self::section_pages()[ $section ] ?? null;

		if ( null === $page_key ) {
			$footer = get_option( self::FOOTER_OPTION, array() );
			if ( is_array( $footer ) && ! empty( $footer[ $lang ][ $key ] ) && is_string( $footer[ $lang ][ $key ] ) ) {
				return $footer[ $lang ][ $key ];
			}
		} elseif ( $post_id ) {
			$stored = self::stored_for_post( $post_id );
			if ( ! empty( $stored[ $key ] ) && is_string( $stored[ $key ] ) ) {
				return $stored[ $key ];
			}
		}

		$legacy = self::legacy_stored();
		if ( ! empty( $legacy[ $lang ][ $key ] ) && is_string( $legacy[ $lang ][ $key ] ) ) {
			return $legacy[ $lang ][ $key ];
		}

		return self::default_for( $key, $lang );
	}

	/**
	 * Get editable string for current frontend language.
	 *
	 * @param string $key      Field key.
	 * @param string $fallback Optional override default.
	 */
	public static function get( string $key, string $fallback = '' ): string {
		$fields  = self::fields();
		$meta    = $fields[ $key ] ?? null;
		$lang    = self::lang();
		$section = (string) ( $meta['section'] ?? '' );
		$page_key = self::section_pages()[ $section ] ?? null;

		$value = '';

		if ( null === $page_key ) {
			$footer = get_option( self::FOOTER_OPTION, array() );
			if ( is_array( $footer ) && ! empty( $footer[ $lang ][ $key ] ) && is_string( $footer[ $lang ][ $key ] ) ) {
				$value = $footer[ $lang ][ $key ];
			} elseif ( 'hu' !== $lang && is_array( $footer ) && ! empty( $footer['hu'][ $key ] ) && is_string( $footer['hu'][ $key ] ) ) {
				$value = $footer['hu'][ $key ];
			}
		} else {
			$pid = self::page_id( $page_key, $lang );
			if ( $pid ) {
				$stored = self::stored_for_post( $pid );
				if ( ! empty( $stored[ $key ] ) && is_string( $stored[ $key ] ) ) {
					$value = $stored[ $key ];
				}
			}
			if ( $value === '' && 'hu' !== $lang ) {
				$hu_pid = self::page_id( $page_key, 'hu' );
				if ( $hu_pid ) {
					$stored = self::stored_for_post( $hu_pid );
					if ( ! empty( $stored[ $key ] ) && is_string( $stored[ $key ] ) ) {
						$value = $stored[ $key ];
					}
				}
			}
		}

		if ( $value === '' ) {
			$legacy = self::legacy_stored();
			if ( ! empty( $legacy[ $lang ][ $key ] ) && is_string( $legacy[ $lang ][ $key ] ) ) {
				$value = $legacy[ $lang ][ $key ];
			} elseif ( 'hu' !== $lang && ! empty( $legacy['hu'][ $key ] ) && is_string( $legacy['hu'][ $key ] ) ) {
				$value = $legacy['hu'][ $key ];
			}
		}

		if ( $value === '' ) {
			$value = $fallback !== '' ? $fallback : self::default_for( $key, $lang );
		}

		return BMA_AJ_Company::fill_response_days( $value );
	}

	/**
	 * Echo escaped text.
	 *
	 * @param string $key Field key.
	 */
	public static function e( string $key ): void {
		$fields = self::fields();
		$html   = ! empty( $fields[ $key ]['html'] );
		$value  = self::get( $key );
		if ( $html ) {
			echo wp_kses_post( $value );
			return;
		}
		echo esc_html( $value );
	}
}
