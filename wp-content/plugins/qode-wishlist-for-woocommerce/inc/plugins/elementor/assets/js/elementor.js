(function ( $ ) {
	'use strict';

	$( window ).on(
		'elementor/frontend/init',
		function () {
			qodeWishlistForWooCommerceElementor.init();
		}
	);

	var qodeWishlistForWooCommerceElementor = {
		init: function () {
			var isEditMode = Boolean( elementorFrontend.isEditMode() );

			if ( isEditMode ) {
				for ( var key in qodeWishlistForWooCommerce.shortcodes ) {
					for ( var keyChild in qodeWishlistForWooCommerce.shortcodes[key] ) {
						qodeWishlistForWooCommerceElementor.reInitShortcode( key, keyChild );
					}
				}
			}
		},
		reInitShortcode: function ( key, keyChild ) {
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/' + key + '.default',
				function ( e ) {
					// Check if object doesn't exist and print the module where is the error.
					if ( typeof qodeWishlistForWooCommerce.shortcodes[key][keyChild] === 'undefined' ) {
						console.log( keyChild );
					} else if ( typeof qodeWishlistForWooCommerce.shortcodes[key][keyChild].initItem === 'function' && e.find( '.qwfw-shortcode' ).length ) {
						qodeWishlistForWooCommerce.shortcodes[key][keyChild].initItem( e.find( '.qwfw-shortcode' ) );
					} else if ( typeof qodeWishlistForWooCommerce.shortcodes[key][keyChild].init === 'function' ) {
						qodeWishlistForWooCommerce.shortcodes[key][keyChild].init();
					}
				}
			);
		},
	};

})( jQuery );
