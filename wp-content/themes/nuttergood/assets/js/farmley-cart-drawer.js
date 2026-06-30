/**
 * Farmley product cards — Add to Cart opens side cart; Buy Now goes to checkout.
 */
( function ( $ ) {
	'use strict';

	if ( typeof ngFarmleyCartDrawer === 'undefined' || ! ngFarmleyCartDrawer.enabled ) {
		return;
	}

	function isBuyNowButton( $button ) {
		if ( ! $button || ! $button.length ) {
			return false;
		}

		return $button.hasClass( 'ng-farmley-popular-buy' )
			|| $button.hasClass( 'ng-farmley-qv__buy-now' )
			|| $button.hasClass( 'ng-farmley-buy-now' );
	}

	function isProductListContext( $button ) {
		if ( ! $button || ! $button.length ) {
			return false;
		}

		return $button.closest(
			'.ng-farmley-product-cards, .qodef-woo-product-list, .qodef-e-widget-area, .ng-farmley-popular-card, .ng-farmley-qv, #qodef-woo-page'
		).length > 0;
	}

	function shouldOpenDrawer( $button ) {
		if ( isBuyNowButton( $button ) ) {
			return false;
		}

		return isProductListContext( $button );
	}

	function getCheckoutUrl() {
		if ( ngFarmleyCartDrawer.checkoutUrl ) {
			return ngFarmleyCartDrawer.checkoutUrl;
		}

		if ( window.wc_add_to_cart_params && wc_add_to_cart_params.checkout_url ) {
			return wc_add_to_cart_params.checkout_url;
		}

		if ( window.wc_add_to_cart_params && wc_add_to_cart_params.cart_url ) {
			var cartUrl = wc_add_to_cart_params.cart_url;
			if ( /\/cart\/?$/.test( cartUrl ) ) {
				return cartUrl.replace( /\/cart\/?$/, '/checkout/' );
			}
		}

		return '/checkout/';
	}

	function redirectToCheckout() {
		window.location.href = getCheckoutUrl();
	}

	function getSideCartWidget() {
		return $( '.widget_greenpath_core_woo_side_area_cart' ).first();
	}

	function openSideCart() {
		var $holder = getSideCartWidget();

		if ( ! $holder.length || $holder.hasClass( 'qodef--opened' ) ) {
			return;
		}

		$holder.find( '.qodef-m-opener' ).first().trigger( 'click' );
	}

	function closeQuickView() {
		var $qv = $( '#qode-quick-view-for-woocommerce-pop-up.qqvfw--opened' );

		if ( $qv.length ) {
			$qv.find( '.qqvfw-m-close' ).first().trigger( 'click' );
		}
	}

	function setButtonLoading( $button, loading ) {
		if ( ! $button || ! $button.length ) {
			return;
		}

		if ( loading ) {
			$button.addClass( 'loading' ).removeClass( 'added' );
		} else {
			$button.removeClass( 'loading' );
		}
	}

	function setBuyNowLoading( $button, loading ) {
		if ( ! $button || ! $button.length ) {
			return;
		}

		var $label = $button.find( 'span' ).first();
		if ( ! $label.length ) {
			$label = $button;
		}

		if ( loading ) {
			if ( ! $button.data( 'ngOriginalBuyLabel' ) ) {
				$button.data( 'ngOriginalBuyLabel', $.trim( $label.text() ) );
			}

			$button.addClass( 'loading ng-farmley-buy-now--busy' ).attr( 'aria-busy', 'true' );
			$label.text( ngFarmleyCartDrawer.i18nBuyNowLoading || 'Processing…' );
			return;
		}

		var original = $button.data( 'ngOriginalBuyLabel' );
		if ( original ) {
			$label.text( original );
		}

		$button.removeClass( 'loading ng-farmley-buy-now--busy' ).removeAttr( 'aria-busy' );
	}

	function cleanupCardCartUi( $button ) {
		if ( isBuyNowButton( $button ) ) {
			return;
		}

		$( '.ng-farmley-card-footer__cart .added_to_cart, .ng-farmley-product-cards .added_to_cart.wc-forward' ).remove();

		if ( ! $button || ! $button.length ) {
			return;
		}

		$button.removeClass( 'loading added' );
		$button.siblings( '.added_to_cart' ).remove();
		$button.closest( '.product, .ng-farmley-card-footer__cart' ).find( '.added_to_cart.wc-forward' ).remove();
	}

	function ajaxAddToCart( productId, quantity, $button ) {
		if ( typeof wc_add_to_cart_params === 'undefined' || ! productId ) {
			return $.Deferred().reject().promise();
		}

		$button = $button && $button.length ? $button : $( '<a/>' );
		quantity = quantity || 1;

		var data = {
			product_id: productId,
			quantity: quantity,
		};

		if ( isBuyNowButton( $button ) ) {
			setBuyNowLoading( $button, true );
		} else {
			setButtonLoading( $button, true );
		}

		$( document.body ).trigger( 'adding_to_cart', [ $button, data ] );

		return $.ajax( {
			type: 'POST',
			url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' ),
			data: data,
			dataType: 'json',
		} )
			.done( function ( response ) {
				if ( ! response ) {
					if ( isBuyNowButton( $button ) ) {
						setBuyNowLoading( $button, false );
					} else {
						setButtonLoading( $button, false );
					}
					return;
				}

				if ( response.error && response.product_url ) {
					window.location = response.product_url;
					return;
				}

				if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' && ! isBuyNowButton( $button ) ) {
					window.location = wc_add_to_cart_params.cart_url;
					return;
				}

				$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $button ] );
			} )
			.fail( function () {
				if ( isBuyNowButton( $button ) ) {
					setBuyNowLoading( $button, false );
				} else {
					setButtonLoading( $button, false );
				}
			} );
	}

	function handleAddToCartClick( event ) {
		var target = event.target.closest( '.add_to_cart_button.ajax_add_to_cart, .ng-farmley-popular-buy.ajax_add_to_cart, .ng-farmley-buy-now.ajax_add_to_cart' );

		if ( ! target || ! isProductListContext( $( target ) ) ) {
			return;
		}

		var productId = target.getAttribute( 'data-product_id' ) || target.dataset.product_id;

		if ( ! productId ) {
			return;
		}

		event.preventDefault();
		event.stopImmediatePropagation();

		var $button = $( target );

		if ( $button.hasClass( 'loading' ) || $button.hasClass( 'ng-farmley-buy-now--busy' ) ) {
			return;
		}

		var quantity = $button.data( 'quantity' ) || target.dataset.quantity || 1;

		ajaxAddToCart( productId, quantity, $button );
	}

	function bindCaptureAddToCart() {
		document.addEventListener( 'click', handleAddToCartClick, true );
	}

	function bindQuickViewForms() {
		$( document ).on( 'submit.ngCartDrawer', '.ng-farmley-qv__cart', function ( event ) {
			var $form = $( this );
			var $panel = $form.closest( '.ng-farmley-qv' );

			if ( ! $panel.length ) {
				return;
			}

			event.preventDefault();
			event.stopImmediatePropagation();

			var productId = $panel.data( 'product-id' ) || $form.find( '[name="add-to-cart"]' ).val();
			var qty = $form.find( '.ng-farmley-qv__qty-input, input.qty' ).val() || 1;
			var $button = $form.find( '.ng-farmley-qv__atc, .single_add_to_cart_button' ).first();

			ajaxAddToCart( productId, qty, $button );
		} );

		$( document ).on( 'click.ngCartDrawer', '.ng-farmley-qv__buy-now', function ( event ) {
			var $panel = $( this ).closest( '.ng-farmley-qv' );
			var $form = $panel.find( '.ng-farmley-qv__cart' );

			if ( ! $form.length ) {
				return;
			}

			event.preventDefault();
			event.stopImmediatePropagation();

			var productId = $panel.data( 'product-id' );
			var qty = $form.find( '.ng-farmley-qv__qty-input, input.qty' ).val() || 1;

			ajaxAddToCart( productId, qty, $( this ) );
		} );
	}

	$( document.body ).on( 'added_to_cart.ngCartDrawer', function ( event, fragments, cartHash, $button ) {
		if ( isBuyNowButton( $button ) ) {
			setBuyNowLoading( $button, true );
			redirectToCheckout();
			return;
		}

		window.setTimeout( function () {
			cleanupCardCartUi( $button );
		}, 0 );

		if ( shouldOpenDrawer( $button ) ) {
			closeQuickView();
			window.setTimeout( function () {
				openSideCart();
				$( document.body ).trigger( 'ng_farmley_side_cart_opened' );
			}, 80 );
		}
	} );

	$( document.body ).on( 'wc_cart_button_updated.ngCartDrawer', function ( event, $button ) {
		if ( ! isProductListContext( $button ) || isBuyNowButton( $button ) ) {
			return;
		}

		cleanupCardCartUi( $button );
	} );

	$( document ).on( 'greenpath_trigger_get_new_posts', function () {
		cleanupCardCartUi( null );
	} );

	$( function () {
		bindCaptureAddToCart();
		bindQuickViewForms();
		cleanupCardCartUi( null );
	} );
}( jQuery ) );
