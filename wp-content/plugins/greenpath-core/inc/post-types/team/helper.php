<?php

if ( ! function_exists( 'greenpath_core_team_has_single' ) ) {
	/**
	 * Function that check if custom post type has single page
	 *
	 * @return bool
	 */
	function greenpath_core_team_has_single() {
		return false;
	}
}

if ( ! function_exists( 'greenpath_core_generate_team_single_layout' ) ) {
	/**
	 * Function that return default layout for custom post type single page
	 *
	 * @return string
	 */
	function greenpath_core_generate_team_single_layout() {
		$team_template = greenpath_core_get_post_value_through_levels( 'qodef_team_single_layout' );
		$team_template = empty( $team_template ) ? 'default' : $team_template;

		return $team_template;
	}

	add_filter( 'greenpath_core_filter_team_single_layout', 'greenpath_core_generate_team_single_layout' );
}

if ( ! function_exists( 'greenpath_core_get_team_holder_classes' ) ) {
	/**
	 * Function that return classes for the main team holder
	 *
	 * @return string
	 */
	function greenpath_core_get_team_holder_classes() {
		$classes = array( '' );

		$classes[] = 'qodef-team-single';

		$item_layout = greenpath_core_generate_team_single_layout();
		$classes[]   = 'qodef-item-layout--' . $item_layout;

		return implode( ' ', $classes );
	}
}

if ( ! function_exists( 'greenpath_core_generate_team_archive_with_shortcode' ) ) {
	/**
	 * Function that executes team list shortcode with params on archive pages
	 *
	 * @param string $tax - type of taxonomy
	 * @param string $tax_slug - slug of taxonomy
	 */
	function greenpath_core_generate_team_archive_with_shortcode( $tax, $tax_slug ) {
		$params = array();

		$params['additional_params']          = 'tax';
		$params['tax']                        = $tax;
		$params['tax_slug']                   = $tax_slug;
		$params['layout']                     = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_item_layout' );
		$params['behavior']                   = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_behavior' );
		$params['columns']                    = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_columns' );
		$params['space']                      = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_space' );
		$params['space_custom']               = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_space_custom' );
		$params['space_custom_1512']          = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_space_custom_1512' );
		$params['space_custom_1200']          = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_space_custom_1200' );
		$params['space_custom_880']           = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_space_custom_880' );
		$params['vertical_space']             = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_vertical_space' );
		$params['vertical_space_custom']      = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_vertical_space_custom' );
		$params['vertical_space_custom_1512'] = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_vertical_space_custom_1512' );
		$params['vertical_space_custom_1200'] = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_vertical_space_custom_1200' );
		$params['vertical_space_custom_880']  = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_vertical_space_custom_880' );
		$params['columns_responsive']         = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_columns_responsive' );
		$params['columns_1512']               = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_columns_1512' );
		$params['columns_1368']               = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_columns_1368' );
		$params['columns_1200']               = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_columns_1200' );
		$params['columns_1024']                = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_columns_1024' );
		$params['columns_880']                = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_columns_880' );
		$params['columns_680']                = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_columns_680' );
		$params['slider_loop']                = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_slider_loop' );
		$params['slider_autoplay']            = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_slider_autoplay' );
		$params['slider_speed']               = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_slider_speed' );
		$params['slider_navigation']          = greenpath_core_get_post_value_through_levels( 'navigation' );
		$params['slider_pagination']          = greenpath_core_get_post_value_through_levels( 'pagination' );
		$params['pagination_type']            = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_pagination_type' );

		echo GreenPathCore_Team_List_Shortcode::call_shortcode( $params );
	}
}

if ( ! function_exists( 'greenpath_core_is_team_title_enabled' ) ) {
	/**
	 * Function that check is module enabled
	 *
	 * @param bool $is_enabled
	 *
	 * @return bool
	 */
	function greenpath_core_is_team_title_enabled( $is_enabled ) {
		if ( is_singular( 'team' ) ) {
			$is_enabled = 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_enable_team_title' );
		}

		return $is_enabled;
	}

	add_filter( 'greenpath_filter_enable_page_title', 'greenpath_core_is_team_title_enabled' );
}

if ( ! function_exists( 'greenpath_core_team_title_grid' ) ) {
	/**
	 * Function that check is option enabled
	 *
	 * @param bool $enable_title_grid
	 *
	 * @return bool
	 */
	function greenpath_core_team_title_grid( $enable_title_grid ) {
		if ( is_singular( 'team' ) ) {
			$enable_title_grid = 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_set_team_title_area_in_grid' );
		}

		return $enable_title_grid;
	}

	add_filter( 'greenpath_core_filter_page_title_grid', 'greenpath_core_team_title_grid' );
}

