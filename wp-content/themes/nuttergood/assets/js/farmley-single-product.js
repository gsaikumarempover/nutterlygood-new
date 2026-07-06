(function ($) {
	'use strict';

	function formatPrice(amount) {
		if (!amount || isNaN(amount)) {
			return '';
		}
		var sym = $('.woocommerce-Price-currencySymbol').first().text() || '₹';
		return sym + parseFloat(amount).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	function parseWeightGrams(raw) {
		var match = String(raw || '').match(/(\d+(?:\.\d+)?)\s*(kg|g|gm)\b/i);
		if (!match) {
			return 0;
		}

		var num = parseFloat(match[1]);
		var unit = match[2].toLowerCase();
		if (unit === 'kg') {
			return Math.round(num * 1000);
		}

		return Math.round(num);
	}

	function parseVolumeMl(raw) {
		var match = String(raw || '').match(/(\d+(?:\.\d+)?)\s*(l|ml)\b/i);
		if (!match) {
			return 0;
		}

		var num = parseFloat(match[1]);
		var unit = match[2].toLowerCase();
		if (unit === 'l') {
			return Math.round(num * 1000);
		}

		return Math.round(num);
	}

	function buildUspHtml(offer, weightLabel) {
		var unitLabel = '';
		var unitAmount = 0;
		var grams = parseWeightGrams(weightLabel);
		var ml = parseVolumeMl(weightLabel);

		if (grams > 0 && offer > 0) {
			unitAmount = Math.round((offer / grams) * 100 * 100) / 100;
			unitLabel = '100g';
		} else if (ml > 0 && offer > 0) {
			unitAmount = Math.round((offer / ml) * 100 * 100) / 100;
			unitLabel = '100ml';
		}

		if (!unitLabel || unitAmount <= 0) {
			return '';
		}

		return (
			'<span class="ng-farmley-sp-usp" aria-label="Unit selling price: ' + formatPrice(unitAmount) + ' per ' + unitLabel + '">' +
			'<span class="ng-farmley-sp-usp__label">USP:</span> ' +
			'<span class="ng-farmley-sp-usp__value">' + formatPrice(unitAmount) + '/' + unitLabel + '</span>' +
			'</span>'
		);
	}

	function buildPriceHtml(offer, mrp, weightLabel) {
		var html = '';

		if (mrp > offer && offer > 0 && mrp > 0) {
			html =
				'<ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(offer) + '</bdi></span></ins>' +
				'<del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(mrp) + '</bdi></span></del>';
		} else if (offer > 0) {
			html = '<span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(offer) + '</bdi></span>';
		}

		if (!html) {
			return '';
		}

		return html + buildUspHtml(offer, weightLabel);
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

	function applySingleWeightSelection($btn, updateActiveState) {
		if (!$btn || !$btn.length) {
			return;
		}

		var $wrap = $btn.closest('.ng-farmley-sp-weight');

		if (updateActiveState) {
			$wrap.find('.ng-farmley-sp-weight__btn')
				.removeClass('is-active')
				.attr('aria-selected', 'false');

			$btn.addClass('is-active').attr('aria-selected', 'true');
		}

		var price = parseFloat($btn.data('price'));
		var mrp = parseFloat($btn.data('mrp'));
		var img = $btn.data('image');
		var weightLabel = $btn.data('weight') || $btn.find('.ng-farmley-card-weight__text').text().trim();
		var $price = $('#qodef-woo-page.qodef--single .summary .price').first();

		if ($price.length) {
			$price.html(buildPriceHtml(price, mrp, weightLabel));
		}

		updateGalleryImage($('.ng-farmley-sp-gallery').first(), img);
	}

	function onSingleWeightSelect(e) {
		e.preventDefault();
		applySingleWeightSelection($(this), true);
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

	function readPriceAmount($price) {
		var $ins = $price.find('ins .woocommerce-Price-amount bdi').first();
		var $amt = $ins.length ? $ins : $price.find('.woocommerce-Price-amount bdi').first();
		return parseFloat(String($amt.text() || '').replace(/[^\d.]/g, '')) || 0;
	}

	function readMrpAmount($price) {
		var $del = $price.find('del .woocommerce-Price-amount bdi').first();
		return parseFloat(String($del.text() || '').replace(/[^\d.]/g, '')) || 0;
	}

	function extractWeightFromTitle() {
		var title = $('#qodef-woo-page.qodef--single .product_title').first().text() || '';
		var match = title.match(/(\d+(?:\.\d+)?)\s*(kg|g|gm|ml|l)\b/i);
		if (!match) {
			return '';
		}
		return match[1] + match[2];
	}

	function initUspOnLoad() {
		var $price = $('#qodef-woo-page.qodef--single .summary .price').first();
		if (!$price.length) {
			return;
		}

		if ($price.find('.ng-farmley-sp-usp').length) {
			return;
		}

		var $activeBtn = $('.ng-farmley-sp-weight__btn.is-active').first();
		var $badge = $('.ng-farmley-sp-weight__badge').first();
		var weightLabel = '';
		var offer = 0;
		var mrp = 0;

		if ($activeBtn.length) {
			applySingleWeightSelection($activeBtn, false);
			return;
		}

		if ($badge.length) {
			weightLabel = $badge.data('weight') || $badge.find('.ng-farmley-card-weight__text').text().trim();
			offer = parseFloat($badge.data('price'));
			mrp = parseFloat($badge.data('mrp'));
		}

		if (!weightLabel) {
			weightLabel = extractWeightFromTitle();
		}

		if (!offer) {
			offer = readPriceAmount($price);
		}

		if (!mrp) {
			mrp = readMrpAmount($price);
		}

		var html = buildPriceHtml(offer, mrp, weightLabel);
		if (html) {
			$price.html(html);
		}
	}

	$(function () {
		$('.ng-farmley-sp-gallery').each(function () {
			initGallery($(this));
		});

		$('#qodef-woo-page.qodef--single .woocommerce-tabs, #qodef-woo-page.qodef--single .wc-tabs-wrapper').each(function () {
			initProductTabs($(this));
		});

		$(document).on('click', '.ng-farmley-sp-weight__btn', onSingleWeightSelect);

		initUspOnLoad();
	});
})(jQuery);
