/**
 * Tools Adapter — Sélecteur de variations visuel (swatches couleur / texte).
 * Pilote le <select> natif WooCommerce masqué pour rester compatible avec
 * son script de correspondance des variations.
 */
(function () {
	'use strict';

	function findSelect(wrap, name) {
		if (!name) {
			return null;
		}
		var container = wrap.closest('.variations') || wrap.closest('form.cart') || document;
		return container.querySelector('select[name="' + name + '"], select#' + CSS.escape(name));
	}

	function setActive(wrap, button) {
		wrap.querySelectorAll('.ta-swatch').forEach(function (btn) {
			btn.classList.remove('is-active');
			btn.setAttribute('aria-pressed', 'false');
		});
		button.classList.add('is-active');
		button.setAttribute('aria-pressed', 'true');
	}

	function syncDisabledState(wrap, select) {
		var options = select.querySelectorAll('option');
		wrap.querySelectorAll('.ta-swatch').forEach(function (btn) {
			var value = btn.getAttribute('data-value');
			var match = Array.prototype.find.call(options, function (opt) {
				return opt.value === value;
			});
			btn.disabled = !!(match && match.disabled);
			btn.classList.toggle('is-disabled', !!(match && match.disabled));
		});
	}

	function init(wrap) {
		if (wrap.dataset.taBound === '1') {
			return;
		}
		wrap.dataset.taBound = '1';

		var selectName = wrap.getAttribute('data-select');
		var select = findSelect(wrap, selectName);
		if (!select) {
			return;
		}

		wrap.querySelectorAll('.ta-swatch').forEach(function (btn) {
			btn.addEventListener('click', function () {
				if (btn.disabled) {
					return;
				}
				select.value = btn.getAttribute('data-value');
				setActive(wrap, btn);
				select.dispatchEvent(new Event('change', { bubbles: true }));
				if (window.jQuery) {
					window.jQuery(select).trigger('change');
				}
			});
		});

		// Reflect WooCommerce's own enabling/disabling of <option> elements
		// (e.g. when a combination becomes unavailable) onto our swatches.
		if ('MutationObserver' in window) {
			var observer = new MutationObserver(function () {
				syncDisabledState(wrap, select);
			});
			observer.observe(select, { attributes: true, attributeFilter: ['disabled'], subtree: true });
		}
		syncDisabledState(wrap, select);

		// Reset button support (WooCommerce's "Effacer" link resets the <select>s).
		var form = select.closest('form.cart');
		if (form) {
			form.addEventListener('reset', function () {
				window.setTimeout(function () {
					wrap.querySelectorAll('.ta-swatch').forEach(function (btn) {
						btn.classList.remove('is-active');
						btn.setAttribute('aria-pressed', 'false');
					});
					syncDisabledState(wrap, select);
				}, 0);
			});
		}
	}

	function boot() {
		document.querySelectorAll('[data-ta-swatches]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(document.body).on('wc_variation_form woocommerce_variation_has_changed', function () {
			document.querySelectorAll('[data-ta-swatches]').forEach(function (wrap) {
				var select = findSelect(wrap, wrap.getAttribute('data-select'));
				if (select) {
					syncDisabledState(wrap, select);
				}
			});
		});
	}
})();
