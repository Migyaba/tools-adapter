/**
 * Tools Adapter — Galerie Projets Mosaïque (Bento Grid Fade Slider Engine)
 */
(function ($) {
	'use strict';

	function initProjectGallery(container) {
		var gallery = container instanceof Element ? container : (container[0] ? container[0] : null);
		if (!gallery) return;

		var galleryEl = gallery.classList.contains('ta-project-gallery') ? gallery : gallery.querySelector('.ta-project-gallery');
		if (!galleryEl || galleryEl.dataset.taInitialized) return;
		galleryEl.dataset.taInitialized = 'true';

		var intervalMs = parseInt(galleryEl.getAttribute('data-interval'), 10) || 4000;
		var pauseOnHover = galleryEl.getAttribute('data-pause-hover') === 'true';
		var cards = galleryEl.querySelectorAll('.ta-project-card');

		cards.forEach(function (card, cardIndex) {
			var slides = card.querySelectorAll('.ta-project-card__slide');
			if (slides.length <= 1) return;

			var currentIndex = 0;
			var timer = null;
			var isHovered = false;

			function goToNext() {
				if (document.hidden || (pauseOnHover && isHovered)) return;

				slides[currentIndex].classList.remove('is-active');
				currentIndex = (currentIndex + 1) % slides.length;
				slides[currentIndex].classList.add('is-active');
			}

			function startTimer() {
				if (timer) clearInterval(timer);
				timer = setInterval(goToNext, intervalMs);
			}

			// Décalage automatique progressif (stagger) pour que les cadres ne changent pas en même temps
			var staggerDelay = (cardIndex * 650) % intervalMs;
			var initialTimeout = setTimeout(function () {
				startTimer();
			}, staggerDelay);

			if (pauseOnHover) {
				card.addEventListener('mouseenter', function () {
					isHovered = true;
				});
				card.addEventListener('mouseleave', function () {
					isHovered = false;
				});
			}

			// Nettoyage si le nœud est retiré
			card.addEventListener('ta:destroy', function () {
				clearTimeout(initialTimeout);
				if (timer) clearInterval(timer);
			});
		});
	}

	$(window).on('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/tools-adapter-project-gallery.default',
				function ($scope) {
					initProjectGallery($scope);
				}
			);
		}
	});

	// Initialisation directe hors éditeur / vanilla DOM
	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.ta-project-gallery').forEach(function (gallery) {
			initProjectGallery(gallery);
		});
	});

})(typeof jQuery !== 'undefined' ? jQuery : function (callback) {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', callback);
	} else {
		callback();
	}
});
