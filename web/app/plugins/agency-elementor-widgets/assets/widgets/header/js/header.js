(function () {
	'use strict';

	var TABLET_DEVICES = [ 'tablet', 'tablet_extra', 'mobile', 'mobile_extra' ];

	function getBreakpoint(header) {
		var raw = header.style.getPropertyValue('--aew-header-bp') || '1024';
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

	function syncAdminBarOffset(header) {
		var adminBar = document.getElementById('wpadminbar');
		var offset = 0;

		if (adminBar && document.body.classList.contains('admin-bar')) {
			offset = adminBar.offsetHeight || 0;
		}

		header.style.setProperty('--aew-admin-bar-offset', offset + 'px');
	}

	function syncCompactLayout(header) {
		var bp = getBreakpoint(header);
		var mq = window.matchMedia('(max-width: ' + bp + 'px)');

		function apply() {
			var isCompact = mq.matches || isElementorCompactDevice();
			header.classList.toggle('aew-header--compact', isCompact);

			if (!isCompact && header.classList.contains('is-menu-open')) {
				var toggle = header.querySelector('.aew-header__toggle');
				var overlay = header.querySelector('.aew-header__overlay');
				header.classList.remove('is-menu-open');
				if (toggle) {
					toggle.setAttribute('aria-expanded', 'false');
				}
				if (overlay) {
					overlay.setAttribute('hidden', '');
				}
				document.body.classList.remove('aew-header-menu-open');
			}
		}

		apply();

		if (typeof mq.addEventListener === 'function') {
			mq.addEventListener('change', apply);
		} else if (typeof mq.addListener === 'function') {
			mq.addListener(apply);
		}

		if (!header.dataset.aewCompactObserved) {
			header.dataset.aewCompactObserved = '1';
			var observer = new MutationObserver(apply);
			observer.observe(document.body, {
				attributes: true,
				attributeFilter: [ 'class', 'data-elementor-device-mode' ],
			});
		}
	}

	function initHeader(header) {
		if (!header) {
			return;
		}

		syncAdminBarOffset(header);
		syncCompactLayout(header);

		if (header.dataset.aewInit === '1') {
			return;
		}
		header.dataset.aewInit = '1';

		var toggle = header.querySelector('.aew-header__toggle');
		var overlay = header.querySelector('.aew-header__overlay');
		var closeBtn = header.querySelector('.aew-header__close');
		var closeOnClick = header.getAttribute('data-close-on-click') === '1';

		if (!toggle || !overlay) {
			return;
		}

		function openMenu() {
			syncAdminBarOffset(header);
			header.classList.add('is-menu-open');
			toggle.setAttribute('aria-expanded', 'true');
			overlay.removeAttribute('hidden');
			document.body.classList.add('aew-header-menu-open');
		}

		function closeMenu() {
			header.classList.remove('is-menu-open');
			toggle.setAttribute('aria-expanded', 'false');
			overlay.setAttribute('hidden', '');
			document.body.classList.remove('aew-header-menu-open');
		}

		window.addEventListener('resize', function () {
			syncAdminBarOffset(header);
		});

		toggle.addEventListener('click', function () {
			if (header.classList.contains('is-menu-open')) {
				closeMenu();
			} else {
				openMenu();
			}
		});

		if (closeBtn) {
			closeBtn.addEventListener('click', closeMenu);
		}

		overlay.addEventListener('click', function (event) {
			if (event.target === overlay) {
				closeMenu();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && header.classList.contains('is-menu-open')) {
				closeMenu();
			}
		});

		if (closeOnClick) {
			overlay.querySelectorAll('a').forEach(function (link) {
				link.addEventListener('click', closeMenu);
			});
		}
	}

	function boot() {
		document.querySelectorAll('[data-aew-header]').forEach(initHeader);
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

		elementorFrontend.hooks.addAction('frontend/element_ready/agency-header.default', function ($scope) {
			var el = $scope[0];
			if (el) {
				var header = el.querySelector('[data-aew-header]') || el.closest('[data-aew-header]');
				if (header) {
					initHeader(header);
				}
			}
		});
	}

	var jq = window.jQuery;
	if (jq) {
		jq(window).on('elementor/frontend/init', registerElementorHooks);
	}
})();