if ( ! function_exists( 'greenpath_core_team_breadcrumbs_title' ) ) {
	/**
	 * Improve main breadcrumb template with additional cases
	 *
	 * @param string|html $wrap_child
	 * @param array $settings
	 *
	 * @return string|html
	 */
	function greenpath_core_team_breadcrumbs_title( $wrap_child, $settings ) {
		if ( is_tax( 'team-category' ) ) {
			$wrap_child  = '';
			$term_object = get_term( get_queried_object_id(), 'team-category' );

			if ( isset( $term_object->parent ) && 0 !== $term_object->parent ) {
				$parent      = get_term( $term_object->parent );
				$wrap_child .= sprintf( $settings['link'], get_term_link( $parent->term_id ), $parent->name ) . $settings['separator'];
			}

			$wrap_child .= sprintf( $settings['current_item'], single_cat_title( '', false ) );
		} elseif ( is_singular( 'team' ) ) {
			$wrap_child = '';
			$post_terms = wp_get_post_terms( get_the_ID(), 'team-category' );

			if ( ! empty( $post_terms ) ) {
				$post_term = $post_terms[0];
				if ( isset( $post_term->parent ) && 0 !== $post_term->parent ) {
					$parent      = get_term( $post_term->parent );
					$wrap_child .= sprintf( $settings['link'], get_term_link( $parent->term_id ), $parent->name ) . $settings['separator'];
				}
				$wrap_child .= sprintf( $settings['link'], get_term_link( $post_term ), $post_term->name ) . $settings['separator'];
			}

			$wrap_child .= sprintf( $settings['current_item'], get_the_title() );
		}

		return $wrap_child;
	}

	add_filter( 'greenpath_core_filter_breadcrumbs_content', 'greenpath_core_team_breadcrumbs_title', 10, 2 );
}

if ( ! function_exists( 'greenpath_core_set_team_custom_sidebar_name' ) ) {
	/**
	 * Function that return sidebar name
	 *
	 * @param string $sidebar_name
	 *
	 * @return string
	 */
	function greenpath_core_set_team_custom_sidebar_name( $sidebar_name ) {

		if ( is_singular( 'team' ) ) {
			$option = greenpath_core_get_post_value_through_levels( 'qodef_team_single_custom_sidebar' );
		} elseif ( is_tax() ) {
			$taxonomies = get_object_taxonomies( 'team' );

			foreach ( $taxonomies as $tax ) {
				if ( is_tax( $tax ) ) {
					$option = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_custom_sidebar' );
				}
			}
		}

		if ( isset( $option ) && ! empty( $option ) ) {
			$sidebar_name = $option;
		}

		return $sidebar_name;
	}

	add_filter( 'greenpath_filter_sidebar_name', 'greenpath_core_set_team_custom_sidebar_name' );
}

if ( ! function_exists( 'greenpath_core_set_team_sidebar_layout' ) ) {
	/**
	 * Function that return sidebar layout
	 *
	 * @param string $layout
	 *
	 * @return string
	 */
	function greenpath_core_set_team_sidebar_layout( $layout ) {

		if ( is_singular( 'team' ) ) {
			$option = greenpath_core_get_post_value_through_levels( 'qodef_team_single_sidebar_layout' );
		} elseif ( is_tax() ) {
			$taxonomies = get_object_taxonomies( 'team' );
			foreach ( $taxonomies as $tax ) {
				if ( is_tax( $tax ) ) {
					$option = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_sidebar_layout' );
				}
			}
		}

		if ( isset( $option ) && ! empty( $option ) ) {
			$layout = $option;
		}

		return $layout;
	}

	add_filter( 'greenpath_filter_sidebar_layout', 'greenpath_core_set_team_sidebar_layout' );
}

if ( ! function_exists( 'greenpath_core_set_team_sidebar_grid_gutter_classes' ) ) {
	/**
	 * Function that returns grid gutter classes
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	function greenpath_core_set_team_sidebar_grid_gutter_classes( $classes ) {

		if ( is_singular( 'team' ) ) {
			$option = greenpath_core_get_post_value_through_levels( 'qodef_team_single_sidebar_grid_gutter' );
		} elseif ( is_tax() ) {
			$taxonomies = get_object_taxonomies( 'team' );
			foreach ( $taxonomies as $tax ) {
				if ( is_tax( $tax ) ) {
					$option = greenpath_core_get_post_value_through_levels( 'qodef_team_archive_sidebar_grid_gutter' );
				}
			}
		}
		if ( isset( $option ) && ! empty( $option ) ) {
			$classes = 'qodef-gutter--' . esc_attr( $option );
		}

		return $classes;
	}

	add_filter( 'greenpath_filter_grid_gutter_classes', 'greenpath_core_set_team_sidebar_grid_gutter_classes' );
}

if ( ! function_exists( 'greenpath_core_set_team_sidebar_grid_gutter_styles' ) ) {
	/**
	 * Function that returns grid gutter styles
	 *
	 * @param array $styles
	 *
	 * @return array
	 */
	function greenpath_core_set_team_sidebar_grid_gutter_styles( $styles ) {

		if ( is_singular( 'team' ) ) {
			$styles = greenpath_core_get_gutter_custom_styles( 'qodef_team_single_sidebar_grid_gutter_' );
		} elseif ( is_tax() ) {
			$taxonomies = get_object_taxonomies( 'team' );
			foreach ( $taxonomies as $tax ) {
				if ( is_tax( $tax ) ) {
					$styles = greenpath_core_get_gutter_custom_styles( 'qodef_team_archive_sidebar_grid_gutter_' );
				}
			}
		}

		return $styles;
	}

	add_filter( 'greenpath_filter_grid_gutter_styles', 'greenpath_core_set_team_sidebar_grid_gutter_styles' );
}

if ( ! function_exists( 'greenpath_core_team_set_admin_options_map_position' ) ) {
	/**
	 * Function that set dashboard admin options map position for this module
	 *
	 * @param int $position
	 * @param string $map
	 *
	 * @return int
	 */
	function greenpath_core_team_set_admin_options_map_position( $position, $map ) {

		if ( 'team' === $map ) {
			$position = 52;
		}

		return $position;
	}

	add_filter( 'greenpath_core_filter_admin_options_map_position', 'greenpath_core_team_set_admin_options_map_position', 10, 2 );
}
