/**
 * Tools Adapter — Nav Menu Widget Scripts
 */
(function ($) {
	'use strict';

	function initNavMenu($scope) {
		var $toggle = $scope.find('.ta-nav-mobile-toggle');
		var $drawer = $scope.find('.ta-nav-drawer');
		var $overlay = $scope.find('.ta-nav-drawer-overlay');
		var $closeBtn = $scope.find('.ta-nav-drawer-close');

		function openDrawer() {
			$drawer.addClass('is-open');
			$overlay.addClass('is-open');
			$('body').css('overflow', 'hidden');
		}

		function closeDrawer() {
			$drawer.removeClass('is-open');
			$overlay.removeClass('is-open');
			$('body').css('overflow', '');
		}

		$toggle.off('click').on('click', function (e) {
			e.preventDefault();
			openDrawer();
		});

		$closeBtn.off('click').on('click', function (e) {
			e.preventDefault();
			closeDrawer();
		});

		$overlay.off('click').on('click', function () {
			closeDrawer();
		});

		$(document).off('keydown.taNavMenu').on('keydown.taNavMenu', function (e) {
			if (e.key === 'Escape' || e.keyCode === 27) {
				closeDrawer();
			}
		});

		// Submenu accordion toggles in mobile drawer
		$scope.find('.ta-mobile-submenu-toggle').off('click').on('click', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var $submenu = $btn.closest('.ta-mobile-nav-item').children('.ta-mobile-submenu');

			$btn.toggleClass('is-open');
			$submenu.toggleClass('is-open');
		});
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/tools-adapter-nav-menu.default',
			initNavMenu
		);
	});

	// Direct document ready for non-Elementor preview context
	$(document).ready(function () {
		$('.ta-nav-menu-wrapper').each(function () {
			initNavMenu($(this));
		});
	});
})(jQuery);
