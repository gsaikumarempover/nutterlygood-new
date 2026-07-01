(function () {
	'use strict';

	document.addEventListener('click', function (event) {
		var button = event.target.closest('[data-ng-shop-go-back]');
		if (!button) {
			return;
		}

		event.preventDefault();

		var fallback = button.getAttribute('data-fallback') || '/';
		if (window.history.length > 1) {
			window.history.back();
			return;
		}

		window.location.href = fallback;
	});
})();
