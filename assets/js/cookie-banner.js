/**
 * Tools Adapter — Bandeau cookies (RGPD).
 */
(function () {
	'use strict';

	function getCookie(name) {
		var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
		return match ? decodeURIComponent(match[1]) : null;
	}

	function setCookie(name, value, days) {
		var expires = '';
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
			expires = '; expires=' + date.toUTCString();
		}
		document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
	}

	function dispatchConsent(consent) {
		document.dispatchEvent(new CustomEvent('tools-adapter:cookie-consent', { detail: consent }));
	}

	function init(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var cookieName = root.getAttribute('data-cookie-name') || 'ta_cookie_consent';
		var expiry = parseInt(root.getAttribute('data-cookie-expiry'), 10) || 180;
		var categoriesPanel = root.querySelector('[data-cookie-categories]');
		var checkboxes = root.querySelectorAll('[data-cookie-category]');

		var existing = getCookie(cookieName);
		if (existing) {
			// Already answered: nothing to display, but still expose the choice.
			try {
				dispatchConsent(JSON.parse(existing));
			} catch (e) {
				/* ignore malformed cookie */
			}
			return;
		}

		root.removeAttribute('hidden');
		window.requestAnimationFrame(function () {
			root.classList.add('is-visible');
		});

		function close() {
			root.classList.remove('is-visible');
			window.setTimeout(function () {
				root.setAttribute('hidden', '');
			}, 300);
		}

		function buildConsent(allEnabled) {
			var consent = {};
			checkboxes.forEach(function (checkbox) {
				var key = checkbox.getAttribute('data-cookie-category');
				consent[key] = checkbox.disabled ? true : allEnabled;
			});
			return consent;
		}

		function saveAndClose(consent) {
			setCookie(cookieName, JSON.stringify(consent), expiry);
			dispatchConsent(consent);
			close();
		}

		var acceptBtn = root.querySelector('[data-cookie-accept]');
		if (acceptBtn) {
			acceptBtn.addEventListener('click', function () {
				checkboxes.forEach(function (checkbox) {
					checkbox.checked = true;
				});
				saveAndClose(buildConsent(true));
			});
		}

		var declineBtn = root.querySelector('[data-cookie-decline]');
		if (declineBtn) {
			declineBtn.addEventListener('click', function () {
				checkboxes.forEach(function (checkbox) {
					if (!checkbox.disabled) {
						checkbox.checked = false;
					}
				});
				saveAndClose(buildConsent(false));
			});
		}

		var customizeBtn = root.querySelector('[data-cookie-customize]');
		if (customizeBtn && categoriesPanel) {
			customizeBtn.addEventListener('click', function () {
				categoriesPanel.hidden = !categoriesPanel.hidden;
			});
		}

		var saveBtn = root.querySelector('[data-cookie-save]');
		if (saveBtn) {
			saveBtn.addEventListener('click', function () {
				var consent = {};
				checkboxes.forEach(function (checkbox) {
					consent[checkbox.getAttribute('data-cookie-category')] = checkbox.disabled ? true : checkbox.checked;
				});
				saveAndClose(consent);
			});
		}
	}

	function boot() {
		document.querySelectorAll('[data-ta-cookie-banner]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-cookie-banner.default', function ($scope) {
				var root = $scope[0].querySelector('[data-ta-cookie-banner]');
				if (root) {
					init(root);
				}
			});
		});
	}
})();
