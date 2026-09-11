/**
 * Tools Adapter — Page de réglages (activation des widgets).
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var form = document.querySelector('[data-ta-settings-form]');
		if (!form) {
			return;
		}

		var globalCounter = form.querySelector('[data-ta-global-counter] strong');
		var searchInput = form.querySelector('[data-ta-search]');
		var groups = Array.prototype.slice.call(form.querySelectorAll('[data-ta-group]'));

		function updateGroupCounter(group) {
			var groupId = (group.id || '').replace('ta-group-', '');
			var checkboxes = group.querySelectorAll('[data-ta-checkbox]');
			var checked = group.querySelectorAll('[data-ta-checkbox]:checked').length;
			var counters = form.querySelectorAll('[data-ta-group-counter="' + groupId + '"]');
			counters.forEach(function (counter) {
				counter.textContent = checked + '/' + checkboxes.length;
			});
		}

		function updateGlobalCounter() {
			if (!globalCounter) {
				return;
			}
			// Only the enabled count changes at runtime — the total number of
			// widgets is fixed for a given plugin version, so we only ever
			// need to update the <strong> value, not the surrounding text.
			var checked = form.querySelectorAll('input[name*="[widgets]"][data-ta-checkbox]:checked').length;
			globalCounter.textContent = String(checked);
		}

		function markDirty() {
			form.classList.add('has-unsaved-changes');
		}

		function refreshAllCounters() {
			groups.forEach(updateGroupCounter);
			updateGlobalCounter();
		}

		form.addEventListener('change', function (event) {
			if (event.target.matches('[data-ta-checkbox]')) {
				markDirty();
				refreshAllCounters();
			}
		});

		// Bulk actions scoped to the whole page.
		form.querySelectorAll('[data-ta-toggle-all]').forEach(function (button) {
			button.addEventListener('click', function () {
				var enable = button.getAttribute('data-ta-toggle-all') === 'on';
				form.querySelectorAll('[data-ta-checkbox]').forEach(function (checkbox) {
					checkbox.checked = enable;
				});
				markDirty();
				refreshAllCounters();
			});
		});

		// Bulk actions scoped to a single group (card).
		groups.forEach(function (group) {
			group.querySelectorAll('[data-ta-toggle-group]').forEach(function (button) {
				button.addEventListener('click', function () {
					var enable = button.getAttribute('data-ta-toggle-group') === 'on';
					group.querySelectorAll('[data-ta-checkbox]').forEach(function (checkbox) {
						checkbox.checked = enable;
					});
					markDirty();
					refreshAllCounters();
				});
			});
		});

		// Live search across all widget/feature items.
		if (searchInput) {
			searchInput.addEventListener('input', function () {
				var query = searchInput.value.trim().toLowerCase();
				form.querySelectorAll('[data-ta-item]').forEach(function (item) {
					var haystack = item.getAttribute('data-ta-search-text') || '';
					item.classList.toggle('is-hidden', query.length > 0 && haystack.indexOf(query) === -1);
				});
			});
		}

		// Warn before leaving the page with unsaved changes.
		window.addEventListener('beforeunload', function (event) {
			if (form.classList.contains('has-unsaved-changes')) {
				event.preventDefault();
				event.returnValue = '';
			}
		});
		form.addEventListener('submit', function () {
			form.classList.remove('has-unsaved-changes');
		});

		refreshAllCounters();
	});
})();
