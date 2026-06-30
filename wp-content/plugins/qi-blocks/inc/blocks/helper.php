<?php

if ( ! function_exists( 'qi_blocks_content_contains_block_reference' ) ) {
	/**
	 * Check whether content references a Qi block by slug or namespace.
	 *
	 * @param string $content    Post content, widget content, or rendered template HTML.
	 * @param string $block_name Optional block slug without namespace.
	 *
	 * @return bool
	 */
	function qi_blocks_content_contains_block_reference( $content, $block_name = '' ) {
		if ( empty( $content ) || ! is_string( $content ) ) {
			return false;
		}

		if ( '' === $block_name ) {
			if ( false !== strpos( $content, 'qi-blocks/' ) ) {
				return true;
			}

			return false !== strpos( $content, 'wp-block-qi-blocks-' );
		}

		if ( false !== strpos( $content, 'qi-blocks/' . $block_name ) ) {
			return true;
		}

		return false !== strpos( $content, 'wp-block-qi-blocks-' . $block_name );
	}
}

if ( ! function_exists( 'qi_blocks_parsed_blocks_contain_block' ) ) {
	/**
	 * Recursively check parsed blocks for a Qi block, including reusable blocks.
	 *
	 * @param array  $blocks     Parsed blocks array.
	 * @param string $block_name Optional block slug without namespace.
	 *
	 * @return bool
	 */
	function qi_blocks_parsed_blocks_contain_block( $blocks, $block_name = '' ) {
		if ( empty( $blocks ) || ! is_array( $blocks ) ) {
			return false;
		}

		foreach ( $blocks as $block ) {
			if ( empty( $block['blockName'] ) ) {
				continue;
			}

			if ( '' === $block_name ) {
				if ( 0 === strpos( $block['blockName'], 'qi-blocks/' ) ) {
					return true;
				}
			} elseif ( 'qi-blocks/' . $block_name === $block['blockName'] ) {
				return true;
			}

			if ( 'core/block' === $block['blockName'] && ! empty( $block['attrs']['ref'] ) ) {
				$reusable_post = get_post( (int) $block['attrs']['ref'] );

				if ( $reusable_post && ! empty( $reusable_post->post_content ) ) {
					if ( qi_blocks_content_contains_block_reference( $reusable_post->post_content, $block_name ) ) {
						return true;
					}

					if ( function_exists( 'parse_blocks' ) && qi_blocks_parsed_blocks_contain_block( parse_blocks( $reusable_post->post_content ), $block_name ) ) {
						return true;
					}
				}
			}

			if ( 'core/template-part' === $block['blockName'] && ! empty( $block['attrs']['slug'] ) && function_exists( 'get_block_template' ) ) {
				$template_theme = ! empty( $block['attrs']['theme'] ) ? $block['attrs']['theme'] : get_stylesheet();
				$template_part  = get_block_template( $template_theme . '//' . $block['attrs']['slug'], 'wp_template_part' );

				if ( $template_part && ! empty( $template_part->content ) ) {
					if ( qi_blocks_content_contains_block_reference( $template_part->content, $block_name ) ) {
						return true;
					}

					if ( function_exists( 'parse_blocks' ) && qi_blocks_parsed_blocks_contain_block( parse_blocks( $template_part->content ), $block_name ) ) {
						return true;
					}
				}
			}

			if ( ! empty( $block['innerBlocks'] ) && qi_blocks_parsed_blocks_contain_block( $block['innerBlocks'], $block_name ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'qi_blocks_page_contains_qi_blocks' ) ) {
	/**
	 * Detect whether the current frontend request needs Qi Blocks assets.
	 *
	 * @return bool
	 */
	function qi_blocks_page_contains_qi_blocks() {
		static $contains  = null;
		static $resolving = false;

		if ( null !== $contains ) {
			return $contains;
		}

		if ( $resolving ) {
			return false;
		}

		$resolving = true;
		$contains  = false;

		if ( is_admin() ) {
			$resolving = false;

			return $contains;
		}

		$page_id = get_queried_object_id();

		if ( $page_id ) {
			$post_content = get_post_field( 'post_content', $page_id );

			if ( qi_blocks_content_contains_block_reference( $post_content ) ) {
				$contains  = true;
				$resolving = false;

				return $contains;
			}

			if ( function_exists( 'parse_blocks' ) && qi_blocks_parsed_blocks_contain_block( parse_blocks( $post_content ) ) ) {
				$contains  = true;
				$resolving = false;

				return $contains;
			}
		}

		if ( ! $contains && function_exists( 'qi_blocks_get_block_template_content' ) && qi_blocks_should_resolve_block_template_html() ) {
			$template_content = qi_blocks_get_block_template_content();

			if ( qi_blocks_content_contains_block_reference( $template_content ) ) {
				$contains = true;
			} elseif ( function_exists( 'parse_blocks' ) && qi_blocks_parsed_blocks_contain_block( parse_blocks( $template_content ) ) ) {
				$contains = true;
			}
		}

		if ( ! $contains ) {
			$widgets_block = get_option( 'widget_block' );

			if ( ! empty( $widgets_block ) && is_array( $widgets_block ) ) {
				foreach ( $widgets_block as $widget_block ) {
					if ( isset( $widget_block['content'] ) && qi_blocks_content_contains_block_reference( $widget_block['content'] ) ) {
						$contains = true;
						break;
					}
				}
			}
		}

		$resolving = false;

		return $contains;
	}
}

if ( ! function_exists( 'qi_blocks_should_load_frontend_assets' ) ) {
	/**
	 * Whether Qi Blocks frontend assets should be enqueued.
	 *
	 * @return bool
	 */
	function qi_blocks_should_load_frontend_assets() {
		return (bool) apply_filters( 'qi_blocks_should_load_frontend_assets', qi_blocks_page_contains_qi_blocks() );
	}
}

if ( ! function_exists( 'qi_blocks_is_block_present_on_page' ) ) {
	/**
	 * Check if a specific Qi block is present in post content, templates, or widgets.
	 *
	 * @param string $block_name Block slug without namespace.
	 *
	 * @return bool
	 */
	function qi_blocks_is_block_present_on_page( $block_name ) {
		static $presence_cache = array();

		if ( isset( $presence_cache[ $block_name ] ) ) {
			return $presence_cache[ $block_name ];
		}

		$is_present = function_exists( 'has_block' ) && has_block( 'qi-blocks/' . $block_name );

		if ( ! $is_present ) {
			$page_id = get_queried_object_id();

			if ( $page_id ) {
				$post_content = get_post_field( 'post_content', $page_id );

				if ( qi_blocks_content_contains_block_reference( $post_content, $block_name ) ) {
					$is_present = true;
				} elseif ( function_exists( 'parse_blocks' ) && qi_blocks_parsed_blocks_contain_block( parse_blocks( $post_content ), $block_name ) ) {
					$is_present = true;
				}
			}
		}

		if ( ! $is_present && function_exists( 'qi_blocks_get_block_template_content' ) && qi_blocks_should_resolve_block_template_html() ) {
			$template_content = qi_blocks_get_block_template_content();

			if ( qi_blocks_content_contains_block_reference( $template_content, $block_name ) ) {
				$is_present = true;
			} elseif ( function_exists( 'parse_blocks' ) && qi_blocks_parsed_blocks_contain_block( parse_blocks( $template_content ), $block_name ) ) {
				$is_present = true;
			}
		}

		if ( ! $is_present ) {
			static $widgets_block = null;

			if ( null === $widgets_block ) {
				$widgets_block = get_option( 'widget_block' );
			}

			if ( ! empty( $widgets_block ) && is_array( $widgets_block ) ) {
				foreach ( $widgets_block as $widget_block ) {
					if ( isset( $widget_block['content'] ) && qi_blocks_content_contains_block_reference( $widget_block['content'], $block_name ) ) {
						$is_present = true;
						break;
					}
				}
			}
		}

		$presence_cache[ $block_name ] = $is_present;

		return $is_present;
	}
}

if ( ! function_exists( 'qi_blocks_get_slider_block_names' ) ) {
	/**
	 * Block slugs that require the Swiper library on the frontend.
	 *
	 * @return array
	 */
	function qi_blocks_get_slider_block_names() {
		return (array) apply_filters(
			'qi_blocks_filter_slider_block_names',
			array(
				'image-slider',
				'blog-slider',
				'product-slider',
				'clients-slider',
				'testimonials-slider',
				'device-slider',
				'device-carousel',
				'preview-slider',
				'slider-switch',
			)
		);
	}
}

if ( ! function_exists( 'qi_blocks_parsed_blocks_contain_slider_block' ) ) {
	/**
	 * Recursively check parsed blocks for any Qi slider block.
	 *
	 * @param array $blocks Parsed blocks array.
	 *
	 * @return bool
	 */
	function qi_blocks_parsed_blocks_contain_slider_block( $blocks ) {
		foreach ( qi_blocks_get_slider_block_names() as $slider_block_name ) {
			if ( qi_blocks_parsed_blocks_contain_block( $blocks, $slider_block_name ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'qi_blocks_content_contains_swiper_markup' ) ) {
	/**
	 * Whether content includes rendered Qi slider markup.
	 *
	 * @param string $content Optional content string.
	 *
	 * @return bool
	 */
	function qi_blocks_content_contains_swiper_markup( $content = '' ) {
		if ( is_string( $content ) && '' !== $content ) {
			return false !== strpos( $content, 'qodef-block-swiper' );
		}

		$page_id = get_queried_object_id();

		if ( $page_id ) {
			$post_content = get_post_field( 'post_content', $page_id );

			if ( false !== strpos( $post_content, 'qodef-block-swiper' ) ) {
				return true;
			}
		}

		if ( function_exists( 'qi_blocks_get_block_template_content' ) && qi_blocks_should_resolve_block_template_html() ) {
			$template_content = qi_blocks_get_block_template_content();

			foreach ( qi_blocks_get_slider_block_names() as $slider_block_name ) {
				if ( qi_blocks_content_contains_block_reference( $template_content, $slider_block_name ) ) {
					return true;
				}
			}

			if ( function_exists( 'parse_blocks' ) && qi_blocks_parsed_blocks_contain_slider_block( parse_blocks( $template_content ) ) ) {
				return true;
			}
		}

		$widgets_block = get_option( 'widget_block' );

		if ( ! empty( $widgets_block ) && is_array( $widgets_block ) ) {
			foreach ( $widgets_block as $widget_block ) {
				if ( isset( $widget_block['content'] ) && false !== strpos( $widget_block['content'], 'qodef-block-swiper' ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'qi_blocks_page_needs_swiper' ) ) {
	/**
	 * Whether the current request needs the Swiper library.
	 *
	 * @return bool
	 */
	function qi_blocks_page_needs_swiper() {
		static $needs_swiper = null;

		if ( null !== $needs_swiper ) {
			return $needs_swiper;
		}

		$needs_swiper = false;

		foreach ( qi_blocks_get_slider_block_names() as $slider_block_name ) {
			if ( qi_blocks_is_block_present_on_page( $slider_block_name ) ) {
				$needs_swiper = true;

				return $needs_swiper;
			}
		}

		if ( qi_blocks_content_contains_swiper_markup() ) {
			$needs_swiper = true;
		}

		return $needs_swiper;
	}
}

if ( ! function_exists( 'qi_blocks_should_enqueue_third_party_script' ) ) {
	/**
	 * Whether a shared 3rd party script should be enqueued.
	 *
	 * @param string $script_key   Script handle.
	 * @param array  $script_value Script config.
	 * @param bool   $editor_mode  Whether the editor is loading assets.
	 *
	 * @return bool
	 */
	function qi_blocks_should_enqueue_third_party_script( $script_key, $script_value, $editor_mode = false ) {
		if ( $editor_mode ) {
			return true;
		}

		if ( 'swiper' === $script_key ) {
			return qi_blocks_page_needs_swiper();
		}

		$block_names = array();

		if ( ! empty( $script_value['block_names'] ) && is_array( $script_value['block_names'] ) ) {
			$block_names = $script_value['block_names'];
		} elseif ( ! empty( $script_value['block_name'] ) ) {
			$block_names = array( $script_value['block_name'] );
		}

		if ( empty( $block_names ) ) {
			return true;
		}

		foreach ( $block_names as $block_name ) {
			if ( qi_blocks_is_block_present_on_page( $block_name ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'qi_blocks_should_resolve_block_template_html' ) ) {
	/**
	 * Whether block template content should be resolved for frontend asset loading.
	 *
	 * @return bool
	 */
	function qi_blocks_should_resolve_block_template_html() {
		$should_resolve = function_exists( 'wp_is_block_theme' ) && wp_is_block_theme();

		return (bool) apply_filters( 'qi_blocks_should_resolve_block_template_html', $should_resolve );
	}
}

if ( ! function_exists( 'qi_blocks_get_block_template_content' ) ) {
	/**
	 * Return raw block template content for the current request.
	 *
	 * Does not render blocks — safe to call during wp_enqueue_scripts.
	 *
	 * @return string
	 */
	function qi_blocks_get_block_template_content() {
		static $request_cache = null;

		if ( null !== $request_cache ) {
			return $request_cache;
		}

		if ( ! qi_blocks_should_resolve_block_template_html() ) {
			$request_cache = '';

			return $request_cache;
		}

		global $_wp_current_template_content;

		if ( empty( $_wp_current_template_content ) || ! is_string( $_wp_current_template_content ) ) {
			$request_cache = '';

			return $request_cache;
		}

		$request_cache = $_wp_current_template_content;

		return $request_cache;
	}
}

if ( ! function_exists( 'qi_blocks_get_the_block_template_html' ) ) {
	/**
	 * Backward-compatible alias for raw template content used in asset detection.
	 *
	 * @deprecated Never calls get_the_block_template_html() — rendering blocks during asset detection caused duplicate script loads in the Site Editor.
	 *
	 * @return string Raw block template content.
	 */
	function qi_blocks_get_the_block_template_html() {
		return qi_blocks_get_block_template_content();
	}
}

if ( ! function_exists( 'qi_blocks_cleanup_legacy_block_template_transient' ) ) {
	/**
	 * Remove legacy transient key that contained a trailing space.
	 *
	 * @return void
	 */
	function qi_blocks_cleanup_legacy_block_template_transient() {
		delete_transient( '_qi_blocks_get_the_block_template_html' );
		delete_transient( '_qi_blocks_get_the_block_template_html ' );
	}

	add_action( 'init', 'qi_blocks_cleanup_legacy_block_template_transient', 1 );
}

if ( ! function_exists( 'qi_blocks_get_premium_blocks_list' ) ) {
	/**
	 * Function that return premium blocks list
	 *
	 * @return array
	 */
	function qi_blocks_get_premium_blocks_list() {
		$block_status = apply_filters( 'qi_blocks_filter_block_status', false );
		$premium_flag = qi_blocks_is_installed( 'premium' ) && $block_status;

		if ( $premium_flag ) {
			return array();
		}

		return array(
			'add-to-cart-button'        => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Add to Cart Button', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'WooCommerce', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/add-to-cart-button/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#add_to_cart_button',
			),
			'advanced-navigation'       => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Advanced Navigation', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Showcase/Presentational', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/advanced-navigation/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#advanced_navigation',
			),
			'animated-text'             => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Animated Text', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Typography', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/animated-text/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#animated_text',
			),
			'blockquote'                => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Blockquote', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Typography', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/blockquote/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#blockquote',
			),
			'blog-slider'               => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Blog Carousel', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Business', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/blog-carousel/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#blog_carousel',
			),
			'business-hours'            => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Working Hours', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Business', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/working-hours/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#working_hours',
			),
			'cards-slider'              => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Cards Slider', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Showcase/Presentational', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/cards-slider/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#cards_slider',
			),
			'charts'                    => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Pie and Donut Charts', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Infographics', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/pie-and-donut-charts/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#pie_and_donut_charts',
			),
			'clients-slider'            => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Clients Carousel', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Business', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/clients-carousel/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#clients_carousel',
			),
			'content-menu'              => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Content Menu', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Showcase/Presentational', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/content-menu/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#content_menu',
			),
			'data-table'                => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Data Table', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Business', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/data-table/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#data_table',
			),
			'device-carousel'           => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Device Frame Carousel', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Creative', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/device-frame-carousel/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#device_frame_carousel',
			),
			'device-slider'             => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Device Frame Slider', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Creative', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/device-frame-slider/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#device_frame_slider',
			),
			'dropcaps'                  => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Drop Caps', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Typography', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/drop-caps/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#drop_caps',
			),
			'dual-image-with-content'   => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Dual Image with Content', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Showcase/Presentational', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/dual-image-with-content/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#dual_image_with_content',
			),
			'google-map'                => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Google Map', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Business', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/google-map/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#google_map',
			),
			'graphs'                    => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Graphs', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Infographics', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/graphs/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#graphs',
			),
			'highlight'                 => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Highlighted Text', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Typography', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/highlighted-text/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#highlighted_text',
			),
			'image-hotspots'            => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Image Hotspots', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Showcase/Presentational', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/image-hotspots/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#image_hotspots',
			),
			'info-button'               => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Info Button', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Typography', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/info-button/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#info_button',
			),
			'interactive-banner'        => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Interactive Banners', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Business', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/interactive-banners/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#interactive_banner',
			),
			'interactive-link-showcase' => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Interactive Links', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Creative', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/interactive-links/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#interactive_link_showcase',
			),
			'item-showcase'             => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Item Showcase', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Showcase/Presentational', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/item-showcase/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#item_showcase',
			),
			'preview-slider'            => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Preview Slider', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Creative', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/preview-slider/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#preview_slider',
			),
			'pricing-list'              => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Pricing List', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Business', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/pricing-list/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#pricing_list',
			),
			'product-category-list'     => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Product Category List', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'WooCommerce', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/product-category-list/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#product_category_list',
			),
			'product-slider'            => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Product Slider', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'WooCommerce', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/product-slider/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#product_slider',
			),
			'rating'                    => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Rating', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'WooCommerce', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/rating/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#rating',
			),
			'slider-switch'             => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Slider Switch', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Creative', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/slider-switch/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#slider_switch',
			),
			'testimonials-slider'       => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Testimonials Carousel', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Business', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/testimonials-carousel/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#testimonials_carousel',
			),
			'typeout-text'              => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Typeout Text', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Typography', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/typeout-text/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#typeout_text',
			),
			'wp-forms'                  => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'WPForms', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Form Style', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/wpforms/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#wp_forms',
			),
			'before-after'              => array(
				'type'          => 'premium',
				'title'         => esc_attr__( 'Before/After Comparison Slider', 'qi-blocks' ),
				'subcategory'   => esc_attr__( 'Showcase/Presentational', 'qi-blocks' ),
				'demo'          => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/before-after-comparison-slider/',
				'documentation' => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/#before_after_comparison_slider',
			),
		);
	}
}

