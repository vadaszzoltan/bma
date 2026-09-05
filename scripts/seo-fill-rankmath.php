<?php
/**
 * Fill Rank Math SEO settings and per-page meta for BMA Engineering.
 *
 * Run: wp eval-file scripts/seo-fill-rankmath.php
 *
 * @package BMA
 */

defined( 'ABSPATH' ) || exit;

$home = home_url( '/' );

$org_description = 'A BMA Engineering mérnöki tervezést, engedélyeztetést, műszaki dokumentációt és közműcsatlakozás-koordinációt nyújt lakossági és üzleti projektekhez Odorheiu Secuiesc / Székelyudvarhely térségében és Romániában.';

$blog_tagline = 'Mérnöki tervezés, engedélyeztetés és műszaki dokumentáció';

update_option( 'blogdescription', $blog_tagline );
update_option( 'blog_public', '1' );

// Enable relevant Rank Math modules.
$modules = array(
	'link-counter',
	'analytics',
	'seo-analysis',
	'sitemap',
	'rich-snippet',
	'local-seo',
	'robots-txt',
	'redirections',
	'404-monitor',
	'instant-indexing',
);
update_option( 'rank_math_modules', $modules );
update_option( 'rank_math_wizard_completed', true );
update_option( 'rank_math_registration_skip', true );

// --- Titles / Local SEO / Organization ---
$titles = get_option( 'rank-math-options-titles', array() );
if ( ! is_array( $titles ) ) {
	$titles = array();
}

$titles = array_merge(
	$titles,
	array(
		'title_separator'           => '|',
		'twitter_card_type'         => 'summary_large_image',
		'knowledgegraph_type'       => 'company',
		'knowledgegraph_name'       => 'BMA ENGINEERING S.R.L.',
		'website_name'              => 'BMA ENGINEERING',
		'website_alternate_name'    => 'BMA Engineering',
		'organization_description'  => $org_description,
		'url'                       => $home,
		'email'                     => 'office@bmaeng.ro',
		'local_business_type'       => 'ProfessionalService',
		'local_address'             => array(
			'streetAddress'   => 'Str. Raza Soarelui 11',
			'addressLocality' => 'Odorheiu Secuiesc',
			'addressRegion'   => 'Harghita',
			'postalCode'      => '535600',
			'addressCountry'  => 'RO',
		),
		'local_address_format'      => '{address}, {postalcode} {locality}, {region}, {country}',
		'geo'                       => '46.3047462,25.2950261',
		'phone_numbers'             => array(
			array(
				'type'   => 'customer support',
				'number' => '+40-728-205-982',
			),
		),
		'opening_hours_format'      => 'off',
		'opening_hours'             => array(
			array( 'day' => 'Monday', 'time' => '08:00-17:00' ),
			array( 'day' => 'Tuesday', 'time' => '08:00-17:00' ),
			array( 'day' => 'Wednesday', 'time' => '08:00-17:00' ),
			array( 'day' => 'Thursday', 'time' => '08:00-17:00' ),
			array( 'day' => 'Friday', 'time' => '08:00-17:00' ),
		),
		'additional_info'           => array(
			array(
				'type'  => 'legalName',
				'value' => 'BMA ENGINEERING S.R.L.',
			),
			array(
				'type'  => 'taxID',
				'value' => 'RO40593519',
			),
			array(
				'type'  => 'vatID',
				'value' => 'RO40593519',
			),
		),
		'local_seo_about_page'      => 10, // Főoldal (about content on home).
		'local_seo_contact_page'    => 13,
		'homepage_title'            => 'BMA Engineering | Mérnöki tervezés és engedélyeztetés',
		'homepage_description'      => 'Mérnöki tervezés, engedélyeztetés, műszaki dokumentáció és közmű koordináció. Kérjen ajánlatot online – BMA Engineering, Odorheiu Secuiesc.',
		'homepage_facebook_title'   => 'BMA Engineering | Mérnöki tervezés és engedélyeztetés',
		'homepage_facebook_description' => 'Professzionális mérnöki szolgáltatások lakossági és üzleti projektekhez. Tervezés, engedélyeztetés, dokumentáció – egy helyen.',
		'homepage_custom_robots'    => 'off',
		'disable_author_archives'   => 'on',
		'disable_date_archives'     => 'on',
		'noindex_search'            => 'on',
		'404_title'                 => 'Az oldal nem található %sep% %sitename%',
		'search_title'              => 'Keresés: %search_query% %sep% %sitename%',
		'pt_page_title'             => '%title% %sep% %sitename%',
		'pt_page_description'       => '%excerpt%',
		'pt_page_default_rich_snippet' => 'off',
		'pt_bma_service_title'      => '%title% %sep% Mérnöki szolgáltatás %sep% %sitename%',
		'pt_bma_service_description'=> '%excerpt%',
		'pt_bma_service_default_rich_snippet' => 'service',
		'pt_bma_service_default_snippet_name' => '%title%',
		'pt_bma_service_default_snippet_desc' => '%excerpt%',
		'pt_bma_service_archive_title' => 'Szolgáltatások %page% %sep% %sitename%',
		'pt_bma_reference_title'    => '%title% %sep% Referencia %sep% %sitename%',
		'pt_bma_reference_description' => '%excerpt%',
		'pt_bma_reference_default_rich_snippet' => 'off',
		'pt_bma_reference_archive_title' => 'Referenciák %page% %sep% %sitename%',
		'pt_attachment_custom_robots' => 'on',
		'pt_attachment_robots'      => array( 'noindex' ),
		'tax_post_tag_custom_robots' => 'on',
		'tax_post_tag_robots'       => array( 'noindex' ),
	)
);

