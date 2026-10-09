/**
 * Tools Adapter — Widget: Simulateur de Tarifs / Bois de chauffage.
 *
 * Configuration (data-config) :
 *   essences : ["Hêtre", …]          lengths : ["25 cm", …]
 *   prices   : [[135, 130, …], …]    (prix[essence][longueur], null = indisponible)
 *   unit, detail (modèle), prefill (modèle), service (mot-clé)
 */
(function () {
	'use strict';

	var locale = (document.documentElement.getAttribute('lang') || 'fr-FR').replace('_', '-');

	function formatNumber(n) {
		try {
			return Number(n).toLocaleString(locale, { maximumFractionDigits: 2 });
		} catch (e) {
			return String(Math.round(n * 100) / 100);
		}
	}

	function fillTemplate(template, vars) {
		return String(template || '').replace(/\{(\w+)\}/g, function (match, key) {
			return Object.prototype.hasOwnProperty.call(vars, key) ? vars[key] : match;
		});
	}

	function initCalculator(el) {
		if (el.dataset.taCalcBound === '1') {
			return;
		}
		el.dataset.taCalcBound = '1';

		var config;
		try {
			config = JSON.parse(el.getAttribute('data-config'));
		} catch (e) {
			return;
		}
		if (!config || !config.essences || !config.lengths || !config.prices) {
			return;
		}

		var essenceInputs = el.querySelectorAll('[data-calc-essence]');
		var lengthInputs = el.querySelectorAll('[data-calc-length]');
		var rangeInput = el.querySelector('[data-calc-range]');
		var volDisplay = el.querySelector('[data-calc-vol-display]');
		var unitDisplay = el.querySelector('[data-calc-unit-display]');
		var totalDisplay = el.querySelector('[data-calc-total-display]');
		var detailDisplay = el.querySelector('[data-calc-detail]');
		var ctaBtn = el.querySelector('[data-calc-cta]');
		var lastVars = {};

		// Value of a radio group or a <select> (both expose the index as value).
		function readIndex(inputs) {
			if (inputs.length === 1 && inputs[0].tagName === 'SELECT') {
				return parseInt(inputs[0].value, 10) || 0;
			}
			for (var i = 0; i < inputs.length; i++) {
				if (inputs[i].checked) {
					return parseInt(inputs[i].value, 10) || 0;
				}
			}
			return 0;
		}

		function writeLength(index) {
			if (lengthInputs.length === 1 && lengthInputs[0].tagName === 'SELECT') {
				lengthInputs[0].value = String(index);
				return;
			}
			lengthInputs.forEach(function (radio) {
				radio.checked = parseInt(radio.value, 10) === index;
			});
		}

		// Disable lengths without a price for the chosen essence; move the
		// selection to the first available one if needed.
		function syncLengths(essence) {
			var row = config.prices[essence] || [];
			var options = lengthInputs.length === 1 && lengthInputs[0].tagName === 'SELECT' ? lengthInputs[0].options : lengthInputs;

			for (var i = 0; i < options.length; i++) {
				var idx = parseInt(options[i].value, 10);
				var unavailable = row[idx] === null || row[idx] === undefined;
				options[i].disabled = unavailable;
				var label = options[i].closest ? options[i].closest('.ta-calculator__radio') : null;
				if (label) {
					label.classList.toggle('is-disabled', unavailable);
				}
			}

			var current = readIndex(lengthInputs);
			if (row[current] === null || row[current] === undefined) {
				for (var j = 0; j < row.length; j++) {
					if (row[j] !== null && row[j] !== undefined) {
						writeLength(j);
						break;
					}
				}
			}
		}

		function updateRangeFill() {
			if (!rangeInput) {
				return;
			}
			var min = parseFloat(rangeInput.min) || 0;
			var max = parseFloat(rangeInput.max) || 100;
			var val = parseFloat(rangeInput.value) || 0;
			var pct = max > min ? ((val - min) / (max - min)) * 100 : 0;
			rangeInput.style.setProperty('--ta-calc-fill', pct + '%');
		}

		function calculate() {
			var essence = readIndex(essenceInputs);
			syncLengths(essence);
			var length = readIndex(lengthInputs);

			var volume = rangeInput ? parseFloat(rangeInput.value) || 0 : 1;
			var unitPrice = (config.prices[essence] || [])[length];
			unitPrice = typeof unitPrice === 'number' ? unitPrice : 0;
			var total = Math.round(volume * unitPrice * 100) / 100;

			lastVars = {
				prix: formatNumber(unitPrice),
				volume: formatNumber(volume),
				unite: config.unit || '',
				total: formatNumber(total),
				essence: config.essences[essence] || '',
				longueur: config.lengths[length] || ''
			};

			if (volDisplay) {
				volDisplay.textContent = lastVars.volume;
			}
			if (rangeInput) {
				rangeInput.setAttribute('aria-valuetext', lastVars.volume + ' ' + lastVars.unite);
			}
			if (unitDisplay) {
				unitDisplay.textContent = lastVars.prix;
			}
			if (totalDisplay) {
				totalDisplay.textContent = lastVars.total;
			}
			if (detailDisplay) {
				detailDisplay.textContent = fillTemplate(config.detail, lastVars);
			}
			updateRangeFill();
		}

		essenceInputs.forEach(function (input) {
			input.addEventListener('change', calculate);
		});
		lengthInputs.forEach(function (input) {
			input.addEventListener('change', calculate);
		});
		if (rangeInput) {
			rangeInput.addEventListener('input', calculate);
			rangeInput.addEventListener('change', calculate);
		}

		// CTA vers une ancre : défilement doux + pré-remplissage du formulaire.
		if (ctaBtn) {
			ctaBtn.addEventListener('click', function (e) {
				var href = ctaBtn.getAttribute('href') || '';
				if (href.charAt(0) !== '#' || href.length < 2) {
					return;
				}
				var target = null;
				try {
					target = document.querySelector(href);
				} catch (err) {
					target = null;
				}
				if (!target) {
					return;
				}
				e.preventDefault();
				target.scrollIntoView({ behavior: 'smooth', block: 'start' });

				var form = target.matches && target.matches('form') ? target : (target.querySelector('form') || document.querySelector('[data-ta-contact-form]'));
				if (!form) {
					return;
				}

				var keyword = (config.service || '').toLowerCase();
				var serviceSelect = form.querySelector('select[name="service"]');
				if (keyword && serviceSelect) {
					for (var i = 0; i < serviceSelect.options.length; i++) {
						var opt = serviceSelect.options[i];
						if (opt.text.toLowerCase().indexOf(keyword) !== -1 || opt.value.toLowerCase().indexOf(keyword) !== -1) {
							serviceSelect.selectedIndex = i;
							break;
						}
					}
				}

				var msgField = form.querySelector('textarea[name="message"]');
				if (config.prefill && msgField && !msgField.value) {
					msgField.value = fillTemplate(config.prefill, lastVars);
				}
			});
		}

		calculate();
	}

	function boot(scope) {
		(scope || document).querySelectorAll('[data-ta-calculator]').forEach(initCalculator);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			boot();
		});
	} else {
		boot();
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-cost-calculator.default', function ($scope) {
				boot($scope[0]);
			});
		});
	}
})();
