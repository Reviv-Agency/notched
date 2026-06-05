(function () {
	'use strict';

	function initHeader(header) {
		if (!header || header.dataset.aewHv2Init === '1') return;
		header.dataset.aewHv2Init = '1';

		var toggle      = header.querySelector('.aew-hv2__toggle');
		var overlay     = header.querySelector('.aew-hv2__overlay');
		var closeBtn    = header.querySelector('.aew-hv2__drawer-close');
		var drawer      = header.querySelector('.aew-hv2__drawer');
		var closeOnClick = header.getAttribute('data-close-on-click') === '1';
		var isAnimating  = false;

		if (!toggle || !overlay) return;

		// Always keep overlay in DOM — use class to show/hide instead of hidden attr
		overlay.removeAttribute('hidden');

		function open() {
			if (isAnimating) return;
			header.classList.add('is-open');
			toggle.setAttribute('aria-expanded', 'true');
			document.body.classList.add('aew-hv2-open');
		}

		function close() {
			if (isAnimating) return;
			isAnimating = true;
			header.classList.remove('is-open');
			toggle.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('aew-hv2-open');

			// Reset animating flag after transition
			if (drawer) {
				drawer.addEventListener('transitionend', function handler() {
					isAnimating = false;
					drawer.removeEventListener('transitionend', handler);
				});
			} else {
				setTimeout(function () { isAnimating = false; }, 400);
			}
		}

		toggle.addEventListener('click', function () {
			header.classList.contains('is-open') ? close() : open();
		});

		if (closeBtn) closeBtn.addEventListener('click', close);

		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) close();
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && header.classList.contains('is-open')) close();
		});

		if (closeOnClick) {
			overlay.querySelectorAll('.aew-hv2__drawer-nav a').forEach(function (a) {
				a.addEventListener('click', close);
			});
		}

		// ── Smart sticky: hide on scroll-down, reveal on scroll-up ──────────────
		// So the menu is always reachable with a small upward scroll instead of
		// returning to the very top of the page.
		(function initSmartSticky() {
			var lastY = window.pageYOffset || document.documentElement.scrollTop || 0;
			var ticking = false;
			var REVEAL_AT_TOP = 8;   // always show within this many px of the top
			var DELTA = 6;           // ignore tiny jitters

			function update() {
				ticking = false;
				var y = window.pageYOffset || document.documentElement.scrollTop || 0;

				// Near the top: always show.
				if (y <= REVEAL_AT_TOP) {
					header.classList.remove('aew-hv2--bar-hidden');
					lastY = y;
					return;
				}

				// Keep the bar visible while the drawer is open.
				if (header.classList.contains('is-open')) {
					lastY = y;
					return;
				}

				var diff = y - lastY;
				if (Math.abs(diff) < DELTA) return; // too small to act on

				if (diff > 0) {
					// scrolling down → hide
					header.classList.add('aew-hv2--bar-hidden');
				} else {
					// scrolling up → show
					header.classList.remove('aew-hv2--bar-hidden');
				}
				lastY = y;
			}

			window.addEventListener('scroll', function () {
				if (!ticking) {
					ticking = true;
					window.requestAnimationFrame(update);
				}
			}, { passive: true });
		})();

		// Accordion sub-menu toggles
		overlay.querySelectorAll('.aew-hv2__drawer-toggle').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				var expanded = btn.getAttribute('aria-expanded') === 'true';
				var subId    = btn.getAttribute('aria-controls');
				var sub      = subId ? document.getElementById(subId) : null;

				// Close all other open submenus
				overlay.querySelectorAll('.aew-hv2__drawer-toggle[aria-expanded="true"]').forEach(function (other) {
					if (other === btn) return;
					other.setAttribute('aria-expanded', 'false');
					var otherId  = other.getAttribute('aria-controls');
					var otherSub = otherId ? document.getElementById(otherId) : null;
					if (otherSub) otherSub.setAttribute('hidden', '');
				});

				if (expanded) {
					btn.setAttribute('aria-expanded', 'false');
					if (sub) sub.setAttribute('hidden', '');
				} else {
					btn.setAttribute('aria-expanded', 'true');
					if (sub) sub.removeAttribute('hidden');
				}
			});
		});
	}

	function boot() {
		document.querySelectorAll('[data-aew-header-v2]').forEach(initHeader);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') return;
			elementorFrontend.hooks.addAction('frontend/element_ready/agency-header-v2.default', function ($scope) {
				var h = $scope[0] && $scope[0].querySelector('[data-aew-header-v2]');
				if (h) initHeader(h);
			});
		});
	}
})();
