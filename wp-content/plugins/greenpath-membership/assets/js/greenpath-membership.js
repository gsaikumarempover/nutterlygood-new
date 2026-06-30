(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qodefLoginModal.init();
		}
	);

	var qodefLoginModal = {
		init: function () {
			this.holder = $( '#qodef-membership-login-modal' );

			if ( this.holder.length ) {
				qodefLoginModal.triggerShowModal( this.holder );
				qodefLoginModal.initTabs( this.holder );
				qodefLoginModal.triggerResetPasswordLink( this.holder );
				qodefLoginModal.triggerFormSubmit( this.holder );
				qodefLoginModal.triggerFormSocialSubmit( this.holder );
			}
		},
		triggerShowModal: function ( $holder ) {
			$holder.children( '.qodef-membership-login-modal-overlay' ).on(
				'click',
				function () {
					qodefLoginModal.hideModal( $holder );
				}
			);

			// Esc press
			$( window ).on(
				'keyup',
				function ( e ) {
					if ( e.keyCode === 27 ) {
						qodefLoginModal.hideModal( $holder );
					}
				}
			);

			$( document.body ).on(
				'greenpath_membership_trigger_login_modal',
				function () {
					qodefLoginModal.showModal( $holder );
				}
			);
		},
		showModal: function ( $holder ) {
			if ( ! $holder.hasClass( 'qodef--opened' ) ) {
				$holder.addClass( 'qodef--opened' );
			}
		},
		hideModal: function ( $holder ) {
			if ( $holder.hasClass( 'qodef--opened' ) ) {
				$holder.removeClass( 'qodef--opened' );
			}
		},
		initTabs: function ( $holder ) {
			$holder.children( '.qodef-membership-login-modal-content' ).tabs();
		},
		triggerResetPasswordLink: function ( $holder ) {
			$holder.find( '#qodef-membership-login-modal-part .qodef-m-links-reset-password' ).on(
				'click',
				function ( e ) {
					e.preventDefault();

					var $navigationItem = $holder.find( '.qodef-membership-login-modal-navigation .qodef-m-navigation-item.qodef--reset-password' );

					if ( $navigationItem.length ) {
						$navigationItem.find( '.qodef-e-link' ).trigger( 'click' );
					}
				}
			);
		},
		triggerFormSubmit: function ( $holder ) {
			var $forms = $holder.find( 'form' );

			if ( $forms.length ) {
				$forms.each(
					function () {
						var $thisForm = $( this );

						$thisForm.on(
							'submit',
							function ( e ) {
								e.preventDefault();

								if ( ! $thisForm.hasClass( 'qodef--loading' ) ) {
									qodefLoginModal.triggerRequest( $thisForm );
								}
							}
						);
					}
				);
			}
		},
		triggerFormSocialSubmit: function ( $holder ) {
			var $form = $holder.find( 'form[id*="qodef-membership-login"]' );

			if ( $form.length ) {
				var $socialButton = $form.find( '.qodef-m-social-login' );

				$socialButton.find( '.qodef-m-social-login-btn' ).on(
					'click',
					function ( e ) {
						e.preventDefault();

						$( document ).trigger(
							'greenpath_membership_social_login_is_triggered',
							[qodefLoginModal, $form, $( this ).data( 'social' )]
						);
					}
				);
			}
		},
		triggerRequest: function ( $holder, socialNetwork, socialResponse ) {
			$holder.addClass( 'qodef--loading' );

			var $responseHolder = $holder.find( '.qodef-m-response' ),
				$requestType    = $holder.find( '.qodef-m-request-type' ).val();

			$responseHolder.removeClass( 'qodef--success qodef--error qodef--undefined' ).empty();

			var ajaxData = {
				options: {
					request_type: $requestType,
					redirect: $holder.find( '.qodef-m-redirect' ).val(),
					private_key: 'false'
				},
				nonce: $holder.find( '#greenpath-membership-ajax-' + $requestType + '-nonce' ).val()
			};

			var httpType  = 'POST';
			var restRoute = qodefGlobal.vars.loginModalRestRoute;

			switch ($requestType) {
				case 'login':
					httpType  = 'GET';
					restRoute = qodefGlobal.vars.loginModalGetRestRoute;

					ajaxData.options.user_login    = $holder.find( '.qodef-m-user-name' ).val();
					ajaxData.options.user_password = $holder.find( '.qodef-m-user-password' ).val();
					ajaxData.options.remember      = $holder.find( '.qodef-m-links-remember:checked' ).length;

					if ( typeof socialNetwork !== 'undefined' && socialNetwork !== null ) {
						ajaxData.options.social_login = socialNetwork;

						if ( typeof socialResponse !== 'undefined' && socialResponse !== null ) {
							ajaxData.options.social_response = socialResponse;
						}
					}
					break;
				case 'register':
					ajaxData.options.user_login            = $holder.find( '.qodef-m-user-name' ).val();
					ajaxData.options.user_email            = $holder.find( '.qodef-m-user-email' ).val();
					ajaxData.options.user_password         = $holder.find( '.qodef-m-user-password' ).val();
					ajaxData.options.user_confirm_password = $holder.find( '.qodef-m-user-confirm-password' ).val();
					break;
				case 'reset-password':
					ajaxData.options.user_login = $holder.find( '.qodef-m-user-login' ).val();
					break;
			}

			$.ajax(
				{
					type: httpType,
					url: qodefGlobal.vars.restUrl + restRoute,
					data: ajaxData,
					success: function ( response ) {
						$responseHolder.addClass( 'qodef--' + response.status ).html( response.message );

						if ( response.status === 'success' ) {
							if ( $requestType === 'register' && ! ajaxData.options.hasOwnProperty( 'social_login' ) ) {
								qodefLoginModal.triggerForceLogin( $holder, ajaxData, response.redirect );
							} else {
								qodefLoginModal.triggerRedirection( response.redirect );
							}
						}
					},
					complete: function () {
						$holder.removeClass( 'qodef--loading' );
					}
				}
			);

			return false;
		},
		triggerRedirection: function ( url ) {
			if ( url !== '' && url !== window.location ) {
				window.location = url;
			}
		},
		triggerForceLogin: function ( $holder, ajaxData, redirect ) {
			ajaxData.options.request_type = 'login';
			ajaxData.nonce                = $holder.parent().find( '#greenpath-membership-ajax-login-nonce' ).val();

			$.ajax(
				{
					type: 'GET',
					url: qodefGlobal.vars.restUrl + qodefGlobal.vars.loginModalGetRestRoute,
					data: ajaxData,
					dataType: 'json',
					success: function ( response ) {

						if ( response.status === 'success' ) {
							qodefLoginModal.triggerRedirection( redirect );
						}
					}
				}
			);
		}
	};

})( jQuery );

