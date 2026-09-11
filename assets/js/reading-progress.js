/**
 * Tools Adapter — Barre de progression de lecture.
 */
(function () {
	'use strict';

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var bar = root.querySelector('[data-progress-bar]');
		var containerSelector = root.getAttribute('data-container');
		var container = containerSelector ? document.querySelector(containerSelector) : null;

		function update() {
			var percent;
			if (container) {
				var rect = container.getBoundingClientRect();
				var total = rect.height - window.innerHeight;
				var scrolled = -rect.top;
				percent = total > 0 ? (scrolled / total) * 100 : 0;
			} else {
				var doc = document.documentElement;
				var totalHeight = doc.scrollHeight - doc.clientHeight;
				percent = totalHeight > 0 ? (doc.scrollTop / totalHeight) * 100 : 0;
			}
			percent = Math.max(0, Math.min(100, percent));
			bar.style.width = percent + '%';
			root.setAttribute('aria-valuenow', String(Math.round(percent)));
		}

		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	function boot() {
		document.querySelectorAll('[data-ta-reading-progress]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-reading-progress.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-reading-progress]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
