/**
 * Service selection UX (list + detail): draft state, summary bar, unsaved guard.
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
	var pendingHref = null;
	var suppressGuard = false;
	var sourceMap = bmaAj.serviceSources || {};
	var cartBaseline = normalizeMap(bmaAj.cartSelection || {});

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
			// Prefer densest list if aliases collide.
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
		var source = hasDraft ? draft : cartBaseline;

		root.querySelectorAll('.bma-item-checkbox').forEach(function (cb) {
			var sid = sourceId(cb.getAttribute('data-service-id') || '');
			var id = parseInt(cb.value, 10);
			var selected = !!(source[sid] && source[sid].indexOf(id) !== -1);
			cb.checked = selected;
			var card = cb.closest('.bma-select-card');
			if (card) {
				card.classList.toggle('is-selected', selected);
			}
		});
	}

	function isDirty() {
		return !mapsEqual(collectFromDom(), cartBaseline);
	}

	function selectedCards() {
		return Array.prototype.slice.call(root.querySelectorAll('.bma-item-checkbox:checked')).map(function (cb) {
			var card = cb.closest('.bma-select-card');
			return {
				id: parseInt(cb.value, 10),
				name: card ? card.getAttribute('data-item-name') : '',
				serviceId: cb.getAttribute('data-service-id'),
				serviceName: card ? card.getAttribute('data-service-name') : ''
			};
		});
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
				? (bmaAj.strings.oneItemSelected || '1 szolgáltatáselem kiválasztva')
				: (bmaAj.strings.nItemsSelected || '%d szolgáltatáselem kiválasztva').replace('%d', String(items.length));
		}
		if (namesEl) {
			namesEl.textContent = items.map(function (i) { return i.name; }).join(' · ');
		}

		var dirty = isDirty();
		if (dirtyEl) dirtyEl.hidden = !dirty;
		if (savedEl) savedEl.hidden = dirty || !Object.keys(cartBaseline).length;
		if (cartLink) cartLink.hidden = dirty || !Object.keys(cartBaseline).length;
		if (cta) {
			if (mode === 'list') {
				cta.disabled = false;
				cta.textContent = bmaAj.strings.continueQuote || 'Tovább az ajánlatkéréshez';
			} else if (!dirty && Object.keys(cartBaseline).length) {
				cta.disabled = true;
				cta.textContent = bmaAj.strings.alreadyAdded || 'Már hozzáadva az ajánlatkéréshez';
			} else {
				cta.disabled = false;
				cta.textContent = bmaAj.strings.addToQuote || 'Kiválasztott szolgáltatások hozzáadása az ajánlatkéréshez';
			}
		}
	}

	function updateHeaderCount(count) {
		var el = document.getElementById('bma-header-cart-count');
		if (!el) return;
		el.textContent = String(count);
		el.setAttribute('data-count', String(count));
		var link = el.closest('a');
		if (link && bmaAj.cartUrl && bmaAj.servicesUrl) {
			link.href = count > 0 ? bmaAj.cartUrl : bmaAj.servicesUrl;
		}
	}

	function saveToCart(options) {
		options = options || {};
		var draft = collectFromDom();
		writeDraft(draft);

		if (!Object.keys(draft).length && !Object.keys(cartBaseline).length) {
			return $.Deferred().reject().promise();
		}

		// Include emptied services that were in cart so they get removed when unchecked.
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
		}).then(function (res) {
			if (!res || !res.success) {
				return $.Deferred().reject(res).promise();
			}
			var newCart = normalizeMap(res.data.selection || {});
			cartBaseline = newCart;
			// Keep draft selections for services not on this page (still unsaved elsewhere).
			var nextDraft = $.extend({}, newCart);
			Object.keys(draft).forEach(function (sid) {
				var visible = root.querySelector('.bma-item-checkbox[data-service-id="' + sid + '"]');
				if (!visible && draft[sid] && draft[sid].length) {
					nextDraft[sid] = draft[sid];
				}
			});
			writeDraft(nextDraft);
			updateHeaderCount(res.data.count || 0);
			updateBar();
			return res;
		});
	}

	function go(href) {
		suppressGuard = true;
		window.location.href = href;
	}

	function openUnsaved(href) {
		pendingHref = href;
		if (dialog && typeof dialog.showModal === 'function') {
			dialog.showModal();
		} else if (window.confirm(bmaAj.strings.unsavedConfirm || 'A kiválasztott elemeket még nem adta hozzá. Tovább lép?')) {
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
	var existingDraft = readDraft();
	if (!Object.keys(existingDraft).length && Object.keys(cartBaseline).length) {
		writeDraft(cartBaseline);
	}
	hydrateFromDraft();
	updateBar();

	$(root).on('change', '.bma-item-checkbox', function () {
		var card = this.closest('.bma-select-card');
		if (card) {
			card.classList.toggle('is-selected', this.checked);
		}
		writeDraft(collectFromDom());
		var savedEl = bar && bar.querySelector('[data-bma-selection-saved]');
		if (savedEl) savedEl.hidden = true;
		updateBar();
	});

	$(document).on('click', '[data-bma-selection-cta]', function (e) {
		e.preventDefault();
		var redirect = mode === 'list' ? (bmaAj.cartUrl || '/ajanlatkosar/') : '';
		var $btn = $(this);
		$btn.prop('disabled', true);
		saveToCart({ redirect: redirect }).done(function (res) {
			if (mode === 'list') {
				go((res.data && res.data.redirect) || bmaAj.cartUrl);
				return;
			}
			$btn.prop('disabled', false);
			updateBar();
		}).fail(function () {
			$btn.prop('disabled', false);
			alert(bmaAj.strings.uploadError || 'Hiba történt.');
		});
	});

	if (dialog) {
		$(dialog).on('click', '[data-bma-unsaved-add]', function () {
			var href = pendingHref;
			saveToCart().done(function () {
				closeDialog();
				if (href) go(href);
			}).fail(function () {
				alert(bmaAj.strings.uploadError || 'Hiba történt.');
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

	$(document).on('click', 'a[href]', function (e) {
		if (suppressGuard || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
		if (e.defaultPrevented) return;
		var a = this;
		if (a.hasAttribute('download') || a.getAttribute('target') === '_blank') return;
		var href = a.href;
		if (!href || href.indexOf('javascript:') === 0 || href.indexOf('#') === href.length - 1) return;
		try {
			var url = new URL(href, window.location.href);
			if (url.origin !== window.location.origin) return;
			if (url.pathname === window.location.pathname && url.search === window.location.search) return;
		} catch (err) {
			return;
		}
		if (!isDirty()) return;
		e.preventDefault();
		openUnsaved(href);
	});

	window.addEventListener('beforeunload', function (e) {
		if (suppressGuard || !isDirty()) return;
		e.preventDefault();
		e.returnValue = '';
	});

})(jQuery);
