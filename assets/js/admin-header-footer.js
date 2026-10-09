/**
 * Tools Adapter — Header & Footer Builder Admin Scripts
 */
(function ($) {
	'use strict';

	$(document).ready(function () {
		// Toggle Specific IDs field based on Display On
		function toggleDisplaySpecific() {
			var val = $('#ta_hf_display_on').val();
			if ('specific' === val) {
				$('#ta_hf_field_specific_ids').addClass('is-visible');
			} else {
				$('#ta_hf_field_specific_ids').removeClass('is-visible');
			}
		}

		// Toggle Exclude Specific IDs field based on Exclude On
		function toggleExcludeSpecific() {
			var val = $('#ta_hf_exclude_on').val();
			if ('specific' === val) {
				$('#ta_hf_field_exclude_ids').addClass('is-visible');
			} else {
				$('#ta_hf_field_exclude_ids').removeClass('is-visible');
			}
		}

		$('#ta_hf_display_on').on('change', toggleDisplaySpecific);
		$('#ta_hf_exclude_on').on('change', toggleExcludeSpecific);

		toggleDisplaySpecific();
		toggleExcludeSpecific();

		// Copy shortcode to clipboard
		$(document).on('click', '.ta-hf-copy-btn', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var text = $btn.data('clipboard');

			if (!text) {
				var $input = $btn.closest('.ta-hf-shortcode-wrap, .ta-hf-shortcode-cell').find('input');
				if ($input.length) {
					text = $input.val();
				}
			}

			if (!text) {
				return;
			}

			var origText = $btn.text();
			var copiedLabel = (window.ToolsAdapterHF && window.ToolsAdapterHF.copiedText) || 'Copié !';

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(function () {
					$btn.text(copiedLabel);
					setTimeout(function () {
						$btn.text(origText);
					}, 2000);
				});
			} else {
				// Fallback
				var temp = $('<input>');
				$('body').append(temp);
				temp.val(text).select();
				document.execCommand('copy');
				temp.remove();
				$btn.text(copiedLabel);
				setTimeout(function () {
					$btn.text(origText);
				}, 2000);
			}
		});
	});
})(jQuery);
