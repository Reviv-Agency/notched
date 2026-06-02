/**
 * Footer V2 — horizontal parallax on the hero image.
 * As the footer hero scrolls through the viewport, the image translates
 * left so the visible portion pans from right side → left side of the photo.
 *
 * Pure scroll-progress driver via rAF; no listeners on scroll itself.
 * Respects prefers-reduced-motion.
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
			var hero = instances[i];
			var rect = hero.getBoundingClientRect();

			// Progress: 0 when hero's top edge sits just below the viewport,
			// 1 when hero's bottom edge sits just above the viewport.
			var travel = viewportH + rect.height;
			var passed = viewportH - rect.top;
			var progress = passed / travel;

			if (progress < 0) progress = 0;
			else if (progress > 1) progress = 1;

			// Map 0→1 of scroll progress to full 0%→100% of object-position
			// so the pan range is obvious. (Tighten to 0.25→0.75 if too much.)
			var pan = progress;

			hero.style.setProperty('--aew-fov2-pan', pan.toFixed(4));
		}
	}

	function onScrollOrResize() {
		if (ticking) return;
		ticking = true;
		window.requestAnimationFrame(update);
	}

	function initFooter(footer) {
		if (!footer || footer.dataset.aewFov2Init === '1') return;
		footer.dataset.aewFov2Init = '1';

		if (prefersReducedMotion) return;

		var hero = footer.querySelector('.aew-fov2__hero');
		if (!hero) return;

		instances.push(hero);

		if (instances.length === 1) {
			window.addEventListener('scroll', onScrollOrResize, { passive: true });
			window.addEventListener('resize', onScrollOrResize);
		}
		update();
	}

	function boot() {
		document.querySelectorAll('[data-aew-footer-v2]').forEach(initFooter);
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
				'frontend/element_ready/agency-footer-v2.default',
				function ($scope) {
					var f = $scope[0] && $scope[0].querySelector('[data-aew-footer-v2]');
					if (f) initFooter(f);
				}
			);
		});
	}
})();
