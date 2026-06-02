(function () {
    'use strict';

    // Match the CSS breakpoint: at/below this width the mobile image is shown
    // instead of the video, so we never load the (large) video file there.
    var DESKTOP_QUERY = '(min-width: 1025px)';

    function activateVideo(video) {
        if (!video || video.dataset.aewHev2Loaded === '1') return;
        var src = video.getAttribute('data-src');
        if (!src) return;
        video.dataset.aewHev2Loaded = '1';
        video.setAttribute('preload', 'metadata');
        video.src = src;
        video.muted = true;
        video.load();
        var p = video.play();
        if (p && typeof p.catch === 'function') {
            p.catch(function () { /* autoplay blocked — poster stays visible */ });
        }
    }

    function initWidget(el) {
        if (!el || el.dataset.aewHev2Init === '1') return; // idempotency guard
        el.dataset.aewHev2Init = '1';

        var video = el.querySelector('.aew-hev2__video[data-src]');
        if (!video || typeof video.play !== 'function') return;

        var mql = window.matchMedia ? window.matchMedia(DESKTOP_QUERY) : null;

        // Only load + play the video on desktop. On mobile/tablet the CSS
        // shows the mobile image and the video file is never fetched.
        if (!mql || mql.matches) {
            activateVideo(video);
        }

        // If the viewport later crosses into desktop (resize / rotate), load it.
        if (mql) {
            var onChange = function (e) { if (e.matches) activateVideo(video); };
            if (typeof mql.addEventListener === 'function') {
                mql.addEventListener('change', onChange);
            } else if (typeof mql.addListener === 'function') {
                mql.addListener(onChange); // older Safari
            }
        }
    }

    function boot() {
        document.querySelectorAll('[data-aew-hero-v2]').forEach(initWidget);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    // Re-init when Elementor editor re-renders the widget.
    if (typeof window.jQuery !== 'undefined') {
        window.jQuery(window).on('elementor/frontend/init', function () {
            if (typeof elementorFrontend === 'undefined') return;
            elementorFrontend.hooks.addAction('frontend/element_ready/agency-hero-v2.default', function ($scope) {
                var el = $scope[0] && $scope[0].querySelector('[data-aew-hero-v2]');
                if (el) initWidget(el);
            });
        });
    }
})();