// Load the SDK asynchronously
(function ( d, s, id ) {
	var js, fjs = d.getElementsByTagName( s )[0];
	if ( d.getElementById( id ) ) {
		return;
	}
	js     = d.createElement( s );
	js.id  = id;
	js.src = 'https://connect.facebook.net/en_US/sdk.js';
	fjs.parentNode.insertBefore( js, fjs );
}(
	document,
	'script',
	'facebook-jssdk'
));

(function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		qodefFacebookLogin.init();
	} );

	$( document ).on(
		'greenpath_membership_social_login_is_triggered',
		function ( e, $modal, $form, social ) {
			if ( qodefFacebookLogin.fbIsAppIdSet() && social === 'facebook' ) {
				qodefFacebookLogin.fbLogin( $modal, $form, social );
			}
		}
	);

	var qodefFacebookLogin = {
		init: function () {

			if ( qodefFacebookLogin.fbIsAppIdSet() ) {
				qodefFacebookLogin.fbAsyncInit( greenpathMembershipGlobal.facebookAppId );
			}
		},
		fbIsAppIdSet: function () {
			return typeof greenpathMembershipGlobal.facebookAppId !== 'undefined' && greenpathMembershipGlobal.facebookAppId !== '';
		},
		fbAsyncInit: function ( appID ) {

			if ( appID !== '' ) {
				window.fbAsyncInit = function () {
					FB.init( {
						appId: appID, // - test app ID
						autoLogAppEvents: true,
						cookie: true,  // enable cookies to allow the server to access
						xfbml: true,  // parse social plugins on this page
						version: 'v5.0' // use version 5.0
					} );

					window.FB = FB;
				};
			}
		},
		fbLogin: function ( $modal, $form, social ) {
			window.FB.login(
				function ( response ) {
					qodefFacebookLogin.fbCheckStatus( response, $modal, $form, social );
				},
				{ scope: 'email, public_profile' }
			);
		},
		fbCheckStatus: function ( response, $modal, $form, social ) {
			if ( response.status === 'connected' ) {
				// Logged into your app and Facebook.
				qodefFacebookLogin.fbGetUserData( $modal, $form, social );
			} else if ( response.status === 'not_authorized' ) {
				// The person is logged into Facebook, but not your app.
				console.log( 'Please log into this app' );
			} else {
				// The person is not logged into Facebook, so we're not sure if
				// they are logged into this app or not.
				console.log( 'Please log into Facebook' );
			}
		},
		fbGetUserData: function ( $modal, $form, social ) {
			FB.api(
				'/me',
				'GET',
				{ 'fields': 'id, name, email, link, picture' },
				function ( response ) {
					response.image = response.picture.data.url;

					if ( ! $form.hasClass( 'qodef--loading' ) ) {
						$modal.triggerRequest( $form, social, response );
					}
				}
			);
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).on(
		'ready',
		function () {
			qodefGoogleLogin.init();
		}
	);

	$( document ).on(
		'greenpath_membership_social_login_is_triggered',
		function ( e, $modal, $form, social ) {
			if ( qodefGoogleLogin.isAppIdSet() && social === 'google' ) {
				qodefGoogleLogin.login( $modal, $form, social );
			}
		}
	);

	var qodefGoogleLogin = {
		init: function () {

			if ( qodefGoogleLogin.isAppIdSet() ) {
				qodefGoogleLogin.asyncInit( greenpathMembershipGlobal.googleAppId );
			}
		},
		isAppIdSet: function () {
			return typeof greenpathMembershipGlobal.googleAppId !== 'undefined' && greenpathMembershipGlobal.googleAppId !== '';
		},
		asyncInit: function ( appID ) {

			if ( appID !== '' ) {
				google.accounts.id.initialize(
					{
						client_id: appID,
					}
				);
			}
		},
		login: function ( $modal, $form, social ) {

			var client = google.accounts.oauth2.initTokenClient(
				{
					client_id: greenpathMembershipGlobal.googleAppId,
					scope: 'https://www.googleapis.com/auth/userinfo.profile',
					prompt: '',
					callback: function ( response ) {
						qodefGoogleLogin.signIn( response, $modal, $form, social );
					},
				}
			);

			client.requestAccessToken();
		},
		signIn: function ( response, $modal, $form, social ) {
			if ( ! response || ! response.access_token ) {
				return;
			}

			var url = 'https://people.googleapis.com/v1/people/me?personFields=names,emailAddresses,photos&access_token=' + encodeURIComponent( response.access_token );

			fetch( url ).then(
				function ( response ) {
					return response.text();
				}
			).then(
				function ( responseObject ) {

					if ( responseObject && responseObject.length ) {
						qodefGoogleLogin.getUserData( responseObject, $modal, $form, social );
					}
				}
			);
		},
		getUserData: function ( responseObject, $modal, $form, social ) {

			if ( responseObject ) {
				responseObject = JSON.parse( responseObject );

				var userData = {
					id: responseObject?.resourceName.replace( /[A-Za-z\\/]/g, '' ),
					name: responseObject?.names[0]?.displayName,
					email: responseObject?.emailAddresses[0]?.value,
					image: responseObject?.photos[0]?.url,
				};

				if ( ! $form.hasClass( 'qodef--loading' ) ) {
					$modal.triggerRequest( $form, social, userData );
				}
			}
		}
	};

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).on(
		'greenpath_membership_social_login_is_triggered',
		function ( e, $modal, $form, social ) {
			if ( social === 'twitter' ) {
				qodefTwitterLogin.triggerRequest( $modal, $form, social );
			}
		}
	);

	var qodefTwitterLogin = {
		triggerRequest: function ( $modal, $form, social ) {

			if ( ! $form.hasClass( 'qodef--loading' ) ) {
				$modal.triggerRequest( $form, social );
			}
		}
	};

})( jQuery );