if ( ! function_exists( 'qi_blocks_add_additional_options_for_advanced_block_panel' ) ) {
	/**
	 * Function that localize main editor js script with additional blocks feature
	 *
	 * @param array $global
	 *
	 * @return array
	 */
	function qi_blocks_add_additional_options_for_advanced_block_panel( $global ) {
		$global['advancedBlockPanel'] = array(
			'help' => array(
				0 => array(
					'title' => esc_attr__( 'Block Showcase', 'qi-blocks' ),
					'link'  => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/',
				),
				1 => array(
					'title' => esc_attr__( 'Live Demos', 'qi-blocks' ),
					'link'  => 'https://qodeinteractive.com/qi-templates?utm_source=dash&utm_medium=qitemplates&utm_campaign=gopremium',
				),
				2 => array(
					'title' => esc_attr__( 'Documentation', 'qi-blocks' ),
					'link'  => 'https://qodeinteractive.com/qi-blocks-for-gutenberg/documentation/',
				),
				3 => array(
					'title' => esc_attr__( 'Video Tutorial', 'qi-blocks' ),
					'link'  => 'https://www.youtube.com/watch?v=m9beJAnVCnI&list=PLNypD600o6nIILMn287UeeRWsfc8lyiPa',
				),
				4 => array(
					'title' => esc_attr__( 'Help Center', 'qi-blocks' ),
					'link'  => 'https://helpcenter.qodeinteractive.com/',
				),
			),
			'features' => array(
				0 => array(
					'title' => esc_attr__( 'Premium Qi Blocks for Gutenberg', 'qi-blocks' ),
					'link'  => 'https://qodeinteractive.com/pricing/?qi_product=blocks?utm_source=dash&utm_medium=qiblockspro&utm_campaign=gopremium',
					'image' => esc_url( QI_BLOCKS_ADMIN_URL_PATH . '/admin-pages/assets/img/features-qi-blocks-premium.jpg' ),
				),
				1 => array(
					'title' => esc_attr__( 'Qi Templates for Gutenberg', 'qi-blocks' ),
					'link'  => 'https://qodeinteractive.com/qi-templates?utm_source=dash&utm_medium=qitemplates&utm_campaign=gopremium',
					'image' => esc_url( QI_BLOCKS_ADMIN_URL_PATH . '/admin-pages/assets/img/features-qi-templates.jpg' ),
				),
			),
			'blocks' => Qi_Blocks_Blocks_List::get_instance()->get_blocks(),
		);

		return $global;
	}

	add_filter( 'qi_blocks_filter_localize_main_editor_js', 'qi_blocks_add_additional_options_for_advanced_block_panel' );
}
