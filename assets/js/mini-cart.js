/**
 * Tools Adapter — Mini-panier (dropdown AJAX).
 */
(function () {
	'use strict';

	function refresh(root) {
		var nonce = root.getAttribute('data-nonce');
		if (!window.ToolsAdapterCart || !nonce) {
			return;
		}
		var body = new URLSearchParams();
		body.set('action', window.ToolsAdapterCart.actionGet);
		body.set('nonce', nonce);

		fetch(window.ToolsAdapterCart.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
			.then(function (res) {
				return res.json();
			})
			.then(function (json) {
				if (!json || !json.success) {
					return;
				}
				var data = json.data;
				var itemsWrap = root.querySelector('[data-minicart-items]');
				var countEl = root.querySelector('[data-minicart-count]');
				var subtotalEl = root.querySelector('[data-minicart-subtotal]');
				if (itemsWrap) {
					itemsWrap.innerHTML = data.itemsHtml;
				}
				if (countEl) {
					countEl.textContent = data.count;
				}
				if (subtotalEl) {
					subtotalEl.innerHTML = data.subtotal;
				}
			})
			.catch(function () {});
	}

	function removeItem(root, key) {
		var nonce = root.getAttribute('data-nonce');
		if (!window.ToolsAdapterCart || !nonce) {
			return;
		}
		var body = new URLSearchParams();
		body.set('action', window.ToolsAdapterCart.actionRemove);
		body.set('nonce', nonce);
		body.set('cart_item_key', key);

		fetch(window.ToolsAdapterCart.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
			.then(function (res) {
				return res.json();
			})
			.then(function (json) {
				if (!json || !json.success) {
					return;
				}
				var data = json.data;
				var itemsWrap = root.querySelector('[data-minicart-items]');
				var countEl = root.querySelector('[data-minicart-count]');
				var subtotalEl = root.querySelector('[data-minicart-subtotal]');
				if (itemsWrap) {
					itemsWrap.innerHTML = data.itemsHtml;
				}
				if (countEl) {
					countEl.textContent = data.count;
				}
				if (subtotalEl) {
					subtotalEl.innerHTML = data.subtotal;
				}
				document.dispatchEvent(new CustomEvent('ta_cart_updated'));
			})
			.catch(function () {});
	}

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var toggle = root.querySelector('[data-minicart-toggle]');
		var dropdown = root.querySelector('[data-minicart-dropdown]');

		toggle.addEventListener('click', function (e) {
			e.stopPropagation();
			var isOpen = root.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			if (isOpen) {
				refresh(root);
			}
		});

		document.addEventListener('click', function (e) {
			if (!root.contains(e.target)) {
				root.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
			}
		});

		root.addEventListener('click', function (e) {
			var removeBtn = e.target.closest('[data-cart-remove]');
			if (removeBtn) {
				removeItem(root, removeBtn.getAttribute('data-cart-remove'));
			}
		});

		// Refresh whenever WooCommerce (or Quick View) reports a successful add-to-cart.
		document.body.addEventListener('added_to_cart', function () {
			refresh(root);
		});
		document.addEventListener('ta_cart_updated', function () {
			refresh(root);
		});
	}

	function boot() {
		document.querySelectorAll('[data-ta-minicart]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-mini-cart.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-minicart]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
