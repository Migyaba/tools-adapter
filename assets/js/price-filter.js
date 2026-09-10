/**
 * Tools Adapter — Price Filter (dual-range + AJAX).
 */
(function ($) {
	'use strict';

	var cfg = window.ToolsAdapterPriceFilter || {};

	function parseNum(value, fallback) {
		var n = parseFloat(value);
		return Number.isFinite(n) ? n : fallback;
	}

	function formatLabel(amount, currency) {
		var decimals = 2;
		try {
			return currency + Number(amount).toLocaleString(undefined, {
				minimumFractionDigits: decimals,
				maximumFractionDigits: decimals,
			});
		} catch (e) {
			return currency + Number(amount).toFixed(decimals);
		}
	}

	function getCurrency(form) {
		return form.dataset.currency ||
			(form.querySelector('.ta-price-filter__currency')
				? form.querySelector('.ta-price-filter__currency').textContent.trim()
				: '');
	}

	function updateRange(form, minVal, maxVal) {
		var range = form.querySelector('[data-range]');
		var minBound = parseNum(form.dataset.min, 0);
		var maxBound = parseNum(form.dataset.max, 100);
		var span = Math.max(maxBound - minBound, 0.0001);
		var left = ((minVal - minBound) / span) * 100;
		var right = ((maxBound - maxVal) / span) * 100;

		if (range) {
			range.style.left = left + '%';
			range.style.right = right + '%';
		}
	}

	function syncDisplays(form, minVal, maxVal) {
		var currency = getCurrency(form);
		var displayMin = form.querySelector('[data-display-min]');
		var displayMax = form.querySelector('[data-display-max]');
		var inputMin = form.querySelector('[data-input="min"]');
		var inputMax = form.querySelector('[data-input="max"]');

		if (displayMin) {
			displayMin.textContent = formatLabel(minVal, currency);
		}
		if (displayMax) {
			displayMax.textContent = formatLabel(maxVal, currency);
		}
		if (inputMin) {
			inputMin.value = minVal;
		}
		if (inputMax) {
			inputMax.value = maxVal;
		}

		updateRange(form, minVal, maxVal);
	}

	function findProductsTarget(selector) {
		var el = document.querySelector(selector);
		if (el) {
			return el;
		}
		// Fallbacks courants WooCommerce / Elementor.
		return (
			document.querySelector('ul.products') ||
			document.querySelector('.products') ||
			document.querySelector('.woocommerce-loop') ||
			null
		);
	}

	function setLoading(form, target, isLoading) {
		var status = form.querySelector('[data-status]');
		form.classList.toggle('is-loading', isLoading);

		if (target) {
			target.classList.toggle('ta-products-loading', isLoading);
			var wrap = target.closest('.woocommerce') || target.parentElement;
			if (wrap) {
				wrap.classList.toggle('ta-products-loading-wrap', isLoading);
			}
		}

		if (status) {
			if (isLoading) {
				status.hidden = false;
				status.textContent = (cfg.i18n && cfg.i18n.loading) || '';
			} else {
				status.hidden = true;
				status.textContent = '';
			}
		}
	}

	function updateBrowserUrl(minVal, maxVal) {
		try {
			var url = new URL(window.location.href);
			url.searchParams.set('min_price', String(minVal));
			url.searchParams.set('max_price', String(maxVal));
			url.searchParams.delete('paged');
			window.history.pushState({ taPriceFilter: true }, '', url.toString());
		} catch (e) {
			// Ignore URL API errors on older browsers.
		}
	}

	function extractProductsHtml(html) {
		var tmp = document.createElement('div');
		tmp.innerHTML = html;

		var products = tmp.querySelector('ul.products') || tmp.querySelector('.products');
		if (products) {
			return products.innerHTML;
		}
		return html;
	}

	function runAjaxFilter(form, minVal, maxVal, isReset) {
		if (form.dataset.ajax !== '1' || !cfg.ajaxUrl) {
			return;
		}

		// Archive Produits takes over when present.
		if (document.querySelector('[data-ta-archive="1"]') && window.ToolsAdapterArchiveAPI) {
			if (isReset) {
				window.ToolsAdapterArchiveAPI.setPrice(null, null);
			} else {
				window.ToolsAdapterArchiveAPI.setPrice(minVal, maxVal);
			}
			return;
		}

		var selector = form.dataset.productsSelector || 'ul.products';
		var target = findProductsTarget(selector);
		if (!target) {
			form.submit();
			return;
		}

		if (form._taXhr && form._taXhr.abort) {
			form._taXhr.abort();
		}

		setLoading(form, target, true);

		var data = {
			action: cfg.action,
			nonce: cfg.nonce,
			min_price: isReset ? form.dataset.min : minVal,
			max_price: isReset ? form.dataset.max : maxVal,
			page: 1,
			taxonomy: form.dataset.taxonomy || '',
			term_id: form.dataset.termId || 0,
			orderby: form.dataset.orderby || '',
		};

		form._taXhr = $.ajax({
			url: cfg.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: data,
		})
			.done(function (response) {
				if (!response || !response.success || !response.data) {
					var status = form.querySelector('[data-status]');
					if (status) {
						status.hidden = false;
						status.textContent = (cfg.i18n && cfg.i18n.error) || '';
					}
					return;
				}

				var html = response.data.html || '';
				var inner = extractProductsHtml(html);

				// Remplacer le contenu de la grille.
				if (target.matches('ul.products') || target.classList.contains('products')) {
					target.innerHTML = inner;
				} else {
					var existingUl = target.querySelector('ul.products') || target.querySelector('.products');
					if (existingUl) {
						existingUl.innerHTML = extractProductsHtml(html);
					} else {
						target.innerHTML = html;
					}
				}

				// Compteur de résultats.
				var countSel = form.dataset.resultCountSelector || '.woocommerce-result-count';
				var countEl = document.querySelector(countSel);
				if (countEl && response.data.result_count) {
					var tmp = document.createElement('div');
					tmp.innerHTML = response.data.result_count;
					var fresh = tmp.firstElementChild;
					if (fresh) {
						countEl.replaceWith(fresh);
					} else {
						countEl.innerHTML = response.data.result_count;
					}
				}

				if (form.dataset.updateUrl === '1' && !isReset) {
					updateBrowserUrl(response.data.min_price, response.data.max_price);
				} else if (form.dataset.updateUrl === '1' && isReset) {
					try {
						var url = new URL(window.location.href);
						url.searchParams.delete('min_price');
						url.searchParams.delete('max_price');
						url.searchParams.delete('paged');
						window.history.pushState({ taPriceFilter: true }, '', url.toString());
					} catch (e) {}
				}

				if (form.dataset.scroll === '1') {
					target.scrollIntoView({ behavior: 'smooth', block: 'start' });
				}

				// Re-init Woo / Elementor handlers if present.
				$(document.body).trigger('ta_price_filter_updated', [response.data]);
				$(document.body).trigger('wc_fragment_refresh');
			})
			.fail(function (_xhr, textStatus) {
				if (textStatus === 'abort') {
					return;
				}
				var status = form.querySelector('[data-status]');
				if (status) {
					status.hidden = false;
					status.textContent = (cfg.i18n && cfg.i18n.error) || '';
				}
			})
			.always(function () {
				setLoading(form, target, false);
			});
	}

	function debounce(fn, wait) {
		var t;
		return function () {
			var ctx = this;
			var args = arguments;
			clearTimeout(t);
			t = setTimeout(function () {
				fn.apply(ctx, args);
			}, wait);
		};
	}

	function initForm(form) {
		if (form.dataset.taReady === '1') {
			return;
		}
		form.dataset.taReady = '1';

		var thumbMin = form.querySelector('[data-thumb="min"]');
		var thumbMax = form.querySelector('[data-thumb="max"]');
		var inputMin = form.querySelector('[data-input="min"]');
		var inputMax = form.querySelector('[data-input="max"]');
		var resetBtn = form.querySelector('[data-reset]');
		var minBound = parseNum(form.dataset.min, 0);
		var maxBound = parseNum(form.dataset.max, 100);
		var debounceMs = parseNum(form.dataset.debounce, 400);

		if (!thumbMin || !thumbMax) {
			return;
		}

		function read() {
			return {
				minVal: parseNum(thumbMin.value, minBound),
				maxVal: parseNum(thumbMax.value, maxBound),
			};
		}

		function onThumbChange(source) {
			var values = read();
			if (values.minVal > values.maxVal) {
				if (source === 'min') {
					values.minVal = values.maxVal;
					thumbMin.value = values.minVal;
				} else {
					values.maxVal = values.minVal;
					thumbMax.value = values.maxVal;
				}
			}
			syncDisplays(form, values.minVal, values.maxVal);
		}

		var liveFilter = debounce(function () {
			if (form.dataset.ajax !== '1' || form.dataset.ajaxOnChange !== '1') {
				return;
			}
			var values = read();
			runAjaxFilter(form, values.minVal, values.maxVal, false);
		}, debounceMs);

		thumbMin.addEventListener('input', function () {
			onThumbChange('min');
			liveFilter();
		});
		thumbMax.addEventListener('input', function () {
			onThumbChange('max');
			liveFilter();
		});

		function onInputChange(source) {
			var minVal = parseNum(inputMin && inputMin.value, minBound);
			var maxVal = parseNum(inputMax && inputMax.value, maxBound);

			minVal = Math.max(minBound, Math.min(minVal, maxBound));
			maxVal = Math.max(minBound, Math.min(maxVal, maxBound));

			if (minVal > maxVal) {
				if (source === 'min') {
					minVal = maxVal;
				} else {
					maxVal = minVal;
				}
			}

			thumbMin.value = minVal;
			thumbMax.value = maxVal;
			syncDisplays(form, minVal, maxVal);
			liveFilter();
		}

		if (inputMin) {
			inputMin.addEventListener('change', function () {
				onInputChange('min');
			});
		}
		if (inputMax) {
			inputMax.addEventListener('change', function () {
				onInputChange('max');
			});
		}

		form.addEventListener('submit', function (e) {
			if (form.dataset.ajax !== '1') {
				return;
			}
			e.preventDefault();
			var values = read();
			runAjaxFilter(form, values.minVal, values.maxVal, false);
		});

		if (resetBtn) {
			resetBtn.addEventListener('click', function (e) {
				e.preventDefault();
				thumbMin.value = minBound;
				thumbMax.value = maxBound;
				syncDisplays(form, minBound, maxBound);

				if (form.dataset.ajax === '1') {
					runAjaxFilter(form, minBound, maxBound, true);
				} else if (resetBtn.dataset.href) {
					window.location.href = resetBtn.dataset.href;
				}
			});
		}

		onThumbChange('min');
	}

	function boot() {
		document.querySelectorAll('.ta-price-filter').forEach(initForm);
	}

	$(boot);

	$(window).on('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !elementorFrontend.hooks) {
			return;
		}
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/tools-adapter-price-filter.default',
			function ($scope) {
				var form = $scope[0] ? $scope[0].querySelector('.ta-price-filter') : null;
				if (form) {
					form.dataset.taReady = '0';
					initForm(form);
				}
			}
		);
	});
})(jQuery);
