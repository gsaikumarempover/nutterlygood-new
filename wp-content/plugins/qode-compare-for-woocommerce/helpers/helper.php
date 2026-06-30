<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_is_installed' ) ) {
	/**
	 * Function check is some plugin is installed
	 *
	 * @param string $plugin name
	 *
	 * @return bool
	 */
	function qode_compare_for_woocommerce_is_installed( $plugin ) {
		switch ( $plugin ) :
			case 'compare-premium':
				return defined( 'QODE_COMPARE_FOR_WOOCOMMERCE_PREMIUM_VERSION' );
			case 'gutenberg-page':
				$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : array();
				return method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor();
			case 'gutenberg-editor':
				return class_exists( 'WP_Block_Type' );
			case 'wpbakery':
				return class_exists( 'WPBakeryVisualComposerAbstract' );
			case 'elementor':
				return defined( 'ELEMENTOR_VERSION' );
			case 'revolution-slider':
				return class_exists( 'RevSliderFront' );
			case 'woocommerce':
				return class_exists( 'WooCommerce' );
			case 'contact_form_7':
				return defined( 'WPCF7_VERSION' );
			case 'wp_forms':
				return defined( 'WPFORMS_VERSION' );
			case 'wpml':
				return defined( 'ICL_SITEPRESS_VERSION' );
			default:
				return apply_filters( 'qode_compare_for_woocommerce_filter_is_plugin_installed', false, $plugin );
		endswitch;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_execute_template_with_params' ) ) {
	/**
	 * Loads module template part.
	 *
	 * @param string $template path to template that is going to be included
	 * @param array $params params that are passed to template
	 *
	 * @return string - template html
	 */
	function qode_compare_for_woocommerce_execute_template_with_params( $template, $params ) {
		if ( ! empty( $template ) && file_exists( $template ) ) {
			// Extract params so they could be used in template.
			if ( is_array( $params ) && count( $params ) ) {
				extract( $params, EXTR_SKIP ); // @codingStandardsIgnoreLine
			}

			ob_start();
			include $template;
			$html = ob_get_clean();

			return $html;
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_sanitize_module_template_part' ) ) {
	/**
	 * Sanitize module template part.
	 *
	 * @param string $template temp path to file that is being loaded
	 *
	 * @return string - string with template path
	 */
	function qode_compare_for_woocommerce_sanitize_module_template_part( $template ) {
		$available_characters = '/[^A-Za-z0-9\_\-\/]/';

		if ( ! empty( $template ) && is_scalar( $template ) ) {
			$template = preg_replace( $available_characters, '', $template );
		} else {
			$template = '';
		}

		return $template;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_template_with_slug' ) ) {
	/**
	 * Loads module template part.
	 *
	 * @param string $temp temp path to file that is being loaded
	 * @param string $slug slug that should be checked if exists
	 *
	 * @return string - string with template path
	 */
	function qode_compare_for_woocommerce_get_template_with_slug( $temp, $slug ) {
		$template = '';

		if ( ! empty( $temp ) ) {
			$slug = qode_compare_for_woocommerce_sanitize_module_template_part( $slug );

			if ( ! empty( $slug ) ) {
				$template = "$temp-$slug.php";

				if ( ! file_exists( $template ) ) {
					$template = $temp . '.php';
				}
			} else {
				$template = $temp . '.php';
			}
		}

		return $template;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_template_part' ) ) {
	/**
	 * Loads module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 *
	 * @return string - string containing html of template
	 */
	function qode_compare_for_woocommerce_get_template_part( $module, $template, $slug = '', $params = array() ) {
		$module   = qode_compare_for_woocommerce_sanitize_module_template_part( $module );
		$template = qode_compare_for_woocommerce_sanitize_module_template_part( $template );

		$temp = QODE_COMPARE_FOR_WOOCOMMERCE_INC_PATH . '/' . $module . '/' . $template;

		$template = qode_compare_for_woocommerce_get_template_with_slug( $temp, $slug );

		return qode_compare_for_woocommerce_execute_template_with_params( $template, $params );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_template_part' ) ) {
	/**
	 * Echo module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 */
	function qode_compare_for_woocommerce_template_part( $module, $template, $slug = '', $params = array() ) {
		$module_template_part = qode_compare_for_woocommerce_get_template_part( $module, $template, $slug, $params );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_compare_for_woocommerce_framework_wp_kses_html( 'html', $module_template_part );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_option_value' ) ) {
	/**
	 * Function that returns option value using framework function but providing its own scope
	 *
	 * @param string $type option type
	 * @param string $name name of option
	 * @param string $default_value option default value
	 * @param int $post_id id of
	 *
	 * @return string value of option
	 */
	function qode_compare_for_woocommerce_get_option_value( $type, $name, $default_value = '', $post_id = null ) {
		$scope = QODE_COMPARE_FOR_WOOCOMMERCE_OPTIONS_NAME;

		return qode_compare_for_woocommerce_framework_get_option_value( $scope, $type, $name, $default_value, $post_id );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_post_value_through_levels' ) ) {
	/**
	 * Function that returns meta value if exists, otherwise global value using framework function but providing its own scope
	 *
	 * @param string $name name of option
	 * @param int $post_id id of
	 *
	 * @return string|array value of option
	 */
	function qode_compare_for_woocommerce_get_post_value_through_levels( $name, $post_id = null ) {
		$scope = QODE_COMPARE_FOR_WOOCOMMERCE_OPTIONS_NAME;

		return qode_compare_for_woocommerce_framework_get_post_value_through_levels( $scope, $name, $post_id );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_render_svg_icon' ) ) {
	/**
	 * Function that print svg html icon
	 *
	 * @param string $name - icon name
	 * @param string $class_name - custom html tag class name
	 */
	function qode_compare_for_woocommerce_render_svg_icon( $name, $class_name = '' ) {
		$svg_template_part = qode_compare_for_woocommerce_get_svg_icon( $name, $class_name );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_compare_for_woocommerce_framework_wp_kses_html( 'html', $svg_template_part );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_svg_icon' ) ) {
	/**
	 * Returns svg html
	 *
	 * @param string $name - icon name
	 * @param string $class_name - custom html tag class name
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_get_svg_icon( $name, $class_name = '' ) {
		$html  = '';
		$class = 'qcfw-svg--' . $name;
		$class = isset( $class_name ) && ! empty( $class_name ) ? $class . ' ' . $class_name : $class;

		switch ( $name ) {
			case 'trash':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" width="12.25" height="14" viewBox="0 0 12.25 14"><path d="M12.25,2.625v.4375a.4374.4374,0,0,1-.4375.4375H11.375v9.1875A1.3128,1.3128,0,0,1,10.0625,14H2.1875A1.3128,1.3128,0,0,1,.875,12.6875V3.5H.4375A.4374.4374,0,0,1,0,3.0625V2.625a.4374.4374,0,0,1,.4375-.4375H2.6909l.93-1.55A1.4556,1.4556,0,0,1,4.7466,0H7.5039A1.4556,1.4556,0,0,1,8.6294.6372l.93,1.55h2.2534A.4374.4374,0,0,1,12.25,2.625ZM10.0625,3.5H2.1875v9.1875h7.875Zm-6.125,7.5469V5.1406a.3282.3282,0,0,1,.3281-.3281h.6563a.3282.3282,0,0,1,.3281.3281v5.9063a.3282.3282,0,0,1-.3281.3281H4.2656A.3282.3282,0,0,1,3.9375,11.0469Zm.2842-8.8594H8.0283l-.4775-.7954a.1818.1818,0,0,0-.1406-.08H4.8394a.1818.1818,0,0,0-.1406.08ZM7,11.0469V5.1406a.3282.3282,0,0,1,.3281-.3281h.6563a.3282.3282,0,0,1,.3281.3281v5.9063a.3282.3282,0,0,1-.3281.3281H7.3281A.3282.3282,0,0,1,7,11.0469Z"></path></svg>';
				break;
			case 'search':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20"><path d="M18.869 19.162l-5.943-6.484c1.339-1.401 2.075-3.233 2.075-5.178 0-2.003-0.78-3.887-2.197-5.303s-3.3-2.197-5.303-2.197-3.887 0.78-5.303 2.197-2.197 3.3-2.197 5.303 0.78 3.887 2.197 5.303 3.3 2.197 5.303 2.197c1.726 0 3.362-0.579 4.688-1.645l5.943 6.483c0.099 0.108 0.233 0.162 0.369 0.162 0.121 0 0.242-0.043 0.338-0.131 0.204-0.187 0.217-0.503 0.031-0.706zM1 7.5c0-3.584 2.916-6.5 6.5-6.5s6.5 2.916 6.5 6.5-2.916 6.5-6.5 6.5-6.5-2.916-6.5-6.5z"></path></svg>';
				break;
			case 'spinner':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"></path></svg>';
				break;
			case 'close':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18.1213 18.1213" stroke-miterlimit="10" stroke-width="2"><line x1="1.0607" y1="1.0607" x2="17.0607" y2="17.0607"/><line x1="17.0607" y1="1.0607" x2="1.0607" y2="17.0607"/></svg>';
				break;
			case 'compare':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="15px" height="14px" viewBox="0 0 15 14" fill="currentColor"><g><path d="M12.4,3.8L11.4,5c-0.3,0.3-0.3,0.7,0,1c0.2,0.3,0.6,0.3,0.9,0c0,0,0,0,0,0l2.2-2.4 c0.3-0.3,0.3-0.7,0-1l-2.2-2.4c-0.2-0.3-0.6-0.3-0.9,0c0,0,0,0,0,0c-0.3,0.3-0.3,0.7,0,1l1.1,1.2h-1.1c-1.3,0-3,1.8-5,3.8 C4.7,8,2.7,10.1,1.6,10.1h-1c-0.4,0-0.7,0.4-0.7,0.8c0,0.4,0.3,0.6,0.7,0.7h1c1.6,0,3.5-2.1,5.6-4.2c1.5-1.5,3.3-3.4,4.1-3.4 L12.4,3.8L12.4,3.8z"/><path d="M12.2,8c-0.3-0.3-0.7-0.3-1,0c-0.3,0.3-0.3,0.7,0,1l1.1,1.1h-1.1c-0.7,0-1.7-1-2.7-1.8 C8.3,8,7.9,8.1,7.6,8.4C7.4,8.6,7.4,9,7.6,9.3c1.5,1.4,2.6,2.2,3.6,2.2h1.1l-1.1,1.1c-0.3,0.3-0.2,0.7,0,1c0.3,0.3,0.7,0.3,0.9,0 l2.3-2.3c0.3-0.3,0.3-0.7,0-1L12.2,8z"/><path d="M0.7,3.8h1c0.8,0,2,1,2.9,1.8c0.3,0.2,0.7,0.2,1-0.1c0.2-0.3,0.2-0.6,0-0.9 C4.1,3.3,2.9,2.4,1.7,2.4h-1C0.3,2.4,0,2.7,0,3.1C0,3.5,0.3,3.8,0.7,3.8L0.7,3.8z"/></g></svg>';
				break;
		}

		return apply_filters( 'qode_compare_for_woocommerce_filter_svg_icon', $html, $name, $class_name );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_class_attribute' ) ) {
	/**
	 * Function that echoes class attribute
	 *
	 * @param string|array $value - value of class attribute
	 *
	 * @see qode_compare_for_woocommerce_get_class_attribute()
	 */
	function qode_compare_for_woocommerce_class_attribute( $value ) {
		echo wp_kses_post( qode_compare_for_woocommerce_get_class_attribute( $value ) );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_class_attribute' ) ) {
	/**
	 * Function that returns generated class attribute
	 *
	 * @param string|array $value - value of class attribute
	 *
	 * @return string generated class attribute
	 *
	 * @see qode_compare_for_woocommerce_get_inline_attr()
	 */
	function qode_compare_for_woocommerce_get_class_attribute( $value ) {
		return qode_compare_for_woocommerce_get_inline_attr( $value, 'class', ' ' );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_id_attribute' ) ) {
	/**
	 * Function that echoes id attribute
	 *
	 * @param string|array $value - value of id attribute
	 *
	 * @see qode_compare_for_woocommerce_get_id_attribute()
	 */
	function qode_compare_for_woocommerce_id_attribute( $value ) {
		echo wp_kses_post( qode_compare_for_woocommerce_get_id_attribute( $value ) );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_id_attribute' ) ) {
	/**
	 * Function that returns generated id attribute
	 *
	 * @param string|array $value - value of id attribute
	 *
	 * @return string generated id attribute
	 *
	 * @see qode_compare_for_woocommerce_get_inline_attr()
	 */
	function qode_compare_for_woocommerce_get_id_attribute( $value ) {
		return qode_compare_for_woocommerce_get_inline_attr( $value, 'id', ' ' );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_inline_style' ) ) {
	/**
	 * Function that echoes generated style attribute
	 *
	 * @param string|array $value - attribute value
	 *
	 * @see qode_compare_for_woocommerce_get_inline_style()
	 */
	function qode_compare_for_woocommerce_inline_style( $value ) {
		$inline_style_part = qode_compare_for_woocommerce_get_inline_style( $value );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_compare_for_woocommerce_framework_wp_kses_html( 'attributes', $inline_style_part );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_inline_style' ) ) {
	/**
	 * Function that generates style attribute and returns generated string
	 *
	 * @param string|array $value - value of style attribute
	 *
	 * @return string generated style attribute
	 *
	 * @see qode_compare_for_woocommerce_get_inline_style()
	 */
	function qode_compare_for_woocommerce_get_inline_style( $value ) {
		return qode_compare_for_woocommerce_get_inline_attr( $value, 'style', ';' );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_inline_attrs' ) ) {
	/**
	 * Echo multiple inline attributes
	 *
	 * @param array $attrs
	 * @param bool $allow_zero_values
	 */
	function qode_compare_for_woocommerce_inline_attrs( $attrs, $allow_zero_values = false ) {
		$inline_attrs_part = qode_compare_for_woocommerce_get_inline_attrs( $attrs, $allow_zero_values );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_compare_for_woocommerce_framework_wp_kses_html( 'attributes', $inline_attrs_part );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_inline_attrs' ) ) {
	/**
	 * Generate multiple inline attributes
	 *
	 * @param array $attrs
	 * @param bool $allow_zero_values
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_get_inline_attrs( $attrs, $allow_zero_values = false ) {
		$output = '';
		if ( is_array( $attrs ) && count( $attrs ) ) {
			if ( $allow_zero_values ) {
				foreach ( $attrs as $attr => $value ) {
					$output .= ' ' . qode_compare_for_woocommerce_get_inline_attr( $value, $attr, '', true );
				}
			} else {
				foreach ( $attrs as $attr => $value ) {
					$output .= ' ' . qode_compare_for_woocommerce_get_inline_attr( $value, $attr );
				}
			}
		}

		$output = ltrim( $output );

		return $output;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_inline_attr' ) ) {
	/**
	 * Function that generates html attribute
	 *
	 * @param string|array $value value of html attribute
	 * @param string $attr - name of html attribute to generate
	 * @param string $glue - glue with which to implode $attr. Used only when $attr is array
	 */
	function qode_compare_for_woocommerce_inline_attr( $value, $attr, $glue = '' ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_compare_for_woocommerce_get_inline_attr( $value, $attr, $glue );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_inline_attr' ) ) {
	/**
	 * Function that generates html attribute
	 *
	 * @param string|array $value value of html attribute
	 * @param string $attr - name of html attribute to generate
	 * @param string $glue - glue with which to implode $attr. Used only when $attr is arrayed
	 * @param bool $allow_zero_values - allow data to have zero value
	 *
	 * @return string generated html attribute
	 */
	function qode_compare_for_woocommerce_get_inline_attr( $value, $attr, $glue = '', $allow_zero_values = false ) {
		if ( $allow_zero_values ) {
			if ( '' !== $value ) {

				if ( is_array( $value ) && count( $value ) ) {
					$properties = implode( $glue, $value );
				} else {
					$properties = $value;
				}

				return $attr . '="' . esc_attr( $properties ) . '"';
			}
		} else {
			if ( ! empty( $value ) ) {

				if ( is_array( $value ) && count( $value ) ) {
					$properties = implode( $glue, $value );
				} elseif ( '' !== $value ) {
					$properties = $value;
				} else {
					return '';
				}

				return $attr . '="' . esc_attr( $properties ) . '"';
			}
		}

		return '';
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_dynamic_style' ) ) {
	/**
	 * Outputs css based on passed selectors and properties
	 *
	 * @param array|string $selector
	 * @param array $properties
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_dynamic_style( $selector, $properties ) {
		$output = '';
		// check if selector and rules are valid data.
		if ( ! empty( $selector ) && ( is_array( $properties ) && count( $properties ) ) ) {

			if ( is_array( $selector ) && count( $selector ) ) {
				$output .= implode( ', ', $selector );
			} else {
				$output .= $selector;
			}

			$output .= ' { ';
			foreach ( $properties as $prop => $value ) {
				if ( '' !== $prop ) {

					if ( 'font-family' === $prop ) {
						$output .= $prop . ': "' . esc_attr( $value ) . '";';
					} else {
						$output .= $prop . ': ' . esc_attr( $value ) . ';';
					}
				}
			}

			$output .= '}';
		}

		return $output;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_dynamic_style_responsive' ) ) {
	/**
	 * Outputs css based on passed selectors and properties
	 *
	 * @param array|string $selector
	 * @param array $properties
	 * @param string $min_width
	 * @param string $max_width
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_dynamic_style_responsive( $selector, $properties, $min_width = '', $max_width = '' ) {
		$output = '';
		// check if min width or max width is set.
		if ( ! empty( $min_width ) || ! empty( $max_width ) ) {
			$output .= '@media only screen';

			if ( ! empty( $min_width ) ) {
				$output .= ' and (min-width: ' . $min_width . 'px)';
			}

			if ( ! empty( $max_width ) ) {
				$output .= ' and (max-width: ' . $max_width . 'px)';
			}

			$output .= ' { ';

			$output .= qode_compare_for_woocommerce_dynamic_style( $selector, $properties );

			$output .= '}';
		}

		return $output;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_attachment_id_from_url' ) ) {
	/**
	 * Function that retrieves attachment id for passed attachment url
	 *
	 * @param string $attachment_url
	 *
	 * @return null|string
	 */
	function qode_compare_for_woocommerce_get_attachment_id_from_url( $attachment_url ) {
		global $wpdb;
		$attachment_id = '';

		if ( '' !== $attachment_url ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid=%s", $attachment_url ) );

			// Additional check for undefined reason when guid is not image src.
			if ( empty( $attachment_id ) ) {
				$modified_url = substr( $attachment_url, strrpos( $attachment_url, '/' ) + 1 );

				// get attachment id.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value LIKE %s", '%' . $modified_url . '%' ) );
			}
		}

		return $attachment_id;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_resize_image' ) ) {
	/**
	 * Function that generates custom thumbnail for given attachment
	 *
	 * @param int|string $attachment - attachment id or url of image to resize
	 * @param int $width desired - height of custom thumbnail
	 * @param int $height desired - width of custom thumbnail
	 * @param bool $crop - whether to crop image or not
	 *
	 * @return array returns array containing img_url, width and height
	 *
	 * @see qode_compare_for_woocommerce_get_attachment_id_from_url()
	 * @see get_attached_file()
	 * @see wp_get_attachment_url()
	 * @see wp_get_image_editor()
	 */
	function qode_compare_for_woocommerce_resize_image( $attachment, $width = null, $height = null, $crop = true ) {
		$return_array = array();

		if ( ! empty( $attachment ) ) {
			if ( is_int( $attachment ) ) {
				$attachment_id = $attachment;
			} else {
				$attachment_id = qode_compare_for_woocommerce_get_attachment_id_from_url( $attachment );
			}

			if ( ! empty( $attachment_id ) && ( isset( $width ) && isset( $height ) ) ) {

				// get file path of the attachment.
				$img_path = get_attached_file( $attachment_id );

				// get attachment url.
				$img_url = wp_get_attachment_url( $attachment_id );

				// break down img path to array, so we can use its components in building thumbnail path.
				$img_path_array = pathinfo( $img_path );

				// build thumbnail path.
				$new_img_path = $img_path_array['dirname'] . '/' . $img_path_array['filename'] . '-' . $width . 'x' . $height . '.' . $img_path_array['extension'];

				// build thumbnail url.
				$new_img_url = str_replace( $img_path_array['filename'], $img_path_array['filename'] . '-' . $width . 'x' . $height, $img_url );

				// check if thumbnail exists by its path.
				if ( ! file_exists( $new_img_path ) ) {
					// get image manipulation object.
					$image_object = wp_get_image_editor( $img_path );

					if ( ! is_wp_error( $image_object ) ) {
						// resize image and save it new to path.
						$image_object->resize( $width, $height, $crop );
						$image_object->save( $new_img_path );

						// get sizes of newly created thumbnail.
						// we don't use $width and $height because those might differ from end result based on $crop parameter.
						$image_sizes = $image_object->get_size();

						$width  = $image_sizes['width'];
						$height = $image_sizes['height'];
					}
				}

				// generate data to be returned.
				$return_array = array(
					'img_url'    => $new_img_url,
					'img_width'  => $width,
					'img_height' => $height,
				);

				// attachment wasn't found in gallery, but it is not empty.
			} elseif ( '' !== $attachment && ( isset( $width ) && isset( $height ) ) ) {
				// generate data to be returned.
				$return_array = array(
					'img_url'    => $attachment,
					'img_width'  => $width,
					'img_height' => $height,
				);
			}
		}

		return $return_array;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_generate_thumbnail' ) ) {
	/**
	 * Generates thumbnail img tag. It calls qode_compare_for_woocommerce_resize_image function for resizing image
	 *
	 * @param int|string $attachment - attachment id or url to generate thumbnail from
	 * @param int $width - width of thumbnail
	 * @param int $height - height of thumbnail
	 * @param bool $crop - whether to crop thumbnail or not
	 *
	 * @return string generated img tag
	 *
	 * @see qode_compare_for_woocommerce_resize_image()
	 * @see qode_compare_for_woocommerce_get_attachment_id_from_url()
	 */
	function qode_compare_for_woocommerce_generate_thumbnail( $attachment, $width = null, $height = null, $crop = true ) {
		if ( ! empty( $attachment ) ) {
			if ( is_int( $attachment ) ) {
				$attachment_id = $attachment;
			} else {
				$attachment_id = qode_compare_for_woocommerce_get_attachment_id_from_url( $attachment );
			}
			$img_info = qode_compare_for_woocommerce_resize_image( $attachment_id, $width, $height, $crop );
			$img_alt  = ! empty( $attachment_id ) ? get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) : '';

			if ( is_array( $img_info ) && count( $img_info ) ) {
				$url            = esc_url( $img_info['img_url'] );
				$attr           = array();
				$attr['alt']    = esc_attr( $img_alt );
				$attr['width']  = esc_attr( $img_info['img_width'] );
				$attr['height'] = esc_attr( $img_info['img_height'] );

				return qode_compare_for_woocommerce_get_image_html_from_src( $url, $attr );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_image_html_from_src' ) ) {
	/**
	 * Function that returns image tag from url and it's attributes.
	 *
	 * @param string $url
	 * @param array $attr
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_get_image_html_from_src( $url, $attr = array() ) {
		$html = '';

		if ( ! empty( $url ) ) {
			$html .= '<img src="' . esc_url( $url ) . '"';

			if ( ! empty( $attr ) ) {
				foreach ( $attr as $name => $value ) {
					$html .= ' ' . $name . '="' . $value . '"';
				}
			}

			$html .= ' />';
		}

		return $html;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_ajax_status' ) ) {
	/**
	 * Function that return status from ajax functions
	 *
	 * @param string $status - success or error
	 * @param string $message - ajax message value
	 * @param string|array $data - returned value
	 * @param string $redirect - url address
	 */
	function qode_compare_for_woocommerce_get_ajax_status( $status, $message, $data = null, $redirect = '' ) {
		$response = array(
			'status'   => esc_attr( $status ),
			'message'  => esc_html( $message ),
			'data'     => $data,
			'redirect' => ! empty( $redirect ) ? esc_url( $redirect ) : '',
		);

		$output = wp_json_encode( $response );

		exit( $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_call_shortcode' ) ) {
	/**
	 * Function that call/render shortcode
	 *
	 * @param      $base - shortcode base
	 * @param      $params - shortcode parameters
	 * @param null $content - shortcode content
	 *
	 * @return mixed|string
	 */
	function qode_compare_for_woocommerce_call_shortcode( $base, $params = array(), $content = null ) {
		global $shortcode_tags;

		if ( ! isset( $shortcode_tags[ $base ] ) ) {
			return false;
		}

		if ( is_array( $shortcode_tags[ $base ] ) ) {
			$shortcode = $shortcode_tags[ $base ];

			return call_user_func(
				array(
					$shortcode[0],
					$shortcode[1],
				),
				$params,
				$content,
				$base
			);
		}

		return call_user_func( $shortcode_tags[ $base ], $params, $content, $base );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_button_classes' ) ) {
	/**
	 * Function that return theme and plugin classes for button elements
	 *
	 * @param array $additional_classes
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_get_button_classes( $additional_classes = array() ) {
		$classes = array(
			'button',
		);

		if ( function_exists( 'wc_wp_theme_get_element_class_name' ) ) {
			$classes[] = wc_wp_theme_get_element_class_name( 'button' );
		}

		return implode( ' ', array_merge( $classes, $additional_classes ) );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_formatted_font_family' ) ) {
	/**
	 * Function that returns formatted font family name
	 *
	 * @param string $value
	 * @param bool $reverse
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_get_formatted_font_family( $value, $reverse = false ) {
		return $reverse ? str_replace( ' ', '+', $value ) : str_replace( '+', ' ', $value );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_string_ends_with' ) ) {
	/**
	 * Checks if $haystack ends with $needle and returns proper bool value
	 *
	 * @param string $haystack - to check
	 * @param string $needle - on end to match
	 *
	 * @return bool
	 */
	function qode_compare_for_woocommerce_string_ends_with( $haystack, $needle ) {
		if ( '' !== $haystack && '' !== $needle ) {
			return ( substr( $haystack, - strlen( $needle ), strlen( $needle ) ) === $needle );
		}

		return false;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_string_ends_with_allowed_units' ) ) {
	/**
	 * Checks if $haystack ends with predefined needles and returns proper bool value
	 *
	 * @param string $haystack - to check
	 *
	 * @return bool
	 */
	function qode_compare_for_woocommerce_string_ends_with_allowed_units( $haystack ) {
		$result  = false;
		$needles = array( 'px', '%', 'em', 'rem', 'vh', 'vw', ')' );

		if ( '' !== $haystack ) {
			foreach ( $needles as $needle ) {
				if ( qode_compare_for_woocommerce_string_ends_with( $haystack, $needle ) ) {
					$result = true;
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_select_type_options_pool' ) ) {
	/**
	 * Function that returns array with pool of options for select fields in framework
	 *
	 * @param string $type           - type of select field
	 * @param bool   $enable_default - add first element empty for default value
	 * @param array  $exclude        - array of items to exclude
	 * @param array  $include        - array of items to include
	 *
	 * @return array escaped output
	 */
	function qode_compare_for_woocommerce_get_select_type_options_pool( $type, $enable_default = true, $exclude = array(), $include = array() ) {
		$options = array();

		if ( $enable_default ) {
			$options[''] = esc_html__( 'Default', 'qode-compare-for-woocommerce' );
		}

		switch ( $type ) {
			case 'title_tag':
				$options['h1'] = esc_html__( 'H1', 'qode-compare-for-woocommerce' );
				$options['h2'] = esc_html__( 'H2', 'qode-compare-for-woocommerce' );
				$options['h3'] = esc_html__( 'H3', 'qode-compare-for-woocommerce' );
				$options['h4'] = esc_html__( 'H4', 'qode-compare-for-woocommerce' );
				$options['h5'] = esc_html__( 'H5', 'qode-compare-for-woocommerce' );
				$options['h6'] = esc_html__( 'H6', 'qode-compare-for-woocommerce' );
				$options['p']  = esc_html__( 'P', 'qode-compare-for-woocommerce' );
				break;
			case 'link_target':
				$options['_self']  = esc_html__( 'Same Window', 'qode-compare-for-woocommerce' );
				$options['_blank'] = esc_html__( 'New Window', 'qode-compare-for-woocommerce' );
				break;
			case 'border_style':
				$options['solid']  = esc_html__( 'Solid', 'qode-compare-for-woocommerce' );
				$options['dashed'] = esc_html__( 'Dashed', 'qode-compare-for-woocommerce' );
				$options['dotted'] = esc_html__( 'Dotted', 'qode-compare-for-woocommerce' );
				break;
			case 'font_weight':
				$options['100'] = esc_html__( 'Thin (100)', 'qode-compare-for-woocommerce' );
				$options['200'] = esc_html__( 'Extra Light (200)', 'qode-compare-for-woocommerce' );
				$options['300'] = esc_html__( 'Light (300)', 'qode-compare-for-woocommerce' );
				$options['400'] = esc_html__( 'Normal (400)', 'qode-compare-for-woocommerce' );
				$options['500'] = esc_html__( 'Medium (500)', 'qode-compare-for-woocommerce' );
				$options['600'] = esc_html__( 'Semi Bold (600)', 'qode-compare-for-woocommerce' );
				$options['700'] = esc_html__( 'Bold (700)', 'qode-compare-for-woocommerce' );
				$options['800'] = esc_html__( 'Extra Bold (800)', 'qode-compare-for-woocommerce' );
				$options['900'] = esc_html__( 'Black (900)', 'qode-compare-for-woocommerce' );
				break;
			case 'font_style':
				$options['normal']  = esc_html__( 'Normal', 'qode-compare-for-woocommerce' );
				$options['italic']  = esc_html__( 'Italic', 'qode-compare-for-woocommerce' );
				$options['oblique'] = esc_html__( 'Oblique', 'qode-compare-for-woocommerce' );
				$options['initial'] = esc_html__( 'Initial', 'qode-compare-for-woocommerce' );
				$options['inherit'] = esc_html__( 'Inherit', 'qode-compare-for-woocommerce' );
				break;
			case 'text_transform':
				$options['none']       = esc_html__( 'None', 'qode-compare-for-woocommerce' );
				$options['capitalize'] = esc_html__( 'Capitalize', 'qode-compare-for-woocommerce' );
				$options['uppercase']  = esc_html__( 'Uppercase', 'qode-compare-for-woocommerce' );
				$options['lowercase']  = esc_html__( 'Lowercase', 'qode-compare-for-woocommerce' );
				$options['initial']    = esc_html__( 'Initial', 'qode-compare-for-woocommerce' );
				$options['inherit']    = esc_html__( 'Inherit', 'qode-compare-for-woocommerce' );
				break;
			case 'text_decoration':
				$options['none']         = esc_html__( 'None', 'qode-compare-for-woocommerce' );
				$options['underline']    = esc_html__( 'Underline', 'qode-compare-for-woocommerce' );
				$options['overline']     = esc_html__( 'Overline', 'qode-compare-for-woocommerce' );
				$options['line-through'] = esc_html__( 'Line-Through', 'qode-compare-for-woocommerce' );
				$options['initial']      = esc_html__( 'Initial', 'qode-compare-for-woocommerce' );
				$options['inherit']      = esc_html__( 'Inherit', 'qode-compare-for-woocommerce' );
				break;
			case 'yes_no':
				$options['yes'] = esc_html__( 'Yes', 'qode-compare-for-woocommerce' );
				$options['no']  = esc_html__( 'No', 'qode-compare-for-woocommerce' );
				break;
			case 'no_yes':
				$options['no']  = esc_html__( 'No', 'qode-compare-for-woocommerce' );
				$options['yes'] = esc_html__( 'Yes', 'qode-compare-for-woocommerce' );
				break;
			case 'columns_number':
				$options['1'] = esc_html__( 'One', 'qode-compare-for-woocommerce' );
				$options['2'] = esc_html__( 'Two', 'qode-compare-for-woocommerce' );
				$options['3'] = esc_html__( 'Three', 'qode-compare-for-woocommerce' );
				$options['4'] = esc_html__( 'Four', 'qode-compare-for-woocommerce' );
				$options['5'] = esc_html__( 'Five', 'qode-compare-for-woocommerce' );
				$options['6'] = esc_html__( 'Six', 'qode-compare-for-woocommerce' );
				break;
			case 'order_by':
				$options['date']       = esc_html__( 'Date', 'qode-compare-for-woocommerce' );
				$options['ID']         = esc_html__( 'ID', 'qode-compare-for-woocommerce' );
				$options['menu_order'] = esc_html__( 'Menu Order', 'qode-compare-for-woocommerce' );
				$options['name']       = esc_html__( 'Post Name', 'qode-compare-for-woocommerce' );
				$options['rand']       = esc_html__( 'Random', 'qode-compare-for-woocommerce' );
				$options['title']      = esc_html__( 'Title', 'qode-compare-for-woocommerce' );
				break;
			case 'order':
				$options['DESC'] = esc_html__( 'Descending', 'qode-compare-for-woocommerce' );
				$options['ASC']  = esc_html__( 'Ascending', 'qode-compare-for-woocommerce' );
				break;
		}

		if ( ! empty( $exclude ) ) {
			foreach ( $exclude as $e ) {
				if ( array_key_exists( $e, $options ) ) {
					unset( $options[ $e ] );
				}
			}
		}

		if ( ! empty( $include ) ) {
			foreach ( $include as $key => $value ) {
				if ( ! array_key_exists( $key, $options ) ) {
					$options[ $key ] = $value;
				}
			}
		}

		return apply_filters( 'qode_compare_for_woocommerce_filter_select_type_option', $options, $type, $enable_default, $exclude );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_escape_title_tag' ) ) {
	/**
	 * Function that output escape title tag variable for modules
	 *
	 * @param string $title_tag
	 */
	function qode_compare_for_woocommerce_escape_title_tag( $title_tag ) {
		echo esc_html( qode_compare_for_woocommerce_get_escape_title_tag( $title_tag ) );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_escape_title_tag' ) ) {
	/**
	 * Function that return escape title tag variable for modules
	 *
	 * @param string $title_tag
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_get_escape_title_tag( $title_tag ) {
		$allowed_tags = array(
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'span',
			'ul',
			'ol',
		);

		$escaped_title_tag = '';
		$title_tag         = strtolower( sanitize_key( $title_tag ) );

		if ( in_array( $title_tag, $allowed_tags, true ) ) {
			$escaped_title_tag = $title_tag;
		}

		return $escaped_title_tag;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_attribute_value' ) ) {
	/**
	 * Function that return attribute option value
	 *
	 * @param string $name - option key from database
	 * @param int $attribute_id - option type
	 *
	 * @return string|mixed
	 */
	function qode_compare_for_woocommerce_get_attribute_value( $name, $attribute_id = 0 ) {
		$value = '';

		if ( ! empty( $attribute_id ) ) {
			$attribute_option_value = get_option( "$name-$attribute_id" );

			if ( '0' === $attribute_option_value || ! empty( $attribute_option_value ) ) {
				$value = $attribute_option_value;
			}
		}

		return $value;
	}
}
