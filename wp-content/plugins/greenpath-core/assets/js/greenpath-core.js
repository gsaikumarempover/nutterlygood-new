(function ( $ ) {
	'use strict';

	// This case is important when theme is not active
	if ( typeof qodef !== 'object' ) {
		window.qodef = {};
	}

	window.qodefCore                = {};
	qodefCore.shortcodes            = {};
	qodefCore.listShortcodesScripts = {
		qodefSwiper: qodef.qodefSwiper,
		qodefPagination: qodef.qodefPagination,
		qodefFilter: qodef.qodefFilter,
		qodefMasonryLayout: qodef.qodefMasonryLayout,
		qodefJustifiedGallery: qodef.qodefJustifiedGallery,
		qodefCustomCursor: qodefCore.qodefCustomCursor,
	};

	qodefCore.body         = $( 'body' );
	qodefCore.html         = $( 'html' );
	qodefCore.windowWidth  = $( window ).width();
	qodefCore.windowHeight = $( window ).height();
	qodefCore.scroll       = 0;

	$( document ).ready(
		function () {
			qodefCore.scroll = $( window ).scrollTop();
			qodefInlinePageStyle.init();
			qodefStickyColumn.init();
			qodefAppear.init();
			qodefBttSkin.init();
		}
	);

	$( window ).resize(
		function () {
			qodefCore.windowWidth  = $( window ).width();
			qodefCore.windowHeight = $( window ).height();
			qodefStickyColumn.init();
		}
	);

	$( window ).scroll(
		function () {
			qodefCore.scroll = $( window ).scrollTop();
		}
	);

	$( window ).load(
		function () {
			qodefScrollItem.init();
			qodefCursorItem.init();
		}
	);

	/**
	 * Check element to be in the viewport
	 */
	var qodefIsInViewport = {
		check: function ( $element, callback, onlyOnce, callbackOnExit ) {
			if ( $element.length ) {
				var offset = typeof $element.data( 'viewport-offset' ) !== 'undefined' ? $element.data( 'viewport-offset' ) : 0.15; // When item is 15% in the viewport

				var observer = new IntersectionObserver(
					function ( entries ) {
						// isIntersecting is true when element and viewport are overlapping
						// isIntersecting is false when element and viewport don't overlap
						if ( entries[0].isIntersecting === true ) {
							callback.call( $element );

							// Stop watching the element when it's initialize
							if ( onlyOnce !== false ) {
								observer.disconnect();
							}
						} else if ( callbackOnExit && onlyOnce === false ) {
							callbackOnExit.call( $element );
						}
					},
					{ threshold: [offset] }
				);

				observer.observe( $element[0] );
			}
		},
	};

	qodefCore.qodefIsInViewport = qodefIsInViewport;

	var qodefScroll = {
		disable: function () {
			if ( window.addEventListener ) {
				window.addEventListener(
					'wheel',
					qodefScroll.preventDefaultValue,
					{ passive: false }
				);
			}

			// window.onmousewheel = document.onmousewheel = qodefScroll.preventDefaultValue;
			document.onkeydown = qodefScroll.keyDown;
		},
		enable: function () {
			if ( window.removeEventListener ) {
				window.removeEventListener(
					'wheel',
					qodefScroll.preventDefaultValue,
					{ passive: false }
				);
			}
			window.onmousewheel = document.onmousewheel = document.onkeydown = null;
		},
		preventDefaultValue: function ( e ) {
			e = e || window.event;
			if ( e.preventDefault ) {
				e.preventDefault();
			}
			e.returnValue = false;
		},
		keyDown: function ( e ) {
			var keys = [37, 38, 39, 40];
			for ( var i = keys.length; i--; ) {
				if ( e.keyCode === keys[i] ) {
					qodefScroll.preventDefaultValue( e );
					return;
				}
			}
		}
	};

	qodefCore.qodefScroll = qodefScroll;

	var qodefPerfectScrollbar = {
		init: function ( $holder ) {
			if ( $holder.length ) {
				qodefPerfectScrollbar.qodefInitScroll( $holder );
			}
		},
		qodefInitScroll: function ( $holder ) {
			var $defaultParams = {
				wheelSpeed: 0.6,
				suppressScrollX: true
			};

			var $ps = new PerfectScrollbar(
				$holder[0],
				$defaultParams
			);

			$( window ).resize(
				function () {
					$ps.update();
				}
			);
		}
	};

	qodefCore.qodefPerfectScrollbar = qodefPerfectScrollbar;

	var qodefInlinePageStyle = {
		init: function () {
			this.holder = $( '#greenpath-core-page-inline-style' );

			if ( this.holder.length ) {
				var style = this.holder.data( 'style' );

				if ( style.length ) {
					$( 'head' ).append( '<style type="text/css">' + style + '</style>' );
				}
			}
		}
	};

	var qodefStickyColumn = {
		init: function () {
			var stickyColumnHolder = $( '.qodef-sticky-column--enable' );

			if ( stickyColumnHolder.length ) {
				stickyColumnHolder.each(
					function () {
						var height = $( this ).height();

						if ( $( this ).hasClass( 'qodef-sticky-column-snap-to--top' ) ) {
							$( this ).css(
								'top',
								'calc(0% + ' + qodefGlobal.vars.adminBarHeight + 'px)'
							);
						} else if ( $( this ).hasClass( 'qodef-sticky-column-snap-to--bottom' ) ) {
							$( this ).css(
								'top',
								'calc(100% - ' +  height + 'px)'
							);
						} else {
							$( this ).css(
								'top',
								'calc(50% - ' + ( height - qodefGlobal.vars.adminBarHeight ) / 2 + 'px)'
							);
						}
					}
				);
			}
		}
	};

	qodefCore.qodefStickyColumn = qodefStickyColumn;

	/**
	 * Init scroll item
	 */
	var qodefScrollItem = {
		init: function () {
			var $items = $( '.qodef-scroll-item' );

			if ( $items.length ) {
				$items.each(
					function () {
						var $currentItem       = $( this ),
							$defaultMin        = -35,
							$defaultMax        = -50,
							$defaultSmoothness = 30;

						var $min        = parseInt( $( this ).attr( 'data-parallax-min' ) ? $( this ).attr( 'data-parallax-min' ) : $defaultMin ),
							$max        = parseInt( $( this ).attr( 'data-parallax-max' ) ? $( this ).attr( 'data-parallax-max' ) : $defaultMax ),
							$y          = Math.floor( Math.random() * ($max - $min) + $min ),
							$smoothness = parseInt( $( this ).attr( 'data-parallax-smoothness' ) ? $( this ).attr( 'data-parallax-smoothness' ) : $defaultSmoothness );

						if ( $currentItem.hasClass( 'qodef-grid-item' ) ) {
							$currentItem.children( '.qodef-e-inner' ).attr(
								'data-parallax',
								'{"y": ' + $y + ', "smoothness": ' + $smoothness + '}'
							);
						} else {
							$currentItem.attr(
								'data-parallax',
								'{"y": ' + $y + ', "smoothness": ' + $smoothness + '}'
							);
						}
					}
				);
			}

			qodefScrollItem.initScroll();
		},
		initScroll: function () {
			var parallaxInstances = $( '[data-parallax]' );

			if ( parallaxInstances.length && ! qodefCore.html.hasClass( 'touchevents' ) && typeof ParallaxScroll === 'object' ) {
				ParallaxScroll.init(); //initialization removed from plugin js file to have it run only on non-touch devices
			}
		},
	};

	qodefCore.qodefScrollItem = qodefScrollItem;

	var qodefBttSkin = {
		init: function () {
			var $holder = $( '#qodef-back-to-top' );
			var $rows   = $( '#qodef-page-footer' );

			if ( $holder.length ) {
				qodefBttSkin.initItem(
					$holder,
					$rows
				);
			}
		},
		initItem: function ( $holder, $rows ) {
			gsap.registerPlugin( ScrollTrigger );

			$rows.each( function () {
				var $thisHolder = $( this );
				ScrollTrigger.create( {
					trigger: $thisHolder,
					start: 'top bottom-=35px',
					end: 'bottom bottom-=35px',
					// markers: true,
					onEnter: () => $holder.addClass( 'qodef--light' ),
					onLeave: () => $holder.removeClass( 'qodef--light' ),
					onEnterBack: () => $holder.addClass( 'qodef--light' ),
					onLeaveBack: () => $holder.removeClass( 'qodef--light' ),
				} );
			} );
		},
	};

	qodef.qodefBttSkin = qodefBttSkin;

	/**
	 * Init cursor item
	 */
	var qodefCursorItem = {
		init: function () {
			var $items = $( '.qodef-cursor-item' );

			if ( $items.length ) {
				$items.each(
					function () {
						var $currentItem = $( this );

						qodefCursorItem.initCursor( $currentItem );
					}
				);

				window.addEventListener(
					'mousemove',
					function( e ) {
						qodefCore.mousePos = {
							x: e.clientX,
							y: e.clientY,
						};
					}
				);
			}
		},
		initCursor: function ( $currentItem ) {
			var $defaultXMin       = 10,
				$defaultXMax       = 30,
				$defaultYMin       = 10,
				$defaultYMax       = 20,
				$defaultSmoothness = 0.02;

			qodefCore.mousePos = {
				x: qodefCore.windowWidth / 2,
				y: qodefCore.windowHeight / 2
			};

			// Map number x from range [a, b] to [c, d]
			var map = ( x, a, b, c, d ) => (x - a) * (d - c) / (b - a) + c;

			// Linear interpolation
			var lerp = ( a, b, n ) => (1 - n) * a + n * b;

			var translationVals = { tX: 0, tY: 0 },
				xStart          = gsap.utils.random(
					$defaultXMin,
					$defaultXMax,
					10
				),
				yStart          = gsap.utils.random(
					$defaultYMin,
					$defaultYMax,
					10
				);

			var moveAnimation;

			// infinite loop
			var render = function() {
				// Calculate the amount to move.
				// Using linear interpolation to smooth things out.
				// Translation values will be in the range of [-start, start] for a cursor movement from 0 to the window's width/height
				translationVals.tX = lerp(
					translationVals.tX,
					map(
						qodefCore.mousePos.x,
						0,
						qodefCore.windowWidth,
						-xStart,
						xStart
					),
					$defaultSmoothness
				);
				translationVals.tY = lerp(
					translationVals.tY,
					map(
						qodefCore.mousePos.y,
						0,
						qodefCore.windowHeight,
						-yStart,
						yStart
					),
					$defaultSmoothness
				);

				gsap.set(
					$currentItem,
					{
						x: translationVals.tX,
						y: translationVals.tY
					}
				);

				moveAnimation = requestAnimationFrame( render );
			};

			moveAnimation = requestAnimationFrame( render );

			qodefCore.qodefIsInViewport.check(
				$currentItem,
				function () {
					moveAnimation = requestAnimationFrame( render );
				},
				false,
				function() {
					cancelAnimationFrame( moveAnimation );
				}
			);
		}
	};

	qodefCore.qodefCursorItem = qodefCursorItem;

	/**
	 * Init animation on appear
	 */
	var qodefAppear = {
		init: function () {
			this.holder = $('.qodef--has-appear:not(.qodef--appeared), .qodef--custom-appear:not(.qodef--appeared)');

			if (this.holder.length) {
				this.holder.each(
					function () {
						var holder = $(this);

						qodefCore.qodefIsInViewport.check(
							holder,
							()=>{
								qodef.qodefWaitForImages.check(
									holder,
									function(){
										holder.addClass( 'qodef--appeared' );
									}
								)
							}
						);
					}
				);
			}
		},
	};

	qodefCore.qodefAppear = qodefAppear;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefBackToTop.init();
		}
	);

	var qodefBackToTop = {
		init: function () {
			this.holder = $( '#qodef-back-to-top' );

			if ( this.holder.length ) {
				// Scroll To Top
				this.holder.on(
					'click',
					function ( e ) {
						e.preventDefault();
						qodefBackToTop.animateScrollToTop();
					}
				);

				qodefBackToTop.showHideBackToTop();
			}
		},
		animateScrollToTop: function () {
			var startPos = qodef.scroll,
				newPos   = qodef.scroll,
				step     = .9,
				animationFrameId;

			var startAnimation = function () {
				if ( newPos === 0 ) {
					return;
				}

				newPos < 0.0001 ? newPos = 0 : null;

				var ease = qodefBackToTop.easingFunction( (startPos - newPos) / startPos );
				$( 'html, body' ).scrollTop( startPos - (startPos - newPos) * ease );
				newPos = newPos * step;

				animationFrameId = requestAnimationFrame( startAnimation );
			};

			startAnimation();

			$( window ).one(
				'wheel touchstart',
				function () {
					cancelAnimationFrame( animationFrameId );
				}
			);
		},
		easingFunction: function ( n ) {
			return 0 == n ? 0 : Math.pow( 1200, n - 1 );
		},
		showHideBackToTop: function () {
			$( window ).scroll(
				function () {
					var $thisItem = $( this ),
						b         = $thisItem.scrollTop(),
						c         = $thisItem.height(),
						d;

					if ( b > 0 ) {
						d = b + c / 2;
					} else {
						d = 1;
					}

					if ( d < 1e3 ) {
						qodefBackToTop.addClass( 'off' );
					} else {
						qodefBackToTop.addClass( 'on' );
					}
				}
			);
		},
		addClass: function ( a ) {
			this.holder.removeClass( 'qodef--off qodef--on' );

			if ( a === 'on' ) {
				this.holder.addClass( 'qodef--on' );
			} else {
				this.holder.addClass( 'qodef--off' );
			}
		}
	};

})( jQuery );

(function ($) {
	"use strict";

	$( window ).on(
		'load',
		function () {
			qodefBackgroundText.init();
		}
	);

	$( window ).resize(
		function () {
			qodefBackgroundText.init();
		}
	);

	var qodefBackgroundText = {
		init                    : function () {
			var $holder = $( '.qodef-background-text' );

			if ($holder.length) {
				$holder.each(
					function () {
						qodefBackgroundText.responsiveOutputHandler( $( this ) );
					}
				);
			}
		},
		responsiveOutputHandler : function ($holder) {
			var breakpoints = {
				3840: 1441,
				1512: 1369,
				1368: 1201,
				1200: 1
			};

			$.each(
				breakpoints,
				function (max, min) {
					if (qodef.windowWidth <= max && qodef.windowWidth >= min) {
						qodefBackgroundText.generateResponsiveOutput( $holder, max );
					}
				}
			);
		},
		generateResponsiveOutput: function ($holder, width) {
			var $textHolder = $holder.find( '.qodef-m-background-text' );

			if ($textHolder.length) {
				$textHolder.css(
					{
						'font-size': $textHolder.data( 'size-' + width ) + 'px',
						'top'      : $textHolder.data( 'vertical-offset-' + width ) + 'px',
					}
				);
			}
		},
	};

	window.qodefBackgroundText = qodefBackgroundText;
})( jQuery );

(function ($) {
	'use strict';

	$(document).ready(function () {
		qodefCustomCursor.init();
	});

	// $(window).on(
	// 	'elementor/frontend/init',
	// 	function () {
	// 		qodefCustomCursor.init();
	// 	}
	// );

	var qodefCustomCursor = {
		cursorApended: false,
		init         : function () {
			const $dragSelectors = $('.qodef--drag-cursor');

			if ($dragSelectors.length) {
				const customCursor = qodefGlobal.vars.dragCursor;

				if (false === qodefCustomCursor.cursorApended) {
					qodefCore.html.append('<div class="qodef-m-custom-cursor qodef-m"><div class="qodef-m-custom-cursor-inner">' + customCursor + '</div></div>');
					qodefCustomCursor.cursorApended = true;
				}
				const $cursorHolder = $('.qodef-m-custom-cursor');

				if (!qodefCore.html.hasClass('touchevents')) {
					function handleMoveCursor(event) {
						$cursorHolder.css(
							{
								top : event.clientY - 60, // half of svg height
								left: event.clientX - 60, // half of svg width
							}
						);
					}

					document.addEventListener('pointermove', handleMoveCursor);

					// reset cursor selectors
					const resetCursorSelectors =
							'.qodef--drag-cursor .swiper-button-prev,' +
							'.qodef--drag-cursor .swiper-button-next,' +
							'.qodef--drag-cursor .swiper-pagination,' +
							'.qodef--drag-cursor .qodef-e-media-image a,' + // port/blog list link around image
							'.qodef--drag-cursor a:not(.woocommerce-loop-product__link),' + // product list link around image
							'.qodef--drag-cursor .qodef-e-post-link,' + // port/blog/product list link overlay
							'.qodef--drag-cursor .qodef-e-hotspot',
						$resetCursorSelectors = $(resetCursorSelectors);

					$resetCursorSelectors.css(
						{
							cursor: 'pointer',
						}
					);

					$(document).on(
						'mouseenter',
						resetCursorSelectors,
						function () {
							$cursorHolder.addClass('qodef--hide');
						}
					).on(
						'mouseleave',
						resetCursorSelectors,
						function () {
							$cursorHolder.removeClass('qodef--hide');
						}
					);

					// drag cursor selectors
					const dragSelectors = '.qodef--drag-cursor';

					$(document).on(
						'mouseenter',
						dragSelectors,
						function () {
							$cursorHolder.addClass('qodef--show');
						}
					).on(
						'mouseleave',
						dragSelectors,
						function () {
							$cursorHolder.removeClass('qodef--show');
						}
					);
				}
			}
		},
	};

	qodefCore.qodefCustomCursor = qodefCustomCursor;

})(jQuery);

