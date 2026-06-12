/**
 * Notched — product gallery tweaks.
 *  1) Whole main image opens the PhotoSwipe lightbox (not just the magnifier),
 *     and the raw <a href="image.jpg"> no longer navigates to a separate page.
 *  2) A thumbnail strip is injected at the bottom of the open lightbox listing
 *     ALL product images; clicking a thumb jumps to it; the active thumb syncs
 *     as you arrow/swipe through.
 *
 * WooCommerce bundles PhotoSwipe v4 (no built-in filmstrip). We capture each
 * PhotoSwipe instance by wrapping the global constructor, then build/sync the
 * strip from the instance's items via its lifecycle events.
 */
(function () {
	'use strict';

	/* ── 1) Whole-image click opens the lightbox ─────────────────────────────── */
	/* The gallery links (and the zoom trigger) point at .jpg files, so Elementor
	   Pro's global lightbox would ALSO bind to them and open its own swiper on
	   top of WooCommerce's PhotoSwipe. Opt every gallery link out of it. */
	function muteElementorLightbox(gallery) {
		gallery.querySelectorAll('a').forEach(function (a) {
			a.setAttribute('data-elementor-open-lightbox', 'no');
		});
	}

	function wireGallery(gallery) {
		if (!gallery) { return; }
		muteElementorLightbox(gallery); // re-run every boot — WC rebuilds slides on variation change
		if (gallery.dataset.aewGalleryWired === '1') { return; }
		gallery.dataset.aewGalleryWired = '1';
		gallery.addEventListener('click', function (e) {
			var link = e.target.closest('.woocommerce-product-gallery__image a');
			if (!link) { return; }
			if (e.target.closest('.woocommerce-product-gallery__trigger')) { return; }
			var trigger = gallery.querySelector('.woocommerce-product-gallery__trigger');
			if (trigger) {
				e.preventDefault();
				e.stopPropagation();
				trigger.setAttribute('data-elementor-open-lightbox', 'no');
				trigger.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
			}
		}, true);
	}

	function bootGallery() {
		document.querySelectorAll('.woocommerce-product-gallery').forEach(wireGallery);
	}

	/* ── 2) Thumbnail strip inside the open PhotoSwipe lightbox ───────────────── */
	function thumbUrl(item) {
		// items carry msrc (small/medium src) + src (large). Prefer msrc for thumbs.
		return item.msrc || item.src || '';
	}

	function buildStrip(pswp) {
		var pswpEl = pswp.template || document.querySelector('.pswp');
		if (!pswpEl) { return; }
		var ui = pswpEl.querySelector('.pswp__ui') || pswpEl;

		var strip = pswpEl.querySelector('.aew-pswp-strip');
		if (!strip) {
			strip = document.createElement('div');
			strip.className = 'aew-pswp-strip';
			ui.appendChild(strip);
		}
		strip.innerHTML = '';

		pswp.items.forEach(function (item, i) {
			var t = document.createElement('button');
			t.type = 'button';
			t.className = 'aew-pswp-thumb';
			t.setAttribute('data-index', i);
			var u = thumbUrl(item);
			if (u) { t.style.backgroundImage = 'url("' + u + '")'; }
			t.addEventListener('click', function (ev) {
				ev.stopPropagation();
				pswp.goTo(i);
			});
			strip.appendChild(t);
		});
		syncStrip(pswp, strip);
	}

	function syncStrip(pswp, strip) {
		strip = strip || (pswp.template || document).querySelector('.aew-pswp-strip');
		if (!strip) { return; }
		var active = strip.querySelectorAll('.aew-pswp-thumb');
		active.forEach(function (t, i) {
			t.classList.toggle('is-active', i === pswp.getCurrentIndex());
		});
		var cur = strip.children[pswp.getCurrentIndex()];
		// Scroll ONLY the strip. scrollIntoView() also scrolls every scrollable
		// ancestor — including PhotoSwipe's overflow:hidden wrappers and the page —
		// which dragged the whole lightbox sideways on the first/last slides.
		if (cur) {
			strip.scrollTo({
				left: cur.offsetLeft - (strip.clientWidth - cur.offsetWidth) / 2,
				behavior: 'smooth'
			});
		}
	}

	function attach(pswp) {
		pswp.listen('afterInit', function () {
			buildStrip(pswp);
			// Lock the page behind the lightbox — wheel/trackpad overscroll on the
			// first/last slide otherwise scrolls the whole site underneath.
			document.documentElement.classList.add('aew-pswp-open');
			// Re-measure once the overlay is actually laid out — a viewport size
			// captured before .pswp is pinned to the viewport leaves neighbour
			// slides translated ~0px, i.e. visible stacked under the current one.
			setTimeout(function () {
				try { pswp.updateSize(true); } catch (e) { /* no-op */ }
			}, 60);
		});
		pswp.listen('destroy', function () {
			document.documentElement.classList.remove('aew-pswp-open');
		});
		pswp.listen('afterChange', function () { syncStrip(pswp); });
		// rebuild if items load lazily
		pswp.listen('imageLoadComplete', function () { syncStrip(pswp); });
	}

	function hookPhotoSwipe() {
		if (!window.PhotoSwipe || window.PhotoSwipe.__aewHooked) { return; }
		var Orig = window.PhotoSwipe;
		var Wrapped = function (template, UiClass, items, options) {
			var inst = new Orig(template, UiClass, items, options);
			try { attach(inst); } catch (e) { /* no-op */ }
			return inst;
		};
		// preserve statics/prototype
		Wrapped.prototype = Orig.prototype;
		Object.keys(Orig).forEach(function (k) { Wrapped[k] = Orig[k]; });
		Wrapped.__aewHooked = true;
		window.PhotoSwipe = Wrapped;
	}

	function boot() {
		bootGallery();
		hookPhotoSwipe();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
	window.addEventListener('load', boot);
	// PhotoSwipe script may load after us; retry hooking briefly.
	var tries = 0;
	var iv = setInterval(function () {
		hookPhotoSwipe();
		if (window.PhotoSwipe && window.PhotoSwipe.__aewHooked) { clearInterval(iv); }
		if (++tries > 20) { clearInterval(iv); }
	}, 250);
})();
