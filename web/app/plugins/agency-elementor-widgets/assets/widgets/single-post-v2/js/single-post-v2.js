/**
 * Single Post Content V2 — Notched
 *  - Like toggle (shared Post_Engagement AJAX endpoint, optimistic).
 *  - 3-dot Share menu + copy-link buttons (header menu item + footer copy btn).
 */
(function () {
	'use strict';

	function cfgOf(node) { try { return JSON.parse(node.getAttribute('data-config') || '{}'); } catch (e) { return {}; } }

	function post(url, params) {
		var body = new URLSearchParams();
		Object.keys(params).forEach(function (k) { body.append(k, params[k]); });
		return fetch(url, {
			method: 'POST', credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString()
		}).then(function (r) { return r.json().catch(function () { return { success: false }; }); });
	}

	function doCopy(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text).catch(function () { return legacy(text); });
		}
		return Promise.resolve(legacy(text));
	}
	function legacy(text) {
		var ta = document.createElement('textarea');
		ta.value = text; ta.style.position = 'absolute'; ta.style.left = '-9999px';
		document.body.appendChild(ta); ta.select();
		try { document.execCommand('copy'); } catch (e) {}
		document.body.removeChild(ta);
	}

	function formatNum(n) { try { return Number(n).toLocaleString(); } catch (e) { return String(n); } }

	function initWidget(node) {
		if (!node || node.dataset.aewSpv2Init === '1') { return; }
		node.dataset.aewSpv2Init = '1';
		var cfg = cfgOf(node);

		// ── 3-dot share menu (header) ───────────────────────────────────────────
		var share = node.querySelector('[data-aew-spv2-share]');
		if (share) {
			var sbtn = share.querySelector('.aew-spv2__dots-btn');
			var menu = share.querySelector('.aew-spv2__menu');
			if (sbtn && menu) {
				sbtn.addEventListener('click', function (e) {
					e.stopPropagation();
					var open = menu.hidden;
					menu.hidden = !open;
					sbtn.setAttribute('aria-expanded', open ? 'true' : 'false');
				});
			}
		}

		// ── Copy-link buttons (menu item + footer copy icon) ────────────────────
		node.querySelectorAll('[data-aew-spv2-copy]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				var url = btn.getAttribute('data-url') || window.location.href;
				doCopy(url).then(function () {
					if (btn.classList.contains('aew-spv2__menu-item')) {
						var orig = btn.textContent;
						btn.textContent = cfg.copied || 'Link copied!';
						btn.classList.add('is-copied');
						setTimeout(function () { btn.textContent = orig; btn.classList.remove('is-copied'); }, 1400);
					} else {
						btn.classList.add('is-copied');
						setTimeout(function () { btn.classList.remove('is-copied'); }, 1400);
					}
				});
			});
		});

		// ── Like toggle ─────────────────────────────────────────────────────────
		var like = node.querySelector('[data-aew-spv2-like]');
		if (like && cfg.ajaxUrl) {
			var countEl = like.querySelector('[data-aew-spv2-like-count]');
			like.addEventListener('click', function () {
				if (like.dataset.busy === '1') { return; }
				like.dataset.busy = '1';
				var wasLiked = like.classList.contains('is-liked');
				var cur = parseInt((countEl && countEl.textContent || '0').replace(/[^0-9]/g, ''), 10) || 0;
				setLiked(like, countEl, !wasLiked, wasLiked ? cur - 1 : cur + 1);
				post(cfg.ajaxUrl, { action: cfg.likeAction, _aew_nonce: cfg.nonce, post_id: cfg.postId })
					.then(function (json) {
						if (json && json.success && json.data) { setLiked(like, countEl, !!json.data.liked, json.data.count); }
						else { setLiked(like, countEl, wasLiked, cur); }
					})
					.catch(function () { setLiked(like, countEl, wasLiked, cur); })
					.finally(function () { like.dataset.busy = ''; });
			});
		}
	}

	function setLiked(btn, countEl, liked, count) {
		btn.classList.toggle('is-liked', liked);
		btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
		if (countEl) { countEl.textContent = formatNum(Math.max(0, count)); }
	}

	// Close any open menu on outside click / Escape.
	document.addEventListener('click', function () {
		document.querySelectorAll('[data-aew-spv2-share] .aew-spv2__menu').forEach(function (m) {
			m.hidden = true;
			var b = m.parentNode.querySelector('.aew-spv2__dots-btn');
			if (b) { b.setAttribute('aria-expanded', 'false'); }
		});
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { document.querySelectorAll('[data-aew-spv2-share] .aew-spv2__menu').forEach(function (m) { m.hidden = true; }); }
	});

	function boot() { document.querySelectorAll('[data-aew-single-post-v2]').forEach(initWidget); }
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }

	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') { return; }
			elementorFrontend.hooks.addAction('frontend/element_ready/agency-single-post-v2.default', function ($scope) {
				var node = $scope[0] && $scope[0].querySelector('[data-aew-single-post-v2]');
				if (node) { node.dataset.aewSpv2Init = ''; initWidget(node); }
			});
		});
	}
})();
