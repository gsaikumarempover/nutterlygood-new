(function ($) {
	'use strict';

	var DRAWER = '#qode-quick-view-for-woocommerce-pop-up.ng-farmley-qv-drawer';

	function getDrawer() {
		return $(DRAWER);
	}

	function formatPrice(amount) {
		if (!amount || isNaN(amount)) return '';
		var sym = $('.woocommerce-Price-currencySymbol').first().text() || '₹';
		return sym + parseFloat(amount).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	function initGallery($panel) {
		var $gallery = $panel.find('.ng-farmley-qv__gallery');
		var $stage = $panel.find('.ng-farmley-qv__stage');
		var $imgs = $stage.find('.ng-farmley-qv__stage-img');
		var $thumbs = $panel.find('.ng-farmley-qv__thumb');
		var count = $imgs.length;

		function showIndex(idx) {
			if (!count) {
				return;
			}
			if (idx < 0) {
				idx = count - 1;
			}
			if (idx >= count) {
				idx = 0;
			}
			$imgs.removeClass('is-active').filter('[data-index="' + idx + '"]').addClass('is-active');
			$thumbs.removeClass('is-active').filter('[data-index="' + idx + '"]').addClass('is-active');
			$gallery.find('.ng-farmley-qv__gallery-nav-btn--prev').prop('disabled', count < 2);
			$gallery.find('.ng-farmley-qv__gallery-nav-btn--next').prop('disabled', count < 2);
		}

		$thumbs.off('click.ngFv').on('click.ngFv', function () {
			showIndex(parseInt($(this).data('index'), 10) || 0);
		});

		if (count > 1 && !$gallery.find('.ng-farmley-qv__gallery-nav').length) {
			$gallery.append(
				'<div class="ng-farmley-qv__gallery-nav">' +
				'<button type="button" class="ng-farmley-qv__gallery-nav-btn ng-farmley-qv__gallery-nav-btn--prev" aria-label="Previous image">&lsaquo;</button>' +
				'<button type="button" class="ng-farmley-qv__gallery-nav-btn ng-farmley-qv__gallery-nav-btn--next" aria-label="Next image">&rsaquo;</button>' +
				'</div>'
			);
			$gallery.find('.ng-farmley-qv__gallery-nav-btn--prev').on('click.ngFv', function () {
				var current = parseInt($imgs.filter('.is-active').data('index'), 10) || 0;
				showIndex(current - 1);
			});
			$gallery.find('.ng-farmley-qv__gallery-nav-btn--next').on('click.ngFv', function () {
				var current = parseInt($imgs.filter('.is-active').data('index'), 10) || 0;
				showIndex(current + 1);
			});
		}

		showIndex(parseInt($imgs.filter('.is-active').data('index'), 10) || 0);
	}

	function setBodyLock(locked) {
		document.body.classList.toggle('ng-farmley-qv-open', !!locked);
	}

	function watchDrawerState($holder) {
		if (!$holder.length || $holder.data('ngFvWatch')) {
			return;
		}
		$holder.data('ngFvWatch', true);

		var observer = new MutationObserver(function () {
			if ($holder.hasClass('qqvfw--opened')) {
				setBodyLock(true);
			} else {
				setBodyLock(false);
				$holder.removeClass('ng-farmley-qv--closing ng-farmley-qv--ready');
			}
		});

		observer.observe($holder[0], { attributes: true, attributeFilter: ['class'] });
	}

	function bindCloseAnimation($holder) {
		$holder.find('.qqvfw-m-close').off('click.ngFvAnim').on('click.ngFvAnim', function () {
			$holder.removeClass('ng-farmley-qv--ready').addClass('ng-farmley-qv--closing');
		});
	}

	function initSizeOptions($panel) {
		var $btns = $panel.find('.ng-farmley-qv__size-btn');
		if (!$btns.length) return;

		var $sale = $panel.find('.ng-farmley-qv__price-sale');
		var $regular = $panel.find('.ng-farmley-qv__price-regular');
		var $metaOffer = $panel.find('.ng-farmley-qv__meta-row').filter(function () {
			return $(this).find('dt').text().indexOf('Offer') !== -1;
		}).find('dd');
		var $metaMrp = $panel.find('.ng-farmley-qv__meta-row').filter(function () {
			return $(this).find('dt').text().indexOf('MRP') !== -1;
		}).find('dd');

		$btns.off('click.ngFv').on('click.ngFv', function () {
			var $btn = $(this);
			$btns.removeClass('is-active').attr('aria-selected', 'false');
			$btn.addClass('is-active').attr('aria-selected', 'true');

			var price = $btn.data('price');
			var regular = $btn.data('regular');
			var mrp = $btn.data('mrp');
			var img = $btn.data('image');

			if (price) {
				$sale.html('<span class="woocommerce-Price-amount amount"><bdi>' + formatPrice(price) + '</bdi></span>');
			}
			if (regular && parseFloat(regular) > parseFloat(price)) {
				if (!$regular.length) {
					$panel.find('.ng-farmley-qv__price-values').prepend('<del class="ng-farmley-qv__price-regular"></del>');
					$regular = $panel.find('.ng-farmley-qv__price-regular');
				}
				$regular.html(formatPrice(regular)).show();
			}
			if (mrp && $metaMrp.length) {
				$metaMrp.text(formatPrice(mrp));
			}
			if (price && $metaOffer.length) {
				$metaOffer.text(formatPrice(price));
			}
			if (img) {
				var $active = $panel.find('.ng-farmley-qv__stage-img.is-active');
				if ($active.length) {
					$active.attr('src', img);
				}
			}
		});
	}

	function initVariablePills($panel) {
		$panel.find('.variations tr').each(function () {
			var $row = $(this);
			var $select = $row.find('select');
			if (!$select.length || $row.find('.ng-farmley-qv__size-options').length) return;

			var $wrap = $('<div class="ng-farmley-qv__size-options" role="listbox"></div>');

			$select.find('option').each(function () {
				var $opt = $(this);
				var val = $opt.val();
				if (!val) return;

				var $btn = $('<button type="button" class="ng-farmley-qv__size-btn" role="option"></button>');
				$btn.append('<span class="ng-farmley-qv__size-text"></span>');
				$btn.find('.ng-farmley-qv__size-text').text($opt.text());
				$btn.attr('data-value', val);
				if ($opt.is(':selected')) $btn.addClass('is-active');

				$btn.on('click', function () {
					$select.val(val).trigger('change');
					$wrap.find('.ng-farmley-qv__size-btn').removeClass('is-active');
					$btn.addClass('is-active');
				});
				$wrap.append($btn);
			});

			$select.hide();
			$row.find('.value').append($wrap);
		});

		var $form = $panel.find('.variations_form');
		if ($form.length) {
			$form.on('found_variation', function (e, variation) {
				if (variation && variation.image && variation.image.src) {
					$panel.find('.ng-farmley-qv__stage-img.is-active').attr('src', variation.image.src);
				}
				if (variation && variation.price_html) {
					$panel.find('.ng-farmley-qv__price-values').html(variation.price_html);
				}
			});
		}
	}

	function bindOverlayClose($drawer) {
		$drawer.find('.qqvfw-m-overlay').off('click.ngFv').on('click.ngFv', function () {
			$drawer.find('.qqvfw-m-close').trigger('click');
		});
	}

	function destroyPluginScrollbar($holder) {
		$holder.find('.summary, .qqvfw-m-product > .product, .qqvfw-m-media-wrapper, .ng-farmley-qv').each(function () {
			var el = this;

			if (typeof PerfectScrollbar !== 'undefined' && el._ps instanceof PerfectScrollbar) {
				try {
					el._ps.destroy();
				} catch (err) {
					/* ignore */
				}
				el._ps = null;
			}

			el.classList.remove('ps', 'qqvfw-ps');
			el.style.overflow = '';
			el.style.height = '';
			el.style.maxHeight = '';
			el.style.position = '';
		});

		$holder.find('.ps__rail-x, .ps__rail-y').remove();
	}

	function initDrawerScroll($holder) {
		var $scroll = $holder.find('.qqvfw-m-content-inner');
		if (!$scroll.length) {
			return;
		}

		destroyPluginScrollbar($holder);

		$holder.find('.ng-farmley-qv, .qqvfw-m-media-wrapper, .qqvfw-m-product > .product').each(function () {
			this.style.height = 'auto';
			this.style.maxHeight = 'none';
			this.style.overflow = 'visible';
		});

		$scroll.each(function () {
			this.style.overflowY = 'auto';
			this.style.overflowX = 'hidden';
			this.style.webkitOverflowScrolling = 'touch';
			this.style.overscrollBehavior = 'contain';
			this.style.touchAction = 'pan-y';
		});

		$scroll.off('wheel.ngFv').on('wheel.ngFv', function (e) {
			var el = this;
			var oe = e.originalEvent;
			if (!oe) {
				return;
			}
			var delta = oe.deltaY;
			var maxScroll = el.scrollHeight - el.clientHeight;
			if (maxScroll <= 0) {
				return;
			}
			var atTop = el.scrollTop <= 0;
			var atBottom = el.scrollTop >= maxScroll - 1;
			if ((delta < 0 && !atTop) || (delta > 0 && !atBottom)) {
				e.stopPropagation();
			}
		});
	}

	function patchPluginScrollbar($holder) {
		if (!$holder || !$holder.length || !window.qodeQuickViewForWooCommerce) {
			return;
		}

		var psApi = window.qodeQuickViewForWooCommerce.qodeQuickViewForWooCommercePerfectScrollbar;
		if (!psApi || psApi._ngFarmleyPatched) {
			return;
		}

		var originalInit = psApi.init;
		psApi.init = function ($target) {
			if ($target && $target.length && $target.closest(DRAWER).length) {
				destroyPluginScrollbar(getDrawer());
				initDrawerScroll(getDrawer());
				return;
			}
			return originalInit.call(this, $target);
		};
		psApi._ngFarmleyPatched = true;
	}

	function initQuantity($panel) {
		$panel.find('.ng-farmley-qv__qty-control').each(function () {
			var $wrap = $(this);
			var $input = $wrap.find('.ng-farmley-qv__qty-input');
			if (!$input.length || $wrap.data('ngFvQty')) {
				return;
			}
			$wrap.data('ngFvQty', true);

			var min = parseInt($input.attr('min'), 10) || 1;
			var max = parseInt($input.attr('max'), 10) || 0;

			function clamp(val) {
				val = parseInt(val, 10);
				if (isNaN(val) || val < min) {
					val = min;
				}
				if (max > 0 && val > max) {
					val = max;
				}
				return val;
			}

			$wrap.find('.ng-farmley-qv__qty-btn--minus').off('click.ngFv').on('click.ngFv', function () {
				$input.val(clamp((parseInt($input.val(), 10) || min) - 1)).trigger('change');
			});

			$wrap.find('.ng-farmley-qv__qty-btn--plus').off('click.ngFv').on('click.ngFv', function () {
				$input.val(clamp((parseInt($input.val(), 10) || min) + 1)).trigger('change');
			});

			$input.off('change.ngFv blur.ngFv').on('change.ngFv blur.ngFv', function () {
				$input.val(clamp($input.val()));
			});
		});
	}

	function initBuyNow($panel) {
		$panel.find('.ng-farmley-qv__buy-now').off('click.ngFv').on('click.ngFv', function (e) {
			var $form = $panel.find('.ng-farmley-qv__cart');
			if (!$form.length) {
				return;
			}

			if (window.ngFarmleyCartDrawer && ngFarmleyCartDrawer.enabled) {
				return;
			}

			e.preventDefault();

			var qty = $form.find('.ng-farmley-qv__qty-input, input.qty').val() || 1;
			var productId = $panel.data('product-id');
			var $button = $(this);

			if (typeof wc_add_to_cart_params === 'undefined' || !productId) {
				var checkout = (window.wc_add_to_cart_params && window.wc_add_to_cart_params.checkout_url)
					? window.wc_add_to_cart_params.checkout_url
					: '/checkout/';
				window.location = checkout + (checkout.indexOf('?') > -1 ? '&' : '?') + 'add-to-cart=' + productId + '&quantity=' + qty;
				return;
			}

			$button.addClass('loading');

			$.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
				data: { product_id: productId, quantity: qty },
				dataType: 'json',
				success: function (response) {
					if (response && response.error && response.product_url) {
						window.location = response.product_url;
						return;
					}

					var checkoutUrl = wc_add_to_cart_params.checkout_url
						|| (wc_add_to_cart_params.cart_url ? wc_add_to_cart_params.cart_url.replace(/\/cart\/?$/, '/checkout/') : '/checkout/');
					window.location.href = checkoutUrl;
				},
				complete: function () {
					$button.removeClass('loading');
				}
			});
		});
	}

	function stripReviewBlocks($panel) {
		$panel.find(
			'#o_product_page_reviews, .o_shop_discussion_rating, .o_product_page_reviews_link, .o_website_rating_static, #reviews, .woocommerce-Reviews, .commentlist, section[data-snippet="s_dynamic_snippet_products"], #oe_structure_website_sale_recommended_products'
		).remove();
	}

	function enhanceDrawer($holder) {
		if (!$holder || !$holder.length) {
			$holder = getDrawer();
		}
		if (!$holder.length || !$holder.is(DRAWER)) return;

		var $panel = $holder.find('.ng-farmley-qv');
		if (!$panel.length) return;

		stripReviewBlocks($panel);
		initDrawerScroll($holder);

		$holder.removeClass('ng-farmley-qv--ready');
		window.requestAnimationFrame(function () {
			window.requestAnimationFrame(function () {
				$holder.addClass('ng-farmley-qv--ready');
			});
		});

		initGallery($panel);
		initSizeOptions($panel);
		initVariablePills($panel);
		initQuantity($panel);
		initBuyNow($panel);
		bindOverlayClose($holder);
		bindCloseAnimation($holder);
		watchDrawerState($holder);
	}

	function scheduleEnhance($holder) {
		var target = $holder && $holder.length ? $holder : getDrawer();
		patchPluginScrollbar(target);
		enhanceDrawer(target);
		[0, 80, 200, 450].forEach(function (delay) {
			setTimeout(function () {
				enhanceDrawer(target);
				initDrawerScroll(target);
			}, delay);
		});
	}

	$(document.body).on('qode_quick_view_for_woocommerce_trigger_quick_view', function (e, $holder) {
		scheduleEnhance($holder || getDrawer());
	});

	$(window).on('resize.ngFv', function () {
		var $d = getDrawer();
		if ($d.hasClass('qqvfw--opened')) {
			initDrawerScroll($d);
		}
	});

	$(document).on('keydown.ngFv', function (e) {
		if (e.key === 'Escape') {
			var $d = getDrawer();
			if ($d.hasClass('qqvfw--opened')) {
				$d.find('.qqvfw-m-close').trigger('click');
			}
		}
	});

	$(document).ready(function () {
		var $drawer = getDrawer();
		patchPluginScrollbar($drawer);
		bindOverlayClose($drawer);
		watchDrawerState($drawer);
		bindCloseAnimation($drawer);
	});
})(jQuery);