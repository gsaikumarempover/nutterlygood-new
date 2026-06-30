(function ( $ ) {
	'use strict';
	$( document ).ready(
		function () {
			if ( typeof qodefFramework !== 'undefined' && typeof qodefFramework.qodefDragAndDropCheckboxFields !== 'undefined' ) {
				var $mainHolder = $( '.qodef-page-v4-compare' );
				if ( $mainHolder.length ) {
					qodefFramework.qodefDragAndDropCheckboxFields.init( $( '#qodef_qode_compare_for_woocommerce_table_fields' ), $( '#qodef_qode_compare_for_woocommerce_table_fields_positions' ) );
				}
			}

		}
	);
})( jQuery );