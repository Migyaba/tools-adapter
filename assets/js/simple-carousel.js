/**
 * Tools Adapter — Carrousel léger générique (1 slide à la fois).
 * Utilisé par Témoignages et autres widgets à venir.
 */
(function () {
	'use strict';

	function initCarousel(root) {
		if (root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var track = root.querySelector('[data-carousel-track]');
		var slides = Array.prototype.slice.call(root.querySelectorAll('[data-carousel-slide]'));
		if (!track || !slides.length) {
			return;
		}

		var index = 0;
		var dotsWrap = root.querySelector('[data-carousel-dots]');
		var dots = [];
		var autoplayTimer = null;

		function update() {
			track.style.transform = 'translateX(-' + index * 100 + '%)';
			dots.forEach(function (dot, i) {
				dot.classList.toggle('is-active', i === index);
			});
		}

		function goTo(i) {
			index = (i + slides.length) % slides.length;
			update();
		}

		function next() {
			goTo(index + 1);
		}

		function prev() {
			goTo(index - 1);
		}

		if (dotsWrap) {
			slides.forEach(function (_, i) {
				var dot = document.createElement('button');
				dot.type = 'button';
				dot.className = 'ta-carousel__dot';
				dot.setAttribute('aria-label', 'Slide ' + (i + 1));
				dot.addEventListener('click', function () {
					goTo(i);
					restartAutoplay();
				});
				dotsWrap.appendChild(dot);
				dots.push(dot);
			});
		}

		var prevBtn = root.querySelector('[data-carousel-prev]');
		var nextBtn = root.querySelector('[data-carousel-next]');
		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				prev();
				restartAutoplay();
			});
		}
		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				next();
				restartAutoplay();
			});
		}

		function startAutoplay() {
			if (root.getAttribute('data-autoplay') !== '1') {
				return;
			}
			var speed = parseInt(root.getAttribute('data-autoplay-speed'), 10) || 5000;
			autoplayTimer = window.setInterval(next, speed);
		}

		function stopAutoplay() {
			if (autoplayTimer) {
				window.clearInterval(autoplayTimer);
				autoplayTimer = null;
			}
		}

		function restartAutoplay() {
			stopAutoplay();
			startAutoplay();
		}

		root.addEventListener('mouseenter', stopAutoplay);
		root.addEventListener('mouseleave', startAutoplay);

		// Basic touch swipe support.
		var touchStartX = null;
		track.addEventListener('touchstart', function (e) {
			touchStartX = e.touches[0].clientX;
		}, { passive: true });
		track.addEventListener('touchend', function (e) {
			if (touchStartX == null) {
				return;
			}
			var delta = e.changedTouches[0].clientX - touchStartX;
			if (Math.abs(delta) > 40) {
				delta < 0 ? next() : prev();
				restartAutoplay();
			}
			touchStartX = null;
		});

		update();
		startAutoplay();
	}

	function boot() {
		document.querySelectorAll('[data-ta-carousel]').forEach(initCarousel);
	}

	document.addEventListener('DOMContentLoaded', boot);

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !elementorFrontend.hooks) {
				return;
			}
			['tools-adapter-testimonials'].forEach(function (widget) {
				elementorFrontend.hooks.addAction('frontend/element_ready/' + widget + '.default', function ($scope) {
					var root = $scope[0].querySelector('[data-ta-carousel]');
					if (root) {
						initCarousel(root);
					}
				});
			});
		});
	}
})();
