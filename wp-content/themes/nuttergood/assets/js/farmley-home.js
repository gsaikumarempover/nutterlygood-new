(function () {
	'use strict';

	if (!document.body.classList.contains('home')) {
		return;
	}

	var sections = [
		'.elementor-element-47bd4b6',
		'.elementor-element-064fe67',
		'.elementor-element-76f78af',
		'.elementor-element-0f361fa',
		'.elementor-element-93632c8',
		'.elementor-element-ca3823c'
	];

	function markRevealTargets() {
		sections.forEach(function (sel) {
			document.querySelectorAll(sel).forEach(function (el) {
				el.classList.add('ng-farmley-home-reveal');
			});
		});
	}

	function initReveal() {
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			document.querySelectorAll('.ng-farmley-home-reveal').forEach(function (el) {
				el.classList.add('is-visible');
			});
			return;
		}

		if (!('IntersectionObserver' in window)) {
			document.querySelectorAll('.ng-farmley-home-reveal').forEach(function (el) {
				el.classList.add('is-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
		);

		document.querySelectorAll('.ng-farmley-home-reveal').forEach(function (el) {
			observer.observe(el);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			markRevealTargets();
			initReveal();
			initHeroSliderResize();
		});
	} else {
		markRevealTargets();
		initReveal();
		initHeroSliderResize();
	}

	function initHeroSliderResize() {
		var resizeTimer;

		function refreshHeroSlider() {
			window.dispatchEvent(new Event('resize'));

			if (window.revapi17 && typeof window.revapi17.revredraw === 'function') {
				window.revapi17.revredraw();
			}
		}

		window.addEventListener('orientationchange', function () {
			setTimeout(refreshHeroSlider, 350);
		});

		window.addEventListener('resize', function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(refreshHeroSlider, 200);
		});

		if (typeof jQuery !== 'undefined') {
			jQuery(document).on('revolution.slide.onloaded', function (event, revapi) {
				if (revapi && revapi[0] && revapi[0].id === 'rev_slider_17_1') {
					window.revapi17 = revapi[0];
					setTimeout(refreshHeroSlider, 100);
				}
			});
		}
	}
})();
