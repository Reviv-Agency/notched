(function () {
	'use strict';

	var MOBILE_EDITOR_DEVICES = [ 'mobile', 'mobile_extra' ];

	function getBreakpoint(hero) {
		var raw = hero.style.getPropertyValue('--aew-hero-bp') || '768';
		var bp = parseInt(String(raw).replace(/px$/, ''), 10);

		return bp > 0 ? bp : 768;
	}

	function isElementorCompactDevice() {
		var body = document.body;
		if (!body) {
			return false;
		}

		var mode = body.getAttribute('data-elementor-device-mode');
		if (mode && MOBILE_EDITOR_DEVICES.indexOf(mode) !== -1) {
			return true;
		}

		return MOBILE_EDITOR_DEVICES.some(function (device) {
			return body.classList.contains('elementor-device-' + device);
		});
	}

	function syncCompactLayout(hero) {
		var bp = getBreakpoint(hero);
		var mq = window.matchMedia('(max-width: ' + bp + 'px)');

		function apply() {
			var isCompact = mq.matches || isElementorCompactDevice();
			hero.classList.toggle('aew-hero--compact', isCompact);
		}

		apply();

		if (typeof mq.addEventListener === 'function') {
			mq.addEventListener('change', apply);
		} else if (typeof mq.addListener === 'function') {
			mq.addListener(apply);
		}

		if (!hero.dataset.aewHeroCompactObserved) {
			hero.dataset.aewHeroCompactObserved = '1';
			var observer = new MutationObserver(apply);
			observer.observe(document.body, {
				attributes: true,
				attributeFilter: [ 'class', 'data-elementor-device-mode' ],
			});
		}
	}

	function initHero(hero) {
		if (!hero) {
			return;
		}

		syncCompactLayout(hero);
	}

	function boot() {
		document.querySelectorAll('[data-aew-hero]').forEach(initHero);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	function registerElementorHooks() {
		if (
			typeof elementorFrontend === 'undefined' ||
			!elementorFrontend.hooks ||
			typeof elementorFrontend.hooks.addAction !== 'function'
		) {
			return;
		}

		elementorFrontend.hooks.addAction('frontend/element_ready/agency-hero.default', function ($scope) {
			var el = $scope[0];
			if (el) {
				var hero = el.querySelector('[data-aew-hero]') || el.closest('[data-aew-hero]');
				if (hero) {
					initHero(hero);
				}
			}
		});
	}

	var jq = window.jQuery;
	if (jq) {
		jq(window).on('elementor/frontend/init', registerElementorHooks);
	}
})();
