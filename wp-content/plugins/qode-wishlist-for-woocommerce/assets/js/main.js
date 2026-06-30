(function ( $ ) {
	'use strict';

	window.qodeWishlistForWooCommerce 	  = {};
	qodeWishlistForWooCommerce.shortcodes = {};

	qodeWishlistForWooCommerce.body         = $( 'body' );
	qodeWishlistForWooCommerce.html         = $( 'html' );
	qodeWishlistForWooCommerce.windowWidth  = $( window ).width();
	qodeWishlistForWooCommerce.windowHeight = $( window ).height();
	qodeWishlistForWooCommerce.scroll       = 0;

	$( document ).ready(
		function () {
			qodeWishlistForWooCommerce.scroll = $( window ).scrollTop();
		}
	);

	$( window ).resize(
		function () {
			qodeWishlistForWooCommerce.windowWidth  = $( window ).width();
			qodeWishlistForWooCommerce.windowHeight = $( window ).height();
		}
	);

	$( window ).scroll(
		function () {
			qodeWishlistForWooCommerce.scroll = $( window ).scrollTop();
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
	var qodeWishlistForWooCommerceAppear = {
		init: function () {
			this.holder = $( '.qwfw--has-appear:not(.qwfw--appeared)' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						var $holder = $( this );

						qodeWishlistForWooCommerce.qodeWishlistForWooCommerceIsInViewport.check(
							$holder,
							function () {
								qodeWishlistForWooCommerce.qodeWishlistForWooCommerceWaitForImages.check(
									$holder,
									function () {
										$holder.addClass( 'qwfw--appeared' );
									}
								);
							}
						);
					}
				);
			}
		},
	};

	qodeWishlistForWooCommerce.qodeWishlistForWooCommerceAppear = qodeWishlistForWooCommerceAppear;

	var qodeWishlistForWooCommerceIsInViewport = {
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

	qodeWishlistForWooCommerce.qodeWishlistForWooCommerceIsInViewport = qodeWishlistForWooCommerceIsInViewport;

	/**
	 * Check element images to loaded
	 */
	var qodeWishlistForWooCommerceWaitForImages = {
		check: function ( $element, callback ) {
			if ( $element.length ) {
				var images       = $element.find( 'img' );
				var images_count = images.length;

				if ( images_count ) {
					var counter = 0;

					for ( var index = 0; index < images_count; index++ ) {
						var img = images[index];

						if ( img.complete ) {
							counter++;

							if ( counter === images_count ) {
								callback.call( $element );
							}
						} else {
							var image = new Image();

							image.addEventListener(
								'load',
								function () {
									counter++;
									if ( counter === images_count ) {
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

	qodeWishlistForWooCommerce.qodeWishlistForWooCommerceWaitForImages = qodeWishlistForWooCommerceWaitForImages;

	var qodeWishlistForWooCommerceScroll = {
		disable: function () {
			if ( window.addEventListener ) {
				window.addEventListener(
					'wheel',
					qodeWishlistForWooCommerceScroll.preventDefaultValue,
					{ passive: false }
				);
			}

			document.onkeydown = qodeWishlistForWooCommerceScroll.keyDown;
		},
		enable: function () {
			if ( window.removeEventListener ) {
				window.removeEventListener(
					'wheel',
					qodeWishlistForWooCommerceScroll.preventDefaultValue,
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
					qodeWishlistForWooCommerceScroll.preventDefaultValue( e );
					return;
				}
			}
		}
	};

	qodeWishlistForWooCommerce.qodeWishlistForWooCommerceScroll = qodeWishlistForWooCommerceScroll;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodeWishlistForWooCommerceInitWishlistModal.init();
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_show_wishlist_modal',
		function ( e, popupHTML, itemID, itemAdded ) {
			var $wishlistModal = $( '#qode-wishlist-for-woocommerce-modal' );

			if ( $wishlistModal.length && '' !== popupHTML ) {

				if ( itemAdded ) {
					$wishlistModal.addClass( 'qwfw--item-added' );
					$wishlistModal.attr( 'data-item-id', itemID );
				}

				qodeWishlistForWooCommerceInitWishlistModal.updateModalContent( $wishlistModal, popupHTML );

				qodeWishlistForWooCommerceInitWishlistModal.showWishlist( $wishlistModal );
			}
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_hide_wishlist_modal',
		function ( e, itemID ) {
			var $wishlistModal = $( '#qode-wishlist-for-woocommerce-modal' );

			if ( $wishlistModal.length && $wishlistModal.hasClass( 'qwfw--item-added' ) && itemID === parseInt( $wishlistModal.attr( 'data-item-id' ), 10 ) ) {
				$wishlistModal.removeClass( 'qwfw--item-added' );

				qodeWishlistForWooCommerceInitWishlistModal.hideWishlist( $wishlistModal );
			}
		}
	);

	/**
	 * Function object that represents wishlist modal.
	 *
	 * @returns {{init: Function}}
	 */
	var qodeWishlistForWooCommerceInitWishlistModal = {
		init: function () {
			var $wishlistModal = $( '#qode-wishlist-for-woocommerce-modal' );

			if ( $wishlistModal.length ) {
				this.setEventsAction( $wishlistModal );
			}
		},
		setEventsAction: function ( $modal ) {

			$modal.find( '.qwfw-m-close' ).on(
				'click tap',
				function ( e ) {
					e.preventDefault();

					qodeWishlistForWooCommerceInitWishlistModal.hideWishlist( $modal );
				}
			);

			$modal.children( '.qwfw-m-overlay' ).on(
				'click tap',
				function () {
					qodeWishlistForWooCommerceInitWishlistModal.hideWishlist( $modal );
				}
			);

			// Esc press.
			$( window ).on(
				'keyup',
				function ( e ) {
					if ( e.keyCode === 27 ) {
						qodeWishlistForWooCommerceInitWishlistModal.hideWishlist( $modal );
					}
				}
			);
		},
		showWishlist: function ( $modal ) {
			if ( ! $modal.hasClass( 'qwfw--opened' ) ) {
				$modal.addClass( 'qwfw--opened' );

				qodeWishlistForWooCommerce.qodeWishlistForWooCommerceScroll.disable();
			}
		},
		hideWishlist: function ( $modal, $adding_trigger ) {
			if ( $modal.hasClass( 'qwfw--opened' ) ) {
				$modal.removeClass( 'qwfw--opened' );

				if ( typeof $adding_trigger === 'undefined' ) {
					$modal.addClass( 'qwfw--adding' );
				}

				qodeWishlistForWooCommerce.qodeWishlistForWooCommerceScroll.enable();

				setTimeout(
					function () {
						qodeWishlistForWooCommerceInitWishlistModal.cleanModalContent( $modal );
					},
					1000
				);
			}
		},
		updateModalContent: function ( $modal, newHTML ) {
			$modal.find( '.qwfw-m-product' ).empty().html( newHTML );

			// Return to shop behavior, redirect to the shop page from single product page.
			$modal.find( '.qwfw-m-form-response-button.qwfw--shop' ).on(
				'click tap',
				function ( e ) {

					if ( ! $( document.body ).hasClass( 'single-product' ) ) {
						e.preventDefault();

						qodeWishlistForWooCommerceInitWishlistModal.hideWishlist( $modal );
					}
				}
			);
		},
		cleanModalContent: function ( $modal ) {
			$modal.find( '.qwfw-m-product' ).empty();
		},
	};

	qodeWishlistForWooCommerce.qodeWishlistForWooCommerceInitWishlistModal = qodeWishlistForWooCommerceInitWishlistModal;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodeWishlistForWooCommerceInitAddToWishlist.init();
		}
	);

	$( document ).on(
		'qode_quick_view_for_woocommerce_trigger_quick_view',
		function ( e, $holder ) {
			qodeWishlistForWooCommerceInitAddToWishlist.reInit( $holder );
		}
	);

	$( document ).on(
		'woocommerce_variation_has_changed',
		function ( event ) {
			var $variations = $( '.single_variation_wrap' );

			if ( $variations.length ) {
				var productID   = $variations.find( 'input[name="product_id"]' ).val(),
					variationID = $variations.find( 'input[name="variation_id"]' ).val();

				if ( productID ) {
					qodeWishlistForWooCommerceInitAddToWishlist.setVariationsAttributes( productID, variationID );
				}
			}
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_wishlist_table_updated',
		function ( e, $holder, itemIDs, table, action, ajaxType ) {

			if ( typeof ajaxType === 'undefined' && itemIDs.length ) {
				itemIDs.forEach(
					function ( itemID ) {
						var $wishlistButtons = $( '.qwfw-add-to-wishlist[data-item-id=' + itemID + ']' );

						if ( $wishlistButtons.length ) {
							$wishlistButtons.each(
								function () {
									qodeWishlistForWooCommerceInitAddToWishlist.updateItem( $( this ), action, itemID, true );
								}
							);
						}
					}
				);
			}
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_wishlist_widget_updated',
		function ( e, $holder, itemIDs, table, action, ajaxType ) {

			if ( typeof ajaxType === 'undefined' && itemIDs.length ) {
				itemIDs.forEach(
					function ( itemID ) {
						var $wishlistButtons = $( '.qwfw-add-to-wishlist[data-item-id=' + itemID + ']' );

						if ( $wishlistButtons.length ) {
							$wishlistButtons.each(
								function () {
									qodeWishlistForWooCommerceInitAddToWishlist.updateItem( $( this ), action, itemID, true );
								}
							);
						}
					}
				);
			}
		}
	);

	/**
	 * Function object that represents wishlist modal.
	 *
	 * @returns {{init: Function}}
	 */
	var qodeWishlistForWooCommerceInitAddToWishlist = {
		init: function () {
			var $wishlistClass	 = '.qwfw-add-to-wishlist:not(.qwfw-behavior--view.qwfw--added):not(.qwfw--disable)';
			var $wishlistButtons = $( $wishlistClass );

			if ( $wishlistButtons.length ) {
				// Document click with selector is set when ajax render new items.
				$( document ).on(
					'click',
					$wishlistClass,
					function ( e ) {
						e.preventDefault();

						qodeWishlistForWooCommerceInitAddToWishlist.updateItem( $( this ) );
					}
				);
			}

			this.adjsWishlistPosition();
			this.checkLoopVariations();
		},
		reInit: function ( $holder ) {
			var $wishlistButtons = $holder.find( '.qwfw-add-to-wishlist:not(.qwfw-behavior--view.qwfw--added):not(.qwfw--disable)' );

			if ( $wishlistButtons.length ) {
				qodeWishlistForWooCommerceInitAddToWishlist.setEventsAction( $wishlistButtons );
			}

			this.adjsWishlistPosition();
		},
		setEventsAction: function ( $button ) {

			if ( $button ) {
				$button.off().on(
					'click tap',
					function ( e ) {
						e.preventDefault();

						qodeWishlistForWooCommerceInitAddToWishlist.updateItem( $( this ) );
					}
				);
			}
		},
		adjsWishlistPosition: function () {
			var $thumbnailWishlists = $( '.qwfw-add-to-wishlist-wrapper.qwfw-position--on-thumbnail.qwfw-thumb--top-right.qwfw-count--on, .qwfw-add-to-wishlist-wrapper.qwfw-position--on-thumbnail.qwfw-thumb--bottom-right.qwfw-count--on' );

			if ( $thumbnailWishlists.length ) {
				$thumbnailWishlists.each(
					function () {
						var $thumbnailWishlist = $( this ),
							$count             = $thumbnailWishlist.find( '.qwfw-m-count.qwfw--visible' );

						if ( $count.length ) {
							$thumbnailWishlist.css(
								'--qwfw-atw-thumb-offset-adjs',
								$count.outerWidth() + 'px'
							);
						}
					}
				);
			}
		},
		checkLoopVariations: function () {
			var $variations = $( '.qvsfw-list-variations-form' );

			if ( $variations.length ) {
				$variations.on(
					'change',
					function () {
						var $thisVariation = $( this );

						var loopProductID   = $thisVariation.attr( 'data-product_id' ),
							loopVariationID = typeof $thisVariation.attr( 'data-variation_id' ) !== 'undefined' ? $thisVariation.attr( 'data-variation_id' ) : '';

						qodeWishlistForWooCommerceInitAddToWishlist.setVariationsAttributes( loopProductID, loopVariationID );
					}
				);
			}
		},
		setVariationsAttributes: function ( productID, variationID ) {
			var $wishlistButtons = $( '.qwfw-add-to-wishlist[data-original-item-id="' + productID + '"]:not(.qwfw--disable)' );

			if ( $wishlistButtons.length ) {
				variationID = variationID.length ? variationID : productID;

				$wishlistButtons.attr( 'data-item-id', variationID );

				qodeWishlistForWooCommerceInitAddToWishlist.updateVariationItem( $wishlistButtons, 'update_variation', variationID );
			}
		},
		updateItem: function ( $button, action, newID, onlyAjax, redirectToCart, bulkTriggered ) {

			if ( ! $button ) {
				return true;
			}

			var buttonOptions = JSON.parse( $button.attr( 'data-shortcode-atts' ) ),
				buttonAction  = typeof action !== 'undefined' ? action : 'remove' === buttonOptions.button_behavior && $button.hasClass( 'qwfw--added' ) ? 'remove' : 'add',
				itemID        = parseInt( typeof newID !== 'undefined' ? newID : $button.attr( 'data-item-id' ), 10 );

			$.ajax(
				{
					type: 'POST',
					url: qodeWishlistForWooCommerceGlobal.restUrl + qodeWishlistForWooCommerceGlobal.addToWishlistRestRoute,
					data: {
						item_id: itemID,
						action: buttonAction,
						options: $button.attr( 'data-shortcode-atts' ),
						security_token: qodeWishlistForWooCommerceGlobal.restNonce,
					},
					beforeSend: function ( request ) {
						request.setRequestHeader( 'X-WP-Nonce', qodeWishlistForWooCommerceGlobal.restNonce );

						$button.addClass( 'qwfw--loading' );
					},
					complete: function () {
						$button.removeClass( 'qwfw--loading' );
					},
					success: function ( response ) {
						if ( response.status === 'success' ) {

							if ( typeof response.data.popup_html !== 'undefined' && '' !== response.data.popup_html && $button.hasClass( 'qwfw-behavior--add' ) && $button.hasClass( 'qwfw--added' ) ) {
								$( document.body ).trigger( 'qode_wishlist_for_woocommerce_trigger_show_wishlist_modal', [ response.data.popup_html, itemID ] );
							} else {
								var $updateButton = $( '.qwfw-add-to-wishlist[data-item-id=' + itemID + ']' );

								qodeWishlistForWooCommerceInitAddToWishlist.updateProductButton( $updateButton, response.data.button_html );

								if ( $button.hasClass( 'qwfw-behavior--view' ) ) {
									qodeWishlistForWooCommerceInitAddToWishlist.updateProductButtonLink( $updateButton, response.data );
								}

								// Redirect to the cart page if global option is enabled.
								if ( typeof redirectToCart !== 'undefined' && ( typeof bulkTriggered === 'undefined' || bulkTriggered ) ) {
									window.location = redirectToCart;
									return;
								}

								if ( ['add', 'remove'].includes( buttonAction ) ) {

									if ( 'remove' === buttonAction ) {
										$updateButton.removeClass( 'qwfw--added' );
									} else {
										$updateButton.addClass( 'qwfw--added' );
									}

									if ( typeof onlyAjax === 'undefined' ) {
										$( document.body ).trigger( 'qode_wishlist_for_woocommerce_trigger_updated_wishlist_item', [ $button, itemID, buttonAction ] );

										$( document.body ).trigger( 'qode_wishlist_for_woocommerce_trigger_show_wishlist_modal', [ response.data.popup_html, itemID, true ] );

										setTimeout(
											function () {
												$( document.body ).trigger( 'qode_wishlist_for_woocommerce_trigger_hide_wishlist_modal', [ itemID ] );
											},
											qodeWishlistForWooCommerceGlobal.hideWishlistModalTime
										);
									}
								} else if ( 'update_variation' === buttonAction ) {

									if ( response.data.is_item_added ) {
										$updateButton.addClass( 'qwfw--added' );
									} else {
										$updateButton.removeClass( 'qwfw--added' );
									}

									if ( typeof onlyAjax === 'undefined' ) {
										$( document.body ).trigger( 'qode_wishlist_for_woocommerce_trigger_updated_wishlist_item', [ $button, itemID, buttonAction ] );
									}
								}
							}
						} else if ( response.status === 'error' ) {
							console.log( response.message );
						}
					}
				}
			);
		},
		updateVariationItem: function ( $button, action, newID ) {

			if ( ! $button ) {
				return true;
			}

			$button.each(
				function () {
					var $thisButton = $( this );

					var buttonOptions = JSON.parse( $thisButton.attr( 'data-shortcode-atts' ) ),
						buttonAction  = typeof action !== 'undefined' ? action : 'remove' === buttonOptions.button_behavior && $thisButton.hasClass( 'qwfw--added' ) ? 'remove' : 'add',
						itemID        = parseInt( typeof newID !== 'undefined' ? newID : $thisButton.attr( 'data-item-id' ), 10 );

					$.ajax(
						{
							type: 'POST',
							url: qodeWishlistForWooCommerceGlobal.restUrl + qodeWishlistForWooCommerceGlobal.addToWishlistRestRoute,
							data: {
								item_id: itemID,
								action: buttonAction,
								options: $thisButton.attr( 'data-shortcode-atts' ),
								security_token: qodeWishlistForWooCommerceGlobal.restNonce,
							},
							beforeSend: function ( request ) {
								request.setRequestHeader( 'X-WP-Nonce', qodeWishlistForWooCommerceGlobal.restNonce );

								$thisButton.addClass( 'qwfw--loading' );
							},
							complete: function () {
								$thisButton.removeClass( 'qwfw--loading' );
							},
							success: function ( response ) {
								if ( response.status === 'success' ) {
									qodeWishlistForWooCommerceInitAddToWishlist.updateProductButton( $thisButton, response.data.button_html );

									if ( $thisButton.hasClass( 'qwfw-behavior--view' ) ) {
										qodeWishlistForWooCommerceInitAddToWishlist.updateProductButtonLink( $thisButton, response.data );
									}

									if ( response.data.is_item_added ) {
										$thisButton.addClass( 'qwfw--added' );
									} else {
										$thisButton.removeClass( 'qwfw--added' );
									}
								} else if ( response.status === 'error' ) {
									console.log( response.message );
								}
							}
						}
					);
				}
			);
		},
		updateProductButton: function ( $button, newHTML ) {

			if ( '' !== newHTML ) {
				$button.empty().html( newHTML );

				qodeWishlistForWooCommerceInitAddToWishlist.adjsWishlistPosition();
			}
		},
		updateProductButtonLink: function ( $button, response ) {

			if ( typeof response.wishlist_page_url !== 'undefined' && '' !== response.wishlist_page_url ) {
				$button.attr( 'href', response.wishlist_page_url );

				$button.off().on(
					'click tap',
					function ( e ) {
						e.preventDefault();

						window.location = $( this ).attr( 'href' );
					}
				);
			}

			// Return behavior to initial if variations are updated.
			if ( typeof response.is_item_added !== 'undefined' && ! response.is_item_added && $button.attr( 'href' ).length > 2 ) {
				qodeWishlistForWooCommerceInitAddToWishlist.setEventsAction( $button );
			}
		},
	};

	qodeWishlistForWooCommerce.qodeWishlistForWooCommerceInitAddToWishlist = qodeWishlistForWooCommerceInitAddToWishlist;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodeWishlistForWooCommerceInitWishlistTable.init();
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_wishlist_table_updated',
		function ( e, $holder ) {

			// Reinitialize table actions.
			qodeWishlistForWooCommerceInitWishlistTable.reInit( $holder );
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_updated_wishlist_item',
		function ( e, $button, itemID, action ) {
			var $holder = $( '.qwfw-wishlist-table' );

			if ( $holder.length ) {
				$holder.each(
					function () {
						qodeWishlistForWooCommerceInitWishlistTable.updateTable( $( this ), $button, [itemID], '', action, 'add-to-wishlist' );
					}
				);
			}
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_updated_multi_wishlist_item',
		function ( e, $button, itemID, tableName, action ) {
			var $holder = $( '.qwfw-wishlist-table[data-table="' + tableName + '"]' );

			if ( $holder.length ) {
				$holder.each(
					function () {
						qodeWishlistForWooCommerceInitWishlistTable.updateTable( $( this ), $button, [itemID], tableName, action, 'add-to-wishlist' );
					}
				);
			}
		}
	);

	$( document ).on(
		'qode_wishlist_for_woocommerce_trigger_wishlist_widget_updated',
		function ( e, $wishlistDropDown, itemIDs, table, action, ajaxType ) {
			var $holder = $( '.qwfw-wishlist-table' );

			if ( typeof ajaxType === 'undefined' && $holder.length && 'remove' === action ) {
				$holder.each(
					function () {
						qodeWishlistForWooCommerceInitWishlistTable.updateTable( $( this ), '', itemIDs, table, action, 'wishlist-dropdown' );
					}
				);
			}
		}
	);

	/**
	 * Function object that represents wishlist modal.
	 *
	 * @returns {{init: Function}}
	 */
	var qodeWishlistForWooCommerceInitWishlistTable = {
		init: function () {
			var $holder = $( '.qwfw-wishlist-table' );

			if ( $holder.length ) {
				$holder.each(
					function () {
						var $thisHolder = $( this );

						qodeWishlistForWooCommerceInitWishlistTable.setEventsAction( $thisHolder );
						qodeWishlistForWooCommerceInitWishlistTable.initOrdering( $thisHolder );
						qodeWishlistForWooCommerceInitWishlistTable.initPagination( $thisHolder );
					}
				);
			}
		},
		reInit: function ( $holder ) {
			qodeWishlistForWooCommerceInitWishlistTable.setEventsAction( $holder );
		},
		setEventsAction: function ( $holder ) {
			var $removeButton = $holder.find( '.qwfw-e-remove-button' );

			if ( $removeButton.length ) {
				$removeButton.off().on(
					'click tap',
					function ( e ) {
						e.preventDefault();

						qodeWishlistForWooCommerceInitWishlistTable.removeItem( $holder, $( this ) );
					}
				);
			}

			var $addToCartButton = $holder.find( '.qwfw-e-add-to-cart:not(.product_type_variable):not(.product_type_external):not(.product_type_grouped):not(.qwfw--out-of-stock)' );

			if ( $addToCartButton.length && ! $holder.hasClass( 'qwfw--has-token' ) ) {
				$addToCartButton.off().on(
					'click tap',
					function ( e ) {
						e.preventDefault();

						qodeWishlistForWooCommerceInitWishlistTable.ajaxAddToCart( $holder, [$( this )] );
					}
				);
			}

			var $quantity = $holder.find( '.qwfw-e-item.product-quantity :input, .qwfw-e-content-item.qwfw--quantity :input' );

			if ( $quantity.length ) {
				var jqxhr,
					timeout;

				$quantity.on(
					'change',
					function () {
						var $thisQuantity = $( this ),
							$row          = $holder.hasClass( 'qwfw-layout--gallery' ) ? $thisQuantity.closest( '.qwfw-m-gallery-item' ) : $thisQuantity.closest( '.qwfw-m-items-content-row' ),
							itemID        = parseInt( $row.attr( 'data-item-id' ), 10 );

						clearTimeout( timeout );

						// Update quantity for add to cart button.
						$row.find( '.qwfw-e-add-to-cart:not(.product_type_variable):not(.product_type_external):not(.product_type_grouped)' ).attr( 'data-quantity', $thisQuantity.val() );

						timeout = setTimeout(
							function () {
								if ( jqxhr ) {
									jqxhr.abort();
								}

								jqxhr = $.ajax(
									{
										type: 'POST',
										url: qodeWishlistForWooCommerceGlobal.restUrl + qodeWishlistForWooCommerceGlobal.wishlistTableUpdateQuantityRestRoute,
										data: {
											item_id: itemID,
											quantity: parseInt( $thisQuantity.val(), 10 ),
											table: $holder.attr( 'data-table' ),
											token: $holder.attr( 'data-token' ),
											security_token: qodeWishlistForWooCommerceGlobal.restNonce,
										},
										beforeSend: function ( request ) {
											request.setRequestHeader( 'X-WP-Nonce', qodeWishlistForWooCommerceGlobal.restNonce );

											$holder.addClass( 'qwfw--table-updating' );
										},
										complete: function () {
											$holder.removeClass( 'qwfw--table-updating' );
										},
										success: function ( response ) {
											if ( response.status === 'error' ) {
												console.log( response.message );
											}
										}
									}
								);
							},
							1000
						);
					}
				);
			}
		},
		removeItem: function ( $holder, $button ) {
			var itemID      = parseInt( $button.attr( 'data-item-id' ), 10 ),
				confirmText = $button.attr( 'data-confirm-text' );

			qodeWishlistForWooCommerceInitWishlistTable.handleConfirmBox( confirmText ).then(
				function ( response ) {

					if ( response ) {
						qodeWishlistForWooCommerceInitWishlistTable.updateTable( $holder, $button, [itemID], '', 'remove' );
					}
				}
			);
		},
		handleConfirmBox: function ( message, simple_modal ) {
			var $confirmModal = $( simple_modal ? qodeWishlistForWooCommerceGlobal.confirmSimpleModalHTML : qodeWishlistForWooCommerceGlobal.confirmModalHTML );

			$confirmModal.find( '.qwfw-m-form-title' ).empty().html( message );

			document.body.appendChild( $confirmModal[0] );

			return new Promise(
				function ( resolve, reject ) {
					var confirmSelector = document.getElementById( 'qwfw-confirm-button-true' );

					if ( confirmSelector ) {
						confirmSelector.addEventListener(
							'click',
							function () {
								resolve( true );
								document.body.removeChild( $confirmModal[0] );
							}
						);
					}

					var closeSelectors = [ 'qwfw-confirm-modal-overlay', 'qwfw-confirm-close-icon', 'qwfw-confirm-button-false' ];

					closeSelectors.forEach(
						function ( closeSelector ) {
							var rejectSelector = document.getElementById( closeSelector );

							if ( rejectSelector ) {
								rejectSelector.addEventListener(
									'click',
									function (e) {
										e.preventDefault();

										resolve( false );
										document.body.removeChild( $confirmModal[0] );
									}
								);
							}
						}
					);
				}
			);
		},
		updateTable: function ( $holder, $button, itemIDs, tableName, action, ajaxType ) {
			var table = typeof tableName !== 'undefined' && tableName.length ? tableName : $holder.attr( 'data-table' );

			if ( typeof itemIDs === 'undefined' || ! itemIDs.length) {

				if ( $button.length ) {
					$button.removeClass( 'qwfw--loading' );
				}

				return;
			}

			$.ajax(
				{
					type: 'POST',
					url: qodeWishlistForWooCommerceGlobal.restUrl + qodeWishlistForWooCommerceGlobal.wishlistTableRestRoute,
					data: {
						item_ids: itemIDs,
						action: action,
						table: table,
						token: $holder.attr( 'data-token' ),
						options: $holder.attr( 'data-shortcode-atts' ),
						security_token: qodeWishlistForWooCommerceGlobal.restNonce,
					},
					beforeSend: function ( request ) {
						request.setRequestHeader( 'X-WP-Nonce', qodeWishlistForWooCommerceGlobal.restNonce );

						$holder.addClass( 'qwfw--table-updating' );

						if ( $button.length ) {
							$button.addClass( 'qwfw--loading' );
						}
					},
					complete: function () {
						$holder.removeClass( 'qwfw--table-updating' );

						if ( $button.length ) {
							$button.removeClass( 'qwfw--loading' );
						}
					},
					success: function ( response ) {
						if ( response.status === 'success' ) {

							if ( response.data.not_found_content ) {
								qodeWishlistForWooCommerceInitWishlistTable.updateShortcodeContent( $holder, response.data.not_found_content, itemIDs, table, action, ajaxType );
							} else {
								qodeWishlistForWooCommerceInitWishlistTable.updateTableContent( $holder, response.data.new_content, itemIDs, table, action, ajaxType );

								// Update table pagination.
								qodeWishlistForWooCommerceInitWishlistTable.updatePaginationHTML( $holder, response.data );

								// Update items count.
								qodeWishlistForWooCommerceInitWishlistTable.updateItemsCountHTML( $holder, response.data );

								// Update table total amount.
								qodeWishlistForWooCommerceInitWishlistTable.updateTotalAmountHTML( $holder, response.data );
							}
						} else if ( response.status === 'error' ) {
							console.log( response.message );
						}
					}
				}
			);
		},
		moveToTable: function ( $holder, itemIDs, table, newTable ) {

			$.ajax(
				{
					type: 'POST',
					url: qodeWishlistForWooCommerceGlobal.restUrl + qodeWishlistForWooCommerceGlobal.wishlistTableMoveItemsRestRoute,
					data: {
						item_ids: itemIDs,
						table: table,
						new_table: newTable,
						security_token: qodeWishlistForWooCommerceGlobal.restNonce,
					},
					beforeSend: function ( request ) {
						request.setRequestHeader( 'X-WP-Nonce', qodeWishlistForWooCommerceGlobal.restNonce );

						$holder.addClass( 'qwfw--table-updating' );
					},
					success: function ( response ) {
						if ( response.status === 'success' ) {
							location.reload();
						}
					}
				}
			);
		},
		updateShortcodeContent: function ( $holder, newHTML, itemIDs, table, action, ajaxType ) {
			$holder.find( '.qwfw-m-inner' ).empty().html( newHTML );

			$( document.body ).trigger( 'qode_wishlist_for_woocommerce_trigger_wishlist_table_updated', [ $holder, itemIDs, table, action, ajaxType ] );
		},
		updateTableContent: function ( $holder, newHTML, itemIDs, table, action, ajaxType ) {
			$holder.find( '.qwfw-m-items' ).empty().html( newHTML );

			$( document.body ).trigger( 'qode_wishlist_for_woocommerce_trigger_wishlist_table_updated', [ $holder, itemIDs, table, action, ajaxType ] );
		},
		ajaxAddToCart: function ( $holder, $buttons, buttonIndex ) {
			// Using default wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart.js AddToCartHandler function.

			if ( $buttons.length ) {
				var currentButtonIndex = typeof buttonIndex !== 'undefined' ? parseInt( buttonIndex, 10 ) : 0;

				var lastItemTriggered = (currentButtonIndex + 1) === $buttons.length,
					$button           = $buttons[currentButtonIndex];

				if ( ! $button.attr( 'data-product_id' ) ) {
					return true;
				}

				var data = {};

				// Fetch changes that are directly added by calling $button.data( key, value ).
				$.each(
					$button.data(),
					function ( key, value ) {
						data[key] = value;
					}
				);

				// Fetch data attributes in $button. Give preference to data-attributes because they can be directly modified by javascript.
				// while `.data` are jquery specific memory stores.
				$.each(
					$button[0].dataset,
					function ( key, value ) {
						data[key] = value;
					}
				);

				$( document.body ).trigger( 'adding_to_cart', [ $button, data ] );

				$.ajax(
					{
						type: 'POST',
						url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' ),
						data: data,
						dataType: 'json',
						beforeSend: function () {
							$holder.addClass( 'qwfw--table-updating' );
							$button.addClass( 'qwfw--loading' );
						},
						complete: function () {
							$holder.removeClass( 'qwfw--table-updating' );

							if ( typeof $button.attr( 'data-remove-from-wishlist' ) === 'undefined' ) {
								$button.removeClass( 'qwfw--loading' );
							}

							// If bulk action is triggered do recursion until the last ajax call.
							if ( ! lastItemTriggered ) {
								qodeWishlistForWooCommerceInitWishlistTable.ajaxAddToCart( $holder, $buttons, currentButtonIndex + 1 );
							}
						},
						success: function ( response ) {

							if ( ! response ) {
								return;
							}

							if ( response.error && response.product_url ) {
								window.location = response.product_url;
								return;
							}

							// Trigger event so themes can refresh other areas.
							$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $button ] );

							// Remove from the wishlist if global option is enabled.
							if ( typeof $button.attr( 'data-remove-from-wishlist' ) !== 'undefined' ) {
								$button.parents( '.qwfw-m-items-content-row' ).css( 'display', 'none' );

								qodeWishlistForWooCommerce.qodeWishlistForWooCommerceInitAddToWishlist.updateItem( $( '.qwfw-add-to-wishlist[data-original-item-id="' + $button.attr( 'data-product_id' ) + '"]:not(.qwfw--disable)' ), 'remove', $button.attr( 'data-product_id' ), false, $button.attr( 'data-redirect-to-cart' ), lastItemTriggered );
							} else {

								// Redirect to the cart page if global option is enabled.
								if ( typeof $button.attr( 'data-redirect-to-cart' ) !== 'undefined' && lastItemTriggered ) {
									window.location = $button.attr( 'data-redirect-to-cart' );
								}
							}
						},
					}
				);
			}
		},
		initOrdering: function ( $holder ) {
			var $orderField = $holder.find( '.qwfw-m-ordering-field' );

			if ( $orderField.length ) {
				$orderField.off().on(
					'change',
					function ( e ) {
						e.preventDefault();

						var $orderValue = $( this ).val();

						qodeWishlistForWooCommerceInitWishlistTable.getNewPaginationItems( $holder, false, $orderValue );
					}
				);
			}
		},
		initPagination: function ( $holder ) {
			// If its dashboard page return it.
			if ( $holder.hasClass( 'qwfw--dashboard-page' ) ) {
				return;
			}

			var $paginationItems = $holder.find( '.qwfw-m-pagination-items' );

			if ( $paginationItems.length ) {
				qodeWishlistForWooCommerceInitWishlistTable.setPaginationEventsAction( $holder, $paginationItems );
			}
		},
		setPaginationEventsAction: function ( $holder, $paginationItems ) {
			$paginationItems.find( '.qwfw-m-pagination-item' ).off().on(
				'click tap',
				function ( e ) {
					e.preventDefault();

					var $clickedItem = $( this );

					if ( ! $clickedItem.hasClass( 'qwfw--active' ) ) {
						qodeWishlistForWooCommerceInitWishlistTable.getNewPaginationItems( $holder, $clickedItem );
					}
				}
			);
		},
		getNewPaginationItems: function ( $holder, $clickedItem, $orderValue ) {
			var $items  = $holder.find( '.qwfw-m-items' ),
				options = JSON.parse( $holder.attr( 'data-shortcode-atts' ) );

			if ( $clickedItem ) {
				options.current = $clickedItem.attr( 'data-paged' );
			}

			if ( typeof $orderValue !== 'undefined' ) {
				options.orderby = $orderValue;
			}

			$holder.attr( 'data-shortcode-atts', JSON.stringify( options ) );

			$.ajax(
				{
					type: 'GET',
					url: qodeWishlistForWooCommerceGlobal.restUrl + qodeWishlistForWooCommerceGlobal.wishlistTablePaginationRestRoute,
					data: {
						options: JSON.stringify( options ),
						token: $holder.attr( 'data-token' ),
						security_token: qodeWishlistForWooCommerceGlobal.restNonce,
					},
					beforeSend: function ( request ) {
						request.setRequestHeader( 'X-WP-Nonce', qodeWishlistForWooCommerceGlobal.restNonce );

						$holder.addClass( 'qwfw--table-updating' );
					},
					complete: function () {
						$holder.removeClass( 'qwfw--table-updating' );
					},
					success: function ( response ) {

						if ( response.status === 'success' ) {
							$items.empty().html( response.data.new_content );

							qodeWishlistForWooCommerceInitWishlistTable.updatePaginationState( $holder, response.data.new_shortcode_atts );

							// Reinitialize table actions.
							qodeWishlistForWooCommerceInitWishlistTable.reInit( $holder );

							$( document.body ).trigger( 'qode_wishlist_for_woocommerce_trigger_wishlist_table_pagination_updated', [ $holder ] );
						} else {
							console.log( response.message );
						}
					},
				}
			);
		},
		updatePaginationState: function ( $holder, options ) {
			var $paginationItems = $holder.find( '.qwfw-m-pagination-items' ),
				$numericItem     = $paginationItems.children( '.qwfw--number' ),
				$prevItem        = $paginationItems.children( '.qwfw--prev' ),
				$nextItem        = $paginationItems.children( '.qwfw--next' ),
				nextPage         = parseInt( options.current, 10 );

			$numericItem.removeClass( 'qwfw--active' ).eq( nextPage - 1 ).addClass( 'qwfw--active' );

			$prevItem.attr( 'data-paged', nextPage - 1 );
			$nextItem.attr( 'data-paged', nextPage + 1 );

			if ( nextPage > 1 ) {
				$prevItem.show();
			} else {
				$prevItem.hide();
			}

			if ( nextPage === options.total ) {
				$nextItem.hide();
			} else {
				$nextItem.show();
			}
		},
		updatePaginationHTML: function ( $holder, response ) {
			var $pagination = $holder.find( '.qwfw-m-pagination' );

			if ( $pagination.length && '' !== response.new_pagination_content ) {
				var options = response.new_shortcode_atts;

				$pagination.empty().html( response.new_pagination_content );

				qodeWishlistForWooCommerceInitWishlistTable.setPaginationVisibility( $holder, parseInt( options.total, 10 ) );

				qodeWishlistForWooCommerceInitWishlistTable.updatePaginationState( $holder, options );

				qodeWishlistForWooCommerceInitWishlistTable.setPaginationEventsAction( $holder, $holder.find( '.qwfw-m-pagination-items' ) );
			}
		},
		setPaginationVisibility: function ( $holder, maxPagesNum ) {
			var $pagination = $holder.find( '.qwfw-m-pagination' );

			if ( maxPagesNum === 1 && ! $pagination.hasClass( 'qwfw--hide' ) ) {
				$pagination.addClass( 'qwfw--hide' );
			} else if ( maxPagesNum > 1 && ! $pagination.is( ':visible' ) ) {
				$pagination.removeClass( 'qwfw--hide' );
			}
		},
		updateItemsCountHTML: function ( $holder, response ) {
			var $itemsCount = $holder.find( '.qwfw-m-section-heading .qwfw-m-section-items-count-value' );

			if ( $itemsCount.length && '' !== response.new_shortcode_atts.items_count ) {
				$itemsCount.empty().html( parseInt( response.new_shortcode_atts.items_count, 10 ) );
			}
		},
		updateTotalAmountHTML: function ( $holder, response ) {
			var $totalAmount = $holder.find( '.qwfw-m-total-amount' );

			if ( $totalAmount.length && '' !== response.new_total_amount_content ) {
				$totalAmount.empty().html( response.new_total_amount_content );
			}
		},
	};

	qodeWishlistForWooCommerce.qodeWishlistForWooCommerceInitWishlistTable = qodeWishlistForWooCommerceInitWishlistTable;

})( jQuery );
