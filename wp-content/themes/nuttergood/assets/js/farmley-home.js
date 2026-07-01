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

	function initHomeCategoryCards() {
		document.querySelectorAll('.elementor-element-bf23057 .product-category').forEach(function (card) {
			card.style.removeProperty('background-color');
			card.classList.add('ng-home-cat-card');
		});
	}

	function initMobileCategoryCarousel() {
		var media = window.matchMedia('(max-width: 767px)');
		var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
		var container = null;
		var track = null;

		function getNodes() {
			var root = document.querySelector('.home .elementor-element-bf23057 .qodef-woo-product-category-list.qodef-layout--columns');
			if (!root) {
				return null;
			}

			var inner = root.querySelector('.qodef-grid-inner');
			if (!inner) {
				return null;
			}

			return { container: root, track: inner };
		}

		function clearClones() {
			if (!track) {
				return;
			}

			track.querySelectorAll('.ng-home-cat-clone').forEach(function (node) {
				node.remove();
			});
		}

		function teardown() {
			clearClones();

			if (container) {
				container.classList.remove('ng-home-cat-marquee', 'ng-home-cat-marquee-paused');
				delete container.dataset.ngHomeCatMarqueeBound;
			}

			if (track) {
				track.classList.remove('ng-home-cat-marquee-track');
				track.style.transform = '';
			}

			container = null;
			track = null;
		}

		function bindPauseEvents() {
			if (!container || container.dataset.ngHomeCatMarqueeBound === '1') {
				return;
			}

			container.dataset.ngHomeCatMarqueeBound = '1';

			['mouseenter', 'touchstart', 'pointerdown'].forEach(function (eventName) {
				container.addEventListener(eventName, function () {
					container.classList.add('ng-home-cat-marquee-paused');
				}, { passive: true });
			});

			['mouseleave', 'touchend', 'touchcancel'].forEach(function (eventName) {
				container.addEventListener(eventName, function () {
					container.classList.remove('ng-home-cat-marquee-paused');
				}, { passive: true });
			});

		}

		function setup() {
			if (!media.matches || reducedMotion.matches) {
				teardown();
				return;
			}

			var nodes = getNodes();
			if (!nodes) {
				teardown();
				return;
			}

			if (container === nodes.container && track === nodes.track && track.querySelector('.ng-home-cat-clone')) {
				return;
			}

			teardown();

			container = nodes.container;
			track = nodes.track;

			Array.prototype.slice.call(track.children).forEach(function (item) {
				var clone = item.cloneNode(true);
				clone.classList.add('ng-home-cat-clone');
				clone.setAttribute('aria-hidden', 'true');
				track.appendChild(clone);
			});

			container.classList.add('ng-home-cat-marquee');
			track.classList.add('ng-home-cat-marquee-track');
			bindPauseEvents();
		}

		function onBreakpointChange() {
			setup();
		}

		if (typeof media.addEventListener === 'function') {
			media.addEventListener('change', onBreakpointChange);
		} else if (typeof media.addListener === 'function') {
			media.addListener(onBreakpointChange);
		}

		if (typeof reducedMotion.addEventListener === 'function') {
			reducedMotion.addEventListener('change', onBreakpointChange);
		} else if (typeof reducedMotion.addListener === 'function') {
			reducedMotion.addListener(onBreakpointChange);
		}

		setup();
	}

	initHomeCategoryCards();
	initMobileCategoryCarousel();
})();
