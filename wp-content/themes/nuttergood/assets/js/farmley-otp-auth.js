(function ($) {
	'use strict';

	var BUSY_CLASS = 'is-busy';
	var LOADING_CLASS = 'ng-btn-loading';

	function showMessage($form, text, isError) {
		$form.find('.ng-farmley-otp-form__message')
			.text(text || '')
			.toggleClass('is-error', !!isError)
			.toggleClass('is-success', !isError && !!text);
	}

	function revealOtpStep($form) {
		$form.find('.ng-farmley-otp-form__otp-row').removeClass('is-hidden');
		$form.find('.ng-farmley-otp-form__verify').removeClass('is-hidden');
		$form.find('.ng-farmley-otp-form__otp').prop('required', true).trigger('focus');
	}

	function getVerifyLabel(purpose) {
		return purpose === 'register' ? 'Verify & Register' : 'Verify & Login';
	}

	function getSendLabel($form, cooldownSeconds) {
		if (typeof cooldownSeconds === 'number' && cooldownSeconds > 0) {
			return (ngFarmleyOtp.i18n.resendIn || 'Resend in %ss').replace('%s', String(cooldownSeconds));
		}
		if ($form.data('ngOtpSent')) {
			return ngFarmleyOtp.i18n.resendOtp || 'Resend OTP';
		}
		return ngFarmleyOtp.i18n.sendOtp || 'Send OTP';
	}

	function clearResendCooldown($form) {
		var timer = $form.data('ngOtpCountdownTimer');
		if (timer) {
			clearTimeout(timer);
			$form.removeData('ngOtpCountdownTimer');
		}
		$form.removeData('ngOtpCooldownRemaining');
	}

	function updateSendButtonState($form) {
		var $send = $form.find('.ng-farmley-otp-form__send');
		var remaining = $form.data('ngOtpCooldownRemaining') || 0;

		$send.text(getSendLabel($form, remaining));
		$send.prop('disabled', remaining > 0 || isFormBusy($form));
	}

	function startResendCooldown($form) {
		var cooldown = parseInt(ngFarmleyOtp.resendCooldown, 10) || 45;

		clearResendCooldown($form);
		$form.data('ngOtpSent', true);

		function tick(remaining) {
			$form.data('ngOtpCooldownRemaining', remaining);
			updateSendButtonState($form);

			if (remaining <= 0) {
				$form.removeData('ngOtpCooldownRemaining');
				return;
			}

			var timer = setTimeout(function () {
				tick(remaining - 1);
			}, 1000);
			$form.data('ngOtpCountdownTimer', timer);
		}

		tick(cooldown);
	}

	function resetOtpSendState($form) {
		clearResendCooldown($form);
		$form.removeData('ngOtpSent');
		updateSendButtonState($form);
	}

	function isFormBusy($form) {
		return !!$form.data('ngOtpBusy');
	}

	function setFormBusy($form, busy, $activeBtn, loadingText) {
		var $auth = $form.closest('.ng-farmley-auth--tabs');

		if (busy) {
			if (isFormBusy($form)) {
				return;
			}

			$form.data('ngOtpBusy', true);
			$form.addClass(BUSY_CLASS).attr('aria-busy', 'true');

			$form.find('input, select, textarea, button').each(function () {
				var $el = $(this);
				if (typeof $el.data('ngOtpWasDisabled') === 'undefined') {
					$el.data('ngOtpWasDisabled', $el.prop('disabled'));
				}
				$el.prop('disabled', true);
			});

			if ($activeBtn && $activeBtn.length) {
				$activeBtn.data('ngOtpOriginalText', $activeBtn.text());
				$activeBtn.addClass(LOADING_CLASS);
				if (loadingText) {
					$activeBtn.text(loadingText);
				}
			}

			if ($auth.length) {
				$auth.addClass(BUSY_CLASS);
				$auth.find('.ng-farmley-auth__tab').prop('disabled', true);
			}

			return;
		}

		$form.data('ngOtpBusy', false);
		$form.removeClass(BUSY_CLASS).removeAttr('aria-busy');

		$form.find('input, select, textarea, button').each(function () {
			var $el = $(this);
			$el.prop('disabled', $el.data('ngOtpWasDisabled') === true);
			$el.removeData('ngOtpWasDisabled');
		});

		$form.find('.ng-farmley-otp-form__send, .ng-farmley-otp-form__verify').each(function () {
			var $btn = $(this);
			var purpose = $form.data('purpose') || 'login';

			$btn.removeClass(LOADING_CLASS).removeAttr('aria-busy');

			if ($btn.data('ngOtpOriginalText')) {
				$btn.text($btn.data('ngOtpOriginalText'));
				$btn.removeData('ngOtpOriginalText');
			} else if ($btn.hasClass('ng-farmley-otp-form__verify')) {
				$btn.text(getVerifyLabel(purpose));
			} else if ($btn.hasClass('ng-farmley-otp-form__send')) {
				updateSendButtonState($form);
			}
		});

		if ($auth.length) {
			$auth.removeClass(BUSY_CLASS);
			$auth.find('.ng-farmley-auth__tab').prop('disabled', false);
		}

		if ($form.data('ngOtpSent') || ($form.data('ngOtpCooldownRemaining') || 0) > 0) {
			updateSendButtonState($form);
		}
	}

	function getFormIdentifier($form) {
		var $field = $form.find('.ng-farmley-otp-form__identifier').first();
		return $.trim($field.val() || '');
	}

	function setLoginMethod($form, method) {
		method = method === 'phone' ? 'phone' : 'email';

		var $field = $form.find('.ng-farmley-otp-form__identifier').first();
		var $label = $field.closest('.ng-farmley-otp-form__field').find('label').first();
		var $tabs = $form.find('.ng-farmley-otp-form__method-btn');

		if (!$tabs.length) {
			return;
		}

		$tabs.each(function () {
			var $btn = $(this);
			var active = $btn.data('loginMethod') === method;
			$btn.toggleClass('is-active', active).attr('aria-selected', active ? 'true' : 'false');
		});

		$field.attr('data-login-method', method);

		if (method === 'phone') {
			$field.attr('type', 'tel');
			$field.attr('inputmode', 'numeric');
			$field.attr('placeholder', '10-digit mobile number');
			$field.attr('autocomplete', 'tel');
			$label.text('Mobile number');
		} else {
			$field.attr('type', 'email');
			$field.removeAttr('inputmode');
			$field.attr('placeholder', 'Enter your email');
			$field.attr('autocomplete', 'username');
			$label.text('Email address');
		}
	}

	function bindOtpForm($form) {
		if (!$form.length || $form.data('ngOtpBound')) {
			return;
		}
		$form.data('ngOtpBound', true);

		if ($form.find('.ng-farmley-otp-form__method-btn').length) {
			setLoginMethod($form, 'email');
			$form.on('click.ngOtpMethod', '.ng-farmley-otp-form__method-btn', function (e) {
				e.preventDefault();
				if (isFormBusy($form)) {
					return;
				}
				setLoginMethod($form, $(this).data('loginMethod'));
				$form.find('.ng-farmley-otp-form__identifier').first().trigger('focus');
			});
		}

		var purpose = $form.data('purpose') || 'login';

		var formEl = $form.get(0);
		if (formEl) {
			formEl.addEventListener('submit', function (e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				if (isFormBusy($form)) {
					return;
				}
				verifyOtp($form, purpose);
			}, true);
		}

		$form.find('.ng-farmley-otp-form__send').on('click.ngOtp', function (e) {
			e.preventDefault();
			e.stopImmediatePropagation();
			if (isFormBusy($form) || ($form.data('ngOtpCooldownRemaining') || 0) > 0) {
				return;
			}
			sendOtp($form, purpose);
		});

		$form.on('input.ngOtpReset', '.ng-farmley-otp-form__identifier', function () {
			if ($form.data('ngOtpSent')) {
				resetOtpSendState($form);
			}
		});
	}

	function sendOtp($form, purpose) {
		if (!window.ngFarmleyOtp || isFormBusy($form)) {
			return;
		}

		var identifier = getFormIdentifier($form);
		if (!identifier) {
			showMessage($form, $form.find('.ng-farmley-otp-form__method-btn').length ? 'Enter your email or mobile number.' : 'Enter mobile number or email.', true);
			return;
		}

		var $send = $form.find('.ng-farmley-otp-form__send');
		setFormBusy($form, true, $send, ngFarmleyOtp.i18n.sending);
		showMessage($form, '', false);

		$.post(ngFarmleyOtp.ajaxUrl, {
			action: 'ng_farmley_otp_send',
			nonce: ngFarmleyOtp.nonce,
			purpose: purpose,
			identifier: identifier,
			name: $form.find('[name="name"]').val() || ''
		})
			.done(function (res) {
				if (res && res.success) {
					revealOtpStep($form);
					var msg = (res.test_hint) ? res.test_hint : ngFarmleyOtp.i18n.sent;
					showMessage($form, msg, false);
					startResendCooldown($form);
				} else {
					showMessage($form, (res && res.message) ? res.message : ngFarmleyOtp.i18n.error, true);
				}
			})
			.fail(function () {
				showMessage($form, ngFarmleyOtp.i18n.error, true);
			})
			.always(function () {
				setFormBusy($form, false);
			});
	}

	function verifyOtp($form, purpose) {
		if (!window.ngFarmleyOtp || isFormBusy($form)) {
			return;
		}

		var identifier = getFormIdentifier($form);
		var otp = $.trim($form.find('.ng-farmley-otp-form__otp').val());
		if (!identifier || !otp) {
			showMessage($form, 'Enter mobile/email and OTP.', true);
			return;
		}

		var $verify = $form.find('.ng-farmley-otp-form__verify');
		setFormBusy($form, true, $verify, ngFarmleyOtp.i18n.verifying);
		showMessage($form, '', false);

		$.post(ngFarmleyOtp.ajaxUrl, {
			action: 'ng_farmley_otp_verify',
			nonce: ngFarmleyOtp.nonce,
			purpose: purpose,
			identifier: identifier,
			otp: otp,
			remember: $form.find('[name="remember"]').is(':checked') ? 1 : 0
		})
			.done(function (res) {
				if (res && res.success) {
					$form.data('ngOtpRedirecting', true);
					showMessage($form, res.message || 'Success', false);
					if (res.redirect) {
						window.location.href = res.redirect;
					} else {
						window.location.reload();
					}
					return;
				}
				showMessage($form, (res && res.message) ? res.message : ngFarmleyOtp.i18n.error, true);
			})
			.fail(function () {
				showMessage($form, ngFarmleyOtp.i18n.error, true);
			})
			.always(function () {
				if (!$form.data('ngOtpRedirecting')) {
					setFormBusy($form, false);
				}
			});
	}

	function setAuthTab($root, tab) {
		if (!$root.length || $root.hasClass(BUSY_CLASS)) {
			return;
		}

		tab = tab === 'register' ? 'register' : 'login';

		$root.find('.ng-farmley-auth__tab').each(function () {
			var $btn = $(this);
			var active = $btn.data('tab') === tab;
			$btn.toggleClass('is-active', active).attr('aria-selected', active ? 'true' : 'false');
		});

		$root.find('.ng-farmley-auth__panel').each(function () {
			var $panel = $(this);
			var active = $panel.data('panel') === tab;
			$panel.toggleClass('is-active', active);
			if (active) {
				$panel.removeAttr('hidden');
			} else {
				$panel.attr('hidden', 'hidden');
			}
		});

		$root.find('.ng-farmley-auth__footer--login').prop('hidden', tab !== 'login');
		$root.find('.ng-farmley-auth__footer--register').prop('hidden', tab !== 'register');
	}

	function initAuthTabs() {
		$('.ng-farmley-auth--tabs').each(function () {
			var $root = $(this);
			if ($root.data('ngAuthTabs')) {
				return;
			}
			$root.data('ngAuthTabs', true);

			var defaultTab = $root.data('defaultTab') || 'login';
			setAuthTab($root, defaultTab);

			$root.on('click', '.ng-farmley-auth__tab, .ng-farmley-auth__switch', function (e) {
				e.preventDefault();
				if ($root.hasClass(BUSY_CLASS)) {
					return;
				}
				setAuthTab($root, $(this).data('tab'));
			});
		});
	}

	function init() {
		initAuthTabs();
		$('.ng-farmley-otp-form').each(function () {
			bindOtpForm($(this));
		});
	}

	$(document).ready(init);
	$(document).on('greenpath_membership_trigger_login_modal', init);
})(jQuery);