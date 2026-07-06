(function ($) {
	'use strict';

	function getShopList() {
		return $('.qodef-woo-product-list.qodef-filter--advanced').first();
	}

	function enforceShopSidebarLayout() {
		var $list = getShopList();
		if (!$list.length || !$list.hasClass('qodef-filter-type--sidebar')) {
			return;
		}

		if (window.matchMedia('(min-width: 1025px)').matches) {
			$list.css({
				'grid-template-areas': 'none',
				'column-gap': '32px',
				'align-items': 'start'
			});
			$list.find('.qodef-filter-top-bar').css({
				'grid-area': 'auto',
				'grid-column': '1 / -1',
				'grid-row': '1'
			});
			$list.find('.qodef-filter-content').css({
				'grid-area': 'auto',
				'grid-column': '1 / span 3',
				'grid-row': '2'
			});
			$list.find('> ul.qodef-grid-inner').css({
				'grid-area': 'auto',
				'grid-column': '4 / -1',
				'grid-row': '2',
				width: '100%',
				'column-gap': '30px',
				'row-gap': '40px'
			});
			$list.find('.qodef-m-pagination').css({
				'grid-area': 'auto',
				'grid-column': '4 / -1',
				'grid-row': '3'
			});
		}
	}

	function ensureGridLoader($list) {
		if (!$list.find('.ng-shop-grid-loader').length) {
			$list.append(
				'<div class="ng-shop-grid-loader" aria-hidden="true" role="status">' +
					'<span class="ng-shop-grid-loader__spinner"></span>' +
				'</div>'
			);
		}
	}

	function positionGridLoader($list) {
		var $grid = $list.find('> ul.qodef-grid-inner');
		var $loader = $list.find('.ng-shop-grid-loader');

		if (!$grid.length || !$loader.length) {
			return;
		}

		var listOffset = $list.offset();
		var gridOffset = $grid.offset();

		$loader.css({
			top: gridOffset.top - listOffset.top,
			left: gridOffset.left - listOffset.left,
			width: $grid.outerWidth(),
			height: Math.max($grid.outerHeight(), 320)
		});
	}

	function showFilterLoader($list) {
		if (!$list.length) {
			return;
		}

		ensureGridLoader($list);
		positionGridLoader($list);
		$list.find('.ng-shop-grid-loader').addClass('is-active');
	}

	function hideFilterLoader($list) {
		if (!$list.length) {
			return;
		}

		$list.find('.ng-shop-grid-loader').removeClass('is-active');
	}

	function triggerFilter($list) {
		if (!$list.length) {
			return;
		}
		var $btn = $list.find('.qodef-e-price-filter .qodef-filter-button .qodef-button, .qodef-e-price-filter .qodef-filter-button a').first();
		if ($btn.length) {
			showFilterLoader($list);
			$btn.trigger('click');
		}
	}

	function syncPriceInputs($list) {
		var $min = $list.find('#min_price');
		var $max = $list.find('#max_price');
		if (!$min.length || !$max.length) {
			return;
		}
		$min.attr('value', $min.val());
		$max.attr('value', $max.val());
	}

	function patchCategoryFilter() {
		var $list = getShopList();
		if (!$list.length || $list.data('ngShopPatched')) {
			return;
		}
		$list.data('ngShopPatched', true);

		$list.on('change', 'input[name="qodef-product-category"]', function () {
			var $checked = $list.find('input[name="qodef-product-category"]:checked');
			$list.find('input[name="qodef-product-category"]').not($checked).prop('checked', false);
			showFilterLoader($list);
		});

		$list.on('change', '.qodef-e-checkbox input', function () {
			if ($(this).attr('name') !== 'qodef-product-category') {
				showFilterLoader($list);
			}
		});
	}

	function initPriceSlider() {
		var $list = getShopList();
		if (!$list.length) {
			return;
		}

		var $slider = $list.find('.qodef-price-slider');
		if ($slider.length && typeof $slider.slider === 'function' && !$slider.hasClass('ui-slider')) {
			var $min = $list.find('#min_price');
			var $max = $list.find('#max_price');
			var minVal = parseFloat($min.data('min')) || 0;
			var maxVal = parseFloat($max.data('max')) || 1000;
			var curMin = parseFloat($min.val()) || minVal;
			var curMax = parseFloat($max.val()) || maxVal;

			$slider.slider({
				range: true,
				min: minVal,
				max: maxVal,
				step: 10,
				values: [curMin, curMax],
				slide: function (event, ui) {
					$min.val(ui.values[0]).attr('value', ui.values[0]);
					$max.val(ui.values[1]).attr('value', ui.values[1]);
					$list.find('.qodef-e-price-filter .qodef--min').text(ui.values[0]);
					$list.find('.qodef-e-price-filter .qodef--max').text(ui.values[1]);
				},
				stop: function () {
					syncPriceInputs($list);
					triggerFilter($list);
				}
			});
		} else if ($slider.hasClass('ui-slider')) {
			$slider.off('slidestop.ngfarmley').on('slidestop.ngfarmley', function () {
				syncPriceInputs($list);
				triggerFilter($list);
			});
		}

		$list.find('.qodef-e-price-filter .qodef-filter-button .qodef-button, .qodef-e-price-filter .qodef-filter-button a')
			.off('click.ngfarmley')
			.on('click.ngfarmley', function () {
				syncPriceInputs($list);
				showFilterLoader($list);
			});
	}

	function buildDiscountToggle(active) {
		var $toggle = $(
			'<button type="button" class="ng-farmley-discount-toggle ng-farmley-shop-discount-filter" role="switch" aria-checked="false" aria-label="Show discounted products only">' +
				'<span class="ng-farmley-discount-toggle__label">Discount</span>' +
				'<span class="ng-farmley-discount-toggle__track" aria-hidden="true"><span class="ng-farmley-discount-toggle__thumb"></span></span>' +
			'</button>'
		);

		if (active) {
			$toggle.addClass('is-active').attr('aria-checked', 'true');
		}

		return $toggle;
	}

	function setDiscountToggleState($button, active) {
		$button.toggleClass('is-active', !!active).attr('aria-checked', active ? 'true' : 'false');
	}

	function syncSortDropdownLabel($list) {
		var options = $list.data('options') || {};
		var orderby = options.orderby || 'popularity';
		var $ordering = $list.find('.qodef-product-list-ordering').first();

		if (!$ordering.length) {
			return;
		}

		var $active = $ordering.find('.qodef-e-order-link[data-value="' + orderby + '"], .qodef-e-order-link.qodef--active').first();
		var label = $active.find('.qodef-e-label').text() || $active.text() || '';

		if (!label) {
			var labels = {
				popularity: 'Sort by popularity',
				'price-range-low': 'Sort by price: low to high',
				'price-range-high': 'Sort by price: high to low',
				price: 'Sort by price: low to high',
				'price-desc': 'Sort by price: high to low'
			};
			label = labels[orderby] || 'Sort by popularity';
		}

		$ordering.find('.qodef-current-value .qodef-e-text').text($.trim(label));
		$ordering.find('.qodef-e-order-link').removeClass('qodef--active').attr('aria-selected', 'false');
		$ordering.find('.qodef-e-order-link[data-value="' + orderby + '"]').addClass('qodef--active').attr('aria-selected', 'true');
	}

	function initSortDropdown() {
		var $list = getShopList();
		if (!$list.length) {
			return;
		}

		syncSortDropdownLabel($list);

		$list.off('click.ngFarmleySort').on('click.ngFarmleySort', '.qodef-e-order-link', function () {
			var $link = $(this);
			var value = $link.data('value');
			var label = $.trim($link.find('.qodef-e-label').text() || $link.text());

			if (value) {
				var options = $.extend({}, $list.data('options') || {});
				options.orderby = value;
				$list.data('options', options);
			}

			if (label) {
				$list.find('.qodef-product-list-ordering .qodef-current-value .qodef-e-text').text(label);
			}
		});
	}

	function initCatalogToolbar() {
		$('.qodef-woo-product-list .qodef-filter-top-bar .qodef-e-info-right').each(function () {
			var $right = $(this);

			if ($right.data('ngToolbarReady')) {
				return;
			}

			var $list = $right.closest('.qodef-woo-product-list');
			var options = $list.data('options') || {};
			var active = options.ng_discount === '1' || options.ng_discount === 1;
			var $toolbar = $right.children('.ng-farmley-catalog-toolbar.ng-farmley-catalog-toolbar--shop');

			if (!$toolbar.length) {
				$toolbar = $('<div class="ng-farmley-catalog-toolbar ng-farmley-catalog-toolbar--shop"></div>');
				var $ordering = $right.find('.qodef-product-list-ordering').first();

				if ($ordering.length) {
					$toolbar.append($ordering);
				}

				$right.append($toolbar);
			}

			if (!$toolbar.find('.ng-farmley-shop-discount-filter').length) {
				$toolbar.append(buildDiscountToggle(active));
			}

			$right.data('ngToolbarReady', true);
		});
	}

	function toggleArchiveDiscountFilter($button) {
		var url = new URL(window.location.href);

		if ($button.hasClass('is-active')) {
			url.searchParams.delete('ng_discount');
			setDiscountToggleState($button, false);
		} else {
			url.searchParams.set('ng_discount', '1');
			setDiscountToggleState($button, true);
		}

		url.searchParams.delete('paged');
		window.location.assign(url.toString());
	}

	function toggleShopDiscountFilter($button) {
		var $list = getShopList();
		if (!$list.length) {
			return;
		}

		var options = $.extend({}, $list.data('options') || {});
		var active = $button.hasClass('is-active');

		if (active) {
			delete options.ng_discount;
			setDiscountToggleState($button, false);
		} else {
			options.ng_discount = '1';
			setDiscountToggleState($button, true);
		}

		$list.data('options', options);
		showFilterLoader($list);
		$(document.body).trigger('greenpath_trigger_load_more', [$list, 1]);
	}

	$(document).on('click', '.ng-farmley-shop-discount-filter', function (e) {
		e.preventDefault();
		toggleShopDiscountFilter($(this));
	});

	$(document).on('click', '.ng-farmley-archive-discount-filter', function (e) {
		e.preventDefault();
		toggleArchiveDiscountFilter($(this));
	});

	$(document.body).on('added_to_cart', function (event, fragments, cartHash, $button) {
		if ($button && $button.closest('.ng-farmley-card-footer__cart, .ng-farmley-product-cards').length) {
			$button.removeClass('loading added');
			$button.siblings('.added_to_cart').remove();
		}
	});

	function cleanPopularCardButtons() {
		$('.qodef-e-widget-area .ng-farmley-popular-card__actions .button').each(function () {
			var $button = $(this);
			$button.find('.qodef-svg--button-icon').remove();
			$button.removeClass('qodef-button qodef-layout--filled');
			if ($button.find('.ng-farmley-popular-btn-icon').length) {
				return;
			}
			$button.children('.qodef-m-text').each(function () {
				var $wrap = $(this);
				if ($wrap.children('.ng-farmley-card-btn__inner').length) {
					$wrap.replaceWith($wrap.children());
				}
			});
		});
	}

	$(document).ready(function () {
		enforceShopSidebarLayout();
		patchCategoryFilter();
		initPriceSlider();
		initCatalogToolbar();
		initSortDropdown();
		cleanPopularCardButtons();
		setTimeout(cleanPopularCardButtons, 400);
	});

	$(window).on('load resize', function () {
		enforceShopSidebarLayout();
		var $list = getShopList();
		if ($list.find('.ng-shop-grid-loader.is-active').length) {
			positionGridLoader($list);
		}
		cleanPopularCardButtons();
	});

	$(document).on('greenpath_trigger_get_new_posts', function (e, $holder) {
		var $list = $holder && $holder.length ? $holder : getShopList();
		setTimeout(cleanPopularCardButtons, 50);
		setTimeout(function () {
			hideFilterLoader($list);
			enforceShopSidebarLayout();
			initPriceSlider();
			initCatalogToolbar();
			initSortDropdown();
			cleanPopularCardButtons();
		}, 350);
	});
})(jQuery);