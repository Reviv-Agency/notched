/**
 * Post Archive V2 — Notched
 *
 *  - Infinite scroll (IntersectionObserver) or a "Load more" button, fetching
 *    rendered card HTML from admin-ajax.
 *  - Like toggle (optimistic, reconciled with the server count).
 *  - 3-dot "Share" dropdown whose single item copies the post permalink.
 *
 * Dependency-free. Re-inits cleanly inside the Elementor editor.
 */
(function () {
	'use strict';

	function parseConfig(node) {
		try {
			return JSON.parse(node.getAttribute('data-config') || '{}');
		} catch (e) {
			return {};
		}
	}

	// ── AJAX helper ──────────────────────────────────────────────────────────────
	function post(url, params) {
		var body = new URLSearchParams();
		Object.keys(params).forEach(function (k) { body.append(k, params[k]); });
		return fetch(url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString()
		}).then(function (r) {
			return r.json().catch(function () { return { success: false }; });
		});
	}

	// ── Share dropdown ───────────────────────────────────────────────────────────
	function closeAllMenus(except) {
		document.querySelectorAll('[data-aew-pav2-share]').forEach(function (share) {
			if (share === except) { return; }
			var menu = share.querySelector('.aew-pav2__menu');
			var btn = share.querySelector('.aew-pav2__share-btn');
			if (menu) { menu.hidden = true; }
			if (btn) { btn.setAttribute('aria-expanded', 'false'); }
		});
	}

	function wireShare(card, cfg) {
		var share = card.querySelector('[data-aew-pav2-share]');
		if (!share) { return; }
		var btn = share.querySelector('.aew-pav2__share-btn');
		var menu = share.querySelector('.aew-pav2__menu');
		var copy = share.querySelector('[data-aew-pav2-copy]');
		if (!btn || !menu) { return; }

		btn.addEventListener('click', function (e) {
			e.stopPropagation();
			var open = menu.hidden;
			closeAllMenus(share);
			menu.hidden = !open;
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});

		if (copy) {
			copy.addEventListener('click', function () {
				var url = copy.getAttribute('data-url') || '';
				doCopy(url).then(function () {
					var original = copy.textContent;
					copy.textContent = cfg.copied || 'Link copied!';
					copy.classList.add('is-copied');
					setTimeout(function () {
						copy.textContent = original;
						copy.classList.remove('is-copied');
						menu.hidden = true;
						btn.setAttribute('aria-expanded', 'false');
					}, 1400);
				});
			});
		}
	}

	function doCopy(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text).catch(function () { return legacyCopy(text); });
		}
		return Promise.resolve(legacyCopy(text));
	}

	function legacyCopy(text) {
		var ta = document.createElement('textarea');
		ta.value = text;
		ta.setAttribute('readonly', '');
		ta.style.position = 'absolute';
		ta.style.left = '-9999px';
		document.body.appendChild(ta);
		ta.select();
		try { document.execCommand('copy'); } catch (e) { /* noop */ }
		document.body.removeChild(ta);
	}

	// ── Like toggle ──────────────────────────────────────────────────────────────
	function wireLike(card, cfg) {
		var btn = card.querySelector('[data-aew-pav2-like]');
		if (!btn) { return; }
		var postId = card.getAttribute('data-post-id');
		var countEl = btn.querySelector('[data-aew-pav2-like-count]');

		btn.addEventListener('click', function () {
			if (btn.dataset.busy === '1') { return; }
			btn.dataset.busy = '1';

			// Optimistic flip.
			var wasLiked = btn.classList.contains('is-liked');
			var current = parseInt((countEl && countEl.textContent || '0').replace(/[^0-9]/g, ''), 10) || 0;
			setLiked(btn, countEl, !wasLiked, wasLiked ? current - 1 : current + 1);

			post(cfg.ajaxUrl, {
				action: cfg.likeAction,
				_aew_nonce: cfg.nonce,
				post_id: postId
			}).then(function (json) {
				if (json && json.success && json.data) {
					setLiked(btn, countEl, !!json.data.liked, json.data.count);
				} else {
					// Revert on failure.
					setLiked(btn, countEl, wasLiked, current);
				}
			}).catch(function () {
				setLiked(btn, countEl, wasLiked, current);
			}).finally(function () {
				btn.dataset.busy = '';
			});
		});
	}

	function setLiked(btn, countEl, liked, count) {
		btn.classList.toggle('is-liked', liked);
		btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
		if (countEl) { countEl.textContent = formatNum(Math.max(0, count)); }
	}

	function formatNum(n) {
		try { return Number(n).toLocaleString(); } catch (e) { return String(n); }
	}

	// ── Card wiring (idempotent) ─────────────────────────────────────────────────
	function wireCard(card, cfg) {
		if (card.dataset.aewPav2Wired === '1') { return; }
		card.dataset.aewPav2Wired = '1';
		wireShare(card, cfg);
		wireLike(card, cfg);
	}

	// ── Infinite load ────────────────────────────────────────────────────────────
	function initWidget(node) {
		if (!node || node.dataset.aewPav2Init === '1') { return; }
		node.dataset.aewPav2Init = '1';

		var cfg = parseConfig(node);
		var list = node.querySelector('[data-aew-pav2-list]');
		if (!list) { return; }

		// Wire the server-rendered first page.
		list.querySelectorAll('[data-aew-pav2-card]').forEach(function (c) { wireCard(c, cfg); });

		var more = node.querySelector('[data-aew-pav2-more]');
		var loadBtn = node.querySelector('[data-aew-pav2-load]');
		var spinner = node.querySelector('[data-aew-pav2-spinner]');

		var state = {
			page: 1,
			loading: false,
			hasMore: !!cfg.hasMore
		};

		function appendHtml(html) {
			var tmp = document.createElement('div');
			tmp.innerHTML = html;
			var added = [];
			Array.prototype.slice.call(tmp.children).forEach(function (child) {
				if (child.matches && child.matches('[data-aew-pav2-card]')) {
					child.setAttribute('data-aew-pav2-new', '');
					list.appendChild(child);
					wireCard(child, cfg);
					added.push(child);
				}
			});
			return added;
		}

		function loadNext() {
			if (state.loading || !state.hasMore) { return; }
			state.loading = true;
			if (spinner) { spinner.hidden = false; }
			if (loadBtn) { loadBtn.disabled = true; }

			post(cfg.ajaxUrl, {
				action: cfg.loadAction,
				_aew_nonce: cfg.nonce,
				page: state.page + 1,
				per_page: cfg.perPage,
				category: cfg.category || ''
			}).then(function (json) {
				if (json && json.success && json.data) {
					state.page = json.data.page || (state.page + 1);
					state.hasMore = !!json.data.has_more;
					if (json.data.html) { appendHtml(json.data.html); }
				} else {
					state.hasMore = false;
				}
			}).catch(function () {
				state.hasMore = false;
			}).finally(function () {
				state.loading = false;
				if (spinner) { spinner.hidden = true; }
				if (loadBtn) { loadBtn.disabled = false; }
				if (!state.hasMore && more) { more.hidden = true; }
			});
		}

		// Manual "Load more" button (always wired; visible only when infinite is off).
		if (loadBtn) {
			loadBtn.addEventListener('click', loadNext);
		}

		// Infinite scroll via IntersectionObserver on the sentinel (the .more block).
		if (cfg.infinite && more && 'IntersectionObserver' in window) {
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) { loadNext(); }
				});
			}, { rootMargin: '400px 0px' });
			io.observe(more);
		} else if (cfg.infinite && loadBtn) {
			// No IO support → fall back to showing the button.
			loadBtn.hidden = false;
		}
	}

	// Close any open share menu on outside click / Escape.
	document.addEventListener('click', function () { closeAllMenus(null); });
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { closeAllMenus(null); }
	});

	function boot() {
		document.querySelectorAll('[data-aew-post-archive-v2]').forEach(initWidget);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	// Re-init when the Elementor editor re-renders the widget.
	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') { return; }
			elementorFrontend.hooks.addAction('frontend/element_ready/agency-post-archive-v2.default', function ($scope) {
				var node = $scope[0] && $scope[0].querySelector('[data-aew-post-archive-v2]');
				if (node) {
					node.dataset.aewPav2Init = '';
					initWidget(node);
				}
			});
		});
	}
})();
