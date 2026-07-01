( function ( $ ) {
	'use strict';

	var BUSY_CLASS = 'is-busy';
	var LOADING_CLASS = 'ng-btn-loading';

	function i18n( key, fallback ) {
		return window.ngFarmleyNewsletter && ngFarmleyNewsletter.i18n && ngFarmleyNewsletter.i18n[ key ]
			? ngFarmleyNewsletter.i18n[ key ]
			: fallback;
	}

	function setFeedback( $wrap, text, isError ) {
		var $feedback = $wrap.find( '[data-ng-newsletter-feedback]' ).first();
		$feedback
			.text( text || '' )
			.toggleClass( 'is-error', !! isError )
			.toggleClass( 'is-success', ! isError && !! text );
	}

	function setFormBusy( $form, busy ) {
		var $wrap = $form.closest( '[data-ng-newsletter-wrap]' );
		var $btn = $form.find( '[type="submit"]' ).first();
		var $btnText = $btn.find( '.qodef-m-text' ).first();

		if ( busy ) {
			if ( $form.data( 'ngNewsletterBusy' ) ) {
				return;
			}

			$form.data( 'ngNewsletterBusy', true );
			$wrap.addClass( BUSY_CLASS );
			$form.attr( 'aria-busy', 'true' );

			$form.find( 'input, button' ).prop( 'disabled', true );
			$btn.addClass( LOADING_CLASS );
			if ( $btnText.length ) {
				$btnText.data( 'ngOriginalText', $btnText.text() );
				$btnText.text( i18n( 'submitting', 'Submitting…' ) );
			}

			return;
		}

		$form.data( 'ngNewsletterBusy', false );
		$wrap.removeClass( BUSY_CLASS );
		$form.removeAttr( 'aria-busy' );
		$form.find( 'input, button' ).prop( 'disabled', false );
		$btn.removeClass( LOADING_CLASS );

		if ( $btnText.length && $btnText.data( 'ngOriginalText' ) ) {
			$btnText.text( $btnText.data( 'ngOriginalText' ) );
			$btnText.removeData( 'ngOriginalText' );
		}
	}

	function showSuccess( $wrap, message ) {
		$wrap.removeClass( BUSY_CLASS ).removeAttr( 'aria-busy' );

		var html =
			'<div class="ng-farmley-newsletter-success" data-ng-newsletter-success role="status">' +
			'<span class="ng-farmley-newsletter-success__icon" aria-hidden="true">✓</span>' +
			'<p class="ng-farmley-newsletter-success__text"></p>' +
			'</div>';

		$wrap.html( html );
		$wrap.find( '.ng-farmley-newsletter-success__text' ).text( message );

		var el = $wrap.get( 0 );
		if ( el && el.scrollIntoView ) {
			el.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
		}

		if ( window.history && window.history.replaceState ) {
			var url = new URL( window.location.href );
			url.searchParams.set( 'newsletter', 'thanks' );
			window.history.replaceState( {}, '', url.toString() );
		}
	}

	function submitNewsletter( $form ) {
		if ( ! window.ngFarmleyNewsletter || ! ngFarmleyNewsletter.ajaxUrl || ! ngFarmleyNewsletter.nonce ) {
			$form.get( 0 ).submit();
			return;
		}

		var $wrap = $form.closest( '[data-ng-newsletter-wrap]' );
		var email = $.trim( $form.find( 'input[type="email"]' ).val() || '' );

		setFeedback( $wrap, '', false );

		if ( ! email || ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( email ) ) {
			setFeedback( $wrap, i18n( 'invalid', 'Please enter a valid email address.' ), true );
			return;
		}

		setFormBusy( $form, true );

		$.post( ngFarmleyNewsletter.ajaxUrl, {
			action: 'ng_farmley_newsletter',
			security: ngFarmleyNewsletter.nonce,
			email: email,
		} )
			.done( function ( response ) {
				if ( response && response.success && response.data && response.data.message ) {
					showSuccess( $wrap, response.data.message );
					return;
				}

				var msg =
					response && response.data && response.data.message
						? response.data.message
						: i18n( 'failed', 'Something went wrong. Please try again.' );
				setFeedback( $wrap, msg, true );
				setFormBusy( $form, false );
			} )
			.fail( function () {
				setFeedback( $wrap, i18n( 'failed', 'Something went wrong. Please try again.' ), true );
				setFormBusy( $form, false );
			} );
	}

	function bindNewsletterForm( $form ) {
		if ( ! $form.length || $form.data( 'ngNewsletterBound' ) ) {
			return;
		}

		$form.data( 'ngNewsletterBound', true );

		$form.on( 'submit.ngNewsletter', function ( event ) {
			event.preventDefault();
			if ( $form.data( 'ngNewsletterBusy' ) ) {
				return;
			}
			submitNewsletter( $form );
		} );
	}

	function scrollToSuccessFromUrl() {
		var params = new URLSearchParams( window.location.search );
		if ( params.get( 'newsletter' ) !== 'thanks' ) {
			return;
		}

		var $success = $( '[data-ng-newsletter-success]' ).first();
		if ( $success.length && $success.get( 0 ).scrollIntoView ) {
			window.setTimeout( function () {
				$success.get( 0 ).scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
			}, 300 );
		}
	}

	$( function () {
		$( '[data-ng-newsletter-form]' ).each( function () {
			bindNewsletterForm( $( this ) );
		} );
		scrollToSuccessFromUrl();
	} );
}( jQuery ) );