<?php

if ( ! function_exists( 'greenpath_get_sidebar_name' ) ) {
	/**
	 * Function that return sidebar name
	 *
	 * @return string
	 */
	function greenpath_get_sidebar_name() {
		return apply_filters( 'greenpath_filter_sidebar_name', 'qodef-main-sidebar' );
	}
}

if ( ! function_exists( 'greenpath_get_sidebar_layout' ) ) {
	/**
	 * Function that return sidebar layout
	 *
	 * @return string
	 */
	function greenpath_get_sidebar_layout() {
		$sidebar_layout = apply_filters( 'greenpath_filter_sidebar_layout', 'no-sidebar' );

		if ( 'no-sidebar' !== $sidebar_layout && ! is_active_sidebar( greenpath_get_sidebar_name() ) ) {
			$sidebar_layout = 'no-sidebar';
		}

		return $sidebar_layout;
	}
}

if ( ! function_exists( 'greenpath_get_page_content_sidebar_classes' ) ) {
	/**
	 * Function that return classes for the page content when sidebar is enabled
	 *
	 * @return string
	 */
	function greenpath_get_page_content_sidebar_classes() {
		$classes = array( 'qodef-page-content-section', 'qodef-col--content' );

		return implode( ' ', apply_filters( 'greenpath_filter_page_content_sidebar_classes', $classes ) );
	}
}

if ( ! function_exists( 'greenpath_get_page_sidebar_classes' ) ) {
	/**
	 * Function that return classes for the page sidebar when sidebar is enabled
	 *
	 * @return string
	 */
	function greenpath_get_page_sidebar_classes() {
		$classes = array( 'qodef-page-sidebar-section', 'qodef-col--sidebar' );

		return implode( ' ', apply_filters( 'greenpath_filter_page_sidebar_classes', $classes ) );
	}
}

if ( ! function_exists( 'greenpath_get_page_grid_sidebar_classes' ) ) {
	/**
	 * Function that return classes for the page grid when sidebar is enabled
	 *
	 * @return string
	 */
	function greenpath_get_page_grid_sidebar_classes() {
		$layout  = greenpath_get_sidebar_layout();
		$classes = array();

		switch ( $layout ) {
			case 'sidebar-33-right':
				$classes[] = 'qodef-grid-template--8-4';
				break;
			case 'sidebar-25-right':
				$classes[] = 'qodef-grid-template--9-3';
				break;
			case 'sidebar-33-left':
				$classes[] = 'qodef-grid-template--4-8';
				$classes[] = 'qodef-grid-template--reverse';
				break;
			case 'sidebar-25-left':
				$classes[] = 'qodef-grid-template--3-9';
				$classes[] = 'qodef-grid-template--reverse';
				break;
			default:
				$classes[] = 'qodef-grid-template--12';
				break;
		}

		return implode( ' ', apply_filters( 'greenpath_filter_page_grid_sidebar_classes', $classes, $layout ) );
	}
}
