<?php
/**
 * Default WooCommerce coupons (Welcome 10%) + coupon ordering for side cart.
 *
 * @package NutterlyGood
 */

if ( ! function_exists( 'nuttergood_farmley_welcome_coupon_code' ) ) {
	function nuttergood_farmley_welcome_coupon_code() {
		return apply_filters( 'nuttergood_farmley_welcome_coupon_code', 'WELCOME10' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_ensure_welcome_coupon' ) ) {
	function nuttergood_farmley_ensure_welcome_coupon() {
		if ( ! class_exists( 'WC_Coupon' ) ) {
			return '';
		}

		$code   = nuttergood_farmley_welcome_coupon_code();
		$coupon = new WC_Coupon( $code );

		if ( $coupon->get_id() ) {
			return $code;
		}

		$coupon = new WC_Coupon();
		$coupon->set_code( $code );
		$coupon->set_description( __( 'Welcome offer — 10% off for all shoppers', 'nuttergood' ) );
		$coupon->set_discount_type( 'percent' );
		$coupon->set_amount( 10 );
		$coupon->set_individual_use( false );
		$coupon->set_usage_limit( 0 );
		$coupon->set_usage_limit_per_user( 0 );
		$coupon->set_minimum_amount( 0 );
		$coupon->save();

		return $code;
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_coupon_sort_weight' ) ) {
	function nuttergood_farmley_side_cart_coupon_sort_weight( WC_Coupon $coupon ) {
		$code = strtoupper( $coupon->get_code() );

		if ( 0 === strpos( $code, 'NG20' ) ) {
			return 0;
		}

		if ( $code === strtoupper( nuttergood_farmley_welcome_coupon_code() ) ) {
			return 1;
		}

		if ( $coupon->is_type( 'percent' ) ) {
			return 10 - (int) $coupon->get_amount();
		}

		return 50;
	}
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_sort_coupons' ) ) {
	/**
	 * @param WC_Coupon[] $coupons
	 * @return WC_Coupon[]
	 */
	function nuttergood_farmley_side_cart_sort_coupons( $coupons ) {
		usort(
			$coupons,
			static function ( $a, $b ) {
				$wa = nuttergood_farmley_side_cart_coupon_sort_weight( $a );
				$wb = nuttergood_farmley_side_cart_coupon_sort_weight( $b );
				if ( $wa !== $wb ) {
					return $wa - $wb;
				}
				return (float) $b->get_amount() <=> (float) $a->get_amount();
			}
		);

		return $coupons;
	}
}

if ( ! function_exists( 'nuttergood_farmley_saver_coupon_code' ) ) {
	function nuttergood_farmley_saver_coupon_code() {
		return apply_filters( 'nuttergood_farmley_saver_coupon_code', 'SAVER8' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_ensure_saver_coupon' ) ) {
	function nuttergood_farmley_ensure_saver_coupon() {
		if ( ! class_exists( 'WC_Coupon' ) ) {
			return '';
		}

		$code   = nuttergood_farmley_saver_coupon_code();
		$coupon = new WC_Coupon( $code );

		if ( $coupon->get_id() ) {
			return $code;
		}

		$coupon = new WC_Coupon();
		$coupon->set_code( $code );
		$coupon->set_description( __( '8% off on orders above ₹899', 'nuttergood' ) );
		$coupon->set_discount_type( 'percent' );
		$coupon->set_amount( 8 );
		$coupon->set_individual_use( false );
		$coupon->set_usage_limit( 0 );
		$coupon->set_usage_limit_per_user( 0 );
		$coupon->set_minimum_amount( 899 );
		$coupon->save();

		return $code;
	}
}

if ( ! function_exists( 'nuttergood_farmley_resolve_wc_coupon_by_code' ) ) {
	/**
	 * Case-insensitive coupon lookup.
	 */
	function nuttergood_farmley_resolve_wc_coupon_by_code( $code ) {
		if ( ! class_exists( 'WC_Coupon' ) ) {
			return null;
		}

		$raw   = wc_clean( (string) $code );
		$tries = array_unique(
			array_filter(
				array(
					$raw,
					wc_format_coupon_code( $raw ),
					strtoupper( $raw ),
					strtolower( $raw ),
				)
			)
		);

		foreach ( $tries as $try ) {
			$coupon = new WC_Coupon( $try );
			if ( $coupon->get_id() ) {
				return $coupon;
			}
		}

		return null;
	}
}

if ( ! function_exists( 'nuttergood_farmley_coupon_id_from_code_insensitive' ) ) {
	function nuttergood_farmley_coupon_id_from_code_insensitive( $coupon_id, $code ) {
		if ( $coupon_id ) {
			return $coupon_id;
		}

		$coupon = nuttergood_farmley_resolve_wc_coupon_by_code( $code );

		return $coupon ? $coupon->get_id() : $coupon_id;
	}
	add_filter( 'woocommerce_get_coupon_id_from_code', 'nuttergood_farmley_coupon_id_from_code_insensitive', 10, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_coupons_bootstrap_init' ) ) {
	function nuttergood_farmley_coupons_bootstrap_init() {
		nuttergood_farmley_ensure_welcome_coupon();
		nuttergood_farmley_ensure_saver_coupon();
	}
	add_action( 'init', 'nuttergood_farmley_coupons_bootstrap_init', 12 );
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_filter_sorted_wc_coupons' ) ) {
	function nuttergood_farmley_side_cart_filter_sorted_wc_coupons( $coupons ) {
		return nuttergood_farmley_side_cart_sort_coupons( $coupons );
	}
	add_filter( 'nuttergood_farmley_side_cart_wc_coupons', 'nuttergood_farmley_side_cart_filter_sorted_wc_coupons' );
}

if ( ! function_exists( 'nuttergood_farmley_side_cart_filter_sorted_visible_coupons' ) ) {
	function nuttergood_farmley_side_cart_filter_sorted_visible_coupons( $coupons ) {
		return nuttergood_farmley_side_cart_sort_coupons( $coupons );
	}
	add_filter( 'nuttergood_farmley_side_cart_visible_coupons', 'nuttergood_farmley_side_cart_filter_sorted_visible_coupons' );
}