/**
 * Comments V2
 *
 *  - Threaded "Reply" moves the form inline (via WP's comment-reply.js
 *    moveForm) — no page navigation.
 *  - Comment submit posts to wp-comments-post.php via fetch and injects the new
 *    comment (or an "awaiting moderation" notice) WITHOUT a page reload.
 *
 * Depends on WP's comment-reply script for the reply-form movement.
 */
(function () {
	'use strict';

	function cfgOf(node) { try { return JSON.parse(node.getAttribute('data-config') || '{}'); } catch (e) { return {}; } }

	function status(form, msg, ok) {
		var el = form.querySelector('.aew-cmv2__status');
		if (!el) {
			el = document.createElement('p');
			el.className = 'aew-cmv2__status';
			el.setAttribute('role', 'status');
			el.setAttribute('aria-live', 'polite');
			form.appendChild(el);
		}
		el.textContent = msg;
		el.hidden = !msg;
		el.classList.toggle('aew-cmv2__status--error', ok === false);
		el.classList.toggle('aew-cmv2__status--ok', ok === true);
	}

	function initWidget(node) {
		if (!node || node.dataset.aewCmv2Init === '1') { return; }
		node.dataset.aewCmv2Init = '1';
		var cfg = cfgOf(node);

		// The form keeps id="commentform"; its class is our aew-cmv2__form (we
		// overrode class_form), so don't rely on the default .comment-form class.
		var form = node.querySelector('#commentform, .aew-cmv2__form, form[action*="wp-comments-post"]');
		if (!form) { return; }

		// wp-comments-post.php lives at site root; the form action already points
		// there. We post the form as-is and read the response.
		form.addEventListener('submit', function (e) {
			e.preventDefault();

			var submitBtn = form.querySelector('[type="submit"]');
			var textarea = form.querySelector('textarea[name="comment"]');
			if (textarea && !textarea.value.trim()) { textarea.focus(); return; }

			var data = new FormData(form);
			if (submitBtn) { submitBtn.disabled = true; }
			status(form, cfg.posting || 'Posting…', null);

			fetch(form.action, {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			}).then(function (r) {
				// WP returns the single-post page (200) on success, or a 4xx error
				// page (e.g. duplicate / too fast) with a message in the body.
				return r.text().then(function (html) { return { ok: r.ok, status: r.status, html: html }; });
			}).then(function (res) {
				if (!res.ok) {
					status(form, extractError(res.html) || cfg.errorMsg || 'Error.', false);
					return;
				}

				// CRITICAL: move the form back out of the comment list BEFORE we
				// touch the list's innerHTML. When replying, moveForm() relocates
				// #respond (the whole form) INSIDE a <li> in the list — replacing
				// the list HTML would then destroy the form and the box vanishes.
				resetReplyForm();
				form.reset();

				// Inject the freshly-posted comment from the returned page. Returns
				// the number of comments AFTER syncing (or -1 if no list found).
				var before = node.querySelectorAll('li.comment').length;
				var after = injectNewComment(node, res.html);

				if (after > before) {
					// Comment appeared (approved, or shown to its own author).
					status(form, '', null);
				} else {
					// Not in the approved list → held for moderation (first-time
					// commenter or `comment_moderation` on). Tell the user.
					status(form, cfg.pending || 'Thanks! Your comment is awaiting moderation.', true);
				}
			}).catch(function () {
				status(form, cfg.errorMsg || 'Network error.', false);
			}).finally(function () {
				if (submitBtn) { submitBtn.disabled = false; }
			});
		});
	}

	// If the reply form was moved under a comment, snap it back to the bottom.
	// WP's moveForm() leaves a #wp-temp-form-div placeholder where the form
	// belongs; the cancel link restores it. We click cancel unconditionally
	// (guarded by the placeholder's presence) so the form is always back in its
	// home before we rewrite the comment list.
	function resetReplyForm() {
		var temp = document.getElementById('wp-temp-form-div');
		var cancel = document.getElementById('cancel-comment-reply-link');
		if (temp && cancel) {
			cancel.click(); // triggers addComment.cancelEvent → restores #respond
		}
	}

	function extractError(html) {
		// WP error pages put the message in <p> after "Error:" — best-effort.
		var m = html.match(/<p[^>]*>([^<]*(?:error|already|quickly|moderation)[^<]*)<\/p>/i);
		return m ? m[1].trim() : '';
	}

	// Sync our list from the returned page. Returns the resulting comment count,
	// or -1 if the source had no list (caller treats <= before as "held").
	function injectNewComment(node, html) {
		var doc = new DOMParser().parseFromString(html, 'text/html');
		var srcList = doc.querySelector('.aew-cmv2__list');
		var ourList = node.querySelector('.aew-cmv2__list');

		// No list in the response → nothing approved to show (held / first page
		// had zero approved comments). Signal "nothing added".
		if (!srcList) { return -1; }

		var srcCount = srcList.querySelectorAll('li.comment').length;
		var ourCount = ourList ? ourList.querySelectorAll('li.comment').length : 0;

		// Create our list container if the page had zero comments before.
		if (!ourList) {
			ourList = document.createElement('ol');
			ourList.className = 'aew-cmv2__list';
			var card = node.querySelector('.aew-cmv2__card');
			var h = card && card.querySelector('.aew-cmv2__heading');
			if (h) { h.insertAdjacentElement('afterend', ourList); }
			else if (card) { card.prepend(ourList); }
		}

		// Only replace if the source actually has MORE comments than we do —
		// otherwise the comment was held for moderation (not in the approved
		// list) and we leave the current list untouched.
		if (srcCount > ourCount) {
			ourList.innerHTML = srcList.innerHTML;
			var lis = ourList.querySelectorAll('li.comment');
			if (lis.length) { lis[lis.length - 1].setAttribute('data-aew-cmv2-new', ''); }
			var heading = node.querySelector('.aew-cmv2__heading');
			if (heading) { heading.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
		}

		return ourList.querySelectorAll('li.comment').length;
	}

	function boot() { document.querySelectorAll('[data-aew-comments-v2]').forEach(initWidget); }
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }

	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') { return; }
			elementorFrontend.hooks.addAction('frontend/element_ready/agency-comments-v2.default', function ($scope) {
				var node = $scope[0] && $scope[0].querySelector('[data-aew-comments-v2]');
				// Do NOT reset aewCmv2Init here. boot() on DOMContentLoaded already
				// binds the submit handler on the live frontend; clearing the guard
				// and re-initing would bind a SECOND submit listener, causing every
				// comment to POST twice (duplicate rows, identical timestamp).
				// initWidget()'s own guard makes this a no-op when already inited,
				// while still covering the editor case where boot() never ran.
				if (node) { initWidget(node); }
			});
		});
	}
})();
