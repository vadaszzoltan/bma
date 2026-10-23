<?php
/**
 * SEO data v2 (keyword map of 2026-10-23): Rank Math title, description and
 * focus keyword for pages, services and references in HU/RO/EN, noindex for
 * utility pages and retired services, organization description.
 *
 * Unlike seo-fill-rankmath.php it does NOT touch blog_public, modules or
 * robots.txt, so it is safe to run on the password-protected live site.
 *
 * Run: php -r 'require "wp-load.php"; require "scripts/seo-fill-v2.php";'
 *
 * @package BMA
 */

defined( 'ABSPATH' ) || exit;

// ID => [ title, description, focus keywords (comma separated) ].
$bma_seo = array(
	// --- HU pages ---
	10  => array( 'Épületgépészeti és közműtervezés, Románia | BMA Engineering', 'Épületgépészeti, tűzvédelmi és közműtervezés engedélyeztetéssel, Székelyudvarhelyről egész Romániában. Kérjen ajánlatot online.', 'épületgépészeti és közműtervezés, gépészeti tervezés Székelyudvarhely, tűzvédelmi tervezés' ),
	11  => array( 'Gépész tervező iroda Romániában | Szolgáltatások | BMA', 'Épületgépészet, tűzvédelem, közműhálózatok és bekötések tervezése engedélyeztetéssel. Gépész tervező iroda Hargita megyében, egész Romániában.', 'gépész tervező iroda, közműtervezés, engedélyeztetés' ),
	14  => array( 'Tervezés árajánlat online | BMA Engineering', 'Kérjen árajánlatot épületgépészeti, tűzvédelmi vagy közműtervezésre néhány perc alatt. Romániai projektekhez, átlátható folyamattal.', 'tervezés árajánlat, gépészeti terv ára' ),
	12  => array( 'Gépészeti tervezés referenciák Romániában | BMA Engineering', 'Lakó-, irodai és ipari projektek Romániában: épületgépészeti és közműtervek, bekötések és engedélyeztetés. Nézze meg referenciáinkat.', 'gépészeti tervezés referenciák, ipari csarnok tervezés' ),
	13  => array( 'Kapcsolat | BMA Engineering Székelyudvarhely', 'BMA ENGINEERING S.R.L., Str. Raza Soarelui 11, Székelyudvarhely (Odorheiu Secuiesc), Hargita megye, Románia. Telefon: +40 728 205 982.', 'BMA Engineering Székelyudvarhely, tervező iroda Hargita megye' ),
	// --- RO pages ---
	98  => array( 'Proiectare instalații și utilități în România | BMA', 'Proiectare instalații, securitate la incendiu și rețele de utilități, cu avize și autorizații. Din Odorheiu Secuiesc, în toată România.', 'proiectare instalații și utilități, proiectare instalații Harghita, Odorheiu Secuiesc' ),
	100 => array( 'Firmă proiectare instalații în România | Servicii | BMA', 'Proiectare instalații, securitate la incendiu, rețele de utilități și branșamente, cu obținere avize. Birou de proiectare din Harghita.', 'firmă proiectare instalații, birou de proiectare, obținere avize' ),
	106 => array( 'Ofertă proiectare instalații online | BMA Engineering', 'Solicitați ofertă pentru proiectare instalații, securitate la incendiu sau utilități în câteva minute. Pentru proiecte din România.', 'ofertă proiectare instalații, preț proiect instalații' ),
	102 => array( 'Proiecte de instalații realizate | Referințe | BMA', 'Proiecte rezidențiale, de birouri și industriale din România: instalații, utilități, branșamente și autorizare. Vedeți referințele noastre.', 'proiecte instalații realizate, proiectare hală industrială' ),
	104 => array( 'Contact | BMA Engineering Odorheiu Secuiesc', 'BMA ENGINEERING S.R.L., Str. Raza Soarelui 11, Odorheiu Secuiesc, jud. Harghita, România. Telefon: +40 728 205 982.', 'BMA Engineering Odorheiu Secuiesc, birou proiectare Harghita' ),
	// --- EN pages ---
	99  => array( 'MEP and utility design in Romania | BMA Engineering', 'Building services, fire protection and utility network design with permits, from Odorheiu Secuiesc for projects across Romania. Request a quote.', 'MEP design Romania, building services and utility design' ),
	101 => array( 'MEP engineering firm in Romania | Services | BMA', 'Building services, fire protection, utility network and connection design, including permits. An MEP engineering firm serving all of Romania.', 'MEP engineering firm Romania, MEP engineering services' ),
	107 => array( 'Request an MEP design quote | BMA Engineering', 'Request a quote for building services, fire protection or utility design in a few minutes. For projects in Romania, with a transparent process.', 'MEP design quote, engineering quote Romania' ),
	103 => array( 'MEP design projects in Romania | References | BMA', 'Residential, office and industrial projects in Romania: building services, utilities, connections and permitting. See our references.', 'MEP design projects Romania, industrial building MEP design' ),
	105 => array( 'Contact | BMA Engineering, Romania', 'BMA ENGINEERING S.R.L., Str. Raza Soarelui 11, Odorheiu Secuiesc, Harghita County, Romania. Phone: +40 728 205 982.', 'BMA Engineering Romania, MEP engineer Transylvania' ),
	// --- Services ---
	169 => array( 'Épületgépészeti tervezés Romániában | BMA Engineering', 'Víz-csatorna, fűtés, szellőzés és klíma, villamos és belső gázhálózat tervezése engedélyeztetéssel, lakó- és ipari épületekhez Romániában.', 'épületgépészeti tervezés, víz-csatorna tervezés, fűtéstervezés' ),
	173 => array( 'Proiectare instalații în construcții în România | BMA', 'Proiectare instalații sanitare, termice, HVAC, electrice și gaze naturale, cu autorizare, pentru clădiri rezidențiale și industriale din România.', 'proiectare instalații în construcții, proiectare instalații sanitare, proiectare instalații termice' ),
	174 => array( 'Building services design in Romania | BMA Engineering', 'Plumbing, heating, HVAC, electrical and indoor gas design with permitting, for residential and industrial buildings in Romania.', 'building services design, plumbing design, HVAC design Romania' ),
	170 => array( 'Tűzvédelmi tervezés és ISU engedély | BMA Engineering', 'Sprinkler, tűzoltó vízellátás, hő- és füstelvezetés és tűzjelző rendszer tervezése az ISU tűzvédelmi engedélyig, romániai projektekhez.', 'tűzvédelmi tervezés, ISU engedély, sprinkler tervezés' ),
	175 => array( 'Proiectare securitate la incendiu și aviz ISU | BMA', 'Proiectare sprinklere, hidranți, desfumare și detecție incendiu, până la avizul și autorizația ISU, pentru proiecte din toată România.', 'proiectare securitate la incendiu, aviz ISU, proiectare sprinklere' ),
	176 => array( 'Fire protection design and ISU permit | BMA Engineering', 'Sprinkler, fire water supply, smoke and heat exhaust and fire alarm design, up to the ISU fire safety permit, for projects across Romania.', 'fire protection design Romania, ISU fire safety permit, sprinkler design' ),
	171 => array( 'Közműhálózat tervezés Romániában | BMA Engineering', 'Víz-, szennyvíz-, csapadékvíz- és tűzivízhálózatok, tisztítóművek és átemelők tervezése önkormányzatoknak és beruházóknak Romániában.', 'közműhálózat tervezés, vízhálózat tervezés, szennyvíz-csatorna tervezés' ),
	177 => array( 'Proiectare rețele de utilități în România | BMA Engineering', 'Rețele de apă, canalizare menajeră și pluvială, hidranți, stații de epurare și de pompare, pentru primării și investitori din România.', 'proiectare rețele de utilități, proiectare rețea alimentare cu apă, proiectare rețea de canalizare' ),
	178 => array( 'Utility network design in Romania | BMA Engineering', 'Water supply, sewer, stormwater and fire water networks, treatment plants and pumping stations for municipalities and investors in Romania.', 'utility network design, water supply network design, sewer network design' ),
	172 => array( 'Közműbekötés tervezése: víz, csatorna | BMA Engineering', 'Víz-, szennyvíz-, csapadékvíz- és tűzivízbekötés terve és a szolgáltatói engedélyek ügyintézése Hargita megyében és egész Romániában.', 'közműbekötés tervezése, vízbekötés terv, csatornabekötés' ),
	179 => array( 'Proiect branșament apă și canalizare | BMA Engineering', 'Proiect branșament apă, racord canalizare și ape pluviale, plus avizul de branșare de la operator. În Harghita și în toată România.', 'proiect branșament apă și canalizare, racord canalizare, aviz de branșare' ),
	180 => array( 'Utility connection design in Romania | BMA Engineering', 'Water, sewer, stormwater and fire water connection design, plus the utility operator approvals, in Harghita County and across Romania.', 'utility connection design Romania, water and sewer connection, utility coordination' ),
	// --- References (title comes from the post type template) ---
	52  => array( '', 'Családi ház bővítéséhez készült víz-, gáz- és szennyvíztervek engedélyeztetéssel – BMA Engineering referencia, Románia.', 'családi ház közműterv' ),
	130 => array( '', 'Planuri de apă, gaz și canalizare pentru extinderea unei case, cu autorizare – referință BMA Engineering, România.', 'proiect utilități extindere casă' ),
	131 => array( '', 'Water, gas and sewer plans for a house extension, with permitting – a BMA Engineering reference in Romania.', 'house extension utility design' ),
	53  => array( '', 'Irodaház felújításának műszaki és gépészeti dokumentációja – BMA Engineering referencia, Románia.', 'irodaház gépészeti felújítás' ),
	132 => array( '', 'Documentație tehnică și de instalații pentru renovarea unei clădiri de birouri – referință BMA Engineering, România.', 'proiect instalații renovare birouri' ),
	133 => array( '', 'Technical and building services documentation for an office renovation – a BMA Engineering reference in Romania.', 'office renovation MEP design' ),
	54  => array( '', 'Új lakóépület építési engedélyének előkészítése és a hatósági folyamat támogatása – BMA Engineering referencia, Románia.', 'új ház építési engedély' ),
	134 => array( '', 'Pregătirea autorizației de construire pentru o casă nouă și sprijin în relația cu autoritățile – referință BMA Engineering, România.', 'autorizație de construire casă nouă' ),
	135 => array( '', 'Building permit preparation for a new home and support with the authorities – a BMA Engineering reference in Romania.', 'new build permit Romania' ),
	55  => array( '', 'Ipari csarnok közműcsatlakozásainak tervezése és egyeztetése a szolgáltatókkal – BMA Engineering referencia, Románia.', 'ipari csarnok közmű' ),
	136 => array( '', 'Proiectarea și coordonarea branșamentelor de utilități pentru o hală industrială – referință BMA Engineering, România.', 'utilități hală industrială' ),
	137 => array( '', 'Utility connection design and operator coordination for an industrial hall – a BMA Engineering reference in Romania.', 'industrial hall utilities Romania' ),
);

