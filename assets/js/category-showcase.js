/**
 * Tools Adapter — Vitrine catégories : flèches du carrousel.
 */
(function () {
	'use strict';

	function init(root) {
		if (root.dataset.taCatshowBound) {
			return;
		}
		root.dataset.taCatshowBound = '1';

		var track = root.querySelector('.ta-catshow__track');
		var buttons = root.querySelectorAll('.ta-catshow__arrow-btn');
		if (!track || !buttons.length) {
			return;
		}

		function update() {
			var max = track.scrollWidth - track.clientWidth - 2;
			buttons[0].disabled = track.scrollLeft <= 2;
			buttons[1].disabled = track.scrollLeft >= max;
		}

		buttons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var item = track.querySelector('.ta-catshow__item');
				var step = item ? item.getBoundingClientRect().width : track.clientWidth;
				track.scrollBy({ left: step * parseInt(btn.getAttribute('data-dir'), 10), behavior: 'smooth' });
			});
		});

		track.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	function boot() {
		document.querySelectorAll('[data-ta-catshow-carousel]').forEach(init);
	}

	if (document.readyState !== 'loading') {
		boot();
	} else {
		document.addEventListener('DOMContentLoaded', boot);
	}

	// Elementor editor preview.
	window.addEventListener('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-category-showcase.default', function ($scope) {
				var el = $scope[0].querySelector('[data-ta-catshow-carousel]');
				if (el) {
					init(el);
				}
			});
		}
	});
})();
