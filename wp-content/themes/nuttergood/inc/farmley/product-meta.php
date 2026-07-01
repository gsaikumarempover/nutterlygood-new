<?php
/**
 * Farmley-style product meta helpers.
 */

if ( ! function_exists( 'nuttergood_farmley_default_packed_by' ) ) {
	function nuttergood_farmley_default_packed_by() {
		if ( function_exists( 'nuttergood_farmley_contact_info' ) ) {
			return nuttergood_farmley_contact_info()['packed_by'];
		}
		return 'Nutterly Good, CS-09, Etna Block, Rajapushpa Atria, Golden Mile Road, Kokapet, Hyderabad, Telangana 500075';
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_product_meta' ) ) {
	/**
	 * @param WC_Product $product Product object.
	 *
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_get_product_meta( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		$id = $product->get_id();

		$mrp         = get_post_meta( $id, '_ng_mrp', true );
		$offer_price = get_post_meta( $id, '_ng_offer_price', true );

		if ( '' === $mrp ) {
			$mrp = $product->get_regular_price();
		}
		if ( '' === $offer_price ) {
			$offer_price = $product->get_sale_price() ? $product->get_sale_price() : $product->get_price();
		}

		$sizes_raw = get_post_meta( $id, '_ng_farmley_sizes', true );
		$sizes     = array();
		if ( ! empty( $sizes_raw ) ) {
			$decoded = json_decode( $sizes_raw, true );
			if ( is_array( $decoded ) ) {
				$sizes = $decoded;
			}
		}

		return array(
			'subtitle'    => (string) get_post_meta( $id, '_ng_subtitle', true ),
			'country'     => (string) get_post_meta( $id, '_ng_country_origin', true ),
			'mrp'         => (string) $mrp,
			'offer_price' => (string) $offer_price,
			'shelf_life'  => (string) get_post_meta( $id, '_ng_shelf_life', true ),
			'ingredients' => (string) get_post_meta( $id, '_ng_ingredients', true ),
			'packed_by'   => (string) get_post_meta( $id, '_ng_packed_by', true ),
			'sizes'       => $sizes,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_gallery_ids' ) ) {
	/**
	 * @param WC_Product $product Product object.
	 *
	 * @return array<int>
	 */
	function nuttergood_farmley_get_gallery_ids( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		$ids = array();

		if ( function_exists( 'nuttergood_farmley_get_card_primary_image_id' ) ) {
			$primary = nuttergood_farmley_get_card_primary_image_id( $product );
			if ( $primary ) {
				$ids[] = (int) $primary;
			}

			if ( function_exists( 'nuttergood_farmley_get_product_hover_image_id' ) ) {
				$hover = nuttergood_farmley_get_product_hover_image_id( $product, $primary );
				if ( $hover && ! in_array( $hover, $ids, true ) ) {
					$ids[] = (int) $hover;
				}
			}
		}

		if ( ! empty( $ids ) ) {
			return $ids;
		}

		$thumb = $product->get_image_id();
		if ( $thumb ) {
			$ids[] = (int) $thumb;
		}

		foreach ( $product->get_gallery_image_ids() as $gid ) {
			$gid = (int) $gid;
			if ( $gid && ! in_array( $gid, $ids, true ) ) {
				$ids[] = $gid;
			}
		}

		return $ids;
	}
}

if ( ! function_exists( 'nuttergood_farmley_format_money' ) ) {
	function nuttergood_farmley_format_money( $amount ) {
		if ( '' === $amount || null === $amount ) {
			return '';
		}

		return wc_price( (float) $amount );
	}
}