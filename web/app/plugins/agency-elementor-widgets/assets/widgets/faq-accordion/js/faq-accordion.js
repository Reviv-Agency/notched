(function () {
	'use strict';

	var DURATION_MS = 380;

	function prefersReducedMotion() {
		return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function getPanel(item) {
		return item ? item.querySelector('.aew-faq-accordion__panel') : null;
	}

	function getTrigger(item) {
		return item ? item.querySelector('.aew-faq-accordion__trigger') : null;
	}

	function clearPanelTimer(panel) {
		if (panel && panel._aewCloseTimer) {
			window.clearTimeout(panel._aewCloseTimer);
			panel._aewCloseTimer = null;
		}
	}

	function closeItem(item, instant) {
		if (!item) {
			return;
		}

		var panel = getPanel(item);
		var trigger = getTrigger(item);

		clearPanelTimer(panel);
		item.classList.remove('is-open');

		if (trigger) {
			trigger.setAttribute('aria-expanded', 'false');
		}

		if (!panel) {
			return;
		}

		if (instant || prefersReducedMotion()) {
			panel.hidden = true;
			return;
		}

		panel._aewCloseTimer = window.setTimeout(function () {
			if (!item.classList.contains('is-open')) {
				panel.hidden = true;
			}

			panel._aewCloseTimer = null;
		}, DURATION_MS);
	}

	function openItem(item, instant) {
		if (!item) {
			return;
		}

		var panel = getPanel(item);
		var trigger = getTrigger(item);

		clearPanelTimer(panel);

		if (panel) {
			panel.hidden = false;
		}

		if (trigger) {
			trigger.setAttribute('aria-expanded', 'true');
		}

		if (instant || prefersReducedMotion()) {
			item.classList.add('is-open');
			return;
		}

		// Allow layout to settle after removing `hidden` before expanding.
		requestAnimationFrame(function () {
			requestAnimationFrame(function () {
				item.classList.add('is-open');
			});
		});
	}

	function initAccordion(root) {
		var accordion = root.querySelector('[data-aew-faq-accordion-list]');

		if (!accordion || accordion.dataset.aewFaqAccordionReady === '1') {
			return;
		}

		accordion.dataset.aewFaqAccordionReady = '1';

		var items = accordion.querySelectorAll('.aew-faq-accordion__item');
		var instant = prefersReducedMotion();

		items.forEach(function (item, index) {
			var trigger = getTrigger(item);
			var panel = getPanel(item);

			if (!trigger || !panel) {
				return;
			}

			var panelId = panel.id || 'aew-faq-accordion-panel-' + index;
			panel.id = panelId;
			trigger.setAttribute('aria-controls', panelId);

			if (item.classList.contains('is-open')) {
				openItem(item, instant);
			} else {
				closeItem(item, instant);
			}

			trigger.addEventListener('click', function () {
				var isOpen = item.classList.contains('is-open');

				items.forEach(function (other) {
					if (other !== item) {
						closeItem(other, instant);
					}
				});

				if (isOpen) {
					closeItem(item, instant);
				} else {
					openItem(item, instant);
				}
			});
		});
	}

	function boot() {
		document.querySelectorAll('[data-aew-faq-accordion]').forEach(initAccordion);
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

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/agency-faq-accordion.default',
			function ($scope) {
				var el = $scope[0];

				if (!el) {
					return;
				}

				var root =
					el.querySelector('[data-aew-faq-accordion]') ||
					el.closest('[data-aew-faq-accordion]');

				if (root) {
					initAccordion(root);
				}
			}
		);
	}

	var jq = window.jQuery;
	if (jq) {
		jq(window).on('elementor/frontend/init', registerElementorHooks);
	}
})();
