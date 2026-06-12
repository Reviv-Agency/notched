/* Gallery V2 — reveal hidden grid items in batches via a Load More button. */
(function () {
	'use strict';

	function initWidget(el) {
		if (!el || el.dataset.aewGalv2Init === '1') {
			return; // idempotency guard
		}
		el.dataset.aewGalv2Init = '1';

		var grid = el.querySelector('.aew-galv2__grid');
		var moreBtn = el.querySelector('[data-aew-galv2-more]');
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

		function hideButton() {
			var wrap = el.querySelector('.aew-galv2__more-wrap');
			if (wrap) {
				wrap.hidden = true;
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
			hideButton();
			return;
		}

		if (!moreBtn) {
			return;
		}

		moreBtn.addEventListener('click', function () {
			var remaining = revealNextBatch();
			if (remaining === 0) {
				hideButton();
			}
		});
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
