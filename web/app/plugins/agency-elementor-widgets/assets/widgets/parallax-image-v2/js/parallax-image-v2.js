/**
 * Parallax Image V2 — horizontal pan on the image band.
 * As the band scrolls through the viewport, the background image translates
 * so the visible portion pans from the right side → left side of the photo.
 *
 * Pure scroll-progress driver via rAF; one shared scroll/resize listener for
 * all instances. Respects prefers-reduced-motion.
 */
(function () {
	'use strict';

	var prefersReducedMotion =
		window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	var instances = [];
	var ticking = false;

	function update() {
		ticking = false;
		var viewportH = window.innerHeight || document.documentElement.clientHeight;

		for (var i = 0; i < instances.length; i++) {
			var band = instances[i];
			var rect = band.getBoundingClientRect();

			// Progress: 0 when the band's top edge sits just below the viewport,
			// 1 when the band's bottom edge sits just above the viewport.
			var travel = viewportH + rect.height;
			var passed = viewportH - rect.top;
			var progress = passed / travel;

			if (progress < 0) progress = 0;
			else if (progress > 1) progress = 1;

			// Map 0→1 of scroll progress to full 0%→100% of background-position-x.
			band.style.setProperty('--aew-pimg-pan', progress.toFixed(4));
		}
	}

	function onScrollOrResize() {
		if (ticking) return;
		ticking = true;
		window.requestAnimationFrame(update);
	}

	function initWidget(el) {
		if (!el || el.dataset.aewPimgInit === '1') return; // idempotency guard
		el.dataset.aewPimgInit = '1';

		if (prefersReducedMotion) return;

		var band = el.querySelector('.aew-pimg__band');
		if (!band) return;

		instances.push(band);

		if (instances.length === 1) {
			window.addEventListener('scroll', onScrollOrResize, { passive: true });
			window.addEventListener('resize', onScrollOrResize);
		}
		update();
	}

	function boot() {
		document.querySelectorAll('[data-aew-parallax-image-v2]').forEach(initWidget);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	// Re-init when Elementor editor re-renders the widget.
	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') return;
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/agency-parallax-image-v2.default',
				function ($scope) {
					var el = $scope[0] && $scope[0].querySelector('[data-aew-parallax-image-v2]');
					if (el) initWidget(el);
				}
			);
		});
	}
})();
