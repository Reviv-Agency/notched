/**
 * Recent Posts V2 — Notched
 * Like toggle on each card (shared Post_Engagement AJAX endpoint, optimistic).
 * The like is a real button now (was a link that navigated to the post).
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

	function formatNum(n) { try { return Number(n).toLocaleString(); } catch (e) { return String(n); } }

	function setLiked(btn, countEl, liked, count) {
		btn.classList.toggle('is-liked', liked);
		btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
		if (countEl) { countEl.textContent = formatNum(Math.max(0, count)); }
	}

	function initWidget(node) {
		if (!node || node.dataset.aewRpv2Init === '1') { return; }
		node.dataset.aewRpv2Init = '1';
		var cfg = cfgOf(node);

		node.querySelectorAll('[data-aew-rpv2-like]').forEach(function (btn) {
			var countEl = btn.querySelector('[data-aew-rpv2-like-count]');
			var postId = btn.getAttribute('data-post-id');

			btn.addEventListener('click', function (e) {
				e.preventDefault();
				if (btn.dataset.busy === '1' || !cfg.ajaxUrl) { return; }
				btn.dataset.busy = '1';

				var wasLiked = btn.classList.contains('is-liked');
				var cur = parseInt((countEl && countEl.textContent || '0').replace(/[^0-9]/g, ''), 10) || 0;
				setLiked(btn, countEl, !wasLiked, wasLiked ? cur - 1 : cur + 1);

				post(cfg.ajaxUrl, { action: cfg.likeAction, _aew_nonce: cfg.nonce, post_id: postId })
					.then(function (json) {
						if (json && json.success && json.data) { setLiked(btn, countEl, !!json.data.liked, json.data.count); }
						else { setLiked(btn, countEl, wasLiked, cur); }
					})
					.catch(function () { setLiked(btn, countEl, wasLiked, cur); })
					.finally(function () { btn.dataset.busy = ''; });
			});
		});
	}

	function boot() { document.querySelectorAll('[data-aew-recent-posts-v2]').forEach(initWidget); }
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }

	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') { return; }
			elementorFrontend.hooks.addAction('frontend/element_ready/agency-recent-posts-v2.default', function ($scope) {
				var node = $scope[0] && $scope[0].querySelector('[data-aew-recent-posts-v2]');
				if (node) { node.dataset.aewRpv2Init = ''; initWidget(node); }
			});
		});
	}
})();
