/**
 * Tools Adapter — Mega Menu.
 */
(function () {
	'use strict';

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var isSticky = root.getAttribute('data-sticky') === '1';
		var breakpoint = parseInt(root.getAttribute('data-breakpoint'), 10) || 992;
		var toggleBtn = root.querySelector('[data-mega-toggle]');
		var list = root.querySelector('[data-mega-list]');

		function updateMobileState() {
			var isMobile = window.innerWidth <= breakpoint;
			root.classList.toggle('ta-mega-menu--mobile', isMobile);
			if (!isMobile) {
				root.classList.remove('is-mobile-open');
				if (toggleBtn) {
					toggleBtn.setAttribute('aria-expanded', 'false');
				}
				root.querySelectorAll('.ta-mega-menu__item.is-open').forEach(function (item) {
					item.classList.remove('is-open');
				});
			}
		}
		updateMobileState();
		window.addEventListener('resize', updateMobileState);

		if (toggleBtn && list) {
			toggleBtn.addEventListener('click', function () {
				var isOpen = root.classList.toggle('is-mobile-open');
				toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			});
		}

		// Mobile: tap on a parent link toggles its panel instead of navigating.
		root.querySelectorAll('.ta-mega-menu__item.has-panel > .ta-mega-menu__link').forEach(function (link) {
			link.addEventListener('click', function (event) {
				if (!root.classList.contains('ta-mega-menu--mobile')) {
					return;
				}
				event.preventDefault();
				var item = link.closest('.ta-mega-menu__item');
				var wasOpen = item.classList.contains('is-open');
				root.querySelectorAll('.ta-mega-menu__item.is-open').forEach(function (openItem) {
					if (openItem !== item) {
						openItem.classList.remove('is-open');
					}
				});
				item.classList.toggle('is-open', !wasOpen);
			});
		});

		// Sticky behaviour: a sentinel placed right before the nav tells us
		// when the nav's natural position has scrolled out of view.
		if (isSticky) {
			root.classList.add('ta-mega-menu--sticky');
			var sentinel = document.createElement('div');
			sentinel.setAttribute('aria-hidden', 'true');
			sentinel.style.height = '0';
			sentinel.style.width = '100%';
			root.parentNode.insertBefore(sentinel, root);

			var spacer = document.createElement('div');
			spacer.className = 'ta-mega-menu__spacer';
			spacer.style.display = 'none';

			if ('IntersectionObserver' in window) {
				var observer = new IntersectionObserver(
					function (entries) {
						entries.forEach(function (entry) {
							if (entry.isIntersecting) {
								root.classList.remove('is-stuck');
								spacer.style.display = 'none';
							} else {
								spacer.style.height = root.offsetHeight + 'px';
								spacer.style.display = 'block';
								root.classList.add('is-stuck');
							}
						});
					},
					{ threshold: 0 }
				);
				root.parentNode.insertBefore(spacer, root.nextSibling);
				observer.observe(sentinel);
			}
		}
	}

	function boot() {
		document.querySelectorAll('[data-ta-mega-menu]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-mega-menu.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-mega-menu]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
