/**
 * Tools Adapter — Carrousel léger générique (Support 1 ou plusieurs slides simultanées).
 * Utilisé par Témoignages et autres widgets.
 */
(function () {
	'use strict';

	function initCarousel(root) {
		if (!root || root.dataset.taBound === '1') {
			return;
		}
		root.dataset.taBound = '1';

		var track = root.querySelector('[data-carousel-track]');
		var slides = Array.prototype.slice.call(root.querySelectorAll('[data-carousel-slide]'));
		if (!track || !slides.length) {
			return;
		}

		var showDesktop = Math.max(1, parseInt(root.getAttribute('data-slides-show'), 10) || 1);
		var showTablet = Math.max(1, parseInt(root.getAttribute('data-slides-show-tablet'), 10) || Math.min(showDesktop, 2));
		var showMobile = Math.max(1, parseInt(root.getAttribute('data-slides-show-mobile'), 10) || 1);
		var defaultGap = parseFloat(root.getAttribute('data-gap')) || 24;

		var index = 0;
		var maxIndex = 0;
		var pageCount = 1;
		var dotsWrap = root.querySelector('[data-carousel-dots]');
		var dots = [];
		var autoplayTimer = null;
		var currentSlideWidth = 0;
		var currentGap = defaultGap;
		var isSingleSlide = (showDesktop === 1 && showTablet === 1 && showMobile === 1);

		function getSlidesToShow() {
			var w = window.innerWidth;
			if (w <= 767) {
				return showMobile;
			}
			if (w <= 1024) {
				return showTablet;
			}
			return showDesktop;
		}

		function layout() {
			var show = getSlidesToShow();
			currentGap = defaultGap;
			if (window.innerWidth <= 767) {
				currentGap = Math.min(defaultGap, 16);
			}

			var containerWidth = root.clientWidth;
			if (containerWidth <= 0 && root.parentElement) {
				containerWidth = root.parentElement.clientWidth;
			}

			if (show > 1) {
				currentSlideWidth = (containerWidth - currentGap * (show - 1)) / show;
				slides.forEach(function (slide) {
					slide.style.width = currentSlideWidth + 'px';
					slide.style.flex = '0 0 ' + currentSlideWidth + 'px';
					slide.style.marginRight = currentGap + 'px';
				});
				maxIndex = Math.max(0, slides.length - show);
			} else {
				currentSlideWidth = containerWidth;
				slides.forEach(function (slide) {
					slide.style.width = '100%';
					slide.style.flex = '0 0 100%';
					slide.style.marginRight = '0px';
				});
				maxIndex = Math.max(0, slides.length - 1);
			}

			pageCount = maxIndex + 1;

			if (index > maxIndex) {
				index = maxIndex;
			}

			renderDots();
			update(false);
		}

		function update(animated) {
			if (animated !== false) {
				track.style.transition = 'transform 0.45s cubic-bezier(0.22, 1, 0.36, 1)';
			} else {
				track.style.transition = 'none';
			}

			var show = getSlidesToShow();
			if (show > 1) {
				var offset = index * (currentSlideWidth + currentGap);
				track.style.transform = 'translateX(-' + offset + 'px)';
			} else {
				track.style.transform = 'translateX(-' + (index * 100) + '%)';
			}

			dots.forEach(function (dot, i) {
				dot.classList.toggle('is-active', i === index);
				dot.setAttribute('aria-current', i === index ? 'true' : 'false');
			});

			updateArrows();
		}

		function goTo(i) {
			if (i < 0) {
				index = maxIndex;
			} else if (i > maxIndex) {
				index = 0;
			} else {
				index = i;
			}
			update(true);
		}

		function next() {
			goTo(index + 1);
		}

		function prev() {
			goTo(index - 1);
		}

		function renderDots() {
			if (!dotsWrap) {
				return;
			}
			dotsWrap.innerHTML = '';
			dots = [];

			if (pageCount <= 1) {
				dotsWrap.style.display = 'none';
				return;
			}
			dotsWrap.style.display = '';

			for (var i = 0; i < pageCount; i++) {
				(function (pageIdx) {
					var dot = document.createElement('button');
					dot.type = 'button';
					dot.className = 'ta-carousel__dot';
					if (pageIdx === index) {
						dot.classList.add('is-active');
					}
					dot.setAttribute('aria-label', 'Slide ' + (pageIdx + 1));
					dot.addEventListener('click', function () {
						goTo(pageIdx);
						restartAutoplay();
					});
					dotsWrap.appendChild(dot);
					dots.push(dot);
				})(i);
			}
		}

		var prevBtn = root.querySelector('[data-carousel-prev]');
		var nextBtn = root.querySelector('[data-carousel-next]');

		function updateArrows() {
			if (pageCount <= 1) {
				if (prevBtn) prevBtn.style.display = 'none';
				if (nextBtn) nextBtn.style.display = 'none';
			} else {
				if (prevBtn) prevBtn.style.display = '';
				if (nextBtn) nextBtn.style.display = '';
			}
		}

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
			if (root.getAttribute('data-autoplay') !== '1' || pageCount <= 1) {
				return;
			}
			var speed = parseInt(root.getAttribute('data-autoplay-speed'), 10) || 5000;
			if (autoplayTimer) {
				clearInterval(autoplayTimer);
			}
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

		// Touch swipe
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

		layout();
		startAutoplay();

		var resizeTimeout = null;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(layout, 150);
		});
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
