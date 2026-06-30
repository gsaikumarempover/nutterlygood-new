(function ($) {
	'use strict';

	var AUTOPLAY_DELAY = 4500;

	function initGoogleReviewsFeed() {
		$('.ng-farmley-greviews__feed-wrap').each(function () {
			var $wrap = $(this);
			if ($wrap.data('ngGreviewsInit')) {
				return;
			}
			$wrap.data('ngGreviewsInit', true);

			var $feed = $wrap.find('.ng-farmley-greviews__feed');
			var $prev = $wrap.find('.ng-farmley-greviews__nav--prev');
			var $next = $wrap.find('.ng-farmley-greviews__nav--next');
			var autoplayTimer = null;
			var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

			function getScrollAmount() {
				var $card = $feed.find('.ng-farmley-greviews__card').first();
				if (!$card.length) {
					return 0;
				}
				var gap = parseFloat($feed.css('column-gap') || $feed.css('gap')) || 18;
				return $card.outerWidth(true) + gap;
			}

			function canScroll() {
				var feed = $feed[0];
				return feed.scrollWidth - feed.clientWidth > 1;
			}

			function scrollByCards(direction) {
				var feed = $feed[0];
				var amount = getScrollAmount();
				if (!amount) {
					return;
				}

				var maxScroll = feed.scrollWidth - feed.clientWidth;
				if (direction > 0 && feed.scrollLeft >= maxScroll - 1) {
					feed.scrollLeft = 0;
					return;
				}
				if (direction < 0 && feed.scrollLeft <= 0) {
					feed.scrollLeft = maxScroll;
					return;
				}

				feed.scrollLeft += direction * amount;
			}

			function startAutoplay() {
				if (reducedMotion || autoplayTimer) {
					return;
				}
				autoplayTimer = window.setInterval(function () {
					scrollByCards(1);
				}, AUTOPLAY_DELAY);
			}

			function stopAutoplay() {
				if (autoplayTimer) {
					window.clearInterval(autoplayTimer);
					autoplayTimer = null;
				}
			}

			function pauseThenResume() {
				stopAutoplay();
				window.setTimeout(startAutoplay, AUTOPLAY_DELAY);
			}

			$prev.on('click', function () {
				scrollByCards(-1);
				pauseThenResume();
			});

			$next.on('click', function () {
				scrollByCards(1);
				pauseThenResume();
			});

			$wrap.on('mouseenter focusin', stopAutoplay);
			$wrap.on('mouseleave focusout', startAutoplay);

			if ($feed.find('.ng-farmley-greviews__card').length > 1 && canScroll()) {
				startAutoplay();
			}
		});
	}

	$(document).ready(initGoogleReviewsFeed);
	$(window).on('load', initGoogleReviewsFeed);
})(jQuery);
