<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$show_add_to_cart = apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_show_add_to_cart', true, $product, $item_id );

if ( $show_add_to_cart ) {
	// Using default woocommerce_template_loop_add_to_cart function.
	$default_classes = 'qcfw-m-add-to-cart';

	$has_spinner = ! ( isset( $disable_spinner ) && $disable_spinner );

	if ( $product->is_in_stock() ) {
		$default_classes .= $has_spinner ? ' qcfw-spinner-item' : '';
	} else {
		$default_classes .= ' qcfw--out-of-stock';
	}

	$args = array(
		'quantity'   => 1,
		'class'      => implode(
			' ',
			array_filter(
				array(
					$default_classes,
					qode_compare_for_woocommerce_get_button_classes(),
					'product_type_' . $product->get_type(),
					'add_to_cart_button',
					$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
				)
			)
		),
		'attributes' => array(
			'data-product_id'  => $product->get_id(),
			'data-product_sku' => $product->get_sku(),
			'aria-label'       => $product->add_to_cart_description(),
			'rel'              => 'nofollow',
		),
	);

	if ( isset( $args['attributes']['aria-label'] ) ) {
		$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
	}

	$redirect_to_cart       = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_table_items_redirect_to_cart' );
	$remove_from_comparison = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_table_items_remove_from_comparison' );

	if ( $product->is_type( array( 'simple', 'variation' ) ) && 'yes' === $redirect_to_cart ) {
		$args['attributes']['data-redirect-to-cart'] = esc_url( wc_get_cart_url() );
	}

	if ( ! $product->is_type( 'external' ) && 'yes' === $remove_from_comparison ) {
		$args['attributes']['data-remove-from-comparison'] = true;
	}

	$args = apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_add_to_cart_args', $args, $product );

	if ( $product->is_in_stock() ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters(
			'qode_compare_for_woocommerce_filter_comparison_table_item_add_to_cart',
			sprintf(
				'<a href="%s" data-quantity="%s" class="%s" %s>%s%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( $args['quantity'] ?? 1 ),
				esc_attr( $args['class'] ?? $default_classes ),
				isset( $args['attributes'] ) ? qode_compare_for_woocommerce_get_inline_attrs( $args['attributes'] ) : '',
				'<span class="qcfw-m-add-to-cart-label">' . $product->add_to_cart_text() . '</span>',
				$has_spinner ? '<span class="qcfw-m-add-to-cart-spinner qcfw-spinner-icon">' . qode_compare_for_woocommerce_get_svg_icon( 'spinner' ) . '</span>' : ''
			),
			$product,
			$args,
			$item_id
		);
	} else {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters(
			'qode_compare_for_woocommerce_filter_compare_table_item_view_product',
			sprintf(
				'<a href="%s" class="%s">%s</a>',
				esc_url( $product->get_permalink() ),
				esc_attr( $args['class'] ?? $default_classes ),
				'<span class="qcfw-e-add-to-cart-label">' . $product->add_to_cart_text() . '</span>'
			),
			$product,
			$args,
			$item_id
		);
	}
}
