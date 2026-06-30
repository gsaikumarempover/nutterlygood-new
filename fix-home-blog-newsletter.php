<?php
/**
 * Add "Latest from Our Blog" section + sample posts to the homepage Elementor layout.
 * Newsletter strip is rendered via inc/farmley/home-blog-newsletter.php on the front page.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once get_template_directory() . '/inc/farmley/home-blog-newsletter.php';

function ng_blog_log( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_get_attachment_id_by_filename( $filename ) {
	global $wpdb;
	$like = '%' . $wpdb->esc_like( $filename ) . '%';
	$id   = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1",
			$like
		)
	);
	return $id > 0 ? $id : 0;
}

function ng_ensure_blog_category() {
	$term = get_term_by( 'slug', 'wellness', 'category' );
	if ( $term && ! is_wp_error( $term ) ) {
		return (int) $term->term_id;
	}
	$result = wp_insert_term( 'Wellness', 'category', array( 'slug' => 'wellness' ) );
	if ( is_wp_error( $result ) ) {
		return 0;
	}
	return (int) $result['term_id'];
}

function ng_create_sample_blog_posts() {
	$cat_id = ng_ensure_blog_category();
	$posts  = array(
		array(
			'title'    => 'Why soaked dry fruits are better for digestion',
			'image'    => 'blog-img-11.jpg',
			'excerpt'  => 'Soaking almonds, raisins, and walnuts can improve nutrient absorption and make everyday snacking gentler on your stomach.',
			'content'  => '<p>Soaking dry fruits overnight helps reduce phytic acid and makes minerals easier for your body to absorb. It is a simple habit that pairs well with Nutterly Good premium nuts and raisins.</p>',
			'days_ago' => 12,
		),
		array(
			'title'    => 'Healthy snacking with nuts and seeds',
			'image'    => 'blog-img-12.jpg',
			'excerpt'  => 'A handful of thoughtfully chosen nuts can keep energy steady between meals without relying on processed snacks.',
			'content'  => '<p>Choose roasted or raw nuts with minimal seasoning, pair them with fruit, and keep portions practical. Our mixes are crafted for balanced everyday snacking.</p>',
			'days_ago' => 18,
		),
		array(
			'title'    => 'Best mouth fresheners for after meals',
			'image'    => 'blog-img-10.jpg',
			'excerpt'  => 'Traditional mukhwas blends refresh naturally and finish a meal with aroma, crunch, and familiar comfort.',
			'content'  => '<p>From saunf mixes to premium supari blends, mouth fresheners are a cultural staple. Store them airtight to preserve crunch and fragrance.</p>',
			'days_ago' => 24,
		),
		array(
			'title'    => 'How to store dry fruits at home',
			'image'    => 'blog-img-7.jpg',
			'excerpt'  => 'Airtight containers, cool cupboards, and small batch buying help dry fruits stay fresh, crisp, and flavourful longer.',
			'content'  => '<p>Keep nuts away from heat and humidity. Refrigeration works well for opened packs in warmer climates. Always seal bags tightly after each use.</p>',
			'days_ago' => 31,
		),
		array(
			'title'    => 'Protein-rich trail mixes for busy days',
			'image'    => 'blog-img-8.jpg',
			'excerpt'  => 'Trail mixes with almonds, cashews, and seeds deliver convenient energy for work, travel, and post-workout recovery.',
			'content'  => '<p>Look for mixes with whole nuts, minimal sugar, and balanced salt. Portion into small jars so healthy snacking stays effortless all week.</p>',
			'days_ago' => 38,
		),
		array(
			'title'    => 'A guide to choosing quality almonds',
			'image'    => 'blog-img-6.jpg',
			'excerpt'  => 'Uniform size, fresh aroma, and clean crunch are easy quality cues when buying almonds for roasting, gifting, or daily use.',
			'content'  => '<p>Good almonds feel heavy for their size and taste naturally sweet. Avoid dull or rubbery nuts, and prefer sealed packs from trusted sources.</p>',
			'days_ago' => 45,
		),
	);

	$created = 0;
	foreach ( $posts as $item ) {
		$existing = get_page_by_title( $item['title'], OBJECT, 'post' );
		if ( $existing ) {
			ng_blog_log( 'Post exists: ' . $item['title'] );
			continue;
		}

		$post_date = gmdate( 'Y-m-d H:i:s', strtotime( '-' . (int) $item['days_ago'] . ' days' ) );
		$post_id   = wp_insert_post(
			array(
				'post_title'   => $item['title'],
				'post_content' => $item['content'],
				'post_excerpt' => $item['excerpt'],
				'post_status'  => 'publish',
				'post_type'    => 'post',
				'post_date'    => $post_date,
				'post_date_gmt' => get_gmt_from_date( $post_date ),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			ng_blog_log( 'Failed post: ' . $item['title'] . ' — ' . $post_id->get_error_message() );
			continue;
		}

		if ( $cat_id ) {
			wp_set_post_categories( $post_id, array( $cat_id ) );
		}

		$attachment_id = ng_get_attachment_id_by_filename( $item['image'] );
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}

		++$created;
		ng_blog_log( 'Created post #' . $post_id . ': ' . $item['title'] );
	}

	return $created;
}

function ng_get_blog_container_from_export() {
	$export = __DIR__ . '/export/home-elementor-36.json';
	if ( ! file_exists( $export ) ) {
		return null;
	}

	$elements = json_decode( file_get_contents( $export ), true );
	if ( ! is_array( $elements ) ) {
		return null;
	}

	$walker = function ( $nodes ) use ( &$walker ) {
		foreach ( $nodes as $el ) {
			if ( ( $el['id'] ?? '' ) === 'ca3823c' ) {
				return $el;
			}
			if ( ! empty( $el['elements'] ) ) {
				$found = $walker( $el['elements'] );
				if ( $found ) {
					return $found;
				}
			}
		}
		return null;
	};

	return $walker( $elements );
}

function ng_update_homepage_blog_section() {
	$page_id = (int) get_option( 'page_on_front' );
	if ( ! $page_id ) {
		ng_blog_log( 'No front page set.' );
		return false;
	}

	$raw = get_post_meta( $page_id, '_elementor_data', true );
	if ( ! $raw ) {
		ng_blog_log( 'No Elementor data.' );
		return false;
	}

	$elements = json_decode( $raw, true );
	if ( ! is_array( $elements ) ) {
		ng_blog_log( 'Invalid Elementor JSON.' );
		return false;
	}

	$has_blog = false;
	foreach ( $elements as $el ) {
		if ( ( $el['id'] ?? '' ) === 'ca3823c' ) {
			$has_blog = true;
			break;
		}
	}

	if ( ! $has_blog ) {
		$blog_container = ng_get_blog_container_from_export();
		if ( ! $blog_container ) {
			ng_blog_log( 'Blog container template missing from export.' );
			return false;
		}

		// Update blog list widget settings for live site posts.
		$blog_settings = nuttergood_farmley_home_blog_widget_settings();
		foreach ( $blog_container['elements'] as &$child ) {
			if ( ( $child['id'] ?? '' ) === '1ea2b9b' && 'greenpath_core_blog_list' === ( $child['widgetType'] ?? '' ) ) {
				$child['settings'] = array_merge( $child['settings'] ?? array(), $blog_settings );
				unset( $child['settings']['post_ids'], $child['settings']['tax_slug'] );
			}
			if ( ( $child['id'] ?? '' ) === '216d355' ) {
				$child['settings']['title'] = 'Latest from Our Blog';
			}
		}
		unset( $child );

		$elements[] = $blog_container;
		ng_blog_log( 'Added blog section container ca3823c.' );
	} else {
		$walker = function ( array &$nodes ) use ( &$walker, &$blog_settings_applied ) {
			foreach ( $nodes as &$el ) {
				if ( 'greenpath_core_blog_list' === ( $el['widgetType'] ?? '' ) && ( $el['id'] ?? '' ) === '1ea2b9b' ) {
					$el['settings'] = array_merge(
						$el['settings'] ?? array(),
						nuttergood_farmley_home_blog_widget_settings()
					);
					unset( $el['settings']['post_ids'], $el['settings']['tax_slug'] );
					$blog_settings_applied = true;
				}
				if ( 'greenpath_core_section_title' === ( $el['widgetType'] ?? '' ) && ( $el['id'] ?? '' ) === '216d355' ) {
					$el['settings']['title'] = 'Latest from Our Blog';
				}
				if ( ! empty( $el['elements'] ) ) {
					$walker( $el['elements'] );
				}
			}
		};
		$blog_settings_applied = false;
		$walker( $elements );
		ng_blog_log( $blog_settings_applied ? 'Updated existing blog widget.' : 'Blog container present; widget not found.' );
	}

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	delete_post_meta( $page_id, '_elementor_css' );

	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}

	return true;
}

ng_blog_log( '=== Fix homepage blog + newsletter ===' );
$created = ng_create_sample_blog_posts();
ng_blog_log( "Blog posts created: {$created}" );
$ok = ng_update_homepage_blog_section();
ng_blog_log( $ok ? 'Homepage Elementor updated. Hard-refresh the front page.' : 'Homepage Elementor update failed.' );