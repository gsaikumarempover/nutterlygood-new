(function () {
	'use strict';

	var cfg = window.ngFarmleyOrderPay || {};
	var i18n = cfg.i18n || {};

	function $(sel, ctx) {
		return (ctx || document).querySelector(sel);
	}

	function showOverlay(type) {
		var overlay = $('#ng-farmley-payment-overlay');
		if (!overlay) {
			return;
		}

		var title = $('.ng-farmley-payment-overlay__title', overlay);
		var text = $('.ng-farmley-payment-overlay__text', overlay);
		var icon = $('.ng-farmley-payment-overlay__icon', overlay);
		var actions = $('.ng-farmley-payment-overlay__actions', overlay);

		overlay.classList.remove(
			'is-processing',
			'is-cancelled',
			'is-modal-closed'
		);
		overlay.classList.add('is-visible', 'is-' + type);
		overlay.hidden = false;
		overlay.setAttribute('aria-hidden', 'false');

		if (type === 'processing') {
			title.textContent = i18n.processingTitle || 'Processing payment';
			text.textContent =
				i18n.processingText ||
				'Please wait while we confirm your payment.';
			icon.innerHTML =
				'<span class="ng-farmley-payment-overlay__spinner" aria-hidden="true"></span>';
			actions.innerHTML = '';
			return;
		}

		if (type === 'cancelled') {
			title.textContent = i18n.cancelledTitle || 'Payment cancelled';
			text.textContent =
				i18n.cancelledText ||
				'Your order is saved. You can try paying again.';
			icon.innerHTML =
				'<span class="ng-farmley-payment-overlay__mark ng-farmley-payment-overlay__mark--cancel" aria-hidden="true"></span>';
			actions.innerHTML =
				'<button type="button" class="ng-farmley-order-pay__btn ng-farmley-order-pay__btn--primary" data-ng-action="retry">' +
				(i18n.tryAgain || 'Try again') +
				'</button>';
			return;
		}

		if (type === 'modal-closed') {
			overlay.classList.remove('is-visible');
			overlay.hidden = true;
			overlay.setAttribute('aria-hidden', 'true');
			showInlineBanner(
				i18n.modalClosedText ||
					'Payment window closed. Tap Pay Now when you are ready.'
			);
		}
	}

	function hideOverlay() {
		var overlay = $('#ng-farmley-payment-overlay');
		if (!overlay) {
			return;
		}
		overlay.classList.remove('is-visible', 'is-processing', 'is-cancelled');
		overlay.hidden = true;
		overlay.setAttribute('aria-hidden', 'true');
	}

	function showInlineBanner(message) {
		var gateway = $('.ng-farmley-order-pay__gateway');
		if (!gateway) {
			return;
		}

		var existing = $('.ng-farmley-order-pay__banner', gateway);
		if (existing) {
			existing.remove();
		}

		var banner = document.createElement('div');
		banner.className =
			'ng-farmley-order-pay__banner ng-farmley-order-pay__banner--info is-visible';
		banner.setAttribute('role', 'status');
		banner.textContent = message;
		gateway.insertBefore(banner, gateway.firstChild);

		window.setTimeout(function () {
			banner.classList.remove('is-visible');
			window.setTimeout(function () {
				if (banner.parentNode) {
					banner.parentNode.removeChild(banner);
				}
			}, 400);
		}, 6000);
	}

	function redirectToOrderPay() {
		var url = cfg.orderPayUrl || window.location.href.split('#')[0];
		var target = url.indexOf('ng_payment=') === -1
			? url + (url.indexOf('?') > -1 ? '&' : '?') + 'ng_payment=cancelled'
			: url.replace(/ng_payment=[^&]+/, 'ng_payment=cancelled');
		window.location.href = target;
	}

	function bindCancelButton() {
		var cancelBtn = $('#btn-razorpay-cancel');
		if (!cancelBtn) {
			return;
		}

		cancelBtn.removeAttribute('onclick');
		cancelBtn.addEventListener('click', function (event) {
			event.preventDefault();
			if (window.ngFarmleyButtonLoader) {
				window.ngFarmleyButtonLoader.setLoading(cancelBtn, true);
			} else {
				cancelBtn.classList.add('ng-btn-loading');
				cancelBtn.disabled = true;
			}
			showOverlay('cancelled');
			window.setTimeout(redirectToOrderPay, 1400);
		});
	}

	function bindProcessingState() {
		var successMsg = $('#msg-razorpay-success');
		if (!successMsg) {
			return;
		}

		var observer = new MutationObserver(function () {
			if (successMsg.style.display && successMsg.style.display !== 'none') {
				showOverlay('processing');
			}
		});

		observer.observe(successMsg, {
			attributes: true,
			attributeFilter: ['style'],
		});
	}

	function watchRazorpayModal() {
		var wasOpen = false;

		window.setInterval(function () {
			var frame =
				document.querySelector('.razorpay-container') ||
				document.querySelector('iframe[src*="razorpay"]') ||
				document.querySelector('.razorpay-checkout-frame');

			if (frame) {
				wasOpen = true;
				return;
			}

			if (wasOpen) {
				wasOpen = false;
				var overlay = $('#ng-farmley-payment-overlay');
				if (overlay && overlay.classList.contains('is-processing')) {
					return;
				}
				showOverlay('modal-closed');
			}
		}, 500);
	}

	function bindOverlayActions() {
		document.addEventListener('click', function (event) {
			var btn = event.target.closest('[data-ng-action="retry"]');
			if (!btn) {
				return;
			}
			event.preventDefault();
			hideOverlay();
			var payBtn = $('#btn-razorpay');
			if (payBtn) {
				payBtn.removeAttribute('disabled');
				payBtn.click();
			}
		});
	}

	function enhanceGatewayMarkup() {
		var gateway = $('.ng-farmley-order-pay__gateway');
		var payBtn = $('#btn-razorpay');
		var cancelBtn = $('#btn-razorpay-cancel');

		if (!gateway || !payBtn) {
			return;
		}

		var actions = $('.ng-farmley-order-pay__actions');
		if (!actions) {
			actions = document.createElement('div');
			actions.className = 'ng-farmley-order-pay__actions';
			if (cancelBtn && cancelBtn.parentNode) {
				cancelBtn.parentNode.replaceWith(actions);
			}
			actions.appendChild(payBtn);
			if (cancelBtn) {
				actions.appendChild(cancelBtn);
			}
			gateway.appendChild(actions);
		}

		payBtn.classList.add('ng-farmley-order-pay__btn', 'ng-farmley-order-pay__btn--primary');
		payBtn.textContent = payBtn.textContent.trim() || 'Pay Now';

		if (cancelBtn) {
			cancelBtn.classList.add('ng-farmley-order-pay__btn', 'ng-farmley-order-pay__btn--ghost');
			cancelBtn.textContent = cancelBtn.textContent.trim() || 'Cancel';
		}

		var legacyLead = gateway.querySelector('p:not(.ng-farmley-order-pay__payment-lead)');
		if (legacyLead && !legacyLead.id) {
			legacyLead.classList.add('ng-farmley-order-pay__legacy-note');
		}
	}

	function init() {
		enhanceGatewayMarkup();
		bindCancelButton();
		bindProcessingState();
		watchRazorpayModal();
		bindOverlayActions();

		// phpcs:ignore — query param handled in PHP too; show overlay on return from server redirect.
		if (window.location.search.indexOf('ng_payment=cancelled') > -1) {
			window.setTimeout(function () {
				showInlineBanner(
					i18n.cancelledText ||
						'Payment was cancelled. You can try again below.'
				);
			}, 300);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