$bma_index_robots   = array( 'index', 'follow', 'max-snippet:-1', 'max-video-preview:-1', 'max-image-preview:large' );
$bma_noindex_robots = array( 'noindex', 'follow' );

foreach ( $bma_seo as $post_id => $seo ) {
	if ( ! get_post( $post_id ) ) {
		echo "skip {$post_id} (missing)\n";
		continue;
	}
	if ( '' !== $seo[0] ) {
		update_post_meta( $post_id, 'rank_math_title', $seo[0] );
		update_post_meta( $post_id, 'rank_math_facebook_title', $seo[0] );
	}
	update_post_meta( $post_id, 'rank_math_description', $seo[1] );
	update_post_meta( $post_id, 'rank_math_facebook_description', $seo[1] );
	update_post_meta( $post_id, 'rank_math_focus_keyword', $seo[2] );
	update_post_meta( $post_id, 'rank_math_robots', $bma_index_robots );
	echo "ok {$post_id}\n";
}

// Utility pages (cart, portal, login, payment in 3 languages) and the retired
// v1 services: keep out of search and the sitemap.
$bma_noindex = array( 15, 16, 19, 47, 108, 109, 110, 111, 116, 117, 118, 119, 48, 49, 50, 51, 122, 123, 124, 125, 126, 127, 128, 129 );
foreach ( $bma_noindex as $post_id ) {
	if ( get_post( $post_id ) ) {
		update_post_meta( $post_id, 'rank_math_robots', $bma_noindex_robots );
		delete_post_meta( $post_id, 'rank_math_focus_keyword' );
	}
}

