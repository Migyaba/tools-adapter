/**
 * Tools Adapter — Table des matières auto-générée + surlignage de la section active.
 */
(function () {
	'use strict';

	function slugify(text, used) {
		var slug = text
			.toString()
			.trim()
			.toLowerCase()
			.replace(/[^\w\s-]/g, '')
			.replace(/\s+/g, '-');
		if (!slug) {
			slug = 'section';
		}
		var base = slug;
		var i = 1;
		while (used[slug]) {
			slug = base + '-' + (i++);
		}
		used[slug] = true;
		return slug;
	}

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var levels = (root.getAttribute('data-levels') || 'h2,h3').split(',').filter(Boolean);
		var containerSelector = root.getAttribute('data-container');
		var numbering = root.getAttribute('data-numbering') === '1';
		var smooth = root.getAttribute('data-smooth') === '1';
		var spy = root.getAttribute('data-spy') === '1';
		var collapsible = root.getAttribute('data-collapsible') === '1';

		var container = containerSelector ? document.querySelector(containerSelector) : document.body;
		if (!container) {
			container = document.body;
		}

		var selector = levels.join(',');
		var headings = Array.prototype.slice.call(container.querySelectorAll(selector)).filter(function (h) {
			return !h.closest('[data-ta-toc]');
		});

		var list = root.querySelector('[data-toc-list]');
		var empty = root.querySelector('[data-toc-empty]');

		if (!headings.length) {
			return;
		}
		if (empty) {
			empty.remove();
		}

		var used = {};
		var ul = document.createElement('ul');
		ul.className = 'ta-toc__ul';
		if (numbering) {
			ul.classList.add('ta-toc__ul--numbered');
		}

		var links = [];
		headings.forEach(function (heading) {
			if (!heading.id) {
				heading.id = 'ta-toc-' + slugify(heading.textContent, used);
			} else {
				used[heading.id] = true;
			}
			var level = parseInt(heading.tagName.replace('H', ''), 10);
			var li = document.createElement('li');
			li.className = 'ta-toc__item ta-toc__item--level-' + level;
			var a = document.createElement('a');
			a.href = '#' + heading.id;
			a.textContent = heading.textContent;
			a.dataset.tocTarget = heading.id;
			if (smooth) {
				a.addEventListener('click', function (e) {
					e.preventDefault();
					heading.scrollIntoView({ behavior: 'smooth', block: 'start' });
					if (history.pushState) {
						history.pushState(null, '', '#' + heading.id);
					}
				});
			}
			li.appendChild(a);
			ul.appendChild(li);
			links.push({ heading: heading, link: a });
		});

		list.appendChild(ul);

		if (collapsible) {
			var toggle = root.querySelector('[data-toc-toggle]');
			if (toggle) {
				toggle.addEventListener('click', function () {
					var collapsed = root.classList.toggle('is-collapsed');
					toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
				});
			}
		}

		if (spy && 'IntersectionObserver' in window) {
			var observer = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						var match = links.find(function (item) {
							return item.heading === entry.target;
						});
						if (match && entry.isIntersecting) {
							links.forEach(function (item) {
								item.link.classList.remove('is-active');
							});
							match.link.classList.add('is-active');
						}
					});
				},
				{ rootMargin: '-20% 0px -70% 0px' }
			);
			headings.forEach(function (h) {
				observer.observe(h);
			});
		}
	}

	function boot() {
		document.querySelectorAll('[data-ta-toc]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-toc.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-toc]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
