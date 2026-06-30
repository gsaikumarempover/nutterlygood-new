<?php
/**
 * Product highlight presets (gluten free, etc.) + story block helpers.
 */

if ( ! function_exists( 'nuttergood_farmley_highlight_presets' ) ) {
	/**
	 * @return array<string, array{label: string, icon: string}>
	 */
	function nuttergood_farmley_highlight_presets() {
		return array(
			'gluten_free'        => array(
				'label' => __( 'Gluten Free', 'nuttergood' ),
				'icon'  => 'wheat-off',
			),
			'preservative_free'  => array(
				'label' => __( 'No Preservatives', 'nuttergood' ),
				'icon'  => 'shield',
			),
			'no_added_sugar'     => array(
				'label' => __( 'No Added Sugar', 'nuttergood' ),
				'icon'  => 'sugar-off',
			),
			'high_protein'       => array(
				'label' => __( 'High Protein', 'nuttergood' ),
				'icon'  => 'protein',
			),
			'vegan'              => array(
				'label' => __( 'Vegan', 'nuttergood' ),
				'icon'  => 'leaf',
			),
			'non_gmo'            => array(
				'label' => __( 'Non-GMO', 'nuttergood' ),
				'icon'  => 'seedling',
			),
			'roasted_not_fried'  => array(
				'label' => __( 'Roasted Not Fried', 'nuttergood' ),
				'icon'  => 'flame',
			),
			'handcrafted'        => array(
				'label' => __( 'Handcrafted', 'nuttergood' ),
				'icon'  => 'hands',
			),
			'trans_fat_free'     => array(
				'label' => __( 'Trans Fat Free', 'nuttergood' ),
				'icon'  => 'heart',
			),
			'cholesterol_free'   => array(
				'label' => __( 'Cholesterol Free', 'nuttergood' ),
				'icon'  => 'heart-pulse',
			),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_highlight_icon_svg' ) ) {
	/**
	 * @param string $icon Icon key.
	 */
	function nuttergood_farmley_highlight_icon_svg( $icon ) {
		$common = 'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"';

		$paths = array(
			'wheat-off'   => '<path d="M4 20l4-8m0 0l4 8m-4-8V4m8 16l4-8m0 0l4 8m-4-8V8"/><circle cx="12" cy="12" r="2"/>',
			'shield'      => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
			'sugar-off'   => '<circle cx="9" cy="9" r="2"/><circle cx="15" cy="15" r="2"/><path d="M5 19L19 5"/>',
			'protein'     => '<path d="M6 18h12"/><path d="M8 18V8l4-4 4 4v10"/><path d="M12 8v4"/>',
			'leaf'        => '<path d="M5 19c6-10 14-12 14-12s-2 8-12 14z"/><path d="M12 19c0-4 2-8 7-11"/>',
			'seedling'    => '<path d="M12 21V11"/><path d="M12 11c-4-3-8-2-8 2s4 6 8 4"/><path d="M12 11c4-3 8-2 8 2s-4 6-8 4"/>',
			'flame'       => '<path d="M12 3c2 4 5 5 5 9a5 5 0 11-10 0c0-3 2-4 5-9z"/>',
			'hands'       => '<path d="M8 11V8a2 2 0 114 0v1"/><path d="M12 11V7a2 2 0 114 0v6"/><path d="M8 15v-2a2 2 0 00-4 0v3c0 3 2 5 8 5s8-2 8-5v-3a2 2 0 00-4 0v2"/>',
			'heart'       => '<path d="M12 20s-7-4.5-7-10a4 4 0 017-2 4 4 0 017 2c0 5.5-7 10-7 10z"/>',
			'heart-pulse' => '<path d="M12 20s-7-4.5-7-10a4 4 0 017-2 4 4 0 017 2c0 5.5-7 10-7 10z"/><path d="M3 12h4l2-3 4 6 2-3h6"/>',
		);

		$body = $paths[ $icon ] ?? $paths['leaf'];

		return '<svg class="ng-farmley-highlight__icon" ' . $common . ' aria-hidden="true">' . $body . '</svg>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_product_highlights' ) ) {
	/**
	 * @param WC_Product $product Product.
	 *
	 * @return array<int, array{key: string, label: string, icon: string, image_id: int, image_url: string}>
	 */
	function nuttergood_farmley_get_product_highlights( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		$presets = nuttergood_farmley_highlight_presets();
		$raw     = get_post_meta( $product->get_id(), '_ng_product_highlights', true );
		$decoded = is_string( $raw ) ? json_decode( $raw, true ) : $raw;

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		$items = array();
		foreach ( $decoded as $row ) {
			if ( is_string( $row ) ) {
				$row = array( 'key' => $row );
			}
			if ( ! is_array( $row ) ) {
				continue;
			}

			$key = sanitize_key( $row['key'] ?? '' );
			if ( '' === $key || ! isset( $presets[ $key ] ) ) {
				continue;
			}

			$image_id  = (int) ( $row['image_id'] ?? 0 );
			$image_url = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';

			$items[] = array(
				'key'       => $key,
				'label'     => $presets[ $key ]['label'],
				'icon'      => $presets[ $key ]['icon'],
				'image_id'  => $image_id,
				'image_url' => $image_url,
			);
		}

		return $items;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_story_blocks' ) ) {
	/**
	 * @param WC_Product $product Product.
	 *
	 * @return array<int, array{image_id: int, image_url: string, title: string, content: string}>
	 */
	function nuttergood_farmley_get_story_blocks( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		$raw     = get_post_meta( $product->get_id(), '_ng_story_blocks', true );
		$decoded = is_string( $raw ) ? json_decode( $raw, true ) : $raw;

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		$blocks = array();
		foreach ( $decoded as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}

			$title   = trim( (string) ( $block['title'] ?? '' ) );
			$content = trim( (string) ( $block['content'] ?? '' ) );
			$image_id = (int) ( $block['image_id'] ?? 0 );

			if ( '' === $title && '' === $content && ! $image_id ) {
				continue;
			}

			$blocks[] = array(
				'image_id'  => $image_id,
				'image_url' => $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '',
				'title'     => $title,
				'content'   => $content,
			);
		}

		return $blocks;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_description_intro' ) ) {
	/**
	 * @param WC_Product $product Product.
	 */
	function nuttergood_farmley_get_description_intro( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}

		$intro = trim( (string) get_post_meta( $product->get_id(), '_ng_description_intro', true ) );
		if ( '' !== $intro ) {
			return $intro;
		}

		$post = get_post( $product->get_id() );
		if ( ! $post ) {
			return '';
		}

		$short = trim( (string) $product->get_short_description() );
		if ( '' !== $short ) {
			return wp_strip_all_tags( $short );
		}

		$html = nuttergood_farmley_strip_specs_from_html( $post->post_content );
		$plain = trim( wp_strip_all_tags( $html ) );

		return $plain;
	}
}

if ( ! function_exists( 'nuttergood_farmley_strip_specs_from_html' ) ) {
	/**
	 * Remove attribute tables / spec lists from imported descriptions.
	 *
	 * @param string $content HTML content.
	 */
	function nuttergood_farmley_strip_specs_from_html( $content ) {
		if ( '' === trim( (string) $content ) ) {
			return '';
		}

		if ( function_exists( 'nuttergood_farmley_clean_product_html' ) ) {
			$content = nuttergood_farmley_clean_product_html( $content );
		}

		$patterns = array(
			'/<table\b[^>]*>.*?<\/table>/is',
			'/<dl\b[^>]*>.*?<\/dl>/is',
			'/<div[^>]*class="[^"]*woocommerce-product-attributes[^"]*"[^>]*>.*?<\/div>/is',
			'/<section[^>]*class="[^"]*additional-information[^"]*"[^>]*>.*?<\/section>/is',
			'/<h[1-6][^>]*>\s*(additional information|product details|specifications|ingredients|nutrition)\s*<\/h[1-6]>.*?$/is',
		);

		foreach ( $patterns as $pattern ) {
			$content = preg_replace( $pattern, '', $content );
		}

		// Remove list blocks that look like spec key-value pairs.
		$content = preg_replace_callback(
			'/<ul[^>]*>(.*?)<\/ul>/is',
			function ( $matches ) {
				$inner = $matches[1];
				if ( preg_match_all( '/<li[^>]*>/i', $inner ) > 2 && stripos( $inner, 'origin' ) !== false ) {
					return '';
				}
				return $matches[0];
			},
			$content
		);

		return trim( $content );
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_description_prose' ) ) {
	/**
	 * @param WC_Product $product Product.
	 */
	function nuttergood_farmley_get_description_prose( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}

		$post = get_post( $product->get_id() );
		if ( ! $post || '' === trim( $post->post_content ) ) {
			return '';
		}

		$html = nuttergood_farmley_strip_specs_from_html( $post->post_content );

		// If only empty tags remain, treat as no prose.
		$plain = trim( wp_strip_all_tags( $html ) );
		if ( '' === $plain ) {
			return '';
		}

		return $html;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_description_bullets' ) ) {
	/**
	 * @param WC_Product $product Product.
	 *
	 * @return array<int, string>
	 */
	function nuttergood_farmley_get_description_bullets( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		$raw = get_post_meta( $product->get_id(), '_ng_description_bullets', true );
		if ( is_string( $raw ) && '' !== trim( $raw ) ) {
			$lines = preg_split( '/\r\n|\r|\n/', $raw );
			$items = array();
			foreach ( $lines as $line ) {
				$line = trim( (string) $line );
				if ( '' !== $line ) {
					$items[] = $line;
				}
			}
			if ( ! empty( $items ) ) {
				return $items;
			}
		}

		$from_highlights = array();
		foreach ( nuttergood_farmley_get_product_highlights( $product ) as $item ) {
			if ( ! empty( $item['label'] ) ) {
				$from_highlights[] = $item['label'];
			}
		}

		return $from_highlights;
	}
}

if ( ! function_exists( 'nuttergood_farmley_product_has_description_tab_content' ) ) {
	/**
	 * @param WC_Product $product Product.
	 */
	function nuttergood_farmley_product_has_description_tab_content( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		return '' !== nuttergood_farmley_get_description_intro( $product )
			|| ! empty( nuttergood_farmley_get_description_bullets( $product ) );
	}
}
