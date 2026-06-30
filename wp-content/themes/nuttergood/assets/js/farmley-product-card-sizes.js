/**
 * Product card weight badges — update price, discount, and image on selection.
 */
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
				'<div class="qodef-woo-product-price price">' +
				'<ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(offer) + '</bdi></span></ins>' +
				'<del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(mrp) + '</bdi></span></del>' +
				'</div>'
			);
		}

		if (offer > 0) {
			return (
				'<div class="qodef-woo-product-price price">' +
				'<span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(offer) + '</bdi></span>' +
				'</div>'
			);
		}

		return '';
	}

	function updateDiscountBadge($card, discount, index) {
		var $badge = $card.find('.ng-farmley-card-badge').first();
		discount = parseInt(discount, 10) || 0;

		if (discount > 0) {
			if (!$badge.length) {
				var $target = $card.find('.qodef-e-media-image').first();
				if (!$target.length) {
					$target = $card.find('.qodef-e-media').first();
				}
				$target.append('<span class="ng-farmley-card-badge"></span>');
				$badge = $card.find('.ng-farmley-card-badge').first();
			}
			$badge
				.attr('data-size-index', index)
				.text(discount + '% OFF')
				.show();
			return;
		}

		if ($badge.length) {
			$badge.hide();
		}
	}

	function updateCardImage($card, img) {
		if (!img) {
			return;
		}

		var $primary = $card.find('.ng-farmley-card-media__img--primary').first();
		if ($primary.length) {
			$primary.attr('src', img).attr('srcset', '');
			return;
		}

		$card.find('.qodef-e-media-image img').first().attr('src', img).attr('srcset', '');
	}

	function onWeightSelect(e) {
		e.preventDefault();
		e.stopPropagation();

		var $btn = $(this);
		var $foot = $btn.closest('.ng-farmley-card-foot');
		var $card = $btn.closest('.ng-farmley-card');

		$foot.find('.ng-farmley-card-weight__btn')
			.removeClass('is-active')
			.attr('aria-selected', 'false');

		$btn.addClass('is-active').attr('aria-selected', 'true');

		var price = parseFloat($btn.data('price'));
		var mrp = parseFloat($btn.data('mrp'));
		var discount = $btn.data('discount');
		var index = $btn.data('index');
		var img = $btn.data('image');

		var $priceHolder = $foot.find('.ng-farmley-card-price');
		$priceHolder.attr('data-size-index', index);
		$priceHolder.html(buildPriceHtml(price, mrp));

		updateDiscountBadge($card, discount, index);
		updateCardImage($card, img);
	}

	$(document).on('click', '.ng-farmley-card-weight__btn', onWeightSelect);
})(jQuery);