/**
 * Tools Adapter — Slider Avant/Après.
 *
 * La position est stockée dans la variable CSS --ta-ba-pos du conteneur ;
 * le CSS s'en sert pour découper l'image « Avant » et placer le curseur.
 * Pointer Events + capture : le glisser continue même hors de l'image, et
 * touch-action (pan-y / pan-x) laisse la page défiler dans l'autre sens.
 */
(function () {
	'use strict';

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var handle = root.querySelector('[data-ba-handle]');
		var vertical = root.classList.contains('ta-ba--vertical');
		var follow = root.classList.contains('ta-ba--follow');
		var dragging = false;
		var current = parseFloat(root.getAttribute('data-position'));

		if (isNaN(current)) {
			current = 50;
		}

		function setPosition(percent) {
			current = Math.max(0, Math.min(100, percent));
			root.style.setProperty('--ta-ba-pos', current + '%');
			if (handle) {
				var rounded = Math.round(current);
				handle.setAttribute('aria-valuenow', rounded);
				handle.setAttribute('aria-valuetext', rounded + ' %');
			}
		}

		function percentFromEvent(e) {
			var rect = root.getBoundingClientRect();
			if (vertical) {
				return ((e.clientY - rect.top) / rect.height) * 100;
			}
			return ((e.clientX - rect.left) / rect.width) * 100;
		}

		root.addEventListener('pointerdown', function (e) {
			if (e.pointerType === 'mouse' && e.button !== 0) {
				return;
			}
			dragging = true;
			root.classList.add('is-dragging');
			if (root.setPointerCapture) {
				root.setPointerCapture(e.pointerId);
			}
			// Touch: wait for a move, so starting a page scroll on the image
			// does not make the comparison jump.
			if (e.pointerType !== 'touch') {
				setPosition(percentFromEvent(e));
			}
		});

		root.addEventListener('pointermove', function (e) {
			if (dragging || (follow && e.pointerType === 'mouse')) {
				setPosition(percentFromEvent(e));
			}
		});

		function stopDrag() {
			dragging = false;
			root.classList.remove('is-dragging');
		}

		root.addEventListener('pointerup', stopDrag);
		// The browser took over the gesture (page scroll): stop dragging.
		root.addEventListener('pointercancel', stopDrag);

		if (handle) {
			handle.addEventListener('keydown', function (e) {
				var step = e.shiftKey ? 10 : 2;
				var next = null;

				switch (e.key) {
					case 'ArrowLeft':
					case 'ArrowUp':
						next = current - step;
						break;
					case 'ArrowRight':
					case 'ArrowDown':
						next = current + step;
						break;
					case 'PageUp':
						next = current - 10;
						break;
					case 'PageDown':
						next = current + 10;
						break;
					case 'Home':
						next = 0;
						break;
					case 'End':
						next = 100;
						break;
				}

				if (next !== null) {
					e.preventDefault();
					setPosition(next);
				}
			});
		}

		setPosition(current);
	}

	function boot(scope) {
		(scope || document).querySelectorAll('[data-ta-before-after]').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			boot();
		});
	} else {
		boot();
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-before-after.default', function ($scope) {
				boot($scope[0]);
			});
		});
	}
})();
