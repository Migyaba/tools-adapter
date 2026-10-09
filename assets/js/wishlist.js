/**
 * Tools Adapter — Liste de souhaits.
 *
 * Buttons: [data-ta-wishlist="<product id>"]. Counters: [data-ta-wishlist-count].
 * Lists: [data-ta-wishlist-list] (rendered through AJAX from the stored IDs).
 */
(function () {
	'use strict';

	var cfg = window.ToolsAdapterWishlist || {};
	var KEY = 'taWishlist';
	var i18n = cfg.i18n || {};
	var max = cfg.max || 100;

	function read() {
		try {
			var value = JSON.parse(window.localStorage.getItem(KEY) || '[]');
			return Array.isArray(value) ? value.map(Number).filter(Boolean) : [];
		} catch (e) {
			return [];
		}
	}

	function write(list) {
		try {
			window.localStorage.setItem(KEY, JSON.stringify(list));
		} catch (e) {}
	}

	function union(a, b) {
		var out = a.slice();
		b.forEach(function (id) {
			if (out.indexOf(id) === -1) {
				out.push(id);
			}
		});
		return out.slice(0, max);
	}

	function post(action, data) {
		var body = new FormData();
		body.append('action', action);
		body.append('nonce', cfg.nonce);
		Object.keys(data).forEach(function (key) {
			body.append(key, data[key]);
		});
		return fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' }).then(function (r) {
			return r.json();
		});
	}

	var items = read();

	function sync() {
		if (cfg.loggedIn) {
			post(cfg.actionSync, { items: items.join(',') }).catch(function () {});
		}
	}

	// Logged-in customers: merge what is stored on the account with this browser.
	if (cfg.loggedIn) {
		var server = (cfg.items || []).map(Number);
		var merged = union(server, items);
		items = merged;
		write(items);
		if (merged.length !== server.length) {
			sync();
		}
	}

	function refresh() {
		document.querySelectorAll('[data-ta-wishlist]').forEach(function (btn) {
			var active = items.indexOf(parseInt(btn.getAttribute('data-ta-wishlist'), 10)) !== -1;
			btn.classList.toggle('is-active', active);
			btn.setAttribute('aria-pressed', active ? 'true' : 'false');
			var label = active ? i18n.remove : i18n.add;
			if (label) {
				btn.setAttribute('aria-label', label);
				btn.setAttribute('title', label);
			}
		});
		document.querySelectorAll('[data-ta-wishlist-count]').forEach(function (el) {
			el.textContent = String(items.length);
			el.setAttribute('data-count', String(items.length));
		});
	}

	function renderLists() {
		document.querySelectorAll('[data-ta-wishlist-list]').forEach(function (el) {
			el.classList.add('is-loading');
			post(cfg.actionRender, { items: items.join(','), columns: el.getAttribute('data-columns') || 4 })
				.then(function (res) {
					if (res && res.success) {
						el.innerHTML = res.data.html;
						refresh();
					}
				})
				.catch(function () {})
				.then(function () {
					el.classList.remove('is-loading');
				});
		});
	}

	function toast(message) {
		var el = document.querySelector('.ta-wishlist-toast');
		if (!el) {
			el = document.createElement('div');
			el.className = 'ta-wishlist-toast';
			el.setAttribute('role', 'status');
			document.body.appendChild(el);
		}
		el.innerHTML = '';
		el.appendChild(document.createTextNode(message));
		if (cfg.pageUrl) {
			var link = document.createElement('a');
			link.href = cfg.pageUrl;
			link.textContent = i18n.view || '';
			el.appendChild(link);
		}
		el.classList.add('is-visible');
		window.clearTimeout(toast.timer);
		toast.timer = window.setTimeout(function () {
			el.classList.remove('is-visible');
		}, 3200);
	}

	function toggle(id) {
		var index = items.indexOf(id);
		var added = index === -1;
		if (added) {
			items = union([id], items);
		} else {
			items.splice(index, 1);
		}
		write(items);
		sync();
		refresh();
		if (added && i18n.added) {
			toast(i18n.added);
		}
		if (!added) {
			renderLists();
		}
		document.dispatchEvent(new CustomEvent('ta:wishlist:change', { detail: { items: items.slice(), id: id, added: added } }));
	}

	document.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-ta-wishlist]');
		if (!btn) {
			return;
		}
		event.preventDefault();
		event.stopPropagation();
		toggle(parseInt(btn.getAttribute('data-ta-wishlist'), 10));
	});

	// Cards added later (AJAX archive, quick view, carousels) get the right state.
	if ('MutationObserver' in window) {
		var pending = false;
		new MutationObserver(function () {
			if (pending) {
				return;
			}
			pending = true;
			window.requestAnimationFrame(function () {
				pending = false;
				refresh();
			});
		}).observe(document.body, { childList: true, subtree: true });
	}

	window.ToolsAdapterWishlistAPI = {
		items: function () {
			return items.slice();
		},
		toggle: toggle,
	};

	refresh();
	renderLists();
})();
