(function () {
    'use strict';

    function initWidget(el) {
        if (!el || el.dataset.aewFaqv2Init === '1') return; // idempotency guard
        el.dataset.aewFaqv2Init = '1';

        var list      = el.querySelector('[data-aew-faqv2-list]');
        var items     = Array.prototype.slice.call(el.querySelectorAll('[data-aew-faqv2-item]'));
        var cats      = Array.prototype.slice.call(el.querySelectorAll('[data-aew-faqv2-cat]'));
        var search    = el.querySelector('[data-aew-faqv2-search]');
        var noResults = el.querySelector('[data-aew-faqv2-noresults]');

        var activeCat = '';
        var firstCat  = cats.find(function (c) { return c.classList.contains('is-active'); });
        if (firstCat) activeCat = firstCat.getAttribute('data-aew-faqv2-cat') || '';

        // ── Accordion toggle ──────────────────────────────────────────────
        function openItem(item) {
            var trigger = item.querySelector('.aew-faqv2__trigger');
            var panel   = item.querySelector('.aew-faqv2__panel');
            if (!trigger || !panel) return;
            item.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
            panel.removeAttribute('hidden');
        }

        items.forEach(function (item) {
            var trigger = item.querySelector('.aew-faqv2__trigger');
            var panel   = item.querySelector('.aew-faqv2__panel');
            if (!trigger || !panel) return;

            trigger.addEventListener('click', function () {
                var isOpen = item.classList.contains('is-open');
                if (isOpen) {
                    item.classList.remove('is-open');
                    trigger.setAttribute('aria-expanded', 'false');
                    panel.setAttribute('hidden', 'hidden');
                } else {
                    item.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                    panel.removeAttribute('hidden');
                }
            });
        });

        // ── Filtering (category + search combined) ────────────────────────
        function applyFilter() {
            var query = search ? norm(search.value) : '';
            var visible = 0;

            items.forEach(function (item) {
                var itemCat = item.getAttribute('data-cat') || '';
                var text    = item.getAttribute('data-text') || '';

                // With a non-empty search query, search wins across ALL categories.
                // Otherwise show only items in the active category (or all if no rail).
                // itemCat is a pipe-delimited list like "|cost & value|timber pergola kits|"
                // so one question can belong to multiple categories.
                var catOk = query ? true : (!activeCat || itemCat.indexOf('|' + activeCat + '|') !== -1);
                var qOk   = !query || text.indexOf(query) !== -1;
                var show  = catOk && qOk;

                if (show) {
                    item.removeAttribute('hidden');
                    visible++;
                } else {
                    item.setAttribute('hidden', 'hidden');
                }
            });

            if (noResults) {
                if (visible === 0) noResults.removeAttribute('hidden');
                else noResults.setAttribute('hidden', 'hidden');
            }
        }

        cats.forEach(function (cat) {
            cat.addEventListener('click', function () {
                activeCat = cat.getAttribute('data-aew-faqv2-cat') || '';
                cats.forEach(function (c) {
                    var on = c === cat;
                    c.classList.toggle('is-active', on);
                    c.setAttribute('aria-selected', on ? 'true' : 'false');
                    c.setAttribute('tabindex', on ? '0' : '-1');
                });
                // A category click clears any active search so the tab shows fully.
                if (search) search.value = '';
                applyFilter();
            });
        });

        if (search) {
            search.addEventListener('input', applyFilter);
        }

        // ── Share bar ─────────────────────────────────────────────────────
        var shareBars = el.querySelectorAll('[data-aew-faqv2-share]');
        Array.prototype.forEach.call(shareBars, function (bar) {
            var qid = bar.getAttribute('data-qid') || '';
            var pageUrl = window.location.origin + window.location.pathname;
            var deepUrl = pageUrl + (qid ? ('?questionId=' + encodeURIComponent(qid)) : '');
            var enc = encodeURIComponent(deepUrl);

            Array.prototype.forEach.call(bar.querySelectorAll('.aew-faqv2__share-btn'), function (btn) {
                var kind = btn.getAttribute('data-share');
                if (kind === 'facebook') {
                    btn.setAttribute('href', 'https://www.facebook.com/sharer/sharer.php?u=' + enc);
                } else if (kind === 'twitter') {
                    btn.setAttribute('href', 'https://twitter.com/intent/tweet?url=' + enc);
                } else if (kind === 'linkedin') {
                    btn.setAttribute('href', 'https://www.linkedin.com/sharing/share-offsite/?url=' + enc);
                } else if (kind === 'copy') {
                    btn.addEventListener('click', function () {
                        copyToClipboard(deepUrl);
                        btn.classList.add('is-copied');
                        setTimeout(function () { btn.classList.remove('is-copied'); }, 1500);
                    });
                }
            });
        });

        applyFilter();

        // ── Deep link: ?questionId=q-<wid>-<i> opens & scrolls to that question ─
        function activateCategory(slug) {
            if (!slug) return;
            var match = cats.find(function (c) {
                return (c.getAttribute('data-aew-faqv2-cat') || '') === slug;
            });
            if (!match) return;
            activeCat = slug;
            cats.forEach(function (c) {
                var on = c === match;
                c.classList.toggle('is-active', on);
                c.setAttribute('aria-selected', on ? 'true' : 'false');
                c.setAttribute('tabindex', on ? '0' : '-1');
            });
            if (search) search.value = '';
            applyFilter();
        }

        function openFromQuestionId() {
            var qid = '';
            try { qid = new URLSearchParams(window.location.search).get('questionId') || ''; }
            catch (e) { return; }
            if (!qid) return;

            var bar = el.querySelector('[data-aew-faqv2-share][data-qid="' + qid + '"]');
            if (!bar) return;
            var item = bar.closest('[data-aew-faqv2-item]');
            if (!item) return;

            // The target may be filtered out by the active category — switch to
            // the first category the item belongs to so it's visible.
            var cat = (item.getAttribute('data-cat') || '').split('|').filter(Boolean)[0];
            if (cat) activateCategory(cat);

            openItem(item);

            // Scroll once layout settles, then re-scroll after lazy sections /
            // images above may have shifted the page (a single early scroll can
            // miss because the target's position keeps changing during load).
            function scrollToItem() {
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            setTimeout(scrollToItem, 300);
            setTimeout(scrollToItem, 900);
            window.addEventListener('load', function () {
                setTimeout(scrollToItem, 100);
            });
        }

        openFromQuestionId();
    }

    function norm(v) {
        return (v || '').toString().toLowerCase().trim();
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).catch(function () { fallbackCopy(text); });
        } else {
            fallbackCopy(text);
        }
    }

    function fallbackCopy(text) {
        try {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        } catch (e) { /* no-op */ }
    }

    function boot() {
        document.querySelectorAll('[data-aew-faq-v2]').forEach(initWidget);
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
            elementorFrontend.hooks.addAction('frontend/element_ready/agency-faq-v2.default', function ($scope) {
                var el = $scope[0] && $scope[0].querySelector('[data-aew-faq-v2]');
                if (el) initWidget(el);
            });
        });
    }
})();
