/**
 * Tools Adapter — Vue rapide produit (déclenchement + AJAX + modale partagée).
 */
(function () {
	'use strict';

	var MODAL_ID = 'ta-quick-view';

	function ensureModal() {
		if (document.querySelector('[data-ta-modal="' + MODAL_ID + '"]')) {
			return;
		}
		var wrap = document.createElement('div');
		wrap.className = 'ta-modal';
		wrap.setAttribute('data-ta-modal', MODAL_ID);
		wrap.setAttribute('aria-hidden', 'true');
		wrap.innerHTML =
			'<div class="ta-modal__backdrop"></div>' +
			'<div class="ta-modal__dialog ta-modal__dialog--wide" role="dialog" aria-modal="true">' +
			'<button type="button" class="ta-modal__close" data-ta-modal-close aria-label="Fermer">&times;</button>' +
			'<div class="ta-modal__body" data-quick-view-body></div>' +
			'</div>';
		document.body.appendChild(wrap);
	}

	function openQuickView(productId) {
		if (!window.ToolsAdapterQuickView) {
			return;
		}
		ensureModal();
		var body = document.querySelector('[data-quick-view-body]');
		body.innerHTML = '<p class="ta-quick-view__loading">' + window.ToolsAdapterQuickView.i18n.loading + '</p>';
		window.ToolsAdapterModal.open(MODAL_ID);

		var data = new URLSearchParams();
		data.set('action', window.ToolsAdapterQuickView.action);
		data.set('nonce', window.ToolsAdapterQuickView.nonce);
		data.set('product_id', productId);

		fetch(window.ToolsAdapterQuickView.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
			.then(function (res) {
				return res.json();
			})
			.then(function (json) {
				if (json && json.success) {
					body.innerHTML = json.data.html;
					if (window.jQuery) {
						window.jQuery(body).trigger('woocommerce_quick_view_loaded');
						window.jQuery(document.body).trigger('wc-quick-view-loaded');
					}
				} else {
					body.innerHTML = '<p class="ta-quick-view__error">' + window.ToolsAdapterQuickView.i18n.error + '</p>';
				}
			})
			.catch(function () {
				body.innerHTML = '<p class="ta-quick-view__error">' + window.ToolsAdapterQuickView.i18n.error + '</p>';
			});
	}

	document.addEventListener('click', function (e) {
		var trigger = e.target.closest('[data-quick-view]');
		if (trigger) {
			e.preventDefault();
			openQuickView(trigger.getAttribute('data-quick-view'));
		}
	});

	window.ToolsAdapterOpenQuickView = openQuickView;
})();