update_option( 'rank-math-options-titles', $titles );

// --- General / robots.txt ---
$general = get_option( 'rank-math-options-general', array() );
if ( ! is_array( $general ) ) {
	$general = array();
}

$robots_txt = implode(
	"\n",
	array(
		'User-agent: *',
		'Disallow: /wp-admin/',
		'Allow: /wp-admin/admin-ajax.php',
		'Disallow: /ugyfelportal/',
		'Disallow: /ajanlatkosar/',
		'Disallow: /belepes/',
		'Disallow: /fizetes/',
		'Disallow: /bma-download/',
		'',
		'Sitemap: ' . home_url( '/sitemap_index.xml' ),
		'',
	)
);

$general = array_merge(
	$general,
	array(
		'robots_txt_content'       => $robots_txt,
		'breadcrumbs'              => 'on',
		'breadcrumbs_separator'    => '›',
		'breadcrumbs_home'         => 'on',
		'breadcrumbs_home_label'   => 'Főoldal',
		'breadcrumbs_archive_format' => 'Archívum: %s',
		'breadcrumbs_search_format'=> 'Keresés: %s',
		'breadcrumbs_404_label'    => 'Az oldal nem található',
		'nofollow_external_links'  => 'off',
		'new_window_external_links'=> 'on',
		'add_img_alt'              => 'on',
		'img_alt_format'           => '%title% %filename%',
		'content_ai_language'      => 'Hungarian',
		'content_ai_tone'          => 'Formal',
		'content_ai_audience'      => 'Business Audience',
		'setup_mode'               => 'advanced',
	)
);

update_option( 'rank-math-options-general', $general );

// --- Sitemap ---
$sitemap = get_option( 'rank-math-options-sitemap', array() );
if ( ! is_array( $sitemap ) ) {
	$sitemap = array();
}

// Private / utility pages: exclude from sitemap.
$exclude_ids = array( 15, 16, 19, 47, 2 ); // kosár, portál, belépés, fizetés, minta oldal

$sitemap = array_merge(
	$sitemap,
	array(
		'items_per_page'        => 200,
		'include_images'        => 'on',
		'authors_sitemap'       => 'off',
		'pt_post_sitemap'       => 'off',
		'pt_page_sitemap'       => 'on',
		'pt_attachment_sitemap' => 'off',
		'pt_bma_service_sitemap'=> 'on',
		'pt_bma_reference_sitemap' => 'on',
		'tax_category_sitemap'  => 'off',
		'tax_post_tag_sitemap'  => 'off',
		'tax_bma_service_category_sitemap' => 'on',
		'tax_bma_reference_category_sitemap' => 'on',
		'exclude_posts'         => implode( ', ', $exclude_ids ),
		'html_sitemap'          => 'off',
	)
);

update_option( 'rank-math-options-sitemap', $sitemap );

