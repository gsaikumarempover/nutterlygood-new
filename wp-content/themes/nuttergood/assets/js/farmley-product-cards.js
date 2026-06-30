/**
 * Farmley-style product cards — tap to preview hover image on touch devices.
 */
(function ($) {
	'use strict';

	function isTouchLike() {
		return window.matchMedia('(hover: none), (pointer: coarse)').matches;
	}

	function initFarmleyCardMedia() {
		if (!isTouchLike()) {
			return;
		}

		$(document).on('click', '.ng-farmley-product-cards .product .qodef-e-media-image > a', function (e) {
			var $product = $(this).closest('.product');
			var $media = $product.find('.ng-farmley-card-media--has-hover').first();

			if (!$media.length) {
				return;
			}

			if (!$product.hasClass('ng-farmley-card-media--active')) {
				e.preventDefault();
				$product.addClass('ng-farmley-card-media--active');
				$('.ng-farmley-product-cards .product.ng-farmley-card-media--active')
					.not($product)
					.removeClass('ng-farmley-card-media--active');
			}
		});

		$(document).on('click', function (e) {
			if (!$(e.target).closest('.ng-farmley-product-cards .product').length) {
				$('.ng-farmley-product-cards .product').removeClass('ng-farmley-card-media--active');
			}
		});
	}

	function applyCarouselGap(swiper, gap) {
		if (!swiper || !swiper.params) {
			return;
		}

		swiper.params.spaceBetween = gap;
		swiper.params.slidesPerGroup = 1;

		if (swiper.params.breakpoints) {
			Object.keys(swiper.params.breakpoints).forEach(function (key) {
				swiper.params.breakpoints[key].spaceBetween = gap;
			});
		}

		swiper.update();

		if (typeof swiper.updateSlides === 'function') {
			swiper.updateSlides();
		}

		if (typeof swiper.updateSize === 'function') {
			swiper.updateSize();
		}
	}

	var mobileCarouselMq = window.matchMedia('(max-width: 767px)');
	var mobileCarouselBreakpointKeys = ['0', '681'];

	function storeBreakpointSlidesPerView(breakpoints, key) {
		if (!breakpoints[key]) {
			return;
		}

		if (breakpoints[key]._ngOrigSlidesPerView === undefined) {
			breakpoints[key]._ngOrigSlidesPerView = breakpoints[key].slidesPerView;
		}
	}

	function applyMobileSingleSlide(swiper) {
		if (!swiper || !swiper.params) {
			return;
		}

		var useSingle = mobileCarouselMq.matches;
		var breakpoints = swiper.params.breakpoints;

		if (breakpoints) {
			mobileCarouselBreakpointKeys.forEach(function (key) {
				storeBreakpointSlidesPerView(breakpoints, key);

				if (useSingle) {
					breakpoints[key].slidesPerView = 1;
				} else if (breakpoints[key]._ngOrigSlidesPerView !== undefined) {
					breakpoints[key].slidesPerView = breakpoints[key]._ngOrigSlidesPerView;
				}
			});
		}

		if (swiper.params._ngOrigSlidesPerView === undefined) {
			swiper.params._ngOrigSlidesPerView = swiper.params.slidesPerView;
		}

		if (swiper.params._ngOrigAutoHeight === undefined) {
			swiper.params._ngOrigAutoHeight = swiper.params.autoHeight;
		}

		swiper.params.slidesPerView = useSingle ? 1 : swiper.params._ngOrigSlidesPerView;
		swiper.params.slidesPerGroup = 1;
		swiper.params.autoHeight = useSingle ? true : swiper.params._ngOrigAutoHeight;

		swiper.update();

		if (typeof swiper.updateSlides === 'function') {
			swiper.updateSlides();
		}

		if (typeof swiper.updateSize === 'function') {
			swiper.updateSize();
		}

		if (useSingle && typeof swiper.updateAutoHeight === 'function') {
			swiper.updateAutoHeight(0);
		}
	}

	function refreshCarouselAutoHeight($holder) {
		if (!$holder || !$holder.length) {
			return;
		}

		var swiper = $holder[0].swiper;

		if (!swiper || !mobileCarouselMq.matches) {
			return;
		}

		if (typeof swiper.updateAutoHeight === 'function') {
			swiper.updateAutoHeight(0);
		}
	}

	function bindCarouselImageHeightRefresh($holder) {
		if (!$holder || !$holder.length || $holder.data('ngCarouselImageHeightBound')) {
			return;
		}

		if (!$holder.closest('.elementor-element-a463981').length) {
			return;
		}

		$holder.data('ngCarouselImageHeightBound', true);

		$holder.find('img').each(function () {
			var img = this;

			if (img.complete) {
				return;
			}

			$(img).one('load error', function () {
				refreshCarouselAutoHeight($holder);
			});
		});

		window.setTimeout(function () {
			refreshCarouselAutoHeight($holder);
		}, 120);
	}

	function configureFarmleyCarousel($holder, gap) {
		if (!$holder || !$holder.length) {
			return;
		}

		var swiper = $holder[0].swiper;

		if (!swiper) {
			return;
		}

		applyCarouselGap(swiper, gap);

		if ($holder.closest('.elementor-element-a463981').length > 0) {
			applyMobileSingleSlide(swiper);
			bindCarouselImageHeightRefresh($holder);
		}
	}

	function bindMobileCarouselResize() {
		if (bindMobileCarouselResize._bound) {
			return;
		}

		bindMobileCarouselResize._bound = true;

		mobileCarouselMq.addEventListener('change', function () {
			$('.ng-farmley-product-cards .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container').each(function () {
				var swiper = this.swiper;

				if (swiper) {
					applyMobileSingleSlide(swiper);
				}
			});
		});
	}

	function bindCarouselPauseOnHover($holder) {
		if (!$holder || !$holder.length) {
			return;
		}

		var swiper = $holder[0].swiper;

		if (!swiper || !swiper.autoplay) {
			return;
		}

		var $target = $holder.closest('.qodef-product-slider-holder');

		if (!$target.length) {
			$target = $holder;
		}

		if ($target.data('ngCarouselPauseBound')) {
			return;
		}

		$target.data('ngCarouselPauseBound', true);

		$target.on('mouseenter.ngCarouselPause', function () {
			if (swiper.autoplay && swiper.autoplay.running) {
				swiper.autoplay.stop();
				$target.data('ngCarouselPausedByHover', true);
			}
		});

		$target.on('mouseleave.ngCarouselPause', function () {
			if (!$target.data('ngCarouselPausedByHover')) {
				return;
			}

			$target.removeData('ngCarouselPausedByHover');

			if ($holder.hasClass('qodef--cart-adding')) {
				return;
			}

			if (swiper.autoplay && !swiper.autoplay.running) {
				swiper.autoplay.start();
			}
		});
	}

	function initCarouselPauseOnHover() {
		$('.ng-farmley-product-cards .qodef-woo-product-list.qodef-swiper-container').each(function () {
			bindCarouselPauseOnHover($(this));
		});
	}

	function tightenProductCarousels() {
		$('.ng-farmley-product-cards .qodef-woo-product-list.qodef-swiper-container').each(function () {
			var $holder = $(this);
			var isHomeFresh = $holder.closest('.elementor-element-a463981').length > 0;
			// Fresh carousel gaps come from CSS slide padding; avoid doubling with spaceBetween.
			var gap = isHomeFresh ? 0 : 20;

			configureFarmleyCarousel($holder, gap);
		});
	}

	function tightenProductCarouselsWithRetry(attempts) {
		tightenProductCarousels();

		if (attempts > 0) {
			window.setTimeout(function () {
				tightenProductCarouselsWithRetry(attempts - 1);
			}, 350);
		}
	}

	$(document).ready(function () {
		initFarmleyCardMedia();
		bindMobileCarouselResize();
		tightenProductCarouselsWithRetry(6);
		initCarouselPauseOnHover();
	});

	$(window).on('load', function () {
		tightenProductCarouselsWithRetry(4);
		initCarouselPauseOnHover();
	});
	$(document).on('greenpath_trigger_get_new_posts', function () {
		tightenProductCarouselsWithRetry(4);
		initCarouselPauseOnHover();
	});

	// Apply gaps after GreenPath initializes Swiper (fixes missing carousel spacing).
	$(document).on('greenpath_trigger_swiper_is_initialized', function (e, $holder) {
		if (!$holder || !$holder.length) {
			return;
		}

		if (!$holder.closest('.ng-farmley-product-cards').length) {
			return;
		}

		if (!$holder.hasClass('qodef-woo-product-list')) {
			return;
		}

		var isHomeFresh = $holder.closest('.elementor-element-a463981').length > 0;
		configureFarmleyCarousel($holder, isHomeFresh ? 0 : 20);
		bindCarouselPauseOnHover($holder);
		bindCarouselImageHeightRefresh($holder);
		bindMobileCarouselResize();
	});
})(jQuery);