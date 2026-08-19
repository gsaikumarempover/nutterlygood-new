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
	var openAssistTimer = null;

	function $widgets() {
		return $( '.widget_greenpath_core_woo_side_area_cart' );
	}

	function $widget() {
		var $all = $widgets().not( '.ng-farmley-sc-portal' );
		if ( ! $all.length ) {
			return $widgets();
		}

		var $opened = $all.filter( '.qodef--opened' );
		if ( $opened.length ) {
			return $opened.first();
		}

		if ( window.matchMedia( '(max-width: 1200px)' ).matches ) {
			var $mobile = $( '#qodef-page-mobile-header .widget_greenpath_core_woo_side_area_cart, #qodef-side-area-mobile-header .widget_greenpath_core_woo_side_area_cart' ).filter( ':visible' );
			if ( $mobile.length ) {
				return $mobile.first();
			}
		}

		var $visible = $all.filter( function () {
			return $( this ).is( ':visible' );
		} );

		return $visible.length ? $visible.first() : $all.first();
	}

	function $panel() {
		var $ported = $( '.ng-farmley-sc-portal .qodef-widget-side-area-cart-content' ).first();
		if ( $ported.length ) {
			return $ported;
		}

		return $widget().find( '.qodef-widget-side-area-cart-content' ).first();
	}

	function $scroll() {
		return $panel().children( '.ng-farmley-sc-scroll' ).first();
	}

	/**
	 * Mobile headers are ~58px tall. A position:fixed drawer still lives in that
	 * stacking / hit-test context, so the dimming cover steals touch scroll below
	 * the header bar. Port the panel onto body while open so touches hit the drawer.
	 */
	function portalSideCartPanel() {
		// Never portal a closed cart — the open-assist timeout used to fall back to
		// any widget and re-open the drawer after the X was clicked.
		if ( ! $( 'body' ).hasClass( 'qodef-woo-side-area-cart--opened' ) ) {
			return;
		}

		var $opened = $( '.widget_greenpath_core_woo_side_area_cart.qodef--opened' ).not( '.ng-farmley-sc-portal' ).first();
		if ( ! $opened.length ) {
			return;
		}

		var $content = $opened.find( '.qodef-widget-side-area-cart-content' ).first();
		if ( ! $content.length || $content.closest( '.ng-farmley-sc-portal' ).length ) {
			return;
		}

		$content.data( 'ngScHome', $content.parent() );
		$content.data( 'ngScSourceWidget', $opened );

		var $portal = $( '<div class="widget_greenpath_core_woo_side_area_cart ng-farmley-sc-ready qodef--opened ng-farmley-sc-portal"></div>' );
		var $inner = $( '<div class="qodef-widget-side-area-cart-inner"></div>' );
		$portal.data( 'ngScSourceWidget', $opened );
		$portal.append( $inner );
		$inner.append( $content );
		$( document.body ).append( $portal );
	}

	function unportalSideCartPanel() {
		$( '.ng-farmley-sc-portal' ).each( function () {
			var $portal = $( this );
			var $content = $portal.find( '.qodef-widget-side-area-cart-content' ).first();
			var $home = $content.data( 'ngScHome' );

			if ( $content.length && $home && $home.length ) {
				$home.append( $content );
			}

			$portal.remove();
		} );
	}

	function getSourceSideCartWidget() {
		var $portal = $( '.ng-farmley-sc-portal' ).first();
		if ( $portal.length ) {
			var $fromPortal = $portal.data( 'ngScSourceWidget' );
			if ( $fromPortal && $fromPortal.length ) {
				return $fromPortal;
			}

			var $fromPanel = $portal.find( '.qodef-widget-side-area-cart-content' ).first().data( 'ngScSourceWidget' );
			if ( $fromPanel && $fromPanel.length ) {
				return $fromPanel;
			}
		}

		return $( '.widget_greenpath_core_woo_side_area_cart.qodef--opened' ).not( '.ng-farmley-sc-portal' ).first();
	}

	function closeSideCart( event ) {
		if ( event ) {
			event.preventDefault();
			event.stopPropagation();
		}

		if ( openAssistTimer ) {
			window.clearTimeout( openAssistTimer );
			openAssistTimer = null;
		}

		cartIsOpen = false;

		var $source = getSourceSideCartWidget();

		unportalSideCartPanel();

		if ( $source && $source.length ) {
			$source.removeClass( 'qodef--opened' );
		}

		$( '.widget_greenpath_core_woo_side_area_cart' ).removeClass( 'qodef--opened' );
		$( 'body' ).removeClass( 'qodef-woo-side-area-cart--opened' );

		if ( window.qodefCore && qodefCore.qodefScroll && typeof qodefCore.qodefScroll.enable === 'function' ) {
			try {
				qodefCore.qodefScroll.enable();
			} catch ( err ) {
				// Ignore.
			}
		}

		$( '#qodef-page-mobile-header, #qodef-page-header, .qodef-header-sticky' ).css( 'z-index', '' );
		unlockPageScroll();
	}

	function bindCloseHandlers() {
		if ( document.body._ngScCloseBound ) {
			return;
		}

		document.body._ngScCloseBound = true;

		function onCloseTrigger( event ) {
			var target = event.target;
			if ( ! target || ! target.closest ) {
				return;
			}

			var closeBtn = target.closest(
				'.qodef-widget-side-area-cart-content .qodef-m-close, .ng-farmley-sc-portal .qodef-m-close'
			);
			if ( ! closeBtn ) {
				return;
			}

			closeSideCart( event );
		}

		// Capture phase so theme handlers / overlays cannot swallow the X click.
		document.addEventListener( 'click', onCloseTrigger, true );

		$( document.body ).on( 'click.ngScCoverClose', '.qodef-woo-side-area-cart-cover', function ( event ) {
			if ( ! $( 'body' ).hasClass( 'qodef-woo-side-area-cart--opened' ) && ! $( '.ng-farmley-sc-portal' ).length ) {
				return;
			}
			closeSideCart( event );
		} );

		$( document ).on( 'keyup.ngScClose', function ( event ) {
			if ( event.keyCode === 27 && ( cartIsOpen || $( 'body' ).hasClass( 'qodef-woo-side-area-cart--opened' ) || $( '.ng-farmley-sc-portal' ).length ) ) {
				closeSideCart( event );
			}
		} );
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
		var $roots = $widgets().add( '.ng-farmley-sc-portal' );

		// Replace PS-marked lists so orphaned click-drag handlers cannot survive.
		$roots.find( '.qodef-woo-side-area-cart.ps' ).each( function () {
			var clean = this.cloneNode( true );
			clean.classList.remove( 'ps', 'ps--active-y', 'ps--active-x', 'ps--scrolling-y', 'ps--scrolling-x' );
			$( clean ).children( '.ps__rail-x, .ps__rail-y' ).remove();
			if ( this.parentNode ) {
				this.parentNode.replaceChild( clean, this );
			}
		} );

		$roots.find( '.ps, .qodef-woo-side-area-cart, .ng-farmley-sc-scroll' ).each( function () {
			var el = this;
			var instance = el._ps || el.perfectScrollbar || el.__perfectScrollbar || null;

			try {
				if ( instance && typeof instance.destroy === 'function' ) {
					instance.destroy();
				}
			} catch ( err ) {
				// Ignore teardown errors from stale instances.
			}

			delete el._ps;
			delete el.perfectScrollbar;
			delete el.__perfectScrollbar;

			el.classList.remove( 'ps', 'ps--active-y', 'ps--active-x', 'ps--scrolling-y', 'ps--scrolling-x' );
			if ( el.style ) {
				el.style.overflow = '';
				el.style.overflowX = '';
				el.style.overflowY = '';
			}
		} );

		$roots.find( '.ps__rail-x, .ps__rail-y, .ps__thumb-x, .ps__thumb-y' ).remove();
	}

	function unlockPageScroll() {
		$( 'html, body' ).css( {
			overflow: '',
			touchAction: '',
			position: '',
			height: '',
			top: '',
			width: ''
		} );

		$( '.qodef-woo-side-area-cart-cover' ).css( 'pointer-events', '' );

		if ( window.qodefCore && qodefCore.qodefScroll && typeof qodefCore.qodefScroll.enable === 'function' ) {
			try {
				qodefCore.qodefScroll.enable();
			} catch ( err ) {
				// Ignore.
			}
		}
	}

	function isCartCheckoutPage() {
		return document.body.classList.contains( 'woocommerce-cart' )
			|| document.body.classList.contains( 'woocommerce-checkout' );
	}

	function patchCartPageScrollLock() {
		if ( ! isCartCheckoutPage() || ! window.qodefCore || ! qodefCore.qodefScroll ) {
			return;
		}

		if ( qodefCore.qodefScroll._ngCartScrollPatched ) {
			return;
		}

		var originalDisable = qodefCore.qodefScroll.disable;

		qodefCore.qodefScroll.disable = function () {
			if ( isCartCheckoutPage() ) {
				purgeCartPageWheelBlockers();
				return;
			}

			return originalDisable.apply( this, arguments );
		};

		qodefCore.qodefScroll._ngCartScrollPatched = true;
	}

	function purgeCartPageWheelBlockers() {
		if ( ! isCartCheckoutPage() ) {
			return;
		}

		document.documentElement.classList.add( 'ng-farmley-cart-scroll' );

		var i;

		if ( window.qodefCore && qodefCore.qodefScroll && typeof qodefCore.qodefScroll.enable === 'function' ) {
			for ( i = 0; i < 10; i++ ) {
				try {
					qodefCore.qodefScroll.enable();
				} catch ( err ) {
					// Ignore.
				}
			}
		}

		$( 'html, body' ).css( {
			overflow: '',
			overflowY: '',
			touchAction: '',
			position: '',
			height: '',
			top: '',
			width: ''
		} );

		$( '.qodef-woo-side-area-cart-cover, .qodef-woo-side-area-menu-cover' ).css( {
			pointerEvents: 'none',
			zIndex: -1
		} );

		if ( ! $( 'body' ).hasClass( 'qodef-woo-side-area-cart--opened' ) ) {
			$( '.qodef-woo-side-area-cart-cover' ).css( {
				opacity: 0,
				visibility: 'hidden'
			} );
		}

		if ( ! $( 'body' ).hasClass( 'qodef-woo-side-area-menu--opened' ) ) {
			$( '.qodef-woo-side-area-menu-cover' ).css( {
				opacity: 0,
				visibility: 'hidden'
			} );
		}
	}

	function ensureCartCheckoutScrollUnlock() {
		if ( ! isCartCheckoutPage() ) {
			return;
		}

		patchCartPageScrollLock();
		purgeCartPageWheelBlockers();
		unlockPageScroll();
	}

	function ensureCartCheckoutScroll() {
		if ( ! isCartCheckoutPage() ) {
			return;
		}

		$( 'body' ).removeClass( 'qodef-woo-side-area-cart--opened qqvfw-quick-view--opened ng-farmley-qv-open' );
		getQuickViewDrawer().removeClass( 'qqvfw--opened ng-farmley-qv--ready ng-farmley-qv--enhanced ng-farmley-qv--closing' );
		$widgets().removeClass( 'qodef--opened' );
		unportalSideCartPanel();
		cartIsOpen = false;

		ensureCartCheckoutScrollUnlock();
	}

	function getQuickViewDrawer() {
		return $( '#qode-quick-view-for-woocommerce-pop-up.ng-farmley-qv-drawer, #qode-quick-view-for-woocommerce-pop-up' ).first();
	}

	function unblockCartPageUi() {
		if ( ! document.body.classList.contains( 'woocommerce-cart' ) ) {
			return;
		}

		$( '.woocommerce-cart-form.processing, div.cart_totals.processing' ).each( function () {
			var $el = $( this );

			try {
				if ( $el.data( 'blockUI.isBlocked' ) ) {
					$el.removeClass( 'processing' ).unblock();
					return;
				}
			} catch ( err ) {
				// Ignore blockUI teardown errors.
			}

			$el.removeClass( 'processing' );
		} );
	}

	function isFarmleyCartPage() {
		if ( window.ngFarmleySideCart && ngFarmleySideCart.isCartPage ) {
			return true;
		}

		if ( document.body.classList.contains( 'woocommerce-cart' ) ) {
			return true;
		}

		if ( document.querySelector( '.woocommerce-cart-form' ) ) {
			return true;
		}

		var path = ( window.location.pathname || '' ).toLowerCase();

		return path.indexOf( 'cart-2' ) !== -1 || /(?:^|\/)cart(?:\/|$)/.test( path );
	}

	function farmleyCartAjaxNonce() {
		if ( window.ngFarmleySideCart && ngFarmleySideCart.nonce ) {
			return ngFarmleySideCart.nonce;
		}

		if ( window.ngFarmleyCart && ngFarmleyCart.nonce ) {
			return ngFarmleyCart.nonce;
		}

		return '';
	}

	function farmleyWcAjaxUrl( endpoint ) {
		var template = '';

		if ( typeof wc_cart_params !== 'undefined' && wc_cart_params.wc_ajax_url ) {
			template = wc_cart_params.wc_ajax_url;
		} else if ( typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.wc_ajax_url ) {
			template = wc_add_to_cart_params.wc_ajax_url;
		} else if ( window.ngFarmleySideCart && ngFarmleySideCart.wcAjaxUrl ) {
			template = ngFarmleySideCart.wcAjaxUrl;
		} else if ( window.ngFarmleyCart && ngFarmleyCart.wcAjaxUrl ) {
			template = ngFarmleyCart.wcAjaxUrl;
		}

		if ( template ) {
			return template.toString().replace( '%%endpoint%%', endpoint );
		}

		return ( window.location.origin || '' ) + '/?wc-ajax=' + endpoint;
	}

	var farmleyCartPageRefreshTimer = null;
	var farmleyCartPageRefreshBusy = false;
	var farmleyCartPageSyncDirty = false;
	var farmleyCartPageInitialSyncDone = false;

	function markFarmleyCartPageDirty() {
		if ( ! isFarmleyCartPage() ) {
			return;
		}

		farmleyCartPageSyncDirty = true;
		scheduleFarmleyCartPageRefresh( 200 );
	}

	function applyFarmleyCartPageShell( shellHtml ) {
		if ( ! shellHtml ) {
			return false;
		}

		var $parsed = $( '<div>' ).append( $.parseHTML( shellHtml, document, true ) );
		var $newWooPage = $parsed.find( '#qodef-woo-page' ).first();
		var $newShell = $parsed.find( '.ng-farmley-cart-shell' ).first();
		var $wooPage = $( '#qodef-woo-page' ).first();
		var $shell = $( '.ng-farmley-cart-shell' ).first();

		if ( $newWooPage.length && $wooPage.length ) {
			$wooPage.replaceWith( $newWooPage );
			return true;
		}

		if ( $newShell.length && $wooPage.length ) {
			$wooPage.empty().append( $newShell );
			return true;
		}

		if ( $newShell.length && $shell.length ) {
			$shell.replaceWith( $newShell );
			return true;
		}

		if ( $newShell.length ) {
			var $form = $( '.woocommerce-cart-form' ).first();
			if ( $form.length ) {
				$form.closest( '.woocommerce, #qodef-woo-page' ).first().empty().append( $newShell );
				return true;
			}
		}

		var $empty = $parsed.find( '.wc-empty-cart-message' ).closest( '.woocommerce' ).first();
		if ( ! $empty.length ) {
			$empty = $parsed.filter( '.woocommerce' ).first();
		}
		if ( ! $empty.length ) {
			$empty = $parsed.first();
		}

		if ( $wooPage.length && $empty.length ) {
			$wooPage.empty().append( $empty );
			return true;
		}

		if ( $shell.length && $empty.length ) {
			$shell.replaceWith( $empty );
			return true;
		}

		return false;
	}

	function refreshFarmleyCartPageNow() {
		if ( ! isFarmleyCartPage() ) {
			return;
		}

		if ( farmleyCartPageRefreshBusy ) {
			// Another refresh is running; mark dirty so it re-runs when done.
			farmleyCartPageSyncDirty = true;
			return;
		}

		var nonce = farmleyCartAjaxNonce();
		var url = farmleyWcAjaxUrl( 'ng_farmley_cart_page_fragments' );

		if ( ! nonce || ! url ) {
			return;
		}

		farmleyCartPageRefreshBusy = true;
		unblockCartPageUi();

		$( '.woocommerce-cart-form, div.cart_totals' ).addClass( 'ng-farmley-cart-updating processing' );

		$.ajax( {
			type: 'POST',
			url: url,
			data: { security: nonce },
			dataType: 'json',
		} ).done( function ( response ) {
			if ( ! response || ! response.success || ! response.data ) {
				return;
			}

			if ( ! applyFarmleyCartPageShell( response.data.shell_html || '' ) ) {
				return;
			}

			farmleyCartPageSyncDirty = false;
			farmleyCartPageInitialSyncDone = true;

			if ( typeof qodefWooQuantityButtons !== 'undefined' && qodefWooQuantityButtons.init ) {
				qodefWooQuantityButtons.init();
			}
		} ).always( function () {
			farmleyCartPageRefreshBusy = false;
			$( '.woocommerce-cart-form, div.cart_totals' ).removeClass( 'ng-farmley-cart-updating processing' );
			unblockCartPageUi();
			// If another update arrived while we were busy, re-run now.
			if ( farmleyCartPageSyncDirty ) {
				scheduleFarmleyCartPageRefresh( 100 );
			}
		} );
	}

	function scheduleFarmleyCartPageRefresh( delay, force ) {
		if ( ! isFarmleyCartPage() ) {
			return;
		}

		if ( ! force && farmleyCartPageInitialSyncDone && ! farmleyCartPageSyncDirty ) {
			return;
		}

		clearTimeout( farmleyCartPageRefreshTimer );
		farmleyCartPageRefreshTimer = window.setTimeout( refreshFarmleyCartPageNow, delay || 150 );
	}

	function refreshCartPageAfterSideCartChange() {
		markFarmleyCartPageDirty();
	}

	function cleanupDuplicates() {
		var $footer = $panel().find( '.ng-farmley-sc-footer' ).first();
		var $content = $panel().find( '.qodef-widget-side-area-cart-content' ).first();

		$panel().find( '.ng-farmley-sc-reco' ).not( $footer.find( '.ng-farmley-sc-reco' ) ).remove();
		$panel().find( '.ng-farmley-sc-reco-slot' ).not( $footer.find( '.ng-farmley-sc-reco-slot' ) ).remove();
		$panel().find( '.ng-farmley-sc-coupon' ).not( $footer.find( '.ng-farmley-sc-coupon' ) ).remove();
		$panel().find( '.ng-farmley-sc-coupon-slot' ).not( $footer.find( '.ng-farmley-sc-coupon-slot' ) ).remove();
		$panel().find( '.ng-farmley-sc-items-scroll' ).remove();

		// Orphaned reco Add buttons that leaked into the item list.
		$panel().find( '.ng-farmley-sc-cart-slot .ng-farmley-sc-reco__add, .qodef-woo-side-area-cart > .ng-farmley-sc-reco__add' ).remove();

		if ( $content.length ) {
			var $progress = $content.children( '.ng-farmley-sc-progress' );
			if ( $progress.length > 1 ) {
				$progress.slice( 1 ).remove();
			}
		}

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

	function ensureFooter( $content ) {
		var $p = $content && $content.length ? $content : $panel();
		if ( ! $p.length ) {
			return $();
		}

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

		// Keep checkout buttons outside the footer so they can pin below the scroll area.
		var $action = $p.find( '.qodef-m-action' ).first();
		if ( $action.length && $.contains( $footer[0], $action[0] ) ) {
			$action.detach().insertAfter( $footer );
		} else if ( $action.length && ! $.contains( $p[0], $action[0] ) ) {
			$action.detach().appendTo( $p );
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
					refreshCartPageAfterSideCartChange();
				} else {
					$btn.removeClass( 'ng-farmley-sc-removing' );
					$item.removeClass( 'ng-farmley-sc-item--removing' );
				}
			} ).fail( function () {
				$btn.removeClass( 'ng-farmley-sc-removing' );
				$item.removeClass( 'ng-farmley-sc-item--removing' );
			} ).always( function () {
				setUpdating( false );
				ensureCartCheckoutScrollUnlock();
				unblockCartPageUi();
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
			$val.text( next );
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
					finishCartUpdate( response.data, { animateProgress: false } );
					refreshCartPageAfterSideCartChange();
				} else {
					$val.text( current );
					$wrap.removeClass( 'ng-farmley-sc-qty--busy' );
					$item.removeClass( 'ng-farmley-sc-item--updating' );
				}
			} ).fail( function () {
				$val.text( current );
				$wrap.removeClass( 'ng-farmley-sc-qty--busy' );
				$item.removeClass( 'ng-farmley-sc-item--updating' );
			} ).always( function () {
				setUpdating( false );
			} );
		} );
	}

	function ensureScrollWrap() {
		$widgets().each( function () {
			var $p = $( this ).find( '.qodef-widget-side-area-cart-content' ).first();
			if ( ! $p.length ) {
				return;
			}

			ensureFooter( $p );

			var $scroll = $p.children( '.ng-farmley-sc-scroll' ).first();
			if ( ! $scroll.length ) {
				$scroll = $( '<div class="ng-farmley-sc-scroll" tabindex="-1"></div>' );
			}

			// Only pin header + checkout. Progress, items, coupon, reco stay inside scroll
			// so the item list never collapses to 0 height under a tall footer.
			$scroll.find( '.qodef-side-area-cart-top, .qodef-m-action' ).each( function () {
				$( this ).detach().appendTo( $p );
			} );

			$p.children()
				.not( '.qodef-side-area-cart-top, .qodef-m-action, .ng-farmley-sc-burst, .ng-farmley-sc-scroll' )
				.appendTo( $scroll );

			if ( ! $scroll.parent().is( $p ) ) {
				$p.append( $scroll );
			}

			var $top = $p.children( '.qodef-side-area-cart-top' ).first();
			var $action = $p.children( '.qodef-m-action' ).first();
			var $burst = $p.children( '.ng-farmley-sc-burst' ).first();

			if ( $burst.length ) {
				$burst.prependTo( $p );
			}
			if ( $top.length ) {
				$top.insertBefore( $scroll );
			}
			if ( $action.length ) {
				$action.insertAfter( $scroll );
			}

			attachScrollHandlers( $scroll[0] );

			if ( ! $p[0]._ngPanelWheelBound ) {
				$p[0]._ngPanelWheelBound = true;

				$p[0].addEventListener(
					'wheel',
					function ( event ) {
						if ( ! isSideCartOpen() || event.target.closest( '.ng-farmley-sc-scroll' ) ) {
							return;
						}

						applyWheelToScrollEl( $scroll[0], event );
					},
					{ passive: false }
				);
			}
		} );
	}

	function attachScrollHandlers( scrollEl ) {
		if ( ! scrollEl ) {
			return;
		}

		scrollEl.style.webkitOverflowScrolling = 'touch';
		scrollEl.style.overflowY = 'scroll';
		scrollEl.style.touchAction = 'pan-y';
		scrollEl.style.flex = '1 1 0%';
		scrollEl.style.minHeight = '140px';
		scrollEl.style.height = '0';
		scrollEl.style.maxHeight = 'none';
		scrollEl.style.pointerEvents = 'auto';
		scrollEl.style.overscrollBehaviorY = 'contain';

		// Desktop: theme qodefScroll.disable() blocks native wheel — scroll manually inside drawer.
		if ( ! scrollEl._ngWheelScrollBound ) {
			scrollEl._ngWheelScrollBound = true;

			scrollEl.addEventListener(
				'wheel',
				function ( event ) {
					if ( ! isSideCartOpen() ) {
						return;
					}

					applyWheelToScrollEl( scrollEl, event );
				},
				{ passive: false }
			);
		}

		// Manual touch scroll only on phones — on desktop it fights the native scrollbar drag.
		var coarsePointer = window.matchMedia && window.matchMedia( '(pointer: coarse)' ).matches;
		if ( ! coarsePointer || scrollEl._ngTouchScrollBound ) {
			return;
		}

		scrollEl._ngTouchScrollBound = true;
		var startY = 0;
		var startScroll = 0;

		scrollEl.addEventListener( 'touchstart', function ( event ) {
			if ( ! event.touches || ! event.touches.length ) {
				return;
			}
			startY = event.touches[0].clientY;
			startScroll = scrollEl.scrollTop;
		}, { passive: true } );

		scrollEl.addEventListener( 'touchmove', function ( event ) {
			if ( ! event.touches || ! event.touches.length ) {
				return;
			}

			var maxScroll = scrollEl.scrollHeight - scrollEl.clientHeight;
			if ( maxScroll <= 0 ) {
				return;
			}

			var dy = startY - event.touches[0].clientY;
			var next = Math.max( 0, Math.min( maxScroll, startScroll + dy ) );
			scrollEl.scrollTop = next;

			if ( event.cancelable ) {
				event.preventDefault();
			}
		}, { passive: false } );
	}

	function isSideCartScrollTarget( el ) {
		if ( ! el || ! el.closest ) {
			return false;
		}
		return !! el.closest( '.widget_greenpath_core_woo_side_area_cart, .ng-farmley-sc-portal' );
	}

	function isSideCartOpen() {
		return cartIsOpen
			|| document.body.classList.contains( 'qodef-woo-side-area-cart--opened' )
			|| $( '.ng-farmley-sc-portal' ).length > 0;
	}

	function applyWheelToScrollEl( scrollEl, event ) {
		if ( ! scrollEl || ! event ) {
			return false;
		}

		var delta = event.deltaY || 0;

		if ( ! delta ) {
			return false;
		}

		var maxScroll = scrollEl.scrollHeight - scrollEl.clientHeight;

		if ( maxScroll <= 0 ) {
			return false;
		}

		var atTop = scrollEl.scrollTop <= 0;
		var atBottom = scrollEl.scrollTop >= maxScroll - 1;

		if ( ( delta < 0 && atTop ) || ( delta > 0 && atBottom ) ) {
			return false;
		}

		scrollEl.scrollTop += delta;

		if ( event.cancelable ) {
			event.preventDefault();
		}

		event.stopPropagation();

		return true;
	}

	function patchSideCartWheelScroll() {
		if ( ! window.qodefCore || ! qodefCore.qodefScroll || qodefCore.qodefScroll._ngScWheelPatched ) {
			return;
		}

		var originalPrevent = qodefCore.qodefScroll.preventDefaultValue;

		qodefCore.qodefScroll.preventDefaultValue = function ( event ) {
			if ( isSideCartOpen() && isSideCartScrollTarget( event.target ) ) {
				return;
			}

			return originalPrevent.call( this, event );
		};

		qodefCore.qodefScroll._ngScWheelPatched = true;
	}

	function disableSideCartPerfectScrollbar() {
		destroyPerfectScrollbar();

		// Block PerfectScrollbar before theme can attach click-drag handlers again.
		if ( window.PerfectScrollbar && ! window.PerfectScrollbar._ngFarmleyPatched ) {
			var OriginalPS = window.PerfectScrollbar;
			function PatchedPerfectScrollbar( element, options ) {
				if ( isSideCartScrollTarget( element ) ) {
					return {
						update: function () {},
						destroy: function () {},
						element: element
					};
				}
				return new OriginalPS( element, options );
			}
			PatchedPerfectScrollbar.prototype = OriginalPS.prototype;
			Object.keys( OriginalPS ).forEach( function ( key ) {
				try {
					PatchedPerfectScrollbar[ key ] = OriginalPS[ key ];
				} catch ( err ) {
					// Ignore non-configurable props.
				}
			} );
			PatchedPerfectScrollbar._ngFarmleyPatched = true;
			PatchedPerfectScrollbar._ngFarmleyOriginal = OriginalPS;
			window.PerfectScrollbar = PatchedPerfectScrollbar;
		}

		if ( window.qodefCore && qodefCore.qodefPerfectScrollbar && ! qodefCore.qodefPerfectScrollbar._ngFarmleyPatched ) {
			var originalInit = qodefCore.qodefPerfectScrollbar.init;
			qodefCore.qodefPerfectScrollbar.init = function ( $el ) {
				var node = $el && $el[0] ? $el[0] : $el;
				if ( isSideCartScrollTarget( node ) ) {
					return;
				}
				if ( typeof originalInit === 'function' ) {
					return originalInit.apply( this, arguments );
				}
			};
			qodefCore.qodefPerfectScrollbar._ngFarmleyPatched = true;
		}
	}

	// Patch as early as the file loads so theme document.ready cannot install PS first.
	disableSideCartPerfectScrollbar();
	patchSideCartWheelScroll();

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

		if ( cartIsOpen ) {
			portalSideCartPanel();
		}

		var $p = $panel();

		if ( ! $p.length ) {
			return;
		}

		normalizeCartMarkup( $p );
		$widgets().addClass( 'ng-farmley-sc-ready' );
		$( '.ng-farmley-sc-portal' ).addClass( 'ng-farmley-sc-ready' );

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
		disableSideCartPerfectScrollbar();
		initRecoCarousels( options );
		ensureThumbnailsVisible( $p );
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

	function ensureThumbnailsVisible( $scope ) {
		var $root = $scope && $scope.length ? $scope : $panel();
		if ( ! $root.length ) {
			return;
		}

		$root.find( '.ng-farmley-sc-item__image img, .qodef-e-image.ng-farmley-sc-item__image img' ).each( function () {
			var img = this;
			var $img = $( img );
			var lazySrc = $img.attr( 'data-src' ) || $img.attr( 'data-lazy-src' ) || $img.attr( 'data-src-img' );

			if ( lazySrc && ( ! img.getAttribute( 'src' ) || /placeholder|data:image\/svg|lazy/i.test( String( img.getAttribute( 'src' ) || '' ) ) ) ) {
				img.setAttribute( 'src', lazySrc );
			}

			if ( $img.attr( 'data-srcset' ) && ! $img.attr( 'srcset' ) ) {
				$img.attr( 'srcset', $img.attr( 'data-srcset' ) );
			}

			img.setAttribute( 'loading', 'eager' );
			img.removeAttribute( 'data-lazy-src' );
			$img.removeClass( 'lazyload lazyloading lazyloaded' ).css( {
				opacity: 1,
				visibility: 'visible',
				display: 'block'
			} );
		} );
	}

	function onCartOpened() {
		if ( cartIsOpen ) {
			return;
		}

		cartIsOpen = true;

		patchSideCartWheelScroll();
		$( 'html, body' ).css( { touchAction: '', overscrollBehavior: '' } );

		if ( openAssistTimer ) {
			window.clearTimeout( openAssistTimer );
			openAssistTimer = null;
		}

		// Move drawer onto body so the full-screen cover cannot steal mobile touches.
		portalSideCartPanel();
		buildLayout();
		ensureThumbnailsVisible();
		disableSideCartPerfectScrollbar();

		var target = readProgressPercent();
		lastProgressPercent = 0;
		animateProgressFromTo( 0, target );
		syncUnlockedMilestones( false );

		openAssistTimer = window.setTimeout( function () {
			openAssistTimer = null;
			if ( ! cartIsOpen || ! $( 'body' ).hasClass( 'qodef-woo-side-area-cart--opened' ) ) {
				return;
			}
			portalSideCartPanel();
			disableSideCartPerfectScrollbar();
			ensureScrollWrap();
			ensureThumbnailsVisible();
			paperBurst();
		}, 150 );
	}

	function onCartClosed() {
		if ( openAssistTimer ) {
			window.clearTimeout( openAssistTimer );
			openAssistTimer = null;
		}

		if ( ! cartIsOpen && ! $( '.ng-farmley-sc-portal' ).length ) {
			return;
		}

		cartIsOpen = false;
		unportalSideCartPanel();
		$( '#qodef-page-mobile-header, #qodef-page-header, .qodef-header-sticky' ).css( 'z-index', '' );
		unlockPageScroll();
	}

	function watchOpen() {
		$widgets().not( '.ng-farmley-sc-portal' ).each( function () {
			var el = this;
			if ( el._ngFarmleyOpenWatch ) {
				return;
			}
			el._ngFarmleyOpenWatch = true;

			var obs = new MutationObserver( function () {
				if ( $( el ).hasClass( 'qodef--opened' ) ) {
					onCartOpened();
				} else if ( ! $widgets().not( '.ng-farmley-sc-portal' ).filter( '.qodef--opened' ).length ) {
					onCartClosed();
				}
			} );

			obs.observe( el, { attributes: true, attributeFilter: [ 'class' ] } );
		} );

		if ( $widgets().not( '.ng-farmley-sc-portal' ).filter( '.qodef--opened' ).length ) {
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
		bindCloseHandlers();
		disableSideCartPerfectScrollbar();
		buildLayout();
		setProgressInstant( readProgressPercent() );
		syncUnlockedMilestones( false );
		watchOpen();

		if ( isFarmleyCartPage() && ! farmleyCartPageInitialSyncDone ) {
			scheduleFarmleyCartPageRefresh( 500, true );
		}

		ensureCartCheckoutScroll();

		if ( isCartCheckoutPage() ) {
			patchCartPageScrollLock();
			purgeCartPageWheelBlockers();

			$( window ).on( 'load.ngScCartScroll', function () {
				ensureCartCheckoutScroll();
				purgeCartPageWheelBlockers();
			} );

			$( document.body ).on(
				'wc_fragments_refreshed.ngScCartScroll wc_fragments_loaded.ngScCartScroll updated_cart_totals.ngScCartScroll item_removed_from_classic_cart.ngScCartScroll',
				ensureCartCheckoutScrollUnlock
			);

			$( document.body ).on( 'item_removed_from_classic_cart.ngScCartUnblock updated_cart_totals.ngScCartUnblock wc_cart_emptied.ngScCartUnblock', unblockCartPageUi );

			$( document.body ).on( 'click.ngScCartPageRemove', '.woocommerce-cart-form .product-remove > a', function () {
				var safetyTimer = window.setTimeout( unblockCartPageUi, 15000 );

				$( document.body ).one( 'item_removed_from_classic_cart.ngScCartPageRemove updated_cart_totals.ngScCartPageRemove wc_cart_emptied.ngScCartPageRemove', function () {
					window.clearTimeout( safetyTimer );
					unblockCartPageUi();
					ensureCartCheckoutScrollUnlock();
				} );
			} );

			$( document ).on( 'click.ngScCartWheelFix mouseenter.ngScCartWheelFix', function () {
				purgeCartPageWheelBlockers();
			} );

			var wheelFixRuns = 0;
			var wheelFixTimer = window.setInterval( function () {
				purgeCartPageWheelBlockers();
				wheelFixRuns += 1;

				if ( wheelFixRuns >= 12 ) {
					window.clearInterval( wheelFixTimer );
				}
			}, 500 );

			window.setInterval( unblockCartPageUi, 10000 );
		}
	} );

	$( document.body ).on( 'wc_fragments_refreshed wc_fragments_loaded', function () {
		if ( isCartCheckoutPage() ) {
			ensureCartCheckoutScrollUnlock();
			return;
		}

		finishCartUpdate( {}, { animateProgress: true, skipFragments: true } );
	} );

	$( document.body ).on( 'added_to_cart removed_from_cart', function () {
		if ( isCartCheckoutPage() ) {
			ensureCartCheckoutScrollUnlock();
			if ( isFarmleyCartPage() ) {
				markFarmleyCartPageDirty();
			}
			return;
		}

		finishCartUpdate( {}, { animateProgress: true, skipFragments: true } );
	} );

	$( window ).on( 'pageshow.ngFarmleyCartSync', function ( event ) {
		if ( isFarmleyCartPage() && event.originalEvent && event.originalEvent.persisted ) {
			farmleyCartPageInitialSyncDone = false;
			scheduleFarmleyCartPageRefresh( 0, true );
		}
	} );

	window.ngFarmleyRefreshCartPage = refreshFarmleyCartPageNow;
}( jQuery ) );