/**
 * Tools Adapter — Site Search Widget Scripts
 */
(function ($) {
	'use strict';

	function initSiteSearch($scope) {
		var $trigger = $scope.find('.ta-site-search-trigger');
		var $modal = $scope.find('.ta-site-search-modal');
		var $closeBtn = $scope.find('.ta-search-modal-close');
		var $input = $modal.find('.ta-search-modal-input');

		function openModal() {
			$modal.addClass('is-open');
			$('body').css('overflow', 'hidden');
			setTimeout(function () {
				$input.focus();
			}, 100);
		}

		function closeModal() {
			$modal.removeClass('is-open');
			$('body').css('overflow', '');
		}

		$trigger.off('click').on('click', function (e) {
			e.preventDefault();
			openModal();
		});

		$closeBtn.off('click').on('click', function (e) {
			e.preventDefault();
			closeModal();
		});

		$modal.off('click').on('click', function (e) {
			if ($(e.target).closest('.ta-search-modal-content').length === 0) {
				closeModal();
			}
		});

		$(document).off('keydown.taSiteSearch').on('keydown.taSiteSearch', function (e) {
			if (e.key === 'Escape' || e.keyCode === 27) {
				closeModal();
			}
		});
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/tools-adapter-site-search.default',
			initSiteSearch
		);
	});

	$(document).ready(function () {
		$('.ta-site-search-wrap').each(function () {
			initSiteSearch($(this));
		});
	});
})(jQuery);
