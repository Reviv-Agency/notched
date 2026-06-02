/* Gallery V2 (Notched) — reveal hidden grid items on Load More. */
(function () {
	'use strict';

	function initWidget(el) {
		if (!el || el.dataset.aewGalv2Init === '1') {
			return; // idempotency guard
		}
		el.dataset.aewGalv2Init = '1';

		var button = el.querySelector('.aew-galv2__more');
		if (!button) {
			return;
		}

		function syncButton() {
			var hidden = el.querySelectorAll('.aew-galv2__item--hidden');
			if (hidden.length === 0) {
				button.setAttribute('hidden', '');
				button.setAttribute('disabled', 'disabled');
			}
		}

		button.addEventListener('click', function () {
			var hidden = el.querySelectorAll('.aew-galv2__item--hidden');
			for (var i = 0; i < hidden.length; i++) {
				hidden[i].classList.remove('aew-galv2__item--hidden');
			}
			syncButton();
		});

		// In case markup already shows everything (e.g. editor re-render).
		syncButton();
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
