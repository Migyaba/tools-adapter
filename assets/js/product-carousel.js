/**
 * Tools Adapter — Product Carousel (lightweight, no dependency).
 */
(function () {
	'use strict';

	function parseConfig(el) {
		try {
			return JSON.parse(el.getAttribute('data-config') || '{}');
		} catch (e) {
			return {};
		}
	}

	function getBreakpoint(config) {
		var w = window.innerWidth;
		if (w <= 767) {
			return {
				show: (config.slidesToShow && config.slidesToShow.mobile) || 1,
				gap: (config.gap && config.gap.mobile) || 16,
			};
		}
		if (w <= 1024) {
			return {
				show: (config.slidesToShow && config.slidesToShow.tablet) || 2,
				gap: (config.gap && config.gap.tablet) || 20,
			};
		}
		return {
			show: (config.slidesToShow && config.slidesToShow.desktop) || 4,
			gap: (config.gap && config.gap.desktop) || 20,
		};
	}

	function initCarousel(root) {
		if (!root || root.dataset.taReady === '1') {
			return;
		}
		root.dataset.taReady = '1';

		var config = parseConfig(root);
		var track = root.querySelector('.ta-carousel__track');
		var slides = Array.prototype.slice.call(root.querySelectorAll('.ta-carousel__slide'));
		var prev = root.querySelector('.ta-carousel__arrow--prev');
		var next = root.querySelector('.ta-carousel__arrow--next');
		var dotsWrap = root.querySelector('[data-dots]');

		if (!track || !slides.length) {
			return;
		}

		var index = 0;
		var timer = null;
		var pageCount = 1;

		function layout() {
			var bp = getBreakpoint(config);
			var show = Math.max(1, parseInt(bp.show, 10) || 1);
			var gap = Math.max(0, parseFloat(bp.gap) || 0);
			var viewport = root.querySelector('.ta-carousel__viewport');
			var width = viewport ? viewport.clientWidth : root.clientWidth;
			var slideWidth = (width - gap * (show - 1)) / show;

			slides.forEach(function (slide) {
				slide.style.width = slideWidth + 'px';
				slide.style.marginRight = gap + 'px';
			});

			// Remove trailing margin visually via negative track trick not needed;
			// last margin is clipped by overflow hidden.
			pageCount = Math.max(1, slides.length - show + 1);
			if (index > pageCount - 1) {
				index = pageCount - 1;
			}
			goTo(index, false);
			renderDots();
			updateArrows();
		}

		function goTo(i, animate) {
			var bp = getBreakpoint(config);
			var show = Math.max(1, parseInt(bp.show, 10) || 1);
			var gap = Math.max(0, parseFloat(bp.gap) || 0);
			var max = Math.max(0, slides.length - show);
			index = Math.max(0, Math.min(i, max));

			if (!config.loop) {
				index = Math.max(0, Math.min(i, max));
			} else if (i < 0) {
				index = max;
			} else if (i > max) {
				index = 0;
			}

			var offset = index * ((slides[0] ? slides[0].offsetWidth : 0) + gap);
			track.style.transition = animate === false ? 'none' : '';
			track.style.transform = 'translate3d(' + -offset + 'px, 0, 0)';
			updateDots();
			updateArrows();
		}

		function updateArrows() {
			if (!prev || !next) {
				return;
			}
			if (config.loop) {
				prev.disabled = false;
				next.disabled = false;
				return;
			}
			var bp = getBreakpoint(config);
			var show = Math.max(1, parseInt(bp.show, 10) || 1);
			var max = Math.max(0, slides.length - show);
			prev.disabled = index <= 0;
			next.disabled = index >= max;
		}

		function renderDots() {
			if (!dotsWrap) {
				return;
			}
			dotsWrap.innerHTML = '';
			var bp = getBreakpoint(config);
			var show = Math.max(1, parseInt(bp.show, 10) || 1);
			pageCount = Math.max(1, slides.length - show + 1);
			for (var d = 0; d < pageCount; d++) {
				(function (page) {
					var btn = document.createElement('button');
					btn.type = 'button';
					btn.className = 'ta-carousel__dot' + (page === index ? ' is-active' : '');
					btn.setAttribute('aria-label', 'Slide ' + (page + 1));
					btn.addEventListener('click', function () {
						goTo(page, true);
						restartAutoplay();
					});
					dotsWrap.appendChild(btn);
				})(d);
			}
		}

		function updateDots() {
			if (!dotsWrap) {
				return;
			}
			var dots = dotsWrap.querySelectorAll('.ta-carousel__dot');
			dots.forEach(function (dot, i) {
				dot.classList.toggle('is-active', i === index);
			});
		}

		function nextSlide() {
			goTo(index + 1, true);
		}

		function prevSlide() {
			goTo(index - 1, true);
		}

		function startAutoplay() {
			stopAutoplay();
			if (!config.autoplay) {
				return;
			}
			timer = window.setInterval(nextSlide, config.autoplaySpeed || 4000);
		}

		function stopAutoplay() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		function restartAutoplay() {
			stopAutoplay();
			startAutoplay();
		}

		if (prev) {
			prev.addEventListener('click', function () {
				prevSlide();
				restartAutoplay();
			});
		}
		if (next) {
			next.addEventListener('click', function () {
				nextSlide();
				restartAutoplay();
			});
		}

		if (config.pauseOnHover && config.autoplay) {
			root.addEventListener('mouseenter', stopAutoplay);
			root.addEventListener('mouseleave', startAutoplay);
		}

		var resizeTimer;
		window.addEventListener('resize', function () {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(layout, 150);
		});

		layout();
		startAutoplay();
	}

	function boot() {
		document.querySelectorAll('.ta-carousel').forEach(initCarousel);
	}

	function bindElementor() {
		if (!window.elementorFrontend || !elementorFrontend.hooks) {
			return;
		}
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/tools-adapter-product-carousel.default',
			function ($scope) {
				var el = $scope[0] ? $scope[0].querySelector('.ta-carousel') : null;
				if (el) {
					el.dataset.taReady = '0';
					initCarousel(el);
				}
			}
		);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		bindElementor();
	} else if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', bindElementor);
	}
})();