(function ( $ ) {
	'use strict';

	$( window ).on(
		'load',
		function () {
			qodefUncoverFooter.init();
		}
	);

	var qodefUncoverFooter = {
		holder: '',
		init: function () {
			this.holder = $( '#qodef-page-footer.qodef--uncover' );

			if ( this.holder.length && ! qodefCore.html.hasClass( 'touchevents' ) ) {
				qodefUncoverFooter.addClass();
				qodefUncoverFooter.setHeight( this.holder );

				$( window ).resize(
					function () {
						qodefUncoverFooter.setHeight( qodefUncoverFooter.holder );
					}
				);
			}
		},
		setHeight: function ( $holder ) {
			$holder.css( 'height', 'auto' );

			var footerHeight = $holder.outerHeight();

			if ( footerHeight > 0 ) {
				$( '#qodef-page-outer' ).css(
					{
						'margin-bottom': footerHeight,
						'background-color': qodefCore.body.css( 'backgroundColor' )
					}
				);

				$holder.css( 'height', footerHeight );
			}
		},
		addClass: function () {
			qodefCore.body.addClass( 'qodef-page-footer--uncover' );
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefFullscreenMenu.init();
		}
	);

	$( window ).on(
		'resize',
		function () {
			qodefFullscreenMenu.handleHeaderWidth( 'resize' );
		}
	);

	var qodefFullscreenMenu = {
		init: function () {
			var $fullscreenMenuOpener = $( 'a.qodef-fullscreen-menu-opener' ),
				$menuItems            = $( '#qodef-fullscreen-area nav ul li a' );

			if ( $fullscreenMenuOpener.length ) {
				// prevent header changing width when fullscreen menu is open
				qodefFullscreenMenu.handleHeaderWidth( 'init' );

				// open popup menu
				$fullscreenMenuOpener.on(
					'click',
					function ( e ) {
						e.preventDefault();
						var $thisOpener = $( this );

						if ( ! qodefCore.body.hasClass( 'qodef-fullscreen-menu--opened' ) ) {
							qodefFullscreenMenu.openFullscreen( $thisOpener );

							$( document ).keyup(
								function ( e ) {
									if ( e.keyCode === 27 ) {
										qodefFullscreenMenu.closeFullscreen( $thisOpener );
									}
								}
							);
						} else {
							qodefFullscreenMenu.closeFullscreen( $thisOpener );
						}
					}
				);

				// open dropdowns
				$menuItems.on(
					'tap click',
					function ( e ) {
						var $thisItem = $( this );

						if ( $thisItem.parent().hasClass( 'menu-item-has-children' ) ) {
							e.preventDefault();
							qodefFullscreenMenu.clickItemWithChild( $thisItem );
						} else if ( $thisItem.attr( 'href' ) !== 'http://#' && $thisItem.attr( 'href' ) !== '#' ) {
							qodefFullscreenMenu.closeFullscreen( $fullscreenMenuOpener );
						}
					}
				);
			}
		},
		openFullscreen: function ( $opener ) {
			$opener.addClass( 'qodef--opened' );
			qodefCore.body.removeClass( 'qodef-fullscreen-menu-animate--out' ).addClass( 'qodef-fullscreen-menu--opened qodef-fullscreen-menu-animate--in' );
			qodefCore.qodefScroll.disable();
		},
		closeFullscreen: function ( $opener ) {
			$opener.removeClass( 'qodef--opened' );
			qodefCore.body.removeClass( 'qodef-fullscreen-menu--opened qodef-fullscreen-menu-animate--in' ).addClass( 'qodef-fullscreen-menu-animate--out' );
			qodefCore.qodefScroll.enable();
			$( 'nav.qodef-fullscreen-menu ul.sub_menu' ).slideUp( 200 );
		},
		clickItemWithChild: function ( thisItem ) {
			var $thisItemParent  = thisItem.parent(),
				$thisItemSubMenu = $thisItemParent.find( '.sub-menu' ).first();

			if ( $thisItemSubMenu.is( ':visible' ) ) {
				$thisItemSubMenu.slideUp( 300 );
				$thisItemParent.removeClass( 'qodef--opened' );
			} else {
				$thisItemSubMenu.slideDown( 300 );
				$thisItemParent.addClass( 'qodef--opened' ).siblings().find( '.sub-menu' ).slideUp( 400 );
			}
		},
		handleHeaderWidth: function ( state ) {
			var $header               = $( '#qodef-page-header' );
			var $fullscreenMenuOpener = $( 'a.qodef-fullscreen-menu-opener' );

			if ( $header.length && $fullscreenMenuOpener.length ) {
				// if desktop device
				if ( qodefCore.windowWidth > 1200 ) {
					// if page height is greater than window height, scroll bar is visible
					if ( qodefCore.body.height() > qodefCore.windowHeight ) {
						// on resize reset previously set inline width
						if ( 'resize' === state ) {
							$header.css( { 'width': '' } );
						}
						$header.width( $header.width() );
					}
				} else {
					// reset previously set inline width
					$header.css( { 'width': '' } );
				}
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefHeaderScrollAppearance.init();
		}
	);

	var qodefHeaderScrollAppearance = {
		appearanceType: function () {
			return qodefCore.body.attr( 'class' ).indexOf( 'qodef-header-appearance--' ) !== -1 ? qodefCore.body.attr( 'class' ).match( /qodef-header-appearance--([\w]+)/ )[1] : '';
		},
		init: function () {
			var appearanceType = this.appearanceType();

			if ( appearanceType !== '' && appearanceType !== 'none' ) {
				qodefCore[appearanceType + 'HeaderAppearance']();
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
	    function () {
            qodefMobileHeaderAppearance.init();
        }
	);

	/*
	 **	Init mobile header functionality
	 */
	var qodefMobileHeaderAppearance = {
		init: function () {
			if ( qodefCore.body.hasClass( 'qodef-mobile-header-appearance--sticky' ) ) {

				var $adminBarHeight = qodefGlobal.vars.adminBarHeight,
					docYScroll1     = qodefCore.scroll,
					displayAmount   = 0,
					$pageOuter      = $( '#qodef-page-outer' ),
					$bottomSearch   = $( '.qodef-page-mobile-header-bottom-search' );

				// Admin bar for mobile
				if ( $adminBarHeight > 0 ) {

					if( qodef.windowWidth <= 782 ) {
						$adminBarHeight = $adminBarHeight + 14;
					}

					displayAmount += $adminBarHeight;
				}

				/*if( $bottomSearch.length ) {
					displayAmount += $bottomSearch.outerHeight();
				}*/

				qodefMobileHeaderAppearance.showHideMobileHeader( docYScroll1, displayAmount, $pageOuter );

				$( window ).scroll(
				    function () {
                        qodefMobileHeaderAppearance.showHideMobileHeader( docYScroll1, displayAmount, $pageOuter );
                        docYScroll1 = qodefCore.scroll;
                    }
				);

				$( window ).resize(
				    function () {
                        $pageOuter.css( 'padding-top', 0 );
                        qodefMobileHeaderAppearance.showHideMobileHeader( docYScroll1, displayAmount, $pageOuter );
                    }
				);
			}
		},
		showHideMobileHeader: function ( docYScroll1, displayAmount, $pageOuter ) {
			var $bottomSearch = $( '.qodef-page-mobile-header-bottom-search' );

			if ( qodefCore.windowWidth <= 1200 ) {
				if ( qodefCore.scroll > displayAmount ) {
					//set header to be fixed
					qodefCore.body.addClass( 'qodef-mobile-header--sticky' );

					if( $bottomSearch.length ) {
						//add padding to content so there is no 'jumping'
						$pageOuter.css( 'padding-top', qodefGlobal.vars.mobileHeaderHeight + $bottomSearch.outerHeight() );
					} else {
						$pageOuter.css( 'padding-top', qodefGlobal.vars.mobileHeaderHeight );
					}
				} else {
					//unset fixed header
					qodefCore.body.removeClass( 'qodef-mobile-header--sticky' );

					//remove padding from content since header is not fixed anymore
					$pageOuter.css( 'padding-top', 0 );
				}

				if ( qodefCore.scroll <= displayAmount ) {
					//show sticky header
					qodefCore.body.removeClass( 'qodef-mobile-header--sticky-display' );
				} else {
					//hide sticky header
					qodefCore.body.addClass( 'qodef-mobile-header--sticky-display' );
				}
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefNavMenu.init();
		}
	);

	$( window ).on(
		'resize',
		function () {
			qodefNavMenu.wideDropdownPosition();
		}
	);

	var qodefNavMenu = {
		init: function () {
			qodefNavMenu.dropdownBehavior();
			qodefNavMenu.wideDropdownPosition();
			qodefNavMenu.dropdownPosition();
		},
		dropdownBehavior: function () {
			var $menuItems = $( '.qodef-header-navigation > ul > li' );

			$menuItems.each(
				function () {
					var $thisItem = $( this );

					if ( $thisItem.find( '.qodef-drop-down-second' ).length ) {
						qodef.qodefWaitForImages.check(
							$thisItem,
							function () {
								var $dropdownHolder      = $thisItem.find( '.qodef-drop-down-second' ),
									$dropdownMenuItem    = $dropdownHolder.find( '.qodef-drop-down-second-inner ul' ),
									dropDownHolderHeight = $dropdownMenuItem.outerHeight();

								if ( navigator.userAgent.match( /(iPod|iPhone|iPad)/ ) ) {
									$thisItem.on(
										'touchstart mouseenter',
										function () {
											$dropdownHolder.css(
												{
													'height': dropDownHolderHeight,
													'overflow': 'visible',
													'visibility': 'visible',
													'opacity': '1',
												}
											);
										}
									).on(
										'mouseleave',
										function () {
											$dropdownHolder.css(
												{
													'height': '0px',
													'overflow': 'hidden',
													'visibility': 'hidden',
													'opacity': '0',
												}
											);
										}
									);
								} else {
									if ( qodefCore.body.hasClass( 'qodef-drop-down-second--animate-height' ) ) {
										var animateConfig = {
											interval: 0,
											over: function () {
												setTimeout(
													function () {
														$dropdownHolder.addClass( 'qodef-drop-down--start' ).css(
															{
																'visibility': 'visible',
																'height': '0',
																'opacity': '1',
															}
														);
														$dropdownHolder.stop().animate(
															{
																'height': dropDownHolderHeight,
															},
															400,
															'linear',
															function () {
																$dropdownHolder.css( 'overflow', 'visible' );
															}
														);
													},
													100
												);
											},
											timeout: 100,
											out: function () {
												$dropdownHolder.stop().animate(
													{
														'height': '0',
														'opacity': 0,
													},
													100,
													function () {
														$dropdownHolder.css(
															{
																'overflow': 'hidden',
																'visibility': 'hidden',
															}
														);
													}
												);

												$dropdownHolder.removeClass( 'qodef-drop-down--start' );
											}
										};

										$thisItem.hoverIntent( animateConfig );
									} else {
										var config = {
											interval: 0,
											over: function () {
												setTimeout(
													function () {
														$dropdownHolder.addClass( 'qodef-drop-down--start' ).stop().css( { 'height': dropDownHolderHeight } );
													},
													150
												);
											},
											timeout: 150,
											out: function () {
												$dropdownHolder.stop().css( { 'height': '0' } ).removeClass( 'qodef-drop-down--start' );
											}
										};

										$thisItem.hoverIntent( config );
									}
								}
							}
						);
					}
				}
			);
		},
		wideDropdownPosition: function () {
			var $menuItems = $( '.qodef-header-navigation > ul > li.qodef-menu-item--wide' );

			if ( $menuItems.length ) {
				$menuItems.each(
					function () {
						var $menuItem        = $( this );
						var $menuItemSubMenu = $menuItem.find( '.qodef-drop-down-second' );

						if ( $menuItemSubMenu.length ) {
							$menuItemSubMenu.css( 'left', 0 );

							var leftPosition = $menuItemSubMenu.offset().left;

							if ( qodefCore.body.hasClass( 'qodef--boxed' ) ) {
								//boxed layout case
								var boxedWidth = $( '.qodef--boxed #qodef-page-wrapper' ).outerWidth();
								leftPosition   = leftPosition - (qodefCore.windowWidth - boxedWidth) / 2;
								$menuItemSubMenu.css( { 'left': -leftPosition, 'width': boxedWidth } );

							} else if ( qodefCore.body.hasClass( 'qodef-drop-down-second--full-width' ) ) {
								//wide dropdown full width case
								$menuItemSubMenu.css( { 'left': -leftPosition, 'width': qodefCore.windowWidth } );
							} else {
								//wide dropdown in grid case
								$menuItemSubMenu.css( { 'left': -leftPosition + (qodefCore.windowWidth - $menuItemSubMenu.width()) / 2 } );
							}
						}
					}
				);
			}
		},
		dropdownPosition: function () {
			var $menuItems = $( '.qodef-header-navigation > ul > li.qodef-menu-item--narrow.menu-item-has-children' );

			if ( $menuItems.length ) {
				$menuItems.each(
					function () {
						var $thisItem         = $( this ),
							menuItemPosition  = $thisItem.offset().left,
							$dropdownHolder   = $thisItem.find( '.qodef-drop-down-second' ),
							$dropdownMenuItem = $dropdownHolder.find( '.qodef-drop-down-second-inner ul' ),
							dropdownMenuWidth = $dropdownMenuItem.outerWidth(),
							menuItemFromLeft  = $( window ).width() - menuItemPosition;

						if ( qodef.body.hasClass( 'qodef--boxed' ) ) {
							//boxed layout case
							var boxedWidth   = $( '.qodef--boxed #qodef-page-wrapper' ).outerWidth();
							menuItemFromLeft = boxedWidth - menuItemPosition;
						}

						var dropDownMenuFromLeft;

						if ( $thisItem.find( 'li.menu-item-has-children' ).length > 0 ) {
							dropDownMenuFromLeft = menuItemFromLeft - dropdownMenuWidth;
						}

						$dropdownHolder.removeClass( 'qodef-drop-down--right' );
						$dropdownMenuItem.removeClass( 'qodef-drop-down--right' );
						if ( menuItemFromLeft < dropdownMenuWidth || dropDownMenuFromLeft < dropdownMenuWidth ) {
							$dropdownHolder.addClass( 'qodef-drop-down--right' );
							$dropdownMenuItem.addClass( 'qodef-drop-down--right' );
						}
					}
				);
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( window ).on(
		'load',
		function () {
			qodefParallaxBackground.init();
		}
	);

	/**
	 * Init global parallax background functionality
	 */
	var qodefParallaxBackground = {
		init: function ( settings ) {
			this.$sections = $( '.qodef-parallax' );

			// Allow overriding the default config
			$.extend( this.$sections, settings );

			var isSupported = ! qodefCore.html.hasClass( 'touchevents' ) && ! qodefCore.body.hasClass( 'qodef-browser--edge' ) && ! qodefCore.body.hasClass( 'qodef-browser--ms-explorer' );

			if ( this.$sections.length && isSupported ) {
				this.$sections.each(
					function () {
						if (!$( this ).hasClass('qodef-parallax--init')){//added from elementor js
							qodefParallaxBackground.ready($( this ));
							$( this ).addClass('qodef-parallax--init');
						}
					}
				);
			}
		},
		ready: function ( $section ) {
			qodefParallaxBackground.animateParallax( $section );
		},
		animateParallax: function ( $section ) {
			var isInTitleArea = $section.closest('.qodef-page-title').length;
			var isAboveFold = $section.offset().top < qodefCore.windowHeight;
			var $parallaxHolder = isInTitleArea ? $section.find('.qodef-m-inner') : $section.find('.qodef-parallax-row-holder'),
				$parallaxHolderInner = $section.find('.qodef-parallax-img-holder');

			if (! qodefCore.html.hasClass( 'touchevents' )){

				var maxY =  $parallaxHolderInner.outerHeight() - $parallaxHolder.outerHeight(),
					maxX = $parallaxHolderInner.outerWidth() - $parallaxHolder.outerWidth();

				if ($section.hasClass( 'qodef-parallax-translate' )){
					maxX = 0;
				}

				if ($section.hasClass( 'qodef-parallax-diagonal-to-right' )){
					maxX = -maxX;
				}

				gsap.to(
					$parallaxHolderInner,
					{
						opacity: 1,
						duration: 1,
					}
				)

				const tl = gsap.timeline({
					scrollTrigger: {
						trigger: $section,
						scrub: 1.5,
						start: () => {
							if (isInTitleArea){
								return "top top"
							} else if ( isAboveFold ) {
								return "center center"
							} else {
								return "top bottom"
							}
						},
						end: () => {
							return "bottom top";
						},
						// markers: true,
					}
				});

				tl
				.to(
					$parallaxHolderInner,
					{
						y: -maxY,
						x: -maxX,
					},
				)
			}
		}
	};

	qodefCore.qodefParallaxBackground = qodefParallaxBackground;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefReview.init();
		}
	);

	var qodefReview = {
		init: function () {
			var ratingHolder = $( '#qodef-page-comments-form .qodef-rating-inner' );

			var addActive = function ( stars, ratingValue ) {
				for ( var i = 0; i < stars.length; i++ ) {
					var star = stars[i];

					if ( i < ratingValue ) {
						$( star ).addClass( 'active' );
					} else {
						$( star ).removeClass( 'active' );
					}
				}
			};

			ratingHolder.each(
				function () {
					var thisHolder  = $( this ),
						ratingInput = thisHolder.find( '.qodef-rating' ),
						ratingValue = ratingInput.val(),
						stars       = thisHolder.find( '.qodef-star-rating' );

					addActive( stars, ratingValue );

					stars.on(
						'click',
						function () {
							ratingInput.val( $( this ).data( 'value' ) ).trigger( 'change' );
						}
					);

					ratingInput.change(
						function () {
							ratingValue = ratingInput.val();

							addActive( stars, ratingValue );
						}
					);
				}
			);
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefSideArea.init();
		}
	);

	var qodefSideArea = {
		init: function () {
			var $sideAreaOpener = $( 'a.qodef-side-area-opener' ),
				$sideAreaClose  = $( '#qodef-side-area-close' ),
				$sideArea       = $( '#qodef-side-area' );

			qodefSideArea.openerHoverColor( $sideAreaOpener );

			// Open Side Area
			$sideAreaOpener.on(
				'click',
				function ( e ) {
					e.preventDefault();

					if ( ! qodefCore.body.hasClass( 'qodef-side-area--opened' ) ) {
						qodefSideArea.openSideArea();

						$( document ).keyup(
							function ( e ) {
								if ( e.keyCode === 27 ) {
									qodefSideArea.closeSideArea();
								}
							}
						);
					} else {
						qodefSideArea.closeSideArea();
					}
				}
			);

			$sideAreaClose.on(
				'click',
				function ( e ) {
					e.preventDefault();
					qodefSideArea.closeSideArea();
				}
			);

			if ( $sideArea.length && typeof qodefCore.qodefPerfectScrollbar === 'object' ) {
				qodefCore.qodefPerfectScrollbar.init( $sideArea );
			}
		},
		openSideArea: function () {
			var $wrapper      = $( '#qodef-page-wrapper' );
			var currentScroll = $( window ).scrollTop();

			$( '.qodef-side-area-cover' ).remove();
			$wrapper.prepend( '<div class="qodef-side-area-cover"/>' );
			qodefCore.body.removeClass( 'qodef-side-area-animate--out' ).addClass( 'qodef-side-area--opened qodef-side-area-animate--in' );

			$( '.qodef-side-area-cover' ).on(
				'click',
				function ( e ) {
					e.preventDefault();
					qodefSideArea.closeSideArea();
				}
			);

			$( window ).scroll(
				function () {
					if ( Math.abs( qodefCore.scroll - currentScroll ) > 400 ) {
						qodefSideArea.closeSideArea();
					}
				}
			);
		},
		closeSideArea: function () {
			qodefCore.body.removeClass( 'qodef-side-area--opened qodef-side-area-animate--in' ).addClass( 'qodef-side-area-animate--out' );
		},
		openerHoverColor: function ( $opener ) {
			if ( typeof $opener.data( 'hover-color' ) !== 'undefined' ) {
				var hoverColor    = $opener.data( 'hover-color' );
				var originalColor = $opener.css( 'color' );

				$opener.on(
					'mouseenter',
					function () {
						$opener.css( 'color', hoverColor );
					}
				).on(
					'mouseleave',
					function () {
						$opener.css( 'color', originalColor );
					}
				);
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function() {
			qodefSpinner.init();
		}
	);

	$( window ).on(
		'load',
		function () {
			qodefSpinner.windowLoaded = true;

			if (document.visibilityState === 'visible') {
				qodefSpinner.fadeOutLoader();
			} else {
				document.addEventListener("visibilitychange", function() {
					if (document.visibilityState === 'visible') {
						qodefSpinner.fadeOutLoader();
					}
				});
			}
		}
	);

	$( window ).on(
		'elementor/frontend/init',
		function () {
			var isEditMode = Boolean( elementorFrontend.isEditMode() );

			if ( isEditMode ) {
				qodefSpinner.init( isEditMode );
			}
		}
	);

	var qodefSpinner = {
		holder: '',
		windowLoaded: false,
		init: function ( isEditMode ) {
			this.holder = $( '#qodef-page-spinner:not(.qodef--custom-spinner):not(.qodef-layout--textual)' );

			if ( this.holder.length ) {
				qodefSpinner.animateSpinner( isEditMode );
				qodefSpinner.fadeOutAnimation();
			}
		},
		animateSpinner: function ( isEditMode ) {

			if ( isEditMode ) {
				qodefSpinner.fadeOutLoader();
			}
		},
		fadeOutLoader: function ( speed, delay, easing ) {
			var $holder = qodefSpinner.holder.length ? qodefSpinner.holder : $( '#qodef-page-spinner:not(.qodef--custom-spinner):not(.qodef-layout--textual)' );

			speed  = speed ? speed : 600;
			delay  = delay ? delay : 0;
			easing = easing ? easing : 'swing';

			$holder.delay( delay ).fadeOut( speed, easing );

			$( window ).on(
				'bind',
				'pageshow',
				function ( event ) {
					if ( event.originalEvent.persisted ) {
						$holder.fadeOut( speed, easing );
					}
				}
			);
		},
		fadeOutAnimation: function () {

			// Check for fade out animation
			if ( qodefCore.body.hasClass( 'qodef-spinner--fade-out' ) ) {
				var $pageHolder = $( '#qodef-page-wrapper' ),
					$linkItems  = $( 'a' );

				// If back button is pressed, then show content to avoid state where content is on display:none
				window.addEventListener(
					'pageshow',
					function ( event ) {
						var historyPath = event.persisted || (typeof window.performance !== 'undefined' && window.performance.navigation.type === 2);
						if ( historyPath && ! $pageHolder.is( ':visible' ) ) {
							$pageHolder.show();
						}
					}
				);

				$linkItems.on(
					'click',
					function ( e ) {
						var $clickedLink = $( this );

						if (
							e.which === 1 && // check if the left mouse button has been pressed
							$clickedLink.attr( 'href' ).indexOf( window.location.host ) >= 0 && // check if the link is to the same domain
							! $clickedLink.hasClass( 'remove' ) && // check is WooCommerce remove link
							$clickedLink.parent( '.product-remove' ).length <= 0 && // check is WooCommerce remove link
							$clickedLink.parents( '.woocommerce-product-gallery__image' ).length <= 0 && // check is product gallery link
							typeof $clickedLink.data( 'rel' ) === 'undefined' && // check pretty photo link
							typeof $clickedLink.attr( 'rel' ) === 'undefined' && // check VC pretty photo link
							! $clickedLink.hasClass( 'lightbox-active' ) && // check is lightbox plugin active
							(typeof $clickedLink.attr( 'target' ) === 'undefined' || $clickedLink.attr( 'target' ) === '_self') && // check if the link opens in the same window
							$clickedLink.attr( 'href' ).split( '#' )[0] !== window.location.href.split( '#' )[0] // check if it is an anchor aiming for a different page
						) {
							e.preventDefault();

							$pageHolder.fadeOut(
								600,
								'easeOutSine',
								function () {
									window.location = $clickedLink.attr( 'href' );
								}
							);
						}
					}
				);
			}
		}
	};

	qodefCore.qodefSpinner = qodefSpinner;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( window ).on(
		'load',
		function () {
			qodefSubscribeModal.init();
		}
	);

	var qodefSubscribeModal = {
		init: function () {
			this.holder = $( '#qodef-subscribe-popup-modal' );

			if ( this.holder.length ) {
				var $preventHolder = this.holder.find( '.qodef-sp-prevent' ),
					$modalClose    = $( '.qodef-sp-close' ),
					disabledPopup  = 'no';

				if ( $preventHolder.length ) {
					var isLocalStorage = this.holder.hasClass( 'qodef-sp-prevent-cookies' ),
						$preventInput  = $preventHolder.find( '.qodef-sp-prevent-input' ),
						preventValue   = $preventInput.data( 'value' );

					if ( isLocalStorage ) {
						disabledPopup = localStorage.getItem( 'disabledPopup' );
						sessionStorage.removeItem( 'disabledPopup' );
					} else {
						disabledPopup = sessionStorage.getItem( 'disabledPopup' );
						localStorage.removeItem( 'disabledPopup' );
					}

					$preventHolder.children().on(
						'click',
						function () {
							if ( preventValue !== 'yes' ) {
								preventValue = 'yes';
								$preventInput.addClass( 'qodef-sp-prevent-clicked' ).data( 'value', 'yes' );
							} else {
								preventValue = 'no';
								$preventInput.removeClass( 'qodef-sp-prevent-clicked' ).data( 'value', 'no' );
							}

							if ( preventValue === 'yes' ) {
								if ( isLocalStorage ) {
									localStorage.setItem( 'disabledPopup', 'yes' );
								} else {
									sessionStorage.setItem( 'disabledPopup', 'yes' );
								}
							} else {
								if ( isLocalStorage ) {
									localStorage.setItem( 'disabledPopup', 'no' );
								} else {
									sessionStorage.setItem( 'disabledPopup', 'no' );
								}
							}
						}
					);
				}

				if ( disabledPopup !== 'yes' ) {
					if ( qodefCore.body.hasClass( 'qodef-sp-opened' ) ) {
						qodefSubscribeModal.handleClassAndScroll( 'remove' );
					} else {
						qodefSubscribeModal.handleClassAndScroll( 'add' );
					}

					$modalClose.on(
						'click',
						function ( e ) {
							e.preventDefault();

							qodefSubscribeModal.handleClassAndScroll( 'remove' );
						}
					);

					// Close on escape
					$( document ).keyup(
						function ( e ) {
							if ( e.keyCode === 27 ) { // KeyCode for ESC button is 27
								qodefSubscribeModal.handleClassAndScroll( 'remove' );
							}
						}
					);
				}
			}
		},

		handleClassAndScroll: function ( option ) {
			if ( option === 'remove' ) {
				qodefCore.body.removeClass( 'qodef-sp-opened' );
				qodefCore.qodefScroll.enable();
			}

			if ( option === 'add' ) {
				qodefCore.body.addClass( 'qodef-sp-opened' );
				qodefCore.qodefScroll.disable();
			}
		},
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefHeaderTopMessageClose.init();
		}
	);

	var qodefHeaderTopMessageClose = {
		init: function () {
			var closeMessage = $('.qodef-close-message');
			var messageHolder = $('#qodef-top-message-holder');
			closeMessage.click(function () {
				setTimeout(300, messageHolder.addClass('qodef-close-message'));
				messageHolder.slideUp('fast');
			});
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_accordion = {};

	$( document ).ready(
		function () {
			qodefAccordion.init();
		}
	);

	var qodefAccordion = {
		init: function () {
			var $holder = $( '.qodef-accordion' );

			if ( $holder.length ) {
				$holder.each(
					function () {
						qodefAccordion.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			if ( $currentItem.hasClass( 'qodef-behavior--accordion' ) ) {
				qodefAccordion.initAccordion( $currentItem );
			}

			if ( $currentItem.hasClass( 'qodef-behavior--toggle' ) ) {
				qodefAccordion.initToggle( $currentItem );
			}

			$currentItem.addClass( 'qodef--init' );
		},
		initAccordion: function ( $accordion ) {
			$accordion.accordion(
				{
					header: $accordion.find('.qodef-accordion-title'),
					animate: 'swing',
					collapsible: true,
					active: 0,
					icons: '',
					heightStyle: 'content',
				}
			);
		},
		initToggle: function ( $toggle ) {
			var $toggleAccordionTitle = $toggle.find( '.qodef-accordion-title' );

			$toggleAccordionTitle.off().on(
				'mouseenter',
				function () {
					$( this ).addClass( 'ui-state-hover' );
				}
			).on(
				'mouseleave',
				function () {
					$( this ).removeClass( 'ui-state-hover' );
				}
			).on(
				'click',
				function ( e ) {
					e.preventDefault();
					e.stopImmediatePropagation();

					var $thisTitle = $( this );

					if ( $thisTitle.hasClass( 'ui-state-active' ) ) {
						$thisTitle.removeClass( 'ui-state-active' );
						$thisTitle.next().removeClass( 'ui-accordion-content-active' ).slideUp( 300 );
					} else {
						$thisTitle.addClass( 'ui-state-active' );
						$thisTitle.next().addClass( 'ui-accordion-content-active' ).slideDown( 400 );
					}
				}
			);
		}
	};

	qodefCore.shortcodes.greenpath_core_accordion.qodefAccordion = qodefAccordion;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_button = {};

	$( document ).ready(
		function () {
			qodefButton.init();
		}
	);

	var qodefButton = {
		init: function () {
			this.buttons = $( '.qodef-button' );

			if ( this.buttons.length ) {
				this.buttons.each(
					function () {
						qodefButton.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			qodefButton.buttonHoverColor( $currentItem );
			qodefButton.buttonHoverBgColor( $currentItem );
			qodefButton.buttonHoverBorderColor( $currentItem );
		},
		buttonHoverColor: function ( $button ) {
			if ( typeof $button.data( 'hover-color' ) !== 'undefined' ) {
				var hoverColor    = $button.data( 'hover-color' );
				var originalColor = $button.css( 'color' );

				$button.on(
					'mouseenter touchstart',
					function () {
						qodefButton.changeColor( $button, 'color', hoverColor );
					}
				);
				$button.on(
					'mouseleave touchend',
					function () {
						qodefButton.changeColor( $button, 'color', originalColor );
					}
				);
			}
		},
		buttonHoverBgColor: function ( $button ) {
			if ( typeof $button.data( 'hover-background-color' ) !== 'undefined' ) {
				var hoverBackgroundColor    = $button.data( 'hover-background-color' );
				var originalBackgroundColor = $button.css( 'background-color' );

				$button.on(
					'mouseenter touchstart',
					function () {
						qodefButton.changeColor( $button, 'background-color', hoverBackgroundColor );
					}
				);
				$button.on(
					'mouseleave touchend',
					function () {
						qodefButton.changeColor( $button, 'background-color', originalBackgroundColor );
					}
				);
			}
		},
		buttonHoverBorderColor: function ( $button ) {
			if ( typeof $button.data( 'hover-border-color' ) !== 'undefined' ) {
				var hoverBorderColor    = $button.data( 'hover-border-color' );
				var originalBorderColor = $button.css( 'borderTopColor' );

				$button.on(
					'mouseenter touchstart',
					function () {
						qodefButton.changeColor( $button, 'border-color', hoverBorderColor );
					}
				);
				$button.on(
					'mouseleave touchend',
					function () {
						qodefButton.changeColor( $button, 'border-color', originalBorderColor );
					}
				);
			}
		},
		changeColor: function ( $button, cssProperty, color ) {
			$button.css( cssProperty, color );
		}
	};

	qodefCore.shortcodes.greenpath_core_button.qodefButton = qodefButton;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_countdown = {};

	$( document ).ready(
		function () {
			qodefCountdown.init();
		}
	);

	var qodefCountdown = {
		init: function () {
			this.countdowns = $( '.qodef-countdown' );

			if ( this.countdowns.length ) {
				this.countdowns.each(
					function () {
						qodefCountdown.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			var $countdownElement = $currentItem.find( '.qodef-m-date' ),
				dateFormats       = ['week', 'day', 'hour', 'minute', 'second'],
				options           = qodefCountdown.generateOptions( $currentItem, dateFormats );

			qodefCountdown.initCountdown( $countdownElement, options, dateFormats );
		},
		generateOptions: function ( $countdown, dateFormats ) {
			var options = {};

			options.date = typeof $countdown.data( 'date' ) !== 'undefined' ? $countdown.data( 'date' ) : null;

			for ( var i = 0; i < dateFormats.length; i++ ) {
				var label       = dateFormats[i] + 'Label',
					labelPlural = dateFormats[i] + 'LabelPlural';

				options[label]       = typeof $countdown.data( dateFormats[i] + '-label' ) !== 'undefined' ? $countdown.data( dateFormats[i] + '-label' ) : '';
				options[labelPlural] = typeof $countdown.data( dateFormats[i] + '-label-plural' ) !== 'undefined' ? $countdown.data( dateFormats[i] + '-label-plural' ) : '';
			}

			return options;
		},
		initCountdown: function ( $countdownElement, options, dateFormats ) {
			var countDownDate = new Date( options.date ).getTime();

			// Update the count down every 1 second
			var x = setInterval(
				function () {

					// Get today's date and time
					var now = new Date().getTime();

					// Find the distance between now and the count down date
					var distance = countDownDate - now;

					// Time calculations for days, hours, minutes and seconds
					this.weeks   = Math.floor( distance / (1000 * 60 * 60 * 24 * 7) );
					this.days    = Math.floor( (distance % (1000 * 60 * 60 * 24 * 7)) / (1000 * 60 * 60 * 24) );
					this.hours   = Math.floor( (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60) );
					this.minutes = Math.floor( (distance % (1000 * 60 * 60)) / (1000 * 60) );
					this.seconds = Math.floor( (distance % (1000 * 60)) / 1000 );

					for ( var i = 0; i < dateFormats.length; i++ ) {
						var dateName = dateFormats[i] + 's';
						qodefCountdown.initiateDate( $countdownElement, this[dateName], dateFormats[i], options );
					}

					// If the count down is finished, write some text
					if ( distance < 0 ) {
						clearInterval( x );
						qodefCountdown.afterClearInterval( $countdownElement, dateFormats, options );
					}
				},
				1000
			);
		},
		initiateDate: function ( $countdownElement, date, dateFormat, options ) {
			var $holder = $countdownElement.find( '.qodef-' + dateFormat + 's' );

			$holder.find( '.qodef-label' ).html( ( 1 === date ) ? options[dateFormat + 'Label'] : options[dateFormat + 'LabelPlural'] );

			date = (date < 10) ? '0' + date : date;

			$holder.find( '.qodef-digit' ).html( date );
		},
		afterClearInterval: function( $countdownElement, dateFormats, options ) {
			for ( var i = 0; i < dateFormats.length; i++ ) {
				var $holder = $countdownElement.find( '.qodef-' + dateFormats[i] + 's' );

				$holder.find( '.qodef-label' ).html( options[dateFormats[i] + 'LabelPlural'] );
				$holder.find( '.qodef-digit' ).html( '00' );
			}
		}
	};

	qodefCore.shortcodes.greenpath_core_countdown.qodefCountdown = qodefCountdown;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_counter = {};

	$( document ).ready(
		function () {
			qodefCounter.init();
		}
	);

	var qodefCounter = {
		init: function () {
			this.counters = $( '.qodef-counter' );

			if ( this.counters.length ) {
				this.counters.each(
					function () {
						qodefCounter.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			var $counterElement = $currentItem.find( '.qodef-m-digit' ),
				options         = qodefCounter.generateOptions( $currentItem );

			qodefCore.qodefIsInViewport.check(
				$currentItem,
				function () {
					qodefCounter.counterScript( $counterElement, options );
				},
			);
		},
		generateOptions: function ( $counter ) {
			var options   = {};
			options.start = typeof $counter.data( 'start-digit' ) !== 'undefined' && $counter.data( 'start-digit' ) !== '' ? $counter.data( 'start-digit' ) : 0;
			options.end   = typeof $counter.data( 'end-digit' ) !== 'undefined' && $counter.data( 'end-digit' ) !== '' ? $counter.data( 'end-digit' ) : null;
			options.step  = typeof $counter.data( 'step-digit' ) !== 'undefined' && $counter.data( 'step-digit' ) !== '' ? $counter.data( 'step-digit' ) : 1;
			options.delay = typeof $counter.data( 'step-delay' ) !== 'undefined' && $counter.data( 'step-delay' ) !== '' ? parseInt( $counter.data( 'step-delay' ), 10 ) : 100;
			options.txt   = typeof $counter.data( 'digit-label' ) !== 'undefined' && $counter.data( 'digit-label' ) !== '' ? $counter.data( 'digit-label' ) : '';

			return options;
		},
		counterScript: function ( $counterElement, options ) {
			var defaults = {
				start: 0,
				end: null,
				step: 1,
				delay: 50,
				txt: '',
			};

			var settings = $.extend( defaults, options || {} );
			var nb_start = settings.start;
			var nb_end   = settings.end;

			$counterElement.text( nb_start + settings.txt );

			// Timer
			// Launches every "settings.delay"
			var counterInterval = setInterval(
				function () {
					// Definition of conditions of arrest
					if ( nb_end !== null && nb_start >= nb_end ) {
						return;
					}

					// incrementation
					nb_start = nb_start + settings.step;

					// Check is ended
					if ( nb_start >= nb_end ) {
						nb_start = nb_end;

						clearInterval( counterInterval );
					}

					// display
					$counterElement.text( nb_start + settings.txt );
				},
				settings.delay
			);
		}
	};

	qodefCore.shortcodes.greenpath_core_counter.qodefCounter = qodefCounter;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_google_map = {};

	$( document ).ready(
		function () {
			qodefGoogleMap.init();
		}
	);

	var qodefGoogleMap = {
		init: function () {
			this.holder = $( '.qodef-google-map' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						qodefGoogleMap.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			if ( typeof window.qodefGoogleMap !== 'undefined' ) {
				window.qodefGoogleMap.init( $currentItem.find( '.qodef-m-map' ) );
			}
		},
	};

	qodefCore.shortcodes.greenpath_core_google_map.qodefGoogleMap = qodefGoogleMap;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_icon = {};

	$( document ).ready(
		function () {
			qodefIcon.init();
		}
	);

	var qodefIcon = {
		init: function () {
			this.icons = $( '.qodef-icon-holder' );

			if ( this.icons.length ) {
				this.icons.each(
					function () {
						qodefIcon.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			qodefIcon.iconHoverColor( $currentItem );
			qodefIcon.iconHoverBgColor( $currentItem );
			qodefIcon.iconHoverBorderColor( $currentItem );
		},
		iconHoverColor: function ( $iconHolder ) {
			if ( typeof $iconHolder.data( 'hover-color' ) !== 'undefined' ) {
				var spanHolder    = $iconHolder.find( 'span' ).length ? $iconHolder.find( 'span' ) : $iconHolder;
				var originalColor = spanHolder.css( 'color' );
				var hoverColor    = $iconHolder.data( 'hover-color' );

				$iconHolder.on(
					'mouseenter',
					function () {
						qodefIcon.changeColor(
							spanHolder,
							'color',
							hoverColor
						);
					}
				);
				$iconHolder.on(
					'mouseleave',
					function () {
						qodefIcon.changeColor(
							spanHolder,
							'color',
							originalColor
						);
					}
				);
			}
		},
		iconHoverBgColor: function ( $iconHolder ) {
			if ( typeof $iconHolder.data( 'hover-background-color' ) !== 'undefined' ) {
				var hoverBackgroundColor    = $iconHolder.data( 'hover-background-color' );
				var originalBackgroundColor = $iconHolder.css( 'background-color' );

				$iconHolder.on(
					'mouseenter',
					function () {
						qodefIcon.changeColor(
							$iconHolder,
							'background-color',
							hoverBackgroundColor
						);
					}
				);
				$iconHolder.on(
					'mouseleave',
					function () {
						qodefIcon.changeColor(
							$iconHolder,
							'background-color',
							originalBackgroundColor
						);
					}
				);
			}
		},
		iconHoverBorderColor: function ( $iconHolder ) {
			if ( typeof $iconHolder.data( 'hover-border-color' ) !== 'undefined' ) {
				var hoverBorderColor    = $iconHolder.data( 'hover-border-color' );
				var originalBorderColor = $iconHolder.css( 'borderTopColor' );

				$iconHolder.on(
					'mouseenter',
					function () {
						qodefIcon.changeColor(
							$iconHolder,
							'border-color',
							hoverBorderColor
						);
					}
				);
				$iconHolder.on(
					'mouseleave',
					function () {
						qodefIcon.changeColor(
							$iconHolder,
							'border-color',
							originalBorderColor
						);
					}
				);
			}
		},
		changeColor: function ( iconElement, cssProperty, color ) {
			iconElement.css(
				cssProperty,
				color
			);
		}
	};

	qodefCore.shortcodes.greenpath_core_icon.qodefIcon = qodefIcon;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_image_gallery = {};

	$( document ).ready(
		function () {
			qodefImageGallery.init();
		}
	);

	const qodefImageGallery = {
		init: function () {
			const $holder = $( '.qodef-image-gallery' );

			if ( $holder.length ) {
				$holder.each( function () {
					const $thisHolder = $( this );

					qodefImageGallery.initItem( $thisHolder );
				} );
			}
		},
		initItem: function ( $holder ) {
			if ( ! qodefCore.html.hasClass( 'touchevents' ) &&  $holder.hasClass('qodef-scroll-gallery') && qodefCore.windowWidth > 1024) {
				qodefImageGallery.initScroll( $holder );
			}
		},
		initScroll: function ( $holder ) {
			gsap.registerPlugin(
				ScrollTrigger
			);

			let $scrollContent = $holder.find('.qodef-grid-inner');

			const tl = gsap.timeline( {
				scrollTrigger: {
					trigger: $holder,
					scrub: 1.5,
					start: () => 'top bottom',
					end: () => 'bottom top',
				},
			} );

			tl
			.to(
				$scrollContent,
				{
					xPercent: -10,
				}
			)

			return tl;
		},
	};

	qodefCore.shortcodes.greenpath_core_image_gallery                    = {};
	qodefCore.shortcodes.greenpath_core_image_gallery.qodefImageGallery = qodefImageGallery;
	qodefCore.shortcodes.greenpath_core_image_gallery.qodefSwiper        = qodef.qodefSwiper;
	qodefCore.shortcodes.greenpath_core_image_gallery.qodefMasonryLayout = qodef.qodefMasonryLayout;
	qodefCore.shortcodes.greenpath_core_image_gallery.qodefMagnificPopup = qodef.qodefMagnificPopup;
	qodefCore.shortcodes.greenpath_core_image_gallery.qodefCustomCursor  = qodefCore.qodefCustomCursor;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_image_with_text                    = {};
	qodefCore.shortcodes.greenpath_core_image_with_text.qodefMagnificPopup = qodef.qodefMagnificPopup;
	qodefCore.shortcodes.greenpath_core_image_with_text.qodefAppear        = qodefCore.qodefAppear;
})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_interactive_link_showcase = {};

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_progress_bar = {};

	$( document ).ready(
		function () {
			qodefProgressBar.init();
		}
	);

	/**
	 * Init progress bar shortcode functionality
	 */
	var qodefProgressBar = {
		init: function () {
			this.holder = $( '.qodef-progress-bar' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						qodefProgressBar.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			var layout = $currentItem.data( 'layout' );

			qodefCore.qodefIsInViewport.check(
				$currentItem,
				function () {
					$currentItem.addClass( 'qodef--init' );

					var $container = $currentItem.find( '.qodef-m-canvas' ),
						data       = qodefProgressBar.generateBarData( $currentItem, layout ),
						number     = $currentItem.data( 'number' ) / 100;

					switch (layout) {
						case 'circle':
							qodefProgressBar.initCircleBar( $container, data, number );
							break;
						case 'semi-circle':
							qodefProgressBar.initSemiCircleBar( $container, data, number );
							break;
						case 'line':
							data = qodefProgressBar.generateLineData( $currentItem, number );
							qodefProgressBar.initLineBar( $container, data );
							break;
						case 'custom':
							qodefProgressBar.initCustomBar( $container, data, number );
							break;
					}
				},
			);
		},
		generateBarData: function ( thisBar, layout ) {
			var activeWidth   = thisBar.data( 'active-line-width' );
			var activeColor   = thisBar.data( 'active-line-color' );
			var inactiveWidth = thisBar.data( 'inactive-line-width' );
			var inactiveColor = thisBar.data( 'inactive-line-color' );
			var easing        = 'linear';
			var duration      = typeof thisBar.data( 'duration' ) !== 'undefined' && thisBar.data( 'duration' ) !== '' ? parseInt( thisBar.data( 'duration' ), 10 ) : 1600;
			var textColor     = thisBar.data( 'text-color' );

			return {
				strokeWidth: activeWidth,
				color: activeColor,
				trailWidth: inactiveWidth,
				trailColor: inactiveColor,
				easing: easing,
				duration: duration,
				svgStyle: {
					width: '100%',
					height: '100%'
				},
				text: {
					style: {
						color: textColor
					},
					autoStyleContainer: false
				},
				from: {
					color: inactiveColor
				},
				to: {
					color: activeColor
				},
				step: function ( state, bar ) {
					if ( layout !== 'custom' ) {
						bar.setText( Math.round( bar.value() * 100 ) + '%' );
					}
				},
			};
		},
		generateLineData: function ( thisBar, number ) {
			var height         = thisBar.data( 'active-line-width' );
			var activeColor    = thisBar.data( 'active-line-color' );
			var inactiveHeight = thisBar.data( 'inactive-line-width' );
			var inactiveColor  = thisBar.data( 'inactive-line-color' );
			var duration       = typeof thisBar.data( 'duration' ) !== 'undefined' && thisBar.data( 'duration' ) !== '' ? parseInt( thisBar.data( 'duration' ), 10 ) : 1600;
			var textColor      = thisBar.data( 'text-color' );

			return {
				percentage: number * 100,
				duration: duration,
				fillBackgroundColor: activeColor,
				backgroundColor: inactiveColor,
				height: height,
				inactiveHeight: inactiveHeight,
				followText: thisBar.hasClass( 'qodef-percentage--floating' ),
				textColor: textColor,
			};
		},
		initCircleBar: function ( $container, data, number ) {
			if ( qodefProgressBar.checkBar( $container ) ) {
				var $bar = new ProgressBar.Circle( $container[0], data );

				$bar.animate( number );
			}
		},
		initSemiCircleBar: function ( $container, data, number ) {
			if ( qodefProgressBar.checkBar( $container ) ) {
				var $bar = new ProgressBar.SemiCircle( $container[0], data );

				$bar.animate( number );
			}
		},
		initCustomBar: function ( $container, data, number ) {
			if ( qodefProgressBar.checkBar( $container ) ) {
				var $bar = new ProgressBar.Path( $container[0], data );

				$bar.set( 0 );
				$bar.animate( number );
			}
		},
		initLineBar: function ( $container, data ) {
			$container.LineProgressbar( data );
		},
		checkBar: function ( $container ) {
			// check if svg is already in container, elementor fix
			return ! $container.find( 'svg' ).length;
		}
	};

	qodefCore.shortcodes.greenpath_core_progress_bar.qodefProgressBar = qodefProgressBar;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_swapping_image_gallery = {};

	$( document ).ready(
		function () {
			qodefSwappingImageGallery.init();
		}
	);

	$( window ).resize (
		function() {
			qodefSwappingImageGallery.init();
		}
	);

	/**
	 * Init progress bar shortcode functionality
	 */
	var qodefSwappingImageGallery = {
		init: function () {
			this.holder = $( '.qodef-swapping-image-gallery' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						qodefSwappingImageGallery.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			let $items   = $currentItem.find( '.qodef-m-item' ),
				$images  = $currentItem.find( '.qodef-item-image' ),
				$gallery = $currentItem.find( '.qodef-e-gallery' );

			if ( $items.length ) {
				$items.first().addClass('qodef--active');

				if ( $images.length ) {
					$gallery.css('height', $.makeArray( $images ).reduce( ( max, current) => Math.max(max, $(current).outerHeight()) , 0 ));
					qodefSwappingImageGallery.setActiveImage( $items.first(), $images );
				}

				$items.on('click', function ( e ) {
					let $active = $(e.target.closest('.qodef-m-item'));
					$items.removeClass('qodef--active');
					$active.addClass('qodef--active');
					qodefSwappingImageGallery.setActiveImage( $active, $images );
				});
			}
		},
		setActiveImage: function ( $active, $images ) {
			let $index = $active.data( 'index' );

			$.makeArray( $images ).map( image => $( image ).removeClass( 'qodef--active' ) );
			let current = $.makeArray( $images ).filter( ( image ) => $( image ).data( 'index' ) === $index );
			$( current ).addClass('qodef--active');
		}
	};

	qodefCore.shortcodes.greenpath_core_swapping_image_gallery.qodefSwappingImageGallery = qodefSwappingImageGallery;

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_tabs = {};

	$( document ).ready(
		function () {
			qodefTabs.init();
		}
	);

	var qodefTabs = {
		init: function () {
			this.holder = $( '.qodef-tabs' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						qodefTabs.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			$currentItem.children( '.qodef-tabs-content' ).each(
				function ( index ) {
					index = index + 1;

					var $that    = $( this ),
						link     = $that.attr( 'id' ),
						$navItem = $that.parent().find( '.qodef-tabs-navigation li:nth-child(' + index + ') a' ),
						navLink  = $navItem.attr( 'href' );

					link = '#' + link;

					if ( link.indexOf( navLink ) > -1 ) {
						$navItem.attr(
							'href',
							link
						);
					}
				}
			);

			$currentItem.addClass( 'qodef--init' ).tabs();
		},
		setHeight ( $holder ) {
			var $navigation      = $holder.find( '.qodef-tabs-navigation' ),
				$content         = $holder.find( '.qodef-tabs-content' ),
				navHeight,
				contentHeight,
				maxContentHeight = 0;

			if ( $navigation.length ) {
				navHeight = $navigation.outerHeight( true );
			}

			if ( $content.length ) {
				$content.each(
					function () {
						contentHeight = $( this ).outerHeight( true );
						maxContentHeight = contentHeight > maxContentHeight ? contentHeight : maxContentHeight;
					}
				)
			}

			$holder.height(navHeight + maxContentHeight);
		}
	};

	qodefCore.shortcodes.greenpath_core_tabs.qodefTabs = qodefTabs;

})( jQuery );

(function ($) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_text_marquee = {};

	$(document).ready(
		function () {
			qodefTextMarquee.init();
		}
	);

	$(window).resize(
		function () {
			qodefTextMarquee.init();
		}
	);

	var qodefTextMarquee = {
		init               : function () {
			this.holder = $('.qodef-text-marquee');

			if (this.holder.length) {
				this.holder.each(
					function () {
						qodefTextMarquee.prepareContent($(this));
						qodefTextMarquee.calculateWidthRatio($(this));
					}
				);
			}
		},
		prepareContent     : function ($currentItem) {
			var $contentInnerCopy = $currentItem.find('.qodef--copy');

			// remove holder init class
			$currentItem.removeClass('qodef--init');

			// remove duplicated content
			if ($contentInnerCopy.length) {
				$contentInnerCopy.remove();
			}
		},
		calculateWidthRatio: function ($currentItem) {
			var $content = $currentItem.find('.qodef-m-content'),
				$contentInner = $content.find('.qodef-m-content-inner'),
				multiplyCoef = $contentInner.outerWidth() > 0 ? Math.ceil($content.outerWidth() / $contentInner.outerWidth()) : 1,
				i;

			if ($contentInner.html().length) {
				// duplicate content at least once
				for (i = 0; i < multiplyCoef; i++) {
					qodefTextMarquee.duplicateContent($content, $contentInner);
				}
			}

			// add holder init class
			$currentItem.addClass('qodef--init');
		},
		duplicateContent   : function ($content, $contentInner) {
			$contentInner.clone().appendTo($content).addClass('qodef--copy');
		},
	};

	qodefCore.shortcodes.greenpath_core_text_marquee.qodefTextMarquee = qodefTextMarquee;

})(jQuery);

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_video_button                    = {};
	qodefCore.shortcodes.greenpath_core_video_button.qodefMagnificPopup = qodef.qodefMagnificPopup;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( window ).on(
		'load',
		function () {
			qodefStickySidebar.init();
		}
	);

	var qodefStickySidebar = {
		init: function () {
			var info = $( '.widget_greenpath_core_sticky_sidebar' );

			if ( info.length && qodefCore.windowWidth > 1024 ) {
				info.wrapper = info.parents( '#qodef-page-sidebar' );
				info.offsetM = info.offset().top - info.wrapper.offset().top;
				info.adj     = 15;

				qodefStickySidebar.callStack( info );

				$( window ).on(
					'resize',
					function () {
						if ( qodefCore.windowWidth > 1024 ) {
							qodefStickySidebar.callStack( info );
						}
					}
				);

				$( window ).on(
					'scroll',
					function () {
						if ( qodefCore.windowWidth > 1024 ) {
							qodefStickySidebar.infoPosition( info );
						}
					}
				);
			}
		},
		calc: function ( info ) {
			var content = $( '.qodef-page-content-section' ),
				headerH = qodefCore.body.hasClass( 'qodef-header-appearance--none' ) ? 0 : parseInt( qodefGlobal.vars.headerHeight, 10 );

			// If posts not found set content to have the same height as the sidebar
			if ( qodefCore.windowWidth > 1024 && content.height() < 100 ) {
				content.css( 'height', info.wrapper.height() - content.height() );
			}

			info.start = content.offset().top;
			info.end   = content.outerHeight();
			info.h     = info.wrapper.height();
			info.w     = info.outerWidth();
			info.left  = info.offset().left;
			info.top   = headerH + qodefGlobal.vars.adminBarHeight - info.offsetM;
			info.data( 'state', 'top' );
		},
		infoPosition: function ( info ) {
			if ( qodefCore.scroll < info.start - info.top && qodefCore.scroll + info.h && info.data( 'state' ) !== 'top' ) {
				gsap.to(
					info.wrapper,
					.1,
					{
						y: 5,
					}
				);
				gsap.to(
					info.wrapper,
					.3,
					{
						y: 0,
						delay: .1,
					}
				);
				info.data( 'state', 'top' );
				info.wrapper.css(
					{
						'position': 'static',
					}
				);
			} else if ( qodefCore.scroll >= info.start - info.top && qodefCore.scroll + info.h + info.adj <= info.start + info.end &&
				info.data( 'state' ) !== 'fixed' ) {
				var c = info.data( 'state' ) === 'top' ? 1 : -1;
				info.data( 'state', 'fixed' );
				info.wrapper.css(
					{
						'position': 'fixed',
						'top': info.top,
						'left': info.left,
						'width': info.w,
					}
				);
				gsap.fromTo(
					info.wrapper,
					.2,
					{
						y: 0
					},
					{
						y: c * 10,
						ease: Power4.easeInOut
					}
				);
				gsap.to(
					info.wrapper,
					.2,
					{
						y: 0,
						delay: .2,
					}
				);
			} else if ( qodefCore.scroll + info.h + info.adj > info.start + info.end && info.data( 'state' ) !== 'bottom' ) {
				info.data( 'state', 'bottom' );
				info.wrapper.css(
					{
						'position': 'absolute',
						'top': info.end - info.h - info.adj,
						'left': 'auto',
						'width': info.w,
					}
				);
				gsap.fromTo(
					info.wrapper,
					.1,
					{
						y: 0
					},
					{
						y: -5,
					}
				);
				gsap.to(
					info.wrapper,
					.3,
					{
						y: 0,
						delay: .1,
					}
				);
			}
		},
		callStack: function ( info ) {
			this.calc( info );
			this.infoPosition( info );
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	var shortcode = 'greenpath_core_blog_list';

	qodefCore.shortcodes[shortcode] = {};

	if ( typeof qodefCore.listShortcodesScripts === 'object' ) {
		$.each(
			qodefCore.listShortcodesScripts,
			function ( key, value ) {
				qodefCore.shortcodes[shortcode][key] = value;
			}
		);
	}

	qodefCore.shortcodes[shortcode].qodefResizeIframes = qodef.qodefResizeIframes;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefExtendedDropdownOpener.init();
		}
	);

	var qodefExtendedDropdownOpener = {
		init: function () {
			var opener = $('.qodef-extended-dropdown-menu:not(.qodef-dropdown-always-opened)');

			if ( opener.length ) {

				opener.on('mouseenter', function(){
					opener.addClass('qodef-dropdown-hovered');
				});

				opener.on('mouseleave', function(){
					opener.removeClass('qodef-dropdown-hovered');
				});
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	var fixedHeaderAppearance = {
		showHideHeader: function ( $pageOuter, $header ) {
			if ( qodefCore.windowWidth > 1200 ) {
				if ( qodefCore.scroll <= 0 ) {
					qodefCore.body.removeClass( 'qodef-header--fixed-display' );
					$pageOuter.css( 'padding-top', '0' );
					$header.css( 'margin-top', '0' );
				} else {
					qodefCore.body.addClass( 'qodef-header--fixed-display' );
					$pageOuter.css( 'padding-top', parseInt( qodefGlobal.vars.headerHeight + qodefGlobal.vars.topAreaHeight ) + 'px' );
					$header.css( 'margin-top', parseInt( qodefGlobal.vars.topAreaHeight ) + 'px' );
				}
			}
		},
		init: function () {

			if ( ! qodefCore.body.hasClass( 'qodef-header--vertical' ) ) {
				var $pageOuter = $( '#qodef-page-outer' ),
					$header    = $( '#qodef-page-header' );

				fixedHeaderAppearance.showHideHeader( $pageOuter, $header );

				$( window ).scroll(
					function () {
						fixedHeaderAppearance.showHideHeader( $pageOuter, $header );
					}
				);

				$( window ).resize(
					function () {
						$pageOuter.css( 'padding-top', '0' );
						fixedHeaderAppearance.showHideHeader( $pageOuter, $header );
					}
				);
			}
		}
	};

	qodefCore.fixedHeaderAppearance = fixedHeaderAppearance.init;

})( jQuery );

(function ( $ ) {
	'use strict';

	var stickyHeaderAppearance = {
		header: '',
		docYScroll: 0,
		init: function () {
			var displayAmount = stickyHeaderAppearance.displayAmount();

			// Set variables
			stickyHeaderAppearance.header 	  = $( '.qodef-header-sticky' );
			stickyHeaderAppearance.docYScroll = $( document ).scrollTop();

			// Set sticky visibility
			stickyHeaderAppearance.setVisibility( displayAmount );

			$( window ).scroll(
				function () {
					stickyHeaderAppearance.setVisibility( displayAmount );
				}
			);
		},
		displayAmount: function () {
			if ( qodefGlobal.vars.qodefStickyHeaderScrollAmount !== 0 ) {
				return parseInt( qodefGlobal.vars.qodefStickyHeaderScrollAmount, 10 );
			} else {
				return parseInt( qodefGlobal.vars.headerHeight + qodefGlobal.vars.adminBarHeight, 10 );
			}
		},
		setVisibility: function ( displayAmount ) {
			var isStickyHidden = qodefCore.scroll < displayAmount;

			if ( stickyHeaderAppearance.header.hasClass( 'qodef-appearance--up' ) ) {
				var currentDocYScroll = $( document ).scrollTop();

				isStickyHidden = (currentDocYScroll > stickyHeaderAppearance.docYScroll && currentDocYScroll > displayAmount) || (currentDocYScroll < displayAmount);

				stickyHeaderAppearance.docYScroll = $( document ).scrollTop();
			}

			stickyHeaderAppearance.showHideHeader( isStickyHidden );
		},
		showHideHeader: function ( isStickyHidden ) {
			if ( isStickyHidden ) {
				qodefCore.body.removeClass( 'qodef-header--sticky-display' );
			} else {
				qodefCore.body.addClass( 'qodef-header--sticky-display' );
			}
		},
	};

	qodefCore.stickyHeaderAppearance = stickyHeaderAppearance.init;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefSideAreaMobileHeader.init();
		}
	);

	var qodefSideAreaMobileHeader = {
		init: function () {
			var $holder = $( '#qodef-side-area-mobile-header' );

			if ( $holder.length && qodefCore.body.hasClass( 'qodef-mobile-header--side-area' ) ) {
				if( ! $('.qodef-woo-side-area-menu-cover').length ) {
					$( '#qodef-page-wrapper' ).prepend( '<div class="qodef-woo-side-area-menu-cover"/>' );
				}

				var $navigation = $holder.find( '.qodef-m-navigation' );

				qodefSideAreaMobileHeader.initOpenerTrigger( $holder, $navigation );
				qodefSideAreaMobileHeader.initNavigationClickToggle( $navigation );

				if ( typeof qodefCore.qodefPerfectScrollbar === 'object' ) {
					qodefCore.qodefPerfectScrollbar.init( $holder );
				}
			}
		},
		initOpenerTrigger: function ( $holder, $navigation ) {
			var $openerIcon = $( '.qodef-side-area-mobile-header-opener' ),
				$closeIcon  = $holder.children( '.qodef-m-close' );

			if ( $openerIcon.length && $navigation.length ) {
				$openerIcon.on(
					'tap click',
					function ( e ) {
						e.stopPropagation();
						e.preventDefault();

						qodefCore.qodefScroll.disable();

						if ( qodefCore.body.hasClass( 'qodef-woo-side-area-menu--opened' ) ) {
							qodefCore.body.removeClass( 'qodef-woo-side-area-menu--opened' );
						} else {
							qodefCore.body.addClass( 'qodef-woo-side-area-menu--opened' );
						}

						if ( $holder.hasClass( 'qodef--opened' ) ) {
							$holder.removeClass( 'qodef--opened' );
						} else {
							$holder.addClass( 'qodef--opened' );
						}
					}
				);
			}

			$closeIcon.on(
				'tap click',
				function ( e ) {
					e.stopPropagation();
					e.preventDefault();

					qodefCore.qodefScroll.enable();

					if ( qodefCore.body.hasClass( 'qodef-woo-side-area-menu--opened' ) ) {
						qodefCore.body.removeClass( 'qodef-woo-side-area-menu--opened' );
					}

					if ( $holder.hasClass( 'qodef--opened' ) ) {
						$holder.removeClass( 'qodef--opened' );
					}
				}
			);
		},
		initNavigationClickToggle: function ( $navigation ) {
			var $menuItems = $navigation.find( 'ul li.menu-item-has-children' );

			$menuItems.each(
				function () {
					var $thisItem        = $( this ),
						$elementToExpand = $thisItem.find( ' > .qodef-drop-down-second, > ul' ),
						$dropdownOpener  = $thisItem.find( '> .qodef-menu-item-arrow' ),
						slideUpSpeed     = 'fast',
						slideDownSpeed   = 'slow';

					$dropdownOpener.on(
						'click tap',
						function ( e ) {
							e.preventDefault();
							e.stopPropagation();

							if ( $elementToExpand.is( ':visible' ) ) {
								$thisItem.removeClass( 'qodef-menu-item--open' );
								$elementToExpand.slideUp( slideUpSpeed );
							} else if ( $dropdownOpener.parent().parent().children().hasClass( 'qodef-menu-item--open' ) && $dropdownOpener.parent().parent().parent().hasClass( 'qodef-vertical-menu' ) ) {
								$thisItem.parent().parent().children().removeClass( 'qodef-menu-item--open' );
								$thisItem.parent().parent().children().find( ' > .qodef-drop-down-second' ).slideUp( slideUpSpeed );

								$thisItem.addClass( 'qodef-menu-item--open' );
								$elementToExpand.slideDown( slideDownSpeed );
							} else {

								if ( ! $thisItem.parents( 'li' ).hasClass( 'qodef-menu-item--open' ) ) {
									$menuItems.removeClass( 'qodef-menu-item--open' );
									$menuItems.find( ' > .qodef-drop-down-second, > ul' ).slideUp( slideUpSpeed );
								}

								if ( $thisItem.parent().parent().children().hasClass( 'qodef-menu-item--open' ) ) {
									$thisItem.parent().parent().children().removeClass( 'qodef-menu-item--open' );
									$thisItem.parent().parent().children().find( ' > .qodef-drop-down-second, > ul' ).slideUp( slideUpSpeed );
								}

								$thisItem.addClass( 'qodef-menu-item--open' );
								$elementToExpand.slideDown( slideDownSpeed );
							}
						}
					);
				}
			);
		},
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefSearchCoversHeader.init();
		}
	);

	var qodefSearchCoversHeader = {
		init: function () {
			var $searchOpener = $( 'a.qodef-search-opener' ),
				$searchForm   = $( '.qodef-search-cover-form' ),
				$searchClose  = $searchForm.find( '.qodef-m-close' );

			if ( $searchOpener.length && $searchForm.length ) {
				$searchOpener.on(
					'click',
					function ( e ) {
						e.preventDefault();
						qodefSearchCoversHeader.openCoversHeader( $searchForm );
					}
				);
				$searchClose.on(
					'click',
					function ( e ) {
						e.preventDefault();
						qodefSearchCoversHeader.closeCoversHeader( $searchForm );
					}
				);
			}
		},
		openCoversHeader: function ( $searchForm ) {
			qodefCore.body.addClass( 'qodef-covers-search--opened qodef-covers-search--fadein' );
			qodefCore.body.removeClass( 'qodef-covers-search--fadeout' );

			setTimeout(
				function () {
					$searchForm.find( '.qodef-m-form-field' ).focus();
				},
				600
			);
		},
		closeCoversHeader: function ( $searchForm ) {
			qodefCore.body.removeClass( 'qodef-covers-search--opened qodef-covers-search--fadein' );
			qodefCore.body.addClass( 'qodef-covers-search--fadeout' );

			setTimeout(
				function () {
					$searchForm.find( '.qodef-m-form-field' ).val( '' );
					$searchForm.find( '.qodef-m-form-field' ).blur();
					qodefCore.body.removeClass( 'qodef-covers-search--fadeout' );
				},
				300
			);
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefSearchFullscreen.init();
		}
	);

	var qodefSearchFullscreen = {
		init: function () {
			var $searchOpener = $( 'a.qodef-search-opener' ),
				$searchHolder = $( '.qodef-fullscreen-search-holder' ),
				$searchClose  = $searchHolder.find( '.qodef-m-close' );

			if ( $searchOpener.length && $searchHolder.length ) {
				$searchOpener.on(
					'click',
					function ( e ) {
						e.preventDefault();
						if ( qodefCore.body.hasClass( 'qodef-fullscreen-search--opened' ) ) {
							qodefSearchFullscreen.closeFullscreen( $searchHolder );
						} else {
							qodefSearchFullscreen.openFullscreen( $searchHolder );
						}
					}
				);
				$searchClose.on(
					'click',
					function ( e ) {
						e.preventDefault();
						qodefSearchFullscreen.closeFullscreen( $searchHolder );
					}
				);

				//Close on escape
				$( document ).keyup(
					function ( e ) {
						if ( e.keyCode === 27 && qodefCore.body.hasClass( 'qodef-fullscreen-search--opened' ) ) { //KeyCode for ESC button is 27
							qodefSearchFullscreen.closeFullscreen( $searchHolder );
						}
					}
				);
			}
		},
		openFullscreen: function ( $searchHolder ) {
			qodefCore.body.removeClass( 'qodef-fullscreen-search--fadeout' );
			qodefCore.body.addClass( 'qodef-fullscreen-search--opened qodef-fullscreen-search--fadein' );

			setTimeout(
				function () {
					$searchHolder.find( '.qodef-m-form-field' ).focus();
				},
				900
			);

			qodefCore.qodefScroll.disable();
		},
		closeFullscreen: function ( $searchHolder ) {
			qodefCore.body.removeClass( 'qodef-fullscreen-search--opened qodef-fullscreen-search--fadein' );
			qodefCore.body.addClass( 'qodef-fullscreen-search--fadeout' );

			setTimeout(
				function () {
					$searchHolder.find( '.qodef-m-form-field' ).val( '' );
					$searchHolder.find( '.qodef-m-form-field' ).blur();
					qodefCore.body.removeClass( 'qodef-fullscreen-search--fadeout' );
				},
				300
			);

			qodefCore.qodefScroll.enable();
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefSearch.init();
		}
	);

	var qodefSearch = {
		init: function () {
			this.search = $( 'a.qodef-search-opener' );

			if ( this.search.length ) {
				this.search.each(
					function () {
						var $thisSearch = $( this );

						qodefSearch.searchHoverColor( $thisSearch );
					}
				);
			}
		},
		searchHoverColor: function ( $searchHolder ) {
			if ( typeof $searchHolder.data( 'hover-color' ) !== 'undefined' ) {
				var hoverColor    = $searchHolder.data( 'hover-color' ),
					originalColor = $searchHolder.css( 'color' );

				$searchHolder.on(
					'mouseenter',
					function () {
						$searchHolder.css( 'color', hoverColor );
					}
				).on(
					'mouseleave',
					function () {
						$searchHolder.css( 'color', originalColor );
					}
				);
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefPredefinedSpinner.init();
		}
	);

	$( window ).on(
		'elementor/frontend/init',
		function () {
			const isEditMode = Boolean( elementorFrontend.isEditMode() );

			if ( isEditMode ) {
				qodefPredefinedSpinner.init( isEditMode );
			}
		}
	);

	const qodefPredefinedSpinner = {
		init( isEditMode ) {
			const $holder = $( '#qodef-page-spinner.qodef-layout--predefined' );

			if ( $holder.length ) {
				if ( isEditMode ) {
				} else {
					qodefPredefinedSpinner.animateSpinner( $holder );
				}
			}
		},
		animateSpinner( $holder ) {
			let $imageHolder = $holder.find('.qodef-m-spinner-bg-image'),
				$imagesInner = $holder.find('.qodef-m-spinner-bg-image-inner');

			var tl = gsap.timeline(
				{
					paused: true,
					onStart: () => {
						$holder.addClass( 'qodef--init' );
					},
				}
			);

			var tlOut = gsap.timeline(
				{
					paused: true,
					onStart: () => {
						let appeared = $( '.qodef--appeared' );

						appeared.removeClass( 'qodef--appeared' );
					},
				}
			);

			tlOut
			.to(
				$imageHolder,
				{
					duration: 1.8,
					opacity: 1,
				},
				'0.2'
			)
			.fromTo(
				$imagesInner,
				{
					xPercent: 0,
				},
				{
					xPercent: -50,
					repeat: -1,
					yoyo: true,
					ease: 'none',
					duration: 18
				},
				'0'
			)
			.to(
				$holder,
				{
					onStart: () => {
						qodefCore.qodefAppear.init();
					},
				},
				'3.1'
			)
			.to(
				$holder,
				{
					'--qode-clip': 100,
					duration: 1.5,
					ease: 'power3.inOut',
					onComplete: () => {
						tlOut.pause();
					},
				},
				'2.4'
			);

			tl
			.from(
				$holder,
				{
					duration: 1,
				},
			)
			.to(
				$holder,
				{
					duration: .1,
					repeat: -1,
					onRepeat: () => {
						if ( qodefCore.qodefSpinner.windowLoaded ) {
							tl.pause();
							tlOut.play();
						} else {
							tl.restart();
						}
					},
				},
				'.8'
			);

			tl.play();
		},
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function() {
			qodefProgressBarSpinner.init();
		}
	);

	$( window ).on(
		'load',
		function () {
			qodefProgressBarSpinner.windowLoaded = true;
			qodefProgressBarSpinner.completeAnimation();
		}
	);

	$( window ).on(
		'elementor/frontend/init',
		function () {
			var isEditMode = Boolean( elementorFrontend.isEditMode() );

			if ( isEditMode ) {
				qodefProgressBarSpinner.init( isEditMode );
			}
		}
	);

	var qodefProgressBarSpinner = {
		holder: '',
		windowLoaded: false,
		percentNumber: 0,
		init: function ( isEditMode ) {
			this.holder = $( '#qodef-page-spinner.qodef-layout--progress-bar' );

			if ( this.holder.length ) {
				qodefProgressBarSpinner.animateSpinner( this.holder, isEditMode );
			}
		},
		animateSpinner: function ( $holder, isEditMode ) {
			var $numberHolder = $holder.find( '.qodef-m-spinner-number-label' ),
				$spinnerLine  = $holder.find( '.qodef-m-spinner-line-front' );

			$spinnerLine.animate(
				{ 'width': '100%' },
				10000,
				'linear'
			);

			var numberInterval = setInterval(
				function () {
					qodefProgressBarSpinner.animatePercent( $numberHolder, qodefProgressBarSpinner.percentNumber );

					if ( qodefProgressBarSpinner.windowLoaded ) {
						clearInterval( numberInterval );
					}
				},
				100
			);

			if ( isEditMode ) {
				qodefProgressBarSpinner.fadeOutLoader( $holder );
			}
		},
		completeAnimation: function () {
			var $holder = qodefProgressBarSpinner.holder.length ? qodefProgressBarSpinner.holder : $( '#qodef-page-spinner.qodef-layout--progress-bar' );

			var numberIntervalFastest = setInterval(
				function () {

					if ( qodefProgressBarSpinner.percentNumber >= 100 ) {
						clearInterval( numberIntervalFastest );

						$holder.find( '.qodef-m-spinner-line-front' ).stop().animate(
							{ 'width': '100%' },
							500
						);

						$holder.addClass( 'qodef--finished' );

						setTimeout(
							function () {
								qodefProgressBarSpinner.fadeOutLoader( $holder );
							},
							600
						);
					} else {
						qodefProgressBarSpinner.animatePercent(
							$holder.find( '.qodef-m-spinner-number-label' ),
							qodefProgressBarSpinner.percentNumber
						);
					}
				},
				6
			);
		},
		animatePercent: function ( $numberHolder, percentNumber ) {
			if ( percentNumber < 100 ) {
				percentNumber += 5;
				$numberHolder.text( percentNumber );

				qodefProgressBarSpinner.percentNumber = percentNumber;
			}
		},
		fadeOutLoader: function ( $holder, speed, delay, easing ) {
			speed  = speed ? speed : 600;
			delay  = delay ? delay : 0;
			easing = easing ? easing : 'swing';

			$holder.delay( delay ).fadeOut( speed, easing );

			$( window ).on(
				'bind',
				'pageshow',
				function ( event ) {
					if ( event.originalEvent.persisted ) {
						$holder.fadeOut( speed, easing );
					}
				}
			);
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefTextualSpinner.init();
		}
	);

	$( window ).on(
		'load',
		function () {
			qodefTextualSpinner.windowLoaded = true;
		}
	);

	$( window ).on(
		'elementor/frontend/init',
		function () {
			var isEditMode = Boolean( elementorFrontend.isEditMode() );

			if ( isEditMode ) {
				qodefTextualSpinner.init( isEditMode );
			}
		}
	);

	var qodefTextualSpinner = {
		init ( isEditMode ) {
			var $holder = $( '#qodef-page-spinner.qodef-layout--textual' );

			if ( $holder.length ) {
				if ( isEditMode ) {
					qodefTextualSpinner.fadeOutLoader( $holder );
				} else {
					qodefTextualSpinner.splitText( $holder );
				}
			}
		},
		splitText ( $holder ) {
			var $textHolder = $holder.find( '.qodef-m-text' );

			if ( $textHolder.length ) {
				var text     = $textHolder.text().trim(),
					chars    = text.split( '' ),
					cssClass = '';

				$textHolder.empty();

				chars.forEach(
					( element ) => {
						cssClass = (element === ' ' ? 'qodef-m-empty-char' : ' ');
						$textHolder.append( '<span class="qodef-m-char ' + cssClass + '">' + element + '</span>' );
					}
				);

				setTimeout(
					() => {
						qodefTextualSpinner.animateSpinner( $holder );
					}, 100
				);
			}
		},
		animateSpinner ( $holder ) {
			$holder.addClass( 'qodef--init' );

			function animationLoop ( animationProps ) {
				var $chars      = $holder.find( '.qodef-m-char' ),
					charsLength = $chars.length - 1;

				if ( $chars.length ) {
					$chars.each(
						( index, element ) => {
							var $thisChar = $( element );

							setTimeout(
								() => {
									$thisChar.animate(
									    animationProps.type,
										animationProps.duration,
										animationProps.easing,
										() => {
											if ( index === charsLength ) {
												if ( 1 === animationProps.repeat ) {
													animationLoop(
													    {
                                                            type: { opacity: 0 },
                                                            duration: 1200,
                                                            easing: 'swing',
                                                            delay: 0,
                                                            repeat: 0,
                                                        }
													);
												} else {
													if ( ! qodefTextualSpinner.windowLoaded ) {
														animationLoop(
														    {
                                                                type: { opacity: 1 },
                                                                duration: 1800,
                                                                easing: 'swing',
                                                                delay: 160,
                                                                repeat: 1,
                                                            }
														);
													} else {
														qodefTextualSpinner.fadeOutLoader(
															$holder,
															600,
															0,
															'swing'
														);

														setTimeout(
															() => {
																var $revSlider = $( '.qodef-after-spinner-rev rs-module' );

																if ( $revSlider.length ) {
																	$revSlider.revstart();
																}
															}, 800
														);
													}
												}
											}
										}
									);
								}, index * animationProps.delay
							);
						}
					);
				}
			}

			animationLoop (
			    {
                    type: { opacity: 1 },
                    duration: 1800,
                    easing: 'swing',
                    delay: 160,
                    repeat: 1,
                }
			);
		},
		fadeOutLoader( $holder, speed, delay, easing ) {
			speed  = speed ? speed : 500;
			delay  = delay ? delay : 0;
			easing = easing ? easing : 'swing';

			if ( $holder.length ) {
				$holder.delay( delay ).fadeOut( speed, easing );

				$( window ).on(
					'bind',
					'pageshow',
					function( event ) {

						if ( event.originalEvent.persisted ) {
							$holder.fadeOut( speed, easing );
						}
					}
				);
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_instagram_list = {};

	$( document ).ready(
		function () {
			qodefInstagram.init();
		}
	);

	var qodefInstagram = {
		init: function () {
			this.holder = $( '.qodef-instagram-list #sb_instagram' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {

						if ( $( this ).parent().hasClass( 'qodef-instagram-columns' ) ) {
							var $imagesHolder  = $( this ).find( '#sbi_images' ),
								$images        = $imagesHolder.find( '.sbi_item.sbi_type_image, .sbi_item.sbi_type_carousel' ),
								initialPadding = $imagesHolder.css( 'padding' );

							// remove some unnecessary paddings
							$imagesHolder.css('padding', '0');
							$imagesHolder.css('margin', '-' + initialPadding);
							$imagesHolder.css('width', 'calc(100% + ' + ( initialPadding) + ' + ' + ( initialPadding) + ')');

							$images.attr('style', 'padding: ' + initialPadding + '!important');
						} else if ( $( this ).parent().hasClass( 'qodef-instagram-slider' ) ) {
							qodefInstagram.initSlider( $( this ) );
						}
					}
				);
			}
		},
		initSlider: function ( $currentItem, $initAllItems ) {

			var $imagesHolder  = $currentItem.find( '#sbi_images' ),
				$images        = $currentItem.find( '.sbi_item.sbi_type_image' ),
				initialPadding = $imagesHolder.css( 'padding' );

			// remove some unnecessary paddings
			$imagesHolder.css('padding', '0');
			$images.css('padding', '0');

			// items will inherit this margin
			$imagesHolder.attr('style', 'margin-right: ' + (parseInt( initialPadding ) * 2) + 'px !important');

			var sliderOptions = {};

			sliderOptions.spaceBetween      = parseInt( initialPadding ) * 2;
			sliderOptions.customStages      = true;
			sliderOptions.slidesPerView     = $currentItem.data( 'cols' ) !== undefined && $currentItem.data( 'cols' ) !== '' ? $currentItem.data( 'cols' ) : 3;
			sliderOptions.slidesPerView1200 = $currentItem.data( 'cols' ) !== undefined && $currentItem.data( 'cols' ) !== '' ? $currentItem.data( 'cols' ) : 3;
			sliderOptions.slidesPerView880  = $currentItem.data( 'colstablet' ) !== undefined && $currentItem.data( 'colstablet' ) !== '' ? $currentItem.data( 'colstablet' ) : 2;
			sliderOptions.slidesPerView680  = $currentItem.data( 'colsmobile' ) !== undefined && $currentItem.data( 'colsmobile' ) !== '' ? $currentItem.data( 'colsmobile' ) : 1;

			$currentItem.attr( 'data-options', JSON.stringify(sliderOptions) );

			$imagesHolder.addClass( 'swiper-wrapper' );

			if ( $images.length ) {
				$images.each(
					function () {
						$( this ).addClass( 'qodef-e qodef-image-wrapper swiper-slide' );
					}
				);
			}

			if ( typeof qodef.qodefSwiper === 'object' ) {

				if ( false === $initAllItems ) {
					qodef.qodefSwiper.initSlider( $currentItem );
				} else {
					qodef.qodefSwiper.init( $currentItem );
				}
			}
		},
	};

	qodefCore.shortcodes.greenpath_core_instagram_list.qodefInstagram = qodefInstagram;
	qodefCore.shortcodes.greenpath_core_instagram_list.qodefSwiper    = qodef.qodefSwiper;

})( jQuery );

(function ( $ ) {
	'use strict';

	/*
	 **	Re-init scripts on gallery loaded
	 */
	$( document ).on(
		'yith_wccl_product_gallery_loaded',
		function () {

			if ( typeof qodefCore.qodefWooMagnificPopup === 'function' ) {
				qodefCore.qodefWooMagnificPopup.init();
			}
		}
	);

})( jQuery );

(function ( $ ) {
	'use strict';

	$('body').on( 'yith_woocompare_open_popup', function() {
		var $item = $('.compare-list .remove a'),
			$place = $('.compare-list .add-to-cart .button'),
			$holder = $('#cboxLoadedContent iframe');

		$('#cboxLoadedContent').contents().find('html').css('display','none');
	});

})( jQuery );


(function ($) {
	'use strict';

	$(document).on(
		'ready',
		function() {
			qodefYithQuickView.init();
		}
	);

	$(document).on(
		'qv_loader_stop',
		function () {
			qodefYithDescription.init();
		}
	);

	$(document).on(
		'qv_loader_stop qv_variation_gallery_loaded',
		function () {
			qodefYithSelect2.init();
		}
	);

	var qodefYithSelect2 = {
		init: function (settings) {
			this.holder = [];
			this.holder.push(
				{
					holder: $( '#yith-quick-view-modal .variations select' ),
					options: {
						minimumResultsForSearch: Infinity
					}
				}
			);

			// Allow overriding the default config
			$.extend(
				this.holder,
				settings
			);

			if ( typeof this.holder === 'object' ) {
				$.each(
					this.holder,
					function ( key, value ) {
						qodefYithSelect2.createSelect2(
							value.holder,
							value.options
						);
					}
				);
			}
		},
		createSelect2: function ($holder, options) {
			if (typeof $holder.select2 === 'function') {
				$holder.select2(options);
			}
		}
	};

	var qodefYithQuickView = {
		init: function() {
			var $quickView        = $( '#yith-quick-view-modal .yith-wcqv-wrapper' ),
				$quickViewOpener  = $( '.yith-wcqv-button' ),
				$quickViewClose   = $( '#yith-quick-view-close' ),
				$quickViewOverlay = $( '.yith-quick-view-overlay' );

			if( $quickView.length ) {
				$quickView.off().each(
					function () {
						// Open Side Area
						$quickViewOpener.on(
							'click',
							function () {
								if ( ! qodefCore.body.hasClass( 'qodef-woo-quick-view--opened' ) ) {
									qodefYithQuickView.openQuickView( $quickView );
									qodefYithQuickView.trigger( $quickView );

									$( document ).keyup(
										function ( e ) {
											if ( e.keyCode === 27 ) {
												qodefYithSelect2.closeQuickView( $quickView );
											}
										}
									);
								} else {
									qodefYithQuickView.closeQuickView( $quickView );
								}
							}
						);

						$quickViewClose.on(
							'click',
							function () {
								qodefYithQuickView.closeQuickView( $quickView );
							}
						);

						$quickViewOverlay.on(
							'click',
							function () {
								qodefYithQuickView.closeQuickView( $quickView );
							}
						);
					}
				);
			}
		},
		trigger: function ( $holder ) {
			var $items = $holder.find('.yith-wcqv-main:not(.ps)');
			if ($items.length && typeof qodefCore.qodefPerfectScrollbar === 'object') {
				qodefCore.qodefPerfectScrollbar.init($items);
			}
		},
		openQuickView: function ( $holder ) {
			qodefCore.qodefScroll.disable();
			qodefCore.body.addClass( 'qodef-woo-quick-view--opened' );

			$holder.addClass('qodef--opened');
		},
		closeQuickView: function ( $holder ) {
			qodefCore.qodefScroll.enable();
			qodefCore.body.removeClass( 'qodef-woo-quick-view--opened' );

			if ($holder.hasClass('qodef--opened')) {
				$holder.removeClass('qodef--opened');
			}
		}
	};

	var qodefYithDescription = {
		init: function () {
			var description = $( '.woocommerce-product-details__short-description' );

			if ( description.length ) {
				description.each(
					function () {
						var currentDescription = $( this );

						if ( currentDescription.height() > 66 ) {
							currentDescription.addClass( 'qodef--expand-description' );
							currentDescription.on(
								'click',
								function () {
									currentDescription.toggleClass( 'qodef-fullheight' );
								}
							);
						}
					}
				);
			}
		}
	};

})(jQuery);

(function ($) {
    'use strict';

    qodefCore.shortcodes.greenpath_core_product_cart_showcase = {};

    $( document ).ready(
        function () {
            qodefProductCartShowcase.init();
        }
    );

    var qodefProductCartShowcase = {
        init: function () {
            var $holder = $('.qodef-product-cart-showcase'),
                $button   = $holder.find('.qodef-add-all-to-cart-button'),
                $checkbox = $holder.find('.qodef-m-order-product input[type="checkbox"]' );

            if ( $holder.length ) {
                $checkbox.each( function (e) {
                    var $thisItem = $( this );

                    $thisItem.on( 'change', function() {
                        $thisItem.attr( "checked", ! $thisItem.attr( "checked" ) );
                        qodefProductCartShowcase.updateProductIds($holder);
                    } );
                } );
                $button.on(
                    'click',
                    function (e) {
                        e.preventDefault();
                        qodefProductCartShowcase.updateWooCart($holder);
                    }
                );
            }
        },
        updateProductIds: function( $holder ) {
            var productIds    = document.getElementById('product-cart-showcase-ids'),
                  $checkbox     = $holder.find( '.qodef-m-order-product input[type="checkbox"]' ),
                  checkedArray  = [];

             $checkbox.each( function (e) {
                 var $thisItem = $( this );

                if( $thisItem.attr( 'checked' ) && 'checked' === $thisItem.attr( 'checked' ) ) {
                    checkedArray.push( $thisItem.attr( 'value' ) );
                }

                 productIds.value = checkedArray.join(',');
             } );
        },
        updateWooCart: function ( $holder ) {
            var wooDropdownCart        = $('.widget_greenpath_core_woo_dropdown_cart'),
                wooDropdownOpener        = wooDropdownCart.find('.qodef-m-opener'),
                wooDropdownContent       = wooDropdownCart.find('.qodef-widget-dropdown-cart-content'),
                addAllProductsButton     = $('.qodef-add-all-to-cart-button'),
                addAllProductsButtonText = addAllProductsButton.find('.qodef-m-text'),
                productIds               = document.getElementById('product-cart-showcase-ids');

            $.ajax({
                type: 'POST',
                url: qodefGlobal.vars.restUrl + qodefGlobal.vars.productShowcaseWooCartRestRoute,
                data: {
                    'product-cart-showcase-ids': productIds.value
                },
                dataType: 'html',
                success: function (response) {
                    var newData = JSON.parse(response);

                    wooDropdownOpener.html( newData.data.woo_dropdown_opener );
                    wooDropdownContent.html( newData.data.woo_dropdown_content );
                    addAllProductsButtonText.html( newData.data.success_button_text );
                    addAllProductsButton.addClass( 'added' );
                    addAllProductsButton.attr( 'href', newData.data.success_button_url );

                    // disable preventDefault
                    addAllProductsButton.unbind('click').click();
                },
                complete: function () {
                    console.log('complete');
                }
            });
        },
        doAjaxRequest: function (id, type, buttonWrapper) {
            var productValues = document.getElementById('product-cart-showcase-ids'),
                price = $('.qodef-m-order-price'),
                quantity = $('.qodef-m-order-quantity');

            var productValuesArray = productValues.value.split(','),
                inArray = jQuery.inArray(id, productValuesArray);

            if ( inArray ) {
                productValues.value = productValuesArray.join();

                $.ajax({
                    type: 'POST',
                    url: qodefGlobal.vars.restUrl + qodefGlobal.vars.productShowcaseCartRestRoute,
                    data: {
                        'product-cart-showcase-ids': productValues.value
                    },
                    dataType: 'html',
                    success: function (response) {
                        var newData = JSON.parse(response);
                              price.html(newData.data.cart_total_amount);
                              quantity.html(newData.data.cart_total_quantity);
                    },
                    complete: function () {
                        console.log('complete');
                    }
                });
            }
        },
    };

    qodefCore.shortcodes.greenpath_core_product_cart_showcase.qodefProductCartShowcase = qodefProductCartShowcase;

})(jQuery);
(function ( $ ) {
	'use strict';

	$(document).ready(
		function () {
			qodefProductCategoryList.init();
		}
	);

	var qodefProductCategoryList = {
		init: function () {
			var $categoryList = $( '.qodef-woo-product-category-list.qodef--alternate-image' );

			if ( $categoryList.length ) {
				$categoryList.each(
					function() {
						var $thisList = $( this ),
							$listItem = $thisList.find( '.product-category' ),
							minHeight = 0;

						$listItem.each(
							function() {
								var itemWidth  = $( this ).outerWidth();

								if( itemWidth > minHeight ) {
									minHeight = itemWidth;
								}
							}
						).each(
							function() {
								$( this ).css( 'height', minHeight );
							}
						);
					}
				);
			}
		},
	};

	qodefCore.shortcodes.greenpath_core_product_category_list                    = {};
	qodefCore.shortcodes.greenpath_core_product_category_list.qodefMasonryLayout = qodef.qodefMasonryLayout;
	qodefCore.shortcodes.greenpath_core_product_category_list.qodefSwiper        = qodef.qodefSwiper;

})( jQuery );

(function ( $ ) {
	'use strict';

	var shortcode = 'greenpath_core_product_list';

	qodefCore.shortcodes[shortcode] = {};

	if ( typeof qodefCore.listShortcodesScripts === 'object' ) {
		$.each(
			qodefCore.listShortcodesScripts,
			function ( key, value ) {
				qodefCore.shortcodes[shortcode][key] = value;
			}
		);
	}
	
	$(document).ready(
		function () {
			qodefProductList.init();
			qodefProductFilterContent.init();
			qodefProductFilterMobile.init();
			qodefProductFilter.init();
		}
	);

	var qodefProductList = {
		init: function () {
			var $productList = $('.qodef-woo-product-list');

			if( $productList.length ) {
				$productList.each( function() {
					var $thisList  = $( this ),
						$wishlist  = $thisList.find( '.yith-wcwl-add-to-wishlist' ),
						$quickView = $thisList.find( '.yith-wcqv-button' ),
						$compare   = $thisList.find( 'a.button.compare' );

					if ( $thisList.hasClass( 'qodef--no-wishlist' ) ) {
						$wishlist.remove();
					}

					if ( $thisList.hasClass( 'qodef--no-quickview' ) ) {
						$quickView.remove();
					}

					if ( $thisList.hasClass( 'qodef--no-compare' ) ) {
						$compare.remove();
					}
				});
			}
		},
	}

	var qodefProductFilterContent = {
		holderHeight: 0,
		init: function () {
			var $productList = $('.qodef-woo-product-list.qodef-filter--advanced');

			if ($productList.length) {
				$productList.each( function( ) {
					var $thisList = $( this ),
						$filterContent = $thisList.find('.qodef-product-list-filter-horizontal, .qodef-product-list-filter-vertical'),
						$showMore = $thisList.find('.qodef-filter-show-more, .qodef-filter-show-less'),
						$filterOpener = $thisList.find('.qodef-product-list-filter-holder .qodef-filter-opener'),
						$filterClose  = $thisList.find('.qodef-filter-content .qodef-filter-close');

					qodefProductFilterContent.startingStyle( $thisList, $filterContent );

					if ( $filterOpener.length ) {
						// Open Side Area
						$filterOpener.on(
							'click',
							function ( e ) {
								e.preventDefault();
								
								if ( ! qodefCore.body.hasClass( 'qodef-product-side-area--opened' ) ) {
									qodefProductFilterContent.openSideArea();
									
									$( document ).keyup(
										function ( e ) {
											if ( e.keyCode === 27 ) {
												qodefProductFilterContent.closeSideArea();
											}
										}
									);
								} else {
									qodefProductFilterContent.closeSideArea();
								}
							}
						);
					}
					
					if ( $filterClose.length ) {
						$filterClose.on(
							'click',
							function ( e ) {
								e.preventDefault();
								
								if ( qodefCore.body.hasClass( 'qodef-product-side-area--opened' ) ) {
									qodefProductFilterContent.closeSideArea();
								}
							}
						);
					}
					
					if ( $thisList.hasClass('qodef-filter--advanced') && $thisList.hasClass('qodef-filter-type--side-area') ) {
						if ($filterContent.length && typeof qodefCore.qodefPerfectScrollbar === 'object') {
							qodefCore.qodefPerfectScrollbar.init( $filterContent );
						}
					}
					
					if ( window.matchMedia("(min-width: 881px)") ) {
						$showMore.on(
							'click', function ( e ) {
								e.preventDefault();
								var $target = e.target;
								qodefProductFilterContent.showMore( $thisList, $target );
							}
						);
					}
				});
			}
		},
		startingStyle: function ( $list, $filterContent ) {
			var $optionsHolder = $list.find('.qodef-e-options-wrapper'),
				$gridHolder    = $list.find('.qodef-e-grid-filter'),
				$option        = $list.find('.qodef-e-checkbox');

			if ( $list.hasClass('qodef-grid-filter--on') ) {
				$gridHolder.each(
					function () {
						var $currGrid = $(this),
							$gridOptions   = $currGrid.find('.qodef-e-grid-option');
						
						$gridOptions.removeClass('qodef--active');
						
						if ( $list.hasClass('qodef-item-layout--horizontal') ) {
							$gridOptions.last().addClass('qodef--active');
						} else {
							$gridOptions.first().addClass('qodef--active');
						}
					}
				);
			}
			
			if ( $option.length ) {
				qodefProductFilterContent.holderHeight = $( $option[0] ).outerHeight( true ) * 8;
			}
			
			$optionsHolder.each(
				function () {
					if ( window.matchMedia("(min-width: 881px)").matches ) {
						var $showMore = $(this).siblings('.qodef-filter-show-more');
						
						if ($(this).find('.qodef-e-checkbox').length > 8) {
							$showMore.addClass('qodef--active');
							$showMore.addClass('qodef--active');
							$(this).css('height', qodefProductFilterContent.holderHeight);
						}
					}
				}
			);
			
			$filterContent.addClass('qodef--initialized');
		},
		showMore: function ( $list, target ) {
			var $target = $(target),
				$wrapper = $target.siblings('.qodef-e-options-wrapper');

			if ( $target.is('.qodef-filter-show-more') ) {
				$target.siblings('.qodef-filter-show-less').addClass('qodef--active');
				$target.removeClass('qodef--active');
				$wrapper.css('height', 'auto');
			} else if ( $target.is('.qodef-filter-show-less') ) {
				$target.siblings('.qodef-filter-show-more').addClass('qodef--active');
				$target.removeClass('qodef--active');
				$wrapper.css('height', qodefProductFilterContent.holderHeight);
			}
		},
		openSideArea: function () {
			var $wrapper      = $( '#qodef-page-wrapper' );
			var currentScroll = $( window ).scrollTop();
			
			$( '.qodef-product-side-area-cover' ).remove();
			$wrapper.prepend( '<div class="qodef-product-side-area-cover"/>' );
			qodefCore.body.removeClass( 'qodef-side-area-animate--out' ).addClass( 'qodef-product-side-area--opened qodef-side-area-animate--in' );
			
			$( '.qodef-product-side-area-cover' ).on(
				'click',
				function ( e ) {
					e.preventDefault();
					qodefProductFilterContent.closeSideArea();
				}
			);
		},
		closeSideArea: function () {
			qodefCore.body.removeClass( 'qodef-product-side-area--opened qodef-side-area-animate--in' ).addClass( 'qodef-side-area-animate--out' );
		},
	};
	
	var qodefProductFilterMobile = {
		init: function () {
			var $productList = $('.qodef-woo-product-list.qodef-filter--advanced .qodef-product-list-filter-mobile');
			
			if ($productList.length) {
				$productList.each( function( ) {
					var $mobileFilter = $( this ),
						$list = $mobileFilter.parent(),
						$filterTop = $mobileFilter.find('.qodef-e-info-top'),
						$filterContent = $mobileFilter.find('.qodef-info-bottom'),
						$filterScroll = $mobileFilter.find('.qodef-e-info-scroll'),
						$filterOpener = $mobileFilter.find('.qodef-filter-opener'),
						$filterClose  = $mobileFilter.find('.qodef-filter-close'),
						$showResult   = $mobileFilter.find('.qodef--show');
					
					if ( window.matchMedia("(max-width: 880px)").matches ) {
						
						qodefProductFilterMobile.openItem( $mobileFilter );
						
						window.addEventListener("scroll", function() {
							if (window.scrollY > ($filterTop.offset().top + $filterTop.outerHeight( true )) + 100 && window.scrollY < $list.offset().top + $list.outerHeight( true) - screen.height / 2 ) {
								$filterScroll.addClass('qodef--active');
							} else {
								$filterScroll.removeClass('qodef--active');
							}
						});
						
						if ( $filterOpener.length ) {
							// Open Side Area
							$filterOpener.on(
								'click',
								function ( e ) {
									e.preventDefault();
									
									if ( ! $list.hasClass( 'qodef-product-mobile-filter--opened' ) ) {
										qodefProductFilterMobile.openMobileFilter( $list );
										
										$( document ).keyup(
											function ( e ) {
												if ( e.keyCode === 27 ) {
													qodefProductFilterMobile.closeMobileFilter( $list );
												}
											}
										);
									} else {
										qodefProductFilterMobile.closeMobileFilter( $list );
									}
								}
							);
						}
						
						if ( $filterClose.length ) {
							$filterClose.on(
								'click',
								function ( e ) {
									e.preventDefault();
									
									if ( $list.hasClass( 'qodef-product-mobile-filter--opened' ) ) {
										qodefProductFilterMobile.closeMobileFilter( $list );
									}
								}
							);
						}
						
						if ( $showResult.length ) {
							$showResult.on(
								'click',
								function ( e ) {
									e.preventDefault();
									
									if ( $list.hasClass( 'qodef-product-mobile-filter--opened' ) ) {
										qodefProductFilterMobile.closeMobileFilter( $list );
									}
								}
							);
						}
						
						if ( $list.hasClass('qodef-filter--advanced') ) {
							if ($filterContent.length && typeof qodefCore.qodefPerfectScrollbar === 'object') {
								qodefCore.qodefPerfectScrollbar.init( $filterContent );
							}
						}
					}
				});
			}
		},
		openItem: function ( $filter ) {
			var $titleFilter = $filter.find('.qodef-filter-title');
			
			$titleFilter.on( 'click',
				function ( e ) {
					var $title = $(e.target.closest('.qodef-filter-title')),
						$option = $title.siblings('.qodef-e-options-wrapper'),
						$optionHeight = $option.prop('scrollHeight');
					
					if ( $title.hasClass('qodef--active') ) {
						$title.removeClass('qodef--active');
						$option.css('height', 0);
					} else {
						$title.addClass('qodef--active');
						$option.css('height', $optionHeight);
					}
				}
			);
		},
		openMobileFilter: function ( $list ) {
			var $wrapper      = $( '#qodef-page-wrapper' );
			var currentScroll = $( window ).scrollTop();
			
			$( '.qodef-product-mobile-filter-cover' ).remove();
			$wrapper.prepend( '<div class="qodef-product-mobile-filter-cover"/>' );
			qodefCore.body.removeClass( 'qodef-product-mobile-filter-animate--out' ).addClass( 'qodef-product-mobile-filter-cover--opened qodef-product-mobile-filter-animate--in' );
			$list.addClass('qodef-product-mobile-filter--opened');
			qodefCore.qodefScroll.disable();
		},
		closeMobileFilter: function ( $list ) {
			qodefCore.body.removeClass( 'qodef-product-mobile-filter-cover--opened qodef-product-mobile-filter-animate--in' ).addClass( 'qodef-product-mobile-filter-animate--out' );
			$list.removeClass('qodef-product-mobile-filter--opened');
			qodefCore.qodefScroll.enable();
		},
	};
	
	var qodefProductFilter = {
		list: {},
		fields: {},
		resetText: '',
		init: function () {
			var $productList = $('.qodef-woo-shortcode.qodef-woo-product-list.qodef-filter--advanced');

			if ($productList.length) {
				$productList.each( function( ) {
					var $thisList    = $( this ),
						$priceFilter = $thisList.find( '.qodef-e-price-filter' ),
						$reset       = $thisList.find('.qodef--reset');

					qodefProductFilter.list = $thisList;
					qodefProductFilter.resetText = $reset.children('.qodef-m-text').text();

					if ( $thisList.hasClass( 'qodef-grid-filter--on' ) ) {
						qodefProductFilter.initGrid( $thisList );
					}
					qodefProductFilter.setupPriceFilter( $thisList, $priceFilter );
					
					qodefProductFilter.initSearchParams( $thisList );
					
					$reset.on('click', function ( e ) {
						$reset.children('.qodef-m-text').text( qodefProductFilter.resetText );
						qodefProductFilter.removeActiveFilters( e, $thisList );
					});
				});
			}
		},
		initSearchParams: function ( $productList ) {
			var $orderby = $productList.find('.qodef-e-order-link'),
				$fields  = [],
				$links    = $productList.find('.qodef-e-link'),
				$checkbox = $productList.find('.qodef-e-checkbox'),
				$filterButton = $productList.find('.qodef-e-price-filter .qodef-filter-button .qodef-button');

			$fields.$orderbyFields = $orderby;
			$fields.orderbyFieldsExists = $fields.$orderbyFields.length;
			$fields.$checkboxFields = $productList.find('.qodef-e-checkbox input');
			$fields.checkboxFieldsExists = $fields.$checkboxFields.length;
			$fields.$linkFields = $links;
			$fields.linkFieldsExists = $fields.$linkFields.length;
			$fields.priceRangeFields = $productList.find('#min_price, #max_price');
			$fields.priceRangeFieldsExists = $fields.priceRangeFields.length;

			qodefProductFilter.fields = $fields;

			$orderby.on('click', function ( e ) {
				e.preventDefault();
				var $item = $(e.target).closest('.qodef-e-order-link'),
					$siblings = $item.siblings();
				
				$siblings.removeClass('qodef--active');
				$item.addClass('qodef--active');
				
				$productList.find('#order-current .qodef-e-text').text( $item.text() );
				
				qodefProductFilter.initFilter( $productList, $fields );
			});
			
			$checkbox.on('change', {productList: $productList, fields: $fields}, function ( e ) {
				qodefProductFilter.initFilter( $productList, $fields );
				qodefProductFilter.updateReset( $productList, $fields );
			});
			
			$links.on('click', function ( e ) {
				e.preventDefault();
				var $item = $(e.target).closest('.qodef-e-link');
				
				if ( $item.hasClass('qodef--active') ) {
					$item.removeClass('qodef--active');
				} else {
					$item.addClass('qodef--active');
				}
				
				qodefProductFilter.initFilter( $productList, $fields );
				qodefProductFilter.updateReset( $productList, $fields );
			});
			
			$filterButton.on('click', function ( e ) {
				e.preventDefault();
				qodefProductFilter.initFilter( $productList, $fields );
			});
		},
		updateReset: function ( $list, $items ) {
			var $reset = $list.find('.qodef--reset');
			
			if ( $reset.length ) {
				var $links    = $list.find('.qodef-e-link.qodef--active').length,
					$checkbox = $list.find('.qodef-e-checkbox input:checked').length,
					$index    = $reset.find('.qodef-m-text').text().indexOf('(');
				
				if ( -1 === $index ) {
					if ( $links+$checkbox > 0 ) {
						$reset.children('.qodef-m-text').append( '(' + ( $links + $checkbox ) + ')' );
					}
				} else if ( $links+$checkbox > 0 ) {
					$reset.children('.qodef-m-text').text(qodefProductFilter.resetText + '(' + ( $links + $checkbox ) + ')' );
				} else {
					$reset.children('.qodef-m-text').text(qodefProductFilter.resetText);
				}
			}
		},
		initFilter: function ( $list, $items ) {
			var $productList = $list,
				options      = $productList.data( 'options' ),
				$fields      = $items,
				newOptions   = {};

			if ( 'product_tag' === options['tax'] ) {
				newOptions['tag'] = [];
				newOptions['tag'].push(options['tax_slug']);
				newOptions['tag'] = newOptions['tag'].join( ',' );
			}

			if ($fields.orderbyFieldsExists) {
				$fields.$orderbyFields.each(
					function () {
						if ( $( this ).hasClass( 'qodef--active' ) ) {
							var orderKey = 'order_by',
								value    = $( this ).data( 'value' );
							
							if (typeof value !== "undefined" && value !== "") {
								newOptions[orderKey] = value;
							} else {
								newOptions[orderKey] = '';
							}
						}
					}
				);
			}

			if ($fields.checkboxFieldsExists) {
				var $checked = $productList.find('.qodef-e-checkbox input:checked');

				newOptions['brand']      = [];
				newOptions['rating'] 	 = [];
				newOptions['category']   = [];
				newOptions['attr1']      = [];
				newOptions['attrType1']  = '';
				newOptions['attr2']      = [];
				newOptions['attrType2']  = '';

				if ( 'product_cat' === options['tax'] ) {
					newOptions['category'].push(options['tax_slug']);
				}

				$checked.each(
					function () {
						var item = $(this);
						switch ( item.attr('name') ) {
							case 'qodef-product-brand':
								var fieldKey = 'brand',
									value = item.data('id');
								if (typeof value !== "undefined" && value !== "") {
									newOptions[fieldKey].push( item.data('id') );
								} else {
									newOptions[fieldKey] = '';
								}
								break;
							case 'qodef-product-attribute':
								var attrValue = item.data('id');
								
								if ( newOptions['attrType1'] === '' || newOptions['attrType1'] === item.data('type') ) {
									if (typeof attrValue !== "undefined" && attrValue !== "") {
										newOptions['attrType1'] = item.data('type');
										newOptions['attr1'].push( attrValue );
									} else {
										newOptions['attr1'] = '';
									}
								} else {
									if (typeof attrValue !== "undefined" && attrValue !== "") {
										newOptions['attrType2'] = item.data('type');
										newOptions['attr2'].push( attrValue );
									} else {
										newOptions['attr2'] = '';
									}
								}
								break;
							case 'qodef-product-category':
								var fieldKey = 'category',
									value = item.data('id');
								newOptions['category'] = [];
								if (typeof value !== "undefined" && value !== "") {
									newOptions[fieldKey].push( item.data('id') );
								} else {
									newOptions[fieldKey] = '';
								}
								break;
							case 'qodef-product-rating':
								var fieldKey = 'rating',
									value = item.attr('value');
								if (typeof value !== "undefined" && value !== "") {
									newOptions[fieldKey].push( item.attr('value') );
								} else {
									newOptions[fieldKey] = '';
								}
								break;
						}
					}
				);
				
				newOptions['brand']    = newOptions['brand'].join( ',' );
				newOptions['category'] = newOptions['category'].join( ',' );
				newOptions['rating']   = newOptions['rating'].join( ',' );
			}
			
			if ($fields.linkFieldsExists) {
				newOptions['attr1'] = newOptions['attr1'] ? newOptions['attr1'] : [];
				newOptions['attrType1']  = newOptions['attrType1'] ? newOptions['attrType1'] : '';
				newOptions['attr2'] = newOptions['attr2'] ? newOptions['attr2'] : [];
				newOptions['attrType2']  = newOptions['attrType2'] ? newOptions['attrType2'] : '';
				
				$fields.$linkFields.each(
					function () {
						var item = $( this );
						if ( item.hasClass( 'qodef--active' ) ) {
							var attrValue = item.data('id');
							
							if ( newOptions['attrType1'] === '' || newOptions['attrType1'] === item.data('type') ) {
								if (typeof attrValue !== "undefined" && attrValue !== "") {
									newOptions['attrType1'] = item.data('type');
									newOptions['attr1'].push( attrValue );
								} else {
									newOptions['attr1'] = '';
								}
							} else {
								if (typeof attrValue !== "undefined" && attrValue !== "") {
									newOptions['attrType2'] = item.data('type');
									newOptions['attr2'].push( attrValue );
								} else {
									newOptions['attr2'] = '';
								}
							}
						}
					}
				);
			}
			
			if ( newOptions['attr1'] ) {
				newOptions['attr1'] = newOptions['attr1'].join( ',' );
			}
			
			if ( newOptions['attr2'] ) {
				newOptions['attr2'] = newOptions['attr2'].join( ',' );
			}

			if ($fields.priceRangeFieldsExists) {
				var priceMin    = $productList.find('.qodef-price-slider-amount #min_price, .qodef-price-slider-amount #qodef-mobile-min_price').attr('value'),
					priceMax    = $productList.find('.qodef-price-slider-amount #max_price, .qodef-price-slider-amount #qodef-mobile-max_price').attr('value'),
					priceKey = 'price';

				newOptions['price'] = [];

				if (typeof priceMin !== "undefined" && priceMin !== "") {
					newOptions[priceKey].push(priceMin);
				} else {
					newOptions[priceKey] = '';
				}

				if (typeof priceMax !== "undefined" && priceMax !== "") {
					newOptions[priceKey].push(priceMax);
				} else {
					newOptions[priceKey] = '';
				}
			}

			var additional = qodefProductFilter.createAdditionalQuery( newOptions );

			$.each(
				additional,
				function (key, value) {
					options[key] = value;
				}
			);

			$productList.data( 'options', options );
			qodef.body.trigger( 'greenpath_trigger_load_more', [$productList, 1] );
		},
		createAdditionalQuery: function( newOptions ){
			var addQuery 		= {},
				i = 0;

			addQuery.additional_query_args 			 = {};
			addQuery.additional_query_args.tax_query = {};
			addQuery.additional_query_args.meta_query = {};

			if (typeof newOptions === 'object') {
				$.each(
					newOptions,
					function ( key, value ) {

						switch (key) {
							case 'order_by':
								addQuery.orderby = newOptions.order_by;
								break;
							case 'category':
								if ( value !== '' && value !== '*' ) {
									if ( value.indexOf( ',' ) !== -1 ) {
										value = value.split( ',' );
									}
									addQuery.additional_query_args.tax_query['value' + i]          = {};
									addQuery.additional_query_args.tax_query['value' + i].taxonomy = 'product_cat';
									addQuery.additional_query_args.tax_query['value' + i].field    = typeof value === 'number' ? 'term_id' : 'slug';
									addQuery.additional_query_args.tax_query['value' + i].terms    = value;
									addQuery.additional_query_args.tax_query['value' + i].operator = 'IN';
									i++;
								}
								break;
							case 'tag':
								if ( value !== '' && value !== '*' ) {
									if ( value.indexOf( ',' ) !== -1 ) {
										value = value.split( ',' );
									}
									addQuery.additional_query_args.tax_query['value' + i]          = {};
									addQuery.additional_query_args.tax_query['value' + i].taxonomy = 'product_tag';
									addQuery.additional_query_args.tax_query['value' + i].field    = typeof value === 'number' ? 'term_id' : 'slug';
									addQuery.additional_query_args.tax_query['value' + i].terms    = value;
									addQuery.additional_query_args.tax_query['value' + i].operator = 'IN';
									i++;
								}
								break;
							case 'brand':
								if ( value !== '' ) {
									if ( value.indexOf( ',' ) !== -1 ) {
										value = value.split( ',' );
									}
									addQuery.additional_query_args.tax_query['value' + i]         = {};
									addQuery.additional_query_args.tax_query['value' + i].taxonomy = 'product_' + key;
									addQuery.additional_query_args.tax_query['value' + i].field    = 'slug';
									addQuery.additional_query_args.tax_query['value' + i].terms    = value;
									addQuery.additional_query_args.tax_query['value' + i].operator = 'IN';
									i++;
								}
								break;
							case 'price':
								if (value !== '') {
									addQuery.additional_query_args.meta_query['value'] = {};
									addQuery.additional_query_args.meta_query['value'].key = '_price';
									addQuery.additional_query_args.meta_query['value'].value = [parseInt(value[0]), parseInt(value[1])];
									addQuery.additional_query_args.meta_query['value'].compare = 'BETWEEN';
									addQuery.additional_query_args.meta_query['value'].type = 'NUMERIC';
								}
								break;
							case 'attr1':
								if (value !== '') {
									if ( value.indexOf( ',' ) !== -1 ) {
										value = value.split( ',' );
									}
									addQuery.additional_query_args.tax_query['value' + i] = {};
									addQuery.additional_query_args.tax_query['value' + i].taxonomy = 'pa_' + newOptions['attrType1'];
									addQuery.additional_query_args.tax_query['value' + i].field = 'slug';
									addQuery.additional_query_args.tax_query['value' + i].terms = value;
									addQuery.additional_query_args.tax_query['value' + i].operator = 'IN';
									i++;
								}
								break;
							case 'attr2':
								if (value !== '') {
									if ( value.indexOf( ',' ) !== -1 ) {
										value = value.split( ',' );
									}
									addQuery.additional_query_args.tax_query['value' + i] = {};
									addQuery.additional_query_args.tax_query['value' + i].taxonomy = 'pa_' + newOptions['attrType2'];
									addQuery.additional_query_args.tax_query['value' + i].field = 'slug';
									addQuery.additional_query_args.tax_query['value' + i].terms = value;
									addQuery.additional_query_args.tax_query['value' + i].operator = 'IN';
									i++;
								}
								break;
							case 'rating':
								if ( value !== '' ) {
									var min = 0, max = 0;
									
									if ( value.indexOf( ',' ) !== -1 ) {
										value = value.split( ',' );
										min = Math.min(...value);
										max = Math.max(...value);
										
										if ( max === 5 ) {
											max = max + .1;
											min = min - .5;
										} else if ( min === 1 ) {
											max = max + .5;
										} else {
											max = max + .5;
											min = min - .5;
										}
									} else {
										if ( value === 5 ) {
											max = parseFloat( value ) + .1;
											min = parseFloat( value ) - .5;
										} else if ( value === 1 ) {
											max = parseFloat( value ) + .5;
											min = parseFloat( value );
										} else {
											max = parseFloat( value ) + .5;
											min = parseFloat( value ) - .5;
										}
									}
									addQuery.additional_query_args.meta_query['value' + i] = {};
									addQuery.additional_query_args.meta_query['value' + i].key = '_wc_average_rating';
									addQuery.additional_query_args.meta_query['value' + i].value = [ min, max ];
									addQuery.additional_query_args.meta_query['value' + i].compare = 'BETWEEN';
									i++;
								}
								break;
						}
					}
				);

				if ( Object.entries( addQuery.additional_query_args.tax_query ).length > 1 ) {
					addQuery.additional_query_args.tax_query['relation'] = 'AND';
				}

				if ( Object.entries( addQuery.additional_query_args.meta_query ).length > 1 ) {
					addQuery.additional_query_args.meta_query['relation'] = 'AND';
				}
			}

			if ( $.isEmptyObject( addQuery.additional_query_args.tax_query ) ) {
				delete addQuery.additional_query_args.tax_query;
			}

			return addQuery;
		},
		initGrid: function ( $list ) {
			var $gridOptions = $list.find('.qodef-e-grid-option'),
				$startListClasses = $list.attr("class").split(/\s+/),
				$startLayoutClass = $startListClasses.filter( currentClass => currentClass.startsWith('qodef-item-layout') )[0],
				$startlNumberClass = $startListClasses.filter( currentClass => currentClass.startsWith('qodef-col-num') )[0],
				$listLayoutClass = 'qodef-item-layout--horizontal',
				$listNumberClass = 'qodef-col-num--1',
				$classLength     = $listNumberClass.length,
				$gridLayoutClass = $startLayoutClass === $listLayoutClass ? 'qodef-item-layout--info-below' : $startLayoutClass,
				$gridNumberClass = $list.hasClass('qodef-item-layout--horizontal') ? $listNumberClass : $startlNumberClass;
			
			$gridOptions.on('click', (e) => {
				e.preventDefault();
				
				var $option = $(e.target.closest('.qodef-e-grid-option')),
					$currentClasses = $list.attr("class").split(/\s+/),
					$optList = $list.find('.qodef-e-grid-option.qodef--list'),
					$optGrid = $list.find('.qodef-e-grid-option.qodef--grid'),
					$currentLayoutClass = $currentClasses.filter( currentClass => currentClass.startsWith('qodef-item-layout') )[0],
					$currentNumberClass = $currentClasses.filter( currentClass => currentClass.startsWith('qodef-col-num') && currentClass.length <= $classLength )[0];

				$gridOptions.removeClass('qodef--active');
				
				if ( $option.hasClass('qodef--list') ) {
					$list.removeClass( $currentLayoutClass );
					$list.removeClass( $currentNumberClass );
					$list.addClass( $listLayoutClass );
					$list.addClass( $listNumberClass );
					$optList.addClass('qodef--active');
					$optGrid.removeClass('qodef--active');
				} else {
					$list.removeClass( $currentLayoutClass );
					$list.removeClass( $currentNumberClass );
					$list.addClass( $gridLayoutClass );
					$list.addClass( $gridNumberClass );
					$optGrid.addClass('qodef--active');
					$optList.removeClass('qodef--active');
				}
				
			});
		},
		setupPriceFilter: function ( $list, $slider ) {
			$slider.find( '.qodef-price-slider-amount #min_price, .qodef-price-slider-amount #max_price' ).hide();

			var $sliderHolder     = $slider.find( '.qodef-price-slider' ),
				min_price         = $list.find( '.qodef-price-slider-amount #min_price' ).data( 'min' ),
				max_price         = $list.find( '.qodef-price-slider-amount #max_price' ).data( 'max' ),
				currency          = $list.find( '.qodef-price-slider-amount #min_price' ).data( 'currency' ),
				step              = 1,
				current_min_price = $list.find( '.qodef-price-slider-amount #min_price' ).val(),
				current_max_price = $list.find( '.qodef-price-slider-amount #max_price' ).val(),
				labelMin          = $slider.find( '.qodef--min' ),
				labelMax          = $slider.find( '.qodef--max' );
			
			labelMin.prepend( currency );
			labelMax.prepend( currency );
			
			$sliderHolder.slider({
				range: true,
				animate: true,
				min: min_price,
				max: max_price,
				step: step,
				values: [ current_min_price, current_max_price ],
				create: function() {
					$( '.qodef-price-slider-amount #min_price' ).val( current_min_price );
					$( '.qodef-price-slider-amount #max_price' ).val( current_max_price );
					$( document.body ).trigger( 'price_slider_create', [ current_min_price, current_max_price ] );
				},
				slide: function( event, ui ) {
					$( 'input#min_price' ).attr( 'value', ui.values[0] );
					$( 'input#max_price' ).attr( 'value', ui.values[1] );

					$list.find( '.qodef-e-price-filter .qodef-e-amount .qodef--min' ).text( currency + ui.values[0] );
					$list.find( '.qodef-e-price-filter .qodef-e-amount .qodef--max' ).text( currency + ui.values[1] );

					$( document.body ).trigger( 'price_slider_slide', [ ui.values[0], ui.values[1] ] );
				},
			});
		},
		removeActiveFilters: function ( event, $list ) {
			if ( event.target.matches('.qodef--reset') || event.target.matches('.qodef-m-text') ) {
				event.preventDefault();
				
				//remove all items
				var $items = $list.find('.qodef-e-checkbox input:checked'),
					$links = $list.find('.qodef-e-link.qodef--active');
				
				$items.each( function (i, e) {
					$(e).prop('checked', false);
				});
				
				$links.each( function (i, e) {
					$(e).removeClass('qodef--active');
				});
				
				qodefProductFilter.initFilter( $list, qodefProductFilter.fields );
			}
		},
	};
	
})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefProductSearch.init();
		}
	);

	var qodefProductSearch = {
		init: function () {
			var $holder = $( '.qodef-product-search-holder' );

			if ( $holder.length ) {
				$holder.off().each(
					function () {
						var $thisHolder = $( this );

						qodefProductSearch.trigger( $thisHolder );
					}
				);
			}
		},
		trigger: function ( $holder ) {

			if (typeof (qodefProductList) !== 'undefined' && qodefProductList.products.length) {
				var searchForms = $('.qodef-product-search-holder form');
				var productArray = qodefProductList.products;
				var productSuggestion = function (data) {

					var suggestionTemplate = '<div><div class="qodef-product-search-item">';

					//thumbnail
					if (data.thumb) {
						suggestionTemplate += '<div class="qodef-product-search-image"><img src="' + data.thumb + '" alt="product-featured-image"/></div>';
					}

					//content holder
					suggestionTemplate += '<div class="qodef-product-search-item-content">';

					//product categories

					suggestionTemplate += '<div class="qodef-product-search-item-cat">';

					var product_cat_length = data.product_cat.length;

					for (var i = 0; i < product_cat_length; i++) {

						suggestionTemplate += data.product_cat[i].split('-').join(' ');

						if (i !== product_cat_length - 1) {
							suggestionTemplate += ', ';
						}
					}

					suggestionTemplate += '</div>';

					//product title
					if (data.post_title) {
						suggestionTemplate += '<h6 class="qodef-product-search-title">' + data.post_title + '</h6>';
					}

					//product price
					if (data.price) {
						suggestionTemplate += '<div class="qodef-product-search-price">' + data.price + '</div>';
					}

					suggestionTemplate += '</div></div>';

					return suggestionTemplate;
				};


				var productArrayBl = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('post_title'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					local: productArray
				});

				searchForms.each(function () {
					var searchForm = $(this);

					if( ! searchForm.hasClass( 'qodef-typeahead--initialized' ) ) {

						searchForm.find( '.qodef-product-search' ).typeahead(
							{
								hint: true,
								highlight: true,
								minLength: 1
							},
							{
								name: 's',
								display: 'post_title',
								source: productArrayBl,
								templates: {
									suggestion: productSuggestion
								}
							}
						);

						searchForm.addClass( 'qodef-typeahead--initialized' );
					}

					//change typeahead on category change

					var selectElement = $(this).find('.qodef-product-category')[0];

					selectElement.onchange = function () {
						var selectedValue = selectElement.options[selectElement.selectedIndex].value;
						var productCatArray = [];

						var productCatArrayBl = new Bloodhound({
							datumTokenizer: Bloodhound.tokenizers.obj.whitespace('post_title'),
							queryTokenizer: Bloodhound.tokenizers.whitespace,
							local: function () {

								if (selectedValue !== '') {

									var arrayLength = productArray.length;

									for (var i = 0; i < arrayLength; i++) {
										if (productArray[i].product_cat.indexOf(selectedValue) !== -1) {
											productCatArray.push(productArray[i]);
										}
									}

									return productCatArray;
								}
								else {
									return productArray;
								}
							}
						});

						searchForm.find('.qodef-product-search').typeahead('destroy');

						searchForm.find('.qodef-product-search').typeahead({
								hint: true,
								highlight: true,
								minLength: 1
							},
							{
								name: 's',
								display: 'post_title',
								source: productCatArrayBl,
								templates: {
									suggestion: productSuggestion
								}
							});

					};

					$(".qodef-product-search-form").on('click', '.tt-suggestion', function () {
						setTimeout(function () {
							$(".qodef-product-search-submit").trigger("click");
						}, 100);
					});
					
					$(".qodef-product-search-form").on('click', '.tt-suggestion', function () {
						setTimeout(function () {
							$(".qodef-product-search-submit").trigger("click");
						}, 100);
					});
					
					$(".qodef-product-category-holder").on('click', '', function () {
						$(".qodef-product-category-holder").toggleClass("dropdown-opened");
					});
				});
			}
		},
	};

})( jQuery );

(function ($) {
    'use strict';

    $(document).ready(
        function () {
            qodefSideAreaCart.init();
        }
    );

    var qodefSideAreaCart = {
        init: function () {
            var $holder = $('.widget_greenpath_core_woo_side_area_cart');

            if ($holder.length) {
                $holder.off().each(
                    function () {
                        var $thisHolder = $(this);

                        if( ! $('.qodef-woo-side-area-cart-cover').length ) {
                            $( '#qodef-page-wrapper' ).prepend( '<div class="qodef-woo-side-area-cart-cover"/>' );
                        }

                        /*if (qodefCore.windowWidth > 880) {*/
                            qodefSideAreaCart.trigger($thisHolder);
                            qodefSideAreaCart.start($thisHolder);

                            qodefCore.body.on(
                                'added_to_cart removed_from_cart wc_fragments_refreshed',
                                function () {
                                    qodefSideAreaCart.init();
                                }
                            );

                        /*}*/
                    }
                );
            }
        },
        trigger: function ($holder) {
            var $items = $holder.find('.qodef-woo-side-area-cart');
            if ($items.length && typeof qodefCore.qodefPerfectScrollbar === 'object') {
                qodefCore.qodefPerfectScrollbar.init($items);
            }
        },
        start: function ($holder) {
            $holder.on(
                'click',
                '.qodef-m-opener',
                function (e) {
                    e.preventDefault();

                    if (!$holder.hasClass('qodef--opened')) {
                        qodefSideAreaCart.openSideArea($holder);
                        qodefSideAreaCart.trigger($holder);

                        $(document).keyup(
                            function (e) {
                                if (e.keyCode === 27) {
                                    qodefSideAreaCart.closeSideArea($holder);
                                }
                            }
                        );
                    } else {
                        qodefSideAreaCart.closeSideArea($holder);
                    }
                }
            );

            $holder.on(
                'click',
                '.qodef-m-close',
                function (e) {
                    e.preventDefault();
                    qodefSideAreaCart.closeSideArea($holder);
                }
            );
        },
        openSideArea: function ($holder) {
            qodefCore.qodefScroll.disable();
            qodefCore.body.addClass( 'qodef-woo-side-area-cart--opened' );

            var header = $('#qodef-page-header'),
                fsheader = $('#qodef-fullscreen-area');

            header.css('z-index', '102');
            fsheader.css('z-index', '102');
            $( '#qodef-top-area' ).css( 'z-index', '100' );

            $holder.addClass('qodef--opened');

            $('.qodef-woo-side-area-cart-cover').on(
                'click',
                function (e) {
                    e.preventDefault();

                    qodefSideAreaCart.closeSideArea($holder);
                }
            );
        },
        closeSideArea: function ($holder) {
            var header = $('#qodef-page-header'),
                fsheader = $('#qodef-fullscreen-area');

            if ($holder.hasClass('qodef--opened')) {
                qodefCore.qodefScroll.enable();
                qodefCore.body.removeClass( 'qodef-woo-side-area-cart--opened' );

                $holder.removeClass('qodef--opened');

                setTimeout(function () {
                    header.css('z-index', '100');
                    fsheader.css('z-index', '99');
                    $( '#qodef-top-area' ).css( 'z-index', '101' );
                }, 500);
            }
        }
    };

})(jQuery);

/* GreenPath Yith Wishlist widget counter update
as covered here https://support.yithemes.com/hc/en-us/articles/115001372967-Wishlist-How-to-count-number-of-products-wishlist-in-ajax */
jQuery( document ).ready( function( $ ){
	$(document).on( 'added_to_wishlist removed_from_wishlist', function(){
		var counter = $('.qodef-wishlist-count');

		$.ajax({
			url: yith_wcwl_l10n.ajax_url,
			data: {
			action: 'yith_wcwl_update_wishlist_count'
			},
			dataType: 'json',
			success: function( data ){
			counter.html( data.count );
			},
			beforeSend: function(){
			counter.block();
			},
			complete: function(){
			counter.unblock();
			}
		})
	} )
});

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_clients_list             = {};
	qodefCore.shortcodes.greenpath_core_clients_list.qodefSwiper = qodef.qodefSwiper;

})( jQuery );

(function ( $ ) {
	'use strict';

	var shortcode = 'greenpath_core_team_list';

	qodefCore.shortcodes[shortcode] = {};

	if ( typeof qodefCore.listShortcodesScripts === 'object' ) {
		$.each(
			qodefCore.listShortcodesScripts,
			function ( key, value ) {
				qodefCore.shortcodes[shortcode][key] = value;
			}
		);
	}

})( jQuery );

(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.greenpath_core_testimonials_list             = {};
	qodefCore.shortcodes.greenpath_core_testimonials_list.qodefSwiper = qodef.qodefSwiper;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefInteractiveLinkShowcaseInteractiveList.init();
		}
	);

	var qodefInteractiveLinkShowcaseInteractiveList = {
		init: function () {
			this.holder = $( '.qodef-interactive-link-showcase.qodef-layout--interactive-list' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						qodefInteractiveLinkShowcaseInteractiveList.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			var $links            = $currentItem.find( '.qodef-m-item' ),
				x                 = 0,
				y                 = 0,
				currentXCPosition = 0,
				currentYCPosition = 0;

			if ( $links.length ) {
				$links.on(
					'mouseenter',
					function () {
						$links.removeClass( 'qodef--active' );
						$( this ).addClass( 'qodef--active' );
					}
				).on(
					'mousemove',
					function ( event ) {
						var $thisLink         = $( this ),
							$followInfoHolder = $thisLink.find( '.qodef-e-follow-content' ),
							$followImage      = $followInfoHolder.find( '.qodef-e-follow-image' ),
							$followImageItem  = $followImage.find( 'img' ),
							followImageWidth  = $followImageItem.width(),
							followImagesCount = parseInt( $followImage.data( 'images-count' ), 10 ),
							followImagesSrc   = $followImage.data( 'images' ),
							$followTitle      = $followInfoHolder.find( '.qodef-e-follow-title' ),
							itemWidth         = $thisLink.outerWidth(),
							itemHeight        = $thisLink.outerHeight(),
							itemOffsetTop     = $thisLink.offset().top - qodefCore.scroll,
							itemOffsetLeft    = $thisLink.offset().left;

						x = (event.clientX - itemOffsetLeft) >> 0;
						y = (event.clientY - itemOffsetTop) >> 0;

						if ( x > itemWidth ) {
							currentXCPosition = itemWidth;
						} else if ( x < 0 ) {
							currentXCPosition = 0;
						} else {
							currentXCPosition = x;
						}

						if ( y > itemHeight ) {
							currentYCPosition = itemHeight;
						} else if ( y < 0 ) {
							currentYCPosition = 0;
						} else {
							currentYCPosition = y;
						}

						if ( followImagesCount > 1 ) {
							var imagesUrl    = followImagesSrc.split( '|' ),
								itemPartSize = itemWidth / followImagesCount;

							$followImageItem.removeAttr( 'srcset' );

							if ( currentXCPosition < itemPartSize ) {
								$followImageItem.attr( 'src', imagesUrl[0] );
							}

							// -2 is constant - to remove first and last item from the loop
							for ( var index = 1; index <= (followImagesCount - 2); index++ ) {
								if ( currentXCPosition >= itemPartSize * index && currentXCPosition < itemPartSize * (index + 1) ) {
									$followImageItem.attr( 'src', imagesUrl[index] );
								}
							}

							if ( currentXCPosition >= itemWidth - itemPartSize ) {
								$followImageItem.attr( 'src', imagesUrl[followImagesCount - 1] );
							}
						}

						$followImage.css(
							{
								'top': itemHeight / 2,
							}
						);
						$followTitle.css(
							{
								'transform': 'translateY(' + -(parseInt( itemHeight, 10 ) / 2 + currentYCPosition) + 'px)',
								'left': -(currentXCPosition - followImageWidth / 2),
							}
						);
						$followInfoHolder.css( { 'top': currentYCPosition, 'left': currentXCPosition } );
					}
				).on(
					'mouseleave',
					function () {
						$links.removeClass( 'qodef--active' );
					}
				);
			}

			$currentItem.addClass( 'qodef--init' );
		},
	};

	qodefCore.shortcodes.greenpath_core_interactive_link_showcase.qodefInteractiveLinkShowcaseInteractiveList = qodefInteractiveLinkShowcaseInteractiveList;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefInteractiveLinkShowcaseList.init();
		}
	);

	var qodefInteractiveLinkShowcaseList = {
		init: function () {
			this.holder = $( '.qodef-interactive-link-showcase.qodef-layout--list' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						qodefInteractiveLinkShowcaseList.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			var $images = $currentItem.find( '.qodef-m-image' ),
				$links  = $currentItem.find( '.qodef-m-item' );

			$images.eq( 0 ).addClass( 'qodef--active' );
			$links.eq( 0 ).addClass( 'qodef--active' );

			$links.on(
				'touchstart mouseenter',
				function ( e ) {
					var $thisLink = $( this );

					if ( ! qodefCore.html.hasClass( 'touchevents' ) || ( ! $thisLink.hasClass( 'qodef--active' ) && qodefCore.windowWidth > 680) ) {
						e.preventDefault();
						$images.removeClass( 'qodef--active' ).eq( $thisLink.index() ).addClass( 'qodef--active' );
						$links.removeClass( 'qodef--active' ).eq( $thisLink.index() ).addClass( 'qodef--active' );
					}
				}
			).on(
				'touchend mouseleave',
				function () {
					var $thisLink = $( this );

					if ( ! qodefCore.html.hasClass( 'touchevents' ) || ( ! $thisLink.hasClass( 'qodef--active' ) && qodefCore.windowWidth > 680) ) {
						$links.removeClass( 'qodef--active' ).eq( $thisLink.index() ).addClass( 'qodef--active' );
						$images.removeClass( 'qodef--active' ).eq( $thisLink.index() ).addClass( 'qodef--active' );
					}
				}
			);

			$currentItem.addClass( 'qodef--init' );
		},
	};

	qodefCore.shortcodes.greenpath_core_interactive_link_showcase.qodefInteractiveLinkShowcaseList = qodefInteractiveLinkShowcaseList;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefInteractiveLinkShowcaseSlider.init();
		}
	);

	var qodefInteractiveLinkShowcaseSlider = {
		init: function () {
			this.holder = $( '.qodef-interactive-link-showcase.qodef-layout--slider' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						qodefInteractiveLinkShowcaseSlider.initItem( $( this ) );
					}
				);
			}
		},
		initItem: function ( $currentItem ) {
			var $images = $currentItem.find( '.qodef-m-image' );

			var $swiperSlider = new Swiper(
				$currentItem.find( '.swiper-container' )[0],
				{
					loop: true,
					slidesPerView: 'auto',
					centeredSlides: true,
					speed: 1400,
					mousewheel: true,
					init: false
				}
			);

			$swiperSlider.on(
				'init',
				function () {
					$images.eq( 0 ).addClass( 'qodef--active' );
					$currentItem.find( '.swiper-slide-active' ).addClass( 'qodef--active' );

					$swiperSlider.on(
						'slideChangeTransitionStart',
						function () {
							var $swiperSlides    = $currentItem.find( '.swiper-slide' ),
								$activeSlideItem = $currentItem.find( '.swiper-slide-active' );

							$images.removeClass( 'qodef--active' ).eq( $activeSlideItem.data( 'swiper-slide-index' ) ).addClass( 'qodef--active' );
							$swiperSlides.removeClass( 'qodef--active' );

							$activeSlideItem.addClass( 'qodef--active' );
						}
					);

					$currentItem.find( '.swiper-slide' ).on(
						'click',
						function ( e ) {
							var $thisSwiperLink  = $( this ),
								$activeSlideItem = $currentItem.find( '.swiper-slide-active' );

							if ( ! $thisSwiperLink.hasClass( 'swiper-slide-active' ) ) {
								e.preventDefault();
								e.stopImmediatePropagation();

								if ( e.pageX < $activeSlideItem.offset().left ) {
									$swiperSlider.slidePrev();
									return false;
								}

								if ( e.pageX > $activeSlideItem.offset().left + $activeSlideItem.outerWidth() ) {
									$swiperSlider.slideNext();
									return false;
								}
							}
						}
					);

					$currentItem.addClass( 'qodef--init' );
				}
			);

			qodef.qodefWaitForImages.check(
				$currentItem,
				function () {
					$swiperSlider.init();
				}
			);
		},
	};

	qodefCore.shortcodes.greenpath_core_interactive_link_showcase.qodefInteractiveLinkShowcaseSlider = qodefInteractiveLinkShowcaseSlider;

})( jQuery );
