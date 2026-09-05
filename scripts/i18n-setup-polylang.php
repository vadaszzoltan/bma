<?php
/**
 * Configure Polylang (HU/RO/EN) and import translated posts/taxonomies/overlays.
 *
 * @package BMA
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'PLL' ) || ! PLL() ) {
	WP_CLI::error( 'Polylang is not active.' );
}

$catalog_path = WP_CONTENT_DIR . '/bma-i18n-export/translations.json';
if ( ! file_exists( $catalog_path ) ) {
	WP_CLI::error( 'Missing translations.json – run scripts/i18n-build-translations.py first.' );
}

$catalog = json_decode( (string) file_get_contents( $catalog_path ), true );
if ( ! is_array( $catalog ) ) {
	WP_CLI::error( 'Invalid translations.json' );
}

/**
 * Ensure a Polylang language exists.
 *
 * @param array<string, mixed> $args Language args.
 */
function bma_pll_ensure_language( array $args ): void {
	$existing = PLL()->model->get_language( $args['slug'] );
	if ( $existing ) {
		return;
	}
	PLL()->model->add_language( $args );
}

bma_pll_ensure_language(
	array(
		'name'       => 'Magyar',
		'slug'       => 'hu',
		'locale'     => 'hu_HU',
		'rtl'        => 0,
		'flag'       => 'hu',
		'term_group' => 0,
	)
);
bma_pll_ensure_language(
	array(
		'name'       => 'Română',
		'slug'       => 'ro',
		'locale'     => 'ro_RO',
		'rtl'        => 0,
		'flag'       => 'ro',
		'term_group' => 0,
	)
);
bma_pll_ensure_language(
	array(
		'name'       => 'English',
		'slug'       => 'en',
		'locale'     => 'en_US',
		'rtl'        => 0,
		'flag'       => 'us',
		'term_group' => 0,
	)
);

// Refresh language cache after adds.
if ( method_exists( PLL()->model, 'clean_languages_cache' ) ) {
	PLL()->model->clean_languages_cache();
}

$options = get_option( 'polylang', array() );
if ( ! is_array( $options ) ) {
	$options = array();
}
$options['default_lang']     = 'hu';
$options['force_lang']       = 1; // language code in URL.
$options['hide_default']     = 1;
$options['rewrite']          = 1;
$options['browser']          = 1;
$options['media_support']    = 0;
$options['redirect_lang']    = 0;
$options['sync']             = array();
update_option( 'polylang', $options );

