<?php

abstract class GreenPathCore_List_Shortcode extends QodeFrameworkShortcode {
	private $post_type;
	private $post_type_taxonomy;
	private $post_type_additional_taxonomies = array();
	private $layouts                         = array();
	private $hover_animation_options         = array();
	private $extra_options                   = array();

	public function __construct() {
		parent::__construct();

		$this->register_list_scripts();
	}

	public function register_list_scripts() {
		$scripts      = $this->get_scripts();
		$list_scripts = apply_filters( 'greenpath_core_filter_register_list_shortcode_scripts', isset( $scripts ) ? $scripts : array() );

		$this->set_scripts( $list_scripts );
	}

	public function get_post_type_filter_taxonomy( $atts ) {
		$filter_taxonomy = $this->get_post_type_taxonomy();

		if ( isset( $atts['tax'] ) && ! empty( $atts['tax'] ) ) {
			$filter_taxonomy = $atts['tax'];
		}

		return $filter_taxonomy;
	}

	public function get_post_type_taxonomy() {
		return $this->post_type_taxonomy;
	}

	public function set_post_type_taxonomy( $post_type_taxonomy ) {
		$this->post_type_taxonomy = $post_type_taxonomy;
	}

	public function get_layouts() {
		return $this->layouts;
	}

	public function set_layouts( $layouts ) {
		$this->layouts = $layouts;
	}

	public function get_hover_animation_options() {
		return $this->hover_animation_options;
	}

	public function set_hover_animation_options( $hover_animation_options ) {
		$this->hover_animation_options = $hover_animation_options;
	}

