(function () {
	'use strict';

	var loader = document.querySelector('.ng-page-loader');

	if (!loader) {
		return;
	}

	var canvas = loader.querySelector('.ng-page-loader__canvas');
	var framesBase = loader.getAttribute('data-frames-base') || '';
	var frameCount = parseInt(loader.getAttribute('data-frame-count') || '80', 10);
	var fps = parseInt(loader.getAttribute('data-fps') || '10', 10);
	var currentFrame = 0;
	var frameTimer = null;

	function padFrame(index) {
		return String(index).padStart(4, '0');
	}

	function hideLoader() {
		loader.classList.add('is-hidden');

		if (frameTimer) {
			window.clearInterval(frameTimer);
		}

		window.setTimeout(function () {
			if (loader && loader.parentNode) {
				loader.parentNode.removeChild(loader);
			}
		}, 650);
	}

	function drawFrame(images) {
		if (!canvas) {
			return;
		}

		var ctx = canvas.getContext('2d');
		var image = images[currentFrame];

		if (!ctx || !image || !image.complete || !image.naturalWidth) {
			return;
		}

		ctx.clearRect(0, 0, canvas.width, canvas.height);
		ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
	}

	function startFrameAnimation() {
		if (!canvas || !framesBase) {
			return;
		}

		var images = [];
		var i;

		canvas.width = parseInt(loader.getAttribute('data-canvas-width') || '1026', 10);
		canvas.height = parseInt(loader.getAttribute('data-canvas-height') || '636', 10);

		for (i = 0; i < frameCount; i += 1) {
			(function (index) {
				var image = new Image();
				image.decoding = 'async';
				image.src = framesBase + 'img_' + padFrame(index) + '.webp';
				image.onload = function () {
					if (index === currentFrame) {
						drawFrame(images);
					}
				};
				images[index] = image;
			})(i);
		}

		drawFrame(images);

		frameTimer = window.setInterval(function () {
			currentFrame = (currentFrame + 1) % frameCount;
			drawFrame(images);
		}, 1000 / fps);
	}

	startFrameAnimation();

	if (document.readyState === 'complete') {
		window.setTimeout(hideLoader, 350);
	} else {
		window.addEventListener('load', function () {
			window.setTimeout(hideLoader, 350);
		});
	}

	window.setTimeout(hideLoader, 5000);
})();