// Homepage / organization social image from seeded hero.
$hero_id = absint( get_option( 'bma_hero_image_id', 0 ) );
if ( $hero_id ) {
	$hero_url = wp_get_attachment_image_url( $hero_id, 'full' );
	if ( $hero_url ) {
		$titles = get_option( 'rank-math-options-titles', array() );
		$titles['homepage_facebook_image']    = $hero_url;
		$titles['homepage_facebook_image_id'] = $hero_id;
		$titles['knowledgegraph_logo']        = $hero_url;
		$titles['knowledgegraph_logo_id']     = $hero_id;
		update_option( 'rank-math-options-titles', $titles );
		update_post_meta( 10, 'rank_math_facebook_image', $hero_url );
		update_post_meta( 10, 'rank_math_facebook_image_id', $hero_id );
	}
}

/**
 * Apply Rank Math meta to a post.
 *
 * @param int                  $post_id Post ID.
 * @param array<string, mixed> $meta    Meta map.
 */
function bma_seo_apply_meta( int $post_id, array $meta ): void {
	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}
}

$index_robots   = array( 'index', 'follow', 'max-snippet:-1', 'max-video-preview:-1', 'max-image-preview:large' );
$noindex_robots = array( 'noindex', 'nofollow' );

// --- Pages ---
$page_seo = array(
	10 => array( // Főoldal – also covered by homepage_* settings; keep post meta for editor.
		'title'       => 'BMA Engineering | Mérnöki tervezés és engedélyeztetés',
		'description' => 'Mérnöki tervezés, engedélyeztetés, műszaki dokumentáció és közmű koordináció. Kérjen ajánlatot online – BMA Engineering, Odorheiu Secuiesc.',
		'focus'       => 'mérnöki tervezés, engedélyeztetés',
		'robots'      => $index_robots,
		'schema'      => 'off',
		'excerpt'     => 'A BMA Engineering mérnöki tervezést, engedélyeztetést és műszaki dokumentációt nyújt lakossági és üzleti projektekhez.',
	),
	11 => array(
		'title'       => 'Szolgáltatások | Tervezés, engedélyeztetés, dokumentáció',
		'description' => 'Ismerje meg a BMA Engineering szolgáltatásait: tervezés, engedélyeztetés, műszaki dokumentáció és közmű csatlakozás koordináció.',
		'focus'       => 'mérnöki szolgáltatások',
		'robots'      => $index_robots,
		'schema'      => 'off',
		'excerpt'     => 'Tervezés, engedélyeztetés, műszaki dokumentáció és közmű koordináció egy mérnöki csapattal.',
	),
	12 => array(
		'title'       => 'Referenciák | Elkészült mérnöki projektek',
		'description' => 'Válogatás a BMA Engineering referencia projektjeiből: lakó-, irodai és ipari megbízások tervezése és engedélyeztetése.',
		'focus'       => 'mérnöki referenciák',
		'robots'      => $index_robots,
		'schema'      => 'off',
		'excerpt'     => 'Referencia projektek lakóingatlanoktól az ipari beruházásokig.',
	),
	13 => array(
		'title'       => 'Kapcsolat | BMA Engineering Odorheiu Secuiesc',
		'description' => 'Lépjen kapcsolatba a BMA Engineering csapatával. Cím: Str. Raza Soarelui 11, Odorheiu Secuiesc. Tel: +40 728 205 982.',
		'focus'       => 'BMA Engineering kapcsolat',
		'robots'      => $index_robots,
		'schema'      => 'off',
		'excerpt'     => 'Kapcsolatfelvétel: Odorheiu Secuiesc, Str. Raza Soarelui 11 – office@bmaeng.ro',
	),
	14 => array(
		'title'       => 'Ajánlatkérés | Online mérnöki ajánlat',
		'description' => 'Kérjen személyre szabott mérnöki ajánlatot tervezésre, engedélyeztetésre vagy műszaki dokumentációra. Gyors, átlátható folyamat.',
		'focus'       => 'mérnöki ajánlatkérés',
		'robots'      => $index_robots,
		'schema'      => 'off',
		'excerpt'     => 'Online ajánlatkérés mérnöki tervezéshez, engedélyeztetéshez és dokumentációhoz.',
	),
	17 => array(
		'title'       => 'Adatvédelmi tájékoztató | BMA Engineering',
		'description' => 'A BMA Engineering adatkezelési és adatvédelmi tájékoztatója az ügyfelek és weboldal-látogatók számára.',
		'focus'       => 'adatvédelem',
		'robots'      => $index_robots,
		'schema'      => 'off',
		'excerpt'     => 'Adatvédelmi tájékoztató a BMA Engineering ügyfelei és weboldal-látogatói számára.',
	),
	18 => array(
		'title'       => 'Cookie tájékoztató | BMA Engineering',
		'description' => 'Tájékoztató a BMA Engineering weboldalán használt sütikről (cookie-król) és a beállítási lehetőségekről.',
		'focus'       => 'cookie tájékoztató',
		'robots'      => $index_robots,
		'schema'      => 'off',
		'excerpt'     => 'Cookie (süti) tájékoztató a BMA Engineering weboldalához.',
	),
	71 => array(
		'title'       => 'Általános felhasználási feltételek | BMA Engineering',
		'description' => 'A BMA Engineering weboldalának és szolgáltatásainak általános felhasználási feltételei.',
		'focus'       => 'általános feltételek',
		'robots'      => $index_robots,
		'schema'      => 'off',
		'excerpt'     => 'Általános felhasználási feltételek a BMA Engineering szolgáltatásaihoz.',
	),
	// Utility / private.
	15 => array(
		'title'       => 'Ajánlatkérési kosár',
		'description' => 'Az Ön kiválasztott szolgáltatásai az ajánlatkéréshez.',
		'focus'       => '',
		'robots'      => $noindex_robots,
		'schema'      => 'off',
		'excerpt'     => '',
	),
	16 => array(
		'title'       => 'Ügyfélportál',
		'description' => 'Bejelentkezett ügyfelek számára elérhető portál.',
		'focus'       => '',
		'robots'      => $noindex_robots,
		'schema'      => 'off',
		'excerpt'     => '',
	),
	19 => array(
		'title'       => 'Bejelentkezés',
		'description' => 'Ügyfélportál bejelentkezés.',
		'focus'       => '',
		'robots'      => $noindex_robots,
		'schema'      => 'off',
		'excerpt'     => '',
	),
	47 => array(
		'title'       => 'Fizetés',
		'description' => 'Ajánlat fizetési oldal.',
		'focus'       => '',
		'robots'      => $noindex_robots,
		'schema'      => 'off',
		'excerpt'     => '',
	),
	2  => array(
		'title'       => 'Minta oldal',
		'description' => '',
		'focus'       => '',
		'robots'      => $noindex_robots,
		'schema'      => 'off',
		'excerpt'     => '',
	),
);

