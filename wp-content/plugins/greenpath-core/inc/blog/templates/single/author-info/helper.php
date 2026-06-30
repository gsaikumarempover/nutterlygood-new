<?php

if ( ! function_exists( 'greenpath_core_include_blog_single_author_info_template' ) ) {
	/**
	 * Function which includes additional module on single posts page
	 */
	function greenpath_core_include_blog_single_author_info_template() {
		if ( is_single() ) {
			include_once GREENPATH_CORE_INC_PATH . '/blog/templates/single/author-info/templates/author-info.php';
		}
	}

	add_action( 'greenpath_action_after_blog_post_item', 'greenpath_core_include_blog_single_author_info_template', 20 );  // permission 15 is set to define template position
}

if ( ! function_exists( 'greenpath_core_get_author_social_networks' ) ) {
	/**
	 * Function which includes author info templates on single posts page
	 */
	function greenpath_core_get_author_social_networks( $user_id ) {
		$icons           = array();
		$social_networks = array(
			'facebook',
			'twitter',
			'linkedin',
			'instagram',
			'pinterest',
		);

		foreach ( $social_networks as $network ) {
			$network_meta = get_the_author_meta( 'qodef_user_' . $network, $user_id );

			$icon_params = array(
				'icon_attributes' => array(
					'class' => 'qodef-social-network-icon',
				),
			);

			if ( ! empty( $network_meta ) ) {
				$$network = array(
					'url'     => $network_meta,
					'icon'    => qode_framework_icons()->get_specific_icon_from_pack( $network, 'font-awesome', $icon_params ),
					'class'   => 'qodef-user-social-' . $network,
					'network' => $network,
				);

				$icons[ $network ] = $$network;
			}
		}

		return $icons;
	}
}