// Mark existing content as Hungarian.
$post_types = array( 'page', 'post', 'bma_service', 'bma_reference' );
foreach ( $post_types as $pt ) {
	$ids = get_posts(
		array(
			'post_type'      => $pt,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ( $ids as $pid ) {
		if ( ! pll_get_post_language( $pid ) ) {
			pll_set_post_language( (int) $pid, 'hu' );
		}
	}
}

foreach ( array( 'bma_service_category', 'bma_reference_category', 'category', 'post_tag' ) as $tax ) {
	$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		continue;
	}
	foreach ( $terms as $term ) {
		if ( ! pll_get_term_language( $term->term_id ) ) {
			pll_set_term_language( (int) $term->term_id, 'hu' );
		}
	}
}

/**
 * Upsert a translation post linked to source.
 *
 * @param int                  $source_id Source post ID.
 * @param string               $lang      Language slug.
 * @param array<string, mixed> $data      Translated fields.
 * @param string               $post_type Post type.
 * @return int
 */
function bma_pll_upsert_post( int $source_id, string $lang, array $data, string $post_type ): int {
	$translations = pll_get_post_translations( $source_id );
	$existing_id  = isset( $translations[ $lang ] ) ? (int) $translations[ $lang ] : 0;
	$source       = get_post( $source_id );
	if ( ! $source ) {
		return 0;
	}

	$payload = array(
		'post_type'    => $post_type,
		'post_status'  => $source->post_status,
		'post_title'   => (string) ( $data['title'] ?? $source->post_title ),
		'post_name'    => sanitize_title( (string) ( $data['slug'] ?? $source->post_name ) ),
		'post_excerpt' => (string) ( $data['excerpt'] ?? $source->post_excerpt ),
		'post_content' => array_key_exists( 'content', $data ) && '' !== (string) $data['content']
			? (string) $data['content']
			: $source->post_content,
		'post_parent'  => 0,
		'menu_order'   => $source->menu_order,
	);

	if ( $existing_id ) {
		$payload['ID'] = $existing_id;
		wp_update_post( $payload );
		$new_id = $existing_id;
	} else {
		$new_id = (int) wp_insert_post( $payload, true );
		if ( is_wp_error( $new_id ) || ! $new_id ) {
			return 0;
		}
	}

	pll_set_post_language( $new_id, $lang );
	$translations[ $lang ] = $new_id;
	$translations['hu']    = $source_id;
	pll_save_post_translations( $translations );

	if ( ! empty( $data['seo_title'] ) ) {
		update_post_meta( $new_id, 'rank_math_title', $data['seo_title'] );
		update_post_meta( $new_id, 'rank_math_facebook_title', $data['seo_title'] );
	}
	if ( ! empty( $data['seo_description'] ) ) {
		update_post_meta( $new_id, 'rank_math_description', $data['seo_description'] );
		update_post_meta( $new_id, 'rank_math_facebook_description', $data['seo_description'] );
	}
	if ( isset( $data['focus_keyword'] ) ) {
		update_post_meta( $new_id, 'rank_math_focus_keyword', (string) $data['focus_keyword'] );
	}

	// Copy BMA operational meta so translated CPT posts stay active / ordered / described.
	$meta_keys = array(
		'_bma_is_active',
		'_bma_sort_order',
		'_bma_short_description',
		'_bma_long_description',
		'_bma_related_services',
		'_bma_use_placeholder',
		'_bma_cta_label',
		'_bma_cta_url',
		'_bma_project_type',
		'_bma_category',
		'_bma_year',
		'_bma_location',
		'_bma_client_type',
		'_bma_challenge',
		'_bma_solution',
		'_bma_result',
	);
	foreach ( $meta_keys as $meta_key ) {
		if ( ! metadata_exists( 'post', $source_id, $meta_key ) ) {
			continue;
		}
		$val = get_post_meta( $source_id, $meta_key, true );
		// Prefer translated excerpt as short description when provided.
		if ( '_bma_short_description' === $meta_key && ! empty( $data['excerpt'] ) ) {
			$val = (string) $data['excerpt'];
		}
		update_post_meta( $new_id, $meta_key, $val );
	}
	// Ensure translated services are active even if source meta was missing historically.
	if ( 'bma_service' === $post_type && '' === (string) get_post_meta( $new_id, '_bma_is_active', true ) ) {
		update_post_meta( $new_id, '_bma_is_active', '1' );
	}

	$thumb = get_post_thumbnail_id( $source_id );
	if ( $thumb ) {
		set_post_thumbnail( $new_id, $thumb );
	}

	return $new_id;
}

$created = array(
	'pages'      => 0,
	'services'   => 0,
	'references' => 0,
	'terms'      => 0,
);

foreach ( array( 'pages' => 'page', 'services' => 'bma_service', 'references' => 'bma_reference' ) as $section => $pt ) {
	foreach ( ( $catalog[ $section ] ?? array() ) as $source_id => $langs ) {
		$source_id = (int) $source_id;
		if ( ! get_post( $source_id ) ) {
			continue;
		}
		pll_set_post_language( $source_id, 'hu' );
		foreach ( array( 'ro', 'en' ) as $lang ) {
			if ( empty( $langs[ $lang ] ) ) {
				continue;
			}
			$id = bma_pll_upsert_post( $source_id, $lang, $langs[ $lang ], $pt );
			if ( $id ) {
				++$created[ $section ];
			}
		}
	}
}

// Taxonomies.
foreach ( ( $catalog['taxonomies'] ?? array() ) as $term_id => $langs ) {
	$term_id = (int) $term_id;
	$term    = get_term( $term_id );
	if ( ! $term || is_wp_error( $term ) ) {
		continue;
	}
	pll_set_term_language( $term_id, 'hu' );
	$translations = pll_get_term_translations( $term_id );
	$translations['hu'] = $term_id;

	foreach ( array( 'ro', 'en' ) as $lang ) {
		if ( empty( $langs[ $lang ]['name'] ) ) {
			continue;
		}
		$existing = isset( $translations[ $lang ] ) ? (int) $translations[ $lang ] : 0;
		$args     = array(
			'name' => $langs[ $lang ]['name'],
			'slug' => sanitize_title( $langs[ $lang ]['slug'] ?? $langs[ $lang ]['name'] ),
		);
		if ( $existing ) {
			wp_update_term( $existing, $term->taxonomy, $args );
			$new_id = $existing;
		} else {
			$res = wp_insert_term( $args['name'], $term->taxonomy, array( 'slug' => $args['slug'] ) );
			if ( is_wp_error( $res ) ) {
				continue;
			}
			$new_id = (int) $res['term_id'];
		}
		pll_set_term_language( $new_id, $lang );
		$translations[ $lang ] = $new_id;
		++$created['terms'];
	}
	pll_save_term_translations( $translations );
}

// Assign translated terms to translated reference posts.
foreach ( get_posts( array( 'post_type' => 'bma_reference', 'posts_per_page' => -1, 'post_status' => 'publish' ) ) as $ref ) {
	$lang = pll_get_post_language( $ref->ID );
	if ( ! $lang || 'hu' === $lang ) {
		continue;
	}
	$hu_id = pll_get_post( $ref->ID, 'hu' );
	if ( ! $hu_id ) {
		continue;
	}
	$hu_terms = wp_get_post_terms( $hu_id, 'bma_reference_category' );
	if ( is_wp_error( $hu_terms ) || empty( $hu_terms ) ) {
		continue;
	}
	$mapped = array();
	foreach ( $hu_terms as $ht ) {
		$tid = pll_get_term( $ht->term_id, $lang );
		if ( $tid ) {
			$mapped[] = (int) $tid;
		}
	}
	if ( $mapped ) {
		wp_set_post_terms( $ref->ID, $mapped, 'bma_reference_category', false );
	}
}

// Front page / page IDs map per language.
$page_map = get_option( 'bma_aj_page_ids', array() );
$front_hu = (int) get_option( 'page_on_front' );
if ( $front_hu ) {
	pll_set_post_language( $front_hu, 'hu' );
}

$translated_page_ids = array( 'hu' => $page_map );
foreach ( array( 'ro', 'en' ) as $lang ) {
	$translated_page_ids[ $lang ] = array();
	foreach ( $page_map as $key => $pid ) {
		$tr = pll_get_post( (int) $pid, $lang );
		if ( $tr ) {
			$translated_page_ids[ $lang ][ $key ] = (int) $tr;
		}
	}
}
update_option( 'bma_aj_page_ids_i18n', $translated_page_ids );

// DB / option overlay used by BMA_AJ_I18n.
$overlay = array(
	'options'         => $catalog['options'] ?? array(),
	'seo'             => $catalog['seo'] ?? array(),
	'statuses'        => $catalog['statuses'] ?? array(),
	'service_items'   => $catalog['service_items'] ?? array(),
	'dynamic_fields'  => $catalog['dynamic_fields'] ?? array(),
	'email_templates' => $catalog['email_templates'] ?? array(),
	'ui_strings'      => $catalog['ui_strings'] ?? array(),
);
update_option( 'bma_i18n_overlay', $overlay );

// Generate simple .l10n.php style maps for plugin (WP 6.5+ also accepts PHP translations).
$lang_dir = WP_PLUGIN_DIR . '/bma-ajanlatkeres/languages';
if ( ! is_dir( $lang_dir ) ) {
	wp_mkdir_p( $lang_dir );
}

foreach ( array( 'ro_RO' => 'ro', 'en_US' => 'en' ) as $locale => $lang ) {
	$entries = array();
	foreach ( ( $catalog['ui_strings'] ?? array() ) as $src => $tr ) {
		if ( empty( $tr[ $lang ] ) ) {
			continue;
		}
		$entries[ $src ] = array( $tr[ $lang ] );
	}
	$php = "<?php\nreturn [\n\t'messages' => " . var_export( $entries, true ) . ",\n];\n";
	// WordPress Jed/l10n php format differs; also write a JSON for our custom loader.
	file_put_contents( $lang_dir . '/bma-ajanlatkeres-' . $locale . '.json', wp_json_encode( $entries, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
}

// Store UI map for runtime gettext filter fallback.
update_option( 'bma_i18n_ui_strings', $catalog['ui_strings'] ?? array() );

flush_rewrite_rules( false );

WP_CLI::success(
	sprintf(
		'Polylang ready. Created/updated translations – pages:%d services:%d references:%d terms:%d',
		$created['pages'],
		$created['services'],
		$created['references'],
		$created['terms']
	)
);
