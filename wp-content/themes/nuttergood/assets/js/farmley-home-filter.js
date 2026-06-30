( function ( $ ) {
	'use strict';

	function positionFilterLoader( $holder ) {
		var $filter  = $holder.find( '.qodef-m-filter' ).first();
		var $spinner = $holder.find( '.qodef-filter-pagination-spinner' ).first();

		if ( ! $filter.length || ! $spinner.length ) {
			return;
		}

		var top = $filter.position().top + $filter.outerHeight( true ) + 12;

		$spinner.css( {
			top: top,
			bottom: 'auto',
			left: '50%',
			marginLeft: -16,
		} );
	}

	function bindFilterLoader( $holder ) {
		if ( ! $holder.length || $holder.data( 'ngFilterLoaderBound' ) ) {
			return;
		}

		$holder.data( 'ngFilterLoaderBound', true );

		$holder.on( 'click', '.qodef-m-filter-item', function () {
			window.setTimeout( function () {
				positionFilterLoader( $holder );
			}, 0 );
		} );
	}

	function initFilterLoaders() {
		$( '.qodef-woo-product-list.qodef-filter--on' ).each( function () {
			bindFilterLoader( $( this ) );
		} );
	}

	$( document ).ready( initFilterLoaders );

	$( window ).on( 'resize', function () {
		$( '.qodef-woo-product-list.qodef-filter--on.qodef--filter-loading' ).each( function () {
			positionFilterLoader( $( this ) );
		} );
	} );

	$( document ).on( 'greenpath_trigger_get_new_posts', function () {
		initFilterLoaders();
	} );
}( jQuery ) );