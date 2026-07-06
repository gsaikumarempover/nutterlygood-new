(function ($) {
	'use strict';

	var track = document.querySelector('.ng-farmley-promo__track');
	if (track) {
		track.addEventListener('mouseenter', function () {
			track.style.animationPlayState = 'paused';
		});
		track.addEventListener('mouseleave', function () {
			track.style.animationPlayState = 'running';
		});
	}

	function getWishlistCounter() {
		return $('.widget_greenpath_core_qode_wishlist .qodef-wishlist-count');
	}

	function setWishlistCount(count) {
		var $counter = getWishlistCounter();
		if (!$counter.length) {
			return;
		}

		var value = Math.max(0, parseInt(count, 10) || 0);
		$counter.text(value);
		$counter.toggleClass('ng-wishlist-count--empty', value === 0);
	}

	function refreshWishlistCount() {
		if (typeof ngFarmleyHeader === 'undefined' || !ngFarmleyHeader.ajaxUrl) {
			return;
		}

		$.ajax({
			url: ngFarmleyHeader.ajaxUrl,
			type: 'POST',
			data: { action: 'ng_farmley_wishlist_count' },
			dataType: 'json',
			success: function (response) {
				if (response && response.success && response.data) {
					setWishlistCount(response.data.count);
				}
			}
		});
	}

	function initFarmleyMainMenu() {
		var $menus = $('.qodef-header--standard-extended #qodef-page-header-inner .ng-farmley-header-menu-slot .qodef-extended-dropdown-menu');

		$menus.each(function () {
			var $menu = $(this);
			if ($menu.data('ngFarmleyMenuInit')) {
				return;
			}
			$menu.data('ngFarmleyMenuInit', true);
			$menu.addClass('ng-farmley-main-menu-wrap');

			var $opener = $menu.find('.qodef-extended-dropdown-opener').first();
			if (!$opener.length) {
				return;
			}

			$opener.attr({
				role: 'button',
				tabindex: '0',
				'aria-expanded': 'false',
				'aria-haspopup': 'true',
			});

			function setOpen(isOpen) {
				$menu.toggleClass('ng-farmley-menu--open', isOpen);
				$opener.attr('aria-expanded', isOpen ? 'true' : 'false');
			}

			function closeMenu() {
				setOpen(false);
			}

			function toggleMenu(event) {
				if (event) {
					event.preventDefault();
					event.stopPropagation();
				}
				setOpen(!$menu.hasClass('ng-farmley-menu--open'));
			}

			$opener.on('click', toggleMenu);
			$opener.on('keydown', function (event) {
				if (event.key === 'Enter' || event.key === ' ') {
					toggleMenu(event);
				}
				if (event.key === 'Escape') {
					closeMenu();
				}
			});

			$menu.on('mouseenter', function () {
				setOpen(true);
			});

			$menu.on('mouseleave', function () {
				closeMenu();
			});
		});

		$(document).on('click.ngFarmleyMainMenu', function (event) {
			if (!$(event.target).closest('.qodef-extended-dropdown-menu.ng-farmley-main-menu-wrap').length) {
				$('.qodef-extended-dropdown-menu.ng-farmley-main-menu-wrap').removeClass('ng-farmley-menu--open')
					.find('.qodef-extended-dropdown-opener')
					.attr('aria-expanded', 'false');
			}
		});

		$(document).on('keydown.ngFarmleyMainMenu', function (event) {
			if (event.key === 'Escape') {
				$('.qodef-extended-dropdown-menu.ng-farmley-main-menu-wrap').removeClass('ng-farmley-menu--open')
					.find('.qodef-extended-dropdown-opener')
					.attr('aria-expanded', 'false');
			}
		});
	}

	function fixWishlistLinks() {
		if (typeof ngFarmleyHeader === 'undefined' || !ngFarmleyHeader.wishlistUrl) {
			return;
		}

		$('.widget_greenpath_core_qode_wishlist .qodef-wishlist-widget-link').each(function () {
			var $link = $(this);
			var href = $link.attr('href') || '';
			if (!href || href === window.location.origin + '/' || href === '/') {
				$link.attr('href', ngFarmleyHeader.wishlistUrl);
			}
		});
	}

	$(function () {
		fixWishlistLinks();

		var $counter = getWishlistCounter();
		if ($counter.length) {
			setWishlistCount($counter.text());
		}

		initFarmleyMainMenu();
		initFarmleyMobileMenuDrawer();

		$(document.body).on(
			'qode_wishlist_for_woocommerce_trigger_updated_wishlist_item qode_wishlist_for_woocommerce_trigger_wishlist_table_updated',
			refreshWishlistCount
		);
	});

	function destroyPerfectScrollbar($holder) {
		if (!$holder || !$holder.length) {
			return;
		}

		$holder.each(function () {
			if (this._ps && typeof this._ps.destroy === 'function') {
				this._ps.destroy();
				this._ps = null;
			}
		});

		$holder.removeClass('ps ps--active-y ps--active-x');
		$holder.find('.ps__rail-x, .ps__rail-y').remove();
	}

	function initFarmleyMobileMenuDrawer() {
		var $drawer = $('#qodef-side-area-mobile-header');
		if (!$drawer.length) {
			return;
		}

		$drawer.addClass('ng-farmley-mobile-drawer');

		if (!$drawer.data('ngFarmleyDetached')) {
			$('body').append($drawer);
			$drawer.data('ngFarmleyDetached', true);
		}

		function normalizeDrawer() {
			destroyPerfectScrollbar($drawer);
			$drawer.css({
				overflow: 'hidden',
				zIndex: $drawer.hasClass('qodef--opened') ? 100200 : ''
			});
		}

		normalizeDrawer();
		setTimeout(normalizeDrawer, 0);
		setTimeout(normalizeDrawer, 400);

		$(document).on('click', '.qodef-side-area-mobile-header-opener', function () {
			setTimeout(normalizeDrawer, 0);
		});

		$(document).on('click', '#qodef-side-area-mobile-header .qodef-m-close', function () {
			setTimeout(normalizeDrawer, 0);
		});

		$(document).on('click', '.qodef-woo-side-area-menu-cover', function () {
			setTimeout(normalizeDrawer, 0);
		});
	}
}(jQuery));