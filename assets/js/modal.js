/**
 * Tools Adapter — Infrastructure modale partagée.
 * Utilisée par : Mini-panier (confirmation), Vue rapide produit, Guide des tailles.
 */
(function () {
	'use strict';

	var ACTIVE_CLASS = 'is-open';
	var lastFocused = null;

	function getModal(id) {
		return document.querySelector('[data-ta-modal="' + id + '"]');
	}

	function open(id) {
		var modal = getModal(id);
		if (!modal) {
			return;
		}
		lastFocused = document.activeElement;
		modal.classList.add(ACTIVE_CLASS);
		modal.setAttribute('aria-hidden', 'false');
		document.body.classList.add('ta-modal-open');
		var closeBtn = modal.querySelector('[data-ta-modal-close]');
		if (closeBtn) {
			closeBtn.focus();
		}
		modal.dispatchEvent(new CustomEvent('ta_modal_opened', { bubbles: true }));
	}

	function close(id) {
		var modal = id ? getModal(id) : document.querySelector('.ta-modal.' + ACTIVE_CLASS);
		if (!modal) {
			return;
		}
		modal.classList.remove(ACTIVE_CLASS);
		modal.setAttribute('aria-hidden', 'true');
		var anyOpen = document.querySelector('.ta-modal.' + ACTIVE_CLASS);
		if (!anyOpen) {
			document.body.classList.remove('ta-modal-open');
		}
		if (lastFocused && typeof lastFocused.focus === 'function') {
			lastFocused.focus();
		}
		modal.dispatchEvent(new CustomEvent('ta_modal_closed', { bubbles: true }));
	}

	function toggle(id) {
		var modal = getModal(id);
		if (!modal) {
			return;
		}
		modal.classList.contains(ACTIVE_CLASS) ? close(id) : open(id);
	}

	document.addEventListener('click', function (e) {
		var opener = e.target.closest('[data-ta-modal-open]');
		if (opener) {
			e.preventDefault();
			open(opener.getAttribute('data-ta-modal-open'));
			return;
		}
		var closer = e.target.closest('[data-ta-modal-close]');
		if (closer) {
			e.preventDefault();
			var modal = closer.closest('.ta-modal');
			close(modal ? modal.getAttribute('data-ta-modal') : null);
			return;
		}
		if (e.target.classList && e.target.classList.contains('ta-modal__backdrop')) {
			var wrap = e.target.closest('.ta-modal');
			close(wrap ? wrap.getAttribute('data-ta-modal') : null);
		}
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			close();
		}
	});

	window.ToolsAdapterModal = { open: open, close: close, toggle: toggle };
})();
