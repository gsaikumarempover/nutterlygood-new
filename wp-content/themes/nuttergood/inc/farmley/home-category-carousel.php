<?php
/**
 * Homepage category icons — five parent categories in a static row (no carousel).
 */

if ( ! function_exists( 'nuttergood_farmley_home_category_slugs' ) ) {
	/**
	 * Allowed parent category slugs for the homepage SVG carousel.
	 *
	 * @return string[]
	 */
	function nuttergood_farmley_home_category_slugs() {
		return array(
			'dry-fruits',
			'chips',
			'mixes',
			'brittles',
			'mouth-fresheners',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_category_ids' ) ) {
	/**
	 * Term IDs for homepage carousel categories, in display order.
	 *
	 * @return int[]
	 */
	function nuttergood_farmley_home_category_ids() {
		$ids = array();

		foreach ( nuttergood_farmley_home_category_slugs() as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				$ids[] = (int) $term->term_id;
			}
		}

		return $ids;
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_home_category_carousel_widget' ) ) {
	/**
	 * @param \Elementor\Widget_Base $widget Elementor widget instance.
	 */
	function nuttergood_farmley_is_home_category_carousel_widget( $widget ) {
		if ( ! is_front_page() || ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) ) {
			return false;
		}

		if ( 'greenpath_core_product_category_list' !== $widget->get_name() ) {
			return false;
		}

		return method_exists( $widget, 'get_id' ) && 'bf23057' === $widget->get_id();
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_category_widget_settings' ) ) {
	/**
	 * Elementor settings for the static five-category row.
	 *
	 * @return array<string, string>
	 */
	function nuttergood_farmley_home_category_widget_settings() {
		$slugs = nuttergood_farmley_home_category_slugs();
		$ids   = nuttergood_farmley_home_category_ids();

		return array(
			'behavior'           => 'columns',
			'additional_params'  => 'slug',
			'taxonomy_slugs'     => implode( ',', $slugs ),
			'taxonomy_ids'       => implode( ', ', $ids ),
			'posts_per_page'     => (string) count( $slugs ),
			'orderby'            => 'include',
			'layout'             => 'info-below',
			'use_alternate_image' => 'no',
			'space'              => 'tiny',
			'vertical_space'     => 'tiny',
			'columns'            => '5',
			'columns_responsive' => 'custom',
			'columns_1512'       => '5',
			'columns_1368'       => '5',
			'columns_1200'       => '5',
			'columns_1024'       => '5',
			'columns_880'        => '5',
			'columns_680'        => '5',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_home_category_carousel_widget' ) ) {
	/**
	 * Render homepage categories as a static aligned row (no slider).
	 *
	 * @param \Elementor\Widget_Base $widget Elementor widget instance.
	 */
	function nuttergood_farmley_filter_home_category_carousel_widget( $widget ) {
		if ( ! nuttergood_farmley_is_home_category_carousel_widget( $widget ) ) {
			return;
		}

		$widget->set_settings(
			array_merge(
				$widget->get_settings_for_display(),
				nuttergood_farmley_home_category_widget_settings()
			)
		);
	}
	add_action( 'elementor/frontend/widget/before_render', 'nuttergood_farmley_filter_home_category_carousel_widget', 10, 1 );
}

if ( ! function_exists( 'nuttergood_farmley_home_category_icons_data' ) ) {
	/**
	 * @return array<string, array{png?: string, svg?: string, bg?: string, label?: string}>
	 */
	function nuttergood_farmley_home_category_icons_data() {
		static $icons = null;

		if ( null !== $icons ) {
			return $icons;
		}

		$icons = array();
		$file  = get_template_directory() . '/assets/icons/home-category-icons.json';

		if ( file_exists( $file ) ) {
			$decoded = json_decode( file_get_contents( $file ), true );
			if ( is_array( $decoded ) ) {
				$icons = $decoded;
			}
		}

		return $icons;
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_category_png_attachment_id' ) ) {
	/**
	 * Attachment ID for a homepage category PNG.
	 *
	 * @param string $slug Category slug.
	 */
	function nuttergood_farmley_home_category_png_attachment_id( $slug ) {
		static $cache = array();

		if ( isset( $cache[ $slug ] ) ) {
			return $cache[ $slug ];
		}

		$icons = nuttergood_farmley_home_category_icons_data();
		if ( empty( $icons[ $slug ]['png'] ) ) {
			$cache[ $slug ] = 0;
			return 0;
		}

		$rel = nuttergood_farmley_media_rel( 'categories', $icons[ $slug ]['png'] );

		global $wpdb;
		$attach = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
				$rel
			)
		);

		$cache[ $slug ] = $attach;
		return $attach;
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_category_thumbnail_meta' ) ) {
	/**
	 * Serve homepage category PNGs via thumbnail_id on the front page.
	 *
	 * @param mixed  $value     Meta value.
	 * @param int    $object_id Term ID.
	 * @param string $meta_key  Meta key.
	 * @param bool   $single    Single value flag.
	 * @param string $meta_type Meta type.
	 * @return mixed
	 */
	function nuttergood_farmley_home_category_thumbnail_meta( $value, $object_id, $meta_key, $single, $meta_type ) {
		static $resolving = array();

		if ( ! is_front_page() || 'term' !== $meta_type || 'thumbnail_id' !== $meta_key ) {
			return $value;
		}

		$guard_key = (int) $object_id;
		if ( isset( $resolving[ $guard_key ] ) ) {
			return $value;
		}

		$resolving[ $guard_key ] = true;

		$term = get_term( $guard_key, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) ) {
			unset( $resolving[ $guard_key ] );
			return $value;
		}

		$attach_id = nuttergood_farmley_home_category_png_attachment_id( $term->slug );
		unset( $resolving[ $guard_key ] );

		if ( $attach_id > 0 ) {
			return $single ? (string) $attach_id : array( (string) $attach_id );
		}

		return $value;
	}
	add_filter( 'get_term_metadata', 'nuttergood_farmley_home_category_thumbnail_meta', 25, 5 );
}

if ( ! function_exists( 'nuttergood_farmley_home_category_svg_meta' ) ) {
	/**
	 * Serve homepage category SVGs from theme JSON (no stale DB icons).
	 *
	 * @param mixed  $value     Meta value.
	 * @param int    $object_id Term ID.
	 * @param string $meta_key  Meta key.
	 * @param bool   $single    Single value flag.
	 * @param string $meta_type Meta type.
	 * @return mixed
	 */
	function nuttergood_farmley_home_category_svg_meta( $value, $object_id, $meta_key, $single, $meta_type ) {
		if ( ! is_front_page() || 'term' !== $meta_type ) {
			return $value;
		}

		if ( 'qodef_product_category_alternate_svg' !== $meta_key && 'qodef_product_category_svg_bg' !== $meta_key ) {
			return $value;
		}

		$term = get_term( (int) $object_id, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) ) {
			return $value;
		}

		$icons = nuttergood_farmley_home_category_icons_data();
		if ( empty( $icons[ $term->slug ] ) ) {
			return $value;
		}

		if ( ! empty( $icons[ $term->slug ]['png'] ) ) {
			if ( 'qodef_product_category_alternate_svg' === $meta_key ) {
				return $single ? '' : array( '' );
			}

			if ( 'qodef_product_category_svg_bg' === $meta_key && ! empty( $icons[ $term->slug ]['bg'] ) ) {
				$bg = $icons[ $term->slug ]['bg'];
				return $single ? $bg : array( $bg );
			}

			return $value;
		}

		if ( 'qodef_product_category_alternate_svg' === $meta_key && ! empty( $icons[ $term->slug ]['svg'] ) ) {
			$svg = $icons[ $term->slug ]['svg'];
			return $single ? $svg : array( $svg );
		}

		if ( 'qodef_product_category_svg_bg' === $meta_key && ! empty( $icons[ $term->slug ]['bg'] ) ) {
			$bg = $icons[ $term->slug ]['bg'];
			return $single ? $bg : array( $bg );
		}

		return $value;
	}
	add_filter( 'get_term_metadata', 'nuttergood_farmley_home_category_svg_meta', 20, 5 );
}

if ( ! function_exists( 'nuttergood_farmley_home_category_display_name' ) ) {
	/**
	 * Optional shorter labels for homepage category chips.
	 *
	 * @param WP_Term $term Term object.
	 * @return WP_Term
	 */
	function nuttergood_farmley_home_category_display_name( $term ) {
		if ( is_admin() || ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
			return $term;
		}

		if ( ! function_exists( 'nuttergood_farmley_home_category_slugs' ) ) {
			return $term;
		}

		if ( ! in_array( $term->slug, nuttergood_farmley_home_category_slugs(), true ) ) {
			return $term;
		}

		$icons = nuttergood_farmley_home_category_icons_data();
		if ( ! empty( $icons[ $term->slug ]['label'] ) ) {
			$term->name = $icons[ $term->slug ]['label'];
		}

		return $term;
	}
	add_filter( 'get_term', 'nuttergood_farmley_home_category_display_name', 20, 1 );
}

if ( ! function_exists( 'nuttergood_farmley_sync_home_category_icons' ) ) {
	/**
	 * Push branded SVG icons into homepage category term meta (versioned).
	 */
	function nuttergood_farmley_sync_home_category_icons() {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return;
		}

		$version    = 6;
		$option_key = 'ng_farmley_home_category_icons_v';
		if ( (int) get_option( $option_key, 0 ) >= $version ) {
			return;
		}

		$file = get_template_directory() . '/assets/icons/home-category-icons.json';
		if ( ! file_exists( $file ) ) {
			return;
		}

		$icons = json_decode( file_get_contents( $file ), true );
		if ( ! is_array( $icons ) ) {
			return;
		}

		foreach ( nuttergood_farmley_home_category_slugs() as $slug ) {
			if ( empty( $icons[ $slug ] ) ) {
				continue;
			}

			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}

			if ( ! empty( $icons[ $slug ]['png'] ) ) {
				$attach_id = nuttergood_farmley_home_category_png_attachment_id( $slug );
				if ( $attach_id ) {
					update_term_meta( $term->term_id, 'thumbnail_id', $attach_id );
				}
				delete_term_meta( $term->term_id, 'qodef_product_category_alternate_svg' );
				if ( ! empty( $icons[ $slug ]['bg'] ) ) {
					update_term_meta( $term->term_id, 'qodef_product_category_svg_bg', $icons[ $slug ]['bg'] );
				}
				continue;
			}

			if ( empty( $icons[ $slug ]['svg'] ) ) {
				continue;
			}

			update_term_meta( $term->term_id, 'qodef_product_category_alternate_svg', $icons[ $slug ]['svg'] );

			if ( ! empty( $icons[ $slug ]['bg'] ) ) {
				update_term_meta( $term->term_id, 'qodef_product_category_svg_bg', $icons[ $slug ]['bg'] );
			}
		}

		update_option( $option_key, $version );
	}
	add_action( 'init', 'nuttergood_farmley_sync_home_category_icons', 27 );
}