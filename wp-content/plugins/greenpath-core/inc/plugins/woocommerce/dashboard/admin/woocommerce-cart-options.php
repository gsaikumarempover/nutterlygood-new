<?php

if ( ! function_exists( 'greenpath_core_add_cart_options' ) ) {
	/**
	 * Function that add general options for this module
	 *
	 * @param object $page
	 */
	function greenpath_core_add_cart_options( $page ) {

		if ( $page ) {
			$cart_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-cart',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Cart', 'greenpath-core' ),
					'description' => esc_html__( 'Settings related to the WooCommerce Cart page', 'greenpath-core' ),
				)
			);

			$cart_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_woo_cart_enable_progress_bar',
					'title'         => esc_html__( 'Enable Free Shipping Progress Bar', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show free shipping progress bar on cart page.', 'greenpath-core' ),
					'default_value' => 'yes',
				)
			);

			$pb_section = $cart_tab->add_section_element(
				array(
					'name'        => 'qodef_woo_cart_progress_bar_section',
					'title'       => esc_html__( 'Progress bar', 'greenpath-core' ),
					'description' => esc_html__( 'Progress bar settings', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'qodef_woo_cart_enable_progress_bar' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			$pb_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_cart_progress_bar_amount',
					'title'       => esc_html__( 'Progress Bar Amount', 'greenpath-core' ),
					'description' => esc_html__( 'Set the amount required to get free shipping. Make sure it corresponds to the value used in WooCommerce Shipping Method settings.', 'greenpath-core' ),
				)
			);

			$pb_section->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'qodef_woo_cart_progress_bar_amount_prefix',
					'title'         => esc_html__( 'Text before number', 'greenpath-core' ),
					'description'   => esc_html__( 'Text displayed before number.', 'greenpath-core' ),
					'default_value' => esc_html__( 'Spend', 'greenpath-core' ),
				)
			);

			$pb_section->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'qodef_woo_cart_progress_bar_amount_suffix',
					'title'         => esc_html__( 'Text after number', 'greenpath-core' ),
					'description'   => esc_html__( 'Text displayed after number.', 'greenpath-core' ),
					'default_value' => esc_html__( 'to get free shipping', 'greenpath-core' ),
				)
			);

			$cart_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_woo_cart_enable_countdown',
					'title'         => esc_html__( 'Enable Countdown on Cart page', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show countdown on cart page.', 'greenpath-core' ),
					'default_value' => 'yes',
				)
			);

			$cd_section = $cart_tab->add_section_element(
				array(
					'name'        => 'qodef_woo_cart_countdown_section',
					'title'       => esc_html__( 'Countdown', 'greenpath-core' ),
					'description' => esc_html__( 'Countdown settings', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'qodef_woo_cart_enable_countdown' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			$cd_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_cart_countdown_minutes',
					'title'       => esc_html__( 'Minutes', 'greenpath-core' ),
					'description' => esc_html__( 'How many minutes to count.', 'greenpath-core' ),
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_woo_options_map', 'greenpath_core_add_cart_options' );
}
