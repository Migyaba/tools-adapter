/**
 * Tools Adapter — Popup preuve sociale.
 */
(function () {
	'use strict';

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var toast = root.querySelector('[data-social-proof-toast]');
		if (!toast) {
			return;
		}

		var items;
		try {
			items = JSON.parse(root.getAttribute('data-items') || '[]');
		} catch (e) {
			items = [];
		}
		if (!items.length) {
			return;
		}

		var initialDelay = parseInt(root.getAttribute('data-initial-delay'), 10) || 3000;
		var duration = parseInt(root.getAttribute('data-duration'), 10) || 5000;
		var interval = parseInt(root.getAttribute('data-interval'), 10) || 9000;
		var loop = root.getAttribute('data-loop') === '1';

		var titleEl = toast.querySelector('[data-social-proof-title]');
		var subtitleEl = toast.querySelector('[data-social-proof-subtitle]');
		var mediaEl = toast.querySelector('[data-social-proof-media]');
		var iconEl = toast.querySelector('[data-social-proof-icon]');
		var closeBtn = toast.querySelector('[data-social-proof-close]');

		var index = 0;
		var dismissed = false;
		var hideTimer = null;
		var nextTimer = null;

		function renderItem(item) {
			if (titleEl) {
				titleEl.textContent = item.title || '';
			}
			if (subtitleEl) {
				subtitleEl.textContent = item.subtitle || '';
			}
			if (mediaEl) {
				var existingImg = mediaEl.querySelector('img');
				if (item.image) {
					if (!existingImg) {
						existingImg = document.createElement('img');
						mediaEl.insertBefore(existingImg, mediaEl.firstChild);
					}
					existingImg.src = item.image;
					existingImg.alt = '';
					if (iconEl) {
						iconEl.style.display = 'none';
					}
				} else {
					if (existingImg) {
						existingImg.remove();
					}
					if (iconEl) {
						iconEl.style.display = '';
					}
				}
			}
			toast.onclick = null;
			toast.style.cursor = '';
			if (item.link) {
				toast.style.cursor = 'pointer';
				toast.onclick = function (event) {
					if (event.target === closeBtn) {
						return;
					}
					window.location.href = item.link;
				};
			}
		}

		function show() {
			if (dismissed) {
				return;
			}
			renderItem(items[index]);
			toast.hidden = false;
			window.requestAnimationFrame(function () {
				toast.classList.add('is-visible');
			});

			hideTimer = window.setTimeout(hide, duration);
		}

		function hide() {
			toast.classList.remove('is-visible');
			window.setTimeout(function () {
				toast.hidden = true;
				scheduleNext();
			}, 350);
		}

		function scheduleNext() {
			if (dismissed) {
				return;
			}
			index += 1;
			if (index >= items.length) {
				if (!loop) {
					return;
				}
				index = 0;
			}
			nextTimer = window.setTimeout(show, interval);
		}

		if (closeBtn) {
			closeBtn.addEventListener('click', function (event) {
				event.stopPropagation();
				dismissed = true;
				window.clearTimeout(hideTimer);
				window.clearTimeout(nextTimer);
				toast.classList.remove('is-visible');
				window.setTimeout(function () {
					toast.hidden = true;
				}, 350);
			});
		}

		window.setTimeout(show, initialDelay);
	}

	function boot() {
		document.querySelectorAll('[data-ta-social-proof]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-social-proof.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-social-proof]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
