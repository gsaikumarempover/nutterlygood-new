(function ($) {
	'use strict';

	function ajaxUrl(endpoint) {
		if (typeof wc_cart_params !== 'undefined' && wc_cart_params.wc_ajax_url) {
			return wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', endpoint);
		}
		if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.wc_ajax_url) {
			return wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', endpoint);
		}
		var cfg = window.ngFarmleyCart || window.ngFarmleySideCart || {};
		if (cfg.wcAjaxUrl) {
			return cfg.wcAjaxUrl.toString().replace('%%endpoint%%', endpoint);
		}
		return (window.location.origin || '') + '/?wc-ajax=' + endpoint;
	}

	function applyCoupon($btn, code) {
		var cfg = window.ngFarmleyCart || {};
		var url = ajaxUrl('ng_farmley_side_cart_apply_coupon');

		if (!url || !cfg.nonce || !code || !$btn.length || $btn.prop('disabled') || $btn.hasClass('is-busy')) {
			return;
		}

		var $wrap = $btn.closest('[data-ng-cart-coupons]');
		var $feedback = $wrap.find('[data-ng-sc-coupon-feedback], [data-ng-cart-coupon-feedback]').first();
		var applying = (cfg.i18n && cfg.i18n.applying) || 'Applying…';

		$btn.addClass('is-busy').prop('disabled', true).text(applying);
		$feedback.removeClass('is-error is-success').text('');

		$.post(url, {
			security: cfg.nonce,
			coupon_code: code
		})
			.done(function (response) {
				if (response && response.success) {
					$feedback.addClass('is-success').text((response.data && response.data.message) || ((cfg.i18n && cfg.i18n.applied) || 'Applied'));
					window.location.reload();
					return;
				}

				var msg =
					(response && response.data && response.data.message) ||
					((cfg.i18n && cfg.i18n.failed) || 'Could not apply coupon.');
				$feedback.addClass('is-error').text(msg);
				$btn.removeClass('is-busy').prop('disabled', false).text((cfg.i18n && cfg.i18n.apply) || 'Apply');
			})
			.fail(function () {
				$feedback
					.addClass('is-error')
					.text((cfg.i18n && cfg.i18n.failed) || 'Could not apply coupon.');
				$btn.removeClass('is-busy').prop('disabled', false).text((cfg.i18n && cfg.i18n.apply) || 'Apply');
			});
	}

	function scheduleCartUpdate() {
		var $form = $('.woocommerce-cart-form').first();
		var $button = $form.find('button[name="update_cart"], input[name="update_cart"]').first();

		if (!$form.length || !$button.length || $form.hasClass('ng-farmley-cart-updating')) {
			return;
		}

		clearTimeout($form.data('ngFarmleyCartUpdateTimer'));
		$form.data(
			'ngFarmleyCartUpdateTimer',
			setTimeout(function () {
				$button.prop('disabled', false).removeAttr('disabled aria-disabled');
				$form.addClass('ng-farmley-cart-updating');
				$button.trigger('click');
			}, 650)
		);
	}

	/**
	 * True when the page is the Farmley cart-2 page (uses our custom AJAX shell refresh).
	 * On this page we skip WC's native update_cart form submission to avoid race conditions.
	 */
	function isFarmleyCustomCartPage() {
		if (window.ngFarmleySideCart && ngFarmleySideCart.isCartPage) {
			return true;
		}
		var path = (window.location.pathname || '').toLowerCase();
		return path.indexOf('cart-2') !== -1 || document.body.classList.contains('woocommerce-cart');
	}

	var fullCartRefreshTimer = null;

	function refreshCartDynamicSections($html) {
		var $source = $html && $html.length ? $html : null;
		var selectors = [
			'.ng-farmley-sc-progress, .ng-farmley-cart-progress',
			'.ng-farmley-cart-milestones-wrap',
			'.ng-farmley-cart-sidebar-panel'
		];

		selectors.forEach(function (selector) {
			var $current = $(selector).first();
			if (!$current.length) {
				return;
			}

			var $fresh = $source ? $source.find(selector).first() : $();
			if ($fresh.length) {
				$current.replaceWith($fresh);
			}
		});
	}

	function refreshFullCartPage() {
		if ( typeof window.ngFarmleyRefreshCartPage === 'function' ) {
			window.ngFarmleyRefreshCartPage();
			return;
		}

		if (!document.body.classList.contains('woocommerce-cart') && !document.querySelector('.woocommerce-cart-form')) {
			return;
		}

		var url = ajaxUrl('ng_farmley_cart_page_fragments');
		var cfg = window.ngFarmleyCart || window.ngFarmleySideCart || {};

		if (!url || !cfg.nonce) {
			return;
		}

		$.ajax({
			type: 'POST',
			url: url,
			data: { security: cfg.nonce },
			dataType: 'json'
		}).done(function (response) {
			if (!response || !response.success || !response.data || !response.data.shell_html) {
				return;
			}

			var $parsed = $('<div>').append($.parseHTML(response.data.shell_html, document, true));
			var $newShell = $parsed.find('.ng-farmley-cart-shell').first();
			var $wooPage = $('#qodef-woo-page').first();
			var $shell = $('.ng-farmley-cart-shell').first();

			if ($newShell.length && $wooPage.length) {
				$wooPage.empty().append($newShell);
			} else if ($newShell.length && $shell.length) {
				$shell.replaceWith($newShell);
			} else if ($shell.length) {
				$shell.html(response.data.shell_html);
			}

			if (typeof qodefWooQuantityButtons !== 'undefined' && qodefWooQuantityButtons.init) {
				qodefWooQuantityButtons.init();
			}
		});
	}

	function scheduleFullCartPageRefresh(delay) {
		clearTimeout(fullCartRefreshTimer);
		fullCartRefreshTimer = setTimeout(refreshFullCartPage, delay || 120);
	}

	function refreshCartDynamicSectionsFromServer() {
		$.get(window.location.href, { ng_farmley_cart_refresh: Date.now() })
			.done(function (html) {
				var $html = $('<div>').append($.parseHTML(html, document, true));
				refreshCartDynamicSections($html);
			});
	}

	$(function () {
		$(document.body).on('click.ngFarmleyCartCoupon', '[data-ng-cart-coupons] [data-ng-sc-apply-coupon]', function (event) {
			event.preventDefault();
			var $btn = $(this);
			var code = $btn.attr('data-coupon-code') || '';
			if (!code) {
				return;
			}
			applyCoupon($btn, code);
		});

		$(document.body).on('click.ngFarmleyCartCouponToggle', '[data-ng-cart-coupons] [data-ng-sc-toggle-coupons]', function (event) {
			event.preventDefault();
			var $btn = $(this);
			var $panel = $btn.closest('.ng-farmley-sc-coupon__card').find('[data-ng-sc-coupons-panel]').first();
			if (!$panel.length) {
				return;
			}
			var open = $btn.attr('aria-expanded') !== 'true';
			$btn.attr('aria-expanded', open ? 'true' : 'false').toggleClass('is-open', open);
			$panel.prop('hidden', !open);
		});

		$(document.body).on('input.ngFarmleyAutoCart change.ngFarmleyAutoCart', '.woocommerce-cart-form .qty, .woocommerce-cart-form .qodef-quantity-input', function () {
			scheduleCartUpdate();
		});

		$(document.body).on('click.ngFarmleyAutoCart', '.woocommerce-cart-form .qodef-quantity-minus, .woocommerce-cart-form .qodef-quantity-plus', function () {
			setTimeout(scheduleCartUpdate, 80);
		});

		$(document.body).on('updated_wc_div.ngFarmleyAutoCart wc_fragments_refreshed.ngFarmleyAutoCart', function () {
			$('.woocommerce-cart-form').removeClass('ng-farmley-cart-updating');
			// After WC finishes its own fragment update on cart-2, refresh our shell.
			// Use a short delay to let WC settle its blockUI / totals before we replace HTML.
			if (isFarmleyCustomCartPage() && typeof window.ngFarmleyRefreshCartPage === 'function') {
				setTimeout(window.ngFarmleyRefreshCartPage, 250);
			}
		});

		$(document.body).on('ng_farmley_refresh_cart_page.ngFarmleyAutoCart', refreshFullCartPage);
	});
})(jQuery);
