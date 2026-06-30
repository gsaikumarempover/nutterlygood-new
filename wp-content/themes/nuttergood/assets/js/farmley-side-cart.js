/**
 * Side cart — whole-sidebar scroll, progress animates on cart updates.
 */
( function ( $ ) {
	'use strict';

	var PAPER_COLORS = [ '#0c533d', '#88a842', '#b99531', '#fcf4eb', '#f4a7b9', '#e8f3ee' ];
	var cartIsOpen = false;
	var burstDoneThisOpen = false;
	var lastProgressPercent = 0;
	var unlockedMilestones = {};

	function $widget() {
		return $( '.widget_greenpath_core_woo_side_area_cart' ).first();
	}

	function $panel() {
		return $widget().find( '.qodef-widget-side-area-cart-content' ).first();
	}

	function reducedMotion() {
		return window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function destroyPerfectScrollbar() {
		var $items = $panel().find( '.qodef-woo-side-area-cart' );

		$items.each( function () {
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

		var $directReco = $footer.children( '.ng-farmley-sc-reco' ).first();
		if ( $directReco.length ) {
			$directReco.appendTo( $recoSlot );
		}

		var $orderDetails = $p.find( '.qodef-m-order-details' ).first();
		if ( $orderDetails.length && ! $.contains( $footer[0], $orderDetails[0] ) ) {
			$orderDetails.detach().insertAfter( $recoSlot );
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

		if ( window.ngFarmleySideCart ) {
			if ( ( force || ! $couponSlot.children().length ) && ngFarmleySideCart.couponHtml ) {
				$couponSlot.html( ngFarmleySideCart.couponHtml );
			}
			if ( ( force || ! $recoSlot.find( '.ng-farmley-sc-reco' ).length ) && ngFarmleySideCart.recoHtml ) {
				$recoSlot.html( ngFarmleySideCart.recoHtml );
			}
		}
	}

	function applyCartFragments( fragments ) {
		if ( ! fragments ) {
			return;
		}

		$.each( fragments, function ( selector, html ) {
			var $target = $( selector );
			if ( $target.length ) {
				$target.replaceWith( html );
			}
		} );
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
				ngFarmleySideCart.couponHtml = response.data.couponHtml || '';
				ngFarmleySideCart.recoHtml = response.data.recoHtml || '';
				ngFarmleySideCart.percent = response.data.percent || 0;
			}
		} ).always( function () {
			if ( callback ) {
				callback();
			}
		} );
	}

	function bindCouponApply() {
		var widget = $widget()[0];
		if ( ! widget || widget._ngCouponBound ) {
			return;
		}

		widget._ngCouponBound = true;

		$( widget ).on( 'click.ngScCoupon', '[data-ng-sc-apply-coupon]', function ( event ) {
			event.preventDefault();

			var $btn = $( this );
			if ( $btn.prop( 'disabled' ) || $btn.hasClass( 'ng-farmley-sc-coupon__apply--busy' ) ) {
				return;
			}

			if ( typeof wc_add_to_cart_params === 'undefined' || ! window.ngFarmleySideCart ) {
				return;
			}

			var code = $btn.attr( 'data-coupon-code' ) || ngFarmleySideCart.couponCode || 'SAVER8';
			var $feedback = $btn.closest( '.ng-farmley-sc-coupon' ).find( '[data-ng-sc-coupon-feedback]' ).first();
			var applying = ngFarmleySideCart.i18n && ngFarmleySideCart.i18n.couponApplying ? ngFarmleySideCart.i18n.couponApplying : 'Applying…';

			$btn.addClass( 'ng-farmley-sc-coupon__apply--busy' ).prop( 'disabled', true ).text( applying );
			$feedback.removeClass( 'is-error is-success' ).text( '' );

			$.post(
				wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'ng_farmley_side_cart_apply_coupon' ),
				{
					security: ngFarmleySideCart.nonce,
					coupon_code: code,
				}
			).done( function ( response ) {
				if ( response && response.success && response.data ) {
					ngFarmleySideCart.couponHtml = response.data.couponHtml || ngFarmleySideCart.couponHtml;
					ngFarmleySideCart.recoHtml = response.data.recoHtml || ngFarmleySideCart.recoHtml;
					ngFarmleySideCart.percent = response.data.percent || ngFarmleySideCart.percent;
					applyCartFragments( response.data.fragments );
					buildLayout();
					fillFooterContent( true );
					updateProgress( true );
					$feedback.addClass( 'is-success' ).text( response.data.message || '' );
				} else {
					var msg = response && response.data && response.data.message ? response.data.message : ( ngFarmleySideCart.i18n ? ngFarmleySideCart.i18n.couponFailed : 'Could not apply coupon.' );
					$feedback.addClass( 'is-error' ).text( msg );
					$btn.removeClass( 'ng-farmley-sc-coupon__apply--busy' ).prop( 'disabled', false ).text( 'Apply' );
				}
			} ).fail( function () {
				$feedback.addClass( 'is-error' ).text( ngFarmleySideCart.i18n ? ngFarmleySideCart.i18n.couponFailed : 'Could not apply coupon.' );
				$btn.removeClass( 'ng-farmley-sc-coupon__apply--busy' ).prop( 'disabled', false ).text( 'Apply' );
			} );
		} );
	}

	function bindRemoveItem() {
		var widget = $widget()[0];
		if ( ! widget || widget._ngRemoveBound ) {
			return;
		}

		widget._ngRemoveBound = true;

		$( widget ).on( 'click.ngScRemove', '.qodef-woo-side-area-cart .remove_from_cart_button', function ( event ) {
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

			$.ajax( {
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'remove_from_cart' ),
				data: { cart_item_key: cartKey },
				dataType: 'json',
			} ).done( function ( response ) {
				if ( response && response.fragments ) {
					applyCartFragments( response.fragments );
					$( document.body ).trigger( 'removed_from_cart', [ response.fragments, response.cart_hash, $btn ] );
					syncSideCartMeta( function () {
						buildLayout();
						fillFooterContent( true );
						updateProgress( true );
					} );
				} else {
					$btn.removeClass( 'ng-farmley-sc-removing' );
					$item.removeClass( 'ng-farmley-sc-item--removing' );
				}
			} ).fail( function () {
				$btn.removeClass( 'ng-farmley-sc-removing' );
				$item.removeClass( 'ng-farmley-sc-item--removing' );
			} );
		} );
	}

	function bindQtyStepper() {
		var widget = $widget()[0];
		if ( ! widget || widget._ngQtyBound ) {
			return;
		}

		widget._ngQtyBound = true;

		$( widget ).on( 'click.ngScQty', '[data-ng-sc-qty] [data-action]', function ( event ) {
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

			$.post(
				wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'ng_farmley_side_cart_update_qty' ),
				{
					security: ngFarmleySideCart.nonce,
					cart_item_key: cartKey,
					quantity: next,
				}
			).done( function ( response ) {
				if ( response && response.success && response.data ) {
					ngFarmleySideCart.couponHtml = response.data.couponHtml || ngFarmleySideCart.couponHtml;
					ngFarmleySideCart.recoHtml = response.data.recoHtml || ngFarmleySideCart.recoHtml;
					ngFarmleySideCart.percent = response.data.percent || ngFarmleySideCart.percent;
					applyCartFragments( response.data.fragments );
					$( document.body ).trigger( 'updated_wc_div' );
					syncSideCartMeta( function () {
						buildLayout();
						fillFooterContent( true );
						updateProgress( true );
						updateHeading();
					} );
				} else {
					$wrap.removeClass( 'ng-farmley-sc-qty--busy' );
					$item.removeClass( 'ng-farmley-sc-item--updating' );
				}
			} ).fail( function () {
				$wrap.removeClass( 'ng-farmley-sc-qty--busy' );
				$item.removeClass( 'ng-farmley-sc-item--updating' );
			} );
		} );
	}

	function ensureScrollWrap() {
		var $p = $panel();
		if ( ! $p.length ) {
			return;
		}

		var $scroll = $p.children( '.ng-farmley-sc-scroll' ).first();

		if ( ! $scroll.length ) {
			$scroll = $( '<div class="ng-farmley-sc-scroll" tabindex="-1"></div>' );
			$p.children().not( '.ng-farmley-sc-burst' ).appendTo( $scroll );
			$p.append( $scroll );
		} else {
			$p.children().not( '.ng-farmley-sc-burst, .ng-farmley-sc-scroll' ).appendTo( $scroll );
		}

		attachScrollWheel( $scroll[0] );
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

	function buildLayout() {
		var $p = $panel();
		if ( ! $p.length ) {
			return;
		}

		$widget().addClass( 'ng-farmley-sc-ready' );
		cleanupDuplicates();
		ensureFooter();
		fillFooterContent();
		ensureScrollWrap();
		updateHeading();
		destroyPerfectScrollbar();
		initRecoCarousels();
	}

	function initRecoCarousels() {
		$panel().find( '.ng-farmley-sc-reco' ).each( function () {
			var $reco = $( this );
			var $row = $reco.find( '.ng-farmley-sc-reco__row' ).first();
			var $prev = $reco.find( '.ng-farmley-sc-reco__prev' ).first();
			var $next = $reco.find( '.ng-farmley-sc-reco__next' ).first();

			if ( ! $row.length || ! $prev.length || ! $next.length ) {
				return;
			}

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

			function refreshNavSoon() {
				window.setTimeout( updateNav, 80 );
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
			$reco.find( 'img' ).off( 'load.ngRecoCarousel error.ngRecoCarousel' ).on( 'load.ngRecoCarousel error.ngRecoCarousel', refreshNavSoon );
			$( window ).off( 'resize.ngRecoCarousel' ).on( 'resize.ngRecoCarousel', refreshNavSoon );
			refreshNavSoon();
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
		if ( burstDoneThisOpen || reducedMotion() || typeof gsap === 'undefined' ) {
			return;
		}
		if ( ! $widget().hasClass( 'qodef--opened' ) ) {
			return;
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

		for ( var i = 0; i < 12; i++ ) {
			( function ( idx ) {
				var isFlower = idx % 5 === 0;
				var $el = $( '<span class="ng-farmley-sc-burst__piece"></span>' );
				var rot = Math.random() * 360;
				var x0 = cx + ( Math.random() - 0.5 ) * w * 0.4;
				var drift = ( Math.random() - 0.5 ) * w * 0.18;
				var fall = 30 + Math.random() * 100;

				if ( isFlower ) {
					$el.addClass( 'ng-farmley-sc-burst__piece--flower' ).text( '🌸' ).css( 'font-size', 9 + Math.random() * 4 + 'px' );
				} else {
					$el.addClass( 'ng-farmley-sc-burst__piece--paper' ).css( {
						width: 5 + Math.random() * 5 + 'px',
						height: 7 + Math.random() * 7 + 'px',
						background: PAPER_COLORS[ idx % PAPER_COLORS.length ],
						borderRadius: '50% 50% 50% 0',
					} );
				}

				$el.css( { left: x0 + 'px', top: '6px', transform: 'rotate(' + rot + 'deg)' } );
				$layer.append( $el );

				gsap.fromTo(
					$el[0],
					{ opacity: 0, y: 0, x: 0, scale: 0.5, rotation: rot },
					{
						opacity: 1,
						y: fall,
						x: drift,
						scale: 1,
						rotation: rot + 120 + Math.random() * 120,
						duration: 0.8 + Math.random() * 0.4,
						delay: Math.random() * 0.06,
						ease: 'power2.out',
						onComplete: function () {
							gsap.to( $el[0], {
								opacity: 0,
								duration: 0.2,
								onComplete: function () {
									$el.remove();
								},
							} );
						},
					}
				);
			} )( i );
		}
	}

	function onCartOpened() {
		if ( cartIsOpen ) {
			return;
		}

		cartIsOpen = true;
		burstDoneThisOpen = false;

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
		burstDoneThisOpen = false;
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

	function onFragmentsUpdated() {
		var previousPercent = lastProgressPercent;

		syncSideCartMeta( function () {
			buildLayout();
			fillFooterContent( true );
			updateProgress( cartIsOpen && Math.abs( readProgressPercent() - previousPercent ) > 0.5 );
			window.setTimeout( destroyPerfectScrollbar, 50 );
		} );
	}

	$( function () {
		bindRecoAddLoading();
		bindCouponApply();
		bindRemoveItem();
		bindQtyStepper();
		buildLayout();
		setProgressInstant( readProgressPercent() );
		syncUnlockedMilestones( false );
		watchOpen();
	} );

	$( document.body ).on( 'wc_fragments_refreshed wc_fragments_loaded', function () {
		window.setTimeout( onFragmentsUpdated, 100 );
	} );

	$( document.body ).on( 'added_to_cart', function () {
		window.setTimeout( onFragmentsUpdated, 150 );
	} );
}( jQuery ) );
