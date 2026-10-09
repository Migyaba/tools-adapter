/**
 * Tools Adapter — Formulaire de contact (envoi AJAX).
 */
(function () {
	'use strict';

	function init(form) {
		if (form.dataset.taBound === '1') {
			return;
		}
		form.dataset.taBound = '1';

		var notice = form.querySelector('[data-contact-notice]');
		var submitBtn = form.querySelector('[data-contact-submit]');
		var successText = form.getAttribute('data-success') || '';
		var errorText = form.getAttribute('data-error') || '';
		var recipient = form.getAttribute('data-recipient') || '';
		var recipientSig = form.getAttribute('data-recipient-sig') || '';

		function showNotice(message, type) {
			if (!notice) {
				return;
			}
			notice.textContent = message;
			notice.hidden = false;
			notice.classList.remove('ta-contact-form__notice--success', 'ta-contact-form__notice--error');
			notice.classList.add('ta-contact-form__notice--' + type);
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			if (!window.ToolsAdapterContactForm) {
				return;
			}

			if (!form.checkValidity()) {
				form.reportValidity();
				return;
			}

			form.classList.add('is-submitting');
			if (submitBtn) {
				submitBtn.disabled = true;
			}

			var formData = new FormData(form);
			formData.append('action', window.ToolsAdapterContactForm.action);
			formData.append('nonce', window.ToolsAdapterContactForm.nonce);
			formData.append('recipient', recipient);
			formData.append('recipient_sig', recipientSig);

			fetch(window.ToolsAdapterContactForm.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData,
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (json) {
					if (json && json.success) {
						showNotice((json.data && json.data.message) || successText, 'success');
						form.reset();
					} else {
						showNotice((json && json.data && json.data.message) || errorText, 'error');
					}
				})
				.catch(function () {
					showNotice(errorText, 'error');
				})
				.finally(function () {
					form.classList.remove('is-submitting');
					if (submitBtn) {
						submitBtn.disabled = false;
					}
				});
		});
	}

	function boot() {
		document.querySelectorAll('[data-ta-contact-form]').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/tools-adapter-contact-form.default', function ($scope) {
				var form = $scope[0].querySelector('[data-ta-contact-form]');
				if (form) {
					init(form);
				}
			});
		});
	}
})();
