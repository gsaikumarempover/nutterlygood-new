(function ( $ ) {
	'use strict';

	window.qodeCompareForWooCommerce = {};

	qodeCompareForWooCommerce.body         = $( 'body' );
	qodeCompareForWooCommerce.html         = $( 'html' );
	qodeCompareForWooCommerce.windowWidth  = $( window ).width();
	qodeCompareForWooCommerce.windowHeight = $( window ).height();
	qodeCompareForWooCommerce.scroll       = 0;

	$( document ).ready(
		function () {
			qodeCompareForWooCommerce.scroll = $( window ).scrollTop();
		}
	);

	$( window ).resize(
		function () {
			qodeCompareForWooCommerce.windowWidth  = $( window ).width();
			qodeCompareForWooCommerce.windowHeight = $( window ).height();
		}
	);

	$( window ).scroll(
		function () {
			qodeCompareForWooCommerce.scroll = $( window ).scrollTop();
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
	var qodeCompareForWooCommerceAppear = {
		init: function () {
			this.holder = $( '.qcfw--has-appear:not(.qcfw--appeared)' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						var $holder = $( this );

						qodeCompareForWooCommerce.qodeCompareForWooCommerceIsInViewport.check(
							$holder,
							function () {
								qodeCompareForWooCommerce.qodeCompareForWooCommerceWaitForImages.check(
									$holder,
									function () {
										$holder.addClass( 'qcfw--appeared' );
									}
								);
							}
						);
					}
				);
			}
		},
	};

	qodeCompareForWooCommerce.qodeCompareForWooCommerceAppear = qodeCompareForWooCommerceAppear;

	var qodeCompareForWooCommerceIsInViewport = {
		check: function ( $element, callback, onlyOnce, callbackOnExit ) {
			if ( $element.length ) {
				var offset = typeof $element.data( 'viewport-offset' ) !== 'undefined' ? $element.data( 'viewport-offset' ) : 0.15;
				// When item is 15% in the viewport.

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

	qodeCompareForWooCommerce.qodeCompareForWooCommerceIsInViewport = qodeCompareForWooCommerceIsInViewport;

	/**
	 * Check element images to loaded
	 */
	var qodeCompareForWooCommerceWaitForImages = {
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

	qodeCompareForWooCommerce.qodeCompareForWooCommerceWaitForImages = qodeCompareForWooCommerceWaitForImages;

	var qodeCompareForWooCommerceScroll = {
		disable: function () {
			if ( window.addEventListener ) {
				window.addEventListener(
					'wheel',
					qodeCompareForWooCommerceScroll.preventDefaultValue,
					{ passive: false }
				);
			}

			// window.onmousewheel = document.onmousewheel = qodeCompareForWooCommerceScroll.preventDefaultValue;.
			document.onkeydown = qodeCompareForWooCommerceScroll.keyDown;
		},
		enable: function () {
			if ( window.removeEventListener ) {
				window.removeEventListener(
					'wheel',
					qodeCompareForWooCommerceScroll.preventDefaultValue,
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
					qodeCompareForWooCommerceScroll.preventDefaultValue( e );
					return;
				}
			}
		}
	};

	qodeCompareForWooCommerce.qodeCompareForWooCommerceScroll = qodeCompareForWooCommerceScroll;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodeCompareForWooCommerceCompareTable.init();
		}
	);

	/**
	 * Function object that represents comparison table
	 *
	 * @returns {{init: Function}}
	 */
	var qodeCompareForWooCommerceCompareTable = {
		_instance: null,
		init: function () {
			if ( ! this._instance) {
				this._instance = this;
				// Use the current object as the instance.
				this._init();
				// Call the actual initialization logic.
			}
			return this._instance;
		},
		_init: function () {
			var $holder = this.getHolder();

			if ( $holder.length ) {
				$( document.body ).on(
					'qode_compare_for_woocommerce_trigger_show_comparison_modal',
					function (e, withUpdate) {
						withUpdate = typeof withUpdate !== 'undefined' ? withUpdate : false;

						if ( withUpdate ) {
							$( document.body ).trigger(
								'qode_compare_for_woocommerce_trigger_table_update',
								[ $holder, '', ['all'], '', 'update_fields', true ]
							);
						} else {
							qodeCompareForWooCommerceCompareTable.showElement( $holder );
						}
					}
				);
				$( document ).on(
					'qode_compare_for_woocommerce_trigger_comparison_table_updated',
					function ( e, $holder, responseData, itemIDs, action ) {

						// Reinitialize table actions.
						qodeCompareForWooCommerceCompareTable.reInit( $holder, itemIDs, action );
					}
				);
				$( document.body ).on(
					'qode_compare_for_woocommerce_trigger_table_update',
					function ( e, $holder, $button, itemIDs, tableName, action, showDialog ) {
						qodeCompareForWooCommerceCompareTable.updateTable( $holder, $button, itemIDs, tableName, action, showDialog );
					}
				);
				this.setEventsActions( $holder );
			}
		},
		reInit: function ( $holder, itemIDs, action ) {
			qodeCompareForWooCommerceCompareTable.setEventsActions( $holder, action );
			qodeCompareForWooCommerceCompareTable.setRemoveItemAction( $holder, itemIDs, action );
		},
		getHolder: function () {
			return $( '#qcfw-modal' );
		},
		setEventsActions: function ( $modal, action ) {
			qodeCompareForWooCommerceCompareTable.setCloseModalActions( $modal );
			qodeCompareForWooCommerceCompareTable.setRemoveEventActions( $modal );
			qodeCompareForWooCommerceCompareTable.setDragEventActions( $modal.find( '.qcfw-m-items' ) );
		},
		setCloseModalActions: function ( $modal ) {
			$modal.find( '.qcfw-m-close' ).on(
				'click',
				function ( e ) {
					e.preventDefault();

					qodeCompareForWooCommerceCompareTable.hideElement( $modal );
				}
			);

			$modal.children( '.qcfw-m-overlay' ).on(
				'click',
				function () {
					qodeCompareForWooCommerceCompareTable.hideElement( $modal );
				}
			);
		},
		setRemoveEventActions: function ( $modal ) {
			var $removeButton = $modal.find( '.qcfw-m-remove-button' );

			if ( $removeButton.length ) {
				$removeButton.off().on(
					'click',
					function ( e ) {
						e.preventDefault();
						qodeCompareForWooCommerceCompareTable.removeItem( $modal, $( this ) );
					}
				);
			}
		},
		setDragEventActions: function ( $scrollArea ) {
			if ( ( 'ontouchstart' in window ) || ( navigator.maxTouchPoints > 0 ) || ( navigator.msMaxTouchPoints > 0 ) ) {
				return;
			}
			var isDown = false;
			var startX;
			var scrollLeft;

			if ( $scrollArea.width() < $scrollArea.prop( 'scrollWidth' ) ) {
				$scrollArea.css( 'cursor', 'grab' );

				$scrollArea.on(
					'mousedown',
					function (e) {
						var targetElement = $( e.target );

						if ( targetElement.closest( 'a' ).length || targetElement.closest( 'a' ).length ) {
							return;
						}
						isDown     = true;
						startX     = e.pageX - $scrollArea.offset().left;
						scrollLeft = $scrollArea.scrollLeft();
						$scrollArea.css( 'cursor', 'grabbing' );
						return false;
						// prevent text selection.
					}
				);

				$( document ).on(
					'mouseup',
					function () {
						isDown = false;
						$scrollArea.css( 'cursor', 'grab' );
					}
				);

				$scrollArea.on(
					'mouseleave',
					function () {
						isDown = false;
						$scrollArea.css( 'cursor', 'grab' );
					}
				);

				$scrollArea.on(
					'mousemove',
					function (e) {
						if ( ! isDown) {
							return;
						}
						e.preventDefault();
						var x    = e.pageX - $scrollArea.offset().left;
						var walk = (x - startX);
						// adjust scroll speed.
						$scrollArea.scrollLeft( scrollLeft - walk );
					}
				);
			}
		},
		removeItem: function ( $holder, $button ) {
			var itemID = qodeCompareForWooCommerceCompareTable.getRemovedItemID( $button );

			if ( qodeCompareForWooCommerceGlobal.removalConfirmation ) {
				var confirmText = $button.attr( 'data-confirm-text' );

				qodeCompareForWooCommerceCompareTable.handleConfirmBox( confirmText, qodeCompareForWooCommerceGlobal.modalHTML ).then(
					function ( response ) {
						if ( response ) {
							$( document.body ).trigger(
								'qode_compare_for_woocommerce_trigger_table_update',
								[ $holder, $button, [itemID], '', 'remove' ]
							);
						}
					}
				);
			} else {
				$( document.body ).trigger(
					'qode_compare_for_woocommerce_trigger_table_update',
					[ $holder, $button, [itemID], '', 'remove' ]
				);
			}
		},
		getRemovedItemID: function ( $button ) {
			return 'all' !== $button.attr( 'data-item-id' ) ? parseInt( $button.attr( 'data-item-id' ), 10 ) : $button.attr( 'data-item-id' );
		},
		handleConfirmBox: function ( message, modalHTML ) {
			var confirmModal = document.createElement( 'div' );
			confirmModal.classList.add( 'qcfw-confirm-modal' );
			confirmModal.classList.add( 'qcfw-m' );
			confirmModal.classList.add( 'qcfw--opened' );
			confirmModal.innerHTML = modalHTML;
			document.body.appendChild( confirmModal );
			$( '.qcfw-m-dialog-content .qcfw-m-form-title' ).html( message );

			return new Promise(
				function ( resolve, reject ) {
					var $trueButton = document.getElementById( 'qcfw-confirm-button-true' );
					if ( $trueButton ) {
						$trueButton.addEventListener(
							'click',
							function () {
								resolve( true );
								document.body.removeChild( confirmModal );
							}
						);
					}

					var closeSelectors = [ 'qcfw-confirm-modal-overlay', 'qcfw-confirm-button-false' ];

					closeSelectors.forEach(
						function ( closeSelector ) {
							document.getElementById( closeSelector ).addEventListener(
								'click',
								function () {
									resolve( false );
									document.body.removeChild( confirmModal );
								}
							);
						}
					);
				}
			);
		},
		updateTable: function ( $holder, $button, itemIDs, tableName, action, showDialog ) {
			var table   = typeof tableName !== 'undefined' && tableName.length ? tableName : $holder.attr( 'data-table' );
			var options = qodeCompareForWooCommerceCompareTable.getRequestOptions( $holder, $button );
			showDialog  = typeof showDialog !== 'undefined' ? showDialog : false;

			$.ajax(
				{
					type: 'POST',
					url: qodeCompareForWooCommerceGlobal.restUrl + qodeCompareForWooCommerceGlobal.compareTableRestRoute,
					data: {
						item_ids: itemIDs,
						action: action,
						table: table,
						token: $holder.attr( 'data-token' ),
						options: JSON.stringify( options ),
						security_token: qodeCompareForWooCommerceGlobal.restNonce,
					},
					beforeSend: function ( request ) {
						qodeCompareForWooCommerceCompareTable.beforeTableUpdate( request, $holder, $button, showDialog );
					},
					complete: function () {
						qodeCompareForWooCommerceCompareTable.afterTableUpdate( $holder, $button, showDialog );
					},
					success: function ( response ) {
						if ( response.status === 'success' ) {
							qodeCompareForWooCommerceCompareTable.updateTableContent( $holder, response.data, itemIDs, action );
								$( document.body ).trigger( 'qode_compare_for_woocommerce_trigger_comparison_table_updated', [ $holder, response.data, itemIDs, action ] );
							$holder.addClass( 'qcfw--initialized' );
						} else if ( response.status === 'error' ) {
							console.log( response.message );
						}
					}
				}
			);
		},
		getRequestOptions: function ( $holder, $button ) {
			var $widget = $( '.qcfw-compare.qcfw-widget' );
			if ( $widget.length ) {
				return $widget.attr( 'data-widget-atts' ) ? JSON.parse( $widget.attr( 'data-widget-atts' ) ) : {};

			}
			return {};
		},
		beforeTableUpdate: function ( request, $holder, $button, showDialog ) {
			request.setRequestHeader( 'X-WP-Nonce', qodeCompareForWooCommerceGlobal.restNonce );
			$holder.addClass( 'qcfw--table-updating' );

			if ( $button.length ) {
				$button.addClass( 'qcfw--loading' );
			}
			if ( showDialog ) {
				qodeCompareForWooCommerceCompareTable.showElement( $holder );
			}
		},
		afterTableUpdate: function ( $holder, $button ) {
			$holder.removeClass( 'qcfw--table-updating' );

			if ( $button.length ) {
				$button.removeClass( 'qcfw--loading' );
			}
		},
		updateTableContent: function ( $holder, responseData, itemIDs, action ) {
			var html = responseData.not_found_content ? responseData.not_found_content : responseData.new_content;
			$holder.find( '.qcfw-m-table-wrapper' ).empty().html( html );
			if ( responseData.not_found_content ) {
				$holder.addClass( 'qcfw--empty' );
			} else {
				$holder.removeClass( 'qcfw--empty' );
			}
			var columns = responseData.items_count && responseData.items_count < 7 ? responseData.items_count + 1 : 7;

			if ( $holder.is( "#qcfw-modal" ) ) {
				$holder.find( '.qcfw-compare-table' ).css( '--qcfw-columns', columns );
			}
		},
		showElement: function ( $modal ) {
			if ( ! $modal.hasClass( 'qcfw--opened' ) ) {
				$modal.addClass( 'qcfw--opened' );
			}
		},
		hideElement: function ( $element ) {
			if ( $element.hasClass( 'qcfw--opened' ) ) {
				$element.removeClass( 'qcfw--opened' );
			}
			if ( $element.hasClass( 'qcfw--empty' ) ) {
				$element.removeClass( 'qcfw--initialized' );
			}
		},
		setRemoveItemAction: function ( $holder, itemIDs, action ) {
			if ( 'remove' === action ) {
				itemIDs.forEach(
					function ( itemID ) {
						var $button;
						if ( 'all' === itemID ) {
							$button = $( '.qcfw--added' );
						} else {
							$button = $( '.qcfw--added' ).filter(
								function () {
									return $( this ).data( 'item-id' ) === itemID;
								}
							);
						}
						qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareButton.modifyButton( $button, action );
					}
				);
			}
		},
	};

	qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareTable = qodeCompareForWooCommerceCompareTable;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodeCompareForWooCommerceCompareButton.init();
		}
	);

	/**
	 * Function object that represents button behavior.
	 *
	 * @returns {{init: Function}}
	 */
	var qodeCompareForWooCommerceCompareButton = {
		// singleton pattern because custom events would be bound multiple times whenever this object is called in other modules.
		_instance: null,
		init: function () {
			if ( ! this._instance) {
				this._instance = this;
				// Use the current object as the instance.
				this._init();
				// Call the actual initialization logic.
			}
			return this._instance;
		},
		_init: function () {
			var $compareButtons = $( '.qcfw-button' );
			if ($compareButtons.length) {
				this.setEvents( $compareButtons );
				this.setCompareEvents();
			}
		},
		setEvents: function ( $buttons ) {
			qodeCompareForWooCommerceCompareButton.setButtonsClickEvent( $buttons );
		},
		setButtonsClickEvent: function ( $buttons ) {
			$buttons.off().on(
				'click',
				function ( e ) {
					e.preventDefault();

					var $button      = $( this ),
						buttonAction = $button.hasClass( 'qcfw--added' ) ? 'view' : 'add';

					qodeCompareForWooCommerceCompareButton.compareItem(
						$button,
						buttonAction
					);
				}
			);
		},
		setCompareEvents: function () {
			$( document.body ).on(
				'qode_compare_for_woocommerce_trigger_compare',
				function (e, $button, buttonAction, responseData ) {
					qodeCompareForWooCommerceCompareButton.handleButtonAction( $button, buttonAction, responseData );
				}
			);
		},
		compareItem: function ( $button, action ) {
			var $holder      = qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareTable.getHolder(),
				buttonAction = typeof action !== 'undefined' ? action : $button.hasClass( 'qcfw--added' ) ? 'view' : 'add';

			if ( 'view' === buttonAction && $holder.hasClass( 'qcfw--initialized' ) ) {
				$( document.body ).trigger( 'qode_compare_for_woocommerce_trigger_show_comparison_modal' );
			} else {
				var itemID  = parseInt( $button.attr( 'data-item-id' ), 10 ),
					options = qodeCompareForWooCommerceCompareButton.getRequestOptions( $button );

				qodeCompareForWooCommerceCompareButton.handleAjax( $button, itemID, buttonAction, options );
			}
		},
		getRequestOptions: function ( $button ) {
			return $button.attr( 'data-shortcode-atts' ) ? JSON.parse( $button.attr( 'data-shortcode-atts' ) ) : {};
		},
		handleAjax: function ( $button, itemID, buttonAction, options ) {
			$.ajax(
				{
					type: 'POST',
					url: qodeCompareForWooCommerceGlobal.restUrl + qodeCompareForWooCommerceGlobal.compareRestRoute,
					data: {
						item_id: itemID,
						action: buttonAction,
						options: JSON.stringify( options ),
						security_token: qodeCompareForWooCommerceGlobal.restNonce,
					},
					beforeSend: function ( request ) {
						qodeCompareForWooCommerceCompareButton.beforeCompare( request, $button );
					},
					complete: function () {
						qodeCompareForWooCommerceCompareButton.afterCompare( $button );
					},
					success: function ( response ) {
						if ( response.status === 'success' ) {
							$( document.body ).trigger(
								'qode_compare_for_woocommerce_trigger_compare',
								[ $button, buttonAction, response.data ]
							);
						} else if ( response.status === 'error' ) {
							console.log( response.message );
						}
					},
				}
			);
		},
		beforeCompare: function ( request, $button ) {
			request.setRequestHeader( 'X-WP-Nonce', qodeCompareForWooCommerceGlobal.restNonce );
			$button.addClass( 'qcfw--loading' );
		},
		afterCompare: function ( $button ) {
			$button.removeClass( 'qcfw--loading' );
		},
		handleButtonAction: function ( $button, buttonAction, responseData ) {
			var $holder = qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareTable.getHolder();
			$( document.body ).trigger(
				'qode_compare_for_woocommerce_trigger_table_update',
				[ $holder, '', ['all'], '', 'update_fields' ]
			);
			if ( 'add' === buttonAction ) {
				qodeCompareForWooCommerceCompareButton.modifyButton( $button );
			}
			if ( 'view' === buttonAction || qodeCompareForWooCommerceGlobal.autoPopup ) {
				// open popup.
				$( document.body ).trigger( 'qode_compare_for_woocommerce_trigger_show_comparison_modal' );
			}
		},
		modifyButton: function ( $button, action ) {
			qodeCompareForWooCommerceCompareButton.modifyButtonText( $button );
		},
		modifyButtonText: function ( $button ) {
			var $buttonText = $button.find( '.qcfw-button-text' );
			if ( $button.hasClass( 'qcfw--added' ) ) {
				$button.removeClass( 'qcfw--added' );
				if ( $buttonText.length ) {
					$buttonText.html( qodeCompareForWooCommerceGlobal.buttonText );
				}
			} else {
				$button.addClass( 'qcfw--added' );
				if ( $buttonText.length ) {
					$buttonText.html( qodeCompareForWooCommerceGlobal.addedText );
				}
			}
		},
	};

	qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareButton = qodeCompareForWooCommerceCompareButton;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodeCompareForWooCommerceCompareWidget.init();
		}
	);

	var qodeCompareForWooCommerceCompareWidget = {
		init: function () {
			// Add to comparison buttons present on the page.
			var $holder = $( '.widget_qode_compare_for_woocommerce_compare' );

			this.setEvents( $holder );
			$( document.body ).on(
				'qode_compare_for_woocommerce_trigger_comparison_table_updated',
				function ( e, $table_holder, responseData ) {
					qodeCompareForWooCommerceCompareWidget.updateTableContent( $holder, responseData );
				}
			);
		},
		setEvents: function ( $holder ) {
			if ( $holder.length ) {
				var $remove        = $holder.find( '.qcfw-m-remove-button' ),
					$compareOpener = $holder.find( '.qcfw-m-open-compare' );

				if ( $remove.length ) {

					$remove.off().on(
						'click',
						function ( e ) {
							e.preventDefault();
							var itemID = qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareTable.getRemovedItemID( $( this ) );

							$( document.body ).trigger(
								'qode_compare_for_woocommerce_trigger_table_update',
								[ qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareTable.getHolder(), $( this ), [itemID], '', 'remove' ]
							);
						}
					);
				}

				if ( $compareOpener.length ) {
					qodeCompareForWooCommerceCompareWidget.handleOpenerClick( $compareOpener );
				}
			}
		},
		updateTableContent: function ( $holder, responseData ) {
			if ( responseData.widget_new_content ) {
				$holder.find( '.qcfw-m-inner' ).empty().html( responseData.widget_new_content );
			}
			qodeCompareForWooCommerceCompareWidget.setEvents( $holder );
		},
		handleOpenerClick: function ( $button ) {

			$button.off().on(
				'click',
				function ( e ) {
					e.preventDefault();
					$( document.body ).trigger( 'qode_compare_for_woocommerce_trigger_show_comparison_modal', [ true ] );
				}
			);
		}
	};

	qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareWidget = qodeCompareForWooCommerceCompareWidget;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodeCompareForWooCommerceCompareCounterWidget.init();
		}
	);

	var qodeCompareForWooCommerceCompareCounterWidget = {
		init: function () {
			// Add to comparison buttons present on the page.
			var $holder = $( '.widget .qcfw-compare-counter' );

			this.setEvents( $holder );
			$( document.body ).on(
				'qode_compare_for_woocommerce_trigger_comparison_table_updated',
				function ( e, $table_holder, responseData ) {
					qodeCompareForWooCommerceCompareCounterWidget.updateContent( $holder, responseData );
				}
			);
		},
		setEvents: function ( $holder ) {
			if ( $holder.length ) {
				var $compareOpener = $holder.find( '.qcfw-m-open-compare' );

				if ( $compareOpener.length ) {
					qodeCompareForWooCommerceCompareCounterWidget.handleOpenerClick( $compareOpener );
				}
			}
		},
		updateContent: function ( $holder, responseData ) {
			if ( $holder.length ) {
				$holder.each(
					function () {
						var $widgetHolder    = $( this );
						var newWidgetContent = false;

						if ( $widgetHolder.hasClass( 'qcfw-layout--top-right-count' ) && responseData.widget_counter_trc_new_content ) {
							newWidgetContent = responseData.widget_counter_trc_new_content;
						} else if ( $widgetHolder.hasClass( 'qcfw-layout--with-icon' ) && responseData.widget_counter_wi_new_content ) {
							newWidgetContent = responseData.widget_counter_wi_new_content;
						} else if ( $widgetHolder.hasClass( 'qcfw-layout--icon' ) && responseData.widget_counter_icon_new_content ) {
							newWidgetContent = responseData.widget_counter_icon_new_content;
						}

						if ( newWidgetContent ) {
							$widgetHolder.find( '.qcfw-m-inner' ).empty().html( newWidgetContent );
							qodeCompareForWooCommerceCompareCounterWidget.setEvents( $widgetHolder );
						}
					}
				);
			}
		},
		handleOpenerClick: function ( $button ) {
			$button.off().on(
				'click',
				function ( e ) {
					e.preventDefault();
					$( document.body ).trigger( 'qode_compare_for_woocommerce_trigger_show_comparison_modal', [ true ] );
				}
			);
		}
	};

	qodeCompareForWooCommerce.qodeCompareForWooCommerceCompareCounterWidget = qodeCompareForWooCommerceCompareCounterWidget;

})( jQuery );
