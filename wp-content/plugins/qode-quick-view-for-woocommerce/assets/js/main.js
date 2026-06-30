(function ( $ ) {
	'use strict';

	window.qodeQuickViewForWooCommerce     = {};
	qodeQuickViewForWooCommerce.shortcodes = {};

	qodeQuickViewForWooCommerce.body         = $( 'body' );
	qodeQuickViewForWooCommerce.html         = $( 'html' );
	qodeQuickViewForWooCommerce.windowWidth  = $( window ).width();
	qodeQuickViewForWooCommerce.windowHeight = $( window ).height();
	qodeQuickViewForWooCommerce.scroll       = 0;

	$( document ).ready(
		function () {
			qodeQuickViewForWooCommerce.scroll = $( window ).scrollTop();
		}
	);

	$( window ).resize(
		function () {
			qodeQuickViewForWooCommerce.windowWidth  = $( window ).width();
			qodeQuickViewForWooCommerce.windowHeight = $( window ).height();
		}
	);

	$( window ).scroll(
		function () {
			qodeQuickViewForWooCommerce.scroll = $( window ).scrollTop();
		}
	);

	$( window ).on(
		'load',
		function () {
		}
	);

	/**
	 * Init animation on appear
	 */
	var qodeQuickViewForWooCommerceAppear = {
		init: function () {
			this.holder = $( '.qqvfw--has-appear:not(.qwfw--appeared)' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						var $holder = $( this );

						qodeQuickViewForWooCommerce.qodeQuickViewForWooCommerceIsInViewport.check(
							$holder,
							function () {
								qodeQuickViewForWooCommerce.qodeQuickViewForWooCommerceWaitForImages.check(
									$holder,
									function () {
										$holder.addClass( 'qqvfw--appeared' );
									}
								);
							}
						);
					}
				);
			}
		},
	};

	qodeQuickViewForWooCommerce.qodeQuickViewForWooCommerceAppear = qodeQuickViewForWooCommerceAppear;

	var qodeQuickViewForWooCommerceIsInViewport = {
		check: function ( $element, callback, onlyOnce, callbackOnExit ) {
			if ( $element.length ) {
				// When item is 15% in the viewport.
				var offset = typeof $element.data( 'viewport-offset' ) !== 'undefined' ? $element.data( 'viewport-offset' ) : 0.15;

				var observer = new IntersectionObserver(
					function ( entries ) {
						// isIntersecting is true when element and viewport are overlapping.
						// isIntersecting is false when element and viewport don't overlap.
						if ( entries[0].isIntersecting === true ) {
							callback.call( $element );

							// Stop watching the element when it's initialize.
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

	qodeQuickViewForWooCommerce.qodeQuickViewForWooCommerceIsInViewport = qodeQuickViewForWooCommerceIsInViewport;

	/**
	 * Check element images to loaded
	 */
	var qodeQuickViewForWooCommerceWaitForImages = {
		check: function ( $element, callback ) {
			if ( $element.length ) {
				var images       = $element.find( 'img' );
				var images_count = images.length;

				if ( images.length ) {
					var counter = 0;

					for ( var index = 0; index < images_count; index++ ) {
						var img = images[index];

						if ( img.complete ) {
							counter++;

							if ( counter === images.length ) {
								callback.call( $element );
							}
						} else {
							var image = new Image();

							image.addEventListener(
								'load',
								function () {
									counter++;
									if ( counter === images.length ) {
										callback.call( $element );
										return false;
									}
								},
								false
							);
							image.src = img.src;
						}
					}
				} else {
					callback.call( $element );
				}
			}
		},
	};

	qodeQuickViewForWooCommerce.qodeQuickViewForWooCommerceWaitForImages = qodeQuickViewForWooCommerceWaitForImages;

	var qodeQuickViewForWooCommerceScroll = {
		disable: function () {
			if ( window.addEventListener ) {
				window.addEventListener(
					'wheel',
					qodeQuickViewForWooCommerceScroll.preventDefaultValue,
					{ passive: false }
				);
			}

			// Window.onmousewheel = document.onmousewheel = qodeQuickViewForWooCommerceScroll.preventDefaultValue.
			document.onkeydown = qodeQuickViewForWooCommerceScroll.keyDown;
		},
		enable: function () {
			if ( window.removeEventListener ) {
				window.removeEventListener(
					'wheel',
					qodeQuickViewForWooCommerceScroll.preventDefaultValue,
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
					qodeQuickViewForWooCommerceScroll.preventDefaultValue( e );
					return;
				}
			}
		}
	};

	qodeQuickViewForWooCommerce.qodeQuickViewForWooCommerceScroll = qodeQuickViewForWooCommerceScroll;

	var qodeQuickViewForWooCommercePerfectScrollbar = {
		init: function ( $holder ) {
			if ( $holder.length ) {
				qodeQuickViewForWooCommercePerfectScrollbar.initScroll( $holder );
			}
		},
		initScroll: function ( $holder ) {
			var $defaultParams = {
				wheelSpeed: 0.6,
				suppressScrollX: true,
			};

			var $ps = new PerfectScrollbar(
				$holder[0],
				$defaultParams
			);

			$ps.element.classList.add( 'qqvfw-ps' );

			$( window ).resize(
				function () {
					$ps.update();
				}
			);
		}
	};

	qodeQuickViewForWooCommerce.qodeQuickViewForWooCommercePerfectScrollbar = qodeQuickViewForWooCommercePerfectScrollbar;

})( jQuery );

(function ( $ ) {
	'use strict';

	/**
	 * Function object that represents quick view area popup.
	 *
	 * @returns {{init: Function}}
	 */
	var qqvfwInitQuickView = {
		init: function ( $holder ) {

			if ( $holder.length ) {

				qqvfwInitQuickView.setEventsAction( $holder );
			}
		},
		setEventsAction: function ( $holder ) {

			$( document.body ).on(
				'qode_quick_view_for_woocommerce_trigger_quick_view',
				function ( e, $holder ) {
					qqvfwInitQuickView.showQuickView( $holder );
				}
			);

			$holder.find( '.qqvfw-m-close' ).on(
				'click',
				function ( e ) {
					e.preventDefault();

					qqvfwInitQuickView.hideQuickView( $holder );
				}
			);
		},
		showQuickView: function ( $holder ) {
			if ( ! $holder.hasClass( 'qqvfw--opened' ) ) {
				$holder.addClass( 'qqvfw--opened' );

				if ( ! $holder.is( '#qode-quick-view-for-woocommerce-drop' ) ) {
					qodeQuickViewForWooCommerce.body = qodeQuickViewForWooCommerce.body.length ? qodeQuickViewForWooCommerce.body : $( 'body' );
					qodeQuickViewForWooCommerce.body.addClass( 'qqvfw-quick-view--opened' );
				}

			}
		},
		hideQuickView: function ( $holder ) {
			if ( $holder.hasClass( 'qqvfw--opened' ) ) {
				$holder.removeClass( 'qqvfw--opened' );

				if ( ! $holder.is( '#qode-quick-view-for-woocommerce-drop' ) ) {
					qodeQuickViewForWooCommerce.body = qodeQuickViewForWooCommerce.body.length ? qodeQuickViewForWooCommerce.body : $( 'body' );
					qodeQuickViewForWooCommerce.body.removeClass( 'qqvfw-quick-view--opened' );
				}

				qqvfwInitQuickView.cleanProductContent( $holder );
			}
		},
		addItem: function ( $holder, $button ) {
			var $productContent       = $holder.find( '.qqvfw-m-product' ),
				currentProduct        = $button.parents( '.product' ),
				currentProductId      = parseInt( $button.data( 'item-id' ), 10 ),
				hiddenInput           = $( '.qqvfw-hidden-type' ),
				hiddenInputPageId     = parseInt( hiddenInput.data( 'quick-view-page-id' ), 10 ),
				prevProductId         = 0,
				nextProductId         = 0,
				quickViewType         = window.innerWidth > 1024 ? $button.data( 'quick-view-type' ) : $button.data( 'quick-view-type-mobile' ),
				availableProductTypes = ['simple', 'variable', 'variation', 'grouped'];

			if ( currentProduct.length && currentProduct.prev().hasClass( 'product' ) ) {
				prevProductId = parseInt( currentProduct.prev().find( '.qqvfw-quick-view-button' ).data( 'item-id' ), 10 );
			}

			if ( currentProduct.length && currentProduct.next().hasClass( 'product' ) ) {
				nextProductId = parseInt( currentProduct.next().find( '.qqvfw-quick-view-button' ).data( 'item-id' ), 10 );
			}

			$.ajax(
				{
					type: 'GET',
					url: qodeQuickViewForWooCommerceGlobal.restUrl + qodeQuickViewForWooCommerceGlobal.quickViewRestRoute,
					data: {
						item_id: currentProductId,
						page_id: hiddenInputPageId,
						prev_item_id: prevProductId,
						next_item_id: nextProductId,
						quick_view_type: quickViewType,
						route: qodeQuickViewForWooCommerceGlobal.quickViewRestRouteName,
						security_token: qodeQuickViewForWooCommerceGlobal.restNonce,
					},
					beforeSend: function ( request ) {
						request.setRequestHeader( 'X-WP-Nonce', qodeQuickViewForWooCommerceGlobal.restNonce );
						$button.addClass( 'qqvfw--loading' );
					},
					complete: function () {
						$holder.removeClass( 'qqvfw--loading' );
						$button.removeClass( 'qqvfw--loading' );
					},
					success: function ( response ) {
						if ( response.status === 'success' && response.data.html !== '' ) {
							qqvfwInitQuickView.updateProductContent( $productContent, response.data.html );
							qqvfwInitQuickView.setNavigationEvents( $holder, currentProduct.parent(), '.qqvfw-m-nav', '.qqvfw-m-nav-item' );
							qqvfwInitQuickView.setSuggestedProductsEvents( $holder, currentProduct.parent(), '.qqvfw-m-suggested-products', '.qqvfw-e-quick-view-link' );

							$( document.body ).trigger( 'qode_quick_view_for_woocommerce_trigger_quick_view', [ $holder, $button ] );

							// reInit WooCommerce Scripts.
							qqvfwInitQuickView.reInitProductVariationScripts( $productContent );
							qqvfwInitQuickView.reInitProductGalleryScripts( $productContent );

							// Init Perfect Scrollbar Script.
							qqvfwInitQuickView.initPerfectScrollbar( $holder );

							// Init Product Tabs.
							qqvfwInitQuickView.initProductTabs( $holder );

							// Init Suggested Product flex slider.
							qqvfwInitQuickView.initSuggestedProductsSlider( $holder );

							// Include Ajax Add To Cart Functionality.
							qqvfwInitQuickView.includeAjaxAddToCart( $holder, response, availableProductTypes );

							// Calculate Perfect Scrollbar Available Space.
							if ( ! $holder.is( '#qode-quick-view-for-woocommerce-drop' ) ) {
								qqvfwInitQuickView.calculateScrollContentBottomSpace( $holder );
							}

						} else if ( response.status === 'error' ) {
							console.log( response.message );
						}
					}
				}
			);
		},
		updateProductContent: function ( $productContent, newHTML ) {
			$productContent.empty().html( newHTML );
		},
		cleanProductContent: function ( $holder ) {
			$holder.find( '.qqvfw-m-product' ).empty();
		},
		setNavigationEvents: function ( $holder, $items, $nav, $navItem ) {
			var $navHolder = $holder.find( $nav );

			if ( $navHolder.length && $items.length ) {
				$navHolder.find( $navItem ).off().on(
					'click',
					function ( e ) {
						e.preventDefault();

						$holder.addClass( 'qqvfw--loading' );

						var $newItem = $items.find( '.qqvfw-quick-view-button[data-item-id="' + parseInt( $( this ).data( 'item-id' ), 10 ) + '"]' );

						if ( $newItem.length ) {
							$newItem.trigger( 'click' );
						} else {
							alert( qodeQuickViewForWooCommerceGlobal.protectedDataMessage );
							$holder.removeClass( 'qqvfw--loading' );
						}
					}
				);
			}
		},
		setSuggestedProductsEvents: function ( $holder, $items, $buttonHolder, $button ) {
			qqvfwInitQuickView.setNavigationEvents( $holder, $items, $buttonHolder, $button );
		},
		reInitProductVariationScripts: function ( productContent ) {
			var form_variation = productContent.find( '.variations_form' ),
				stockStatus    = productContent.find( '.product_meta .stock_wrapper' );

			form_variation.each(
				function () {
					$( this ).wc_variation_form();

					$( this ).on(
						'hide_variation',
						function (event) {
							var addToCartButton = $( this ).find( '.single_add_to_cart_button' );

							if ( addToCartButton.hasClass( 'wc-variation-selection-needed' ) ) {
								stockStatus.hide();
							}
						}
					);

					$( this ).on(
						'show_variation',
						function ( event, variation ) {
							var stockStatusHTML = variation.availability_html;

							// Remove any existing stock status element.
							stockStatusHTML = ! stockStatusHTML ? '<p class="stock in-stock">' + qodeQuickViewForWooCommerceGlobal.inStockText + '</p>' : stockStatusHTML;
							stockStatus.show();
							stockStatus.find( '.stock' ).remove();
							stockStatus.append( stockStatusHTML );
						}
					);
				}
			);

			form_variation.trigger( 'check_variations' );
			form_variation.trigger( 'reset_image' );
		},
		reInitProductGalleryScripts: function ( productContent ) {
			if ( typeof $.fn.wc_product_gallery !== 'undefined' ) {
				productContent.find( '.woocommerce-product-gallery' ).each(
					function () {
						$( this ).wc_product_gallery();
					}
				);
			}
		},
		setVariationsAttributes: function ( $holder ) {
			var variationForm = $holder.find( '.variations_form' );

			if ( variationForm.length ) {
				var variations = {};

				variationForm.find( 'select[name^=attribute]' ).each(
					function () {
						var attribute = $( this ).attr( 'name' );

						variations[attribute] = $( this ).val();
					}
				);

				return variations;
			}
		},
		validateVariationsAttributes: function ( $button ) {
			if ( $button.is( '.disabled' ) ) {
				if ( $button.is( '.wc-variation-is-unavailable' ) || $button.is( '.wc-variation-selection-needed' ) ) {
					return false;
				}
			}

			return true;
		},
		setGroupedOptions: function ( $holder ) {
			var groupedForm = $holder.find( '.grouped_form' );

			if ( groupedForm.length ) {
				var options = {};

				groupedForm.find( '.product' ).each(
					function () {
						var option = $( this ).attr( 'id' );

						options[option] = $( this ).find( 'input' ).val();
					}
				);

				return options;
			}
		},
		validateGroupedOptions: function ( $holder ) {
			var options = qqvfwInitQuickView.setGroupedOptions( $holder );

			if ( typeof options !== 'undefined' ) {
				var isEmpty = Object.values( options ).every( value => value === '' || value === '0' );

				if ( isEmpty ) {
					return false;
				}
			}

			return true;
		},
		includeAjaxAddToCart: function ( $holder, response, availableProductTypes ) {
			if ( $holder.hasClass( 'qqvfw-ajax-add-to-cart--enabled' ) && ( $.inArray( response.data.product_type, availableProductTypes ) >= 0 ) ) {
				qqvfwInitQuickView.ajaxAddToCart( $holder );
			}
		},
		ajaxAddToCart: function ( holder ) {
			var addToCartButton = holder.find( '.single_add_to_cart_button' );

			addToCartButton.on(
				'click',
				function (e) {
					e.preventDefault();

					var $thisButton  = $( this ),
						$productForm = $thisButton.closest( 'form.cart' ),
						buttonId     = $thisButton.val(),
						productQty   = $productForm.find( 'input[name=quantity]' ).val() || 1,
						productId    = $productForm.find( 'input[name=add-to-cart]' ).val() || buttonId,
						variationId  = $productForm.find( 'input[name=variation_id]' ).val() || 0,
						nonce        = $productForm.find( '#qode-quick-view-for-woocommerce-add-to-cart-nonce' );

					var data = {
						action: 'qode_quick_view_for_woocommerce_premium_ajax_add_to_cart',
						product_id: productId,
						product_sku: '',
						quantity: productQty,
						variation_id: variationId,
						variations: qqvfwInitQuickView.setVariationsAttributes( holder ),
						options: qqvfwInitQuickView.setGroupedOptions( holder ),
						nonce_value: nonce.val(),
					};

					// check product input validations.
					if ( ! qqvfwInitQuickView.validateVariationsAttributes( $thisButton ) ) {
						return;
					}

					if ( ! qqvfwInitQuickView.validateGroupedOptions( holder ) ) {
						window.alert( qodeQuickViewForWooCommerceGlobal.emptyQuantityText );
						return;
					}

					$( document.body ).trigger( 'adding_to_cart', [ $thisButton, data ] );

					$.ajax(
						{
							type: 'POST',
							url: wc_add_to_cart_params.ajax_url,
							data: data,
							beforeSend: function () {
								$thisButton.removeClass( 'added' ).addClass( 'loading' );
							},
							complete: function () {
								$thisButton.addClass( 'added' ).removeClass( 'loading' );
							},
							success: function ( response ) {
								if ( response.error && response.product_url ) {
									window.location = response.product_url;

								} else {
									$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $thisButton ] );

									// Include Ajax Add To Cart Additional Functionalities.
									qqvfwInitQuickView.autoCloseAfterAjaxAddToCart( holder );
									qqvfwInitQuickView.redirectToCheckoutAfterAjaxAddToCart( holder );
								}
							},
						}
					);

					return false;
				}
			);
		},
		autoCloseAfterAjaxAddToCart: function ( $holder ) {
			if ( $holder.is( '.qqvfw-auto-close--enabled' ) ) {
				// setTimeout -> wait for the view cart button to appear.
				setTimeout(
					function () {
						qqvfwInitQuickView.hideQuickView( $holder );
					},
					1000
				);
			}
		},
		redirectToCheckoutAfterAjaxAddToCart: function ( $holder ) {
			if ( $holder.is( '.qqvfw-redirect-to-checkout--enabled' ) ) {
				// Redirect to the checkout page if global option is enabled.
				window.location = qodeQuickViewForWooCommerceGlobal.checkoutUrl;

			}
		},
		centerSuggestedProductsSliderArrows: function ( $slider ) {
			var sliderArrowLeft  = $slider.find( '.flex-prev' ),
				sliderArrowRight = $slider.find( '.flex-next' );

			if ( sliderArrowLeft.length && sliderArrowRight.length  ) {
				var sliderImage          = $slider.find( '.qqvfw-e-suggested-product-image' ),
					sliderImageMaxHeight = 0;

				if ( sliderImage.length ) {
					sliderImage.each(
						function () {
							if ( $( this ).outerHeight() > sliderImageMaxHeight ) {
								sliderImageMaxHeight = $( this ).outerHeight();
							}
						}
					);
					sliderArrowLeft.css( 'top', sliderImageMaxHeight / 2 )
					sliderArrowRight.css( 'top',sliderImageMaxHeight / 2 )
				}
			}
		},
		initSuggestedProductsSlider: function ( $holder ) {
			var suggestedProducts = $holder.find( '.qqvfw-m-suggested-products' );

			if ( suggestedProducts.length ) {
				var suggestedProductsSlider     = suggestedProducts.find( '.flexslider' ),
					suggestedProductsItems      = window.innerWidth > 680 ? 3 : 2,
					suggestedProductsItemMargin = 15,
					suggestedProductsItemWidth  = ( suggestedProducts.width() / suggestedProductsItems ) - ( suggestedProductsItemMargin / 2 );

				if ( suggestedProductsSlider.length ) {
					suggestedProductsSlider.flexslider(
						{
							animation: "slide",
							animationLoop: false,
							slideshow: false,
							controlNav: false,
							prevText: qodeQuickViewForWooCommerceGlobal.arrowLeft,
							nextText: qodeQuickViewForWooCommerceGlobal.arrowRight,
							itemWidth: suggestedProductsItemWidth,
							itemMargin: suggestedProductsItemMargin,
							start: function () {
								qqvfwInitQuickView.centerSuggestedProductsSliderArrows( suggestedProductsSlider )
							},
						}
					);
				}
			}
		},
		calculateScrollContentBottomSpace: function ( $holder ) {
			var summarySection     = $holder.find( '.summary' ),
				viewDetailsSection = $holder.find( '.qqvfw-m-product-view-details' ),
				viewDetailsHeight  = viewDetailsSection.length ? viewDetailsSection.outerHeight() : 0,
				// 18 is additional spacing.
				viewDetailsTotalHeight = viewDetailsHeight > 0 ? viewDetailsHeight + ( viewDetailsHeight / 2 + 18 ) : parseInt( summarySection.css( 'padding-bottom' ), 10 ),
				adminBar               = qodeQuickViewForWooCommerce.body.find( '#wpadminbar' ),
				adjustedHeight         = $holder.is( '.qqvfw-type--sidebar' ) && adminBar.length ? adminBar.outerHeight() : 0,
				calculatedHeight       = adjustedHeight + viewDetailsTotalHeight;

			summarySection.css( 'padding-bottom', calculatedHeight );

		},
		initPerfectScrollbar: function ( $holder ) {
			if ( ! $holder.hasClass( 'qqvfw-type--drop' ) ) {
				var scrollContent = $holder.find( $holder.is( '#qode-quick-view-for-woocommerce-sidebar' ) || ( $holder.is( '#qode-quick-view-for-woocommerce-pop-up' ) && window.innerWidth <= 880 ) ? '.qqvfw-m-product > .product' : '.summary' );

				if ( typeof qodeQuickViewForWooCommerce.qodeQuickViewForWooCommercePerfectScrollbar === 'object' ) {
					qodeQuickViewForWooCommerce.qodeQuickViewForWooCommercePerfectScrollbar.init( scrollContent );
				}
			}
		},
		initProductTabs: function ( $holder ) {
			var defaultWooTabs = $holder.find( '.woocommerce-tabs' ),
				qqvfwTabs      = $holder.find( '.qqvfw-m-accordion-tabs' );

			if ( qqvfwTabs.length ) {

				qqvfwTabs.accordion(
					{
						active: false,
						collapsible: true,
						heightStyle: 'content'
					}
				);
			}

			if ( defaultWooTabs.length ) {
				defaultWooTabs.find( 'li' ).first().addClass( 'active' );
			}
		},
		attachQuickView: function ( $type ) {
			var hiddenQuickViewType    = $( '.qqvfw-hidden-type' ),
				isMobile               = window.innerWidth <= 1024,
				quickViewType          = hiddenQuickViewType.data( 'quick-view-type' ),
				quickViewMobileType    = hiddenQuickViewType.data( 'quick-view-type-mobile' ),
				applyOnTouchDevice     = isMobile && quickViewMobileType === $type,
				applyOnNonTouchDevices = ! isMobile && quickViewType === $type;

			return applyOnTouchDevice || applyOnNonTouchDevices;
		},
	};

	qodeQuickViewForWooCommerce.qqvfwInitQuickView = qqvfwInitQuickView;

})( jQuery );

(function ( $ ) {

	$( document ).on(
		'qode_quick_view_for_woocommerce_trigger_quick_view',
		function ( e, $holder ) {
			qqvfwInitQuickViewButton.reInit( $holder );
		}
	);

	'use strict';

	/**
	 * Function object that represents quick view button.
	 *
	 * @returns {{init: Function}}
	 */
	var qqvfwInitQuickViewButton = {
		init: function ( $holder ) {
			var $quickViewButtonClass = '.qqvfw-quick-view-button';
			var $quickViewButtons     = $( $quickViewButtonClass );

			if ( $holder.length ) {
				qqvfwInitQuickViewButton.addItemOnCart( $holder );

				if ( $quickViewButtons.length ) {
					// Document click with selector is set when ajax render new items.
					$( document ).on(
						'click',
						$quickViewButtonClass,
						function ( e ) {
							e.preventDefault();

							if ( ! $( this ).is( '.qqvfw--loading' ) ) {
								qodeQuickViewForWooCommerce.qqvfwInitQuickView.addItem( $holder, $( this ) );
							}
						}
					);
				}

				qodeQuickViewForWooCommerce.qqvfwInitQuickView.init( $holder );
			}
		},
		reInit: function ( $holder ) {
			var $quickViewButtons = $holder.find( '.qqvfw-quick-view-button' );

			if ( $quickViewButtons.length ) {
				this.addItem( $holder, $quickViewButtons );
			}
		},
		addItem: function ( $holder, $buttons ) {
			if ( $buttons.length ) {
				$( $buttons ).off().on(
					'click',
					function ( e ) {
						e.preventDefault();

						if ( ! $buttons.is( '.qqvfw--loading' ) ) {
							qodeQuickViewForWooCommerce.qqvfwInitQuickView.addItem( $holder, $( this ) );
						}
					}
				);
			}
		},
		addItemOnCart: function ( $holder ) {
			var spinnerHTML = $( '<span class="qqvfw-m-spinner"><svg class="qqvfw-svg--spinner" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"></path></svg></span>' );

			$( document ).on(
				'click touch',
				'[href*="#qqvfw-"]',
				function (e) {
					var href    = $( this ).attr( 'href' ),
						regEx   = /#qqvfw-([0-9]+)/g,
						match   = regEx.exec( href ),
						spinner = $( this ).find( spinnerHTML );

					// check if product id exists.
					if ( match[1] !== undefined ) {
						var quickViewType = $holder.is( '#qode-quick-view-for-woocommerce-sidebar' ) ? 'sidebar' : 'pop-up';

						$( this ).addClass( 'qqvfw-quick-view-button' );
						$( this ).attr( 'data-item-id', parseInt( match[1], 10 ) );
						$( this ).attr( 'data-quick-view-type', quickViewType );

						if ( ! spinner.length ) {
							$( this ).append( spinnerHTML );
						}

						qodeQuickViewForWooCommerce.qqvfwInitQuickView.addItem( $holder, $( this ) );
						e.preventDefault();
					}
				}
			);
		},
	};

	qodeQuickViewForWooCommerce.qqvfwInitQuickViewButton = qqvfwInitQuickViewButton;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qqvfwInitQuickViewPopUp.init();
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_wishlist_table_updated',
		function () {
			qqvfwInitQuickViewPopUp.init();
		}
	);

	/**
	 * Function object that represents quick view area popup.
	 *
	 * @returns {{init: Function}}
	 */
	var qqvfwInitQuickViewPopUp = {
		init: function () {
			var $holder = $( '#qode-quick-view-for-woocommerce-pop-up' );

			if ( $holder.length ) {
				if ( qodeQuickViewForWooCommerce.qqvfwInitQuickView.attachQuickView( 'pop-up' ) ) {
					qodeQuickViewForWooCommerce.qqvfwInitQuickViewButton.init( $holder );
					qqvfwInitQuickViewPopUp.setEventsAction( $holder );
				} else {
					$holder.detach();
				}
			}
		},
		setEventsAction: function ( $holder ) {
			$holder.children( '.qqvfw-m-overlay' ).on(
				'click',
				function () {
					qodeQuickViewForWooCommerce.qqvfwInitQuickView.hideQuickView( $holder );
				}
			);

			// Esc press.
			$( window ).on(
				'keyup',
				function ( e ) {
					if ( e.keyCode === 27 ) {
						qodeQuickViewForWooCommerce.qqvfwInitQuickView.hideQuickView( $holder );
					}
				}
			);
		},
	};

})( jQuery );
