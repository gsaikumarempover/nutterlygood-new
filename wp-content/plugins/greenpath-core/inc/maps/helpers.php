<?php

if ( ! function_exists( 'greenpath_core_get_google_maps_api_key' ) ) {
	/**
	 * Function that check is option enabled or return option value
	 *
	 * @param string $return_type
	 *
	 * @return bool|string
	 */
	function greenpath_core_get_google_maps_api_key( $return_type = 'option' ) {
		$option = greenpath_core_get_option_value( 'admin', 'qodef_maps_api_key' );

		if ( 'is_enabled' === $return_type ) {
			return ! empty( $option );
		}

		return $option;
	}
}

/* MULTIPLE MAP FUNCTIONS - START */
if ( ! function_exists( 'greenpath_core_set_multiple_map_variables' ) ) {
	/**
	 * Function for setting single map variables
	 *
	 * @param array $query - $query is used just for multiple type. $query is Wp_Query args object containing listing items which should be presented on map
	 *
	 * @return array - array with addresses parameters
	 */
	function greenpath_core_set_multiple_map_variables( $query = array() ) {
		$map_variables = array();

		if ( is_array( $query ) && count( $query ) ) {
			$items = qode_framework_get_cpt_items( $query['post_type'], $query );

			if ( ! empty( $items ) ) {
				foreach ( $items as $id => $title ) {
					$map_variables['addresses'][] = greenpath_core_generate_map_params( $id, $query['post_type'] );
				}
			}
		}

		return $map_variables;
	}
}

if ( ! function_exists( 'greenpath_core_get_multiple_map' ) ) {
	/**
	 * Function that renders map holder for multiple listing item
	 *
	 * @param array $query - $query is used just for multiple type. $query is Wp_Query object containing listing items which should be presented on map
	 *
	 * @return string
	 */
	function greenpath_core_get_multiple_map( $query = array() ) {
		$addresses = greenpath_core_set_multiple_map_variables( $query );

		if ( greenpath_core_get_google_maps_api_key( 'is_enabled' ) ) {
			$html = '<div id="qodef-multiple-map-holder" ' . qode_framework_get_inline_attr( json_encode( $addresses ), 'data-addresses' ) . '></div>';
		} else {
			$html = '<p id="qodef-multiple-map-notice">' . esc_html__( 'In order for the map functionality to be enabled please input the Google Map API key in the General section of the Nutterlygood Options', 'greenpath-core' ) . '</p>';
		}

		do_action( 'greenpath_core_action_after_multiple_map' );

		return $html;
	}
}

/* MULTIPLE MAP FUNCTIONS - START */

/* MAP ITEMS FUNCTIONS START - */
if ( ! function_exists( 'greenpath_core_marker_info_template' ) ) {
	/**
	 * Template with placeholders for marker info window
	 *
	 * uses underscore templates
	 */
	function greenpath_core_marker_info_template() {
		$marker_template = apply_filters(
			'greenpath_core_filter_marker_info_template',
			'<a itemprop="url" class="qodef-info-window-link" href="<%= itemUrl %>"></a>
			<% if ( typeof featuredImage !== "undefined" ) { %>
				<div class="qodef-info-window-image">
					<img itemprop="image" src="<%= featuredImage[0] %>" alt="<%= title %>" width="<%= featuredImage[1] %>" height="<%= featuredImage[2] %>">
				</div>
			<% } %>
			<div class="qodef-info-window-details">
				<h6 itemprop="name" class="qodef-info-window-title"><%= title %></h6>
				<% if ( typeof address !== "undefined" ) { %>
					<p class="qodef-info-window-location"><%= address %></p>
				<% } %>
			</div>'
		);

		$html = '<script type="text/template" class="qodef-info-window-template"><div class="qodef-info-window"><div class="qodef-info-window-inner">' . $marker_template. '</div></div></script>';

		print $html;
	}

	add_action( 'greenpath_core_action_after_multiple_map', 'greenpath_core_marker_info_template' );
}

if ( ! function_exists( 'greenpath_core_marker_template' ) ) {
	/**
	 * Template with placeholders for marker
	 */
	function greenpath_core_marker_template() {
		$pin_icon = apply_filters(
			'greenpath_core_filter_marker_pin_icon',
			'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="37.875px" height="50.75px" viewBox="0 0 37.875 50.75" enable-background="new 0 0 37.875 50.75" xml:space="preserve"><g><path fill="#EF4960" d="M0,18.938C0,29.396,17.746,50.75,18.938,50.75V0C8.479,0,0,8.479,0,18.938z"/><path fill="#DC4458" d="M37.875,18.938C37.875,8.479,29.396,0,18.938,0v50.75C20.129,50.75,37.875,29.396,37.875,18.938z"/></g><circle fill="#FFFFFF" cx="18.938" cy="19.188" r="14.813"/></svg>'
		);

		$html = '<script type="text/template" class="qodef-marker-template"><div class="qodef-map-marker"><div class="qodef-map-marker-inner">' . $pin_icon . '</div></div></script>';

		print $html;
	}

	add_action( 'greenpath_core_action_after_multiple_map', 'greenpath_core_marker_template' );
}

/* MAP ITEMS FUNCTIONS - END */

/* HELPER FUNCTIONS - START */

if ( ! function_exists( 'greenpath_core_generate_map_params' ) ) {
	/**
	 * Function that generate maps parameters
	 *
	 * @param int $item_id
	 * @param string $post_type
	 *
	 * @return array
	 */
	function greenpath_core_generate_map_params( $item_id, $post_type ) {
		$map_params = array();

		// get listing image
		$image_id = get_post_thumbnail_id( $item_id );
		$image    = wp_get_attachment_image_src( $image_id );

		// take marker pin
		$marker_pin = greenpath_core_get_svg_icon( 'pin' );

		// get address params
		$address_array = apply_filters( 'greenpath_core_filter_address_params', array(), $item_id, $post_type );

		// get item location
		if ( empty( $address_array ) || ( '' === $address_array['address'] && '' === $address_array['address_lat'] && '' === $address_array['address_long'] ) ) {
			$map_params['location'] = null;
		} else {
			$map_params['location'] = array(
				'address'   => $address_array['address'],
				'latitude'  => $address_array['address_lat'],
				'longitude' => $address_array['address_long'],
			);
		}

		$map_params['title']     = get_the_title( $item_id );
		$map_params['itemId']    = $item_id;
		$map_params['markerPin'] = $marker_pin;
		$map_params['itemUrl']   = get_the_permalink( $item_id );
		if ( is_array( $image ) ) {
			$map_params['featuredImage'] = $image;
		}

		return apply_filters( 'greenpath_core_filter_map_params', $map_params, $item_id, $post_type );
	}
}

/* HELPER FUNCTIONS - END */
