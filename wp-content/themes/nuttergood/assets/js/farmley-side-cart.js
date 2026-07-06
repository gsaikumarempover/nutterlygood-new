/**
 * Side cart — granular fragment updates, scroll preservation, no full-panel rebuild flicker.
 */
( function ( $ ) {
	'use strict';

	var PAPER_COLORS = [ '#0c533d', '#88a842', '#b99531', '#fcf4eb', '#f4a7b9', '#e8f3ee' ];
	var cartIsOpen = false;
	var burstDoneThisOpen = false;
	var lastProgressPercent = 0;
	var unlockedMilestones = {};
	var layoutReady = false;
	var fragmentUpdateTimer = null;
	var fragmentUpdatePending = false;
	var updatingCount = 0;

	function $widgets() {
		return $( '.widget_greenpath_core_woo_side_area_cart' );
	}

	function $widget() {
		return $widgets().first();
	}

	function $panel() {
		return $widget().find( '.qodef-widget-side-area-cart-content' ).first();
	}

	function $scroll() {
		return $panel().children( '.ng-farmley-sc-scroll' ).first();
	}

	function reducedMotion() {
		return window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function setUpdating( active ) {
		if ( active ) {
			updatingCount += 1;
		} else {
			updatingCount = Math.max( 0, updatingCount - 1 );
		}

		$widget().toggleClass( 'ng-farmley-sc-is-updating', updatingCount > 0 );
	}

	function captureUIState() {
		var $scrollEl = $scroll();
		var state = {
			scrollTop: $scrollEl.length ? $scrollEl[0].scrollTop : 0,
			recoScroll: {},
			couponExpanded: false,
		};

		$panel().find( '.ng-farmley-sc-reco__row' ).each( function () {
			var key = $( this ).closest( '.ng-farmley-sc-reco' ).attr( 'data-ng-sc-reco-id' ) || 'reco-' + $( this ).index();
			state.recoScroll[ key ] = this.scrollLeft;
		} );

		var $couponToggle = $panel().find( '[data-ng-sc-toggle-coupons][aria-expanded="true"]' ).first();
		state.couponExpanded = !! $couponToggle.length;

		return state;
	}

	function restoreUIState( state ) {
		if ( ! state ) {
			return;
		}

		var $scrollEl = $scroll();
		if ( $scrollEl.length ) {
			$scrollEl[0].scrollTop = state.scrollTop;
		}

		$panel().find( '.ng-farmley-sc-reco' ).each( function ( index ) {
			var key = $( this ).attr( 'data-ng-sc-reco-id' ) || 'reco-' + index;
			var left = state.recoScroll[ key ];
			var $row = $( this ).find( '.ng-farmley-sc-reco__row' ).first();

			if ( typeof left === 'number' && $row.length ) {
				$row[0].scrollLeft = left;
			}
		} );

		if ( state.couponExpanded ) {
			var $btn = $panel().find( '[data-ng-sc-toggle-coupons]' ).first();
			var $panelCoupons = $btn.closest( '.ng-farmley-sc-coupon__card' ).find( '[data-ng-sc-coupons-panel]' ).first();
			if ( $btn.length && $panelCoupons.length ) {
				$btn.attr( 'aria-expanded', 'true' ).addClass( 'is-open' );
				$panelCoupons.prop( 'hidden', false );
			}
		}
	}

	function destroyPerfectScrollbar() {
		$panel().find( '.qodef-woo-side-area-cart' ).each( function () {
			try {
				if ( this._ps ) {
					this._ps.destroy();
					delete this._ps;
				}
			} catch ( err ) {
				// Ignore teardown errors from stale instances.
			}

			$( this ).removeClass( 'ps ps--active-y ps--active-x' );
		} );

		$panel().find( '.qodef-woo-side-area-cart .ps__rail-x, .qodef-woo-side-area-cart .ps__rail-y' ).remove();
	}

	function cleanupDuplicates() {
		var $footer = $panel().find( '.ng-farmley-sc-footer' ).first();

		$panel().find( '.ng-farmley-sc-reco' ).not( $footer.find( '.ng-farmley-sc-reco' ) ).remove();
		$panel().find( '.ng-farmley-sc-reco-slot' ).not( $footer.find( '.ng-farmley-sc-reco-slot' ) ).remove();
		$panel().find( '.ng-farmley-sc-coupon' ).not( $footer.find( '.ng-farmley-sc-coupon' ) ).remove();
		$panel().find( '.ng-farmley-sc-coupon-slot' ).not( $footer.find( '.ng-farmley-sc-coupon-slot' ) ).remove();
		$panel().find( '.ng-farmley-sc-items-scroll' ).remove();

		if ( $footer.length && $footer.find( '.ng-farmley-sc-reco' ).length > 1 ) {
			$footer.find( '.ng-farmley-sc-reco' ).slice( 1 ).remove();
		}
	}

	function updateHeading() {
		var $heading = $panel().find( '.qodef-side-area-cart-heading' ).first();
		if ( ! $heading.length ) {
			return;
		}

		var count = $panel().find( '.qodef-woo-side-area-cart-item' ).length;
		var label = window.ngFarmleySideCart && ngFarmleySideCart.cartLabel ? ngFarmleySideCart.cartLabel : 'Your cart';
		$heading.text( label + ' (' + count + ')' );
	}

	function ensureFooter() {
		var $p = $panel();
		var $footer = $p.find( '.ng-farmley-sc-footer' ).first();

		if ( ! $footer.length ) {
			$footer = $( '<div class="ng-farmley-sc-footer"></div>' );
			$p.append( $footer );
		}

		var $couponSlot = $footer.find( '.ng-farmley-sc-coupon-slot' ).first();
		if ( ! $couponSlot.length ) {
			$couponSlot = $( '<div class="ng-farmley-sc-coupon-slot"></div>' );
			$footer.append( $couponSlot );
		}

		var $recoSlot = $footer.find( '.ng-farmley-sc-reco-slot' ).first();
		if ( ! $recoSlot.length ) {
			$recoSlot = $( '<div class="ng-farmley-sc-reco-slot"></div>' );
			$footer.append( $recoSlot );
		}

		var $orderSlot = $footer.find( '.ng-farmley-sc-order-slot' ).first();
		if ( ! $orderSlot.length ) {
			$orderSlot = $( '<div class="ng-farmley-sc-order-slot"></div>' );
			$footer.append( $orderSlot );
		}

		var $directReco = $footer.children( '.ng-farmley-sc-reco' ).first();
		if ( $directReco.length ) {
			$directReco.appendTo( $recoSlot );
		}

		var $orderDetails = $p.find( '.ng-farmley-sc-order-details, .qodef-m-order-details' ).first();
		if ( $orderDetails.length && ! $.contains( $orderSlot[0], $orderDetails[0] ) ) {
			$orderDetails.detach().appendTo( $orderSlot );
		}

		var $action = $p.find( '.qodef-m-action' ).first();
		if ( $action.length && ! $.contains( $footer[0], $action[0] ) ) {
			$action.detach().appendTo( $footer );
		}

		return $footer;
	}

	function fillFooterContent( force ) {
		var $footer = ensureFooter();
		var $couponSlot = $footer.find( '.ng-farmley-sc-coupon-slot' ).first();
		var $recoSlot = $footer.find( '.ng-farmley-sc-reco-slot' ).first();

		if ( ! window.ngFarmleySideCart ) {
			return;
		}

		if ( ( force || ! $couponSlot.children().length ) && ngFarmleySideCart.couponHtml ) {
			$couponSlot.html( ngFarmleySideCart.couponHtml );
		}

		if ( ( force || ! $recoSlot.find( '.ng-farmley-sc-reco' ).length ) && ngFarmleySideCart.recoHtml ) {
			$recoSlot.html( ngFarmleySideCart.recoHtml );
		}
	}

	function applySideCartMeta( data ) {
		if ( ! data || ! window.ngFarmleySideCart ) {
			return;
		}

		if ( typeof data.couponHtml === 'string' ) {
			ngFarmleySideCart.couponHtml = data.couponHtml;
		}
		if ( typeof data.recoHtml === 'string' ) {
			ngFarmleySideCart.recoHtml = data.recoHtml;
		}
		if ( typeof data.percent !== 'undefined' ) {
			ngFarmleySideCart.percent = data.percent;
		}
		if ( typeof data.itemCount !== 'undefined' ) {
			ngFarmleySideCart.itemCount = data.itemCount;
		}
	}

	function resolveFragmentTargets( selector ) {
		var scopedPrefix = '.widget_greenpath_core_woo_side_area_cart ';
		var $targets = $( selector );

		if ( ! $targets.length && selector.indexOf( scopedPrefix ) === 0 ) {
			var innerSelector = selector.slice( scopedPrefix.length );
			$widgets().each( function () {
				var $match = $( this ).find( innerSelector );
				if ( $match.length ) {
					$targets = $targets.add( $match );
				}
			} );
		}

		return $targets;
	}

	function applyCartFragments( fragments ) {
		if ( ! fragments ) {
			return;
		}

		if ( ! $widgets().length && ! $( '.qodef-widget-side-area-cart-inner' ).length ) {
			return;
		}

		$.each( fragments, function ( selector, html ) {
			var $targets = resolveFragmentTargets( selector );

			if ( ! $targets.length ) {
				return;
			}

			if ( html === '' || html === null ) {
				$targets.remove();
				return;
			}

			$targets.each( function () {
				$( this ).replaceWith( html );
			} );
		} );
	}

	function normalizeCartMarkup( $root ) {
		var $scope = $root && $root.length ? $root : $panel();
		if ( ! $scope.length ) {
			return;
		}

		var $content = $scope.hasClass( 'qodef-widget-side-area-cart-content' )
			? $scope
			: $scope.find( '.qodef-widget-side-area-cart-content' ).first();

		if ( ! $content.length ) {
			return;
		}

		var $cartSlot = $content.children( '.ng-farmley-sc-cart-slot' ).first();
		if ( ! $cartSlot.length ) {
			$cartSlot = $( '<div class="ng-farmley-sc-cart-slot"></div>' );
			var $notFound = $content.children( '.qodef-m-posts-not-found' ).first();
			var $cartList = $content.children( '.qodef-woo-side-area-cart' ).first();

			if ( $notFound.length ) {
				$notFound.appendTo( $cartSlot );
			} else if ( $cartList.length ) {
				$cartList.appendTo( $cartSlot );
			} else {
				$content.children().not( '.qodef-side-area-cart-top, .ng-farmley-sc-progress, .ng-farmley-sc-footer, .qodef-m-action, .ng-farmley-sc-burst, .ng-farmley-sc-scroll' ).appendTo( $cartSlot );
			}

			if ( $cartSlot.children().length ) {
				$content.children( '.qodef-side-area-cart-top' ).last().after( $cartSlot );
			}
		}

		ensureFooter();
	}

	function syncSideCartMeta( callback ) {
		if ( typeof wc_add_to_cart_params === 'undefined' || ! window.ngFarmleySideCart || ! ngFarmleySideCart.nonce ) {
			if ( callback ) {
				callback();
			}
			return;
		}

		$.post(
			wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'ng_farmley_side_cart_meta' ),
			{ security: ngFarmleySideCart.nonce }
		).done( function ( response ) {
			if ( response && response.success && response.data ) {
				applySideCartMeta( response.data );
			}
		} ).always( function () {
			if ( callback ) {
				callback();
			}
		} );
	}

	function applyCouponCode( $btn, code ) {
		if ( typeof wc_add_to_cart_params === 'undefined' || ! window.ngFarmleySideCart || ! ngFarmleySideCart.nonce ) {
			return;
		}

		if ( ! $btn || ! $btn.length || $btn.prop( 'disabled' ) || $btn.hasClass( 'ng-farmley-sc-coupon__apply--busy' ) ) {
			return;
		}

		var $couponWrap = $btn.closest( '[data-ng-sc-coupon]' );
		var $feedback = $couponWrap.find( '[data-ng-sc-coupon-feedback]' ).first();
		var applying = ngFarmleySideCart.i18n && ngFarmleySideCart.i18n.couponApplying ? ngFarmleySideCart.i18n.couponApplying : 'Applying…';

		$couponWrap.find( '.ng-farmley-sc-coupon__apply--busy' ).removeClass( 'ng-farmley-sc-coupon__apply--busy' ).prop( 'disabled', false ).filter( ':not(:disabled)' ).text( 'Apply' );
		$btn.addClass( 'ng-farmley-sc-coupon__apply--busy' ).prop( 'disabled', true ).text( applying );
		$feedback.removeClass( 'is-error is-success' ).text( '' );
		setUpdating( true );

		$.post(
			wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'ng_farmley_side_cart_apply_coupon' ),
			{
				security: ngFarmleySideCart.nonce,
				coupon_code: code,
			}
		).done( function ( response ) {
			if ( response && response.success && response.data ) {
				finishCartUpdate( response.data, { animateProgress: true } );
				$feedback.addClass( 'is-success' ).text( response.data.message || '' );
			} else {
				var msg = response && response.data && response.data.message ? response.data.message : ( ngFarmleySideCart.i18n ? ngFarmleySideCart.i18n.couponFailed : 'Could not apply coupon.' );
				$feedback.addClass( 'is-error' ).text( msg );
				$btn.removeClass( 'ng-farmley-sc-coupon__apply--busy' ).prop( 'disabled', false ).text( 'Apply' );
			}
		} ).fail( function () {
			$feedback.addClass( 'is-error' ).text( ngFarmleySideCart.i18n ? ngFarmleySideCart.i18n.couponFailed : 'Could not apply coupon.' );
			$btn.removeClass( 'ng-farmley-sc-coupon__apply--busy' ).prop( 'disabled', false ).text( 'Apply' );
		} ).always( function () {
			setUpdating( false );
		} );
	}

	function bindCouponApply() {
		if ( document.body._ngCouponBound ) {
			return;
		}

		document.body._ngCouponBound = true;

		$( document.body ).on( 'click.ngScCoupon', '.widget_greenpath_core_woo_side_area_cart [data-ng-sc-apply-coupon]', function ( event ) {
			event.preventDefault();
			event.stopPropagation();

			var $btn = $( this );
			var code = $btn.attr( 'data-coupon-code' ) || ( window.ngFarmleySideCart ? ngFarmleySideCart.couponCode : '' );
			if ( ! code ) {
				return;
			}

			applyCouponCode( $btn, code );
		} );
	}

	function bindCouponViewAll() {
		if ( document.body._ngCouponViewAllBound ) {
			return;
		}

		document.body._ngCouponViewAllBound = true;

		$( document.body ).on( 'click.ngScCouponViewAll', '.widget_greenpath_core_woo_side_area_cart [data-ng-sc-toggle-coupons]', function ( event ) {
			event.preventDefault();
			event.stopPropagation();

			var $btn = $( this );
			var $couponPanel = $btn.closest( '.ng-farmley-sc-coupon__card' ).find( '[data-ng-sc-coupons-panel]' ).first();
			if ( ! $couponPanel.length ) {
				return;
			}

			var isOpen = $btn.attr( 'aria-expanded' ) === 'true';
			isOpen = ! isOpen;

			$btn.attr( 'aria-expanded', isOpen ? 'true' : 'false' );
			$btn.toggleClass( 'is-open', isOpen );
			$couponPanel.prop( 'hidden', ! isOpen );
		} );
	}

	function bindRemoveItem() {
		if ( document.body._ngRemoveBound ) {
			return;
		}

		document.body._ngRemoveBound = true;

		$( document.body ).on( 'click.ngScRemove', '.widget_greenpath_core_woo_side_area_cart .qodef-woo-side-area-cart .remove_from_cart_button', function ( event ) {
			event.preventDefault();
			event.stopImmediatePropagation();

			if ( typeof wc_add_to_cart_params === 'undefined' ) {
				return;
			}

			var $btn = $( this );
			var cartKey = $btn.attr( 'data-cart_item_key' ) || $btn.data( 'cart_item_key' );

			if ( ! cartKey || $btn.hasClass( 'ng-farmley-sc-removing' ) ) {
				return;
			}

			var $item = $btn.closest( '.qodef-woo-side-area-cart-item' );
			$btn.addClass( 'ng-farmley-sc-removing' );
			$item.addClass( 'ng-farmley-sc-item--removing' );
			setUpdating( true );

			$.ajax( {
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'remove_from_cart' ),
				data: { cart_item_key: cartKey },
				dataType: 'json',
			} ).done( function ( response ) {
				if ( response && response.fragments ) {
					finishCartUpdate(
						{
							fragments: response.fragments,
							cart_hash: response.cart_hash,
						},
						{ animateProgress: true }
					);
					$( document.body ).trigger( 'removed_from_cart', [ response.fragments, response.cart_hash, $btn ] );
				} else {
					$btn.removeClass( 'ng-farmley-sc-removing' );
					$item.removeClass( 'ng-farmley-sc-item--removing' );
				}
			} ).fail( function () {
				$btn.removeClass( 'ng-farmley-sc-removing' );
				$item.removeClass( 'ng-farmley-sc-item--removing' );
			} ).always( function () {
				setUpdating( false );
			} );
		} );
	}

	function bindQtyStepper() {
		if ( document.body._ngQtyBound ) {
			return;
		}

		document.body._ngQtyBound = true;

		$( document.body ).on( 'click.ngScQty', '.widget_greenpath_core_woo_side_area_cart [data-ng-sc-qty] [data-action]', function ( event ) {
			event.preventDefault();
			event.stopImmediatePropagation();

			if ( typeof wc_add_to_cart_params === 'undefined' || ! window.ngFarmleySideCart || ! ngFarmleySideCart.nonce ) {
				return;
			}

			var $btn = $( this );
			var $wrap = $btn.closest( '[data-ng-sc-qty]' );

			if ( $wrap.hasClass( 'ng-farmley-sc-qty--busy' ) ) {
				return;
			}

			var cartKey = $wrap.attr( 'data-cart-item-key' ) || '';
			var maxQty = parseInt( $wrap.attr( 'data-max' ) || '0', 10 );
			var action = $btn.attr( 'data-action' );
			var $val = $wrap.find( '.ng-farmley-sc-qty__val' ).first();
			var current = parseInt( $val.text() || '1', 10 );
			var next = current;

			if ( action === 'plus' ) {
				next = current + 1;
				if ( maxQty > 0 && next > maxQty ) {
					return;
				}
			} else if ( action === 'minus' ) {
				next = Math.max( 1, current - 1 );
			} else {
				return;
			}

			if ( next === current ) {
				return;
			}

			var $item = $wrap.closest( '.qodef-woo-side-area-cart-item' );
			$wrap.addClass( 'ng-farmley-sc-qty--busy' );
			$item.addClass( 'ng-farmley-sc-item--updating' );
			setUpdating( true );

			$.post(
				wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'ng_farmley_side_cart_update_qty' ),
				{
					security: ngFarmleySideCart.nonce,
					cart_item_key: cartKey,
					quantity: next,
				}
			).done( function ( response ) {
				if ( response && response.success && response.data ) {
					finishCartUpdate( response.data, { animateProgress: true } );
					$( document.body ).trigger( 'updated_wc_div' );
				} else {
					$wrap.removeClass( 'ng-farmley-sc-qty--busy' );
					$item.removeClass( 'ng-farmley-sc-item--updating' );
				}
			} ).fail( function () {
				$wrap.removeClass( 'ng-farmley-sc-qty--busy' );
				$item.removeClass( 'ng-farmley-sc-item--updating' );
			} ).always( function () {
				setUpdating( false );
			} );
		} );
	}

	function ensureScrollWrap() {
		var $p = $panel();
		if ( ! $p.length ) {
			return;
		}

		var $scrollEl = $p.children( '.ng-farmley-sc-scroll' ).first();

		if ( $scrollEl.length ) {
			attachScrollWheel( $scrollEl[0] );
			return;
		}

		$scrollEl = $( '<div class="ng-farmley-sc-scroll" tabindex="-1"></div>' );
		$p.children().not( '.ng-farmley-sc-burst' ).appendTo( $scrollEl );
		$p.append( $scrollEl );
		attachScrollWheel( $scrollEl[0] );
	}

	function attachScrollWheel( scrollEl ) {
		if ( ! scrollEl ) {
			return;
		}

		if ( scrollEl._ngFarmleyWheelHandler ) {
			scrollEl.removeEventListener( 'wheel', scrollEl._ngFarmleyWheelHandler );
		}

		scrollEl._ngFarmleyWheelHandler = function ( event ) {
			if ( ! $widget().hasClass( 'qodef--opened' ) ) {
				return;
			}

			event.stopPropagation();
		};

		scrollEl.addEventListener( 'wheel', scrollEl._ngFarmleyWheelHandler, { passive: true } );
	}

	function setRecoAddLoading( $btn, loading ) {
		if ( ! $btn || ! $btn.length ) {
			return;
		}

		if ( loading ) {
			$btn.addClass( 'loading' ).attr( 'aria-busy', 'true' );
			return;
		}

		$btn.removeClass( 'loading added' ).removeAttr( 'aria-busy' );
	}

	function bindRecoAddLoading() {
		var widget = $widget()[0];
		if ( ! widget || widget._ngRecoAddBound ) {
			return;
		}

		widget._ngRecoAddBound = true;

		$( widget ).on( 'click.ngRecoAdd', '.ng-farmley-sc-reco__add.ajax_add_to_cart', function () {
			setRecoAddLoading( $( this ), true );
		} );

		$( document.body ).on( 'adding_to_cart.ngRecoAdd', function ( event, $button ) {
			if ( $button && $button.closest && $button.closest( '.ng-farmley-sc-reco' ).length ) {
				setRecoAddLoading( $button, true );
			}
		} );

		$( document.body ).on( 'wc_cart_button_updated.ngRecoAdd', function ( event, $button ) {
			if ( $button && $button.closest && $button.closest( '.ng-farmley-sc-reco' ).length ) {
				setRecoAddLoading( $button, false );
			}
		} );

		$( document.body ).on( 'wc_fragments_refreshed.ngRecoAdd added_to_cart.ngRecoAdd', function () {
			$panel().find( '.ng-farmley-sc-reco__add.loading' ).removeClass( 'loading added' ).removeAttr( 'aria-busy' );
		} );
	}

	function buildLayout( options ) {
		options = options || {};
		var $p = $panel();

		if ( ! $p.length ) {
			return;
		}

		normalizeCartMarkup( $p );
		$widgets().addClass( 'ng-farmley-sc-ready' );

		if ( ! options.light ) {
			cleanupDuplicates();
		}

		ensureFooter();

		if ( options.forceFooter || ! options.light ) {
			fillFooterContent( !! options.forceFooter );
		} else {
			fillFooterContent( false );
		}

		ensureScrollWrap();
		updateHeading();
		destroyPerfectScrollbar();
		initRecoCarousels( options );
		layoutReady = true;
	}

	function initRecoCarousels( options ) {
		options = options || {};

		$panel().find( '.ng-farmley-sc-reco' ).each( function ( index ) {
			var $reco = $( this );
			var $row = $reco.find( '.ng-farmley-sc-reco__row' ).first();
			var $prev = $reco.find( '.ng-farmley-sc-reco__prev' ).first();
			var $next = $reco.find( '.ng-farmley-sc-reco__next' ).first();

			if ( ! $row.length || ! $prev.length || ! $next.length ) {
				return;
			}

			if ( ! $reco.attr( 'data-ng-sc-reco-id' ) ) {
				$reco.attr( 'data-ng-sc-reco-id', 'reco-' + index );
			}

			if ( $reco.data( 'ngRecoReady' ) && ! options.reinitReco ) {
				return;
			}

			$reco.data( 'ngRecoReady', true );

			$prev.off( 'click.ngRecoCarousel' );
			$next.off( 'click.ngRecoCarousel' );
			$row.off( 'scroll.ngRecoCarousel' );

			function getStep() {
				var $item = $row.find( '.ng-farmley-sc-reco__item' ).first();
				return $item.length ? $item.outerWidth( true ) : 198;
			}

			function updateNav() {
				var el = $row[0];
				var maxScroll = Math.max( 0, el.scrollWidth - el.clientWidth );

				$prev.prop( 'disabled', el.scrollLeft <= 2 );
				$next.prop( 'disabled', el.scrollLeft >= maxScroll - 2 );
			}

			function scrollByStep( direction ) {
				var el = $row[0];
				var step = getStep();
				var target = el.scrollLeft + direction * step;
				var maxScroll = Math.max( 0, el.scrollWidth - el.clientWidth );

				target = Math.max( 0, Math.min( maxScroll, target ) );
				$row.stop( true ).animate( { scrollLeft: target }, 280, updateNav );
			}

			$prev.on( 'click.ngRecoCarousel', function ( event ) {
				event.preventDefault();
				event.stopPropagation();
				scrollByStep( -1 );
			} );

			$next.on( 'click.ngRecoCarousel', function ( event ) {
				event.preventDefault();
				event.stopPropagation();
				scrollByStep( 1 );
			} );

			$row.on( 'scroll.ngRecoCarousel', updateNav );
			updateNav();
		} );
	}

	function finishCartUpdate( payload, options ) {
		options = options || {};
		var uiState = captureUIState();
		var previousPercent = lastProgressPercent;

		setUpdating( true );
		applySideCartMeta( payload );

		if ( ! options.skipFragments ) {
			applyCartFragments( payload && payload.fragments );
		}

		window.requestAnimationFrame( function () {
			buildLayout( {
				light: true,
				forceFooter: typeof payload.couponHtml === 'string' || typeof payload.recoHtml === 'string',
				reinitReco: typeof payload.recoHtml === 'string',
			} );
			updateProgress( options.animateProgress && cartIsOpen && Math.abs( readProgressPercent() - previousPercent ) > 0.5 );
			restoreUIState( uiState );
			$panel().find( '.ng-farmley-sc-qty--busy' ).removeClass( 'ng-farmley-sc-qty--busy' );
			$panel().find( '.ng-farmley-sc-item--updating, .ng-farmley-sc-item--removing' ).removeClass( 'ng-farmley-sc-item--updating ng-farmley-sc-item--removing' );
			setUpdating( false );
		} );
	}

	function readProgressPercent() {
		var $wrap = $panel().find( '[data-ng-sc-progress]' ).first();
		if ( $wrap.length ) {
			return Math.min( 100, Math.max( 0, parseFloat( $wrap.attr( 'data-percent' ) || 0 ) ) );
		}

		if ( window.ngFarmleySideCart && ngFarmleySideCart.percent ) {
			return Math.min( 100, Math.max( 0, parseFloat( ngFarmleySideCart.percent ) ) );
		}

		return 0;
	}

	function setProgressInstant( percent ) {
		percent = Math.min( 100, Math.max( 0, parseFloat( percent ) ) );

		$widget().find( '[data-ng-sc-fill]' ).each( function () {
			var $fill = $( this );
			if ( typeof gsap !== 'undefined' ) {
				gsap.killTweensOf( $fill[0] );
			}
			$fill.css( 'width', percent + '%' );
		} );

		lastProgressPercent = percent;
	}

	function animateProgressFromTo( fromPercent, toPercent ) {
		fromPercent = Math.min( 100, Math.max( 0, parseFloat( fromPercent ) ) );
		toPercent = Math.min( 100, Math.max( 0, parseFloat( toPercent ) ) );

		$widget().find( '[data-ng-sc-fill]' ).each( function () {
			var $fill = $( this );

			if ( typeof gsap !== 'undefined' ) {
				gsap.killTweensOf( $fill[0] );
			}

			if ( typeof gsap !== 'undefined' && ! reducedMotion() && Math.abs( toPercent - fromPercent ) > 0.5 ) {
				gsap.fromTo(
					$fill[0],
					{ width: fromPercent + '%' },
					{ width: toPercent + '%', duration: 0.65, ease: 'power2.out' }
				);
			} else {
				$fill.css( 'width', toPercent + '%' );
			}
		} );

		lastProgressPercent = toPercent;
	}

	function syncUnlockedMilestones( celebrateNew ) {
		$panel().find( '.ng-farmley-sc-progress__pin' ).each( function () {
			var $pin = $( this );
			var key = $pin.attr( 'title' ) || $pin.find( '.ng-farmley-sc-progress__pin-label' ).text();
			var isOn = $pin.hasClass( 'is-on' );

			if ( isOn && ! unlockedMilestones[ key ] ) {
				unlockedMilestones[ key ] = true;
				if ( celebrateNew && cartIsOpen ) {
					$pin.addClass( 'ng-farmley-sc-progress__pin--just-unlocked' );
					window.setTimeout( function () {
						$pin.removeClass( 'ng-farmley-sc-progress__pin--just-unlocked' );
					}, 600 );
				}
			} else if ( ! isOn ) {
				delete unlockedMilestones[ key ];
			}
		} );
	}

	function updateProgress( animateFromPrevious ) {
		var target = readProgressPercent();
		var from = animateFromPrevious ? lastProgressPercent : target;

		if ( animateFromPrevious && Math.abs( target - from ) > 0.5 ) {
			animateProgressFromTo( from, target );
		} else {
			setProgressInstant( target );
		}

		if ( window.ngFarmleySideCart ) {
			ngFarmleySideCart.percent = target;
		}

		syncUnlockedMilestones( animateFromPrevious );
	}

	function paperBurst() {
		if ( burstDoneThisOpen || reducedMotion() ) {
			return;
		}
		if ( ! $widget().hasClass( 'qodef--opened' ) ) {
			return;
		}
		try {
			if ( window.sessionStorage && sessionStorage.getItem( 'ngFarmleyCartFlowerBurstShown' ) ) {
				return;
			}
			if ( window.sessionStorage ) {
				sessionStorage.setItem( 'ngFarmleyCartFlowerBurstShown', '1' );
			}
		} catch ( err ) {
			// Storage can be blocked in private browsers; the per-open guard still applies.
		}

		burstDoneThisOpen = true;

		var $p = $panel();
		var $layer = $p.find( '.ng-farmley-sc-burst' ).first();
		if ( ! $layer.length ) {
			$layer = $( '<div class="ng-farmley-sc-burst" aria-hidden="true"></div>' );
			$p.prepend( $layer );
		}

		var w = $layer.width() || 420;
		var cx = w * 0.5;

		$layer.empty().addClass( 'is-active' );

		for ( var i = 0; i < 18; i++ ) {
			( function ( idx ) {
				var isFlower = idx % 3 === 0;
				var $el = $( '<span class="ng-farmley-sc-burst__piece"></span>' );
				var rot = Math.random() * 360;
				var x0 = cx + ( Math.random() - 0.5 ) * w * 0.34;
				var drift = ( Math.random() - 0.5 ) * w * 0.42;
				var fall = 58 + Math.random() * 92;
				var delay = Math.random() * 120;

				if ( isFlower ) {
					$el.addClass( 'ng-farmley-sc-burst__piece--flower' ).text( idx % 2 ? '✿' : '🌸' ).css( 'font-size', 11 + Math.random() * 6 + 'px' );
				} else {
					$el.addClass( 'ng-farmley-sc-burst__piece--paper' ).css( {
						width: 6 + Math.random() * 7 + 'px',
						height: 8 + Math.random() * 8 + 'px',
						background: PAPER_COLORS[ idx % PAPER_COLORS.length ],
					} );
				}

				$el.css( {
					left: x0 + 'px',
					top: '-10px',
					'--ng-burst-x': drift + 'px',
					'--ng-burst-y': fall + 'px',
					'--ng-burst-r': rot + 140 + Math.random() * 180 + 'deg',
					'animation-delay': delay + 'ms',
					transform: 'rotate(' + rot + 'deg)',
				} );
				$layer.append( $el );
			} )( i );
		}

		window.setTimeout( function () {
			$layer.removeClass( 'is-active' ).empty();
		}, 1600 );
	}

	function onCartOpened() {
		if ( cartIsOpen ) {
			return;
		}

		cartIsOpen = true;

		buildLayout();

		var target = readProgressPercent();
		lastProgressPercent = 0;
		animateProgressFromTo( 0, target );
		syncUnlockedMilestones( false );

		window.setTimeout( function () {
			destroyPerfectScrollbar();
			paperBurst();
		}, 150 );
	}

	function onCartClosed() {
		cartIsOpen = false;
	}

	function watchOpen() {
		var el = $widget()[0];
		if ( ! el ) {
			return;
		}

		var obs = new MutationObserver( function () {
			if ( $widget().hasClass( 'qodef--opened' ) ) {
				onCartOpened();
			} else {
				onCartClosed();
			}
		} );

		obs.observe( el, { attributes: true, attributeFilter: [ 'class' ] } );

		if ( $widget().hasClass( 'qodef--opened' ) ) {
			onCartOpened();
		}
	}

	function scheduleFragmentUpdate( delay, animateProgress ) {
		fragmentUpdatePending = true;

		if ( fragmentUpdateTimer ) {
			window.clearTimeout( fragmentUpdateTimer );
		}

		fragmentUpdateTimer = window.setTimeout( function () {
			fragmentUpdateTimer = null;

			if ( ! fragmentUpdatePending ) {
				return;
			}

			fragmentUpdatePending = false;
			var previousPercent = lastProgressPercent;
			var uiState = captureUIState();

			setUpdating( true );
			window.requestAnimationFrame( function () {
				buildLayout( { light: true, reinitReco: true } );
				updateProgress( cartIsOpen && animateProgress && Math.abs( readProgressPercent() - previousPercent ) > 0.5 );
				restoreUIState( uiState );
				setUpdating( false );
			} );
		}, delay || 80 );
	}

	$( function () {
		bindRecoAddLoading();
		bindCouponApply();
		bindCouponViewAll();
		bindRemoveItem();
		bindQtyStepper();
		buildLayout();
		setProgressInstant( readProgressPercent() );
		syncUnlockedMilestones( false );
		watchOpen();
	} );

	$( document.body ).on( 'wc_fragments_refreshed wc_fragments_loaded', function () {
		finishCartUpdate( {}, { animateProgress: true, skipFragments: true } );
	} );

	$( document.body ).on( 'added_to_cart', function () {
		finishCartUpdate( {}, { animateProgress: true, skipFragments: true } );
	} );
}( jQuery ) );