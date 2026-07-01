<?php
/**
 * Farmley-style product cards — weight labels + theme layout override.
 */

if ( ! function_exists( 'nuttergood_farmley_format_weight_label' ) ) {
	/**
	 * @param string $raw Raw weight string e.g. 250gm, 1kg.
	 */
	function nuttergood_farmley_format_weight_label( $raw ) {
		$raw = trim( (string) $raw );
		if ( '' === $raw ) {
			return '';
		}

		if ( preg_match( '/^(\d+(?:\.\d+)?)\s*(gm?|g|kg|ml|l)$/i', $raw, $matches ) ) {
			$num  = $matches[1];
			$unit = strtolower( $matches[2] );

			if ( in_array( $unit, array( 'gm', 'g' ), true ) ) {
				return $num . ' g';
			}
			if ( 'kg' === $unit ) {
				return $num . ' kg';
			}

			return $num . ' ' . $unit;
		}

		return $raw;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_product_weight_label' ) ) {
	/**
	 * @param WC_Product|int $product Product object or ID.
	 */
	function nuttergood_farmley_get_product_weight_label( $product ) {
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product instanceof WC_Product ) {
			return '';
		}

		if ( function_exists( 'nuttergood_farmley_get_product_meta' ) ) {
			$meta = nuttergood_farmley_get_product_meta( $product );
			if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
				$first = reset( $meta['sizes'] );
				if ( is_array( $first ) ) {
					if ( ! empty( $first['weight'] ) ) {
						$label = nuttergood_farmley_format_weight_label( $first['weight'] );
						if ( '' !== $label ) {
							return $label;
						}
					}
					if ( ! empty( $first['label'] ) ) {
						$label = nuttergood_farmley_format_weight_label( $first['label'] );
						if ( '' !== $label ) {
							return $label;
						}
					}
				}
			}
		}

		$weight = $product->get_weight();
		if ( '' === $weight || null === $weight ) {
			return '';
		}

		$weight_num = (float) $weight;
		$unit       = get_option( 'woocommerce_weight_unit', 'kg' );

		if ( 'kg' === $unit && $weight_num > 0 && $weight_num < 1 ) {
			return (int) round( $weight_num * 1000 ) . ' g';
		}

		return rtrim( rtrim( number_format( $weight_num, 2, '.', '' ), '0' ), '.' ) . ' ' . $unit;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_product_weight_labels_list' ) ) {
	/**
	 * All weight/size labels for a product (dynamic tiers included).
	 *
	 * @param WC_Product|null $product Product object.
	 *
	 * @return array<int, string>
	 */
	function nuttergood_farmley_get_product_weight_labels_list( $product = null ) {
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		$labels = array();

		if ( function_exists( 'nuttergood_farmley_get_product_size_options' ) ) {
			foreach ( nuttergood_farmley_get_product_size_options( $product ) as $size ) {
				if ( ! is_array( $size ) ) {
					continue;
				}
				$label = nuttergood_farmley_format_weight_label( $size['weight'] ?? $size['label'] ?? '' );
				if ( '' !== $label ) {
					$labels[] = $label;
				}
			}
		}

		$labels = array_values( array_unique( $labels ) );

		if ( ! empty( $labels ) ) {
			return $labels;
		}

		$single = nuttergood_farmley_get_product_weight_label( $product );
		return '' !== $single ? array( $single ) : array();
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_product_weight_display' ) ) {
	/**
	 * Comma-separated weights for additional-information tab.
	 *
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_get_product_weight_display( $product ) {
		$labels = nuttergood_farmley_get_product_weight_labels_list( $product );

		return ! empty( $labels ) ? implode( ', ', $labels ) : '';
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_ai_product_attachment' ) ) {
	/**
	 * Whether an attachment is an AI-generated product image (not legacy webp).
	 *
	 * @param int $attachment_id Attachment ID.
	 */
	function nuttergood_farmley_is_ai_product_attachment( $attachment_id ) {
		$attachment_id = (int) $attachment_id;
		if ( $attachment_id <= 0 ) {
			return false;
		}

		$file = strtolower( (string) get_post_meta( $attachment_id, '_wp_attached_file', true ) );
		if ( '' === $file || str_ends_with( $file, '.webp' ) || str_ends_with( $file, '.jpg' ) || str_ends_with( $file, '.jpeg' ) ) {
			return false;
		}

		return str_ends_with( $file, '.png' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_card_primary_image_id' ) ) {
	/**
	 * Primary pack shot — AI PNG only.
	 *
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_get_card_primary_image_id( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return 0;
		}

		$id = $product->get_id();

		$hd_id = (int) get_post_meta( $id, '_ng_hd_image_id', true );
		if ( $hd_id && nuttergood_farmley_is_ai_product_attachment( $hd_id ) ) {
			return $hd_id;
		}

		$featured = (int) $product->get_image_id();
		if ( $featured && nuttergood_farmley_is_ai_product_attachment( $featured ) ) {
			return $featured;
		}

		return 0;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_product_hover_image_id' ) ) {
	/**
	 * Second image on card hover — AI gallery angles only.
	 *
	 * @param WC_Product $product Product object.
	 * @param int        $primary_id Primary card image ID.
	 */
	function nuttergood_farmley_get_product_hover_image_id( $product, $primary_id = 0 ) {
		if ( ! $product instanceof WC_Product ) {
			return 0;
		}

		$primary_id = $primary_id > 0 ? $primary_id : nuttergood_farmley_get_card_primary_image_id( $product );

		foreach ( $product->get_gallery_image_ids() as $gid ) {
			$gid = (int) $gid;
			if ( $gid && $gid !== $primary_id && nuttergood_farmley_is_ai_product_attachment( $gid ) ) {
				return $gid;
			}
		}

		if ( function_exists( 'nuttergood_farmley_get_product_meta' ) ) {
			$meta = nuttergood_farmley_get_product_meta( $product );
			if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
				foreach ( array_slice( $meta['sizes'], 1 ) as $size_row ) {
					if ( is_array( $size_row ) && ! empty( $size_row['image_id'] ) ) {
						$image_id = (int) $size_row['image_id'];
						if ( $image_id && $image_id !== $primary_id && nuttergood_farmley_is_ai_product_attachment( $image_id ) ) {
							return $image_id;
						}
					}
				}
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'nuttergood_farmley_resolve_card_prices' ) ) {
	/**
	 * MRP + offer for list cards (meta, size row, WC sale, or display fallback).
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return array{mrp: float, offer: float}
	 */
	function nuttergood_farmley_resolve_card_prices( $product, $apply_display_fallback = true ) {
		if ( ! $product instanceof WC_Product ) {
			return array(
				'mrp'   => 0.0,
				'offer' => 0.0,
			);
		}

		$mrp   = 0.0;
		$offer = 0.0;

		if ( function_exists( 'nuttergood_farmley_get_product_meta' ) ) {
			$meta  = nuttergood_farmley_get_product_meta( $product );
			$mrp   = (float) ( $meta['mrp'] ?? 0 );
			$offer = (float) ( $meta['offer_price'] ?? 0 );

			if ( ( $mrp <= $offer || $mrp <= 0 ) && ! empty( $meta['sizes'][0] ) && is_array( $meta['sizes'][0] ) ) {
				$row       = $meta['sizes'][0];
				$row_mrp   = (float) ( $row['mrp'] ?? $row['regular_price'] ?? 0 );
				$row_offer = (float) ( $row['price'] ?? 0 );
				if ( $row_mrp > $row_offer && $row_offer > 0 ) {
					$mrp   = $row_mrp;
					$offer = $row_offer;
				}
			}
		}

		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $regular > 0 && $sale > 0 && $regular > $sale ) {
			$mrp   = $regular;
			$offer = $sale;
		}

		if ( $mrp <= 0 ) {
			$mrp = $regular;
		}
		if ( $offer <= 0 ) {
			$offer = $sale > 0 ? $sale : (float) $product->get_price();
		}

		if ( $apply_display_fallback && $mrp <= $offer && $offer > 0 ) {
			$mrp = round( $offer / 0.84 );
		}

		return array(
			'mrp'   => (float) $mrp,
			'offer' => (float) $offer,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_default_offer_badge_product_ids' ) ) {
	/**
	 * Products that should show an offer badge (curated — not every product).
	 *
	 * @return int[]
	 */
	function nuttergood_farmley_default_offer_badge_product_ids() {
		return array(
			7517, // Dark Chocolate Almonds
			7521, // Rose Petal Cashews
			7525, // Pizza Cashews
			7529, // Kala Khatta Kishmish
			7547, // Mix Vegetable Chips
			7551, // Coffee Almond Brittle
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_product_show_offer_badge' ) ) {
	/**
	 * Whether this product may display an offer badge on list cards.
	 *
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_product_show_offer_badge( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		if ( 'yes' === get_post_meta( $product->get_id(), '_ng_show_offer_badge', true ) ) {
			return true;
		}

		$ids = apply_filters(
			'nuttergood_farmley_offer_badge_product_ids',
			nuttergood_farmley_default_offer_badge_product_ids()
		);

		return in_array( (int) $product->get_id(), array_map( 'intval', (array) $ids ), true );
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_badge_discount_percent' ) ) {
	/**
	 * Discount % for offer badge — only for flagged products; uses real or display MRP.
	 *
	 * @param WC_Product              $product  Product object.
	 * @param array<string, mixed>|null $size_row Optional size row.
	 */
	function nuttergood_farmley_get_badge_discount_percent( $product, $size_row = null ) {
		if ( ! nuttergood_farmley_product_show_offer_badge( $product ) ) {
			return 0;
		}

		if ( is_array( $size_row ) ) {
			$real = nuttergood_farmley_resolve_size_prices( $size_row, $product, false );
			if ( $real['discount'] > 0 ) {
				return (int) $real['discount'];
			}

			$display = nuttergood_farmley_resolve_size_prices( $size_row, $product, true );
			if ( $display['discount'] > 0 ) {
				return (int) $display['discount'];
			}

			return 0;
		}

		$real = nuttergood_farmley_get_product_discount_percent( $product );
		if ( $real > 0 ) {
			return $real;
		}

		$prices = nuttergood_farmley_resolve_card_prices( $product, true );
		$mrp    = $prices['mrp'];
		$offer  = $prices['offer'];

		if ( $mrp > $offer && $mrp > 0 ) {
			return (int) round( ( ( $mrp - $offer ) / $mrp ) * 100 );
		}

		return 0;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_product_discount_percent' ) ) {
	/**
	 * Real discount only — no synthetic MRP fallback.
	 *
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_get_product_discount_percent( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return 0;
		}

		$prices = nuttergood_farmley_resolve_card_prices( $product, false );
		$mrp    = $prices['mrp'];
		$offer  = $prices['offer'];

		if ( $mrp > $offer && $mrp > 0 ) {
			return (int) round( ( ( $mrp - $offer ) / $mrp ) * 100 );
		}

		return 0;
	}
}

if ( ! function_exists( 'nuttergood_farmley_resolve_list_image_size' ) ) {
	/**
	 * GreenPath passes image_dimension as array( 'size' => 'full', 'class' => '...' ).
	 * WordPress expects a registered size string or array( width, height ).
	 *
	 * @param array<string, mixed> $params Shortcode params.
	 */
	function nuttergood_farmley_resolve_list_image_size( $params = array() ) {
		$fallback = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
		$raw      = $params['image_dimension'] ?? $fallback;

		if ( is_array( $raw ) ) {
			if ( ! empty( $raw['size'] ) ) {
				return (string) $raw['size'];
			}

			if ( isset( $raw[0], $raw[1] ) ) {
				return array( (int) $raw[0], (int) $raw[1] );
			}

			return $fallback;
		}

		return is_string( $raw ) && '' !== $raw ? $raw : $fallback;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_list_image_html' ) ) {
	/**
	 * @param int                  $attachment_id Attachment ID.
	 * @param array<string, mixed> $params        Shortcode params.
	 * @param array<string, mixed> $attr          Image attributes.
	 */
	function nuttergood_farmley_get_list_image_html( $attachment_id, $params = array(), $attr = array() ) {
		$attachment_id     = (int) $attachment_id;
		$image_dimension   = nuttergood_farmley_resolve_list_image_size( $params );
		$custom_image_width  = isset( $params['custom_image_width'] ) ? (int) $params['custom_image_width'] : 0;
		$custom_image_height = isset( $params['custom_image_height'] ) ? (int) $params['custom_image_height'] : 0;

		if ( $attachment_id <= 0 || ! wp_attachment_is_image( $attachment_id ) ) {
			return '';
		}

		if ( function_exists( 'greenpath_core_get_list_shortcode_item_image' ) ) {
			return greenpath_core_get_list_shortcode_item_image( $image_dimension, $attachment_id, $custom_image_width, $custom_image_height, $attr );
		}

		return wp_get_attachment_image( $attachment_id, $image_dimension, false, $attr );
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_product_card_media' ) ) {
	/**
	 * Farmley-style media: dual image hover swap + discount badge.
	 *
	 * @param array<string, mixed> $params Shortcode params.
	 */
	function nuttergood_farmley_render_product_card_media( $params = array() ) {
		$product = wc_get_product( get_the_ID() );
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$primary_id = nuttergood_farmley_get_card_primary_image_id( $product );
		$hover_id   = nuttergood_farmley_get_product_hover_image_id( $product, $primary_id );
		$has_hover          = $hover_id > 0 && $hover_id !== $primary_id && wp_attachment_is_image( $hover_id );

		if ( $primary_id <= 0 || ! wp_attachment_is_image( $primary_id ) ) {
			return;
		}
		$discount           = nuttergood_farmley_get_badge_discount_percent( $product );

		$card_classes = array( 'ng-farmley-card-media' );
		if ( $has_hover ) {
			$card_classes[] = 'ng-farmley-card-media--has-hover';
		}

		echo '<div class="qodef-e-media-image">';
		if ( function_exists( 'woocommerce_template_loop_product_link_open' ) ) {
			woocommerce_template_loop_product_link_open();
		}

		echo '<div class="' . esc_attr( implode( ' ', $card_classes ) ) . '">';

		if ( $discount > 0 ) {
			printf(
				'<span class="ng-farmley-card-badge">%s</span>',
				esc_html( sprintf( __( '%d%% OFF', 'nuttergood' ), $discount ) )
			);
		}

		echo '<span class="ng-farmley-card-media__layer ng-farmley-card-media__layer--primary">';
		echo nuttergood_farmley_get_list_image_html( $primary_id, $params, array( 'class' => 'ng-farmley-card-media__img ng-farmley-card-media__img--primary' ) );
		echo '</span>';

		if ( $has_hover ) {
			echo '<span class="ng-farmley-card-media__layer ng-farmley-card-media__layer--hover">';
			echo nuttergood_farmley_get_list_image_html( $hover_id, $params, array( 'class' => 'ng-farmley-card-media__img ng-farmley-card-media__img--hover' ) );
			echo '</span>';
		}

		echo '</div>';

		if ( function_exists( 'woocommerce_template_loop_product_link_close' ) ) {
			woocommerce_template_loop_product_link_close();
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_parse_weight_grams' ) ) {
	/**
	 * @param string $raw Weight label.
	 */
	function nuttergood_farmley_parse_weight_grams( $raw ) {
		$raw = trim( (string) $raw );
		if ( preg_match( '/(\d+(?:\.\d+)?)\s*(kg|g|gm)\b/i', $raw, $matches ) ) {
			$num  = (float) $matches[1];
			$unit = strtolower( $matches[2] );
			if ( 'kg' === $unit ) {
				return (int) round( $num * 1000 );
			}
			return (int) round( $num );
		}
		return 0;
	}
}

if ( ! function_exists( 'nuttergood_farmley_product_supports_weight_tiers' ) ) {
	/**
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_product_supports_weight_tiers( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		$slugs = array( 'dry-fruits', 'almonds', 'cashews', 'cranberry', 'khishmish', 'walnuts' );
		$terms = get_the_terms( $product->get_id(), 'product_cat' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return false;
		}

		foreach ( $terms as $term ) {
			if ( in_array( $term->slug, $slugs, true ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'nuttergood_farmley_build_weight_tier_options' ) ) {
	/**
	 * Build 250 g / 500 g / 1 kg / 2 kg rows from a single base size.
	 *
	 * @param array<string, mixed> $base_row Base size row.
	 */
	function nuttergood_farmley_build_weight_tier_options( $base_row ) {
		$base_label = nuttergood_farmley_format_weight_label( $base_row['weight'] ?? $base_row['label'] ?? '' );
		$base_grams = nuttergood_farmley_parse_weight_grams( $base_label );
		if ( $base_grams <= 0 ) {
			$base_grams = 250;
			$base_label = '250 g';
		}

		$tier_defs = array(
			array( 'label' => '250 g', 'grams' => 250, 'mult' => 1 ),
			array( 'label' => '500 g', 'grams' => 500, 'mult' => 1.9 ),
			array( 'label' => '1 kg', 'grams' => 1000, 'mult' => 3.62 ),
			array( 'label' => '2 kg', 'grams' => 2000, 'mult' => 6.85 ),
		);

		$base_offer = (float) ( $base_row['price'] ?? 0 );
		$base_mrp   = (float) ( $base_row['mrp'] ?? $base_row['regular_price'] ?? 0 );
		if ( $base_offer <= 0 ) {
			return array();
		}
		if ( $base_mrp <= $base_offer ) {
			$base_mrp = round( $base_offer / 0.84 );
		}

		$scale = $base_grams > 0 ? ( $base_offer / $base_grams ) : 0;
		$rows  = array();

		foreach ( $tier_defs as $tier ) {
			if ( $tier['grams'] < $base_grams ) {
				continue;
			}

			$mult  = $tier['grams'] === $base_grams ? 1 : ( $tier['mult'] * ( $base_grams / 250 ) );
			$offer = (int) round( $base_offer * ( $tier['grams'] / $base_grams ) * ( $tier['grams'] === $base_grams ? 1 : ( $tier['mult'] / ( $tier['grams'] / 250 ) ) ) );
			if ( $tier['grams'] === $base_grams ) {
				$offer = (int) round( $base_offer );
			} elseif ( 250 === $base_grams ) {
				$offer = (int) round( $base_offer * $tier['mult'] );
			} else {
				$offer = (int) round( $scale * $tier['grams'] * 0.98 );
			}

			$mrp = (int) round( $base_mrp * ( $offer / $base_offer ) );

			$rows[] = array(
				'label'         => $tier['label'],
				'weight'        => $tier['label'],
				'image_id'      => (int) ( $base_row['image_id'] ?? 0 ),
				'price'         => (string) $offer,
				'regular_price' => (string) $mrp,
				'mrp'           => (string) $mrp,
			);
		}

		return $rows;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_product_size_options' ) ) {
	/**
	 * @param WC_Product|null $product Product object.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function nuttergood_farmley_get_product_size_options( $product = null ) {
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		$meta  = function_exists( 'nuttergood_farmley_get_product_meta' ) ? nuttergood_farmley_get_product_meta( $product ) : array();
		$sizes = ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ? $meta['sizes'] : array();

		$normalized = array();
		foreach ( $sizes as $size ) {
			if ( ! is_array( $size ) ) {
				continue;
			}
			$label = nuttergood_farmley_format_weight_label( $size['weight'] ?? $size['label'] ?? '' );
			if ( '' === $label ) {
				continue;
			}
			$normalized[] = array_merge(
				$size,
				array(
					'label'  => $label,
					'weight' => $label,
				)
			);
		}

		if ( count( $normalized ) > 1 ) {
			return $normalized;
		}

		$base = ! empty( $normalized[0] ) ? $normalized[0] : array();
		if ( empty( $base ) ) {
			$label = nuttergood_farmley_get_product_weight_label( $product );
			if ( '' === $label ) {
				return array();
			}
			$prices = nuttergood_farmley_resolve_card_prices( $product );
			$base   = array(
				'label'         => $label,
				'weight'        => $label,
				'image_id'      => (int) $product->get_image_id(),
				'price'         => (string) $prices['offer'],
				'regular_price' => (string) $prices['mrp'],
				'mrp'           => (string) $prices['mrp'],
			);
		}

		if ( nuttergood_farmley_product_supports_weight_tiers( $product ) ) {
			$tiers = nuttergood_farmley_build_weight_tier_options( $base );
			if ( count( $tiers ) > 1 ) {
				return $tiers;
			}
		}

		return array( $base );
	}
}

if ( ! function_exists( 'nuttergood_farmley_resolve_size_prices' ) ) {
	/**
	 * @param array<string, mixed> $size_row Size row.
	 * @param WC_Product|null      $product  Product object.
	 *
	 * @return array{mrp: float, offer: float, discount: int}
	 */
	function nuttergood_farmley_resolve_size_prices( $size_row, $product = null, $apply_display_fallback = true ) {
		$offer = (float) ( $size_row['price'] ?? 0 );
		$mrp   = (float) ( $size_row['mrp'] ?? $size_row['regular_price'] ?? 0 );

		if ( $apply_display_fallback && $mrp <= $offer && $offer > 0 ) {
			$mrp = round( $offer / 0.84 );
		}

		$discount = 0;
		if ( $mrp > $offer && $mrp > 0 ) {
			$discount = (int) round( ( ( $mrp - $offer ) / $mrp ) * 100 );
		}

		return array(
			'mrp'      => (float) $mrp,
			'offer'    => (float) $offer,
			'discount' => $discount,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_card_price_markup' ) ) {
	/**
	 * @param float $offer Offer price.
	 * @param float $mrp   MRP.
	 */
	function nuttergood_farmley_render_card_price_markup( $offer, $mrp ) {
		if ( $mrp > $offer && $mrp > 0 && $offer > 0 ) {
			echo '<div class="qodef-woo-product-price price">';
			echo '<ins aria-hidden="true">' . wp_kses_post( wc_price( $offer ) ) . '</ins>';
			echo '<del aria-hidden="true">' . wp_kses_post( wc_price( $mrp ) ) . '</del>';
			echo '</div>';
			return;
		}

		if ( $offer > 0 ) {
			echo '<div class="qodef-woo-product-price price">';
			echo wp_kses_post( wc_price( $offer ) );
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_product_card_price' ) ) {
	/**
	 * Farmley-style price row with struck MRP when discounted.
	 *
	 * @param WC_Product|null $product Product object.
	 * @param int             $index   Selected size index.
	 */
	function nuttergood_farmley_render_product_card_price( $product = null, $index = 0 ) {
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$sizes = nuttergood_farmley_get_product_size_options( $product );
		$row   = ! empty( $sizes[ $index ] ) ? $sizes[ $index ] : null;
		if ( is_array( $row ) ) {
			$prices = nuttergood_farmley_resolve_size_prices( $row, $product );
		} else {
			$prices = nuttergood_farmley_resolve_card_prices( $product );
			$prices['discount'] = nuttergood_farmley_get_product_discount_percent( $product );
		}

		printf(
			'<div class="qodef-e-price-holder ng-farmley-card-price" data-size-index="%1$d">',
			(int) $index
		);
		nuttergood_farmley_render_card_price_markup( $prices['offer'], $prices['mrp'] );
		echo '</div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_card_discount_badge' ) ) {
	/**
	 * Discount badge — top-right on product image (opposite quick view).
	 *
	 * @param WC_Product|null $product Product object.
	 * @param int             $index   Selected size index.
	 */
	function nuttergood_farmley_render_card_discount_badge( $product = null, $index = 0 ) {
		if ( null === $product ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$sizes    = nuttergood_farmley_get_product_size_options( $product );
		$discount = 0;

		if ( ! empty( $sizes[ $index ] ) && is_array( $sizes[ $index ] ) ) {
			$discount = nuttergood_farmley_get_badge_discount_percent( $product, $sizes[ $index ] );
		} else {
			$discount = nuttergood_farmley_get_badge_discount_percent( $product );
		}

		if ( $discount <= 0 ) {
			return;
		}

		printf(
			'<span class="ng-farmley-card-badge" data-size-index="%1$d">%2$s</span>',
			(int) $index,
			esc_html( sprintf( __( '%d%% OFF', 'nuttergood' ), $discount ) )
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_product_weight_badges' ) ) {
	/**
	 * Weight pills — multiple selectable sizes update card price on click.
	 *
	 * @param WC_Product|null $product Product object.
	 */
	function nuttergood_farmley_render_product_weight_badges( $product = null ) {
		if ( null === $product ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$sizes = nuttergood_farmley_get_product_size_options( $product );
		if ( empty( $sizes ) ) {
			return;
		}

		$multi = count( $sizes ) > 1;

		printf(
			'<div class="ng-farmley-card-weight%1$s" role="%2$s" aria-label="%3$s">',
			$multi ? ' ng-farmley-card-weight--multi' : '',
			$multi ? 'listbox' : 'presentation',
			esc_attr__( 'Weight options', 'nuttergood' )
		);
		echo '<div class="ng-farmley-card-weight__options">';

		foreach ( $sizes as $idx => $size ) {
			if ( ! is_array( $size ) ) {
				continue;
			}

			$label        = nuttergood_farmley_format_weight_label( $size['weight'] ?? $size['label'] ?? '' );
			$badge_discount = nuttergood_farmley_get_badge_discount_percent( $product, $size );
			$img_id       = (int) ( $size['image_id'] ?? 0 );
			$img_src = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : '';

			if ( '' === $label ) {
				continue;
			}

			if ( $multi ) {
				printf(
					'<button type="button" class="ng-farmley-card-weight__btn%1$s" role="option" data-index="%2$d" data-price="%3$s" data-mrp="%4$s" data-regular="%5$s" data-discount="%6$d" data-image="%7$s" aria-selected="%8$s"><span class="ng-farmley-card-weight__text">%9$s</span></button>',
					0 === $idx ? ' is-active' : '',
					(int) $idx,
					esc_attr( $size['price'] ?? '' ),
					esc_attr( $size['mrp'] ?? $size['regular_price'] ?? '' ),
					esc_attr( $size['regular_price'] ?? '' ),
					(int) $badge_discount,
					esc_url( $img_src ? $img_src : '' ),
					0 === $idx ? 'true' : 'false',
					esc_html( $label )
				);
			} else {
				printf(
					'<span class="ng-farmley-card-weight__badge"><span class="ng-farmley-card-weight__text">%s</span></span>',
					esc_html( $label )
				);
			}
		}

		echo '</div></div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_product_weight' ) ) {
	function nuttergood_farmley_render_product_weight( $product = null ) {
		if ( null === $product ) {
			$product = wc_get_product( get_the_ID() );
		}

		$label = nuttergood_farmley_get_product_weight_label( $product );
		if ( '' === $label ) {
			return;
		}

		printf(
			'<span class="ng-farmley-product-weight" aria-label="%1$s">%2$s</span>',
			esc_attr( sprintf( __( 'Net weight %s', 'nuttergood' ), $label ) ),
			esc_html( $label )
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_product_list_layout_path' ) ) {
	/**
	 * Use theme info-below layout so we can inject weight between title and price.
	 *
	 * @param string               $path   Default variation path.
	 * @param array<string, mixed> $params Shortcode params.
	 */
	function nuttergood_farmley_product_list_layout_path( $path, $params ) {
		if ( empty( $params['layout'] ) || false === strpos( (string) $path, '/product-list/' ) ) {
			return $path;
		}

		if ( 'info-right' === $params['layout'] ) {
			return $path;
		}

		// Horizontal keeps the plugin variation — theme info-below has no layouts/horizontal.php.
		$unified_layouts = array( 'info-below', 'catalogue' );
		if ( ! in_array( $params['layout'], $unified_layouts, true ) ) {
			return $path;
		}

		$theme_path = get_template_directory() . '/inc/farmley/product-list/variations/info-below';
		if ( is_dir( $theme_path ) ) {
			return $theme_path;
		}

		return $path;
	}
	add_filter( 'qode_framework_list_sc_layout_path', 'nuttergood_farmley_product_list_layout_path', 10, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_loop_product_weight' ) ) {
	function nuttergood_farmley_loop_product_weight() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		if ( nuttergood_farmley_should_apply_product_cards() && function_exists( 'nuttergood_farmley_render_product_weight_badges' ) ) {
			nuttergood_farmley_render_product_weight_badges();
			return;
		}

		nuttergood_farmley_render_product_weight();
	}
	add_action( 'woocommerce_after_shop_loop_item_title', 'nuttergood_farmley_loop_product_weight', 9 );
}

if ( ! function_exists( 'nuttergood_farmley_should_apply_product_cards' ) ) {
	/**
	 * Pages that use the unified Farmley product card design.
	 */
	function nuttergood_farmley_should_apply_product_cards() {
		return ! is_admin();
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_loop_buy_now_link' ) ) {
	/**
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_get_loop_buy_now_link( $product ) {
		if ( ! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
			return '';
		}

		$label      = esc_html__( 'BUY NOW', 'nuttergood' );
		$is_popular = ! empty( $GLOBALS['ng_farmley_popular_card_actions'] );

		if ( $is_popular ) {
			$label = __( 'Buy Now', 'nuttergood' );
		}

		if ( $product->is_type( 'simple' ) ) {
			$classes = 'button ng-farmley-buy-now ajax_add_to_cart product_type_simple';
			if ( $is_popular ) {
				$classes .= ' ng-farmley-popular-btn';
			} else {
				$classes .= ' add_to_cart_button';
			}

			$inner = $is_popular && function_exists( 'nuttergood_farmley_popular_button_inner' )
				? nuttergood_farmley_popular_button_inner( 'bag', $label )
				: '<span class="ng-farmley-card-btn__inner"><span class="qodef-m-text">' . esc_html( $label ) . '</span></span>';

			return sprintf(
				'<a href="%1$s" class="%2$s" data-product_id="%3$d" data-product_sku="%4$s" data-quantity="1" rel="nofollow" aria-label="%5$s">%6$s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( $classes ),
				(int) $product->get_id(),
				esc_attr( $product->get_sku() ),
				esc_attr( sprintf( __( 'Buy %s now', 'nuttergood' ), $product->get_name() ) ),
				$inner
			);
		}

		$inner = $is_popular && function_exists( 'nuttergood_farmley_popular_button_inner' )
			? nuttergood_farmley_popular_button_inner( 'bag', $label )
			: '<span class="ng-farmley-card-btn__inner"><span class="qodef-m-text">' . esc_html( $label ) . '</span></span>';

		$link_classes = 'button ng-farmley-buy-now ng-farmley-buy-now--link';
		if ( $is_popular ) {
			$link_classes .= ' ng-farmley-popular-btn';
		}

		return sprintf(
			'<a href="%1$s" class="%2$s" aria-label="%3$s">%4$s</a>',
			esc_url( $product->get_permalink() ),
			esc_attr( $link_classes ),
			esc_attr( sprintf( __( 'View %s to buy now', 'nuttergood' ), $product->get_name() ) ),
			$inner
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_loop_add_to_cart_link' ) ) {
	/**
	 * Pill button with cart icon + paired buy-now button.
	 *
	 * @param string     $html    Button HTML.
	 * @param WC_Product $product Product object.
	 * @param array      $args    Button args.
	 */
	function nuttergood_farmley_loop_add_to_cart_link( $html, $product, $args ) {
		if ( ! nuttergood_farmley_should_apply_product_cards() || ! $product instanceof WC_Product ) {
			return $html;
		}

		$text = $product->add_to_cart_text();
		if ( $product->is_type( 'simple' ) ) {
			$text = __( 'ADD TO CART', 'nuttergood' );
		}

		$is_popular = ! empty( $GLOBALS['ng_farmley_popular_card_actions'] );
		if ( $is_popular ) {
			$text = __( 'Add to Cart', 'nuttergood' );
		}

		$inner = $is_popular && function_exists( 'nuttergood_farmley_popular_button_inner' )
			? nuttergood_farmley_popular_button_inner( 'cart', $text )
			: '<span class="ng-farmley-card-btn__inner"><span class="qodef-m-text">' . esc_html( $text ) . '</span></span>';
		$buy_now = nuttergood_farmley_get_loop_buy_now_link( $product );

		if ( preg_match( '/^(<a\b[^>]*>)(.*?)(<\/a>)/is', $html, $matches ) ) {
			$add_to_cart = $matches[1] . $inner . $matches[3];

			if ( '' !== $buy_now ) {
				return '<div class="ng-farmley-card-buttons">' . $add_to_cart . $buy_now . '</div>';
			}

			return $add_to_cart;
		}

		return $html;
	}
	add_filter( 'woocommerce_loop_add_to_cart_link', 'nuttergood_farmley_loop_add_to_cart_link', 20, 3 );
}

if ( ! function_exists( 'nuttergood_farmley_loop_add_to_cart_args' ) ) {
	/**
	 * @param array      $args    Button args.
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_loop_add_to_cart_args( $args, $product ) {
		if ( ! nuttergood_farmley_should_apply_product_cards() || ! $product instanceof WC_Product ) {
			return $args;
		}

		if ( ! empty( $GLOBALS['ng_farmley_popular_card_actions'] ) ) {
			$args['class'] = trim( ( $args['class'] ?? '' ) . ' ng-farmley-popular-btn' );
			return $args;
		}

		$args['class'] = trim( ( $args['class'] ?? '' ) . ' qodef-button qodef-layout--filled' );

		return $args;
	}
	add_filter( 'woocommerce_loop_add_to_cart_args', 'nuttergood_farmley_loop_add_to_cart_args', 20, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_product_cards_body_class' ) ) {
	function nuttergood_farmley_product_cards_body_class( $classes ) {
		if ( nuttergood_farmley_should_apply_product_cards() ) {
			$classes[] = 'ng-farmley-product-cards';
		}

		return $classes;
	}
	add_filter( 'body_class', 'nuttergood_farmley_product_cards_body_class' );
}

if ( ! function_exists( 'nuttergood_farmley_product_cards_assets' ) ) {
	function nuttergood_farmley_product_cards_assets() {
		if ( is_admin() ) {
			return;
		}

		if ( ! nuttergood_farmley_should_apply_product_cards() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-product-card-enhancements.css';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-product-card-enhancements',
				$uri . '/assets/css/farmley-product-card-enhancements.css',
				array( 'nuttergood-qode-product-list', 'greenpath-style' ),
				filemtime( $css )
			);
		}

		$base_css = $dir . '/assets/css/farmley-product-cards.css';
		if ( file_exists( $base_css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-product-cards',
				$uri . '/assets/css/farmley-product-cards.css',
				array( 'nuttergood-farmley-product-card-enhancements' ),
				filemtime( $base_css )
			);
		}

		$sizes_js = $dir . '/assets/js/farmley-product-card-sizes.js';
		if ( file_exists( $sizes_js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-product-card-sizes',
				$uri . '/assets/js/farmley-product-card-sizes.js',
				array( 'jquery' ),
				filemtime( $sizes_js ),
				true
			);
		}

		$cards_js = $dir . '/assets/js/farmley-product-cards.js';
		if ( file_exists( $cards_js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-product-cards',
				$uri . '/assets/js/farmley-product-cards.js',
				array( 'jquery' ),
				filemtime( $cards_js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_product_cards_assets', 36 );
}