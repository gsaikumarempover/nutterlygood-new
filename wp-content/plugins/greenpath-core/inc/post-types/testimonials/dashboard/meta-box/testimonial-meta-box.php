<?php

if ( ! function_exists( 'greenpath_core_add_testimonials_meta_box' ) ) {
	/**
	 * Function that adds fields for testimonials
	 */
	function greenpath_core_add_testimonials_meta_box() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope' => array( 'testimonials' ),
				'type'  => 'meta',
				'slug'  => 'testimonials',
				'title' => esc_html__( 'Testimonials Parameters', 'greenpath-core' ),
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_testimonials_title',
					'title'      => esc_html__( 'Title', 'greenpath-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'textarea',
					'name'       => 'qodef_testimonials_text',
					'title'      => esc_html__( 'Text', 'greenpath-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_testimonials_author',
					'title'      => esc_html__( 'Author', 'greenpath-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_testimonials_author_job',
					'title'      => esc_html__( 'Author Job Title', 'greenpath-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_testimonials_rating',
					'title'         => esc_html__( 'Rating', 'greenpath-core' ),
					'options'       => array(
						'0' => esc_html__( '0', 'greenpath-core' ),
						'1' => esc_html__( '1', 'greenpath-core' ),
						'2' => esc_html__( '2', 'greenpath-core' ),
						'3' => esc_html__( '3', 'greenpath-core' ),
						'4' => esc_html__( '4', 'greenpath-core' ),
						'5' => esc_html__( '5', 'greenpath-core' ),
					),
					'default_value' => '0'
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_testimonial_image_right',
					'title'         => esc_html__( 'Featured Image On Right', 'greenpath-core' ),
					'description'   => esc_html__( 'Only for Info Boxed layout', 'greenpath-core' ),
					'default_value' => 'no',
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_testimonials_meta_box_map', $page );
		}
	}

	add_action( 'greenpath_core_action_default_meta_boxes_init', 'greenpath_core_add_testimonials_meta_box' );
}
