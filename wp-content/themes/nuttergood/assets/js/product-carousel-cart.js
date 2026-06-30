( function ( $ ) {
	'use strict';

	var pausedSwipers = [];
	var hoverPaused = new WeakMap();

	function isCarouselButton( $button ) {
		return $button && $button.closest( '.qodef-woo-product-list.qodef-swiper-container' ).length;
	}

	function getSwiper( $button ) {
		var $carousel = $button.closest( '.qodef-woo-product-list.qodef-swiper-container' );

		if ( $carousel.length && $carousel[0].swiper ) {
			return $carousel[0].swiper;
		}

		return null;
	}

	function pauseCarousel( $button ) {
		var swiper = getSwiper( $button );

		if ( ! swiper || ! swiper.autoplay ) {
			return;
		}

		if ( swiper.autoplay.running ) {
			swiper.autoplay.stop();
			pausedSwipers.push( swiper );
		}

		$button.closest( '.qodef-woo-product-list' ).addClass( 'qodef--cart-adding' );
	}

	function resumeCarousels( delay ) {
		window.setTimeout(
			function () {
				while ( pausedSwipers.length ) {
					var swiper = pausedSwipers.pop();

					hoverPaused.delete( swiper );

					if ( swiper.autoplay && ! swiper.autoplay.running ) {
						swiper.autoplay.start();
					}
				}

				$( '.qodef-woo-product-list.qodef--cart-adding' ).removeClass( 'qodef--cart-adding' );
			},
			typeof delay === 'number' ? delay : 400
		);
	}

	function ensureButtonTextWrap( $button ) {
		var $text = $button.find( '.qodef-m-text' );

		if ( $text.length ) {
			return $text;
		}

		var $labelNodes = $button.contents().filter( function () {
			return this.nodeType === 3 || ( this.nodeType === 1 && ! $( this ).hasClass( 'qodef-svg--button-icon' ) );
		} );

		if ( ! $labelNodes.length ) {
			return $();
		}

		$labelNodes.wrapAll( '<span class="qodef-m-text"></span>' );

		return $button.find( '.qodef-m-text' );
	}

	function setButtonLabel( $button, text ) {
		var $text = ensureButtonTextWrap( $button );

		if ( ! $text.length ) {
			return;
		}

		if ( ! $button.data( 'nuttergoodOriginalLabel' ) ) {
			$button.data( 'nuttergoodOriginalLabel', $.trim( $text.text() ) );
		}

		$text.text( text );
	}

	function restoreButtonLabel( $button ) {
		var original = $button.data( 'nuttergoodOriginalLabel' );
		var $text    = $button.find( '.qodef-m-text' );

		if ( original && $text.length ) {
			$text.text( original );
		}
	}

	function startButtonLoading( $button ) {
		if ( ! $button || ! $button.length || ! $button.hasClass( 'ajax_add_to_cart' ) ) {
			return;
		}

		ensureButtonTextWrap( $button );
		$button.addClass( 'loading' );
		setButtonLabel( $button, 'Adding...' );
	}

	function stopButtonLoading( $button ) {
		if ( ! $button || ! $button.length ) {
			return;
		}

		$button.removeClass( 'loading' );
	}

	function cleanupButton( $button, delay ) {
		stopButtonLoading( $button );
		$button.removeClass( 'added' );
		$button.siblings( '.added_to_cart' ).remove();

		window.setTimeout(
			function () {
				restoreButtonLabel( $button );
			},
			typeof delay === 'number' ? delay : 0
		);
	}

	$( document ).on(
		'mouseenter',
		'.qodef-woo-product-list.qodef-swiper-container .qodef-action-holder',
		function () {
			var $list   = $( this ).closest( '.qodef-woo-product-list' );
			var swiper  = getSwiper( $list.find( '.add_to_cart_button' ).first() );

			if ( swiper && swiper.autoplay && swiper.autoplay.running ) {
				swiper.autoplay.stop();
				hoverPaused.set( swiper, true );
			}
		}
	);

	$( document ).on(
		'mouseleave',
		'.qodef-woo-product-list.qodef-swiper-container .qodef-action-holder',
		function () {
			var $list  = $( this ).closest( '.qodef-woo-product-list' );

			if ( $list.hasClass( 'qodef--cart-adding' ) ) {
				return;
			}

			var swiper = getSwiper( $list.find( '.add_to_cart_button' ).first() );

			if ( swiper && hoverPaused.get( swiper ) ) {
				hoverPaused.delete( swiper );

				if ( swiper.autoplay && ! swiper.autoplay.running ) {
					swiper.autoplay.start();
				}
			}
		}
	);

	$( document ).on(
		'mousedown',
		'.qodef-woo-product-list.qodef-swiper-container .add_to_cart_button.ajax_add_to_cart',
		function () {
			var $button = $( this );

			pauseCarousel( $button );
			startButtonLoading( $button );
		}
	);

	$( document.body ).on(
		'adding_to_cart',
		function ( event, $button ) {
			if ( ! isCarouselButton( $button ) ) {
				return;
			}

			pauseCarousel( $button );
			startButtonLoading( $button );
		}
	);

	$( document.body ).on(
		'added_to_cart',
		function ( event, fragments, cartHash, $button ) {
			if ( ! isCarouselButton( $button ) ) {
				return;
			}

			cleanupButton( $button, 900 );
			setButtonLabel( $button, 'Added!' );
			$button.siblings( '.added_to_cart' ).remove();
			resumeCarousels( 900 );
		}
	);

	$( document.body ).on(
		'ajax_request_not_sent.adding_to_cart',
		function ( event, fragments, cartHash, $button ) {
			if ( ! isCarouselButton( $button ) ) {
				return;
			}

			stopButtonLoading( $button );
			restoreButtonLabel( $button );
			resumeCarousels( 0 );
		}
	);

	$( document.body ).on(
		'wc_cart_button_updated',
		function ( event, $button ) {
			if ( ! isCarouselButton( $button ) ) {
				return;
			}

			if ( ! $button.hasClass( 'loading' ) && ! $button.hasClass( 'added' ) ) {
				stopButtonLoading( $button );
				restoreButtonLabel( $button );
				resumeCarousels( 0 );
			}
		}
	);
}( jQuery ) );