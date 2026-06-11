/* ════════════════════════════════════════════════════════════════════════════
   Sticky Image — agency-sticky-image
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

  // Find the hero block the badge should live inside: the first banner/hero on
  // the page. The badge is anchored to the bottom-left of THIS element, so it
  // scrolls away with the hero (no viewport drift) and only shows over the hero.
  function findHero() {
    // Prefer the Elementor WIDGET WRAPPER (no overflow clip) over the inner
    // hero element — the badge may overhang the hero's bottom edge, and the
    // inner .aew-hev2 / .aew-bhero__frame use overflow:hidden which would clip
    // it. The wrapper has the same box but no clip.
    var sel = [
      '.elementor-widget-agency-hero-v2',
      '.elementor-widget-agency-banner-hero-v2',
      '[class*="elementor-widget-agency-hero"]',
      '[class*="elementor-widget-agency-banner-hero"]'
    ];
    for (var i = 0; i < sel.length; i++) {
      var hero = document.querySelector(sel[i]);
      if (hero && !hero.classList.contains('aew-stim') && !hero.closest('.aew-stim')) {
        return hero;
      }
    }
    return null;
  }

  // Anchor the badge inside the hero (absolute) so it travels with the hero and
  // disappears as the hero scrolls off — instead of staying pinned to the
  // viewport. Returns true if it could attach to a hero.
  function attachToHero(el) {
    var hero = findHero();
    if (!hero) return false;

    // The hero must establish a containing block for the absolute badge.
    if (getComputedStyle(hero).position === 'static') {
      hero.style.position = 'relative';
    }

    // Clean up any stale badge a previous editor re-render left behind inside
    // the hero (Elementor recreates the widget on every control change; without
    // this the hero would accumulate duplicate badges).
    hero.querySelectorAll('.aew-stim--in-hero').forEach(function (old) {
      if (old !== el) old.remove();
    });

    if (el.parentNode !== hero) {
      hero.appendChild(el);
    }
    el.classList.add('aew-stim--in-hero');
    return true;
  }

  function initWidget(el) {
    if (!el || el.dataset.aewStickyImageInit === '1') return;
    el.dataset.aewStickyImageInit = '1';

    var isEditor = document.body.classList.contains('elementor-editor-active');

    // Anchor inside the hero so it scrolls with it — in BOTH the editor and the
    // frontend so the editor preview is a true WYSIWYG. If there's no hero
    // (badge used on a page without one), fall back to the legacy fixed-to-
    // viewport behaviour by re-parenting to <body> (frontend only — in the
    // editor leave it where Elementor put it so it stays selectable).
    var attached = attachToHero(el);
    if (!attached && !isEditor && el.parentNode !== document.body) {
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
