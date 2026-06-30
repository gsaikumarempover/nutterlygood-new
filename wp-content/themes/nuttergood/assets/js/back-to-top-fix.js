( function ( $ ) {
	'use strict';

	/**
	 * Reliable scroll-to-top — uses live scroll position and native smooth scroll.
	 */
	function scrollToTop() {
		var start = window.pageYOffset || document.documentElement.scrollTop || 0;

		if ( start <= 0 ) {
			return;
		}

		if ( typeof qodef !== 'undefined' ) {
			qodef.scroll = 0;
		}

		if ( 'scrollBehavior' in document.documentElement.style ) {
			window.scrollTo( { top: 0, behavior: 'smooth' } );
			return;
		}

		var step = function () {
			var current = window.pageYOffset || document.documentElement.scrollTop || 0;
			if ( current <= 0 ) {
				window.scrollTo( 0, 0 );
				return;
			}
			window.scrollTo( 0, Math.max( 0, current - Math.max( current * 0.12, 40 ) ) );
			window.requestAnimationFrame( step );
		};

		window.requestAnimationFrame( step );
	}

	$( document ).ready( function () {
		var $btt = $( '#qodef-back-to-top' );

		if ( ! $btt.length ) {
			return;
		}

		$btt.off( 'click.nuttergoodBtt' ).on( 'click.nuttergoodBtt', function ( e ) {
			e.preventDefault();
			e.stopImmediatePropagation();
			scrollToTop();
			return false;
		} );
	} );
}( jQuery ) );