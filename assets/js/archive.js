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
				attributeFilters: c.attributeFilters && typeof c.attributeFilters === 'object' ? Object.assign({}, c.attributeFilters) : {},
				updateUrl: !!c.updateUrl,
				cardSettings: c.cardSettings || {},
				paginationSettings: c.paginationSettings || {},
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

	function syncAttributeButtons(state) {
		document.querySelectorAll('[data-ta-attribute-filter]').forEach(function (wrap) {
			var taxonomy = wrap.getAttribute('data-taxonomy');
			var active = ( state.attributeFilters && state.attributeFilters[taxonomy] ) || [];
			wrap.querySelectorAll('[data-attribute-term]').forEach(function (btn) {
				var slug = btn.getAttribute('data-attribute-term');
				btn.classList.toggle('is-active', active.indexOf(slug) !== -1);
				if (btn.type === 'checkbox') {
					btn.checked = active.indexOf(slug) !== -1;
				}
			});
		});
	}

	function setAppendLoading(archive, on) {
		var loader = archive.querySelector('[data-loader]');
		if (loader) {
			loader.hidden = !on;
		}
		var btn = archive.querySelector('[data-load-more]');
		if (btn) {
			btn.disabled = on;
			if (on) {
				var loadingText = btn.getAttribute('data-loading-text');
				if (loadingText) {
					btn.textContent = loadingText;
				}
			}
		}
	}

	/**
	 * Fetch a page of products.
	 *
	 * opts.page: page to request (defaults to state.page).
	 * opts.resetPage: force page 1 (used by filters/sort changes).
	 * opts.append: append results to the grid instead of replacing it
	 *              (used by "load more" / infinite scroll).
	 */
	function fetchPage(archive, opts) {
		opts = opts || {};
		var state = getState(archive);
		if (opts.resetPage) {
			state.page = 1;
		}
		if (opts.page != null) {
			state.page = opts.page;
		}
		var append = !!opts.append;

		if (!cfg.ajaxUrl) {
			return;
		}

		if (!append) {
			if (archive._taXhr && archive._taXhr.abort) {
				archive._taXhr.abort();
			}
			setLoading(archive, true);
		} else {
			setAppendLoading(archive, true);
		}

		var data = {
			action: cfg.action,
			nonce: cfg.nonce,
			render_mode: state.renderMode || 'archive',
			page: state.page,
			per_page: state.perPage,
			orderby: state.orderby,
			category_ids: JSON.stringify(state.categoryIds || []),
			attribute_filters: JSON.stringify(state.attributeFilters || {}),
			card_settings: JSON.stringify(state.cardSettings || {}),
			pagination_settings: JSON.stringify(state.paginationSettings || {}),
		};

		if (state.minPrice != null && state.minPrice !== '') {
			data.min_price = state.minPrice;
		}
		if (state.maxPrice != null && state.maxPrice !== '') {
			data.max_price = state.maxPrice;
		}

		var xhr = $.ajax({
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
					if (append) {
						grid.insertAdjacentHTML('beforeend', response.data.html || '');
					} else {
						grid.innerHTML = response.data.html || '';
					}
				}

				var countEl = archive.querySelector('[data-result-count]');
				if (countEl && response.data.result_count) {
					countEl.textContent = response.data.result_count;
				}

				var pagWrap = archive.querySelector('[data-pagination]');
				if (pagWrap) {
					pagWrap.innerHTML = response.data.pagination || '';
					bindPaginationWrap(archive, pagWrap);
				}

				if (!append) {
					updateUrl(state);
				}
				syncCategoryButtons(state);
				syncAttributeButtons(state);
				$(document.body).trigger('ta_archive_updated', [response.data]);
			})
			.always(function () {
				if (!append) {
					setLoading(archive, false);
				} else {
					setAppendLoading(archive, false);
				}
			});

		if (!append) {
			archive._taXhr = xhr;
		}
	}

	function refresh(archive, opts) {
		fetchPage(archive, opts);
	}

	/**
	 * Bind "load more" click + (re)initialize infinite-scroll observer for
	 * the current [data-pagination] markup (called on boot and after every
	 * AJAX render, since the pagination wrap's innerHTML is replaced each time).
	 */
	function bindPaginationWrap(archive, pagWrap) {
		var loadMoreBtn = pagWrap.querySelector('[data-load-more]');
		if (loadMoreBtn) {
			loadMoreBtn.addEventListener('click', function (e) {
				e.preventDefault();
				var state = getState(archive);
				var page = parseInt(loadMoreBtn.getAttribute('data-page'), 10) || state.page + 1;
				fetchPage(archive, { page: page, append: true });
			});
		}

		setupInfiniteObserver(archive, pagWrap);
	}

	function setupInfiniteObserver(archive, pagWrap) {
		if (archive._taInfiniteObserver) {
			archive._taInfiniteObserver.disconnect();
			archive._taInfiniteObserver = null;
		}

		var infiniteEl = pagWrap.querySelector('[data-infinite]');
		if (!infiniteEl || infiniteEl.getAttribute('data-has-more') !== '1') {
			return;
		}

		var sentinel = infiniteEl.querySelector('[data-sentinel]');
		if (!sentinel || !('IntersectionObserver' in window)) {
			return;
		}

		var offset = parseInt(infiniteEl.getAttribute('data-offset'), 10) || 300;
		var nextPage = parseInt(infiniteEl.getAttribute('data-next-page'), 10) || getState(archive).page + 1;

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						observer.disconnect();
						fetchPage(archive, { page: nextPage, append: true });
					}
				});
			},
			{ rootMargin: '0px 0px ' + offset + 'px 0px' }
		);

		observer.observe(sentinel);
		archive._taInfiniteObserver = observer;
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
			// "Load more" buttons also carry [data-page] (for the next page
			// number) but are handled separately in append mode.
			if (pageBtn.hasAttribute('data-load-more') || pageBtn.disabled) {
				return;
			}
			e.preventDefault();
			var state = getState(archive);
			state.page = parseInt(pageBtn.getAttribute('data-page'), 10) || 1;
			refresh(archive);
			archive.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});

		// Bind the pagination markup already present on first paint
		// (load-more click handler + infinite-scroll observer).
		var initialPagWrap = archive.querySelector('[data-pagination]');
		if (initialPagWrap) {
			bindPaginationWrap(archive, initialPagWrap);
		}

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

	function bindAttributeFilters() {
		document.querySelectorAll('[data-ta-attribute-filter]').forEach(function (wrap) {
			if (wrap.dataset.taBound === '1') {
				return;
			}
			wrap.dataset.taBound = '1';
			var taxonomy = wrap.getAttribute('data-taxonomy');

			wrap.addEventListener('click', function (e) {
				var btn = e.target.closest('[data-attribute-term]');
				if (!btn || !wrap.contains(btn)) {
					return;
				}
				e.preventDefault();

				var archive = getArchive();
				if (!archive) {
					return;
				}

				var state = getState(archive);
				var slug = btn.getAttribute('data-attribute-term');
				if (!state.attributeFilters[taxonomy]) {
					state.attributeFilters[taxonomy] = [];
				}
				var list = state.attributeFilters[taxonomy];
				var idx = list.indexOf(slug);
				if (idx === -1) {
					list.push(slug);
				} else {
					list.splice(idx, 1);
				}
				if (!list.length) {
					delete state.attributeFilters[taxonomy];
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
		bindAttributeFilters();
		bindPriceForms();

		document.querySelectorAll('[data-ta-archive="1"]').forEach(function (archive) {
			syncCategoryButtons(getState(archive));
			syncAttributeButtons(getState(archive));
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
			'tools-adapter-attribute-filter',
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
		setAttributeFilters: function (filters) {
			var archive = getArchive();
			if (!archive) {
				return;
			}
			var state = getState(archive);
			state.attributeFilters = filters || {};
			refresh(archive, { resetPage: true });
		},
	};
})(jQuery);
