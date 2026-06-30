(function ($) {
	'use strict';

	function formatPrice(amount) {
		if (!amount || isNaN(amount)) {
			return '';
		}
		var sym = $('.woocommerce-Price-currencySymbol').first().text() || '₹';
		return sym + parseFloat(amount).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	function buildPriceHtml(offer, mrp) {
		if (mrp > offer && offer > 0 && mrp > 0) {
			return (
				'<ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(offer) + '</bdi></span></ins>' +
				'<del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(mrp) + '</bdi></span></del>'
			);
		}

		if (offer > 0) {
			return '<span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(offer) + '</bdi></span>';
		}

		return '';
	}

	function updateGalleryImage($gallery, img) {
		if (!img || !$gallery.length) {
			return;
		}

		var $active = $gallery.find('.ng-farmley-sp-gallery__stage-img.is-active').first();
		if ($active.length) {
			$active.attr('src', img).attr('srcset', '');
		}
	}

	function onSingleWeightSelect(e) {
		e.preventDefault();

		var $btn = $(this);
		var $wrap = $btn.closest('.ng-farmley-sp-weight');

		$wrap.find('.ng-farmley-sp-weight__btn')
			.removeClass('is-active')
			.attr('aria-selected', 'false');

		$btn.addClass('is-active').attr('aria-selected', 'true');

		var price = parseFloat($btn.data('price'));
		var mrp = parseFloat($btn.data('mrp'));
		var img = $btn.data('image');
		var $price = $('#qodef-woo-page.qodef--single .summary .price').first();

		if ($price.length) {
			$price.html(buildPriceHtml(price, mrp));
		}

		updateGalleryImage($('.ng-farmley-sp-gallery').first(), img);
	}

	function initProductTabs($wrapper) {
		if (!$wrapper.length) {
			return;
		}

		var $tabLinks = $wrapper.find('.wc-tabs li a[role="tab"], ul.tabs li a');
		var $panels = $wrapper.find('.wc-tab');

		if (!$tabLinks.length || !$panels.length) {
			return;
		}

		function activateTab($link, updateHash) {
			if (!$link || !$link.length) {
				return;
			}

			var targetId = ($link.attr('href') || '').split('#')[1];
			if (!targetId) {
				return;
			}

			$tabLinks.closest('li').removeClass('active');
			$link.closest('li').addClass('active');

			$tabLinks.attr('aria-selected', 'false').attr('tabindex', '-1');
			$link.attr('aria-selected', 'true').attr('tabindex', '0');

			$panels.removeClass('ng-farmley-tab--active').hide();
			$wrapper.find('#' + targetId).addClass('ng-farmley-tab--active').show();

			if (updateHash !== false && window.history && window.history.replaceState) {
				window.history.replaceState(null, '', '#' + targetId);
			}
		}

		$wrapper.on('click', '.wc-tabs li a, ul.tabs li a', function (e) {
			e.preventDefault();
			activateTab($(this));
		});

		var hash = window.location.hash;
		var $initial = hash ? $tabLinks.filter('[href="' + hash + '"]') : $();
		if (!$initial.length) {
			$initial = $tabLinks.first();
		}

		activateTab($initial, false);
		$wrapper.addClass('ng-farmley-tabs--ready');
	}

	function initGallery($gallery) {
		var $imgs = $gallery.find('.ng-farmley-sp-gallery__stage-img');
		var $thumbs = $gallery.find('.ng-farmley-sp-gallery__thumb');
		var $prev = $gallery.find('.ng-farmley-sp-gallery__nav--prev');
		var $next = $gallery.find('.ng-farmley-sp-gallery__nav--next');
		var count = $imgs.length;
		var current = 0;

		if (!count) {
			return;
		}

		function showIndex(idx) {
			current = ((idx % count) + count) % count;

			$imgs.removeClass('is-active').filter('[data-index="' + current + '"]').addClass('is-active');
			$thumbs
				.removeClass('is-active')
				.attr('aria-selected', 'false')
				.filter('[data-index="' + current + '"]')
				.addClass('is-active')
				.attr('aria-selected', 'true');
		}

		$thumbs.on('click', function () {
			showIndex($(this).data('index'));
		});

		$prev.on('click', function () {
			showIndex(current - 1);
		});

		$next.on('click', function () {
			showIndex(current + 1);
		});

		$gallery.attr('tabindex', '0').on('keydown', function (e) {
			if (e.key === 'ArrowLeft') {
				showIndex(current - 1);
			}
			if (e.key === 'ArrowRight') {
				showIndex(current + 1);
			}
		});
	}

	$(function () {
		$('.ng-farmley-sp-gallery').each(function () {
			initGallery($(this));
		});

		$('#qodef-woo-page.qodef--single .woocommerce-tabs, #qodef-woo-page.qodef--single .wc-tabs-wrapper').each(function () {
			initProductTabs($(this));
		});

		$(document).on('click', '.ng-farmley-sp-weight__btn', onSingleWeightSelect);
	});
})(jQuery);