$bma_sitemap = get_option( 'rank-math-options-sitemap', array() );
if ( is_array( $bma_sitemap ) ) {
	$bma_sitemap['exclude_posts'] = implode( ', ', $bma_noindex );
	update_option( 'rank-math-options-sitemap', $bma_sitemap );
}

// Homepage + organization (Rank Math options; the static front page also uses post meta above).
$bma_titles = get_option( 'rank-math-options-titles', array() );
if ( is_array( $bma_titles ) ) {
	$bma_titles['homepage_title']                = $bma_seo[10][0];
	$bma_titles['homepage_description']          = $bma_seo[10][1];
	$bma_titles['homepage_facebook_title']       = $bma_seo[10][0];
	$bma_titles['homepage_facebook_description'] = $bma_seo[10][1];
	$bma_titles['organization_description']      = 'A BMA ENGINEERING S.R.L. romániai mérnöki iroda (Székelyudvarhely, Hargita megye): épületgépészeti, tűzvédelmi és közműhálózat-tervezés, közműbekötések és engedélyeztetés egész Romániában.';
	update_option( 'rank-math-options-titles', $bma_titles );
}

// Sample "Hello world" post.
if ( get_post( 1 ) && 'hello-vilag' === get_post_field( 'post_name', 1 ) ) {
	wp_delete_post( 1, true );
	echo "deleted sample post 1\n";
}

echo "done\n";
