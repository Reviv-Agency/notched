/* ════════════════════════════════════════════════════════════════════════════
   Sticky Image (Notched) — agency-sticky-image
   Two jobs:
   1. Fixed mode: Elementor often wraps widgets in containers that have a CSS
      `transform` (entrance animations, motion effects), which makes
      `position: fixed` resolve against that ancestor instead of the viewport.
      We re-parent fixed badges to <body> so they truly pin to the viewport.
   2. Spin on scroll: rotate the image proportionally to the scroll position.
      Transform-only + rAF-throttled, and disabled for prefers-reduced-motion.
   ════════════════════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var prefersReducedMotion =
    window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function initSpin(el) {
    if (el.dataset.aewStimSpin !== '1') return;
    if (prefersReducedMotion) return;

    var img = el.querySelector('.aew-stim__img');
    if (!img) return;

    var speed = parseFloat(el.dataset.aewStimSpinSpeed || '0.6');
    var dir = parseFloat(el.dataset.aewStimSpinDir || '1');
    if (!isFinite(speed)) speed = 0.6;
    if (dir !== -1) dir = 1;

    // Hint the compositor; rotation is transform-only so it stays off the main
    // layout/paint path.
    img.style.willChange = 'transform';

    var ticking = false;

    function apply() {
      var angle = window.scrollY * speed * dir;
      img.style.transform = 'rotate(' + angle + 'deg)';
      ticking = false;
    }

    function onScroll() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(apply);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    apply(); // set initial angle for the current scroll position
  }

  function initWidget(el) {
    if (!el || el.dataset.aewStickyImageInit === '1') return;
    el.dataset.aewStickyImageInit = '1';

    // The badge is position:fixed. Re-parent it to <body> so it pins to the
    // viewport rather than a transformed Elementor ancestor (entrance
    // animations / motion effects create a `transform` that would otherwise
    // become its containing block). Skip inside the editor — re-parenting would
    // detach it from the canvas and break editing.
    if (
      !document.body.classList.contains('elementor-editor-active') &&
      el.parentNode !== document.body
    ) {
      document.body.appendChild(el);
    }

    initSpin(el);
  }

  function boot() {
    document.querySelectorAll('[data-aew-sticky-image]').forEach(initWidget);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  // Re-init when Elementor re-renders the widget (editor preview).
  if (typeof window.jQuery !== 'undefined') {
    window.jQuery(window).on('elementor/frontend/init', function () {
      if (typeof elementorFrontend === 'undefined') return;
      elementorFrontend.hooks.addAction(
        'frontend/element_ready/agency-sticky-image.default',
        function ($scope) {
          var el = $scope[0] && $scope[0].querySelector('[data-aew-sticky-image]');
          if (el) initWidget(el);
        }
      );
    });
  }
})();
