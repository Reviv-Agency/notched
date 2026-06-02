(function () {
	'use strict';

	var MOBILE_EDITOR_DEVICES = [ 'mobile', 'mobile_extra' ];

	function getBreakpoint(root) {
		var raw = root.style.getPropertyValue('--aew-feature-rows-bp') || '768';
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

	function syncCompactLayout(root) {
		var bp = getBreakpoint(root);
		var mq = window.matchMedia('(max-width: ' + bp + 'px)');

		function apply() {
			var isCompact = mq.matches || isElementorCompactDevice();
			root.classList.toggle('aew-feature-rows--compact', isCompact);
		}

		apply();

		if (typeof mq.addEventListener === 'function') {
			mq.addEventListener('change', apply);
		} else if (typeof mq.addListener === 'function') {
			mq.addListener(apply);
		}

		if (!root.dataset.aewServicesCompactObserved) {
			root.dataset.aewServicesCompactObserved = '1';
			var observer = new MutationObserver(apply);
			observer.observe(document.body, {
				attributes: true,
				attributeFilter: [ 'class', 'data-elementor-device-mode' ],
			});
		}
	}

	function initFeatureRows(root) {
		if (!root) {
			return;
		}

		syncCompactLayout(root);
	}

	function boot() {
		document.querySelectorAll('[data-aew-feature-rows]').forEach(initFeatureRows);
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

		elementorFrontend.hooks.addAction('frontend/element_ready/agency-feature-rows.default', function ($scope) {
			var el = $scope[0];
			if (el) {
				var root = el.querySelector('[data-aew-feature-rows]') || el.closest('[data-aew-feature-rows]');
				if (root) {
					initFeatureRows(root);
				}
			}
		});
	}

	var jq = window.jQuery;
	if (jq) {
		jq(window).on('elementor/frontend/init', registerElementorHooks);
	}
})();
