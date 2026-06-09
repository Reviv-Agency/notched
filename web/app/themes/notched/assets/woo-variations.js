/**
 * Notched — convert WooCommerce variation <select> dropdowns into the Wix-style
 * button-boxes (sizes / power / end-cut) and colour swatches (stain / roof).
 *
 * Works WITH WooCommerce's variations form: we keep the original <select> (hidden)
 * as the source of truth; clicking a box/swatch sets the select's value and fires
 * a `change` so WC's add-to-cart + price logic still runs. We also reflect WC's
 * own disabling (out-of-stock combos) back onto the boxes via the select options.
 */
(function () {
	'use strict';

	// Colour attributes render as swatches; everything else as button-boxes.
	var COLOR_ATTRS = ['attribute_pa_stain-color', 'attribute_pa_roof-color'];
	// hex map injected by the theme (slug -> #hex), see functions.php
	var HEX = window.NotchedSwatchHex || {};
	// Attributes that get an image preview (slug -> { url, name }) below their
	// swatches/boxes: stain colour + end cut. Injected by the theme.
	var PREVIEW_MAPS = {
		'attribute_pa_stain-color': window.NotchedStainImg || {},
		'attribute_pa_end-cut': window.NotchedCutImg || {}
	};

	function labelFor(select) {
		// the WC variations table row has a <th class="label"><label>NAME</label></th>
		var row = select.closest('tr');
		var lbl = row ? row.querySelector('.label label, td.label label, th.label label') : null;
		return lbl ? lbl.textContent.trim() : '';
	}

	function buildControl(select) {
		if (select.dataset.aewSwatched === '1') { return; }
		select.dataset.aewSwatched = '1';

		var name = select.getAttribute('name') || '';
		var isColor = COLOR_ATTRS.indexOf(name) !== -1;
		var wrap = document.createElement('div');
		wrap.className = 'aew-swatches ' + (isColor ? 'aew-swatches--color' : 'aew-swatches--box');

		Array.prototype.forEach.call(select.options, function (opt) {
			if (!opt.value) { return; } // skip the "Choose an option" placeholder
			var item = document.createElement('button');
			item.type = 'button';
			item.className = 'aew-swatch';
			item.setAttribute('data-value', opt.value);
			item.setAttribute('aria-label', opt.textContent.trim());
			item.title = opt.textContent.trim();

			if (isColor) {
				var hex = HEX[opt.value] || opt.value; // value is the slug; HEX maps slug->#hex
				if (hex && hex.charAt(0) !== '#') { hex = '#' + hex; }
				item.classList.add('aew-swatch--color');
				item.style.setProperty('--sw', hex);
			} else {
				item.classList.add('aew-swatch--box');
				item.textContent = opt.textContent.trim();
			}

			item.addEventListener('click', function () {
				if (item.classList.contains('is-disabled')) { return; }
				select.value = opt.value;
				select.dispatchEvent(new Event('change', { bubbles: true }));
				syncActive(wrap, select);
			});

			wrap.appendChild(item);
		});

		// hide the native select but keep it in the DOM (WC reads/writes it)
		select.classList.add('aew-swatch-source');
		select.insertAdjacentElement('afterend', wrap);

		// STAIN COLOR + END CUT: image preview below the controls. Empty by default;
		// hover an option to preview its image, select to keep it shown.
		var previewMap = PREVIEW_MAPS[name];
		if (previewMap && Object.keys(previewMap).length) { buildPreview(wrap, select, previewMap); }

		syncActive(wrap, select);
	}

	function buildPreview(wrap, select, imgMap) {
		var preview = document.createElement('figure');
		preview.className = 'aew-stain-preview';
		preview.innerHTML = '<img alt="" /><figcaption></figcaption>';
		var img = preview.querySelector('img');
		var cap = preview.querySelector('figcaption');
		wrap.insertAdjacentElement('afterend', preview);

		function show(slug) {
			var rec = imgMap[slug];
			if (rec && rec.url) {
				img.src = rec.url;
				img.alt = rec.name || '';
				cap.textContent = rec.name || '';
				preview.classList.add('is-shown');
			} else {
				preview.classList.remove('is-shown');
			}
		}
		function showSelectedOrHide() {
			if (select.value) { show(select.value); }
			else { preview.classList.remove('is-shown'); }
		}

		// hover previews; mouse-out reverts to the selected (or hidden) state
		wrap.querySelectorAll('.aew-swatch').forEach(function (b) {
			b.addEventListener('mouseenter', function () { show(b.getAttribute('data-value')); });
			b.addEventListener('focus', function () { show(b.getAttribute('data-value')); });
		});
		wrap.addEventListener('mouseleave', showSelectedOrHide);
		// keep in sync with selection changes (click or WC reset)
		select.addEventListener('change', showSelectedOrHide);
		wrap._aewStainSync = showSelectedOrHide;
		showSelectedOrHide();
	}

	function syncActive(wrap, select) {
		var current = select.value;
		Array.prototype.forEach.call(wrap.querySelectorAll('.aew-swatch'), function (b) {
			b.classList.toggle('is-active', b.getAttribute('data-value') === current);
			// reflect WC's enabled/disabled options (WC re-enables/disables <option>s as you pick)
			var opt = select.querySelector('option[value="' + CSS.escape(b.getAttribute('data-value')) + '"]');
			b.classList.toggle('is-disabled', !!(opt && opt.disabled));
		});
	}

	function init(root) {
		var selects = (root || document).querySelectorAll('.variations select, table.variations select');
		Array.prototype.forEach.call(selects, buildControl);
		// keep boxes in sync when WC updates the selects (variation chosen / reset)
		Array.prototype.forEach.call(selects, function (select) {
			var wrap = select.nextElementSibling;
			if (wrap && wrap.classList.contains('aew-swatches')) {
				select.addEventListener('change', function () { syncActive(wrap, select); });
			}
		});
		// Reveal: once every select on the page is converted, drop the anti-FOUC
		// flag so nothing stays hidden. (Safety timeout in the head also clears it.)
		var pending = document.querySelectorAll('.variations select:not(.aew-swatch-source)').length;
		if (pending === 0) {
			document.documentElement.classList.remove('aew-swatch-pending');
		}
	}

	function resyncAll() {
		document.querySelectorAll('.variations select').forEach(function (s) {
			var w = s.nextElementSibling;
			if (w && w.classList.contains('aew-swatches')) { syncActive(w, s); }
		});
	}

	function boot() { init(document); }

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
	// Run again after full load (Elementor/WC may render the variations form late).
	window.addEventListener('load', boot);

	/*
	 * The real fix for "some products still show dropdowns": WooCommerce / Elementor
	 * can (re)render the variation <select>s AFTER our first pass, leaving fresh,
	 * unconverted selects. A MutationObserver watches for any select that appears
	 * (or is replaced) anywhere in the document and converts it immediately. This
	 * makes conversion timing-independent and consistent across all products.
	 */
	if ('MutationObserver' in window) {
		var pending = null;
		var mo = new MutationObserver(function () {
			if (pending) { return; }
			pending = setTimeout(function () { pending = null; boot(); }, 60);
		});
		mo.observe(document.body, { childList: true, subtree: true });
	}

	// WC fires events on the form as variations update; convert + re-sync defensively.
	document.addEventListener('woocommerce_update_variation_values', boot);
	if (window.jQuery) {
		window.jQuery(document.body).on('woocommerce_variation_has_changed wc_variation_form check_variations found_variation reset_data update_variation_values', function () {
			boot();        // convert any newly-rendered selects
			resyncAll();   // refresh active/disabled states
		});
	}
})();
