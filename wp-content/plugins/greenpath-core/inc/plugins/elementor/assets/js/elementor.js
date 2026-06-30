(function ( $ ) {
	'use strict';

	$( window ).on(
		'elementor/frontend/init',
		function () {
			// shortcodes
			qodefElementorShortcodes.init();

			// section extension
			qodefElementorSection.init();
			qodefElementorSectionCallback.init();

			// column extension
			qodefElementorColumn.init();
			qodefElementorColumnCallback.init();

			// container extension
			qodefElementorContainer.init();
			qodefElementorContainerCallback.init();
		}
	);

	// shortcodes
	var qodefElementorShortcodes = {
		init: function () {
			var isEditMode = Boolean( elementorFrontend.isEditMode() );

			if ( isEditMode ) {
				for ( var key in qodefCore.shortcodes ) {
					for ( var keyChild in qodefCore.shortcodes[key] ) {
						qodefElementorShortcodes.reInitShortcode(
							key,
							keyChild
						);
					}
				}
			}
		},
		reInitShortcode: function ( key, keyChild ) {
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/' + key + '.default',
				function ( e ) {

					// check if object doesn't exist and print the module where is the error
					if ( typeof qodefCore.shortcodes[key][keyChild] === 'undefined' ) {
						console.log( keyChild );
					} else if ( typeof qodefCore.shortcodes[key][keyChild].initSlider === 'function' && e.find( '.qodef-instagram-swiper-container' ).length ) {
						qodefCore.shortcodes[key][keyChild].initSlider(
							e.find( '.qodef-instagram-swiper-container' ),
							false
						);
					} else if ( typeof qodefCore.shortcodes[key][keyChild].initSlider === 'function' && e.find( '.qodef-swiper-container' ).length ) {
						qodefCore.shortcodes[key][keyChild].initSlider( e.find( '.qodef-swiper-container' ) );
					} else if ( typeof qodefCore.shortcodes[key][keyChild].initPopup === 'function' && e.find( '.qodef-magnific-popup' ).length ) {
						qodefCore.shortcodes[key][keyChild].initPopup( e.find( '.qodef-magnific-popup' ) );
					} else if ( typeof qodefCore.shortcodes[key][keyChild].initItem === 'function' && e.find( '.qodef-shortcode' ).length ) {
						qodefCore.shortcodes[key][keyChild].initItem( e.find( '.qodef-shortcode' ) );
					} else {
						qodefCore.shortcodes[key][keyChild].init();
					}
				}
			);
		}
	};

	// section extension
	var qodefElementorSection = {
		init: function () {
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/section',
				qodefElementorSectionCallback.init
			);
		}
	};

	var qodefElementorSectionCallback = {
		init: function ( $scope ) {
			var $target     = $scope,
				isEditMode  = Boolean( elementorFrontend.isEditMode() ),
				settings    = [],
				sectionData = {};

			if ( isEditMode && typeof $scope !== 'undefined' ) {

				// generate options when in admin
				var editorElements = window.elementor.elements,
					sectionId      = $target.data( 'id' ),
					isInnerSection = $target.hasClass( 'elementor-inner-section' );

				$.each(
					editorElements.models,
					function ( index, object ) {

						if ( sectionId == object.id ) {
							sectionData = object.attributes.settings.attributes;
						} else if ( isInnerSection && typeof object.attributes.elements.models !== 'undefined' && object.attributes.elements.models.length ) {

							$.each(
								object.attributes.elements.models,
								function ( index, innerObject ) {

									if ( typeof innerObject.attributes.elements.models[0] !== 'undefined' && typeof innerObject.attributes.elements.models[0].attributes.settings.attributes !== 'undefined' && sectionId == innerObject.attributes.elements.models[0].attributes.id ) {
										sectionData = innerObject.attributes.elements.models[0].attributes.settings.attributes;
									}
								}
							);
						}
					}
				);

				// parallax options
				if ( typeof sectionData.qodef_parallax_type !== 'undefined' ) {
					settings['enable_parallax'] = sectionData.qodef_parallax_type;
				}

				if ( typeof sectionData.qodef_parallax_image !== 'undefined' && sectionData.qodef_parallax_image['url'] ) {
					settings['parallax_image_url'] = sectionData.qodef_parallax_image['url'];
				}

				if ( typeof sectionData.qodef_parallax_animation !== 'undefined' ) {
					settings['parallax_animation'] = sectionData.qodef_parallax_animation;
				}

				// offset options
				if ( typeof sectionData.qodef_offset_type !== 'undefined' ) {
					settings['enable_offset'] = sectionData.qodef_offset_type;
				}

				if ( typeof sectionData.qodef_offset_image_items !== 'undefined' ) {
					settings['offset_image_items'] = sectionData.qodef_offset_image_items;
				}

				// generate output backend
				if ( typeof $target !== 'undefined' ) {
					qodefElementorSectionCallback.generateOutput(
						$target,
						settings
					);
				}
			} else {

				// generate options when in frontend using global js variable
				var sectionHandlerData = qodefElementorGlobal.vars.elementorSectionHandler;

				$.each(
					sectionHandlerData,
					function ( index, properties ) {

						properties.forEach(
							function ( property ) {

								if ( typeof property['parallax_type'] !== 'undefined' && property['parallax_type'] === 'parallax' ) {

									$target                        = $( '[data-id="' + index + '"]' );
									settings['parallax_type']      = property['parallax_type'];
									settings['parallax_image_url'] = property['parallax_image']['url'];
									settings['parallax_animation'] = property['parallax_animation'];

									if ( typeof settings['parallax_image_url'] !== 'undefined' ) {
										settings['enable_parallax'] = 'parallax';
									}
								}

								if ( typeof property['offset_type'] !== 'undefined' && property['offset_type'] === 'offset' ) {

									$target                        = $( '[data-id="' + index + '"]' );
									settings['offset_type']        = property['offset_type'];
									settings['offset_image_items'] = property['offset_image_items'];

									if ( typeof settings['offset_image_items'] !== 'undefined' ) {
										settings['enable_offset'] = 'offset';
									}
								}
							}
						);

						// generate output frontend
						if ( typeof $target !== 'undefined' && ! $target.hasClass( 'qodef-parallax--init' ) ) {
							qodefElementorSectionCallback.generateOutput(
								$target,
								settings
							);

							settings = [];
						}
					}
				);
			}
		},
		generateOutput: function ( $target, settings ) {

			if ( typeof settings['enable_parallax'] !== 'undefined' && settings['enable_parallax'] === 'parallax' && typeof settings['parallax_image_url'] !== 'undefined' ) {

				$(
					'.qodef-parallax-row-holder',
					$target
				).remove();
				$target.removeClass( 'qodef-parallax qodef--parallax-row' );
				$target.addClass( 'qodef-parallax qodef--parallax-row' );

				$( '<div class="qodef-parallax-row-holder"><div class="qodef-parallax-img-holder"><div class="qodef-parallax-img-wrapper"><img class="qodef-parallax-img" src="' + settings['parallax_image_url'] + '" alt="Parallax Image"></div></div></div>' ).prependTo( $target );

				// wait for image src to be loaded
				var newImg    = new Image;
				newImg.onload = function () {
					$target.find( 'img.qodef-parallax-img' ).attr(
						'src',
						this.src
					);
					qodefCore.qodefParallaxBackground.ready( $target );
				};

				newImg.src = settings['parallax_image_url'];
			}

			if ( typeof settings['enable_offset'] !== 'undefined' && settings['enable_offset'] === 'offset' && typeof settings['offset_image_items'] !== 'undefined' ) {

				$(
					'.qodef-offset-image-holder',
					$target
				).remove();
				$target.removeClass( 'qodef-offset-image' );

				var $layout = null;

				$target.addClass( 'qodef-offset-image' );

				settings.offset_image_items.forEach(
					function ( repeaterProperty ) {

						// difference between frontend and elementor builder page
						if ( repeaterProperty.attributes ) {
							repeaterProperty = repeaterProperty.attributes;
						}

						settings['offset_image_url']           = repeaterProperty.qodef_offset_image.url;
						settings['offset_movement']            = repeaterProperty.qodef_offset_movement;
						settings['offset_vertical_anchor']     = repeaterProperty.qodef_offset_vertical_anchor;
						settings['offset_vertical_position']   = repeaterProperty.qodef_offset_vertical_position;
						settings['offset_horizontal_anchor']   = repeaterProperty.qodef_offset_horizontal_anchor;
						settings['offset_horizontal_position'] = repeaterProperty.qodef_offset_horizontal_position;

						var $movementClass = '';
						if ( typeof settings['offset_movement'] !== 'undefined' && settings['offset_movement'] !== '' ) {
							$movementClass = ' qodef-' + settings['offset_movement'] + '-item';
						}

						$layout = $( '<div class="qodef-offset-image-holder" style="position: absolute; ' + settings['offset_vertical_anchor'] + ':' + settings['offset_vertical_position'] + ';' + settings['offset_horizontal_anchor'] + ':' + settings['offset_horizontal_position'] + '"><div class="qodef-offset-image-wrapper' + $movementClass + '"><img src="' + settings['offset_image_url'] + '" alt="Offset Image"></div></div>' ).prependTo( $target );
					}
				);
			}
		}
	};

	// column extension
	var qodefElementorColumn = {
		init: function () {
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/column',
				qodefElementorColumnCallback.init
			);
		}
	};

	var qodefElementorColumnCallback = {
		init: function ( $scope ) {
			var $target    = $scope,
				isEditMode = Boolean( elementorFrontend.isEditMode() ),
				settings   = [],
				columnData = {};

			if ( isEditMode && typeof $scope !== 'undefined' ) {
				// generate options when in admin
				var editorElements = window.elementor.elements,
					columnId       = $target.data( 'id' );

				if ( ! editorElements.models ) {
					return false;
				}

				$.each(
					editorElements.models,
					function ( index, object ) {
						$.each(
							object.attributes.elements.models,
							function ( index, object ) {
								if ( columnId == object.id ) {
									columnData = object.attributes.settings.attributes;
								}
							}
						);
					}
				);

				// background text options
				if ( typeof columnData.qodef_background_text_enable !== 'undefined' && columnData.qodef_background_text_enable.length ) {
					settings['background_text_enable'] = columnData.qodef_background_text_enable;
				}

				if ( typeof columnData.qodef_background_text !== 'undefined' && columnData.qodef_background_text.length ) {
					settings['background_text'] = columnData.qodef_background_text;
				}

				if ( typeof columnData.qodef_background_text_color !== 'undefined' && columnData.qodef_background_text_color.length ) {
					settings['background_text_color'] = columnData.qodef_background_text_color;
				}

				if ( typeof columnData.qodef_background_text_size !== 'undefined' && columnData.qodef_background_text_size.length ) {
					settings['background_text_size'] = columnData.qodef_background_text_size;
				}

				if ( typeof columnData.qodef_background_text_size_1512 !== 'undefined' && columnData.qodef_background_text_size_1512.length ) {
					settings['background_text_size_1512'] = columnData.qodef_background_text_size_1512;
				}

				if ( typeof columnData.qodef_background_text_size_1368 !== 'undefined' && columnData.qodef_background_text_size_1368.length ) {
					settings['background_text_size_1368'] = columnData.qodef_background_text_size_1368;
				}

				if ( typeof columnData.qodef_background_text_size_1200 !== 'undefined' && columnData.qodef_background_text_size_1200.length ) {
					settings['background_text_size_1200'] = columnData.qodef_background_text_size_1200;
				}

				if ( typeof columnData.qodef_background_text_vertical_offset !== 'undefined' && columnData.qodef_background_text_vertical_offset.length ) {
					settings['background_text_vertical_offset'] = columnData.qodef_background_text_vertical_offset;
				}

				if ( typeof columnData.qodef_background_text_vertical_offset_1512 !== 'undefined' && columnData.qodef_background_text_vertical_offset_1512.length ) {
					settings['background_text_vertical_offset_1512'] = columnData.qodef_background_text_vertical_offset_1512;
				}

				if ( typeof columnData.qodef_background_text_vertical_offset_1368 !== 'undefined' && columnData.qodef_background_text_vertical_offset_1368.length ) {
					settings['background_text_vertical_offset_1368'] = columnData.qodef_background_text_vertical_offset_1368;
				}

				if ( typeof columnData.qodef_background_text_vertical_offset_1200 !== 'undefined' && columnData.qodef_background_text_vertical_offset_1200.length ) {
					settings['background_text_vertical_offset_1200'] = columnData.qodef_background_text_vertical_offset_1200;
				}

				if ( typeof columnData.qodef_background_text_horizontal_align !== 'undefined' && columnData.qodef_background_text_horizontal_align.length ) {
					settings['background_text_horizontal_align'] = columnData.qodef_background_text_horizontal_align;
				}

				if ( typeof columnData.qodef_background_text_vertical_align !== 'undefined' && columnData.qodef_background_text_vertical_align.length ) {
					settings['background_text_vertical_align'] = columnData.qodef_background_text_vertical_align;
				}
			} else {

				// generate options when in frontend using global js variable
				var columnHandlerData = qodefElementorGlobal.vars.elementorColumnHandler;

				$.each(
					columnHandlerData,
					function ( index, property ) {
						$target = $( '[data-id="' + index + '"]' );

						settings['background_text']                      = property[0];
						settings['background_text_color']                = property[1];
						settings['background_text_size']                 = property[2];
						settings['background_text_size_1512']            = property[3];
						settings['background_text_size_1368']            = property[4];
						settings['background_text_size_1200']            = property[5];
						settings['background_text_vertical_offset']      = property[6];
						settings['background_text_vertical_offset_1512'] = property[7];
						settings['background_text_vertical_offset_1368'] = property[8];
						settings['background_text_vertical_offset_1200'] = property[9];
						settings['background_text_horizontal_align']     = property[10];
						settings['background_text_vertical_align']       = property[11];

						if ( typeof settings['background_text'] !== 'undefined' ) {
							settings['background_text_enable'] = 'yes';
						}

						// generate output frontend
						if ( typeof $target !== 'undefined' && $target.length ) {
							qodefElementorColumnCallback.generateOutput(
								$target,
								settings
							);
						}
					}
				);
			}

			// generate output
			if ( typeof $target !== 'undefined' ) {
				qodefElementorColumnCallback.generateOutput(
					$target,
					settings
				);
			}
		},
		generateOutput: function ( $target, settings ) {

			if ( typeof settings['background_text_enable'] !== 'undefined' && 'yes' === settings['background_text_enable'] && typeof settings['background_text'] !== 'undefined' ) {
				$(
					'.qodef-m-background-text-holder',
					$target
				).remove();
				$target.removeClass( 'qodef-background-text' );
				$target.addClass( 'qodef-background-text' );

				$( '<div class="qodef-m-background-text-holder"><span class="qodef-m-background-text">' + settings['background_text'] + '</span></div>' ).prependTo( $target );

				$target.find( '.qodef-m-background-text' ).css(
					{
						'color': settings['background_text_color'],
					}
				);

				$target.find( '.qodef-m-background-text-holder' ).css(
					{
						'justify-content': settings['background_text_horizontal_align'],
						'align-items': settings['background_text_vertical_align'],
					}
				);

				$target.find( '.qodef-m-background-text' ).attr(
					'data-size-3840',
					settings['background_text_size']
				);
				$target.find( '.qodef-m-background-text' ).attr(
					'data-size-1512',
					settings['background_text_size_1512']
				);
				$target.find( '.qodef-m-background-text' ).attr(
					'data-size-1368',
					settings['background_text_size_1368']
				);
				$target.find( '.qodef-m-background-text' ).attr(
					'data-size-1200',
					settings['background_text_size_1200']
				);
				$target.find( '.qodef-m-background-text' ).attr(
					'data-vertical-offset-3840',
					settings['background_text_vertical_offset']
				);
				$target.find( '.qodef-m-background-text' ).attr(
					'data-vertical-offset-1512',
					settings['background_text_vertical_offset_1512']
				);
				$target.find( '.qodef-m-background-text' ).attr(
					'data-vertical-offset-1368',
					settings['background_text_vertical_offset_1368']
				);
				$target.find( '.qodef-m-background-text' ).attr(
					'data-vertical-offset-1200',
					settings['background_text_vertical_offset_1200']
				);

				// call function to handle font size and top offset
				qodefBackgroundText.init();
			}
		},
	};

	// container extension
	var qodefElementorContainer = {
		init: function () {
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/container',
				qodefElementorContainerCallback.init
			);
		}
	};

	var qodefElementorContainerCallback = {
		init: function ( $scope ) {
			var $target       = $scope,
				isEditMode    = Boolean( elementorFrontend.isEditMode() ),
				settings      = [],
				containerData = {};

			if ( isEditMode && typeof $scope !== 'undefined' ) {
				// generate options when in admin
				var editorElements = window.elementor.elements,
					containerId    = $target.data( 'id' ),
					isInnerSection = $target.hasClass( 'e-con--column' );

				$.each(
					editorElements.models,
					function ( index, object ) {

						if ( containerId == object.id ) {
							containerData = object.attributes.settings.attributes;
						} else if ( isInnerSection && typeof object.attributes.elements.models !== 'undefined' && object.attributes.elements.models.length ) {

							$.each(
								object.attributes.elements.models,
								function ( index, innerObject ) {

									if ( typeof innerObject.attributes.settings.attributes !== 'undefined' && containerId == innerObject.attributes.id ) {
										containerData = innerObject.attributes.settings.attributes;
									}
								}
							);
						}
					}
				);

				// parallax options
				if ( typeof containerData.qodef_parallax_type !== 'undefined' ) {
					settings['enable_parallax'] = containerData.qodef_parallax_type;
				}

				if ( typeof containerData.qodef_parallax_image !== 'undefined' && containerData.qodef_parallax_image['url'] ) {
					settings['parallax_image_url'] = containerData.qodef_parallax_image['url'];
				}

				if ( typeof containerData.qodef_parallax_animation !== 'undefined' ) {
					settings['parallax_animation'] = containerData.qodef_parallax_animation;
				}

				// offset options
				if ( typeof containerData.qodef_offset_type !== 'undefined' ) {
					settings['enable_offset'] = containerData.qodef_offset_type;
				}

				if ( typeof containerData.qodef_offset_image_items !== 'undefined' ) {
					settings['offset_image_items'] = containerData.qodef_offset_image_items;
				}

				// generate output backend
				if ( typeof $target !== 'undefined' ) {
					qodefElementorContainerCallback.generateOutput(
						$target,
						settings
					);
				}
			} else {

				// generate options when in frontend using global js variable
				var containerHandlerData = qodefElementorContainerGlobal.vars.elementorContainerHandler;

				$.each(
					containerHandlerData,
					function ( index, properties ) {

						properties.forEach(
							function ( property ) {

								if ( typeof property['parallax_type'] !== 'undefined' && property['parallax_type'] === 'parallax' ) {
									$target                        = $( '[data-id="' + index + '"]' );
									settings['parallax_type']      = property['parallax_type'];
									settings['parallax_image_url'] = property['parallax_image']['url'];
									settings['parallax_animation'] = property['parallax_animation'];

									if ( typeof settings['parallax_image_url'] !== 'undefined' ) {
										settings['enable_parallax'] = 'parallax';
									}
								}

								if ( typeof property['offset_type'] !== 'undefined' && property['offset_type'] === 'offset' ) {
									$target                        = $( '[data-id="' + index + '"]' );
									settings['offset_type']        = property['offset_type'];
									settings['offset_image_items'] = property['offset_image_items'];

									if ( typeof settings['offset_image_items'] !== 'undefined' ) {
										settings['enable_offset'] = 'offset';
									}
								}
							}
						);

						// generate output frontend
						if ( typeof $target !== 'undefined' && ! $target.hasClass( 'qodef-parallax--init' ) ) {
							qodefElementorContainerCallback.generateOutput(
								$target,
								settings
							);

							settings = [];
						}
					}
				);
			}
		},
		generateOutput: function ( $target, settings ) {

			if ( typeof settings['enable_parallax'] !== 'undefined' && settings['enable_parallax'] === 'parallax' && typeof settings['parallax_image_url'] !== 'undefined' ) {
				$(
					'.qodef-parallax-row-holder',
					$target
				).remove();
				$target.removeClass( 'qodef-parallax qodef--parallax-row' );
				$target.addClass( 'qodef-parallax qodef--parallax-row' );
				$target.addClass( `qodef-parallax-${settings['parallax_animation']}` );

				$( '<div class="qodef-parallax-row-holder"><div class="qodef-parallax-img-holder"><div class="qodef-parallax-img-wrapper"><img class="qodef-parallax-img" src="' + settings['parallax_image_url'] + '" alt="Parallax Image"></div></div></div>' ).prependTo( $target );

				// wait for image src to be loaded
				var newImg    = new Image;
				newImg.onload = function () {
					$target.find( 'img.qodef-parallax-img' ).attr(
						'src',
						this.src
					);

					qodefCore.qodefParallaxBackground.init();
				};

				newImg.src = settings['parallax_image_url'];
			}

			if ( typeof settings['enable_offset'] !== 'undefined' && settings['enable_offset'] === 'offset' && typeof settings['offset_image_items'] !== 'undefined' ) {
				$(
					'.qodef-offset-image-holder',
					$target
				).remove();
				$target.removeClass( 'qodef-offset-image' );

				var $layout = null;

				$target.addClass( 'qodef-offset-image' );

				settings.offset_image_items.forEach(
					function ( repeaterProperty ) {

						// difference between frontend and elementor builder page
						if ( repeaterProperty.attributes ) {
							repeaterProperty = repeaterProperty.attributes;
						}

						settings['offset_image_url']           = repeaterProperty.qodef_offset_image.url;
						settings['offset_movement']            = repeaterProperty.qodef_offset_movement;
						settings['offset_vertical_anchor']     = repeaterProperty.qodef_offset_vertical_anchor;
						settings['offset_vertical_position']   = repeaterProperty.qodef_offset_vertical_position;
						settings['offset_horizontal_anchor']   = repeaterProperty.qodef_offset_horizontal_anchor;
						settings['offset_horizontal_position'] = repeaterProperty.qodef_offset_horizontal_position;

						var $movementClass = '';
						if ( typeof settings['offset_movement'] !== 'undefined' && settings['offset_movement'] !== '' ) {
							$movementClass = ' qodef-' + settings['offset_movement'] + '-item';
						}

						$layout = $( '<div class="qodef-offset-image-holder" style="position: absolute; ' + settings['offset_vertical_anchor'] + ':' + settings['offset_vertical_position'] + ';' + settings['offset_horizontal_anchor'] + ':' + settings['offset_horizontal_position'] + '"><div class="qodef-offset-image-wrapper' + $movementClass + '"><img src="' + settings['offset_image_url'] + '" alt="Offset Image"></div></div>' ).prependTo( $target );
					}
				);
			}
		}
	};

})( jQuery );
