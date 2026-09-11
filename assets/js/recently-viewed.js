/**
 * Tools Adapter — Produits récemment consultés.
 * Suivi client-side (localStorage) + hydratation AJAX du widget.
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'ta_recently_viewed';
	var MAX_STORED = 20;

	function getStored() {
		try {
			var raw = window.localStorage.getItem(STORAGE_KEY);
			var list = raw ? JSON.parse(raw) : [];
			return Array.isArray(list) ? list : [];
		} catch (e) {
			return [];
		}
	}

	function trackCurrentProduct() {
		var id = window.ToolsAdapterCurrentProductId;
		if (!id) {
			return;
		}
		var list = getStored().filter(function (existingId) {
			return existingId !== id;
		});
		list.unshift(id);
		list = list.slice(0, MAX_STORED);
		try {
			window.localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
		} catch (e) {}
	}

	function hydrate(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		if (!window.ToolsAdapterRecentlyViewed) {
			return;
		}

		var limit = parseInt(root.getAttribute('data-limit'), 10) || 4;
		var exclude = parseInt(root.getAttribute('data-exclude'), 10) || 0;
		var hideEmpty = root.getAttribute('data-hide-empty') === '1';
		var cardSettings = root.getAttribute('data-card-settings') || '{}';
		var ids = getStored();

		if (!ids.length) {
			if (!hideEmpty) {
				root.hidden = false;
				var empty = root.querySelector('[data-recently-viewed-empty]');
				if (empty) {
					empty.hidden = false;
				}
			}
			return;
		}

		var data = new URLSearchParams();
		data.set('action', window.ToolsAdapterRecentlyViewed.action);
		data.set('nonce', window.ToolsAdapterRecentlyViewed.nonce);
		data.set('ids', JSON.stringify(ids));
		data.set('exclude', exclude);
		data.set('limit', limit);
		data.set('card_settings', cardSettings);

		fetch(window.ToolsAdapterRecentlyViewed.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
			.then(function (res) {
				return res.json();
			})
			.then(function (json) {
				if (!json || !json.success || !json.data.count) {
					if (!hideEmpty) {
						root.hidden = false;
						var empty = root.querySelector('[data-recently-viewed-empty]');
						if (empty) {
							empty.hidden = false;
						}
					}
					return;
				}
				var grid = root.querySelector('[data-recently-viewed-grid]');
				if (grid) {
					grid.innerHTML = json.data.html;
				}
				root.hidden = false;
			})
			.catch(function () {});
	}

	function boot() {
		trackCurrentProduct();
		document.querySelectorAll('[data-ta-recently-viewed]').forEach(hydrate);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-recently-viewed.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-recently-viewed]');
				if (root) {
					hydrate(root);
				}
			});
		});
	}
})();
