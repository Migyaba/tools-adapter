/**
 * Tools Adapter — Blog Grid & Tabs Filter
 * Gère le filtrage dynamique et instantané par catégorie pour le widget Grille de blog.
 */
(function () {
	'use strict';

	function initBlogGrid(root) {
		if (!root || root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var tabs = root.querySelectorAll('[data-blog-filter]');
		var cards = root.querySelectorAll('[data-blog-card]');
		var emptyMsg = root.querySelector('[data-blog-empty]');

		if (!tabs.length || !cards.length) {
			return;
		}

		function filterCategory(filterId) {
			var visibleCount = 0;

			tabs.forEach(function (tab) {
				var isActive = tab.getAttribute('data-blog-filter') === filterId;
				tab.classList.toggle('is-active', isActive);
				tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
			});

			cards.forEach(function (card) {
				var cats = (card.getAttribute('data-categories') || '').split(' ');
				var isMatch = (filterId === 'all' || cats.indexOf(filterId) !== -1);

				card.classList.remove('ta-animate-in');

				if (isMatch) {
					card.style.display = '';
					card.classList.remove('is-hidden');
					visibleCount++;
					// Trigger reflow for animation restart
					void card.offsetWidth;
					card.classList.add('ta-animate-in');
				} else {
					card.classList.add('is-hidden');
					card.style.display = 'none';
				}
			});

			if (emptyMsg) {
				emptyMsg.style.display = visibleCount === 0 ? '' : 'none';
			}
		}

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function (e) {
				e.preventDefault();
				var filterId = tab.getAttribute('data-blog-filter');
				filterCategory(filterId);
			});
		});
	}

	function boot() {
		document.querySelectorAll('[data-ta-blog-grid]').forEach(initBlogGrid);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-blog-grid.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-blog-grid]');
				if (root) {
					initBlogGrid(root);
				}
			});
		});
	}
})();
