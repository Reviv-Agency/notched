(function () {
    'use strict';

    // Global CTA click tracking. All AEW call-to-action links share the class
    // pattern `aew-{slug}__btn` / `aew-{slug}__cta`, so a single delegated
    // listener covers every widget without touching any widget render method.
    //
    // Fires a GA4 `cta_click` event via gtag (injected by Site Kit). If gtag is
    // absent — local Herd dev, or any page without Site Kit connected — every
    // call is a silent no-op, which is how local traffic stays out of analytics.

    // Anchors whose class names contain `aew-<slug>__btn` or `aew-<slug>__cta`.
    var CTA_RE = /\baew-([a-z0-9-]+)__(btn|cta)\b/;

    function onClick(e) {
        if (typeof window.gtag !== 'function') return;

        var link = e.target.closest ? e.target.closest('a') : null;
        if (!link || typeof link.className !== 'string') return;

        var match = CTA_RE.exec(link.className);
        if (!match) return;

        var text = (link.textContent || '').replace(/\s+/g, ' ').trim();

        window.gtag('event', 'cta_click', {
            link_text: text.slice(0, 100),
            link_url: link.href || '',
            widget: match[1],
            page_path: window.location.pathname
        });
    }

    function boot() {
        // Idempotency guard: bind the document-level listener only once.
        if (document.documentElement.dataset.aewAnalyticsInit === '1') return;
        document.documentElement.dataset.aewAnalyticsInit = '1';
        document.addEventListener('click', onClick, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