foreach ( $page_seo as $id => $data ) {
	if ( ! get_post( $id ) ) {
		continue;
	}
	bma_seo_apply_meta(
		$id,
		array(
			'rank_math_title'                 => $data['title'],
			'rank_math_description'           => $data['description'],
			'rank_math_focus_keyword'         => $data['focus'],
			'rank_math_robots'                => $data['robots'],
			'rank_math_advanced_robots'       => array(
				'max-snippet'       => '-1',
				'max-video-preview' => '-1',
				'max-image-preview' => 'large',
			),
			'rank_math_facebook_title'        => $data['title'],
			'rank_math_facebook_description'  => $data['description'],
			'rank_math_twitter_use_facebook'  => 'on',
			'rank_math_rich_snippet'          => $data['schema'],
		)
	);
	if ( ! empty( $data['excerpt'] ) ) {
		wp_update_post(
			array(
				'ID'           => $id,
				'post_excerpt' => $data['excerpt'],
			)
		);
	}
}

// --- Services ---
$service_seo = array(
	48 => array(
		'title'       => 'Tervezés | Közmű- és épületgépészeti tervek',
		'description' => 'Közmű- és épületgépészeti tervezés lakossági és üzleti projektekhez. Elektromos, gáz- és vízvezeték tervezés a BMA Engineeringtől.',
		'focus'       => 'közműtervezés, épületgépészeti tervezés',
	),
	49 => array(
		'title'       => 'Engedélyeztetés | Építési és használatbavételi engedély',
		'description' => 'Építési és használatbavételi engedélyek előkészítése, hatósági egyeztetések és hiánypótlások támogatása.',
		'focus'       => 'építési engedélyeztetés',
	),
	50 => array(
		'title'       => 'Műszaki dokumentáció | Leírások és átadási anyagok',
		'description' => 'Műszaki leírások, jegyzőkönyvek és átadási dokumentáció készítése hatósági és szerződéses követelmények szerint.',
		'focus'       => 'műszaki dokumentáció',
	),
	51 => array(
		'title'       => 'Közmű csatlakozás koordináció | Víz, gáz, áram',
		'description' => 'Szolgáltatói egyeztetések, csatlakozási igények és határidők koordinálása – víz, gáz és elektromos hálózat.',
		'focus'       => 'közmű csatlakozás koordináció',
	),
);

