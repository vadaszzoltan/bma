<?php
/**
 * Export all BMA textual content for Polylang processing.
 *
 * @package BMA
 */

defined( 'ABSPATH' ) || exit;

global $wpdb;

$export = array(
	'meta'             => array(
		'site'          => get_bloginfo( 'name' ),
		'tagline'       => get_bloginfo( 'description' ),
		'exported_at'   => gmdate( 'c' ),
		'source_locale' => 'hu',
		'target_locales'=> array( 'ro', 'en' ),
	),
	'pages'            => array(),
	'services'         => array(),
	'references'       => array(),
	'taxonomies'       => array(),
	'options'          => array(),
	'menus'            => array(),
	'service_items'    => array(),
	'statuses'         => array(),
	'email_templates'  => array(),
	'dynamic_fields'   => array(),
	'seo'              => array(),
	'ui_strings_note'  => 'Plugin UI strings (__ / esc_html__) are translated via .po/.mo in languages/; Polylang handles posts/taxonomies/menus.',
);

foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft' ), 'numberposts' => -1 ) ) as $p ) {
	$export['pages'][] = array(
		'id'              => $p->ID,
		'slug'            => $p->post_name,
		'status'          => $p->post_status,
		'title'           => $p->post_title,
		'excerpt'         => $p->post_excerpt,
		'content'         => $p->post_content,
		'seo_title'       => (string) get_post_meta( $p->ID, 'rank_math_title', true ),
		'seo_description' => (string) get_post_meta( $p->ID, 'rank_math_description', true ),
		'focus_keyword'   => (string) get_post_meta( $p->ID, 'rank_math_focus_keyword', true ),
	);
}

foreach ( array( 'bma_service' => 'services', 'bma_reference' => 'references' ) as $pt => $key ) {
	foreach ( get_posts( array( 'post_type' => $pt, 'post_status' => 'publish', 'numberposts' => -1 ) ) as $p ) {
		$tax   = 'bma_service' === $pt ? 'bma_service_category' : 'bma_reference_category';
		$terms = wp_get_post_terms( $p->ID, $tax, array( 'fields' => 'all' ) );
		$term_data = array();
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$term_data[] = array(
					'id'   => (int) $t->term_id,
					'slug' => $t->slug,
					'name' => $t->name,
				);
			}
		}
		$export[ $key ][] = array(
			'id'              => $p->ID,
			'slug'            => $p->post_name,
			'title'           => $p->post_title,
			'excerpt'         => $p->post_excerpt,
			'content'         => $p->post_content,
			'terms'           => $term_data,
			'seo_title'       => (string) get_post_meta( $p->ID, 'rank_math_title', true ),
			'seo_description' => (string) get_post_meta( $p->ID, 'rank_math_description', true ),
			'focus_keyword'   => (string) get_post_meta( $p->ID, 'rank_math_focus_keyword', true ),
		);
	}
}

foreach ( array( 'bma_service_category', 'bma_reference_category' ) as $tax ) {
	$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		continue;
	}
	foreach ( $terms as $t ) {
		$export['taxonomies'][] = array(
			'taxonomy'    => $tax,
			'id'          => (int) $t->term_id,
			'slug'        => $t->slug,
			'name'        => $t->name,
			'description' => $t->description,
		);
	}
}

$opt_keys = array(
	'blogname',
	'blogdescription',
	'bma_company_name',
	'bma_contact_address',
	'bma_contact_phone',
	'bma_contact_email',
	'bma_opening_hours',
	'bma_smtp_from_name',
	'bma_bank_beneficiary',
);
foreach ( $opt_keys as $k ) {
	$export['options'][ $k ] = (string) get_option( $k, '' );
}

$titles = get_option( 'rank-math-options-titles', array() );
$export['seo'] = array(
	'homepage_title'           => (string) ( $titles['homepage_title'] ?? '' ),
	'homepage_description'     => (string) ( $titles['homepage_description'] ?? '' ),
	'organization_description' => (string) ( $titles['organization_description'] ?? '' ),
	'knowledgegraph_name'      => (string) ( $titles['knowledgegraph_name'] ?? '' ),
	'website_name'             => (string) ( $titles['website_name'] ?? '' ),
	'website_alternate_name'   => (string) ( $titles['website_alternate_name'] ?? '' ),
	'breadcrumbs_home_label'   => (string) ( get_option( 'rank-math-options-general', array() )['breadcrumbs_home_label'] ?? '' ),
);

$menus = wp_get_nav_menus();
foreach ( $menus as $menu ) {
	$items             = wp_get_nav_menu_items( $menu->term_id );
	$export['menus'][] = array(
		'id'    => (int) $menu->term_id,
		'name'  => $menu->name,
		'slug'  => $menu->slug,
		'items' => array_map(
			static function ( $i ) {
				return array(
					'id'        => (int) $i->ID,
					'title'     => $i->title,
					'url'       => $i->url,
					'object'    => $i->object,
					'object_id' => (int) $i->object_id,
				);
			},
			$items ?: array()
		),
	);
}

$table = $wpdb->prefix . 'bma_service_items';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$export['service_items'] = $wpdb->get_results( "SELECT id, name, description, slug FROM {$table} ORDER BY id", ARRAY_A ) ?: array();
}

