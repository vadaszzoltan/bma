/**
 * Service selection UX (list + detail): immediate cart sync on list, draft+CTA on detail.
 */
(function ($) {
	'use strict';

	var DRAFT_KEY = 'bma_selection_draft_v1';
	var root = document.querySelector('[data-bma-selection-root]');
	if (!root || typeof bmaAj === 'undefined') {
		return;
	}

	var mode = root.getAttribute('data-selection-mode') || 'list';
	var bar = root.querySelector('[data-bma-selection-bar]') || document.querySelector('[data-bma-selection-bar]');
	var dialog = root.querySelector('[data-bma-unsaved-dialog]') || document.querySelector('[data-bma-unsaved-dialog]');
	var summary = document.querySelector('[data-bma-summary]');
	var pendingHref = null;
	var suppressGuard = false;
	var syncing = false;
	var sourceMap = bmaAj.serviceSources || {};
	var cartBaseline = normalizeMap(bmaAj.cartSelection || {});
	var strings = bmaAj.strings || {};

	document.documentElement.classList.add('bma-js');
	root.querySelectorAll('[data-bma-nojs-cta]').forEach(function (el) {
		el.hidden = true;
	});

	function sourceId(sid) {
		var key = String(sid || '');
		return String(sourceMap[key] || key);
	}

	function normalizeMap(map) {
		var out = {};
		Object.keys(map || {}).forEach(function (sid) {
			var ids = (map[sid] || []).map(Number).filter(Boolean).sort(function (a, b) { return a - b; });
			if (!ids.length) return;
			var canon = sourceId(sid);
			if (!out[canon] || ids.length >= out[canon].length) {
				out[canon] = ids;
			}
		});
		return out;
	}

	function mapsEqual(a, b) {
		var ka = Object.keys(a).sort();
		var kb = Object.keys(b).sort();
		if (ka.length !== kb.length) return false;
		for (var i = 0; i < ka.length; i++) {
			if (ka[i] !== kb[i]) return false;
			var aa = a[ka[i]];
			var bb = b[kb[i]];
			if (aa.length !== bb.length) return false;
			for (var j = 0; j < aa.length; j++) {
				if (aa[j] !== bb[j]) return false;
			}
		}
		return true;
	}

	function readDraft() {
		try {
			var raw = sessionStorage.getItem(DRAFT_KEY);
			if (!raw) return {};
			return normalizeMap(JSON.parse(raw));
		} catch (e) {
			return {};
		}
	}

	function writeDraft(map) {
		try {
			sessionStorage.setItem(DRAFT_KEY, JSON.stringify(normalizeMap(map)));
		} catch (e) { /* ignore */ }
	}

	function cardFor(cb) {
		return cb.closest('.task, .taskcard, .bma-select-card');
	}

	function setCardSelected(card, selected) {
		if (!card) return;
		card.classList.toggle('is-sel', selected);
		card.classList.toggle('is-selected', selected);
	}

	function syncToggleButtons() {
		root.querySelectorAll('[data-bma-item-toggle]').forEach(function (btn) {
			var card = btn.closest('.taskcard');
			var cb = card ? card.querySelector('.bma-item-checkbox') : null;
			var on = !!(cb && cb.checked);
			setCardSelected(card, on);
			btn.textContent = on
				? (strings.addedLabel || 'Hozzáadva')
				: (strings.addLabel || 'Hozzáadás');
			btn.classList.toggle('btn--outline', !on);
			btn.classList.toggle('btn--primary', on);
			btn.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
	}

	function collectFromDom() {
		var map = readDraft();
		var scopeSids = {};

		root.querySelectorAll('.bma-item-checkbox').forEach(function (cb) {
			var sid = sourceId(cb.getAttribute('data-service-id') || '');
			if (!sid) return;
			scopeSids[sid] = true;
			if (!map[sid]) map[sid] = [];
		});

		Object.keys(scopeSids).forEach(function (sid) {
			map[sid] = [];
		});

		root.querySelectorAll('.bma-item-checkbox:checked').forEach(function (cb) {
			var sid = sourceId(cb.getAttribute('data-service-id') || '');
			var id = parseInt(cb.value, 10);
			if (!sid || !id) return;
			if (!map[sid]) map[sid] = [];
			map[sid].push(id);
		});

		return normalizeMap(map);
	}

	function hydrateFromDraft() {
		var draft = readDraft();
		var hasDraft = Object.keys(draft).length > 0;
		// Immediate-sync pages prefer the server cart baseline.
		var source = cartBaseline;
		if (mode !== 'list' && mode !== 'detail' && hasDraft) {
			source = draft;
		}

		root.querySelectorAll('.bma-item-checkbox').forEach(function (cb) {
			var sid = sourceId(cb.getAttribute('data-service-id') || '');
			var id = parseInt(cb.value, 10);
			var selected = !!(source[sid] && source[sid].indexOf(id) !== -1);
			cb.checked = selected;
			setCardSelected(cardFor(cb), selected);
		});
	}

	function isDirty() {
		return !mapsEqual(collectFromDom(), cartBaseline);
	}

	function selectedCards() {
		return Array.prototype.slice.call(root.querySelectorAll('.bma-item-checkbox:checked')).map(function (cb) {
			var card = cardFor(cb);
			return {
				id: parseInt(cb.value, 10),
				name: card ? card.getAttribute('data-item-name') : '',
				serviceId: cb.getAttribute('data-service-id'),
				serviceName: card ? card.getAttribute('data-service-name') : ''
			};
		});
	}

	function updateHeaderCount(count) {
		var el = document.getElementById('bma-header-cart-count');
		if (!el) return;
		var n = Number(count) || 0;
		el.textContent = String(n);
		el.setAttribute('data-count', String(n));
		var link = el.closest('a');
		if (link && bmaAj.cartUrl && bmaAj.servicesUrl) {
			link.href = n > 0 ? bmaAj.cartUrl : bmaAj.servicesUrl;
		}
		if (link) {
			var label = (strings.cartAria || 'Ajánlat, %d tétel').replace('%d', String(n));
			link.setAttribute('aria-label', label);
		}
	}

	function updateMobilebar(count) {
		var barEl = document.querySelector('[data-bma-mobilebar="services"]');
		if (!barEl) return;
		var n = Number(count) || 0;
		var strong = barEl.querySelector('[data-bma-mobilebar-count]');
		var cta = barEl.querySelector('[data-bma-mobilebar-cta]');
		if (strong) {
			strong.textContent = n === 1
				? (strings.oneItemSelectedShort || '1 tétel kiválasztva')
				: (strings.nItemsSelectedShort || '%d tétel kiválasztva').replace('%d', String(n));
		}
		if (cta) {
			cta.classList.toggle('is-disabled', n === 0);
			if (n === 0) {
				cta.setAttribute('aria-disabled', 'true');
				cta.setAttribute('tabindex', '-1');
			} else {
				cta.removeAttribute('aria-disabled');
				cta.removeAttribute('tabindex');
			}
		}
	}

	function renderSummary(payload) {
		if (!summary) return;
		var body = summary.querySelector('[data-bma-summary-body]');
		var countEl = summary.querySelector('[data-bma-summary-count]');
		var noteEl = summary.querySelector('[data-bma-summary-note]');
		var cta = summary.querySelector('[data-bma-summary-cta]');
		var lines = (payload && payload.summary) || [];
		var count = typeof (payload && payload.itemCount) === 'number' ? payload.itemCount : lines.length;

		summary.setAttribute('data-count', String(count));
		if (countEl) countEl.textContent = String(count);

		if (body) {
			if (!lines.length) {
				body.innerHTML = '<div class="sum-empty">' +
					(strings.summaryEmpty || 'Még nem választott tételt. Jelölje be bal oldalon, mire van szüksége – bármennyi tétel egy ajánlatkérésbe kerülhet.') +
					'</div>';
			} else {
				body.innerHTML = lines.map(function (line) {
					return '<div class="sum-item" data-item-id="' + String(line.item_id || '') +
						'" data-service-id="' + String(line.service_id || '') + '">' +
						'<span><strong>' + escapeHtml(line.name || '') + '</strong>' +
						'<span class="hint">' + escapeHtml(line.service_name || '') + '</span></span>' +
						'<button type="button" class="sum-del" data-bma-sum-del aria-label="' +
						escapeAttr(strings.removeItem || 'Tétel eltávolítása') + '">&times;</button></div>';
				}).join('');
			}
		}

		if (noteEl) {
			noteEl.textContent = count
				? (strings.summaryFilledNote || 'A kiválasztás bármikor módosítható a beküldésig. Az ajánlatkérés nem jár kötelezettséggel.')
				: (strings.summaryEmptyNote || 'Legalább egy tételt válasszon ki a továbblépéshez.');
		}

		if (cta) {
			cta.classList.toggle('is-disabled', count === 0);
			if (count === 0) {
				cta.setAttribute('aria-disabled', 'true');
				cta.setAttribute('tabindex', '-1');
			} else {
				cta.removeAttribute('aria-disabled');
				cta.removeAttribute('tabindex');
			}
			if (payload && payload.cartUrl) {
				cta.href = payload.cartUrl;
			} else if (bmaAj.cartUrl) {
				cta.href = bmaAj.cartUrl;
			}
		}
	}

	function escapeHtml(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	function escapeAttr(str) {
		return escapeHtml(str).replace(/'/g, '&#39;');
	}

	function updateBar() {
		if (!bar) return;
		var items = selectedCards();
		var countEl = bar.querySelector('[data-bma-selection-count]');
		var namesEl = bar.querySelector('[data-bma-selection-names]');
		var dirtyEl = bar.querySelector('[data-bma-selection-dirty]');
		var savedEl = bar.querySelector('[data-bma-selection-saved]');
		var cartLink = bar.querySelector('[data-bma-cart-link]');
		var cta = bar.querySelector('[data-bma-selection-cta]');

		if (!items.length) {
			bar.hidden = true;
			return;
		}

		bar.hidden = false;
		if (countEl) {
			countEl.textContent = items.length === 1
				? (strings.oneItemSelected || '1 szolgáltatáselem kiválasztva')
				: (strings.nItemsSelected || '%d szolgáltatáselem kiválasztva').replace('%d', String(items.length));
		}
		if (namesEl) {
			namesEl.textContent = items.map(function (i) { return i.name; }).join(' · ');
		}

		var dirty = isDirty();
		if (dirtyEl) dirtyEl.hidden = !dirty;
		if (savedEl) savedEl.hidden = dirty || !Object.keys(cartBaseline).length;
		if (cartLink) cartLink.hidden = dirty || !Object.keys(cartBaseline).length;
		if (cta) {
			if (!dirty && Object.keys(cartBaseline).length) {
				cta.disabled = true;
				cta.textContent = strings.alreadyAdded || 'Már hozzáadva az ajánlatkéréshez';
			} else {
				cta.disabled = false;
				cta.textContent = strings.addToQuote || 'Kiválasztott szolgáltatások hozzáadása az ajánlatkéréshez';
			}
		}
	}

	function buildPayload() {
		var draft = collectFromDom();
		var payload = $.extend({}, draft);
		Object.keys(cartBaseline).forEach(function (sid) {
			if (!payload[sid]) {
				var visible = false;
				root.querySelectorAll('.bma-item-checkbox').forEach(function (cb) {
					if (sourceId(cb.getAttribute('data-service-id') || '') === String(sid)) {
						visible = true;
					}
				});
				if (visible) {
					payload[sid] = [];
				}
			}
		});
		// Ensure every visible service is present (even empty) so clears apply.
		root.querySelectorAll('.bma-item-checkbox').forEach(function (cb) {
			var sid = sourceId(cb.getAttribute('data-service-id') || '');
			if (sid && !Object.prototype.hasOwnProperty.call(payload, sid)) {
				payload[sid] = [];
			}
		});
		return payload;
	}

	function applyCartResponse(res) {
		if (!res || !res.success || !res.data) {
			return $.Deferred().reject(res).promise();
		}
		var newCart = normalizeMap(res.data.selection || {});
		cartBaseline = newCart;
		writeDraft(newCart);
		bmaAj.cartSelection = newCart;
		var itemCount = typeof res.data.itemCount === 'number' ? res.data.itemCount : selectedCards().length;
		updateHeaderCount(itemCount);
		updateMobilebar(itemCount);
		renderSummary(res.data);
		syncToggleButtons();
		if (mode !== 'list' && bar) {
			updateBar();
		}
		return res;
	}

	function saveToCart(options) {
		options = options || {};
		var payload = buildPayload();
		writeDraft(collectFromDom());

		if (!Object.keys(payload).length && !Object.keys(cartBaseline).length) {
			updateHeaderCount(0);
			updateMobilebar(0);
			renderSummary({ itemCount: 0, summary: [] });
			return $.Deferred().resolve({ success: true, data: { itemCount: 0, summary: [], selection: {} } }).promise();
		}

		var data = {
			action: 'bma_add_to_cart',
			nonce: bmaAj.nonce,
			selections: JSON.stringify(payload)
		};
		if (options.redirect) {
			data.redirect = options.redirect;
		}

		return $.ajax({
			url: bmaAj.ajaxUrl,
			type: 'POST',
			data: data,
			xhrFields: { withCredentials: true }
		}).then(applyCartResponse);
	}

	function go(href) {
		suppressGuard = true;
		window.location.href = href;
	}

	function openUnsaved(href) {
		pendingHref = href;
		if (dialog && typeof dialog.showModal === 'function') {
			dialog.showModal();
		} else if (window.confirm(strings.unsavedConfirm || 'A kiválasztott elemeket még nem adta hozzá. Tovább lép?')) {
			writeDraft(cartBaseline);
			hydrateFromDraft();
			go(href);
		}
	}

	function closeDialog() {
		pendingHref = null;
		if (dialog && dialog.open) {
			dialog.close();
		}
	}

	// Init
	writeDraft(cartBaseline);
	hydrateFromDraft();
	syncToggleButtons();

	if (mode === 'list') {
		renderSummary({
			itemCount: (bmaAj.itemCount != null ? bmaAj.itemCount : selectedCards().length),
			summary: bmaAj.summary || [],
			cartUrl: bmaAj.cartUrl
		});
		updateHeaderCount(bmaAj.itemCount != null ? bmaAj.itemCount : selectedCards().length);
		updateMobilebar(bmaAj.itemCount != null ? bmaAj.itemCount : selectedCards().length);
	} else if (bar) {
		updateBar();
		updateHeaderCount(bmaAj.itemCount != null ? bmaAj.itemCount : 0);
		updateMobilebar(bmaAj.itemCount != null ? bmaAj.itemCount : 0);
	} else {
		updateHeaderCount(bmaAj.itemCount != null ? bmaAj.itemCount : 0);
		updateMobilebar(bmaAj.itemCount != null ? bmaAj.itemCount : 0);
	}

	$(root).on('change', '.bma-item-checkbox', function () {
		var card = cardFor(this);
		setCardSelected(card, this.checked);
		if (card && card.classList.contains('taskcard')) {
			syncToggleButtons();
		}
		writeDraft(collectFromDom());

		// List + detail: persist immediately.
		if (syncing) return;
		syncing = true;
		saveToCart().always(function () {
			syncing = false;
		});
	});

	$(root).on('click', '[data-bma-item-toggle]', function (e) {
		e.preventDefault();
		var card = this.closest('.taskcard');
		var cb = card ? card.querySelector('.bma-item-checkbox') : null;
		if (!cb) return;
		cb.checked = !cb.checked;
		$(cb).trigger('change');
	});

	$(document).on('click', '[data-bma-sum-del]', function (e) {
		e.preventDefault();
		var row = this.closest('.sum-item');
		if (!row) return;
		var itemId = row.getAttribute('data-item-id');
		var serviceId = row.getAttribute('data-service-id');
		var cb = root.querySelector(
			'.bma-item-checkbox[value="' + itemId + '"][data-service-id="' + serviceId + '"]'
		);
		if (!cb) {
			// Fallback: match by value only (translated service id aliases).
			cb = root.querySelector('.bma-item-checkbox[value="' + itemId + '"]');
		}
		if (cb) {
			cb.checked = false;
			$(cb).trigger('change');
		}
	});

	$(document).on('click', '[data-bma-summary-cta].is-disabled, [data-bma-mobilebar-cta].is-disabled', function (e) {
		e.preventDefault();
	});

	$(document).on('click', '[data-bma-selection-cta]', function (e) {
		e.preventDefault();
		if (mode === 'list') {
			go(bmaAj.cartUrl || '/');
			return;
		}
		var $btn = $(this);
		$btn.prop('disabled', true);
		saveToCart({}).done(function () {
			$btn.prop('disabled', false);
			updateBar();
		}).fail(function () {
			$btn.prop('disabled', false);
			alert(strings.uploadError || 'Hiba történt.');
		});
	});

	if (dialog) {
		$(dialog).on('click', '[data-bma-unsaved-add]', function () {
			var href = pendingHref;
			saveToCart().done(function () {
				closeDialog();
				if (href) go(href);
			}).fail(function () {
				alert(strings.uploadError || 'Hiba történt.');
			});
		});
		$(dialog).on('click', '[data-bma-unsaved-leave]', function () {
			var href = pendingHref;
			writeDraft(cartBaseline);
			closeDialog();
			if (href) go(href);
		});
		$(dialog).on('click', '[data-bma-unsaved-stay]', function () {
			closeDialog();
		});
	}

	// Detail page no longer uses unsaved navigation guard (cart syncs immediately).

})(jQuery);
