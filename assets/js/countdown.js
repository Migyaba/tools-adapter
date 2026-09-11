/**
 * Tools Adapter — Compte à rebours promo.
 */
(function () {
	'use strict';

	function pad(n) {
		return String(n).padStart(2, '0');
	}

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var target = parseInt(root.getAttribute('data-target'), 10);
		var hideOnExpire = root.getAttribute('data-hide-on-expire') === '1';
		var expiredText = root.getAttribute('data-expired-text') || '';

		var daysEl = root.querySelector('[data-countdown-days]');
		var hoursEl = root.querySelector('[data-countdown-hours]');
		var minutesEl = root.querySelector('[data-countdown-minutes]');
		var secondsEl = root.querySelector('[data-countdown-seconds]');

		function tick() {
			var diff = target - Date.now();
			if (diff <= 0) {
				if (hideOnExpire) {
					root.style.display = 'none';
				} else {
					root.innerHTML = '<p class="ta-countdown__expired">' + expiredText + '</p>';
				}
				window.clearInterval(interval);
				return;
			}

			var totalSeconds = Math.floor(diff / 1000);
			var days = Math.floor(totalSeconds / 86400);
			var hours = Math.floor((totalSeconds % 86400) / 3600);
			var minutes = Math.floor((totalSeconds % 3600) / 60);
			var seconds = totalSeconds % 60;

			if (daysEl) {
				daysEl.textContent = pad(days);
			}
			if (hoursEl) {
				hoursEl.textContent = pad(hours);
			}
			if (minutesEl) {
				minutesEl.textContent = pad(minutes);
			}
			if (secondsEl) {
				secondsEl.textContent = pad(seconds);
			}
		}

		tick();
		var interval = window.setInterval(tick, 1000);
	}

	function boot() {
		document.querySelectorAll('[data-ta-countdown]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-sale-countdown.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-countdown]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
