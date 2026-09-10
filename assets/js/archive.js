/**
 * Tools Adapter — Unified Archive (categories + price + pagination).
 */
(function ($) {
	'use strict';

	var cfg = window.ToolsAdapterArchive || {};

	function parseConfig(el) {
		try {
			return JSON.parse(el.getAttribute('data-config') || '{}');
		} catch (e) {
			return {};
		}
	}

	function getArchive() {
		return document.querySelector('[data-ta-archive="1"]');
	}

	function getState(archive) {
		if (!archive._taState) {
			var c = parseConfig(archive);
			archive._taState = {
				page: c.page || 1,
				perPage: c.perPage || 12,
				orderby: c.orderby || 'menu_order',
				minPrice: c.minPrice,
				maxPrice: c.maxPrice,
				categoryIds: Array.isArray(c.categoryIds) ? c.categoryIds.slice() : [],
				updateUrl: !!c.updateUrl,
				cardSettings: c.cardSettings || {},
				renderMode: c.renderMode || 'archive',
			};
		}
		return archive._taState;
	}

	function setLoading(archive, on) {
		archive.classList.toggle('is-loading', on);
		var grid = archive.querySelector('[data-archive-grid]');
		if (grid) {
			grid.classList.toggle('ta-products-loading', on);
		}
	}

	function updateUrl(state) {
		if (!state.updateUrl) {
			return;
		}
		try {
			var url = new URL(window.location.href);
			if (state.minPrice != null && state.minPrice !== '') {
				url.searchParams.set('min_price', String(state.minPrice));
			} else {
				url.searchParams.delete('min_price');
			}
			if (state.maxPrice != null && state.maxPrice !== '') {
				url.searchParams.set('max_price', String(state.maxPrice));
			} else {
				url.searchParams.delete('max_price');
			}
			url.searchParams.delete('product_cat');
			(state.categoryIds || []).forEach(function (id) {
				url.searchParams.append('product_cat', String(id));
			});
			if (state.orderby) {
				url.searchParams.set('orderby', state.orderby);
			}
			if (state.page > 1) {
				url.searchParams.set('paged', String(state.page));
			} else {
				url.searchParams.delete('paged');
			}
			window.history.pushState({ taArchive: true }, '', url.toString());
		} catch (e) {}
	}

	function syncCategoryButtons(state) {
		document.querySelectorAll('[data-ta-category-filter]').forEach(function (wrap) {
			var buttons = wrap.querySelectorAll('[data-category-filter]');
			var hasCat = state.categoryIds && state.categoryIds.length > 0;
			buttons.forEach(function (btn) {
				var id = parseInt(btn.getAttribute('data-category-id'), 10) || 0;
				var active = id === 0 ? !hasCat : state.categoryIds.indexOf(id) !== -1;
				btn.classList.toggle('is-active', active);
			});
		});
	}

	function refresh(archive, opts) {
		opts = opts || {};
		var state = getState(archive);
		if (opts.resetPage) {
			state.page = 1;
		}

		if (!cfg.ajaxUrl) {
			return;
		}

		if (archive._taXhr && archive._taXhr.abort) {
			archive._taXhr.abort();
		}

		setLoading(archive, true);

		var data = {
			action: cfg.action,
			nonce: cfg.nonce,
			render_mode: state.renderMode || 'archive',
			page: state.page,
			per_page: state.perPage,
			orderby: state.orderby,
			category_ids: JSON.stringify(state.categoryIds || []),
			card_settings: JSON.stringify(state.cardSettings || {}),
		};

		if (state.minPrice != null && state.minPrice !== '') {
			data.min_price = state.minPrice;
		}
		if (state.maxPrice != null && state.maxPrice !== '') {
			data.max_price = state.maxPrice;
		}

		archive._taXhr = $.ajax({
			url: cfg.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: data,
		})
			.done(function (response) {
				if (!response || !response.success || !response.data) {
					return;
				}

				var grid = archive.querySelector('[data-archive-grid]');
				if (grid) {
					grid.innerHTML = response.data.html || '';
				}

				var countEl = archive.querySelector('[data-result-count]');
				if (countEl && response.data.result_count) {
					countEl.textContent = response.data.result_count;
				}

				var pagWrap = archive.querySelector('[data-pagination]');
				if (pagWrap) {
					pagWrap.innerHTML = response.data.pagination || '';
				}

				updateUrl(state);
				syncCategoryButtons(state);
				$(document.body).trigger('ta_archive_updated', [response.data]);
			})
			.always(function () {
				setLoading(archive, false);
			});
	}

	function bindArchive(archive) {
		if (archive.dataset.taBound === '1') {
			return;
		}
		archive.dataset.taBound = '1';
		getState(archive);

		archive.addEventListener('click', function (e) {
			var pageBtn = e.target.closest('[data-page]');
			if (!pageBtn || !archive.contains(pageBtn)) {
				return;
			}
			e.preventDefault();
			var state = getState(archive);
			state.page = parseInt(pageBtn.getAttribute('data-page'), 10) || 1;
			refresh(archive);
			archive.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});

		var orderby = archive.querySelector('[data-orderby]');
		if (orderby) {
			orderby.addEventListener('change', function () {
				var state = getState(archive);
				state.orderby = orderby.value;
				refresh(archive, { resetPage: true });
			});
		}
	}

	function bindCategoryFilters() {
		document.querySelectorAll('[data-ta-category-filter]').forEach(function (wrap) {
			if (wrap.dataset.taBound === '1') {
				return;
			}
			wrap.dataset.taBound = '1';
			var multi = wrap.getAttribute('data-multi') === '1';

			wrap.addEventListener('click', function (e) {
				var btn = e.target.closest('[data-category-filter]');
				if (!btn || !wrap.contains(btn)) {
					return;
				}
				e.preventDefault();

				var archive = getArchive();
				if (!archive) {
					return;
				}

				var state = getState(archive);
				var id = parseInt(btn.getAttribute('data-category-id'), 10) || 0;

				if (id === 0) {
					state.categoryIds = [];
				} else if (multi) {
					var idx = state.categoryIds.indexOf(id);
					if (idx === -1) {
						state.categoryIds.push(id);
					} else {
						state.categoryIds.splice(idx, 1);
					}
				} else {
					state.categoryIds = [id];
				}

				refresh(archive, { resetPage: true });
			});
		});
	}

	/**
	 * Hook price filter forms into archive when present.
	 */
	function bindPriceForms() {
		document.querySelectorAll('.ta-price-filter').forEach(function (form) {
			if (form.dataset.taArchiveBound === '1') {
				return;
			}
			form.dataset.taArchiveBound = '1';

			form.addEventListener('submit', function (e) {
				var archive = getArchive();
				if (!archive || form.dataset.ajax !== '1') {
					return;
				}
				e.preventDefault();
				e.stopImmediatePropagation();

				var state = getState(archive);
				var minInput = form.querySelector('[data-input="min"]');
				var maxInput = form.querySelector('[data-input="max"]');
				state.minPrice = minInput ? parseFloat(minInput.value) : form.dataset.min;
				state.maxPrice = maxInput ? parseFloat(maxInput.value) : form.dataset.max;
				refresh(archive, { resetPage: true });
			}, true);

			var resetBtn = form.querySelector('[data-reset]');
			if (resetBtn) {
				resetBtn.addEventListener('click', function (e) {
					var archive = getArchive();
					if (!archive) {
						return;
					}
					e.preventDefault();
					e.stopImmediatePropagation();

					var state = getState(archive);
					var minBound = parseFloat(form.dataset.min) || 0;
					var maxBound = parseFloat(form.dataset.max) || 0;
					var thumbMin = form.querySelector('[data-thumb="min"]');
					var thumbMax = form.querySelector('[data-thumb="max"]');
					if (thumbMin) thumbMin.value = minBound;
					if (thumbMax) thumbMax.value = maxBound;
					thumbMin && thumbMin.dispatchEvent(new Event('input', { bubbles: true }));

					state.minPrice = null;
					state.maxPrice = null;
					refresh(archive, { resetPage: true });
				}, true);
			}

			// Live filter on slider change → archive.
			if (form.dataset.ajaxOnChange === '1') {
				var debounceTimer;
				form.addEventListener('input', function (e) {
					if (!e.target.matches('[data-thumb], [data-input]')) {
						return;
					}
					var archive = getArchive();
					if (!archive) {
						return;
					}
					window.clearTimeout(debounceTimer);
					debounceTimer = window.setTimeout(function () {
						var state = getState(archive);
						var minInput = form.querySelector('[data-input="min"]');
						var maxInput = form.querySelector('[data-input="max"]');
						state.minPrice = minInput ? parseFloat(minInput.value) : null;
						state.maxPrice = maxInput ? parseFloat(maxInput.value) : null;
						refresh(archive, { resetPage: true });
					}, parseInt(form.dataset.debounce, 10) || 400);
				});
			}
		});
	}

	function boot() {
		document.querySelectorAll('[data-ta-archive="1"]').forEach(bindArchive);
		bindCategoryFilters();
		bindPriceForms();

		document.querySelectorAll('[data-ta-archive="1"]').forEach(function (archive) {
			syncCategoryButtons(getState(archive));
		});
	}

	$(boot);

	$(window).on('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !elementorFrontend.hooks) {
			return;
		}
		[
			'tools-adapter-product-archive',
			'tools-adapter-product-categories',
			'tools-adapter-price-filter',
		].forEach(function (widget) {
			elementorFrontend.hooks.addAction('frontend/element_ready/' + widget + '.default', function () {
				boot();
			});
		});
	});

	// Public API for other scripts.
	window.ToolsAdapterArchiveAPI = {
		refresh: function () {
			var archive = getArchive();
			if (archive) {
				refresh(archive);
			}
		},
		setPrice: function (min, max) {
			var archive = getArchive();
			if (!archive) {
				return;
			}
			var state = getState(archive);
			state.minPrice = min;
			state.maxPrice = max;
			refresh(archive, { resetPage: true });
		},
		setCategories: function (ids) {
			var archive = getArchive();
			if (!archive) {
				return;
			}
			var state = getState(archive);
			state.categoryIds = ids || [];
			refresh(archive, { resetPage: true });
		},
	};
})(jQuery);
