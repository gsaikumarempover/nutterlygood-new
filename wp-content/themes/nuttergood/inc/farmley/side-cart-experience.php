<?php
/**
 * Side cart drawer — progress bar, recommendations, burst animation.
 * Scoped to the cart widget only; does not alter global header/footer layout.
 *
 * @package NutterlyGood
 */

if ( ! function_exists( 'nuttergood_farmley_side_cart_milestones' ) ) {
	/**
	 * @return array<int, array{amount:int,label:string,icon:string}>
	 */
	function nuttergood_farmley_side_cart_milestones() {
		return apply_filters(
			'nuttergood_farmley_side_cart_milestones',
			array(
				array(
					'amount' => 899,
					'label'  => __( 'Extra 8% OFF', 'nuttergood' ),
					'icon'   => 'truck',
				),
				array(
					'amount' => 2500,
					'label'  => __( 'Free delivery', 'nuttergood' ),
					'icon'   => 'gift',
				),
			)
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_subtotal' ) ) {
	function nuttergood_farmley_side_cart_subtotal() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return 0.0;
		}

		$cart    = WC()->cart;
		$subtotal = (float) $cart->get_cart_contents_total();

		if ( $cart->display_prices_including_tax() ) {
			$subtotal += (float) $cart->get_cart_contents_tax();
		}

		return max( 0.0, $subtotal );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_code' ) ) {
	function nuttergood_farmley_side_cart_coupon_code() {
		$featured = nuttergood_farmley_side_cart_featured_coupon();
		return $featured ? $featured->get_code() : apply_filters( 'nuttergood_farmley_side_cart_coupon_code', 'SAVER8' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_get_wc_coupons' ) ) {
	/**
	 * @return WC_Coupon[]
	 */
	function nuttergood_farmley_side_cart_get_wc_coupons() {
		if ( ! function_exists( 'WC' ) || ! class_exists( 'WC_Coupon' ) ) {
			return array();
		}

		$posts = get_posts(
			array(
				'post_type'      => 'shop_coupon',
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		$coupons = array();
		foreach ( $posts as $post ) {
			$coupon = new WC_Coupon( $post->ID );
			if ( $coupon->get_id() ) {
				$coupons[] = $coupon;
			}
		}

		return apply_filters( 'nuttergood_farmley_side_cart_wc_coupons', $coupons );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_email_allowed' ) ) {
	function nuttergood_farmley_side_cart_coupon_email_allowed( WC_Coupon $coupon ) {
		$restrictions = $coupon->get_email_restrictions();
		if ( empty( $restrictions ) ) {
			return true;
		}

		$email = '';
		if ( is_user_logged_in() ) {
			$user  = wp_get_current_user();
			$email = $user->user_email;
		}

		if ( ! $email || ! is_email( $email ) ) {
			return false;
		}

		$email = strtolower( $email );
		foreach ( $restrictions as $allowed ) {
			if ( strtolower( $allowed ) === $email ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_is_eligible' ) ) {
	function nuttergood_farmley_side_cart_coupon_is_eligible( WC_Coupon $coupon ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return false;
		}

		if ( nuttergood_farmley_side_cart_coupon_is_applied( $coupon->get_code() ) ) {
			return false;
		}

		if ( ! nuttergood_farmley_side_cart_coupon_email_allowed( $coupon ) ) {
			return false;
		}

		$expires = $coupon->get_date_expires();
		if ( $expires && $expires->getTimestamp() < time() ) {
			return false;
		}

		$usage_limit = $coupon->get_usage_limit();
		if ( $usage_limit > 0 && $coupon->get_usage_count() >= $usage_limit ) {
			return false;
		}

		$minimum = (float) $coupon->get_minimum_amount();
		if ( $minimum > 0 && nuttergood_farmley_side_cart_subtotal() < $minimum ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_featured_coupon' ) ) {
	/**
	 * @return WC_Coupon|null
	 */
	function nuttergood_farmley_side_cart_featured_coupon() {
		$coupons = nuttergood_farmley_side_cart_get_wc_coupons();
		if ( empty( $coupons ) ) {
			return null;
		}

		$applied_codes = nuttergood_farmley_side_cart_applied_coupon_codes();
		if ( ! empty( $applied_codes ) ) {
			foreach ( $coupons as $coupon ) {
				foreach ( $applied_codes as $applied_code ) {
					if ( strcasecmp( $coupon->get_code(), $applied_code ) === 0 ) {
						return $coupon;
					}
				}
			}
		}

		if ( is_user_logged_in() && function_exists( 'nuttergood_farmley_newsletter_coupon_for_email' ) ) {
			$newsletter_code = nuttergood_farmley_newsletter_coupon_for_email( wp_get_current_user()->user_email );
			if ( $newsletter_code ) {
				foreach ( $coupons as $coupon ) {
					if ( strcasecmp( $coupon->get_code(), $newsletter_code ) === 0 && nuttergood_farmley_side_cart_coupon_is_eligible( $coupon ) ) {
						return $coupon;
					}
				}
			}
		}

		$visible = nuttergood_farmley_side_cart_visible_coupons();
		foreach ( $visible as $coupon ) {
			if ( nuttergood_farmley_side_cart_coupon_is_eligible( $coupon ) ) {
				return $coupon;
			}
		}

		foreach ( $visible as $coupon ) {
			if ( ! nuttergood_farmley_side_cart_coupon_is_applied( $coupon->get_code() ) ) {
				return $coupon;
			}
		}

		return ! empty( $visible[0] ) ? $visible[0] : null;
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_is_applied' ) ) {
	function nuttergood_farmley_side_cart_coupon_is_applied( $code = null ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return false;
		}

		$applied = WC()->cart->get_applied_coupons();
		if ( empty( $applied ) ) {
			return false;
		}

		if ( null === $code || '' === $code ) {
			return true;
		}

		$code = strtolower( wc_format_coupon_code( $code ) );
		foreach ( $applied as $applied_code ) {
			if ( strtolower( $applied_code ) === $code ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_progress_data' ) ) {
	/**
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_side_cart_progress_data() {
		$milestones = nuttergood_farmley_side_cart_milestones();
		$defaults   = array(
			'milestones'   => $milestones,
			'subtotal'     => 0.0,
			'max'          => ! empty( $milestones ) ? (int) end( $milestones )['amount'] : 2500,
			'percent'      => 0,
			'next'         => ! empty( $milestones ) ? $milestones[0] : null,
			'all_unlocked' => false,
			'item_count'   => 0,
		);

		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return $defaults;
		}

		$subtotal = nuttergood_farmley_side_cart_subtotal();
		$max        = ! empty( $milestones ) ? (int) end( $milestones )['amount'] : 2500;
		$max        = max( 1, $max );
		$percent    = min( 100, ( $subtotal / $max ) * 100 );

		$next = null;
		foreach ( $milestones as $milestone ) {
			if ( $subtotal < (float) $milestone['amount'] ) {
				$next = $milestone;
				break;
			}
		}

		$all_unlocked = null === $next;
		$item_count   = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

		if ( $all_unlocked ) {
			$percent = 100;
		}

		return compact( 'milestones', 'subtotal', 'max', 'percent', 'next', 'all_unlocked', 'item_count' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_side_cart_progress' ) ) {
	function nuttergood_farmley_render_side_cart_progress() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return;
		}

		$data = nuttergood_farmley_side_cart_progress_data();
		?>
		<div class="ng-farmley-sc-progress" data-ng-sc-progress data-percent="<?php echo esc_attr( (string) round( $data['percent'] ) ); ?>">
			<?php if ( $data['all_unlocked'] ) : ?>
				<p class="ng-farmley-sc-progress__msg ng-farmley-sc-progress__msg--done">
					<span aria-hidden="true">🎉</span>
					<?php esc_html_e( 'Congratulations! You unlocked all cart rewards.', 'nuttergood' ); ?>
				</p>
			<?php else : ?>
				<p class="ng-farmley-sc-progress__msg">
					<span aria-hidden="true">🚚</span>
					<?php
					if ( 2500 === (int) $data['next']['amount'] ) {
						printf(
							/* translators: 1: minimum order amount, 2: price remaining */
							esc_html__( 'Free delivery for orders above %1$s — add %2$s more', 'nuttergood' ),
							wp_kses_post( wc_price( 2500 ) ),
							wp_kses_post( wc_price( max( 0, (float) $data['next']['amount'] - $data['subtotal'] ) ) )
						);
					} else {
						printf(
							/* translators: 1: price remaining, 2: reward label */
							esc_html__( 'Add items worth %1$s more to unlock %2$s', 'nuttergood' ),
							wp_kses_post( wc_price( max( 0, (float) $data['next']['amount'] - $data['subtotal'] ) ) ),
							'<strong>' . esc_html( $data['next']['label'] ) . '</strong>'
						);
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
							title="<?php echo esc_attr( wc_price( $milestone['amount'] ) . ' — ' . $milestone['label'] ); ?>"
						>
							<span class="ng-farmley-sc-progress__pin-icon" aria-hidden="true"><?php echo 'gift' === $milestone['icon'] ? '🎁' : '🚚'; ?></span>
							<span class="ng-farmley-sc-progress__pin-label"><?php echo esc_html( $milestone['label'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<p class="ng-farmley-sc-progress__count">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: item count */
						_n( '%d item in cart', '%d items in cart', $data['item_count'], 'nuttergood' ),
						$data['item_count']
					)
				);
				?>
			</p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_side_cart_recommended_ids' ) ) {
	/**
	 * @param int $limit Max products.
	 * @return array<int>
	 */
	function nuttergood_farmley_get_side_cart_recommended_ids( $limit = 4 ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return array();
		}

		$in_cart = array();
		foreach ( WC()->cart->get_cart() as $item ) {
			$in_cart[] = (int) $item['product_id'];
		}

		$related = array();
		foreach ( array_unique( $in_cart ) as $pid ) {
			$related = array_merge( $related, wc_get_related_products( $pid, 4 ) );
		}

		$related = array_values(
			array_filter(
				array_unique( array_map( 'intval', $related ) ),
				static function ( $id ) use ( $in_cart ) {
					$p = wc_get_product( $id );
					return $p && $p->is_purchasable() && $p->is_in_stock() && ! in_array( $id, $in_cart, true );
				}
			)
		);

		if ( count( $related ) < $limit ) {
			$exclude = array_merge( $in_cart, $related );
			$fallback = wc_get_products(
				array(
					'limit'    => $limit * 2,
					'status'   => 'publish',
					'orderby'  => 'popularity',
					'order'    => 'DESC',
					'exclude'  => $exclude,
					'return'   => 'ids',
				)
			);
			foreach ( $fallback as $fid ) {
				$fid = (int) $fid;
				if ( $fid && ! in_array( $fid, $related, true ) && ! in_array( $fid, $in_cart, true ) ) {
					$related[] = $fid;
				}
				if ( count( $related ) >= $limit ) {
					break;
				}
			}
		}

		return array_slice( $related, 0, $limit );
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_side_cart_recommendations' ) ) {
	function nuttergood_farmley_render_side_cart_recommendations() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return;
		}

		$ids = nuttergood_farmley_get_side_cart_recommended_ids( 6 );
		if ( empty( $ids ) ) {
			return;
		}
		?>
		<section class="ng-farmley-sc-reco" data-ng-sc-reco aria-label="<?php esc_attr_e( 'Recommended products', 'nuttergood' ); ?>">
			<div class="ng-farmley-sc-reco__head">
				<h3 class="ng-farmley-sc-reco__title"><?php esc_html_e( 'Recommended products for you', 'nuttergood' ); ?></h3>
				<div class="ng-farmley-sc-reco__nav" aria-hidden="false">
					<button type="button" class="ng-farmley-sc-reco__btn ng-farmley-sc-reco__prev" aria-label="<?php esc_attr_e( 'Previous products', 'nuttergood' ); ?>">
						<span aria-hidden="true">&#8249;</span>
					</button>
					<button type="button" class="ng-farmley-sc-reco__btn ng-farmley-sc-reco__next" aria-label="<?php esc_attr_e( 'Next products', 'nuttergood' ); ?>">
						<span aria-hidden="true">&#8250;</span>
					</button>
				</div>
			</div>
			<div class="ng-farmley-sc-reco__viewport">
				<div class="ng-farmley-sc-reco__row">
				<?php foreach ( $ids as $product_id ) :
					$product = wc_get_product( $product_id );
					if ( ! $product ) {
						continue;
					}
					?>
					<article class="ng-farmley-sc-reco__item">
						<a class="ng-farmley-sc-reco__img" href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
						</a>
						<div class="ng-farmley-sc-reco__body">
							<h4 class="ng-farmley-sc-reco__name">
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
							</h4>
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
		</section>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_trash_icon' ) ) {
	function nuttergood_farmley_side_cart_trash_icon() {
		return '<svg class="ng-farmley-sc-trash-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_badge_icon' ) ) {
	function nuttergood_farmley_side_cart_coupon_badge_icon() {
		return '<svg class="ng-farmley-sc-coupon__badge" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="#e8f3ee" stroke="#0c533d" stroke-width="1.25"/><path d="M8.5 9.5c0-1 1-1.5 2-1.5s2 .5 2 1.5-1 1.5-2 2.5-2 1.5-2 2.5 1 1.5 2 1.5 2-.5 2-1.5" stroke="#0c533d" stroke-width="1.5" stroke-linecap="round"/></svg>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_item_subtitle' ) ) {
	/**
	 * @param array<string, mixed> $cart_item
	 */
	function nuttergood_farmley_side_cart_item_subtitle( $cart_item, $product ) {
		$parts = array();

		if ( $product->is_type( 'variation' ) ) {
			$variation = wc_get_formatted_variation( $product, true, false );
			if ( $variation ) {
				$parts[] = wp_strip_all_tags( $variation );
			}
		}

		$item_data = wc_get_formatted_cart_item_data( $cart_item, false );
		if ( $item_data ) {
			$parts[] = wp_strip_all_tags( $item_data );
		}

		$weight = $product->get_weight();
		if ( $weight ) {
			$parts[] = wc_format_weight( (float) $weight );
		}

		$parts = array_filter( array_unique( array_map( 'trim', $parts ) ) );

		return implode( ' · ', $parts );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_item_unit_prices' ) ) {
	/**
	 * @param array<string, mixed> $cart_item
	 * @return array{current:string,regular:string,on_sale:bool}
	 */
	function nuttergood_farmley_side_cart_item_unit_prices( $cart_item, $product, $cart_item_key ) {
		$current_html = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $product ), $cart_item, $cart_item_key );
		$on_sale      = $product->is_on_sale();
		$regular_html = '';

		if ( $on_sale ) {
			$regular_price = (float) $product->get_regular_price();
			if ( $product->is_type( 'variation' ) ) {
				$regular_price = (float) $product->get_regular_price();
			}
			if ( $regular_price > 0 ) {
				$regular_html = wc_price( $regular_price );
			}
		}

		return array(
			'current'  => $current_html,
			'regular'  => $regular_html,
			'on_sale'  => $on_sale && '' !== $regular_html,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_side_cart_loop_item' ) ) {
	/**
	 * @param array<string, mixed> $cart_item
	 */
	function nuttergood_farmley_render_side_cart_loop_item( $cart_item_key, $cart_item ) {
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

		if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
			return;
		}

		$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
		$subtitle          = nuttergood_farmley_side_cart_item_subtitle( $cart_item, $_product );
		$prices            = nuttergood_farmley_side_cart_item_unit_prices( $cart_item, $_product, $cart_item_key );
		$qty               = (int) $cart_item['quantity'];
		$max_qty           = $_product->get_max_purchase_quantity();
		$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
		?>
		<li class="qodef-woo-side-area-cart-item qodef-e ng-farmley-sc-item" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
			<div class="qodef-e-image ng-farmley-sc-item__image">
				<?php
				if ( ! $product_permalink ) {
					echo qode_framework_wp_kses_html( 'img', $thumbnail );
				} else {
					printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), qode_framework_wp_kses_html( 'img', $thumbnail ) );
				}
				?>
			</div>
			<div class="qodef-e-content ng-farmley-sc-item__body">
				<div class="ng-farmley-sc-item__top">
					<div class="ng-farmley-sc-item__meta">
						<h5 itemprop="name" class="qodef-e-title entry-title ng-farmley-sc-item__title">
							<?php
							if ( ! $product_permalink ) {
								echo qode_framework_wp_kses_html( 'content', $product_name );
							} else {
								echo qode_framework_wp_kses_html( 'content', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $product_name ) );
							}
							?>
						</h5>
						<?php if ( $subtitle ) : ?>
							<p class="ng-farmley-sc-item__subtitle"><?php echo esc_html( $subtitle ); ?></p>
						<?php endif; ?>
					</div>
					<div class="ng-farmley-sc-item__prices">
						<p class="ng-farmley-sc-item__price"><?php echo wp_kses_post( $prices['current'] ); ?></p>
						<?php if ( $prices['on_sale'] ) : ?>
							<p class="ng-farmley-sc-item__price ng-farmley-sc-item__price--regular"><?php echo wp_kses_post( $prices['regular'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="ng-farmley-sc-item__actions">
					<?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<button type="button" class="qodef-e-remove remove remove_from_cart_button ng-farmley-sc-item__remove" aria-label="%1$s" data-product_id="%2$s" data-cart_item_key="%3$s" data-product_sku="%4$s">%5$s</button>',
							esc_attr__( 'Remove this item', 'nuttergood' ),
							esc_attr( (string) $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() ),
							nuttergood_farmley_side_cart_trash_icon()
						),
						$cart_item_key
					);
					?>
					<div class="ng-farmley-sc-qty" data-ng-sc-qty data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>" data-max="<?php echo esc_attr( $max_qty > 0 ? (string) $max_qty : '' ); ?>">
						<button type="button" class="ng-farmley-sc-qty__btn" data-action="minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'nuttergood' ); ?>">−</button>
						<span class="ng-farmley-sc-qty__val" aria-live="polite"><?php echo esc_html( (string) $qty ); ?></span>
						<button type="button" class="ng-farmley-sc-qty__btn" data-action="plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'nuttergood' ); ?>">+</button>
					</div>
				</div>
			</div>
		</li>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_side_cart_loop' ) ) {
	function nuttergood_farmley_render_side_cart_loop() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return;
		}
		?>
		<ul class="qodef-woo-side-area-cart ng-farmley-sc-items">
			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					continue;
				}
				nuttergood_farmley_render_side_cart_loop_item( $cart_item_key, $cart_item );
			}
			?>
		</ul>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_side_cart_content' ) ) {
	function nuttergood_farmley_render_side_cart_content() {
		if ( ! is_object( WC()->cart ) ) {
			return;
		}
		?>
		<div class="qodef-widget-side-area-cart-content">
			<div class="qodef-side-area-cart-top">
				<div class="qodef-side-area-cart-logo">
					<?php
					greenpath_core_get_header_logo_image( array( 'only_main' => true ) );
					greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/close' );
					?>
				</div>
				<h6 class="qodef-side-area-cart-heading">
					<?php esc_html_e( 'My Shopping Cart', 'greenpath-core' ); ?>
				</h6>
			</div>
			<?php do_action( 'greenpath_core_action_woocommerce_before_side_area_cart_content' ); ?>
			<?php
			if ( ! WC()->cart->is_empty() ) {
				?>
				<div class="ng-farmley-sc-cart-slot">
					<?php nuttergood_farmley_render_side_cart_loop(); ?>
				</div>
				<div class="ng-farmley-sc-footer">
					<div class="ng-farmley-sc-coupon-slot">
						<?php nuttergood_farmley_render_side_cart_coupon(); ?>
					</div>
					<div class="ng-farmley-sc-reco-slot">
						<?php nuttergood_farmley_render_side_cart_recommendations(); ?>
					</div>
					<div class="ng-farmley-sc-order-slot">
						<?php nuttergood_farmley_render_side_cart_order_details(); ?>
					</div>
				</div>
				<?php
				greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/button' );
			} else {
				?>
				<div class="ng-farmley-sc-cart-slot">
					<?php greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/posts-not-found' ); ?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_savings_amount' ) ) {
	function nuttergood_farmley_side_cart_coupon_savings_amount( $coupon = null ) {
		$subtotal = nuttergood_farmley_side_cart_subtotal();

		if ( $coupon instanceof WC_Coupon ) {
			$wc_coupon = $coupon;
		} else {
			$wc_coupon = new WC_Coupon( nuttergood_farmley_side_cart_coupon_code() );
		}

		if ( $wc_coupon->get_id() ) {
			if ( $wc_coupon->is_type( 'percent' ) ) {
				return max( 0, $subtotal * ( (float) $wc_coupon->get_amount() / 100 ) );
			}
			if ( $wc_coupon->is_type( 'fixed_cart' ) ) {
				return min( (float) $wc_coupon->get_amount(), $subtotal );
			}
		}

		return max( 0, $subtotal * 0.08 );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_visible_coupons' ) ) {
	/**
	 * Coupons the current shopper may see in the side cart (email + expiry).
	 *
	 * @return WC_Coupon[]
	 */
	function nuttergood_farmley_side_cart_visible_coupons() {
		$coupons = nuttergood_farmley_side_cart_get_wc_coupons();
		$visible = array();

		foreach ( $coupons as $coupon ) {
			if ( ! nuttergood_farmley_side_cart_coupon_email_allowed( $coupon ) ) {
				continue;
			}

			$expires = $coupon->get_date_expires();
			if ( $expires && $expires->getTimestamp() < time() ) {
				continue;
			}

			$usage_limit = $coupon->get_usage_limit();
			if ( $usage_limit > 0 && $coupon->get_usage_count() >= $usage_limit ) {
				continue;
			}

			$visible[] = $coupon;
		}

		return apply_filters( 'nuttergood_farmley_side_cart_visible_coupons', $visible );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_minimum_remaining' ) ) {
	function nuttergood_farmley_side_cart_coupon_minimum_remaining( WC_Coupon $coupon ) {
		$minimum = (float) $coupon->get_minimum_amount();
		if ( $minimum <= 0 ) {
			return 0.0;
		}

		return max( 0.0, $minimum - nuttergood_farmley_side_cart_subtotal() );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_applied_coupon_codes' ) ) {
	/**
	 * @return string[]
	 */
	function nuttergood_farmley_side_cart_applied_coupon_codes() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return array();
		}

		return array_map( 'strval', WC()->cart->get_applied_coupons() );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_estimated_total_html' ) ) {
	function nuttergood_farmley_side_cart_estimated_total_html() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return '';
		}

		WC()->cart->calculate_totals();

		return WC()->cart->get_total();
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_discount_total_html' ) ) {
	function nuttergood_farmley_side_cart_discount_total_html() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return '';
		}

		$discount = (float) WC()->cart->get_discount_total();
		if ( WC()->cart->display_prices_including_tax() ) {
			$discount += (float) WC()->cart->get_discount_tax();
		}

		if ( $discount <= 0 ) {
			return '';
		}

		return wc_price( $discount * -1 );
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_side_cart_order_details' ) ) {
	function nuttergood_farmley_render_side_cart_order_details() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return;
		}

		$discount_html = nuttergood_farmley_side_cart_discount_total_html();
		?>
		<div class="qodef-m-order-details ng-farmley-sc-order-details">
			<?php if ( $discount_html ) : ?>
				<div class="ng-farmley-sc-order-details__discount">
					<span class="ng-farmley-sc-order-details__discount-label"><?php esc_html_e( 'Coupon savings', 'nuttergood' ); ?></span>
					<span class="ng-farmley-sc-order-details__discount-amount"><?php echo wp_kses_post( $discount_html ); ?></span>
				</div>
			<?php endif; ?>
			<div class="ng-farmley-sc-order-details__total">
				<h6 class="qodef-m-order-label"><?php esc_html_e( 'Total:', 'greenpath-core' ); ?></h6>
				<div class="qodef-m-order-amount"><?php echo wp_kses_post( nuttergood_farmley_side_cart_estimated_total_html() ); ?></div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_label' ) ) {
	function nuttergood_farmley_side_cart_coupon_label( WC_Coupon $coupon ) {
		if ( $coupon->is_type( 'percent' ) ) {
			return sprintf(
				/* translators: %s: percent amount */
				__( '%s%% OFF', 'nuttergood' ),
				wc_format_decimal( $coupon->get_amount(), 0 )
			);
		}

		if ( $coupon->is_type( 'fixed_cart' ) || $coupon->is_type( 'fixed_product' ) ) {
			return sprintf(
				/* translators: %s: amount */
				__( '%s OFF', 'nuttergood' ),
				wp_strip_all_tags( wc_price( $coupon->get_amount() ) )
			);
		}

		return $coupon->get_code();
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_side_cart_coupon_row' ) ) {
	/**
	 * @param WC_Coupon $coupon
	 * @param bool      $is_featured
	 */
	function nuttergood_farmley_render_side_cart_coupon_row( $coupon, $is_featured = false ) {
		$code     = $coupon->get_code();
		$applied  = nuttergood_farmley_side_cart_coupon_is_applied( $code );
		$eligible = nuttergood_farmley_side_cart_coupon_is_eligible( $coupon );
		$savings  = nuttergood_farmley_side_cart_coupon_savings_amount( $coupon );
		$label    = nuttergood_farmley_side_cart_coupon_label( $coupon );
		$row_class = 'ng-farmley-sc-coupon__row';
		if ( $is_featured ) {
			$row_class .= ' ng-farmley-sc-coupon__row--primary';
		} else {
			$row_class .= ' ng-farmley-sc-coupon__row--list';
		}
		?>
		<div class="<?php echo esc_attr( $row_class ); ?>" data-coupon-code="<?php echo esc_attr( $code ); ?>">
			<span class="ng-farmley-sc-coupon__icon<?php echo $is_featured ? '' : ' ng-farmley-sc-coupon__icon--sm'; ?>"><?php echo nuttergood_farmley_side_cart_coupon_badge_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<div class="ng-farmley-sc-coupon__copy">
				<?php if ( $applied ) : ?>
					<p class="ng-farmley-sc-coupon__save"><?php echo esc_html( $label ); ?> — <?php esc_html_e( 'Applied', 'nuttergood' ); ?></p>
					<p class="ng-farmley-sc-coupon__code-line">
						<?php
						printf(
							/* translators: %s: coupon code */
							esc_html__( "with '%s'", 'nuttergood' ),
							esc_html( $code )
						);
						?>
					</p>
				<?php else : ?>
					<p class="ng-farmley-sc-coupon__save">
						<?php
						printf(
							/* translators: 1: discount label, 2: savings amount */
							esc_html__( '%1$s — Save %2$s', 'nuttergood' ),
							esc_html( $label ),
							wp_kses_post( wc_price( $savings ) )
						);
						?>
					</p>
					<p class="ng-farmley-sc-coupon__code-line">
						<?php
						printf(
							/* translators: %s: coupon code */
							esc_html__( "with '%s'", 'nuttergood' ),
							esc_html( $code )
						);
						?>
					</p>
					<?php if ( ! $eligible && nuttergood_farmley_side_cart_coupon_minimum_remaining( $coupon ) > 0 ) : ?>
						<p class="ng-farmley-sc-coupon__row-hint">
							<?php
							printf(
								/* translators: %s: amount remaining */
								esc_html__( 'Add %s more to unlock', 'nuttergood' ),
								wp_kses_post( wc_price( nuttergood_farmley_side_cart_coupon_minimum_remaining( $coupon ) ) )
							);
							?>
						</p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<?php if ( $applied ) : ?>
				<span class="ng-farmley-sc-coupon__applied-badge"><?php esc_html_e( 'Applied', 'nuttergood' ); ?></span>
			<?php else : ?>
				<button
					type="button"
					class="ng-farmley-sc-coupon__apply"
					data-ng-sc-apply-coupon
					data-coupon-code="<?php echo esc_attr( $code ); ?>"
					<?php disabled( ! $eligible ); ?>
					aria-label="<?php echo esc_attr( sprintf( __( 'Apply coupon %s', 'nuttergood' ), $code ) ); ?>">
					<?php esc_html_e( 'Apply', 'nuttergood' ); ?>
				</button>
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_side_cart_coupon' ) ) {
	function nuttergood_farmley_render_side_cart_coupon() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() || ! wc_coupons_enabled() ) {
			return;
		}

		$visible  = nuttergood_farmley_side_cart_visible_coupons();
		if ( empty( $visible ) ) {
			return;
		}

		$featured     = nuttergood_farmley_side_cart_featured_coupon();
		$featured_code = $featured ? $featured->get_code() : '';
		$applied_codes = nuttergood_farmley_side_cart_applied_coupon_codes();
		$has_applied   = ! empty( $applied_codes );
		$other_count   = 0;

		foreach ( $visible as $coupon ) {
			if ( $featured_code && strcasecmp( $coupon->get_code(), $featured_code ) === 0 ) {
				continue;
			}
			++$other_count;
		}
		?>
		<div class="ng-farmley-sc-coupon<?php echo $has_applied ? ' ng-farmley-sc-coupon--applied' : ''; ?>" data-ng-sc-coupon>
			<div class="ng-farmley-sc-coupon__card">
				<?php
				if ( $featured ) {
					nuttergood_farmley_render_side_cart_coupon_row( $featured, true );
				} elseif ( ! empty( $visible[0] ) ) {
					nuttergood_farmley_render_side_cart_coupon_row( $visible[0], true );
					$featured_code = $visible[0]->get_code();
					$other_count   = max( 0, count( $visible ) - 1 );
				}
				?>
				<?php if ( $other_count > 0 ) : ?>
					<div class="ng-farmley-sc-coupon__row ng-farmley-sc-coupon__row--more">
						<span class="ng-farmley-sc-coupon__icon ng-farmley-sc-coupon__icon--sm"><?php echo nuttergood_farmley_side_cart_coupon_badge_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<p class="ng-farmley-sc-coupon__more-text">
							<?php
							printf(
								/* translators: %d: number of additional coupons */
								esc_html( _n( '+%d more offer', '+%d more offers', $other_count, 'nuttergood' ) ),
								(int) $other_count
							);
							?>
						</p>
						<button type="button" class="ng-farmley-sc-coupon__view-all" data-ng-sc-toggle-coupons aria-expanded="false">
							<?php esc_html_e( 'View all coupons', 'nuttergood' ); ?>
							<span class="ng-farmley-sc-coupon__view-all-icon" aria-hidden="true">›</span>
						</button>
					</div>
					<div class="ng-farmley-sc-coupon__all" data-ng-sc-coupons-panel hidden>
						<?php
						foreach ( $visible as $coupon ) {
							if ( $featured_code && strcasecmp( $coupon->get_code(), $featured_code ) === 0 ) {
								continue;
							}
							nuttergood_farmley_render_side_cart_coupon_row( $coupon, false );
						}
						?>
					</div>
				<?php endif; ?>
			</div>
			<p class="ng-farmley-sc-coupon__feedback" data-ng-sc-coupon-feedback role="status" aria-live="polite"></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_side_cart_coupon_html' ) ) {
	function nuttergood_farmley_get_side_cart_coupon_html() {
		ob_start();
		nuttergood_farmley_render_side_cart_coupon();
		return ob_get_clean();
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_side_cart_reco_html' ) ) {
	function nuttergood_farmley_get_side_cart_reco_html() {
		ob_start();
		nuttergood_farmley_render_side_cart_recommendations();
		return ob_get_clean();
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_hooks' ) ) {
	function nuttergood_farmley_side_cart_hooks() {
		add_action( 'greenpath_core_action_woocommerce_before_side_area_cart_content', 'nuttergood_farmley_render_side_cart_progress', 12 );
	}
	add_action( 'init', 'nuttergood_farmley_side_cart_hooks', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_heading_text' ) ) {
	function nuttergood_farmley_side_cart_heading_text() {
		$count = function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
		$label = __( 'Your cart', 'nuttergood' );

		return $label . ' (' . $count . ')';
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_inner_html' ) ) {
	/**
	 * Full side-cart inner markup (opener + drawer content).
	 */
	function nuttergood_farmley_side_cart_inner_html() {
		ob_start();
		?>
		<div class="qodef-widget-side-area-cart-inner">
			<?php greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/opener' ); ?>
			<?php nuttergood_farmley_render_side_cart_content(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_build_fragments' ) ) {
	/**
	 * Cart fragments: full inner (legacy-compatible) plus granular slots when present.
	 *
	 * @param array<string, string> $fragments Existing fragments.
	 * @return array<string, string>
	 */
	function nuttergood_farmley_side_cart_build_fragments( $fragments = array() ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return $fragments;
		}

		$scope      = '.widget_greenpath_core_woo_side_area_cart';
		$inner_html = nuttergood_farmley_side_cart_inner_html();

		// Primary — matches GreenPath selector so WooCommerce + legacy DOM always update.
		$fragments['.qodef-widget-side-area-cart-inner'] = $inner_html;

		ob_start();
		greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/opener' );
		$fragments[ $scope . ' .qodef-m-opener' ] = ob_get_clean();

		$fragments[ $scope . ' .qodef-side-area-cart-heading' ] = sprintf(
			'<h6 class="qodef-side-area-cart-heading">%s</h6>',
			esc_html( nuttergood_farmley_side_cart_heading_text() )
		);

		if ( WC()->cart->is_empty() ) {
			ob_start();
			greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/posts-not-found' );
			$not_found_html = ob_get_clean();

			$fragments[ $scope . ' .ng-farmley-sc-cart-slot' ]                          = '<div class="ng-farmley-sc-cart-slot">' . $not_found_html . '</div>';
			$fragments[ $scope . ' .qodef-m-posts-not-found' ]                          = $not_found_html;
			$fragments[ $scope . ' .ng-farmley-sc-footer .ng-farmley-sc-coupon-slot' ]  = '<div class="ng-farmley-sc-coupon-slot"></div>';
			$fragments[ $scope . ' .ng-farmley-sc-footer .ng-farmley-sc-reco-slot' ]    = '<div class="ng-farmley-sc-reco-slot"></div>';
			$fragments[ $scope . ' .ng-farmley-sc-footer .ng-farmley-sc-order-slot' ]   = '<div class="ng-farmley-sc-order-slot"></div>';
			$fragments[ $scope . ' .qodef-m-action' ]                                    = '';
			$fragments[ $scope . ' .ng-farmley-sc-progress' ]                           = '';
			$fragments[ $scope . ' .qodef-woo-side-area-cart' ]                         = '';
		} else {
			ob_start();
			nuttergood_farmley_render_side_cart_loop();
			$loop_html = ob_get_clean();

			$fragments[ $scope . ' .ng-farmley-sc-cart-slot' ] = '<div class="ng-farmley-sc-cart-slot">' . $loop_html . '</div>';
			$fragments[ $scope . ' .qodef-woo-side-area-cart' ] = $loop_html;

			ob_start();
			greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/button' );
			$fragments[ $scope . ' .qodef-m-action' ] = ob_get_clean();

			ob_start();
			nuttergood_farmley_render_side_cart_progress();
			$fragments[ $scope . ' .ng-farmley-sc-progress' ] = ob_get_clean();

			ob_start();
			nuttergood_farmley_render_side_cart_coupon();
			$fragments[ $scope . ' .ng-farmley-sc-footer .ng-farmley-sc-coupon-slot' ] = '<div class="ng-farmley-sc-coupon-slot">' . ob_get_clean() . '</div>';

			ob_start();
			nuttergood_farmley_render_side_cart_recommendations();
			$fragments[ $scope . ' .ng-farmley-sc-footer .ng-farmley-sc-reco-slot' ] = '<div class="ng-farmley-sc-reco-slot">' . ob_get_clean() . '</div>';

			ob_start();
			nuttergood_farmley_render_side_cart_order_details();
			$fragments[ $scope . ' .ng-farmley-sc-footer .ng-farmley-sc-order-slot' ] = '<div class="ng-farmley-sc-order-slot">' . ob_get_clean() . '</div>';
		}

		return $fragments;
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_add_to_cart_fragment' ) ) {
	function nuttergood_farmley_side_cart_add_to_cart_fragment( $fragments ) {
		return nuttergood_farmley_side_cart_build_fragments( $fragments );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_register_overrides' ) ) {
	function nuttergood_farmley_side_cart_register_overrides() {
		if ( ! class_exists( 'GreenPathCore_WooCommerce_Side_Area_Cart_Widget' ) ) {
			return;
		}

		remove_filter( 'woocommerce_add_to_cart_fragments', 'greenpath_core_woo_side_area_cart_add_to_cart_fragment' );
		add_filter( 'woocommerce_add_to_cart_fragments', 'nuttergood_farmley_side_cart_add_to_cart_fragment', 10 );

		if ( class_exists( 'Nuttergood_WooCommerce_Side_Area_Cart_Widget' ) ) {
			unregister_widget( 'GreenPathCore_WooCommerce_Side_Area_Cart_Widget' );
			register_widget( 'Nuttergood_WooCommerce_Side_Area_Cart_Widget' );
			return;
		}

		class Nuttergood_WooCommerce_Side_Area_Cart_Widget extends GreenPathCore_WooCommerce_Side_Area_Cart_Widget {
			public function render( $atts ) {
				echo nuttergood_farmley_side_cart_inner_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		unregister_widget( 'GreenPathCore_WooCommerce_Side_Area_Cart_Widget' );
		register_widget( 'Nuttergood_WooCommerce_Side_Area_Cart_Widget' );
	}
	add_action( 'widgets_init', 'nuttergood_farmley_side_cart_register_overrides', 100 );
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_ajax_update_qty' ) ) {
	function nuttergood_farmley_side_cart_ajax_update_qty() {
		check_ajax_referer( 'ng-farmley-sc', 'security' );

		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			wp_send_json_error( array( 'message' => __( 'Cart not available.', 'nuttergood' ) ) );
		}

		$cart_item_key = isset( $_POST['cart_item_key'] ) ? wc_clean( wp_unslash( $_POST['cart_item_key'] ) ) : '';
		$quantity      = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 0;

		if ( ! $cart_item_key || ! isset( WC()->cart->get_cart()[ $cart_item_key ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid cart item.', 'nuttergood' ) ) );
		}

		$cart_item = WC()->cart->get_cart()[ $cart_item_key ];
		$product   = $cart_item['data'];

		if ( $quantity <= 0 ) {
			WC()->cart->remove_cart_item( $cart_item_key );
		} else {
			$passed = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $cart_item, $quantity );

			if ( ! $passed ) {
				wp_send_json_error( array( 'message' => __( 'Could not update quantity.', 'nuttergood' ) ) );
			}

			$max_qty = $product->get_max_purchase_quantity();
			if ( $max_qty > 0 && $quantity > $max_qty ) {
				$quantity = $max_qty;
			}

			WC()->cart->set_quantity( $cart_item_key, $quantity, true );
		}

		WC()->cart->calculate_totals();

		$fragments = apply_filters( 'woocommerce_add_to_cart_fragments', array() );

		wp_send_json_success(
			array_merge(
				nuttergood_farmley_side_cart_meta_payload(),
				array(
					'fragments' => $fragments,
					'cart_hash' => WC()->cart->get_cart_hash(),
				)
			)
		);
	}
	add_action( 'wc_ajax_ng_farmley_side_cart_update_qty', 'nuttergood_farmley_side_cart_ajax_update_qty' );
	add_action( 'wc_ajax_nopriv_ng_farmley_side_cart_update_qty', 'nuttergood_farmley_side_cart_ajax_update_qty' );
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_meta_payload' ) ) {
	/**
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_side_cart_meta_payload() {
		$data = nuttergood_farmley_side_cart_progress_data();

		return array(
			'percent'    => round( $data['percent'] ),
			'itemCount'  => isset( $data['item_count'] ) ? (int) $data['item_count'] : 0,
			'couponHtml' => nuttergood_farmley_get_side_cart_coupon_html(),
			'recoHtml'   => nuttergood_farmley_get_side_cart_reco_html(),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_ajax_meta' ) ) {
	function nuttergood_farmley_side_cart_ajax_meta() {
		check_ajax_referer( 'ng-farmley-sc', 'security' );

		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			wp_send_json_error( array( 'message' => __( 'Cart is empty.', 'nuttergood' ) ) );
		}

		wp_send_json_success( nuttergood_farmley_side_cart_meta_payload() );
	}
	add_action( 'wc_ajax_ng_farmley_side_cart_meta', 'nuttergood_farmley_side_cart_ajax_meta' );
	add_action( 'wc_ajax_nopriv_ng_farmley_side_cart_meta', 'nuttergood_farmley_side_cart_ajax_meta' );
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_ajax_apply_coupon' ) ) {
	function nuttergood_farmley_side_cart_ajax_apply_coupon() {
		check_ajax_referer( 'ng-farmley-sc', 'security' );

		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			wp_send_json_error( array( 'message' => __( 'Cart is empty.', 'nuttergood' ) ) );
		}

		$code = isset( $_POST['coupon_code'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) ) : '';
		if ( ! $code ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a coupon code.', 'nuttergood' ) ) );
		}

		$coupon = function_exists( 'nuttergood_farmley_resolve_wc_coupon_by_code' )
			? nuttergood_farmley_resolve_wc_coupon_by_code( $code )
			: new WC_Coupon( wc_format_coupon_code( $code ) );

		if ( ! $coupon || ! $coupon->get_id() ) {
			wp_send_json_error( array( 'message' => __( 'Coupon does not exist.', 'nuttergood' ) ) );
		}

		$code = $coupon->get_code();

		if ( ! nuttergood_farmley_side_cart_coupon_email_allowed( $coupon ) ) {
			wp_send_json_error( array( 'message' => __( 'This coupon is not available for your account.', 'nuttergood' ) ) );
		}

		$remaining = nuttergood_farmley_side_cart_coupon_minimum_remaining( $coupon );
		if ( $remaining > 0 ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
						/* translators: %s: minimum amount */
						__( 'Add items worth %s more to use this coupon.', 'nuttergood' ),
						wp_strip_all_tags( wc_price( $remaining ) )
					),
				)
			);
		}

		$notices_before = wc_get_notices();
		wc_clear_notices();

		WC()->cart->add_discount( $code );
		WC()->cart->calculate_totals();

		$errors = wc_get_notices( 'error' );
		if ( ! empty( $errors ) ) {
			$message = wp_strip_all_tags( $errors[0]['notice'] ?? __( 'Could not apply coupon.', 'nuttergood' ) );
			wc_clear_notices();
			foreach ( $notices_before as $type => $group ) {
				foreach ( $group as $notice ) {
					wc_add_notice( $notice['notice'], $type );
				}
			}
			wp_send_json_error( array( 'message' => $message ) );
		}

		wc_clear_notices();
		foreach ( $notices_before as $type => $group ) {
			foreach ( $group as $notice ) {
				wc_add_notice( $notice['notice'], $type );
			}
		}

		if ( ! nuttergood_farmley_side_cart_coupon_is_applied( $code ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not apply coupon.', 'nuttergood' ) ) );
		}

		$message = __( 'Coupon applied successfully.', 'nuttergood' );

		$fragments = apply_filters( 'woocommerce_add_to_cart_fragments', array() );

		wp_send_json_success(
			array_merge(
				nuttergood_farmley_side_cart_meta_payload(),
				array(
					'message'   => $message,
					'fragments' => $fragments,
					'cart_hash' => WC()->cart->get_cart_hash(),
				)
			)
		);
	}
	add_action( 'wc_ajax_ng_farmley_side_cart_apply_coupon', 'nuttergood_farmley_side_cart_ajax_apply_coupon' );
	add_action( 'wc_ajax_nopriv_ng_farmley_side_cart_apply_coupon', 'nuttergood_farmley_side_cart_ajax_apply_coupon' );
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_should_load' ) ) {
	function nuttergood_farmley_side_cart_should_load() {
		return ! is_admin() && class_exists( 'WooCommerce' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_assets' ) ) {
	function nuttergood_farmley_side_cart_assets() {
		if ( ! nuttergood_farmley_side_cart_should_load() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();

		$css = $dir . '/assets/css/farmley-side-cart.css';
		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-side-cart',
				$uri . '/assets/css/farmley-side-cart.css',
				array( 'greenpath-core-style' ),
				filemtime( $css )
			);
		}

		if ( wp_script_is( 'gsap', 'registered' ) ) {
			wp_enqueue_script( 'gsap' );
		}

		$js = $dir . '/assets/js/farmley-side-cart.js';
		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-side-cart',
				$uri . '/assets/js/farmley-side-cart.js',
				array( 'jquery', 'gsap', 'greenpath-core-script' ),
				filemtime( $js ),
				true
			);

			$data = function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty()
				? nuttergood_farmley_side_cart_progress_data()
				: array( 'percent' => 0 );
			$meta = function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty()
				? nuttergood_farmley_side_cart_meta_payload()
				: array(
					'percent'    => 0,
					'itemCount'  => 0,
					'couponHtml' => '',
					'recoHtml'   => '',
				);
			wp_localize_script(
				'nuttergood-farmley-side-cart',
				'ngFarmleySideCart',
				array(
					'percent'     => round( $data['percent'] ),
					'itemCount'   => isset( $data['item_count'] ) ? (int) $data['item_count'] : 0,
					'recoHtml'    => $meta['recoHtml'],
					'couponHtml'  => $meta['couponHtml'],
					'cartLabel'   => __( 'Your cart', 'nuttergood' ),
					'couponCode'  => nuttergood_farmley_side_cart_coupon_code(),
				'nonce'       => wp_create_nonce( 'ng-farmley-sc' ),
				'wcAjaxUrl'   => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : admin_url( 'admin-ajax.php' ),
				'i18n'        => array(
						'couponApplying' => __( 'Applying…', 'nuttergood' ),
						'couponApplied'  => __( 'Applied', 'nuttergood' ),
						'couponFailed'   => __( 'Could not apply coupon.', 'nuttergood' ),
						'removing'       => __( 'Removing…', 'nuttergood' ),
					),
				)
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_side_cart_assets', 41 );
}
