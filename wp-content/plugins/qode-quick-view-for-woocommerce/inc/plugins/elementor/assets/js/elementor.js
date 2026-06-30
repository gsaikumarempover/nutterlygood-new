(function ( $ ) {
	'use strict';

	$( window ).on(
		'elementor/frontend/init',
		function () {
			qodeQuickViewForWooCommerceElementor.init();
		}
	);

	var qodeQuickViewForWooCommerceElementor = {
		init: function () {
			var isEditMode = Boolean( elementorFrontend.isEditMode() );

			if ( isEditMode ) {
				for ( var key in qodeQuickViewForWooCommerce.shortcodes ) {
					for ( var keyChild in qodeQuickViewForWooCommerce.shortcodes[key] ) {
						qodeQuickViewForWooCommerceElementor.reInitShortcode( key, keyChild );
					}
				}
			}
		},
		reInitShortcode: function ( key, keyChild ) {
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/' + key + '.default',
				function ( e ) {
					// Check if object doesn't exist and print the module where is the error.
					if ( typeof qodeQuickViewForWooCommerce.shortcodes[key][keyChild] === 'undefined' ) {
						console.log( keyChild );
					} else if ( typeof qodeQuickViewForWooCommerce.shortcodes[key][keyChild].initItem === 'function' && e.find( '.qqvfw-shortcode' ).length ) {
						qodeQuickViewForWooCommerce.shortcodes[key][keyChild].initItem( e.find( '.qqvfw-shortcode' ) );
					} else if ( typeof qodeQuickViewForWooCommerce.shortcodes[key][keyChild].init === 'function' ) {
						qodeQuickViewForWooCommerce.shortcodes[key][keyChild].init();
					}
				}
			);
		},
	};

})( jQuery );
