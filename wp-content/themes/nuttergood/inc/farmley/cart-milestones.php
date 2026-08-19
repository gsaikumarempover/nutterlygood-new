<?php
/**
 * Configurable cart milestone rewards (WooCommerce → Cart Milestones).
 * Free delivery + tiered % discounts. Auto-applied; no manual coupon UI.
 */

if ( ! defined( 'NG_FARMLEY_CART_MILESTONES_OPTION' ) ) {
	define( 'NG_FARMLEY_CART_MILESTONES_OPTION', 'ng_farmley_cart_milestones_raw' );
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestones_default_raw' ) ) {
	function nuttergood_farmley_cart_milestones_default_raw() {
		return implode(
			"\n",
			array(
				'1000 | 10 | 10% OFF',
				'1500 | 15 | 15% OFF',
				'2500 | 20 | 20% OFF',
			)
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_parse_cart_milestones' ) ) {
	/**
	 * @return array<int, array{amount:int,type:string,percent:float,label:string,icon:string}>
	 */
	function nuttergood_farmley_parse_cart_milestones( $raw ) {
		$milestones = array();
		$lines      = preg_split( '/\r\n|\r|\n/', (string) $raw );

		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( '' === $line || '#' === $line[0] ) {
				continue;
			}

			$parts = array_map( 'trim', explode( '|', $line ) );
			if ( count( $parts ) < 2 ) {
				continue;
			}

			$amount = (int) preg_replace( '/[^\d]/', '', $parts[0] );
			if ( $amount <= 0 ) {
				continue;
			}

			$type    = strtolower( $parts[1] );
			$label   = isset( $parts[2] ) && '' !== $parts[2] ? $parts[2] : '';
			$percent = 0.0;
			$icon    = 'truck';

			if ( in_array( $type, array( 'free', 'free_delivery', 'delivery' ), true ) ) {
				$type  = 'free_delivery';
				$icon  = 'gift';
				$label = '' !== $label ? $label : __( 'Free delivery', 'nuttergood' );
			} else {
				$percent = (float) preg_replace( '/[^\d.]/', '', $parts[1] );
				$type    = 'percent';
				if ( $percent <= 0 && '' !== $label && preg_match( '/(\d+(?:\.\d+)?)\s*%/', $label, $match ) ) {
					$percent = (float) $match[1];
				}
				if ( $percent <= 0 ) {
					continue;
				}
				if ( '' === $label ) {
					$label = sprintf(
						/* translators: %s: percent amount */
						__( '%s%% OFF', 'nuttergood' ),
						wc_format_decimal( $percent, 0 )
					);
				}
			}

			$milestones[] = array(
				'amount'  => $amount,
				'type'    => $type,
				'percent' => $percent,
				'label'   => $label,
				'icon'    => $icon,
			);
		}

		usort(
			$milestones,
			static function ( $a, $b ) {
				return (int) $a['amount'] <=> (int) $b['amount'];
			}
		);

		return $milestones;
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestones' ) ) {
	/**
	 * @return array<int, array{amount:int,type:string,percent:float,label:string,icon:string}>
	 */
	function nuttergood_farmley_cart_milestones() {
		$raw = get_option( NG_FARMLEY_CART_MILESTONES_OPTION, false );
		if ( false === $raw || '' === trim( (string) $raw ) ) {
			$raw = nuttergood_farmley_cart_milestones_default_raw();
		}

		$milestones = nuttergood_farmley_parse_cart_milestones( $raw );

		// Free delivery amount comes from WooCommerce → Free Delivery (single source of truth).
		$milestones = array_values(
			array_filter(
				$milestones,
				static function ( $milestone ) {
					return 'free_delivery' !== $milestone['type'];
				}
			)
		);

		if ( function_exists( 'nuttergood_farmley_free_delivery_min_amount' ) ) {
			$free_min = (int) nuttergood_farmley_free_delivery_min_amount();
			if ( $free_min > 0 ) {
				$milestones[] = array(
					'amount'  => $free_min,
					'type'    => 'free_delivery',
					'percent' => 0.0,
					'label'   => __( 'Free delivery', 'nuttergood' ),
					'icon'    => 'gift',
				);
			}
		}

		usort(
			$milestones,
			static function ( $a, $b ) {
				return (int) $a['amount'] <=> (int) $b['amount'];
			}
		);

		return apply_filters( 'nuttergood_farmley_cart_milestones', $milestones );
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestone_subtotal' ) ) {
	function nuttergood_farmley_cart_milestone_subtotal() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return 0.0;
		}

		$cart     = WC()->cart;
		$subtotal = (float) $cart->get_cart_contents_total();

		if ( $cart->display_prices_including_tax() ) {
			$subtotal += (float) $cart->get_cart_contents_tax();
		}

		return max( 0.0, $subtotal );
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestone_progress_data' ) ) {
	/**
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_cart_milestone_progress_data() {
		$milestones = nuttergood_farmley_cart_milestones();
		$defaults   = array(
			'milestones'   => $milestones,
			'subtotal'     => 0.0,
			'max'          => ! empty( $milestones ) ? (int) end( $milestones )['amount'] : 399,
			'percent'      => 0,
			'next'         => ! empty( $milestones ) ? $milestones[0] : null,
			'all_unlocked' => false,
			'item_count'   => 0,
		);

		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return $defaults;
		}

		$subtotal = nuttergood_farmley_cart_milestone_subtotal();
		$max      = ! empty( $milestones ) ? (int) end( $milestones )['amount'] : 399;
		$max      = max( 1, $max );
		$percent  = min( 100, ( $subtotal / $max ) * 100 );

		$next = null;
		foreach ( $milestones as $milestone ) {
			if ( $subtotal < (float) $milestone['amount'] ) {
				$next = $milestone;
				break;
			}
		}

		$all_unlocked = null === $next;
		$item_count   = WC()->cart->get_cart_contents_count();

		if ( $all_unlocked ) {
			$percent = 100;
		}

		return compact( 'milestones', 'subtotal', 'max', 'percent', 'next', 'all_unlocked', 'item_count' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestone_plain_price' ) ) {
	function nuttergood_farmley_cart_milestone_plain_price( $amount ) {
		$text = html_entity_decode( wp_strip_all_tags( wc_price( (float) $amount ) ), ENT_QUOTES, 'UTF-8' );
		$text = preg_replace( '/\s+/u', ' ', $text );

		return is_string( $text ) ? trim( $text ) : '';
	}
}

if ( ! defined( 'NG_FARMLEY_REF_DELIVERY_FEE_OPTION' ) ) {
	define( 'NG_FARMLEY_REF_DELIVERY_FEE_OPTION', 'ng_farmley_reference_delivery_fee' );
}

if ( ! function_exists( 'nuttergood_farmley_cart_reference_delivery_fee' ) ) {
	/**
	 * Reference courier charge shown as delivery savings when free delivery is unlocked.
	 */
	function nuttergood_farmley_cart_reference_delivery_fee() {
		$fee = (float) get_option( NG_FARMLEY_REF_DELIVERY_FEE_OPTION, 79 );
		return (float) apply_filters( 'nuttergood_farmley_cart_reference_delivery_fee', max( 0, $fee ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_has_free_delivery_unlocked' ) ) {
	function nuttergood_farmley_cart_has_free_delivery_unlocked() {
		if ( function_exists( 'nuttergood_farmley_cart_has_free_shipping' ) ) {
			return nuttergood_farmley_cart_has_free_shipping();
		}

		$subtotal = nuttergood_farmley_cart_milestone_subtotal();
		$min      = function_exists( 'nuttergood_farmley_free_delivery_min_amount' )
			? nuttergood_farmley_free_delivery_min_amount()
			: 399;

		return $min <= 0 || $subtotal >= (float) $min;
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestone_discount_amount' ) ) {
	function nuttergood_farmley_cart_milestone_discount_amount() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return 0.0;
		}

		$total = 0.0;
		foreach ( WC()->cart->get_fees() as $fee ) {
			if ( (float) $fee->amount >= 0 ) {
				continue;
			}
			foreach ( nuttergood_farmley_cart_milestones() as $milestone ) {
				if ( 'percent' === $milestone['type'] && (string) $fee->name === (string) $milestone['label'] ) {
					$total += abs( (float) $fee->amount );
					break;
				}
			}
		}

		if ( $total <= 0 ) {
			$active = nuttergood_farmley_cart_milestone_active_discount();
			if ( $active && ! empty( $active['percent'] ) ) {
				$total = max( 0, nuttergood_farmley_cart_milestone_subtotal() * ( (float) $active['percent'] / 100 ) );
			}
		}

		return $total;
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_catalog_product_for_item' ) ) {
	/**
	 * Fresh catalog product for a cart row (variation when set).
	 *
	 * Cart session clones can drop regular/MRP meta; reload from DB for savings math.
	 *
	 * @param array<string, mixed> $cart_item Cart row.
	 * @param WC_Product|null      $product   Fallback product.
	 */
	function nuttergood_farmley_cart_catalog_product_for_item( $cart_item, $product = null ) {
		$product_id = 0;

		if ( is_array( $cart_item ) ) {
			if ( ! empty( $cart_item['variation_id'] ) ) {
				$product_id = (int) $cart_item['variation_id'];
			} elseif ( ! empty( $cart_item['product_id'] ) ) {
				$product_id = (int) $cart_item['product_id'];
			}
		}

		if ( $product_id <= 0 && $product instanceof WC_Product ) {
			$product_id = (int) $product->get_id();
		}

		if ( $product_id > 0 ) {
			$catalog = wc_get_product( $product_id );
			if ( $catalog instanceof WC_Product && $catalog->exists() ) {
				return $catalog;
			}
		}

		return $product instanceof WC_Product ? $product : null;
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_item_display_unit_regular' ) ) {
	/**
	 * Regular (MRP) unit price as shown in cart / side cart strikethrough.
	 *
	 * @param WC_Product $product Product.
	 */
	function nuttergood_farmley_cart_item_display_unit_regular( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return 0.0;
		}

		if (
			function_exists( 'nuttergood_farmley_regular_tax_enabled' )
			&& nuttergood_farmley_regular_tax_enabled()
			&& function_exists( 'nuttergood_farmley_get_mrp_inclusive_unit_price' )
		) {
			return (float) nuttergood_farmley_get_mrp_inclusive_unit_price( $product );
		}

		if ( function_exists( 'nuttergood_farmley_resolve_card_prices' ) ) {
			$prices = nuttergood_farmley_resolve_card_prices( $product );
			if ( ! empty( $prices['mrp'] ) && (float) $prices['mrp'] > 0 ) {
				return (float) $prices['mrp'];
			}
		}

		$regular = (float) $product->get_regular_price();
		if ( $regular <= 0 ) {
			return 0.0;
		}

		return (float) wc_get_price_to_display( $product, array( 'price' => $regular ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_item_display_unit_current' ) ) {
	/**
	 * Payable unit price as shown in cart / side cart.
	 *
	 * @param WC_Product $product Product.
	 */
	function nuttergood_farmley_cart_item_display_unit_current( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return 0.0;
		}

		if (
			function_exists( 'nuttergood_farmley_regular_tax_enabled' )
			&& nuttergood_farmley_regular_tax_enabled()
			&& function_exists( 'nuttergood_farmley_get_payable_unit_price' )
		) {
			return (float) nuttergood_farmley_get_payable_unit_price( $product );
		}

		if ( function_exists( 'nuttergood_farmley_resolve_card_prices' ) ) {
			$prices = nuttergood_farmley_resolve_card_prices( $product );
			if ( ! empty( $prices['offer'] ) && (float) $prices['offer'] > 0 ) {
				return (float) $prices['offer'];
			}
		}

		return (float) wc_get_price_to_display( $product );
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_product_sale_savings_amount' ) ) {
	/**
	 * Sum of per-line sale savings (regular/MRP minus payable price × qty).
	 */
	function nuttergood_farmley_cart_product_sale_savings_amount() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return 0.0;
		}

		$total = 0.0;

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
			if ( ! $product instanceof WC_Product || ! $product->exists() ) {
				continue;
			}

			$qty = max( 0, (int) ( $cart_item['quantity'] ?? 0 ) );
			if ( $qty <= 0 ) {
				continue;
			}

			$catalog = nuttergood_farmley_cart_catalog_product_for_item( $cart_item, $product );
			if ( ! $catalog instanceof WC_Product ) {
				continue;
			}

			$regular = nuttergood_farmley_cart_item_display_unit_regular( $catalog );
			$current = nuttergood_farmley_cart_item_display_unit_current( $catalog );

			if ( $regular <= 0 || $regular <= $current ) {
				continue;
			}

			$total += ( $regular - $current ) * $qty;
		}

		return (float) wc_format_decimal( max( 0, $total ), wc_get_price_decimals() );
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_total_savings_breakdown' ) ) {
	/**
	 * @return array{total:float,discount:float,delivery:float,sale:float,items:array<int,array{label:string,amount:float}>}
	 */
	function nuttergood_farmley_cart_total_savings_breakdown() {
		$items    = array();
		$sale     = nuttergood_farmley_cart_product_sale_savings_amount();
		$discount = nuttergood_farmley_cart_milestone_discount_amount();
		$delivery = 0.0;

		if ( $sale > 0 ) {
			$items[] = array(
				'label'  => __( 'Product discount', 'nuttergood' ),
				'amount' => $sale,
			);
		}

		if ( $discount > 0 ) {
			$active = nuttergood_farmley_cart_milestone_active_discount();
			$items[] = array(
				'label'  => $active ? (string) $active['label'] : __( 'Order discount', 'nuttergood' ),
				'amount' => $discount,
			);
		}

		if ( nuttergood_farmley_cart_has_free_delivery_unlocked() ) {
			$delivery = nuttergood_farmley_cart_reference_delivery_fee();
			if ( $delivery > 0 ) {
				$items[] = array(
					'label'  => __( 'Free delivery', 'nuttergood' ),
					'amount' => $delivery,
				);
			}
		}

		return array(
			'total'    => $sale + $discount + $delivery,
			'discount' => $discount,
			'delivery' => $delivery,
			'sale'     => $sale,
			'items'    => $items,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_cart_total_savings' ) ) {
	/**
	 * @param string $context side-cart|cart-page
	 */
	function nuttergood_farmley_render_cart_total_savings( $context = 'side-cart' ) {
		$breakdown = nuttergood_farmley_cart_total_savings_breakdown();
		if ( $breakdown['total'] <= 0 ) {
			return;
		}

		$class = 'side-cart' === $context
			? 'ng-farmley-total-savings ng-farmley-total-savings--sc'
			: 'ng-farmley-total-savings ng-farmley-total-savings--cart';

		if ( 'cart-page' === $context ) {
			echo '<tr class="ng-farmley-total-savings-row">';
			echo '<th>' . esc_html__( 'Total savings', 'nuttergood' ) . '</th>';
			echo '<td data-title="' . esc_attr__( 'Total savings', 'nuttergood' ) . '">−' . wp_kses_post( wc_price( $breakdown['total'] ) ) . '</td>';
			echo '</tr>';
			return;
		}

		if ( 'checkout' === $context ) {
			?>
			<div class="ng-farmley-total-savings ng-farmley-total-savings--checkout">
				<div class="ng-farmley-total-savings__banner">
					<span class="ng-farmley-total-savings__icon" aria-hidden="true">🎉</span>
					<div class="ng-farmley-total-savings__copy">
						<p class="ng-farmley-total-savings__label"><?php esc_html_e( 'You saved on this order', 'nuttergood' ); ?></p>
						<p class="ng-farmley-total-savings__amount"><?php echo wp_kses_post( wc_price( $breakdown['total'] ) ); ?></p>
					</div>
				</div>
				<?php if ( ! empty( $breakdown['items'] ) ) : ?>
					<ul class="ng-farmley-total-savings__list">
						<?php foreach ( $breakdown['items'] as $item ) : ?>
							<li>
								<span><?php echo esc_html( $item['label'] ); ?></span>
								<span><?php echo wp_kses_post( wc_price( $item['amount'] ) ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php
			return;
		}

		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<div class="ng-farmley-total-savings__banner">
				<span class="ng-farmley-total-savings__icon" aria-hidden="true">🎉</span>
				<div class="ng-farmley-total-savings__copy">
					<p class="ng-farmley-total-savings__label"><?php esc_html_e( 'Total savings', 'nuttergood' ); ?></p>
					<p class="ng-farmley-total-savings__amount"><?php echo wp_kses_post( wc_price( $breakdown['total'] ) ); ?></p>
				</div>
			</div>
			<?php if ( ! empty( $breakdown['items'] ) ) : ?>
				<ul class="ng-farmley-total-savings__list">
					<?php foreach ( $breakdown['items'] as $item ) : ?>
						<li>
							<span><?php echo esc_html( $item['label'] ); ?></span>
							<span><?php echo wp_kses_post( wc_price( $item['amount'] ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_checkout_total_savings' ) ) {
	/**
	 * Checkout order-review savings row, matching the simplified cart-2 row.
	 */
	function nuttergood_farmley_render_checkout_total_savings() {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) ) {
			return;
		}

		$breakdown = nuttergood_farmley_cart_total_savings_breakdown();
		if ( $breakdown['total'] <= 0 ) {
			return;
		}

		echo '<tr class="ng-farmley-total-savings-row ng-farmley-total-savings-row--checkout">';
		echo '<th>' . esc_html__( 'Total savings', 'nuttergood' ) . '</th>';
		echo '<td data-title="' . esc_attr__( 'Total savings', 'nuttergood' ) . '">−' . wp_kses_post( wc_price( $breakdown['total'] ) ) . '</td>';
		echo '</tr>';
	}
	add_action( 'woocommerce_review_order_before_order_total', 'nuttergood_farmley_render_checkout_total_savings', 8 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_can_show_rewards_ui' ) ) {
	/**
	 * Whether checkout should show milestone / free-delivery messaging.
	 */
	function nuttergood_farmley_checkout_can_show_rewards_ui() {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
			return false;
		}

		if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
			return false;
		}

		if ( function_exists( 'nuttergood_farmley_is_order_pay_page' ) && nuttergood_farmley_is_order_pay_page() ) {
			return false;
		}

		return function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty();
	}
}

if ( ! function_exists( 'nuttergood_farmley_checkout_related_product_ids' ) ) {
	/**
	 * Cross-sell IDs for checkout "explore more" row (cross-sells → related fallback).
	 *
	 * @param int $limit Max products.
	 * @return array<int>
	 */
	function nuttergood_farmley_checkout_related_product_ids( $limit = 4 ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return array();
		}

		$in_cart     = array();
		$cross_sells = array();
		$related_ids = array();

		foreach ( WC()->cart->get_cart() as $item ) {
			$pid       = (int) $item['product_id'];
			$in_cart[] = $pid;
			$product   = isset( $item['data'] ) ? $item['data'] : wc_get_product( $pid );
			if ( $product instanceof WC_Product ) {
				$cross_sells = array_merge( $cross_sells, $product->get_cross_sell_ids() );
				$related_ids = array_merge( $related_ids, array_keys( wc_get_related_products( $pid, $limit ) ) );
			}
		}

		$in_cart = array_values( array_unique( $in_cart ) );
		$pool    = array_values( array_unique( array_merge( $cross_sells, $related_ids ) ) );

		$ids = array();
		foreach ( $pool as $id ) {
			$id = (int) $id;
			if ( ! $id || in_array( $id, $in_cart, true ) || in_array( $id, $ids, true ) ) {
				continue;
			}
			$p = wc_get_product( $id );
			if ( $p && $p->is_purchasable() && $p->is_in_stock() && $p->is_visible() ) {
				$ids[] = $id;
			}
			if ( count( $ids ) >= $limit ) {
				break;
			}
		}

		return $ids;
	}
}

if ( ! function_exists( 'nuttergood_farmley_checkout_render_related_products' ) ) {
	/**
	 * Render a small "You might like" row below checkout milestones.
	 * Uses the same compact card style as the side-cart recommendations.
	 */
	function nuttergood_farmley_checkout_render_related_products() {
		$ids = nuttergood_farmley_checkout_related_product_ids( 4 );
		if ( empty( $ids ) ) {
			return;
		}

		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		?>
		<div class="ng-farmley-checkout-explore">
			<div class="ng-farmley-checkout-explore__header">
				<p class="ng-farmley-checkout-explore__label"><?php esc_html_e( 'You might like', 'nuttergood' ); ?></p>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="ng-farmley-checkout-explore__cta">
					<?php esc_html_e( 'Explore more', 'nuttergood' ); ?>
					<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
				</a>
			</div>
			<div class="ng-farmley-checkout-explore__row">
				<?php foreach ( $ids as $product_id ) :
					$product = wc_get_product( $product_id );
					if ( ! $product instanceof WC_Product ) {
						continue;
					}
					$weight_label = function_exists( 'nuttergood_farmley_side_cart_product_weight_label' )
						? nuttergood_farmley_side_cart_product_weight_label( $product )
						: '';
					?>
					<article class="ng-farmley-sc-reco__item ng-farmley-checkout-explore__item">
						<a class="ng-farmley-sc-reco__img" href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
						</a>
						<div class="ng-farmley-sc-reco__body">
							<h4 class="ng-farmley-sc-reco__name">
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
							</h4>
							<?php if ( $weight_label ) : ?>
								<p class="ng-farmley-sc-reco__weight"><?php echo esc_html( $weight_label ); ?></p>
							<?php endif; ?>
							<p class="ng-farmley-sc-reco__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
							<?php if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>
								<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
									class="ng-farmley-sc-reco__add add_to_cart_button ajax_add_to_cart"
									data-quantity="1"
									data-product_id="<?php echo esc_attr( (string) $product_id ); ?>"
									aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>">
									<?php esc_html_e( '+ Add', 'nuttergood' ); ?>
								</a>
							<?php else : ?>
								<a class="ng-farmley-sc-reco__add ng-farmley-sc-reco__add--view" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php esc_html_e( 'View', 'nuttergood' ); ?></a>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_checkout_render_milestone_progress' ) ) {
	/**
	 * Milestone + free-delivery progress on checkout so shoppers see what they unlock next.
	 */
	function nuttergood_farmley_checkout_render_milestone_progress() {
		if (
			! nuttergood_farmley_checkout_can_show_rewards_ui()
			|| ! function_exists( 'nuttergood_farmley_render_cart_milestone_progress' )
		) {
			return;
		}

		$data = nuttergood_farmley_cart_milestone_progress_data();

		echo '<div id="ng-farmley-checkout-milestones" class="ng-farmley-checkout-milestones-wrap ng-farmley-checkout-milestones-wrap--lead">';
		nuttergood_farmley_render_cart_milestone_progress( 'checkout' );

		if ( ! $data['all_unlocked'] ) {
			nuttergood_farmley_checkout_render_related_products();
		}

		echo '</div>';
	}
	add_action( 'woocommerce_before_checkout_form', 'nuttergood_farmley_checkout_render_milestone_progress', 9 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_render_milestone_savings' ) ) {
	/**
	 * Savings banner above checkout totals when milestone rewards are already active.
	 */
	function nuttergood_farmley_checkout_render_milestone_savings() {
		if (
			! nuttergood_farmley_checkout_can_show_rewards_ui()
			|| ! function_exists( 'nuttergood_farmley_render_cart_total_savings' )
		) {
			return;
		}

		echo '<div id="ng-farmley-checkout-savings" class="ng-farmley-checkout-savings-wrap">';
		nuttergood_farmley_render_cart_total_savings( 'checkout' );
		echo '</div>';
	}
	add_action( 'woocommerce_checkout_before_order_review', 'nuttergood_farmley_checkout_render_milestone_savings', 8 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_milestone_fragments' ) ) {
	/**
	 * Keep checkout milestone UI in sync when order review refreshes.
	 *
	 * @param array<string, string> $fragments Checkout fragments.
	 * @return array<string, string>
	 */
	function nuttergood_farmley_checkout_milestone_fragments( $fragments ) {
		try {
			$data = nuttergood_farmley_cart_milestone_progress_data();

			ob_start();
			echo '<div id="ng-farmley-checkout-milestones" class="ng-farmley-checkout-milestones-wrap ng-farmley-checkout-milestones-wrap--lead">';
			if ( function_exists( 'nuttergood_farmley_render_cart_milestone_progress' ) ) {
				nuttergood_farmley_render_cart_milestone_progress( 'checkout' );
			}
			if ( ! $data['all_unlocked'] && function_exists( 'nuttergood_farmley_checkout_render_related_products' ) ) {
				nuttergood_farmley_checkout_render_related_products();
			}
			echo '</div>';
			$fragments['#ng-farmley-checkout-milestones'] = ob_get_clean();

			ob_start();
			echo '<div id="ng-farmley-checkout-savings" class="ng-farmley-checkout-savings-wrap">';
			if ( function_exists( 'nuttergood_farmley_render_cart_total_savings' ) ) {
				nuttergood_farmley_render_cart_total_savings( 'checkout' );
			}
			echo '</div>';
			$fragments['#ng-farmley-checkout-savings'] = ob_get_clean();
		} catch ( \Throwable $e ) {
			// Swallow errors so we never corrupt WC's JSON response.
			if ( ob_get_level() > 0 ) {
				ob_end_clean();
			}
		}

		return $fragments;
	}
	add_filter( 'woocommerce_update_order_review_fragments', 'nuttergood_farmley_checkout_milestone_fragments' );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_total_savings' ) ) {
	/**
	 * Replace the order-pay shipping line with a savings line for Razorpay payment screens.
	 *
	 * @param array    $total_rows Order total rows.
	 * @param WC_Order $order      Order object.
	 * @return array
	 */
	function nuttergood_farmley_order_pay_total_savings( $total_rows, $order ) {
		if (
			! function_exists( 'is_checkout' )
			|| ! is_checkout()
			|| ! function_exists( 'is_wc_endpoint_url' )
			|| ! is_wc_endpoint_url( 'order-pay' )
			|| ! $order instanceof WC_Order
		) {
			return $total_rows;
		}

		$savings = max( 0, (float) $order->get_shipping_total() + (float) $order->get_shipping_tax() );
		foreach ( $order->get_fees() as $fee ) {
			$fee_total = (float) $fee->get_total();
			if ( $fee_total < 0 ) {
				$savings += abs( $fee_total );
			}
		}

		if ( $savings <= 0 ) {
			return $total_rows;
		}

		unset( $total_rows['shipping'] );

		$savings_row = array(
			'label' => __( 'Total savings:', 'nuttergood' ),
			'value' => '−' . wc_price( $savings ),
		);

		$rebuilt = array();
		foreach ( $total_rows as $key => $row ) {
			if ( 'order_total' === $key ) {
				$rebuilt['ng_total_savings'] = $savings_row;
			}
			$rebuilt[ $key ] = $row;
		}

		return $rebuilt;
	}
	add_filter( 'woocommerce_get_order_item_totals', 'nuttergood_farmley_order_pay_total_savings', 20, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestone_active_discount' ) ) {
	/**
	 * Highest unlocked percent milestone.
	 *
	 * @return array{amount:int,type:string,percent:float,label:string,icon:string}|null
	 */
	function nuttergood_farmley_cart_milestone_active_discount( $subtotal = null ) {
		if ( null === $subtotal ) {
			$subtotal = nuttergood_farmley_cart_milestone_subtotal();
		}

		$active = null;
		foreach ( nuttergood_farmley_cart_milestones() as $milestone ) {
			if ( 'percent' !== $milestone['type'] ) {
				continue;
			}
			if ( $subtotal >= (float) $milestone['amount'] ) {
				$active = $milestone;
			}
		}

		return $active;
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_cart_milestone_progress' ) ) {
	/**
	 * @param string $context side-cart|cart-page
	 */
	function nuttergood_farmley_render_cart_milestone_progress( $context = 'side-cart' ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return;
		}

		$data = nuttergood_farmley_cart_milestone_progress_data();
		if ( 'checkout' === $context ) {
			$wrapper_class = 'ng-farmley-sc-progress ng-farmley-checkout-progress';
		} elseif ( 'cart-page' === $context ) {
			$wrapper_class = 'ng-farmley-sc-progress ng-farmley-cart-progress';
		} else {
			$wrapper_class = 'ng-farmley-sc-progress';
		}
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>" data-ng-sc-progress data-percent="<?php echo esc_attr( (string) round( $data['percent'] ) ); ?>">
			<?php if ( $data['all_unlocked'] ) : ?>
				<p class="ng-farmley-sc-progress__msg ng-farmley-sc-progress__msg--done">
					<span aria-hidden="true">🎉</span>
					<?php esc_html_e( 'Congratulations! You unlocked all cart rewards.', 'nuttergood' ); ?>
				</p>
			<?php else : ?>
				<p class="ng-farmley-sc-progress__msg">
					<span aria-hidden="true"><?php echo 'free_delivery' === $data['next']['type'] ? '🎁' : '🚚'; ?></span>
					<?php
				$remaining = max( 0, (float) $data['next']['amount'] - $data['subtotal'] );
				if ( 'free_delivery' === $data['next']['type'] ) {
					if ( 'checkout' === $context ) {
						printf(
							/* translators: 1: minimum order amount, 2: price remaining */
							esc_html__( 'Free delivery for orders above %1$s — add items worth %2$s more', 'nuttergood' ),
							esc_html( nuttergood_farmley_cart_milestone_plain_price( $data['next']['amount'] ) ),
							esc_html( nuttergood_farmley_cart_milestone_plain_price( $remaining ) )
						);
					} else {
						printf(
							/* translators: 1: minimum order amount, 2: price remaining */
							esc_html__( 'Free delivery for orders above %1$s — spend %2$s more', 'nuttergood' ),
							esc_html( nuttergood_farmley_cart_milestone_plain_price( $data['next']['amount'] ) ),
							esc_html( nuttergood_farmley_cart_milestone_plain_price( $remaining ) )
						);
					}
				} else {
					if ( 'checkout' === $context ) {
						printf(
							/* translators: 1: price remaining, 2: reward label */
							esc_html__( 'Add items worth %1$s more to unlock %2$s', 'nuttergood' ),
							esc_html( nuttergood_farmley_cart_milestone_plain_price( $remaining ) ),
							esc_html( $data['next']['label'] )
						);
					} else {
						printf(
							/* translators: 1: price remaining, 2: reward label */
							esc_html__( 'Spend %1$s more to unlock %2$s', 'nuttergood' ),
							esc_html( nuttergood_farmley_cart_milestone_plain_price( $remaining ) ),
							esc_html( $data['next']['label'] )
						);
					}
				}
					?>
				</p>
			<?php endif; ?>

			<div class="ng-farmley-sc-progress__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( (string) round( $data['percent'] ) ); ?>">
				<div class="ng-farmley-sc-progress__rail">
					<div class="ng-farmley-sc-progress__fill" data-ng-sc-fill style="width:<?php echo esc_attr( (string) round( $data['percent'] ) ); ?>%"></div>
					<?php
					$milestone_count = count( $data['milestones'] );
					foreach ( $data['milestones'] as $index => $milestone ) :
						$marker_pct = min( 100, ( (float) $milestone['amount'] / $data['max'] ) * 100 );
						$reached    = $data['subtotal'] >= (float) $milestone['amount'];
						$pin_class  = 'ng-farmley-sc-progress__pin';
						if ( $reached ) {
							$pin_class .= ' is-on';
						}
						if ( $index === $milestone_count - 1 ) {
							$pin_class .= ' is-end';
						}
						?>
						<div
							class="<?php echo esc_attr( $pin_class ); ?>"
							style="--pin-left: <?php echo esc_attr( (string) $marker_pct ); ?>%"
							title="<?php echo esc_attr( nuttergood_farmley_cart_milestone_plain_price( $milestone['amount'] ) . ' — ' . $milestone['label'] ); ?>"
						>
							<span class="ng-farmley-sc-progress__pin-icon" aria-hidden="true"><?php echo 'gift' === $milestone['icon'] ? '🎁' : '🚚'; ?></span>
							<span class="ng-farmley-sc-progress__pin-label"><?php echo esc_html( $milestone['label'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_strip_manual_cart_coupons' ) ) {
	function nuttergood_farmley_strip_manual_cart_coupons( $cart ) {
		static $done = false;
		if ( $done ) {
			return;
		}

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		if ( ! $cart instanceof WC_Cart ) {
			return;
		}

		$applied = $cart->get_applied_coupons();
		if ( empty( $applied ) ) {
			return;
		}

		$done = true;
		foreach ( $applied as $code ) {
			$cart->remove_coupon( $code );
		}
	}
	add_action( 'woocommerce_before_calculate_totals', 'nuttergood_farmley_strip_manual_cart_coupons', 1 );
}

if ( ! function_exists( 'nuttergood_farmley_apply_milestone_cart_discount' ) ) {
	function nuttergood_farmley_apply_milestone_cart_discount( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		if ( ! $cart instanceof WC_Cart || $cart->is_empty() ) {
			return;
		}

		$active = nuttergood_farmley_cart_milestone_active_discount( nuttergood_farmley_cart_milestone_subtotal() );
		if ( ! $active || $active['percent'] <= 0 ) {
			return;
		}

		$subtotal = nuttergood_farmley_cart_milestone_subtotal();
		$discount = max( 0, $subtotal * ( (float) $active['percent'] / 100 ) );

		if ( $discount <= 0 ) {
			return;
		}

		$cart->add_fee( $active['label'], -1 * $discount, false );
	}
	add_action( 'woocommerce_cart_calculate_fees', 'nuttergood_farmley_apply_milestone_cart_discount', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestone_savings_html' ) ) {
	function nuttergood_farmley_cart_milestone_savings_html() {
		$breakdown = nuttergood_farmley_cart_total_savings_breakdown();
		if ( $breakdown['total'] <= 0 ) {
			return '';
		}

		return wc_price( -1 * $breakdown['total'] );
	}
}

if ( ! function_exists( 'nuttergood_farmley_hide_milestone_fee_cart_row' ) ) {
	function nuttergood_farmley_hide_milestone_fee_cart_row( $html, $fee ) {
		if ( ! is_object( $fee ) || (float) $fee->amount >= 0 ) {
			return $html;
		}
		foreach ( nuttergood_farmley_cart_milestones() as $milestone ) {
			if ( 'percent' === $milestone['type'] && (string) $fee->name === (string) $milestone['label'] ) {
				return '';
			}
		}
		return $html;
	}
	add_filter( 'woocommerce_cart_totals_fee_html', 'nuttergood_farmley_hide_milestone_fee_cart_row', 10, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestones_admin_menu' ) ) {
	function nuttergood_farmley_cart_milestones_admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Cart Milestones', 'nuttergood' ),
			__( 'Cart Milestones', 'nuttergood' ),
			'manage_woocommerce',
			'ng-farmley-cart-milestones',
			'nuttergood_farmley_cart_milestones_admin_page'
		);
	}
	add_action( 'admin_menu', 'nuttergood_farmley_cart_milestones_admin_menu', 59 );
}

if ( ! function_exists( 'nuttergood_farmley_cart_milestones_admin_page' ) ) {
	function nuttergood_farmley_cart_milestones_admin_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( isset( $_POST['ng_farmley_milestones_save'] ) && check_admin_referer( 'ng_farmley_milestones_save' ) ) {
			$raw = isset( $_POST['milestone_lines'] ) ? sanitize_textarea_field( wp_unslash( $_POST['milestone_lines'] ) ) : '';
			update_option( NG_FARMLEY_CART_MILESTONES_OPTION, $raw, false );
			echo '<div class="updated"><p>' . esc_html__( 'Cart milestones saved.', 'nuttergood' ) . '</p></div>';
		}

		if ( isset( $_POST['ng_farmley_milestones_reset'] ) && check_admin_referer( 'ng_farmley_milestones_save' ) ) {
			delete_option( NG_FARMLEY_CART_MILESTONES_OPTION );
			echo '<div class="updated"><p>' . esc_html__( 'Reset to default milestones.', 'nuttergood' ) . '</p></div>';
		}

		$stored = get_option( NG_FARMLEY_CART_MILESTONES_OPTION, false );
		$raw    = ( false !== $stored && '' !== trim( (string) $stored ) )
			? (string) $stored
			: nuttergood_farmley_cart_milestones_default_raw();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Cart Milestones', 'nuttergood' ); ?></h1>
			<p><?php esc_html_e( 'Rewards shown on cart and side cart. Only the highest unlocked discount applies automatically.', 'nuttergood' ); ?></p>

			<form method="post">
				<?php wp_nonce_field( 'ng_farmley_milestones_save' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="milestone_lines"><?php esc_html_e( 'Milestones', 'nuttergood' ); ?></label></th>
						<td>
							<textarea name="milestone_lines" id="milestone_lines" rows="10" class="large-text code"><?php echo esc_textarea( $raw ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'One per line:', 'nuttergood' ); ?>
								<code>1000 | 10 | 10% OFF</code><br>
								<?php esc_html_e( 'Use "free" for free delivery (optional — Free Delivery menu controls the amount). Use a number for % off.', 'nuttergood' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<button type="submit" name="ng_farmley_milestones_save" class="button button-primary"><?php esc_html_e( 'Save changes', 'nuttergood' ); ?></button>
					<button type="submit" name="ng_farmley_milestones_reset" class="button" onclick="return confirm('<?php echo esc_js( __( 'Reset to default milestones?', 'nuttergood' ) ); ?>');"><?php esc_html_e( 'Reset to defaults', 'nuttergood' ); ?></button>
				</p>
			</form>
		</div>
		<?php
	}
}
