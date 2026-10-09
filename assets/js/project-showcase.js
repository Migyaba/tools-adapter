/**
 * Tools Adapter — Project Showcase Gallery Widget Scripts
 */
(function ($) {
	'use strict';

	function initProjectShowcase($scope) {
		var $wrapper = $scope.find('.ta-project-showcase');
		if (!$wrapper.length) {
			return;
		}

		var $mainViewer = $wrapper.find('.ta-ps-main-viewer');
		var $mainImg = $wrapper.find('.ta-ps-main-img');
		var $counterBadge = $wrapper.find('.ta-ps-counter-badge');
		var $btnPrev = $wrapper.find('.ta-ps-btn-prev');
		var $btnNext = $wrapper.find('.ta-ps-btn-next');
		var $btnFullscreen = $wrapper.find('.ta-ps-btn-fullscreen');
		var $thumbsStrip = $wrapper.find('.ta-ps-thumbs-strip');
		var $thumbItems = $wrapper.find('.ta-ps-thumb-item');
		var $progressBar = $wrapper.find('.ta-ps-progress-bar-fill');
		var $btnDiaporama = $wrapper.find('.ta-ps-footer-right');

		// Lightbox elements
		var $lightbox = $wrapper.find('.ta-ps-lightbox-modal');
		var $lbImg = $lightbox.find('.ta-ps-lb-img');
		var $lbCounter = $lightbox.find('.ta-ps-lb-counter');
		var $lbBtnClose = $lightbox.find('.ta-ps-lb-close-btn');
		var $lbBtnPrev = $lightbox.find('.ta-ps-lb-prev');
		var $lbBtnNext = $lightbox.find('.ta-ps-lb-next');

		// Options from data attributes
		var autoplay = $wrapper.data('autoplay') === true || $wrapper.data('autoplay') === 'true';
		var autoplaySpeed = parseInt($wrapper.data('autoplay-speed'), 10) || 4500;
		var pauseOnHover = $wrapper.data('pause-hover') === true || $wrapper.data('pause-hover') === 'true';
		var counterPattern = $wrapper.data('counter-pattern') || 'Photo {current} / {total}';

		var items = [];
		$thumbItems.each(function (i) {
			var $t = $(this);
			items.push({
				index: i,
				src: $t.data('src') || $t.find('img').attr('src'),
				alt: $t.find('img').attr('alt') || ('Photo ' + (i + 1))
			});
		});

		var total = items.length;
		if (total === 0) {
			return;
		}

		var currentIndex = 0;
		var timer = null;

		function getCounterText(idx) {
			return counterPattern
				.replace('{current}', idx + 1)
				.replace('{total}', total);
		}

		function updateStage(idx, isInitial) {
			if (idx < 0) idx = total - 1;
			if (idx >= total) idx = 0;
			currentIndex = idx;

			var item = items[currentIndex];

			// Smooth image switch
			if (!isInitial) {
				$mainImg.css('opacity', '0.4');
				setTimeout(function () {
					$mainImg.attr('src', item.src);
					$mainImg.attr('alt', item.alt);
					$mainImg.css('opacity', '1');
				}, 120);
			} else {
				$mainImg.attr('src', item.src);
				$mainImg.attr('alt', item.alt);
			}

			// Update counter
			$counterBadge.text(getCounterText(currentIndex));

			// Update thumbs
			$thumbItems.removeClass('is-active');
			var $activeThumb = $thumbItems.eq(currentIndex);
			$activeThumb.addClass('is-active');

			// Scroll active thumb into view horizontally
			if ($thumbsStrip.length && $activeThumb.length) {
				var stripElem = $thumbsStrip[0];
				var thumbElem = $activeThumb[0];
				var scrollLeftTarget = thumbElem.offsetLeft - (stripElem.clientWidth / 2) + (thumbElem.clientWidth / 2);
				$thumbsStrip.stop().animate({ scrollLeft: scrollLeftTarget }, 300);
			}

			// Update progress bar
			if ($progressBar.length) {
				var progressPercent = ((currentIndex + 1) / total) * 100;
				$progressBar.css('width', progressPercent + '%');
			}
		}

		function next() {
			updateStage(currentIndex + 1);
		}

		function prev() {
			updateStage(currentIndex - 1);
		}

		// Lightbox methods
		function updateLightbox(idx) {
			if (idx < 0) idx = total - 1;
			if (idx >= total) idx = 0;
			currentIndex = idx;
			updateStage(currentIndex);

			var item = items[currentIndex];
			$lbImg.css('opacity', '0.3');
			setTimeout(function () {
				$lbImg.attr('src', item.src);
				$lbImg.attr('alt', item.alt);
				$lbImg.css('opacity', '1');
			}, 100);

			$lbCounter.text(getCounterText(currentIndex));
		}

		function openLightbox(idx) {
			updateLightbox(idx);
			$lightbox.addClass('is-open');
			$('body').css('overflow', 'hidden');
		}

		function closeLightbox() {
			$lightbox.removeClass('is-open');
			$('body').css('overflow', '');
		}

		// Controls click bindings
		$btnNext.off('click').on('click', function (e) {
			e.stopPropagation();
			next();
		});

		$btnPrev.off('click').on('click', function (e) {
			e.stopPropagation();
			prev();
		});

		$mainViewer.off('click').on('click', function () {
			openLightbox(currentIndex);
		});

		$btnFullscreen.off('click').on('click', function (e) {
			e.stopPropagation();
			openLightbox(currentIndex);
		});

		$btnDiaporama.off('click').on('click', function (e) {
			e.preventDefault();
			openLightbox(0);
		});

		$thumbItems.off('click').on('click', function (e) {
			e.preventDefault();
			var idx = $(this).data('index');
			updateStage(idx);
		});

		// Lightbox controls
		$lbBtnClose.off('click').on('click', function () {
			closeLightbox();
		});

		$lbBtnNext.off('click').on('click', function (e) {
			e.stopPropagation();
			updateLightbox(currentIndex + 1);
		});

		$lbBtnPrev.off('click').on('click', function (e) {
			e.stopPropagation();
			updateLightbox(currentIndex - 1);
		});

		$lightbox.off('click').on('click', function (e) {
			if ($(e.target).is('.ta-ps-lb-body, .ta-ps-lb-image-container, .ta-ps-lightbox-modal')) {
				closeLightbox();
			}
		});

		// Keyboard controls
		$(document).off('keydown.taPS_' + $scope.data('id')).on('keydown.taPS_' + $scope.data('id'), function (e) {
			if ($lightbox.hasClass('is-open')) {
				if (e.key === 'Escape' || e.keyCode === 27) {
					closeLightbox();
				} else if (e.key === 'ArrowRight' || e.keyCode === 39) {
					updateLightbox(currentIndex + 1);
				} else if (e.key === 'ArrowLeft' || e.keyCode === 37) {
					updateLightbox(currentIndex - 1);
				}
			}
		});

		// Touch swipe support
		var touchStartX = 0;
		var touchEndX = 0;

		$mainViewer.on('touchstart', function (e) {
			touchStartX = e.originalEvent.changedTouches[0].screenX;
		});

		$mainViewer.on('touchend', function (e) {
			touchEndX = e.originalEvent.changedTouches[0].screenX;
			handleSwipe();
		});

		$lightbox.on('touchstart', function (e) {
			touchStartX = e.originalEvent.changedTouches[0].screenX;
		});

		$lightbox.on('touchend', function (e) {
			touchEndX = e.originalEvent.changedTouches[0].screenX;
			handleSwipe(true);
		});

		function handleSwipe(isLb) {
			var diff = touchEndX - touchStartX;
			if (Math.abs(diff) > 40) {
				if (diff < 0) {
					// Swipe left -> Next
					isLb ? updateLightbox(currentIndex + 1) : next();
				} else {
					// Swipe right -> Prev
					isLb ? updateLightbox(currentIndex - 1) : prev();
				}
			}
		}

		// Autoplay
		function startAutoplay() {
			if (autoplay && !timer) {
				timer = setInterval(next, autoplaySpeed);
			}
		}

		function stopAutoplay() {
			if (timer) {
				clearInterval(timer);
				timer = null;
			}
		}

		if (autoplay) {
			startAutoplay();
			if (pauseOnHover) {
				$wrapper.on('mouseenter', stopAutoplay).on('mouseleave', startAutoplay);
			}
		}

		// Initialize stage
		updateStage(0, true);
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/tools-adapter-project-showcase.default',
			initProjectShowcase
		);
	});

	$(document).ready(function () {
		$('.ta-project-showcase-container').each(function () {
			initProjectShowcase($(this));
		});
	});
})(jQuery);
