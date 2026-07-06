(function ($) {
	'use strict';

	function setActiveFilter($buttons, slug) {
		$buttons.each(function () {
			var $btn = $(this);
			var active = $btn.data('ngBlogFilter') === slug;
			$btn.toggleClass('is-active', active).attr('aria-selected', active ? 'true' : 'false');
		});
	}

	function applyBlogFilter(slug) {
		var $cards = $('.ng-farmley-blog .qodef-blog-item');

		if (!$cards.length) {
			return;
		}

		$cards.each(function () {
			var $card = $(this);
			if ('all' === slug) {
				$card.removeClass('ng-farmley-blog-card--hidden');
				return;
			}

			var match = $card.hasClass('ng-farmley-blog-card--' + slug);
			$card.toggleClass('ng-farmley-blog-card--hidden', !match);
		});
	}

	$(function () {
		var $filters = $('[data-ng-blog-filter]');
		if (!$filters.length) {
			return;
		}

		$filters.on('click', function () {
			var slug = $(this).data('ngBlogFilter');
			setActiveFilter($('.ng-farmley-blog-filter, .ng-farmley-blog-sidebar__tag'), slug);
			applyBlogFilter(slug);
		});
	});
})(jQuery);