	public function map_list_options( $params = array() ) {
		$group                 = isset( $params['group'] ) ? $params['group'] : null;
		$exclude_option        = isset( $params['exclude_option'] ) ? $params['exclude_option'] : array();
		$exclude_behavior      = isset( $params['exclude_behavior'] ) ? $params['exclude_behavior'] : array();
		$exclude_columns       = isset( $params['exclude_columns'] ) ? $params['exclude_columns'] : array();
		$include_columns       = isset( $params['include_columns'] ) ? $params['include_columns'] : array();
		$include_slider_option = isset( $params['include_slider_option'] ) ? $params['include_slider_option'] : array();

		if ( empty( $exclude_behavior ) || ( ! empty( $exclude_behavior ) && ! in_array( 'behavior', $exclude_behavior, true ) ) ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'behavior',
					'title'         => esc_html__( 'List Appearance', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'list_behavior', false, $exclude_behavior ),
					'default_value' => 'columns',
					'group'         => $group,
				)
			);
		}
		if ( empty( $exclude_behavior ) || ( ! empty( $exclude_behavior ) && ! in_array( 'masonry', $exclude_behavior, true ) ) ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'masonry_images_proportion',
					'title'         => esc_html__( 'Image Proportions', 'greenpath-core' ),
					'options'       => array(
						''      => esc_html__( 'Original', 'greenpath-core' ),
						'fixed' => esc_html__( 'Fixed', 'greenpath-core' ),
					),
					'default_value' => '',
					'group'         => $group,
					'dependency'    => array(
						'show' => array(
							'behavior' => array(
								'values'        => 'masonry',
								'default_value' => 'columns',
							),
						),
					),
				)
			);
		}
		if ( empty( $exclude_option ) || ( ! empty( $exclude_option ) && ! in_array( 'images_proportion', $exclude_option, true ) ) ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'images_proportion',
					'default_value' => 'full',
					'title'         => esc_html__( 'Image Proportions', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'list_image_dimension', false ),
					'group'         => $group,
					'dependency'    => array(
						'show' => array(
							'behavior' => array(
								'values'        => array( '', 'columns', 'slider' ),
								'default_value' => 'columns',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'custom_image_width',
					'title'       => esc_html__( 'Custom Image Width', 'greenpath-core' ),
					'description' => esc_html__( 'Enter image width in px', 'greenpath-core' ),
					'group'       => $group,
					'dependency'  => array(
						'show' => array(
							'images_proportion' => array(
								'values'        => 'custom',
								'default_value' => 'full',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'custom_image_height',
					'title'       => esc_html__( 'Custom Image Height', 'greenpath-core' ),
					'description' => esc_html__( 'Enter image height in px', 'greenpath-core' ),
					'group'       => $group,
					'dependency'  => array(
						'show' => array(
							'images_proportion' => array(
								'values'        => 'custom',
								'default_value' => 'full',
							),
						),
					),
				)
			);
		}

		if ( empty( $exclude_option ) || ( ! empty( $exclude_option ) && ! in_array( 'columns', $exclude_option, true ) ) ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'columns',
					'title'         => esc_html__( 'Number of Columns', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'columns_number', true, $exclude_columns, $include_columns ),
					'default_value' => '3',
					'group'         => $group,
					'dependency'    => array(
						'hide' => array(
							'behavior' => array(
								'values'        => 'justified-gallery',
								'default_value' => 'columns',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'columns_responsive',
					'title'         => esc_html__( 'Columns Responsive', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'columns_responsive' ),
					'default_value' => 'predefined',
					'group'         => $group,
					'dependency'    => array(
						'hide' => array(
							'behavior' => array(
								'values'        => 'justified-gallery',
								'default_value' => 'columns',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'columns_1512',
					'title'         => esc_html__( 'Number of Columns 1369px - 1512px', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'columns_number', true, $exclude_columns, $include_columns ),
					'default_value' => '3',
					'dependency'    => array(
						'show' => array(
							'columns_responsive' => array(
								'values'        => 'custom',
								'default_value' => '3',
							),
						),
					),
					'group'         => $group,
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'columns_1368',
					'title'         => esc_html__( 'Number of Columns 1201px - 1368px', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'columns_number', true, $exclude_columns, $include_columns ),
					'default_value' => '3',
					'dependency'    => array(
						'show' => array(
							'columns_responsive' => array(
								'values'        => 'custom',
								'default_value' => '3',
							),
						),
					),
					'group'         => $group,
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'columns_1200',
					'title'         => esc_html__( 'Number of Columns 1025px - 1200px', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'columns_number', true, $exclude_columns, $include_columns ),
					'default_value' => '3',
					'dependency'    => array(
						'show' => array(
							'columns_responsive' => array(
								'values'        => 'custom',
								'default_value' => '3',
							),
						),
					),
					'group'         => $group,
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'columns_1024',
					'title'         => esc_html__( 'Number of Columns 881px - 1024px', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'columns_number', true, $exclude_columns, $include_columns ),
					'default_value' => '3',
					'dependency'    => array(
						'show' => array(
							'columns_responsive' => array(
								'values'        => 'custom',
								'default_value' => '3',
							),
						),
					),
					'group'         => $group,
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'columns_880',
					'title'         => esc_html__( 'Number of Columns 681px - 880px', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'columns_number', true, $exclude_columns, $include_columns ),
					'default_value' => '3',
					'dependency'    => array(
						'show' => array(
							'columns_responsive' => array(
								'values'        => 'custom',
								'default_value' => '3',
							),
						),
					),
					'group'         => $group,
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'columns_680',
					'title'         => esc_html__( 'Number of Columns 0 - 680px', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'columns_number', true, $exclude_columns, $include_columns ),
					'default_value' => '3',
					'dependency'    => array(
						'show' => array(
							'columns_responsive' => array(
								'values'        => 'custom',
								'default_value' => '3',
							),
						),
					),
					'group'         => $group,
				)
			);
		}
		$this->set_option(
			array(
				'field_type'    => 'select',
				'name'          => 'space',
				'title'         => esc_html__( 'Items Horizontal Spacing', 'greenpath-core' ),
				'options'       => greenpath_core_get_select_type_options_pool( 'items_space' ),
				'default_value' => '',
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'space_custom',
				'title'         => esc_html__( 'Custom Horizontal Spacing', 'greenpath-core' ),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'space' => array(
							'values'        => 'custom',
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'space_custom_1512',
				'title'         => esc_html__( 'Custom Horizontal Spacing - 1512', 'greenpath-core' ),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'space' => array(
							'values'        => 'custom',
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'space_custom_1200',
				'title'         => esc_html__( 'Custom Horizontal Spacing - 1200', 'greenpath-core' ),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'space' => array(
							'values'        => 'custom',
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'space_custom_880',
				'title'         => esc_html__( 'Custom Horizontal Spacing - 880', 'greenpath-core' ),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'space' => array(
							'values'        => 'custom',
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'select',
				'name'          => 'vertical_space',
				'title'         => esc_html__( 'Items Vertical Spacing', 'greenpath-core' ),
				'options'       => greenpath_core_get_select_type_options_pool( 'items_space' ),
				'default_value' => '',
				'dependency'    => array(
					'hide' => array(
						'behavior' => array(
							'values'        => array( 'slider' ),
							'default_value' => 'columns',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'vertical_space_custom',
				'title'         => esc_html__( 'Custom Vertical Spacing', 'greenpath-core' ),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'vertical_space' => array(
							'values'        => 'custom',
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'vertical_space_custom_1512',
				'title'         => esc_html__( 'Custom Vertical Spacing - 1512', 'greenpath-core' ),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'vertical_space' => array(
							'values'        => 'custom',
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'vertical_space_custom_1200',
				'title'         => esc_html__( 'Custom Vertical Spacing - 1200', 'greenpath-core' ),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'vertical_space' => array(
							'values'        => 'custom',
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'vertical_space_custom_880',
				'title'         => esc_html__( 'Custom Vertical Spacing - 880', 'greenpath-core' ),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'vertical_space' => array(
							'values'        => 'custom',
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'    => 'select',
				'name'          => 'offset_movement',
				'title'         => esc_html__( 'Enable Offset Movement', 'greenpath-core' ),
				'options'       => array(
					''       => esc_html__( 'No', 'greenpath-core' ),
					'scroll' => esc_html__( 'Scroll', 'greenpath-core' ),
					'cursor' => esc_html__( 'Cursor', 'greenpath-core' ),
				),
				'default_value' => '',
				'dependency'    => array(
					'show' => array(
						'behavior' => array(
							'values'        => 'masonry',
							'default_value' => 'columns',
						),
					),
				),
				'group'         => $group,
			)
		);

		if ( empty( $exclude_behavior ) || ( ! empty( $exclude_behavior ) && ! in_array( 'slider', $exclude_behavior, true ) ) ) {
			$params['dependency'] = array(
				'show' => array(
					'behavior' => array(
						'values'        => 'slider',
						'default_value' => 'columns',
					),
				),
			);
			$this->map_slider_options( $params );
		}

		if ( empty( $exclude_behavior ) || ( ! empty( $exclude_behavior ) && ! in_array( 'justified-gallery', $exclude_behavior, true ) ) ) {
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'justified_gallery_row_height',
					'title'      => esc_html__( 'Row Height', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'behavior' => array(
								'values'        => 'justified-gallery',
								'default_value' => 'columns',
							),
						),
					),
					'group'      => $group,
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'justified_gallery_row_height_max',
					'title'      => esc_html__( 'Max Row Height', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'behavior' => array(
								'values'        => 'justified-gallery',
								'default_value' => 'columns',
							),
						),
					),
					'group'      => $group,
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'justified_gallery_treshold',
					'title'      => esc_html__( 'Last Row Treshold', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'behavior' => array(
								'values'        => 'justified-gallery',
								'default_value' => 'columns',
							),
						),
					),
					'group'      => $group,
				)
			);
		}
	}

	public function map_slider_options( $params = array() ) {
		$group                 = isset( $params['group'] ) ? $params['group'] : null;
		$dependency            = isset( $params['dependency'] ) ? $params['dependency'] : array();
		$include_slider_option = isset( $params['include_slider_option'] ) ? $params['include_slider_option'] : array();

		$this->set_option(
			array(
				'field_type' => 'select',
				'name'       => 'slider_loop',
				'title'      => esc_html__( 'Enable Slider Loop', 'greenpath-core' ),
				'options'    => greenpath_core_get_select_type_options_pool( 'yes_no' ),
				'dependency' => $dependency,
				'group'      => $group,
			)
		);
		$this->set_option(
			array(
				'field_type' => 'select',
				'name'       => 'slider_autoplay',
				'title'      => esc_html__( 'Enable Slider Autoplay', 'greenpath-core' ),
				'options'    => greenpath_core_get_select_type_options_pool( 'yes_no' ),
				'dependency' => $dependency,
				'group'      => $group,
			)
		);
		$this->set_option(
			array(
				'field_type' => 'select',
				'name'       => 'slider_reverse_direction',
				'title'      => esc_html__( 'Reverse Autoplay Direction', 'greenpath-core' ),
				'options'    => greenpath_core_get_select_type_options_pool( 'yes_no' ),
				'dependency' => $dependency,
				'group'      => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'  => 'text',
				'name'        => 'slider_speed',
				'title'       => esc_html__( 'Slide Duration', 'greenpath-core' ),
				'description' => esc_html__( 'Default value is 5000 (ms)', 'greenpath-core' ),
				'dependency'  => $dependency,
				'group'       => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'  => 'text',
				'name'        => 'slider_decimal',
				'title'       => esc_html__( 'Slide Decimal', 'greenpath-core' ),
				'description' => esc_html__( 'Input decimal value to display a percentage of the next slide, e.g. 0.5 will display an additional half slide', 'greenpath-core' ),
				'dependency'  => $dependency,
				'group'       => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'  => 'text',
				'name'        => 'slider_decimal_1512',
				'title'       => esc_html__( 'Slide Decimal - 1512', 'greenpath-core' ),
				'description' => esc_html__( 'Input decimal value for responsive screens from 1369px to 1512px', 'greenpath-core' ),
				'dependency'  => $dependency,
				'group'       => $group,
			)
		);
		$this->set_option(
			array(
				'field_type'  => 'text',
				'name'        => 'slider_speed_animation',
				'title'       => esc_html__( 'Slide Animation Duration', 'greenpath-core' ),
				'description' => esc_html__( 'Speed of slide animation in milliseconds. Default value is 800.', 'greenpath-core' ),
				'dependency'  => $dependency,
				'group'       => $group,
			)
		);

		$this->set_option(
			array(
				'field_type'    => 'select',
				'name'          => 'slider_slide_animation',
				'title'         => esc_html__( 'Choose Slide Animation', 'greenpath-core' ),
				'options'       => array(
					''     => esc_html__( 'Default (Slide)', 'greenpath-core' ),
					'fade' => esc_html__( 'Fade', 'greenpath-core' ),
				),
				'default_value' => '',
				'description'   => esc_html__( 'Number of columns will be set to one.', 'greenpath-core' ),
				'dependency'    => $dependency,
				'group'         => $group,
			)
		);

		if ( ! empty( $include_slider_option ) && in_array( 'slider_direction', $include_slider_option, true ) ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'slider_direction',
					'title'         => esc_html__( 'Choose Slide Direction', 'greenpath-core' ),
					'options'       => array(
						''         => esc_html__( 'Default (Horizontal)', 'greenpath-core' ),
						'vertical' => esc_html__( 'Vertical', 'greenpath-core' ),
					),
					'default_value' => '',
					'description'   => esc_html__( 'Number of columns will be set to one.', 'greenpath-core' ),
					'dependency'    => $dependency,
					'group'         => $group,
				)
			);
		}

		if ( ! empty( $include_slider_option ) && in_array( 'slider_hidden_slides', $include_slider_option, true ) ) {
			$this->set_option(
				array(
					'field_type'  => 'select',
					'name'        => 'slider_hidden_slides',
					'title'       => esc_html__( 'Show Hidden Slides', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'yes_no' ),
					'description' => esc_html__( 'Works best with horizontal slide direction and slide animation.', 'greenpath-core' ),
					'dependency'  => $dependency,
					'group'       => $group,
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'slider_loop_additional_slides',
					'title'       => esc_html__( 'Loop Additional Slides', 'greenpath-core' ),
					'description' => esc_html__( 'Default value is 0 and this option works only when Show Hidden Slides option is enabled', 'greenpath-core' ),
					'dependency'  => $dependency,
					'group'       => $group,
				)
			);
		}

		if ( ! empty( $include_slider_option ) && in_array( 'slider_centered_slides', $include_slider_option, true ) ) {
			$this->set_option(
				array(
					'field_type'  => 'select',
					'name'        => 'slider_centered_slides',
					'title'       => esc_html__( 'Enable Centered Slides', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'yes_no' ),
					'description' => esc_html__( 'If true, then active slide will be centered, not always on the left side.', 'greenpath-core' ),
					'dependency'  => $dependency,
					'group'       => $group,
				)
			);
		}

		if ( ! empty( $include_slider_option ) && in_array( 'slider_mousewheel_navigation', $include_slider_option, true ) ) {
			$this->set_option(
				array(
					'field_type'  => 'select',
					'name'        => 'slider_mousewheel_navigation',
					'title'       => esc_html__( 'Enable Slider Mousewheel Navigation', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'yes_no' ),
					'description' => esc_html__( 'Enables navigation through slides using mouse wheel.', 'greenpath-core' ),
					'dependency'  => $dependency,
					'group'       => $group,
				)
			);
		}

		if ( ! empty( $include_slider_option ) && in_array( 'slider_drag_cursor', $include_slider_option, true ) ) {
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'slider_drag_cursor',
					'title'      => esc_html__( 'Show Drag Cursor', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'yes_no' ),
					'dependency' => $dependency,
					'group'      => $group,
				)
			);
		}

		if ( ! empty( $include_slider_option ) && in_array( 'slider_auto_height', $include_slider_option, true ) ) {
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'slider_auto_height',
					'title'      => esc_html__( 'Calculate Slide Height Automatically', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'yes_no' ),
					'dependency' => $dependency,
					'group'      => $group,
				)
			);
		}

		$this->set_option(
			array(
				'field_type' => 'select',
				'name'       => 'slider_navigation',
				'title'      => esc_html__( 'Show Slider Navigation', 'greenpath-core' ),
				'options'    => greenpath_core_get_select_type_options_pool( 'yes_no', true, '', array( 'combo' => esc_html__( 'Combo', 'greenpath-core' ) ) ),
				'dependency' => $dependency,
				'group'      => $group,
			)
		);

		$this->set_option(
			array(
				'field_type'    => 'select',
				'name'          => 'slider_navigation_position',
				'title'         => esc_html__( 'Slider Navigation Position', 'greenpath-core' ),
				'options'       => array(
					''        => esc_html__( 'Default', 'greenpath-core' ),
					'outside' => esc_html__( 'Outside', 'greenpath-core' ),
				),
				'default_value' => '',
				'dependency' => array(
					'show' => array(
						'slider_navigation' => array(
							'values'        => array( 'yes', 'combo' ),
							'default_value' => '',
						),
					),
				),
				'group'         => $group,
			)
		);

		$this->set_option(
			array(
				'field_type'  => 'select',
				'name'        => 'slider_pagination',
				'title'       => esc_html__( 'Show Slider Pagination', 'greenpath-core' ),
				'options'     => greenpath_core_get_select_type_options_pool( 'yes_no' ),
				'description' => esc_html__( 'This option does\'t work when Show Slider Navigation option is set to Combo', 'greenpath-core' ),
				'dependency'  => $dependency,
				'group'       => $group,
			)
		);
	}

	public function get_list_classes( $atts ) {
		$holder_classes = array();

		$holder_classes[] = 'qodef-grid';
		$holder_classes[] = ! empty( $atts['behavior'] ) && 'slider' === $atts['behavior'] ? 'qodef-swiper-container' : 'qodef-layout--' . $atts['behavior'];
		$holder_classes[] = ! empty( $atts['behavior'] ) && 'masonry' === $atts['behavior'] && ! empty( $atts['masonry_images_proportion'] ) && 'fixed' === $atts['masonry_images_proportion'] ? 'qodef-items--fixed' : '';
		$holder_classes[] = ! empty( $atts['space'] ) ? 'qodef-gutter--' . $atts['space'] : '';
		$holder_classes[] = ! empty( $atts['vertical_space'] ) ? 'qodef-vertical-gutter--' . $atts['vertical_space'] : '';
		$holder_classes[] = ! empty( $atts['columns'] ) ? 'qodef-col-num--' . $atts['columns'] : '';

		$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-item-layout--' . $atts['layout'] : '';

		if ( isset( $atts['enable_filter'] ) && 'yes' === $atts['enable_filter'] ) {
			$holder_classes[] = 'qodef-filter--on';
		}

		if ( ! empty( $atts['pagination_type'] ) ) {
			if ( 'no-pagination' === $atts['pagination_type'] ) {
				$holder_classes[] = 'qodef-pagination--off';
			} else {
				$holder_classes[] = 'qodef-pagination--on';
				$holder_classes[] = 'qodef-pagination-type--' . $atts['pagination_type'];
			}
		}

		if ( ! empty( $atts['columns_responsive'] ) && 'custom' === $atts['columns_responsive'] ) {
			$holder_classes[] = 'qodef-responsive--custom';
			$holder_classes[] = ! empty( $atts['columns_1512'] ) ? 'qodef-col-num--1512--' . $atts['columns_1512'] : 'qodef-col-num--1512--' . $atts['columns'];
			$holder_classes[] = ! empty( $atts['columns_1368'] ) ? 'qodef-col-num--1368--' . $atts['columns_1368'] : 'qodef-col-num--1368--' . $atts['columns'];
			$holder_classes[] = ! empty( $atts['columns_1200'] ) ? 'qodef-col-num--1200--' . $atts['columns_1200'] : 'qodef-col-num--1200--' . $atts['columns'];
			$holder_classes[] = ! empty( $atts['columns_1024'] ) ? 'qodef-col-num--1024--' . $atts['columns_1024'] : 'qodef-col-num--1024--' . $atts['columns'];
			$holder_classes[] = ! empty( $atts['columns_880'] ) ? 'qodef-col-num--880--' . $atts['columns_880'] : 'qodef-col-num--880--' . $atts['columns'];
			$holder_classes[] = ! empty( $atts['columns_680'] ) ? 'qodef-col-num--680--' . $atts['columns_680'] : 'qodef-col-num--680--' . $atts['columns'];
		} else {
			$holder_classes[] = 'qodef-responsive--predefined';
		}

		if ( isset( $atts['slider_pagination'] ) && 'no' !== $atts['slider_pagination'] ) {
			$holder_classes[] = 'qodef-swiper--show-pagination';
		}

		if ( isset( $atts['slider_navigation'] ) && 'combo' === $atts['slider_navigation'] ) {
			$holder_classes[] = 'qodef-swiper--show-navigation-combo';
		}

		if ( isset( $atts['slider_hidden_slides'] ) && 'yes' === $atts['slider_hidden_slides'] ) {
			$holder_classes[] = 'qodef-swiper--show-hidden-slides';
		}

		if ( isset( $atts['slider_drag_cursor'] ) && 'yes' === $atts['slider_drag_cursor'] ) {
			$holder_classes[] = 'qodef--drag-cursor';
		}

		return $holder_classes;
	}

	public function get_list_styles( $atts ) {
		$styles = greenpath_core_get_gutter_custom_styles( 'space_', 'vertical_space_', $atts, true );

		return $styles;
	}

	public function get_hover_animation_classes( $atts ) {
		$holder_classes = array();

		$layout = $atts['layout'];
		if ( isset( $atts[ 'hover_animation_' . $layout ] ) ) {
			$holder_classes[] = 'qodef-hover-animation--' . $atts[ 'hover_animation_' . $layout ];
		}

		return $holder_classes;
	}

	public function get_list_item_classes( $atts ) {
		$item_classes = array();

		$item_classes[] = ! empty( $atts['behavior'] ) && 'slider' === $atts['behavior'] ? 'swiper-slide' : 'qodef-grid-item';
		$item_classes[] = ! empty( $atts['offset_movement'] ) ? 'qodef-' . $atts['offset_movement'] . '-item' : '';

		if ( ! empty( $atts['image_dimension'] ) ) {
			$item_classes[] = $atts['image_dimension']['class'];
		}

		return $item_classes;
	}

	public function get_list_item_image_dimension( $atts ) {
		$image_dimension = array();

		if ( ! empty( $atts['behavior'] ) && 'masonry' === $atts['behavior'] && ! empty( $atts['masonry_images_proportion'] ) && 'fixed' === $atts['masonry_images_proportion'] ) {
			$masonry_image_dimension_name = 'qodef_masonry_image_dimension_' . str_replace( '-', '_', $atts['post_type'] );
			$image_dimension              = greenpath_core_get_custom_image_size_meta( 'meta-box', $masonry_image_dimension_name, get_the_ID() );
		}

		if ( ! empty( $atts['behavior'] ) && in_array( $atts['behavior'], array( 'columns', 'slider' ), true ) ) {
			$image_dimension = array(
				'size'  => $atts['images_proportion'],
				'class' => greenpath_core_get_custom_image_size_class_name( $atts['images_proportion'] ),
			);
		}

		return $image_dimension;
	}

	public function get_slider_data( $atts, $include = array() ) {
		$data = array();

		if ( isset( $atts['space'] ) ) {
			if ( 'custom' === $atts['space'] ) {
				$stages = array( 'custom', 'custom_1512', 'custom_1200', 'custom_880' );
				foreach ( $stages as $stage ) {
					$stage_value = str_replace( array( 'custom', '_' ), array( '', '' ), $stage );
					if ( ! empty( $atts[ 'space_' . $stage ] ) ) {
						$data[ 'spaceBetween' . $stage_value ] = intval( $atts[ 'space_' . $stage ] );
					} else {
						$data[ 'spaceBetween' . $stage_value ] = 30;
					}
				}
			} else {
				$data['spaceBetween'] = greenpath_core_get_space_value( $atts['space'] );
			}
		} else {
			$data['spaceBetween'] = 30;
		}

		$decimal                  = isset( $atts['slider_decimal'] ) ? floatval( $atts['slider_decimal'] ) : 0;
		$decimal_1512             = isset( $atts['slider_decimal'] ) ? floatval( $atts['slider_decimal_1512'] ) : 0;
		$data['slidesPerView']    = isset( $atts['columns'] ) ? intval( $atts['columns'] ) + $decimal : 1 + $decimal;
		$data['loop']             = isset( $atts['slider_loop'] ) ? 'no' !== $atts['slider_loop'] : true;
		$data['autoplay']         = isset( $atts['slider_autoplay'] ) ? 'no' !== $atts['slider_autoplay'] : true;
		$data['reverseDirection'] = isset( $atts['slider_reverse_direction'] ) && 'yes' === $atts['slider_reverse_direction']  ? true : '';
		$data['speed']            = isset( $atts['slider_speed'] ) ? $atts['slider_speed'] : '';
		$data['speedAnimation']   = isset( $atts['slider_speed_animation'] ) ? $atts['slider_speed_animation'] : '';
		$data['slideAnimation']   = isset( $atts['slider_slide_animation'] ) ? $atts['slider_slide_animation'] : '';
		$data['direction']        = isset( $atts['slider_direction'] ) ? $atts['slider_direction'] : '';
		$data['centeredSlides']   = isset( $atts['slider_centered_slides'] ) && 'yes' === $atts['slider_centered_slides'] ? true : '';
		$data['sliderScroll']     = isset( $atts['slider_mousewheel_navigation'] ) && 'yes' === $atts['slider_mousewheel_navigation'] ? true : '';
		$data['autoHeight']       = isset( $atts['slider_auto_height'] ) && 'yes' === $atts['slider_auto_height'] ? true : '';
		$data['loopedSlides']     = isset( $atts['slider_loop_additional_slides'] ) ? $atts['slider_loop_additional_slides'] : '';
		$data['hiddenSlides']     = isset( $atts['slider_hidden_slides'] ) ? $atts['slider_hidden_slides'] : '';

		if ( ! empty( $atts['columns_responsive'] ) && 'custom' === $atts['columns_responsive'] ) {
			$data['customStages']      = true;
			$data['slidesPerView1512'] = ! empty( $atts['columns_1512'] ) ? intval( $atts['columns_1512'] ) + $decimal_1512 : intval( $atts['columns'] ) + $decimal_1512; // Apply decimal to this stage
			$data['slidesPerView1368'] = ! empty( $atts['columns_1368'] ) ? $atts['columns_1368'] : $atts['columns'];
			$data['slidesPerView1200'] = ! empty( $atts['columns_1200'] ) ? $atts['columns_1200'] : $atts['columns'];
			$data['slidesPerView1024'] = ! empty( $atts['columns_1024'] ) ? $atts['columns_1024'] : $atts['columns'];
			$data['slidesPerView880']  = ! empty( $atts['columns_880'] ) ? $atts['columns_880'] : $atts['columns'];
			$data['slidesPerView680']  = ! empty( $atts['columns_680'] ) ? $atts['columns_680'] : $atts['columns'];
		}

		// force slides per view to 1 when fade animation or vertical direction
		if ( ( isset( $atts['slider_slide_animation'] ) && 'fade' === $atts['slider_slide_animation'] ) || ( isset( $atts['slider_direction'] ) && 'vertical' === $atts['slider_slide_animation'] ) ) {
			$data['slidesPerView']     = 1;
			$data['customStages']      = false;
			$data['slidesPerView1512'] = 1;
			$data['slidesPerView1368'] = 1;
			$data['slidesPerView1200'] = 1;
			$data['slidesPerView1024'] = 1;
			$data['slidesPerView880']  = 1;
			$data['slidesPerView680']  = 1;
		}

		if ( isset( $atts['unique'] ) && ! empty( $atts['unique'] ) ) {
			$data['outsideNavigation'] = 'yes';
		}

		if ( ! empty( $include ) ) {
			foreach ( $include as $key => $value ) {
				if ( ! array_key_exists( $key, $data ) ) {
					$data[ $key ] = $value;
				}
			}
		}

		return json_encode( $data );
	}

	public function map_query_options( $params = array() ) {
		$group                = isset( $params['group'] ) ? $params['group'] : esc_html__( 'Query', 'greenpath-core' );
		$post_type            = isset( $params['post_type'] ) ? $params['post_type'] : 'post';
		$taxonomies_formatted = array();
		$exclude_option       = isset( $params['exclude_option'] ) ? $params['exclude_option'] : array();
		$exclude_order_by     = isset( $params['exclude_order_by'] ) ? $params['exclude_order_by'] : array();
		$include_order_by     = isset( $params['include_order_by'] ) ? $params['include_order_by'] : array();

		if ( ! empty( $post_type ) ) {
			$main_taxonomy = $this->get_post_type_taxonomy();
			$taxonomies    = array_filter( array_merge( array( ! empty( $main_taxonomy ) ? $main_taxonomy : '' ), $this->get_post_type_additional_taxonomies() ) );

			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					$taxonomies_formatted[ $taxonomy ] = ucwords( str_replace( array( '_', '-' ), array( ' ', ' ' ), $taxonomy ) );
				}
			}
		}

		$this->set_option(
			array(
				'field_type'    => 'text',
				'name'          => 'posts_per_page',
				'title'         => esc_html__( 'Posts per Page', 'greenpath-core' ),
				'default_value' => '9',
				'group'         => $group,
			)
		);
		if ( empty( $exclude_option ) || ( ! empty( $exclude_option ) && ! in_array( 'orderby', $exclude_option, true ) ) ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'orderby',
					'title'         => esc_html__( 'Order By', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'order_by', true, $exclude_order_by, $include_order_by ),
					'default_value' => 'date',
					'group'         => $group,
				)
			);
		}
		if ( empty( $exclude_option ) || ( ! empty( $exclude_option ) && ! in_array( 'order', $exclude_option, true ) ) ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'order',
					'title'         => esc_html__( 'Order', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'order' ),
					'default_value' => 'DESC',
					'group'         => $group,
				)
			);
		}

		if ( empty( $exclude_option ) || ( ! empty( $exclude_option ) && ! in_array( 'additional_params', $exclude_option, true ) ) ) {

			do_action( 'greenpath_core_action_map_query_options_before_additional', $group );

			$additional_params = apply_filters(
				'greenpath_core_filter_map_additional_query_params',
				array(
					''       => esc_html__( 'No', 'greenpath-core' ),
					'id'     => esc_html__( 'Post IDs', 'greenpath-core' ),
					'tax'    => esc_html__( 'Taxonomy Slug', 'greenpath-core' ),
					'author' => esc_html__( 'Author Name', 'greenpath-core' ),
				),
				$this->get_base()
			);

			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'additional_params',
					'title'      => esc_html__( 'Additional Params', 'greenpath-core' ),
					'options'    => $additional_params,
					'group'      => $group,
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'post_ids',
					'title'       => esc_html__( 'Posts IDs', 'greenpath-core' ),
					'description' => esc_html__( 'Separate post IDs with commas', 'greenpath-core' ),
					'group'       => $group,
					'dependency'  => array(
						'show' => array(
							'additional_params' => array(
								'values'        => 'id',
								'default_value' => '',
							),
						),
					),
				)
			);
			if ( ! empty( $taxonomies_formatted ) ) {
				$this->set_option(
					array(
						'field_type' => 'select',
						'name'       => 'tax',
						'title'      => esc_html__( 'Taxonomy', 'greenpath-core' ),
						'options'    => $taxonomies_formatted,
						'group'      => $group,
						'dependency' => array(
							'show' => array(
								'additional_params' => array(
									'values'        => 'tax',
									'default_value' => '',
								),
							),
						),
					)
				);
				$this->set_option(
					array(
						'field_type' => 'text',
						'name'       => 'tax_slug',
						'title'      => esc_html__( 'Taxonomy Slug', 'greenpath-core' ),
						'group'      => $group,
						'dependency' => array(
							'show' => array(
								'additional_params' => array(
									'values'        => 'tax',
									'default_value' => '',
								),
							),
						),
					)
				);
				$this->set_option(
					array(
						'field_type'  => 'text',
						'name'        => 'tax__in',
						'title'       => esc_html__( 'Taxonomy IDs', 'greenpath-core' ),
						'description' => esc_html__( 'Separate taxonomy IDs with commas', 'greenpath-core' ),
						'group'       => $group,
						'dependency'  => array(
							'show' => array(
								'additional_params' => array(
									'values'        => 'tax',
									'default_value' => '',
								),
							),
						),
					)
				);
			}
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'author_slug',
					'title'      => esc_html__( 'Author Slug', 'greenpath-core' ),
					'group'      => $group,
					'dependency' => array(
						'show' => array(
							'additional_params' => array(
								'values'        => 'author',
								'default_value' => '',
							),
						),
					),
				)
			);

			do_action( 'greenpath_core_action_map_query_options_after_additional', $group );
		}
	}

	public function get_post_type_additional_taxonomies() {
		return $this->post_type_additional_taxonomies;
	}

	public function set_post_type_additional_taxonomies( $post_type_additional_taxonomies ) {
		$this->post_type_additional_taxonomies = $post_type_additional_taxonomies;
	}

	public function get_additional_query_args( $atts ) {
		$args = array();

		if ( ! empty( $atts['additional_params'] ) && 'id' === $atts['additional_params'] && ! empty( $atts['post_ids'] ) ) {
			$post_ids         = explode( ',', $atts['post_ids'] );
			$args['orderby']  = 'post__in';
			$args['post__in'] = $post_ids;
		}

		if ( ! empty( $atts['additional_params'] ) && 'tax' === $atts['additional_params'] ) {
			$taxonomy_values = array();

			$slug = isset( $atts['tax_slug'] ) ? $atts['tax_slug'] : '';
			$ids  = isset( $atts['tax__in'] ) ? $atts['tax__in'] : '';

			if ( ! empty( $ids ) && empty( $slug ) ) {
				$taxonomy_values['field'] = 'term_id';
				$taxonomy_values['terms'] = is_array( $ids ) ? array_map( 'intval', $ids ) : array_map( 'intval', explode( ',', str_replace( ' ', '', $ids ) ) );
			} elseif ( ! empty( $slug ) ) {
				$taxonomy_values['field'] = 'slug';
				$taxonomy_values['terms'] = strpos( $slug, ',' ) !== false ? explode( ',', $slug ) : trim( $slug );
			}

			if ( ! empty( $atts['tax'] ) && ! empty( $taxonomy_values ) ) {
				$args['tax_query'] = array( array_merge( array( 'taxonomy' => $atts['tax'] ), $taxonomy_values ) );
			}
		}

		if ( ! empty( $atts['additional_params'] ) && 'author' === $atts['additional_params'] ) {

			if ( is_array( $atts['author_slug'] ) ) {
				$args['author'] = implode( ',', $atts['author_slug'] );
			} else {
				$args['author_name'] = $atts['author_slug'];
			}
		}

		$args = apply_filters( 'greenpath_core_filter_additional_query_args', $args, $atts, $this->get_post_type() );

		return $args;
	}

	public function get_post_type() {
		return $this->post_type;
	}

	public function set_post_type( $post_type ) {
		$this->post_type = $post_type;
	}

	public function map_layout_options( $params = array() ) {
		$layouts                 = isset( $params['layouts'] ) ? $params['layouts'] : array();
		$hover_animations        = isset( $params['hover_animations'] ) ? $params['hover_animations'] : array();
		$exclude_option          = isset( $params['exclude_option'] ) ? $params['exclude_option'] : array();
		$default_value_title_tag = isset( $params['default_value_title_tag'] ) ? $params['default_value_title_tag'] : 'h4';

		$layout_visibility_field_type = sizeof( $layouts ) > 1 ? 'select' : 'hidden';

		$default_value = '';
		if ( ! empty( $layouts ) ) {
			reset( $layouts );
			$default_value = key( $layouts );
		}

		$this->set_option(
			array(
				'field_type'    => $layout_visibility_field_type,
				'name'          => 'layout',
				'title'         => esc_html__( 'Item Layout', 'greenpath-core' ),
				'options'       => $layouts,
				'default_value' => apply_filters( 'greenpath_core_filter_map_layout_options_default_value', $default_value, $this->get_base(), $params ),
				'group'         => esc_html__( 'Layout', 'greenpath-core' ),
			)
		);

		if ( ! empty( $hover_animations ) ) {
			foreach ( $hover_animations as $option ) {
				$this->set_option( $option );
			}
		}

		if ( empty( $exclude_option ) || ( ! empty( $exclude_option ) && ! in_array( 'title_tag', $exclude_option, true ) ) ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'title_tag',
					'title'         => esc_html__( 'Title Tag', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'title_tag' ),
					'default_value' => $default_value_title_tag,
					'group'         => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
		}
		if ( empty( $exclude_option ) || ( ! empty( $exclude_option ) && ! in_array( 'text_transform', $exclude_option, true ) ) ) {
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'text_transform',
					'title'      => esc_html__( 'Title Text Transform', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_transform' ),
					'group'      => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
		}
	}

	public function map_additional_options( $params = array() ) {
		$group_name         = esc_html__( 'Additional', 'greenpath-core' );
		$exclude_filter     = isset( $params['exclude_filter'] ) ? (bool) $params['exclude_filter'] : false;
		$exclude_pagination = isset( $params['exclude_pagination'] ) ? (bool) $params['exclude_pagination'] : false;

		if ( ! $exclude_filter ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_filter',
					'title'         => esc_html__( 'Enable Filter', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show categories filter above list', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes' ),
					'default_value' => '',
					'dependency'    => array(
						'show' => array(
							'behavior' => array(
								'values'        => array( 'columns', 'masonry' ),
								'default_value' => 'columns',
							),
						),
					),
					'group'         => $group_name,
				)
			);

			do_action( 'greenpath_core_action_map_additional_options_after_filter', $group_name );
		}

		if ( ! $exclude_pagination ) {
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'pagination_type',
					'title'         => esc_html__( 'Pagination', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'pagination_type' ),
					'default_value' => 'no-pagination',
					'group'         => $group_name,
				)
			);

			$this->set_option(
				array(
					'field_type'    => 'text',
					'name'          => 'pagination_top_margin',
					'title'         => esc_html__( 'Pagination Top Margin (px or %)', 'greenpath-core' ),
					'default_value' => '',
					'group'         => $group_name,
					'dependency'    => array(
						'show' => array(
							'pagination_type' => array(
								'values'        => array( 'standard', 'load-more' ),
								'default_value' => '',
							),
						),
					),
				)
			);

			do_action( 'greenpath_core_action_map_additional_options_after_pagination', $group_name );
		}
	}

	public function map_extra_options() {
		$extra_options = $this->get_extra_options();

		if ( ! empty( $extra_options ) ) {
			foreach ( $extra_options as $option ) {
				$this->set_option( $option );
			}
		}
	}

	public function get_extra_options() {
		return $this->extra_options;
	}

	public function set_extra_options( $extra_options ) {
		$this->extra_options = $extra_options;
	}

	public function load_assets() {
		do_action( 'greenpath_core_action_list_shortcodes_load_assets', $this->get_atts() );
	}

	public function render( $options, $content = null ) {
		parent::render( $options );

		$atts = $this->get_atts();

		$pagination_top_margin = '';
		if ( ! empty( $atts['pagination_top_margin'] ) ) {
			if ( qode_framework_string_ends_with_space_units( $atts['pagination_top_margin'] ) ) {
				$pagination_top_margin = 'margin-top: ' . $atts['pagination_top_margin'];
			} else {
				$pagination_top_margin = 'margin-top: ' . intval( $atts['pagination_top_margin'] ) . 'px';
			}
		}
		$atts['pagination_top_margin'] = $pagination_top_margin;

		$this->set_option_atts( $atts );
	}
}
