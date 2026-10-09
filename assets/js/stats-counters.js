/**
 * Tools Adapter — Compteurs animés (Stats Counters widget).
 * Anime chaque nombre de 0 à sa valeur cible quand le bloc entre dans le viewport.
 */
(function () {
	'use strict';

	function animateValue(el, target, duration, decimals) {
		var start = 0;
		var startTime = null;

		function step(timestamp) {
			if (!startTime) {
				startTime = timestamp;
			}
			var progress = Math.min((timestamp - startTime) / duration, 1);
			var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
			var value = start + (target - start) * eased;
			el.textContent = decimals > 0 ? value.toFixed(decimals) : Math.round(value).toLocaleString('fr-FR');
			if (progress < 1) {
				window.requestAnimationFrame(step);
			} else {
				el.textContent = decimals > 0 ? target.toFixed(decimals) : target.toLocaleString('fr-FR');
			}
		}

		window.requestAnimationFrame(step);
	}

	function initGroup(group) {
		if (group.dataset.taBound === '1') {
			return;
		}
		group.dataset.taBound = '1';

		var duration = parseInt(group.getAttribute('data-duration'), 10) || 2000;
		var numbers = group.querySelectorAll('[data-count-to]');

		if (!numbers.length) {
			return;
		}

		var run = function () {
			numbers.forEach(function (el) {
				var target = parseFloat(el.getAttribute('data-count-to')) || 0;
				var decimals = parseInt(el.getAttribute('data-decimals'), 10) || 0;
				animateValue(el, target, duration, decimals);
			});
		};

		if ('IntersectionObserver' in window) {
			var observer = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							run();
							observer.disconnect();
						}
					});
				},
				{ threshold: 0.3 }
			);
			observer.observe(group);
		} else {
			run();
		}
	}

	function boot() {
		document.querySelectorAll('[data-ta-stats]').forEach(initGroup);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			// Global hook: also covers other widgets reusing the counter
			// (e.g. the "Images superposées" badge).
			elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
				$scope[0].querySelectorAll('[data-ta-stats]').forEach(initGroup);
			});
		});
	}
})();
