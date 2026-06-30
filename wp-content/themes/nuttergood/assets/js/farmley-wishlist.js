(function ($) {
	'use strict';

	var SCOPES =
		'.ng-farmley-sp-gallery__wishlist, ' +
		'.ng-farmley-media-actions, ' +
		'#qodef-woo-page .qodef-action-holder, ' +
		'#qodef-woo-page.qodef--single .related.products .qodef-action-holder, ' +
		'.qodef-woo-product-list .qodef-action-holder, ' +
		'.qodef-woo-shortcode .qodef-action-holder';

	function isInScope($el) {
		return $el && $el.length && $el.closest(SCOPES).length > 0;
	}

	function getShortcode($el) {
		if (!$el || !$el.length) {
			return $();
		}

		if ($el.hasClass('qwfw-shortcode')) {
			return $el;
		}

		return $el.closest('.qwfw-shortcode');
	}

	function setWishlistVisual($shortcode, isActive) {
		if (!$shortcode || !$shortcode.length || !isInScope($shortcode)) {
			return;
		}

		var $path = $shortcode.find('.qwfw-m-icon svg path').first();

		if (isActive) {
			$shortcode.addClass('ng-farmley-wishlist--active');
			$path.attr('fill', '#E53935').attr('stroke', '#E53935');
		} else {
			$shortcode.removeClass('ng-farmley-wishlist--active');
			$path.attr('fill', 'none').attr('stroke', 'currentColor');
		}
	}

	function playWishlistPop($shortcode) {
		if (!$shortcode || !$shortcode.length) {
			return;
		}

		$shortcode.addClass('ng-farmley-wishlist--pop');
		window.setTimeout(function () {
			$shortcode.removeClass('ng-farmley-wishlist--pop');
		}, 700);
	}

	function syncAllWishlists() {
		$(SCOPES)
			.find('.qwfw-add-to-wishlist.qwfw-shortcode, .qwfw-shortcode.qwfw-add-to-wishlist')
			.each(function () {
				var $btn = $(this);
				setWishlistVisual($btn, $btn.hasClass('qwfw--added') || $btn.hasClass('ng-farmley-wishlist--active'));
			});
	}

	function watchWishlistContainers() {
		if (!window.MutationObserver) {
			return;
		}

		var pending = false;

		var observer = new MutationObserver(function () {
			if (pending) {
				return;
			}

			pending = true;
			window.requestAnimationFrame(function () {
				pending = false;
				syncAllWishlists();
			});
		});

		$(SCOPES).each(function () {
			observer.observe(this, {
				childList: true,
				subtree: true,
				attributes: true,
				attributeFilter: ['class'],
			});
		});

		observer.observe(document.body, {
			childList: true,
			subtree: true,
		});
	}

	function initFarmleyWishlist() {
		syncAllWishlists();

		$(document.body).on('qode_wishlist_for_woocommerce_trigger_updated_wishlist_item', function (e, $button, itemID, action) {
			var $btn = $button && $button.jquery ? $button : $($button);
			var $shortcode = getShortcode($btn);

			if (!$shortcode.length && itemID) {
				$shortcode = $(SCOPES).find('.qwfw-add-to-wishlist[data-item-id="' + itemID + '"]');
			}

			if (!isInScope($shortcode)) {
				return;
			}

			if (action === 'add') {
				setWishlistVisual($shortcode, true);
				playWishlistPop($shortcode);
			} else if (action === 'remove') {
				setWishlistVisual($shortcode, false);
			}
		});

		$(document).on('click', SCOPES + ' .qwfw-add-to-wishlist', function () {
			var $shortcode = $(this);
			var attempts = 0;

			var poll = window.setInterval(function () {
				attempts += 1;

				if ($shortcode.hasClass('qwfw--added')) {
					setWishlistVisual($shortcode, true);
					playWishlistPop($shortcode);
					window.clearInterval(poll);
				} else if (attempts >= 30 || !$shortcode.hasClass('qwfw--loading')) {
					window.clearInterval(poll);
				}
			}, 100);
		});

		watchWishlistContainers();

		$(document).on('farmley_shop_products_updated ng_farmley_products_loaded ng_farmley_product_icons_relocated', syncAllWishlists);
	}

	$(initFarmleyWishlist);
})(jQuery);