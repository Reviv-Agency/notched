/**
 * Products Slider V2 — Notched.
 *
 * Drives a native scroll-snap track: prev/next arrows page by the visible
 * width, dot pagination reflects the closest slide, and arrows disable at the
 * track edges. No library, no jQuery. Touch/trackpad swipe is native scroll.
 */
(function () {
  'use strict';

  function initWidget(el) {
    if (!el || el.dataset.aewPrsv2Init === '1') return;
    el.dataset.aewPrsv2Init = '1';

    var track = el.querySelector('[data-aew-prs-track]');
    if (!track) return;

    var prev = el.querySelector('[data-aew-prs-prev]');
    var next = el.querySelector('[data-aew-prs-next]');
    var dotsWrap = el.querySelector('[data-aew-prs-dots]');
    var slides = Array.prototype.slice.call(track.querySelectorAll('.aew-prsv2__slide'));
    if (!slides.length) return;

    // ── Build one dot per slide ──────────────────────────────────────────────
    var dots = [];
    if (dotsWrap) {
      dotsWrap.innerHTML = '';
      slides.forEach(function (slide, i) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'aew-prsv2__dot';
        dot.setAttribute('aria-label', 'Go to product ' + (i + 1));
        dot.addEventListener('click', function () {
          scrollToSlide(i);
        });
        dotsWrap.appendChild(dot);
        dots.push(dot);
      });
      dotsWrap.removeAttribute('aria-hidden');
    }

    function step() {
      // Page by the viewport width, snapped to whole slides.
      var slideW = slides[0].getBoundingClientRect().width;
      var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || '0') || 0;
      var per = Math.max(1, Math.floor(track.clientWidth / (slideW + gap)));
      return per * (slideW + gap);
    }

    function scrollToSlide(i) {
      var target = slides[Math.max(0, Math.min(i, slides.length - 1))];
      if (target) track.scrollTo({ left: target.offsetLeft, behavior: 'smooth' });
    }

    function nearestIndex() {
      var pos = track.scrollLeft;
      var best = 0;
      var bestDist = Infinity;
      slides.forEach(function (slide, i) {
        var d = Math.abs(slide.offsetLeft - pos);
        if (d < bestDist) { bestDist = d; best = i; }
      });
      return best;
    }

    function update() {
      var atStart = track.scrollLeft <= 2;
      var atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
      if (prev) prev.disabled = atStart;
      if (next) next.disabled = atEnd;

      var active = nearestIndex();
      dots.forEach(function (dot, i) {
        if (i === active) dot.setAttribute('aria-current', 'true');
        else dot.removeAttribute('aria-current');
      });
    }

    if (prev) prev.addEventListener('click', function () {
      track.scrollBy({ left: -step(), behavior: 'smooth' });
    });
    if (next) next.addEventListener('click', function () {
      track.scrollBy({ left: step(), behavior: 'smooth' });
    });

    // rAF-throttled scroll handler.
    var ticking = false;
    track.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(function () {
        update();
        ticking = false;
      });
    }, { passive: true });

    window.addEventListener('resize', update);
    update();
  }

  function boot() {
    document.querySelectorAll('[data-aew-products-slider-v2]').forEach(initWidget);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  // Re-init inside the Elementor editor preview.
  if (typeof window.jQuery !== 'undefined') {
    window.jQuery(window).on('elementor/frontend/init', function () {
      if (typeof elementorFrontend === 'undefined') return;
      elementorFrontend.hooks.addAction('frontend/element_ready/agency-products-slider-v2.default', function ($scope) {
        var el = $scope[0] && $scope[0].querySelector('[data-aew-products-slider-v2]');
        if (el) initWidget(el);
      });
    });
  }
})();
