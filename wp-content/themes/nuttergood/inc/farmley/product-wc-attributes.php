<?php
/**
 * Map Farmley product meta into WooCommerce additional-information attributes.
 */

if ( ! function_exists( 'nuttergood_farmley_additional_detail_fields' ) ) {
	/**
	 * @return array<string, string> Label => meta key.
	 */
	function nuttergood_farmley_additional_detail_fields() {
		return array(
			'Country of origin'              => 'country',
			'Shelf life'                     => 'shelf_life',
			'Ingredients'                    => 'ingredients',
			'Processed, packed & marketed by' => 'packed_by',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_normalize_attribute_label' ) ) {
	/**
	 * @param string $label Attribute label.
	 */
	function nuttergood_farmley_normalize_attribute_label( $label ) {
		return strtolower( preg_replace( '/[^a-z0-9]+/', '', remove_accents( wp_strip_all_tags( $label ) ) ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_additional_detail_values' ) ) {
	/**
	 * @param WC_Product $product Product object.
	 *
	 * @return array<string, string> Label => value.
	 */
	function nuttergood_farmley_get_additional_detail_values( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		$meta   = nuttergood_farmley_get_product_meta( $product );
		$values = array();

		foreach ( nuttergood_farmley_additional_detail_fields() as $label => $key ) {
			$value = isset( $meta[ $key ] ) ? trim( (string) $meta[ $key ] ) : '';

			if ( 'country' === $key && '' === $value ) {
				$value = 'India';
			}

			if ( 'shelf_life' === $key && '' === $value ) {
				$value = '9 Months';
			}

			if ( 'packed_by' === $key && '' === $value ) {
				$value = nuttergood_farmley_default_packed_by();
			}

			if ( '' !== $value ) {
				$values[ $label ] = $value;
			}
		}

		$weight_display = nuttergood_farmley_get_product_weight_display( $product );
		if ( '' !== $weight_display ) {
			$values[ __( 'Net weight', 'nuttergood' ) ] = $weight_display;
		}

		return $values;
	}
}

if ( ! function_exists( 'nuttergood_farmley_replace_product_display_attributes' ) ) {
	/**
	 * Show each Farmley detail once (avoids duplicate rows from synced WC attributes).
	 *
	 * @param array<string, array<string, string>> $product_attributes Existing rows.
	 * @param WC_Product                         $product            Product.
	 *
	 * @return array<string, array<string, string>>
	 */
	function nuttergood_farmley_replace_product_display_attributes( $product_attributes, $product ) {
		if ( ! $product instanceof WC_Product || ! nuttergood_farmley_is_single_product_page() ) {
			return $product_attributes;
		}

		$details = nuttergood_farmley_get_additional_detail_values( $product );
		if ( empty( $details ) ) {
			return array();
		}

		$rows = array();
		foreach ( $details as $label => $value ) {
			$rows[ 'ng_detail_' . sanitize_title( $label ) ] = array(
				'label' => $label,
				'value' => wp_kses_post( wptexturize( $value ) ),
			);
		}

		return $rows;
	}

	add_filter( 'woocommerce_display_product_attributes', 'nuttergood_farmley_replace_product_display_attributes', 99, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_render_additional_details_panel' ) ) {
	/**
	 * Custom additional-information tab markup (clean, aligned list).
	 */
	function nuttergood_farmley_render_additional_details_panel() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$details = nuttergood_farmley_get_additional_detail_values( $product );
		if ( empty( $details ) ) {
			return;
		}

		echo '<div class="ng-farmley-sp-details">';
		echo '<table class="woocommerce-product-attributes shop_attributes ng-farmley-sp-details__table">';
		echo '<tbody>';

		foreach ( $details as $label => $value ) {
			printf(
				'<tr class="ng-farmley-sp-details__row"><th class="ng-farmley-sp-details__label">%1$s</th><td class="ng-farmley-sp-details__value">%2$s</td></tr>',
				esc_html( $label ),
				wp_kses_post( wptexturize( $value ) )
			);
		}

		echo '</tbody></table></div>';
	}
}


if ( ! function_exists( 'nuttergood_farmley_sync_product_wc_attributes' ) ) {
	/**
	 * Persist Farmley meta as visible custom product attributes in WooCommerce.
	 *
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_sync_product_wc_attributes( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$details    = nuttergood_farmley_get_additional_detail_values( $product );
		$attributes = $product->get_attributes();

		foreach ( array_keys( $attributes ) as $key ) {
			if ( 0 === strpos( $key, 'ng_' ) ) {
				unset( $attributes[ $key ] );
			}
		}

		// Remove legacy/imported duplicates that match our detail labels.
		$known_labels = array_map( 'nuttergood_farmley_normalize_attribute_label', array_keys( nuttergood_farmley_get_additional_detail_values( $product ) ) );
		foreach ( $attributes as $key => $attribute ) {
			if ( ! $attribute instanceof WC_Product_Attribute ) {
				continue;
			}
			$name = nuttergood_farmley_normalize_attribute_label( wc_attribute_label( $attribute->get_name(), $product ) );
			if ( in_array( $name, $known_labels, true ) ) {
				unset( $attributes[ $key ] );
			}
		}

		foreach ( $details as $label => $value ) {
			$key = 'ng_' . sanitize_title( $label );

			$attribute = new WC_Product_Attribute();
			$attribute->set_id( 0 );
			$attribute->set_name( $label );
			$attribute->set_options( array( $value ) );
			$attribute->set_position( count( $attributes ) );
			$attribute->set_visible( true );
			$attribute->set_variation( false );

			$attributes[ $key ] = $attribute;
		}

		$product->set_attributes( $attributes );
	}

	add_action( 'woocommerce_admin_process_product_object', 'nuttergood_farmley_sync_product_wc_attributes', 25 );
}

if ( ! function_exists( 'nuttergood_farmley_maybe_bulk_sync_product_attributes' ) ) {
	/**
	 * One-time sync for existing products (admin only).
	 */
	function nuttergood_farmley_maybe_bulk_sync_product_attributes() {
		if ( 'yes' === get_option( 'ng_farmley_wc_attrs_synced_v3', '' ) ) {
			return;
		}

		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		$ids = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		foreach ( $ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				nuttergood_farmley_sync_product_wc_attributes( $product );
				$product->save();
			}
		}

		update_option( 'ng_farmley_wc_attrs_synced_v3', 'yes', false );
	}

	add_action( 'init', 'nuttergood_farmley_maybe_bulk_sync_product_attributes', 21 );
}

if ( ! function_exists( 'nuttergood_farmley_maybe_seed_product_story_defaults' ) ) {
	/**
	 * One-time: set starter bullets for products missing Product Story data.
	 */
	function nuttergood_farmley_maybe_seed_product_story_defaults() {
		if ( 'yes' === get_option( 'ng_farmley_story_seeded_v2', '' ) ) {
			return;
		}

		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		$default_highlights = array(
			array( 'key' => 'gluten_free', 'image_id' => 0 ),
			array( 'key' => 'preservative_free', 'image_id' => 0 ),
			array( 'key' => 'no_added_sugar', 'image_id' => 0 ),
		);

		$ids = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		foreach ( $ids as $product_id ) {
			$product_id = (int) $product_id;

			delete_post_meta( $product_id, '_ng_story_blocks' );

			if ( '' === get_post_meta( $product_id, '_ng_product_highlights', true ) ) {
				update_post_meta( $product_id, '_ng_product_highlights', wp_json_encode( $default_highlights ) );
			}
		}

		update_option( 'ng_farmley_story_seeded_v2', 'yes', false );
	}

	add_action( 'init', 'nuttergood_farmley_maybe_seed_product_story_defaults', 20 );
}
