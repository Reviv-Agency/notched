(function () {
	'use strict';

	var TABLET_DEVICES = [ 'tablet', 'tablet_extra', 'mobile', 'mobile_extra' ];

	function getBreakpoint(root) {
		var raw = root.style.getPropertyValue('--aew-split-media-bp') || '1024';
		var bp = parseInt(String(raw).replace(/px$/, ''), 10);

		return bp > 0 ? bp : 1024;
	}

	function isElementorCompactDevice() {
		var body = document.body;
		if (!body) {
			return false;
		}

		var mode = body.getAttribute('data-elementor-device-mode');
		if (mode && TABLET_DEVICES.indexOf(mode) !== -1) {
			return true;
		}

		return TABLET_DEVICES.some(function (device) {
			return body.classList.contains('elementor-device-' + device);
		});
	}

	function syncCompactLayout(root) {
		var bp = getBreakpoint(root);
		var mq = window.matchMedia('(max-width: ' + bp + 'px)');

		function apply() {
			var isCompact = mq.matches || isElementorCompactDevice();
			root.classList.toggle('aew-split-media--compact', isCompact);
		}

		apply();

		if (typeof mq.addEventListener === 'function') {
			mq.addEventListener('change', apply);
		} else if (typeof mq.addListener === 'function') {
			mq.addListener(apply);
		}

		if (!root.dataset.aewHomeStoryCompactObserved) {
			root.dataset.aewHomeStoryCompactObserved = '1';
			var observer = new MutationObserver(apply);
			observer.observe(document.body, {
				attributes: true,
				attributeFilter: [ 'class', 'data-elementor-device-mode' ],
			});
		}
	}

	function initSplitMedia(root) {
		if (!root) {
			return;
		}

		syncCompactLayout(root);
	}

	function boot() {
		document.querySelectorAll('[data-aew-split-media]').forEach(initSplitMedia);
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

		elementorFrontend.hooks.addAction('frontend/element_ready/agency-split-media.default', function ($scope) {
			var el = $scope[0];
			if (el) {
				var root = el.querySelector('[data-aew-split-media]') || el.closest('[data-aew-split-media]');
				if (root) {
					initSplitMedia(root);
				}
			}
		});
	}

	var jq = window.jQuery;
	if (jq) {
		jq(window).on('elementor/frontend/init', registerElementorHooks);
	}
})();
