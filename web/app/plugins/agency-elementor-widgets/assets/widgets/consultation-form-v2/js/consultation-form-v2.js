/**
 * Consultation Form V2
 *
 *  - Upgrades the phone input into a dependency-free intl-tel flag dropdown.
 *  - Submits the form via fetch() to admin-ajax and shows inline status.
 */
(function () {
	'use strict';

	// Full ISO 3166 country list with dial codes. Flag emoji are derived from the
	// ISO code at runtime (regional-indicator codepoints) so we don't hand-type
	// 240 emoji. Source data: [iso2, name, dialCode].
	var COUNTRY_DATA = [
		['af','Afghanistan','+93'],['al','Albania','+355'],['dz','Algeria','+213'],
		['as','American Samoa','+1684'],['ad','Andorra','+376'],['ao','Angola','+244'],
		['ai','Anguilla','+1264'],['ag','Antigua and Barbuda','+1268'],['ar','Argentina','+54'],
		['am','Armenia','+374'],['aw','Aruba','+297'],['au','Australia','+61'],
		['at','Austria','+43'],['az','Azerbaijan','+994'],['bs','Bahamas','+1242'],
		['bh','Bahrain','+973'],['bd','Bangladesh','+880'],['bb','Barbados','+1246'],
		['by','Belarus','+375'],['be','Belgium','+32'],['bz','Belize','+501'],
		['bj','Benin','+229'],['bm','Bermuda','+1441'],['bt','Bhutan','+975'],
		['bo','Bolivia','+591'],['ba','Bosnia and Herzegovina','+387'],['bw','Botswana','+267'],
		['br','Brazil','+55'],['io','British Indian Ocean Territory','+246'],['vg','British Virgin Islands','+1284'],
		['bn','Brunei','+673'],['bg','Bulgaria','+359'],['bf','Burkina Faso','+226'],
		['bi','Burundi','+257'],['kh','Cambodia','+855'],['cm','Cameroon','+237'],
		['ca','Canada','+1'],['cv','Cape Verde','+238'],['ky','Cayman Islands','+1345'],
		['cf','Central African Republic','+236'],['td','Chad','+235'],['cl','Chile','+56'],
		['cn','China','+86'],['co','Colombia','+57'],['km','Comoros','+269'],
		['cg','Congo','+242'],['cd','Congo (DRC)','+243'],['ck','Cook Islands','+682'],
		['cr','Costa Rica','+506'],['ci','Côte d’Ivoire','+225'],['hr','Croatia','+385'],
		['cu','Cuba','+53'],['cw','Curaçao','+599'],['cy','Cyprus','+357'],
		['cz','Czech Republic','+420'],['dk','Denmark','+45'],['dj','Djibouti','+253'],
		['dm','Dominica','+1767'],['do','Dominican Republic','+1809'],['ec','Ecuador','+593'],
		['eg','Egypt','+20'],['sv','El Salvador','+503'],['gq','Equatorial Guinea','+240'],
		['er','Eritrea','+291'],['ee','Estonia','+372'],['et','Ethiopia','+251'],
		['fk','Falkland Islands','+500'],['fo','Faroe Islands','+298'],['fj','Fiji','+679'],
		['fi','Finland','+358'],['fr','France','+33'],['gf','French Guiana','+594'],
		['pf','French Polynesia','+689'],['ga','Gabon','+241'],['gm','Gambia','+220'],
		['ge','Georgia','+995'],['de','Germany','+49'],['gh','Ghana','+233'],
		['gi','Gibraltar','+350'],['gr','Greece','+30'],['gl','Greenland','+299'],
		['gd','Grenada','+1473'],['gp','Guadeloupe','+590'],['gu','Guam','+1671'],
		['gt','Guatemala','+502'],['gg','Guernsey','+44'],['gn','Guinea','+224'],
		['gw','Guinea-Bissau','+245'],['gy','Guyana','+592'],['ht','Haiti','+509'],
		['hn','Honduras','+504'],['hk','Hong Kong','+852'],['hu','Hungary','+36'],
		['is','Iceland','+354'],['in','India','+91'],['id','Indonesia','+62'],
		['ir','Iran','+98'],['iq','Iraq','+964'],['ie','Ireland','+353'],
		['im','Isle of Man','+44'],['il','Israel','+972'],['it','Italy','+39'],
		['jm','Jamaica','+1876'],['jp','Japan','+81'],['je','Jersey','+44'],
		['jo','Jordan','+962'],['kz','Kazakhstan','+7'],['ke','Kenya','+254'],
		['ki','Kiribati','+686'],['kw','Kuwait','+965'],['kg','Kyrgyzstan','+996'],
		['la','Laos','+856'],['lv','Latvia','+371'],['lb','Lebanon','+961'],
		['ls','Lesotho','+266'],['lr','Liberia','+231'],['ly','Libya','+218'],
		['li','Liechtenstein','+423'],['lt','Lithuania','+370'],['lu','Luxembourg','+352'],
		['mo','Macau','+853'],['mk','North Macedonia','+389'],['mg','Madagascar','+261'],
		['mw','Malawi','+265'],['my','Malaysia','+60'],['mv','Maldives','+960'],
		['ml','Mali','+223'],['mt','Malta','+356'],['mh','Marshall Islands','+692'],
		['mq','Martinique','+596'],['mr','Mauritania','+222'],['mu','Mauritius','+230'],
		['mx','Mexico','+52'],['fm','Micronesia','+691'],['md','Moldova','+373'],
		['mc','Monaco','+377'],['mn','Mongolia','+976'],['me','Montenegro','+382'],
		['ms','Montserrat','+1664'],['ma','Morocco','+212'],['mz','Mozambique','+258'],
		['mm','Myanmar','+95'],['na','Namibia','+264'],['nr','Nauru','+674'],
		['np','Nepal','+977'],['nl','Netherlands','+31'],['nc','New Caledonia','+687'],
		['nz','New Zealand','+64'],['ni','Nicaragua','+505'],['ne','Niger','+227'],
		['ng','Nigeria','+234'],['nu','Niue','+683'],['nf','Norfolk Island','+672'],
		['kp','North Korea','+850'],['mp','Northern Mariana Islands','+1670'],['no','Norway','+47'],
		['om','Oman','+968'],['pk','Pakistan','+92'],['pw','Palau','+680'],
		['ps','Palestine','+970'],['pa','Panama','+507'],['pg','Papua New Guinea','+675'],
		['py','Paraguay','+595'],['pe','Peru','+51'],['ph','Philippines','+63'],
		['pl','Poland','+48'],['pt','Portugal','+351'],['pr','Puerto Rico','+1787'],
		['qa','Qatar','+974'],['re','Réunion','+262'],['ro','Romania','+40'],
		['ru','Russia','+7'],['rw','Rwanda','+250'],['ws','Samoa','+685'],
		['sm','San Marino','+378'],['st','São Tomé and Príncipe','+239'],['sa','Saudi Arabia','+966'],
		['sn','Senegal','+221'],['rs','Serbia','+381'],['sc','Seychelles','+248'],
		['sl','Sierra Leone','+232'],['sg','Singapore','+65'],['sk','Slovakia','+421'],
		['si','Slovenia','+386'],['sb','Solomon Islands','+677'],['so','Somalia','+252'],
		['za','South Africa','+27'],['kr','South Korea','+82'],['ss','South Sudan','+211'],
		['es','Spain','+34'],['lk','Sri Lanka','+94'],['sd','Sudan','+249'],
		['sr','Suriname','+597'],['sz','Eswatini','+268'],['se','Sweden','+46'],
		['ch','Switzerland','+41'],['sy','Syria','+963'],['tw','Taiwan','+886'],
		['tj','Tajikistan','+992'],['tz','Tanzania','+255'],['th','Thailand','+66'],
		['tl','Timor-Leste','+670'],['tg','Togo','+228'],['tk','Tokelau','+690'],
		['to','Tonga','+676'],['tt','Trinidad and Tobago','+1868'],['tn','Tunisia','+216'],
		['tr','Turkey','+90'],['tm','Turkmenistan','+993'],['tc','Turks and Caicos Islands','+1649'],
		['tv','Tuvalu','+688'],['ug','Uganda','+256'],['ua','Ukraine','+380'],
		['ae','United Arab Emirates','+971'],['gb','United Kingdom','+44'],['us','United States','+1'],
		['uy','Uruguay','+598'],['uz','Uzbekistan','+998'],['vu','Vanuatu','+678'],
		['va','Vatican City','+39'],['ve','Venezuela','+58'],['vn','Vietnam','+84'],
		['wf','Wallis and Futuna','+681'],['ye','Yemen','+967'],['zm','Zambia','+260'],
		['zw','Zimbabwe','+263']
	];

	// Turn an ISO-3166 alpha-2 code into its flag emoji (two regional indicators).
	function isoToFlag(iso) {
		return iso.toUpperCase().replace(/./g, function (c) {
			return String.fromCodePoint(127397 + c.charCodeAt(0));
		});
	}

	var COUNTRIES = COUNTRY_DATA.map(function (row) {
		return { iso: row[0], name: row[1], dial: row[2], flag: isoToFlag(row[0]) };
	});
	var DEFAULT_ISO = 'us';

	function el(tag, cls) {
		var n = document.createElement(tag);
		if (cls) { n.className = cls; }
		return n;
	}

	// ── intl-tel upgrade ───────────────────────────────────────────────────────
	function upgradeTel(input) {
		if (!input || input.dataset.aewCfv2Tel === '1') { return; }
		input.dataset.aewCfv2Tel = '1';

		var current = COUNTRIES.filter(function (c) { return c.iso === DEFAULT_ISO; })[0] || COUNTRIES[0];

		var wrap = el('div', 'aew-cfv2-tel');
		input.parentNode.insertBefore(wrap, input);

		var btn = el('button', 'aew-cfv2-tel__flag-btn');
		btn.type = 'button';
		btn.setAttribute('aria-haspopup', 'listbox');
		btn.setAttribute('aria-expanded', 'false');
		btn.setAttribute('aria-label', 'Select country code');

		var flag = el('span', 'aew-cfv2-tel__flag-emoji'); flag.textContent = current.flag;
		var dial = el('span', 'aew-cfv2-tel__flag-dial');  dial.textContent = current.dial;
		var caret = el('span', 'aew-cfv2-tel__caret');     caret.textContent = '▾'; caret.setAttribute('aria-hidden', 'true');
		btn.appendChild(flag); btn.appendChild(dial); btn.appendChild(caret);

		wrap.appendChild(btn);
		wrap.appendChild(input);

		var menu = el('div', 'aew-cfv2-tel__menu');
		menu.hidden = true;

		// Search box: filters the (long) list by name or dial code.
		var search = el('input', 'aew-cfv2-tel__search');
		search.type = 'text';
		search.setAttribute('placeholder', 'Search country or code…');
		search.setAttribute('aria-label', 'Search country');
		menu.appendChild(search);

		var list = el('ul', 'aew-cfv2-tel__list');
		list.setAttribute('role', 'listbox');
		menu.appendChild(list);

		var selectedDial = current.dial;
		var selectedIso = current.iso;

		function selectCountry(c) {
			selectedDial = c.dial;
			selectedIso = c.iso;
			flag.textContent = c.flag;
			dial.textContent = c.dial;
			closeMenu();
			input.focus();
		}

		// Build (or rebuild) the option rows, optionally filtered by `query`.
		function renderOptions(query) {
			list.textContent = '';
			var q = (query || '').trim().toLowerCase();
			var matches = COUNTRIES.filter(function (c) {
				if (!q) { return true; }
				return c.name.toLowerCase().indexOf(q) !== -1 || c.dial.indexOf(q) !== -1;
			});

			if (!matches.length) {
				var empty = el('li', 'aew-cfv2-tel__empty');
				empty.textContent = 'No matches';
				list.appendChild(empty);
				return;
			}

			matches.forEach(function (c) {
				var li = el('li', 'aew-cfv2-tel__option');
				li.setAttribute('role', 'option');
				if (c.iso === selectedIso) { li.classList.add('aew-cfv2-tel__option--active'); li.setAttribute('aria-selected', 'true'); }

				var f = el('span', 'aew-cfv2-tel__option-flag'); f.textContent = c.flag;
				var nm = el('span', 'aew-cfv2-tel__option-name'); nm.textContent = c.name;
				var dl = el('span', 'aew-cfv2-tel__option-dial'); dl.textContent = c.dial;
				li.appendChild(f); li.appendChild(nm); li.appendChild(dl);

				li.addEventListener('click', function () { selectCountry(c); });
				list.appendChild(li);
			});
		}

		renderOptions('');
		search.addEventListener('input', function () { renderOptions(search.value); });
		// Keep clicks inside the search field from bubbling out and closing the menu.
		search.addEventListener('click', function (e) { e.stopPropagation(); });

		wrap.appendChild(menu);

		function openMenu() {
			menu.hidden = false;
			btn.setAttribute('aria-expanded', 'true');
			search.value = '';
			renderOptions('');
			search.focus();
			document.addEventListener('click', onDocClick, true);
		}
		function closeMenu() {
			menu.hidden = true;
			btn.setAttribute('aria-expanded', 'false');
			document.removeEventListener('click', onDocClick, true);
		}
		function onDocClick(e) { if (!wrap.contains(e.target)) { closeMenu(); } }

		btn.addEventListener('click', function () { if (menu.hidden) { openMenu(); } else { closeMenu(); } });
		btn.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeMenu(); } });
		search.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeMenu(); btn.focus(); } });

		// Expose the selected dial code to the submit handler.
		input._aewGetDial = function () { return selectedDial; };
	}

	// ── AJAX submit ────────────────────────────────────────────────────────────
	function setStatus(form, msg, ok) {
		var node = form.querySelector('.aew-cfv2__status');
		if (!node) { return; }
		node.textContent = msg;
		node.hidden = false;
		node.classList.remove('aew-cfv2__status--ok', 'aew-cfv2__status--error');
		node.classList.add(ok ? 'aew-cfv2__status--ok' : 'aew-cfv2__status--error');
	}

	function wireSubmit(form) {
		if (form.dataset.aewCfv2Submit === '1') { return; }
		form.dataset.aewCfv2Submit = '1';

		form.addEventListener('submit', function (e) {
			e.preventDefault();

			var btn = form.querySelector('.aew-cfv2__btn');
			var phone = form.querySelector('input[type="tel"]');

			// Prefix the dial code if the user didn't type one.
			if (phone && phone.value.trim() && phone.value.trim().charAt(0) !== '+' && typeof phone._aewGetDial === 'function') {
				phone.value = phone._aewGetDial() + ' ' + phone.value.trim();
			}

			// Native validity first (gives free required/email checks).
			if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
				form.reportValidity();
				return;
			}

			var data = new FormData(form);
			if (btn) { btn.disabled = true; }
			setStatus(form, 'Sending…', true);

			// NOTE: use getAttribute('action'), not form.action — the form has a
			// hidden <input name="action"> (required by admin-ajax) which shadows the
			// form.action DOM property, making it return that input node instead of
			// the URL. Using form.action posts to a bad URL → 404 → "Unexpected
			// server response." getAttribute returns the real admin-ajax URL.
			var endpoint = form.getAttribute('action');

			fetch(endpoint, {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			})
				.then(function (r) { return r.json().catch(function () { return { success: false, data: { message: 'Unexpected server response.' } }; }); })
				.then(function (json) {
					if (json && json.success) {
						// GA4 lead conversion. No-ops without gtag; no PII sent.
						if (typeof window.gtag === 'function') {
							window.gtag('event', 'generate_lead', {
								form: 'consultation-form-v2',
								page_path: window.location.pathname
							});
						}
						setStatus(form, (json.data && json.data.message) || 'Thanks!', true);
						form.reset();
					} else {
						setStatus(form, (json && json.data && json.data.message) || 'Something went wrong. Please try again.', false);
					}
				})
				.catch(function () {
					setStatus(form, 'Network error. Please try again.', false);
				})
				.finally(function () {
					if (btn) { btn.disabled = false; }
				});
		});
	}

	function initWidget(node) {
		if (!node || node.dataset.aewCfv2Init === '1') { return; }
		node.dataset.aewCfv2Init = '1';

		var form = node.querySelector('[data-aew-cfv2-form]');
		if (!form) { return; }
		upgradeTel(form.querySelector('input[type="tel"]'));
		wireSubmit(form);
	}

	function boot() {
		document.querySelectorAll('[data-aew-consultation-form-v2]').forEach(initWidget);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	// Re-init when Elementor editor re-renders the widget.
	if (typeof window.jQuery !== 'undefined') {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (typeof elementorFrontend === 'undefined') { return; }
			elementorFrontend.hooks.addAction('frontend/element_ready/agency-consultation-form-v2.default', function ($scope) {
				var node = $scope[0] && $scope[0].querySelector('[data-aew-consultation-form-v2]');
				if (node) { initWidget(node); }
			});
		});
	}
})();