$st = $wpdb->prefix . 'bma_statuses';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $st ) ) === $st ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$export['statuses'] = $wpdb->get_results( "SELECT id, name, slug FROM {$st} ORDER BY sort_order, id", ARRAY_A ) ?: array();
}

$et = $wpdb->prefix . 'bma_email_templates';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $et ) ) === $et ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$export['email_templates'] = $wpdb->get_results( "SELECT id, slug, subject, body FROM {$et} ORDER BY id", ARRAY_A ) ?: array();
}

$df = $wpdb->prefix . 'bma_dynamic_fields';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $df ) ) === $df ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$export['dynamic_fields'] = $wpdb->get_results( "SELECT id, field_key, label, placeholder, help_text, options_json FROM {$df} ORDER BY id", ARRAY_A ) ?: array();
}

$dir = WP_CONTENT_DIR . '/bma-i18n-export';
if ( ! is_dir( $dir ) ) {
	wp_mkdir_p( $dir );
}

$json_path = $dir . '/content-hu.json';
file_put_contents( $json_path, wp_json_encode( $export, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );

// Human-readable Markdown export.
$md   = array();
$md[] = '# BMA Engineering – szöveges tartalom export (HU)';
$md[] = '';
$md[] = '- Export: `' . gmdate( 'Y-m-d H:i:s' ) . ' UTC`';
$md[] = '- Forrásnyelv: `hu`';
$md[] = '- Célnyelvek: `ro`, `en`';
$md[] = '- Gépi formátum: `content-hu.json`';
$md[] = '';

$md[] = '## Oldalak';
foreach ( $export['pages'] as $p ) {
	if ( 'draft' === $p['status'] ) {
		continue;
	}
	$md[] = '';
	$md[] = '### ' . $p['title'] . ' (`' . $p['slug'] . '`, ID ' . $p['id'] . ')';
	$md[] = '- Excerpt: ' . ( $p['excerpt'] ?: '—' );
	$md[] = '- SEO title: ' . ( $p['seo_title'] ?: '—' );
	$md[] = '- SEO description: ' . ( $p['seo_description'] ?: '—' );
	$md[] = '- Content: `' . trim( preg_replace( '/\s+/', ' ', $p['content'] ) ) . '`';
}

$md[] = '';
$md[] = '## Szolgáltatások';
foreach ( $export['services'] as $p ) {
	$md[] = '';
	$md[] = '### ' . $p['title'] . ' (`' . $p['slug'] . '`)';
	$md[] = $p['excerpt'];
	$md[] = '';
	$md[] = $p['content'];
	$md[] = '';
	$md[] = '- SEO: ' . $p['seo_title'] . ' / ' . $p['seo_description'];
}

$md[] = '';
$md[] = '## Referenciák';
foreach ( $export['references'] as $p ) {
	$md[] = '';
	$md[] = '### ' . $p['title'] . ' (`' . $p['slug'] . '`)';
	$md[] = $p['excerpt'];
	$md[] = '- SEO: ' . $p['seo_title'] . ' / ' . $p['seo_description'];
}

$md[] = '';
$md[] = '## Taxonómiák';
foreach ( $export['taxonomies'] as $t ) {
	$md[] = '- [' . $t['taxonomy'] . '] ' . $t['name'] . ' (`' . $t['slug'] . '`)';
}

$md[] = '';
$md[] = '## Opciók / cégadatok';
foreach ( $export['options'] as $k => $v ) {
	$md[] = '- `' . $k . '`: ' . str_replace( "\n", ' / ', (string) $v );
}

$md[] = '';
$md[] = '## SEO (Rank Math)';
foreach ( $export['seo'] as $k => $v ) {
	$md[] = '- `' . $k . '`: ' . $v;
}

$md[] = '';
$md[] = '## Menük';
foreach ( $export['menus'] as $menu ) {
	$md[] = '### ' . $menu['name'];
	foreach ( $menu['items'] as $item ) {
		$md[] = '- ' . $item['title'] . ' → ' . $item['url'];
	}
}

$md[] = '';
$md[] = '## Szolgáltatáselemek';
foreach ( $export['service_items'] as $row ) {
	$md[] = '- **' . $row['name'] . '** (`' . ( $row['slug'] ?? '' ) . '`): ' . ( $row['description'] ?? '' );
}

$md[] = '';
$md[] = '## Státuszok';
foreach ( $export['statuses'] as $row ) {
	$md[] = '- ' . $row['name'] . ' (`' . $row['slug'] . '`)';
}

$md[] = '';
$md[] = '## E-mail sablonok';
foreach ( $export['email_templates'] as $row ) {
	$md[] = '';
	$md[] = '### ' . $row['slug'];
	$md[] = '- Subject: ' . $row['subject'];
	$md[] = '- Body:';
	$md[] = '```';
	$md[] = $row['body'];
	$md[] = '```';
}

$md[] = '';
$md[] = '## Dinamikus mezők';
foreach ( $export['dynamic_fields'] as $row ) {
	$md[] = '- `' . $row['field_key'] . '`: ' . $row['label'] . ' | placeholder: ' . ( $row['placeholder'] ?? '' ) . ' | help: ' . ( $row['help_text'] ?? '' );
}

$md_path = $dir . '/content-hu.md';
file_put_contents( $md_path, implode( "\n", $md ) . "\n" );

WP_CLI::success( 'Export ready: ' . $json_path . ' + ' . $md_path );
