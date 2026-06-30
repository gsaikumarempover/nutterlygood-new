(function ( $ ) {
	'use strict';

	$( window ).on(
		'elementor/frontend/init',
		function () {
			qodeCompareForWooCommerceElementor.init();
		}
	);

	var qodeCompareForWooCommerceElementor = {
		init: function () {
			var isEditMode = Boolean( elementorFrontend.isEditMode() );

			if ( isEditMode ) {
				for ( var key in qodeCompareForWooCommerce.shortcodes ) {
					for ( var keyChild in qodeCompareForWooCommerce.shortcodes[key] ) {
						qodeCompareForWooCommerceElementor.reInitShortcode( key, keyChild );
					}
				}
			}
		},
		reInitShortcode: function ( key, keyChild ) {
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/' + key + '.default',
				function ( e ) {
					// Check if object doesn't exist and print the module where is the error.
					if ( typeof qodeCompareForWooCommerce.shortcodes[key][keyChild] === 'undefined' ) {
						console.log( keyChild );
					} else if ( typeof qodeCompareForWooCommerce.shortcodes[key][keyChild].initItem === 'function' && e.find( '.qcfw-shortcode' ).length ) {
						qodeCompareForWooCommerce.shortcodes[key][keyChild].initItem( e.find( '.qcfw-shortcode' ) );
					} else if ( typeof qodeCompareForWooCommerce.shortcodes[key][keyChild].init === 'function' ) {
						qodeCompareForWooCommerce.shortcodes[key][keyChild].init();
					}
				}
			);
		},
	};

})( jQuery );
