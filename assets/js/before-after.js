/**
 * Tools Adapter — Slider Avant/Après.
 */
(function () {
	'use strict';

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var handle = root.querySelector('[data-ba-handle]');
		var clip = root.querySelector('[data-ba-clip]');
		var vertical = root.classList.contains('ta-ba--vertical');
		var dragging = false;

		function setPosition(percent) {
			percent = Math.max(0, Math.min(100, percent));
			if (vertical) {
				clip.style.clipPath = 'polygon(0 0, 100% 0, 100% ' + percent + '%, 0 ' + percent + '%)';
				handle.style.top = percent + '%';
				handle.style.left = '';
			} else {
				clip.style.clipPath = 'polygon(0 0, ' + percent + '% 0, ' + percent + '% 100%, 0 100%)';
				handle.style.left = percent + '%';
				handle.style.top = '';
			}
		}

		function percentFromEvent(clientX, clientY) {
			var rect = root.getBoundingClientRect();
			if (vertical) {
				return ((clientY - rect.top) / rect.height) * 100;
			}
			return ((clientX - rect.left) / rect.width) * 100;
		}

		function onMove(e) {
			if (!dragging) {
				return;
			}
			var point = e.touches ? e.touches[0] : e;
			setPosition(percentFromEvent(point.clientX, point.clientY));
		}

		function stopDrag() {
			dragging = false;
		}

		handle.addEventListener('mousedown', function () {
			dragging = true;
		});
		handle.addEventListener('touchstart', function () {
			dragging = true;
		}, { passive: true });

		root.addEventListener('mousemove', onMove);
		root.addEventListener('touchmove', onMove, { passive: true });
		window.addEventListener('mouseup', stopDrag);
		window.addEventListener('touchend', stopDrag);

		root.addEventListener('click', function (e) {
			if (e.target.closest('[data-ba-handle]')) {
				return;
			}
			setPosition(percentFromEvent(e.clientX, e.clientY));
		});

		var initial = parseFloat(root.getAttribute('data-position'));
		setPosition(isNaN(initial) ? 50 : initial);
	}

	function boot() {
		document.querySelectorAll('[data-ta-before-after]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-before-after.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-before-after]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
