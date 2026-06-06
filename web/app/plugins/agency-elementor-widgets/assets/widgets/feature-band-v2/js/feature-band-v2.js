/**
 * Feature Band V2 — vertical parallax on the full-bleed background image.
 * As the band scrolls through the viewport, the image pans within its (slightly
 * oversized) height by driving --aew-fbv2-pan (0→1) on background-position-y.
 *
 * Pure scroll-progress driver via rAF (no work off the main path). Only runs on
 * instances that opted in (.aew-fbv2--parallax). Respects prefers-reduced-motion.
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

			// Progress: 0 when the band's top edge is just entering from the
			// bottom of the viewport, 1 when its bottom edge has just left the top.
			var travel = viewportH + rect.height;
			var passed = viewportH - rect.top;
			var progress = passed / travel;

			if (progress < 0) progress = 0;
			else if (progress > 1) progress = 1;

			band.style.setProperty('--aew-fbv2-pan', progress.toFixed(4));
		}
	}

	function onScrollOrResize() {
		if (ticking) return;
		ticking = true;
		window.requestAnimationFrame(update);
	}

	function initBand(band) {
		if (!band || band.dataset.aewFbv2Init === '1') return;
		band.dataset.aewFbv2Init = '1';

		if (prefersReducedMotion) return;
		// Only parallax instances opted in via the control.
		if (!band.classList.contains('aew-fbv2--parallax')) return;

		instances.push(band);

		if (instances.length === 1) {
			window.addEventListener('scroll', onScrollOrResize, { passive: true });
			window.addEventListener('resize', onScrollOrResize);
		}
		update();
	}

	function boot() {
		document.querySelectorAll('[data-aew-feature-band-v2]').forEach(initBand);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') return;
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/agency-feature-band-v2.default',
				function ($scope) {
					var b = $scope[0] && $scope[0].querySelector('[data-aew-feature-band-v2]');
					if (b) initBand(b);
				}
			);
		});
	}
})();