foreach ( $service_seo as $id => $data ) {
	if ( ! get_post( $id ) ) {
		continue;
	}
	$excerpt = get_the_excerpt( $id );
	bma_seo_apply_meta(
		$id,
		array(
			'rank_math_title'                => $data['title'],
			'rank_math_description'          => $data['description'],
			'rank_math_focus_keyword'        => $data['focus'],
			'rank_math_robots'               => $index_robots,
			'rank_math_facebook_title'       => $data['title'],
			'rank_math_facebook_description' => $data['description'],
			'rank_math_twitter_use_facebook' => 'on',
			'rank_math_rich_snippet'         => 'service',
			'rank_math_snippet_service_type'=> 'ProfessionalService',
			'rank_math_snippet_name'         => get_the_title( $id ),
			'rank_math_snippet_desc'         => $data['description'],
		)
	);
}

// --- References ---
$reference_seo = array(
	52 => array(
		'title'       => 'Családi ház bővítés – közműtervek | Referencia',
		'description' => 'Lakóingatlan bővítéséhez készített víz-, gáz- és szennyvíztervek engedélyeztetési támogatással – BMA Engineering referencia.',
		'focus'       => 'családi ház közműtervek',
	),
	53 => array(
		'title'       => 'Irodaház felújítás – műszaki dokumentáció | Referencia',
		'description' => 'Üzleti ingatlan felújításához kapcsolódó műszaki leírások és átadási dokumentáció – BMA Engineering referencia.',
		'focus'       => 'irodaház műszaki dokumentáció',
	),
	54 => array(
		'title'       => 'Új építés – engedélyeztetési csomag | Referencia',
		'description' => 'Új lakóépület építési engedélyének előkészítése és hatósági folyamat támogatása – BMA Engineering referencia.',
		'focus'       => 'építési engedélyeztetési csomag',
	),
	55 => array(
		'title'       => 'Ipari csarnok – közmű koordináció | Referencia',
		'description' => 'Ipari beruházás közműcsatlakozásainak egyeztetése és dokumentálása – BMA Engineering referencia.',
		'focus'       => 'ipari közmű koordináció',
	),
);

foreach ( $reference_seo as $id => $data ) {
	if ( ! get_post( $id ) ) {
		continue;
	}
	bma_seo_apply_meta(
		$id,
		array(
			'rank_math_title'                => $data['title'],
			'rank_math_description'          => $data['description'],
			'rank_math_focus_keyword'        => $data['focus'],
			'rank_math_robots'               => $index_robots,
			'rank_math_facebook_title'       => $data['title'],
			'rank_math_facebook_description' => $data['description'],
			'rank_math_twitter_use_facebook' => 'on',
			'rank_math_rich_snippet'         => 'off',
		)
	);
}

// Soft-hide sample page from public navigation (keep noindex).
$sample = get_post( 2 );
if ( $sample && 'publish' === $sample->post_status ) {
	wp_update_post(
		array(
			'ID'          => 2,
			'post_status' => 'draft',
		)
	);
}

// Clear Rank Math sitemap cache.
delete_option( 'rank_math_sitemap_cache_files' );
if ( class_exists( '\RankMath\Sitemap\Cache' ) ) {
	\RankMath\Sitemap\Cache::invalidate_storage();
}

flush_rewrite_rules( false );

WP_CLI::success( 'Rank Math SEO tartalom kitöltve (organization, oldalak, szolgáltatások, referenciák, robots, sitemap).' );
