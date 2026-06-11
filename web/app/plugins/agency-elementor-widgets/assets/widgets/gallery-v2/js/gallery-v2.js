/* Gallery V2 — auto-reveal hidden grid items on scroll (infinite scroll). */
(function () {
	'use strict';

	function initWidget(el) {
		if (!el || el.dataset.aewGalv2Init === '1') {
			return; // idempotency guard
		}
		el.dataset.aewGalv2Init = '1';

		var grid = el.querySelector('.aew-galv2__grid');
		var sentinel = el.querySelector('.aew-galv2__sentinel');
		if (!grid) {
			return;
		}

		var batch = parseInt(grid.getAttribute('data-batch'), 10);
		if (isNaN(batch) || batch < 1) {
			batch = 6;
		}

		function hiddenItems() {
			return el.querySelectorAll('.aew-galv2__item--hidden');
		}

		function revealAll() {
			var hidden = hiddenItems();
			for (var i = 0; i < hidden.length; i++) {
				hidden[i].classList.remove('aew-galv2__item--hidden');
			}
		}

		function revealNextBatch() {
			var hidden = hiddenItems();
			var count = Math.min(batch, hidden.length);
			for (var i = 0; i < count; i++) {
				hidden[i].classList.remove('aew-galv2__item--hidden');
			}
			return hiddenItems().length;
		}

		// Nothing to reveal.
		if (hiddenItems().length === 0) {
			return;
		}

		// No sentinel or no observer support: reveal everything up front.
		if (!sentinel || typeof window.IntersectionObserver === 'undefined') {
			revealAll();
			return;
		}

		var observer = new IntersectionObserver(function (entries) {
			for (var k = 0; k < entries.length; k++) {
				if (entries[k].isIntersecting) {
					var remaining = revealNextBatch();
					if (remaining === 0) {
						observer.disconnect();
					}
				}
			}
		}, { root: null, rootMargin: '200px 0px', threshold: 0 });

		observer.observe(sentinel);
	}

	function boot() {
		document.querySelectorAll('[data-aew-gallery-v2]').forEach(initWidget);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	// Re-init when Elementor editor re-renders the widget.
	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') {
				return;
			}
			elementorFrontend.hooks.addAction('frontend/element_ready/agency-gallery-v2.default', function ($scope) {
				var el = $scope[0] && $scope[0].querySelector('[data-aew-gallery-v2]');
				if (el) {
					initWidget(el);
				}
			});
		});
	}
})();
