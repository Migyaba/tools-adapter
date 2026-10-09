/**
 * Tools Adapter — Lookbook : ouverture des vignettes produit au clic / clavier.
 */
(function () {
	'use strict';

	function closeAll(except) {
		document.querySelectorAll('.ta-lookbook__spot.is-open').forEach(function (spot) {
			if (spot !== except) {
				spot.classList.remove('is-open');
				spot.querySelector('.ta-lookbook__dot').setAttribute('aria-expanded', 'false');
			}
		});
	}

	document.addEventListener('click', function (event) {
		var dot = event.target.closest('.ta-lookbook__dot');
		if (dot) {
			var spot = dot.closest('.ta-lookbook__spot');
			var open = !spot.classList.contains('is-open');
			closeAll(spot);
			spot.classList.toggle('is-open', open);
			dot.setAttribute('aria-expanded', open ? 'true' : 'false');
			return;
		}
		if (!event.target.closest('.ta-lookbook__card')) {
			closeAll(null);
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeAll(null);
		}
	});
})();
