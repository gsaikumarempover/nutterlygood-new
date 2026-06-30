(function ($) {
	'use strict';

	function initCheckoutLogin() {
		var $wrap = $('[data-ng-login]');
		if (!$wrap.length) {
			return;
		}

		var $panel = $('#ng-farmley-checkout-login-form');
		var $toggle = $wrap.find('.showlogin');

		$toggle.off('click.ngFarmley').on('click.ngFarmley', function (event) {
			event.preventDefault();

			var isOpen = $panel.hasClass('is-open');
			if (isOpen) {
				$panel.removeClass('is-open').attr('hidden', true);
				$toggle.attr('aria-expanded', 'false');
				return;
			}

			$panel.removeAttr('hidden').addClass('is-open');
			$toggle.attr('aria-expanded', 'true');

			var $user = $panel.find('#username');
			if ($user.length) {
				window.setTimeout(function () {
					$user.trigger('focus');
				}, 120);
			}
		});

		// Hide duplicate WooCommerce toggle notice if present.
		$wrap.prev('.woocommerce-form-login-toggle').remove();
	}

	$(document.body).on('init_checkout updated_checkout', initCheckoutLogin);
	$(initCheckoutLogin);
})(jQuery);
