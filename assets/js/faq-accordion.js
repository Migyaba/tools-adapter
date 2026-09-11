/**
 * Tools Adapter — FAQ Accordéon.
 */
(function () {
	'use strict';

	function closeItem(item) {
		var body = item.querySelector('[data-faq-body]');
		item.classList.remove('is-open');
		item.querySelector('[data-faq-toggle]').setAttribute('aria-expanded', 'false');
		if (body) {
			body.style.maxHeight = null;
		}
	}

	function openItem(item) {
		var body = item.querySelector('[data-faq-body]');
		item.classList.add('is-open');
		item.querySelector('[data-faq-toggle]').setAttribute('aria-expanded', 'true');
		if (body) {
			body.style.maxHeight = body.scrollHeight + 'px';
		}
	}

	function initAccordion(wrap) {
		if (wrap.dataset.taBound === '1') {
			return;
		}
		wrap.dataset.taBound = '1';

		var allowMultiple = wrap.getAttribute('data-multiple') === '1';
		var items = Array.prototype.slice.call(wrap.querySelectorAll('[data-faq-item]'));

		items.forEach(function (item) {
			var toggle = item.querySelector('[data-faq-toggle]');
			if (!toggle) {
				return;
			}
			toggle.addEventListener('click', function (e) {
				e.preventDefault();
				var isOpen = item.classList.contains('is-open');

				if (!allowMultiple) {
					items.forEach(function (other) {
						if (other !== item) {
							closeItem(other);
						}
					});
				}

				if (isOpen) {
					closeItem(item);
				} else {
					openItem(item);
				}
			});

			if (item.classList.contains('is-open')) {
				openItem(item);
			}
		});

		window.addEventListener('resize', function () {
			items.forEach(function (item) {
				if (item.classList.contains('is-open')) {
					var body = item.querySelector('[data-faq-body]');
					if (body) {
						body.style.maxHeight = body.scrollHeight + 'px';
					}
				}
			});
		});
	}

	function boot() {
		document.querySelectorAll('[data-ta-faq]').forEach(initAccordion);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-faq.default', function ($scope) {
				var wrap = $scope[0].querySelector('[data-ta-faq]');
				if (wrap) {
					initAccordion(wrap);
				}
			});
		});
	}
})();
