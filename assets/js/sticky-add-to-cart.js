/**
 * Tools Adapter — Barre "Ajouter au panier" collante.
 */
(function () {
	'use strict';

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var triggerSelector = root.getAttribute('data-trigger') || 'form.cart';
		var triggerEl = document.querySelector(triggerSelector);
		var isSimple = root.getAttribute('data-simple') === '1';

		function show() {
			root.classList.add('is-visible');
		}
		function hide() {
			root.classList.remove('is-visible');
		}

		if (triggerEl && 'IntersectionObserver' in window) {
			var observer = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						entry.isIntersecting ? hide() : show();
					});
				},
				{ threshold: 0 }
			);
			observer.observe(triggerEl);
		} else {
			// Fallback: show after scrolling 400px.
			window.addEventListener('scroll', function () {
				window.scrollY > 400 ? show() : hide();
			}, { passive: true });
		}

		if (isSimple) {
			var qtyInput = root.querySelector('[data-qty-input]');
			var decreaseBtn = root.querySelector('[data-qty-decrease]');
			var increaseBtn = root.querySelector('[data-qty-increase]');
			var addBtn = root.querySelector('[data-sticky-add]');

			if (decreaseBtn) {
				decreaseBtn.addEventListener('click', function () {
					var val = Math.max(1, (parseInt(qtyInput.value, 10) || 1) - 1);
					qtyInput.value = val;
				});
			}
			if (increaseBtn) {
				increaseBtn.addEventListener('click', function () {
					var max = parseInt(qtyInput.getAttribute('max'), 10);
					var val = (parseInt(qtyInput.value, 10) || 1) + 1;
					if (!isNaN(max) && val > max) {
						val = max;
					}
					qtyInput.value = val;
				});
			}
			if (addBtn) {
				addBtn.addEventListener('click', function () {
					if (!triggerEl) {
						return;
					}
					var mainQtyInput = triggerEl.querySelector('input.qty');
					if (mainQtyInput && qtyInput) {
						mainQtyInput.value = qtyInput.value;
						mainQtyInput.dispatchEvent(new Event('change', { bubbles: true }));
					}
					var submitBtn = triggerEl.querySelector('button[type="submit"], button[name="add-to-cart"]');
					if (submitBtn) {
						submitBtn.click();
					}
				});
			}
		} else {
			var scrollBtn = root.querySelector('[data-sticky-scroll]');
			if (scrollBtn && triggerEl) {
				scrollBtn.addEventListener('click', function () {
					triggerEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
				});
			}
		}
	}

	function boot() {
		document.querySelectorAll('[data-ta-sticky-atc]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-sticky-atc.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-sticky-atc]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
