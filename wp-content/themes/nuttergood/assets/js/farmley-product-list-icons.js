/**
 * Product cards — quick view + wishlist stacked on image (eye above heart).
 */
(function ($) {
	'use strict';

	function ensureMediaActionsStack($media) {
		var $stack = $media.children('.ng-farmley-media-actions').first();

		if (!$stack.length) {
			$stack = $('<div class="ng-farmley-media-actions" aria-label="Product actions"></div>');
			$media.prepend($stack);
		}

		return $stack;
	}

	function relocateProductMediaIcons() {
		$('.qodef-woo-product-list .product').each(function () {
			var $product = $(this);
			var $media = $product.find('.qodef-e-media').first();

			if (!$media.length) {
				return;
			}

			var $stack = ensureMediaActionsStack($media);
			var $qv = $product.find('.qqvfw-quick-view-button-wrapper').first();
			var $wish = $product.find('.qwfw-add-to-wishlist-wrapper').first();
			var $badge = $product.find('.ng-farmley-card-badge').first();

			if ($qv.length && !$qv.closest('.ng-farmley-media-actions').length) {
				$stack.append($qv);
			}

			if ($wish.length && $('body').hasClass('logged-in') && !$wish.closest('.ng-farmley-media-actions').length) {
				$stack.append($wish);
			}

			if ($badge.length) {
				$media.append($badge);
			}

			if (!$stack.children().length) {
				$stack.remove();
			}

			$product.find('.qodef-action-holder, .ng-farmley-card-actions').each(function () {
				var $actions = $(this);
				var hasVisibleChild = $actions.children().filter(function () {
					var $child = $(this);
					return (
						$child.css('display') !== 'none' &&
						$child.is(':visible') &&
						!$child.hasClass('qqvfw-quick-view-button-wrapper') &&
						!$child.hasClass('qwfw-add-to-wishlist-wrapper')
					);
				}).length > 0;

				if (!hasVisibleChild) {
					$actions.hide();
				}
			});
		});
	}

	function normalizeNativeProductCards() {
		$('.ng-farmley-product-cards .qodef-woo-product-list > ul.products > li.product').each(function () {
			var $product = $(this);
			var $card = $product.find('> .qodef-e-inner').first();
			var $content = $card.find('> .qodef-e-content').first();
			var $foot = $content.find('> .ng-farmley-card-foot').first();

			if (!$card.length || !$content.length || $content.data('ngNativeCardReady')) {
				return;
			}

			if ($foot.length && $foot.find('> .ng-farmley-card-footer').length) {
				$card.addClass('ng-farmley-card');
				$content.data('ngNativeCardReady', true);
				return;
			}

			var $title = $content.find('> .qodef-woo-product-title').first();
			var $priceHolder = $content.find('> .qodef-e-price-holder').first();
			var $actions = $content.find('> .qodef-action-holder').first();
			var $buttonWrap = $actions.find('.ng-farmley-card-buttons').first();
			var $cartButton = ($buttonWrap.length ? $buttonWrap : $actions)
				.find('.add_to_cart_button, .button.product_type_variable, .button.product_type_grouped')
				.not('.ng-farmley-buy-now')
				.first();

			$card.addClass('ng-farmley-card');
			$content.addClass('ng-farmley-card-foot');

			if ($priceHolder.length) {
				$priceHolder.addClass('ng-farmley-card-price');

				$priceHolder.children('.ng-farmley-card-weight, .ng-farmley-product-weight').each(function () {
					var $weight = $(this);
					if ($title.length) {
						$weight.insertAfter($title);
					} else {
						$content.prepend($weight);
					}
				});
			}

			if ($priceHolder.length && $cartButton.length && !$content.find('> .ng-farmley-card-footer').length) {
				var $footer = $('<div class="ng-farmley-card-footer"></div>');
				var $priceCol = $('<div class="ng-farmley-card-footer__price"></div>');
				var $cartCol = $('<div class="ng-farmley-card-footer__cart"></div>');
				var $buttons = $buttonWrap.length ? $buttonWrap : $cartButton;

				$priceCol.append($priceHolder);
				$cartCol.append($buttons);

				var $readerText = $actions.find('.screen-reader-text').first();
				if ($readerText.length) {
					$cartCol.append($readerText);
				}

				$footer.append($priceCol, $cartCol);
				$content.append($footer);
			}

			$content.data('ngNativeCardReady', true);

			var $activeWeight = $content.find('.ng-farmley-card-weight__btn.is-active').first();
			if ($activeWeight && $activeWeight.length) {
				window.setTimeout(function () {
					$activeWeight.trigger('click');
				}, 0);
			}
		});
	}

	function refreshProductCards() {
		normalizeNativeProductCards();
		relocateProductMediaIcons();
		$(document.body).trigger('ng_farmley_product_icons_relocated');
	}

	$(document).ready(refreshProductCards);
	$(window).on('load', refreshProductCards);
	$(document).on('greenpath_trigger_get_new_posts farmley_shop_products_updated ng_farmley_products_loaded', refreshProductCards);
})(jQuery);
