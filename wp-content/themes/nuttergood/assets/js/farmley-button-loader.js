/**
 * Shared button loading state — checkout, pay, login, register, account.
 */
(function ($) {
	'use strict';

	var LOADING_CLASS = 'ng-btn-loading';

	function setLoading(btn, isLoading, loadingText) {
		var $btn = btn && btn.jquery ? btn : $(btn);

		if (!$btn || !$btn.length) {
			return;
		}

		if (isLoading) {
			if (!$btn.data('ngOriginalHtml')) {
				$btn.data('ngOriginalHtml', $btn.html());
			}
			$btn.addClass(LOADING_CLASS).prop('disabled', true).attr('aria-busy', 'true');
			if (loadingText) {
				$btn.attr('data-ng-loading-text', loadingText);
			}
			return;
		}

		$btn.removeClass(LOADING_CLASS).prop('disabled', false).removeAttr('aria-busy');
		if ($btn.data('ngOriginalHtml')) {
			$btn.html($btn.data('ngOriginalHtml'));
		}
	}

	function bindFormSubmit(selector, loadingText) {
		$(document).on('submit', selector, function () {
			var $form = $(this);
			var $btn = $form.find('[type="submit"]').first();
			if ($btn.length && !$btn.prop('disabled')) {
				setLoading($btn, true, loadingText);
			}
		});
	}

	function bindCheckoutPlaceOrder() {
		$(document.body).on('checkout_place_order', function () {
			setLoading($('#place_order'), true);
		});

		$(document.body).on('checkout_error init_checkout updated_checkout', function () {
			setLoading($('#place_order'), false);
		});

		$('form.checkout').on('submit', function () {
			var $btn = $('#place_order');
			if ($btn.length && !$(this).hasClass('processing')) {
				window.setTimeout(function () {
					if ($('form.checkout').hasClass('processing')) {
						setLoading($btn, true);
					}
				}, 50);
			}
		});
	}

	function bindPayNow() {
		$(document).on('click', '#btn-razorpay', function () {
			setLoading($(this), true);
		});

		// Razorpay modal opened — stop pay button spinner.
		window.setInterval(function () {
			var frame =
				document.querySelector('.razorpay-container') ||
				document.querySelector('iframe[src*="razorpay"]') ||
				document.querySelector('.razorpay-checkout-frame');
			if (frame) {
				setLoading($('#btn-razorpay'), false);
			}
		}, 400);
	}

	function bindCreateAccount() {
		$(document).on('submit', '.ng-farmley-create-account__form', function () {
			setLoading($(this).find('.ng-farmley-create-account__btn'), true);
		});
	}

	function bindAccountOrderActions() {
		$(document).on('click', '.woocommerce-orders-table__cell-order-actions .button', function () {
			setLoading($(this), true);
		});
	}

	function init() {
		bindFormSubmit('.woocommerce-form-login', 'Signing in…');
		bindFormSubmit('.woocommerce-form-register', 'Creating account…');
		bindFormSubmit('form.woocommerce-form--login', 'Signing in…');
		bindFormSubmit('form.register', 'Creating account…');
		bindFormSubmit('.ng-farmley-checkout-login__form-wrap form.login', 'Signing in…');
		bindCheckoutPlaceOrder();
		bindPayNow();
		bindCreateAccount();
		bindAccountOrderActions();
	}

	window.ngFarmleyButtonLoader = {
		setLoading: setLoading,
	};

	$(init);
})(jQuery);
