/**
 * Quote request v2 wizard (step-by-step UI over one multipart form).
 *
 * Mirrors the rules of BMA_AJ_Quote_Flow for instant feedback; the server
 * validates everything again. Without this script every step is visible
 * and the form posts normally.
 */
(function () {
	'use strict';

	var form = document.getElementById('bma-quote-wizard');
	var dataEl = document.getElementById('bma-wiz-data');
	if (!form || !dataEl) return;

	var D = JSON.parse(dataEl.textContent || '{}');
	var S = D.strings || {};
	var STEPS = 7;
	var current = 1;
	var reached = 1;
	var autoApplied = {};   // add-on id => true while switched on by a rule
	var softWarned = false;

	var $ = function (sel, root) { return (root || form).querySelector(sel); };
	var $$ = function (sel, root) { return Array.prototype.slice.call((root || form).querySelectorAll(sel)); };
	var parse = function (raw, fallback) { try { return raw ? JSON.parse(raw) : fallback; } catch (e) { return fallback; } };
	var intersects = function (a, b) { return a.some(function (x) { return b.indexOf(x) !== -1; }); };

	form.classList.add('is-js');
	form.elements.addons_js.value = '1';
	$$('[data-wiz-steps], [data-wiz-nav]', document).forEach(function (el) { el.hidden = false; });

	/* ---------------- state ---------------- */

	function checkedValues(name) {
		return $$('input[name="' + name + '"]:checked').map(function (el) { return el.value; });
	}

	function fieldWrappers() {
		return $$('[data-bma-field]').filter(function (w) {
			return w.querySelector('[name^="field_values["]');
		});
	}

	function fieldKey(w) { return w.getAttribute('data-bma-field'); }

	function fieldValue(w) {
		var inputs = $$('[name^="field_values["]', w);
		if (!inputs.length) return '';
		var first = inputs[0];
		if (first.type === 'file') {
			return inputs.reduce(function (n, el) { return n + (el.files ? el.files.length : 0); }, 0) ? 'file' : '';
		}
		if (first.type === 'radio') {
			var c = inputs.filter(function (el) { return el.checked; })[0];
			return c ? c.value : '';
		}
		if (first.type === 'checkbox') {
			return inputs.filter(function (el) { return el.checked; }).map(function (el) { return el.value; });
		}
		return (first.value || '').trim();
	}

	function state() {
		var st = {
			clientType: checkedValues('client_type')[0] || '',
			phase: checkedValues('design_phase')[0] || '',
			items: checkedValues('items[]').map(Number),
			itemPhases: {},
			addons: checkedValues('addons[]').map(Number),
			values: {}
		};
		$$('[data-wiz-override]').forEach(function (box) {
			var sel = box.querySelector('select');
			var id = Number((sel.name.match(/\[(\d+)\]/) || [])[1]);
			if (!sel.disabled && sel.value && st.items.indexOf(id) !== -1) st.itemPhases[id] = sel.value;
		});
		fieldWrappers().forEach(function (w) { st.values[fieldKey(w)] = fieldValue(w); });
		return st;
	}

	function effectivePhases(st) {
		var out = {};
		st.items.forEach(function (id) {
			var item = D.items[id];
			if (item && item.hasPhase) out[id] = st.itemPhases[id] || st.phase;
		});
		return out;
	}

	function serviceSlugs(st) {
		return st.items.map(function (id) { return D.items[id] ? D.items[id].slug : ''; });
	}

	function context(st) {
		var ctx = {};
		Object.keys(st.values).forEach(function (k) { ctx[k] = st.values[k]; });
		var ph = effectivePhases(st);
		ctx._phase = Object.keys(ph).map(function (k) { return ph[k]; });
		ctx._addon = st.addons.map(function (id) { return D.addons[id] ? D.addons[id].slug : ''; });
		return ctx;
	}

	function condMatches(cond, ctx) {
		var have = ctx[cond.field];
		if (have === undefined || have === null || have === '') return false;
		have = [].concat(have).map(String);
		return intersects(have, (cond.values || []).map(String));
	}

	function ruleMatches(rule, ctx) {
		return (rule.any || []).some(function (c) { return condMatches(c, ctx); });
	}

	function fieldVisible(w, st, ctx) {
		var items = parse(w.getAttribute('data-items'), null);
		if (items && !intersects(items, st.items)) return false;
		var cond = parse(w.getAttribute('data-cond'), {});
		if (cond.field && !condMatches(cond, ctx)) return false;
		return true;
	}

	function hasValue(v) { return Array.isArray(v) ? v.length > 0 : v !== '' && v !== undefined; }

	function fieldRequired(w, st) {
		var cond = parse(w.getAttribute('data-cond'), {});
		var req = w.getAttribute('data-required') === '1';
		if (cond.required_services) req = req || intersects(cond.required_services, serviceSlugs(st));
		if (req && cond.one_of) {
			req = !cond.one_of.some(function (k) { return hasValue(st.values[k]); });
		}
		return req;
	}

	/* ---------------- UI sync ---------------- */

	function syncChoiceClasses() {
		$$('.optcard').forEach(function (c) { c.classList.toggle('is-sel', !!c.querySelector('input:checked')); });
		$$('label.task').forEach(function (t) {
			var i = t.querySelector('input');
			if (i) t.classList.toggle('is-sel', i.checked);
		});
	}

	function update() {
		var st = state();

		// SF / DALI only for municipalities.
		$$('[data-only]').forEach(function (el) {
			var ok = el.getAttribute('data-only').split(',').indexOf(st.clientType) !== -1;
			if (el.tagName === 'OPTION') {
				el.hidden = !ok;
				el.disabled = !ok;
				if (!ok && el.selected) el.parentNode.value = '';
			} else {
				el.hidden = !ok;
				var input = el.querySelector('input');
				if (!ok && input.checked) input.checked = false;
			}
		});

		// Per-item phase override.
		$$('[data-wiz-override]').forEach(function (box) {
			var task = box.parentNode.querySelector('input[name="items[]"]');
			var toggle = box.querySelector('[data-wiz-override-toggle]');
			var sel = box.querySelector('select');
			box.hidden = !task.checked;
			sel.hidden = !toggle.checked;
			sel.disabled = !task.checked || !toggle.checked;
		});

		st = state();
		var needsPhase = st.items.some(function (id) { return D.items[id] && D.items[id].hasPhase; });
		var note = $('[data-wiz-nophase-note]');
		if (note) note.hidden = !(st.items.length && !needsPhase);

		// Permits of selected groups only.
		var groups = st.items.map(function (id) { return String(D.items[id].service); });
		$$('[data-wiz-permits] [data-service]').forEach(function (lbl) {
			var on = groups.indexOf(lbl.getAttribute('data-service')) !== -1;
			lbl.hidden = !on;
			if (!on) lbl.querySelector('input').checked = false;
		});
		var permits = $('[data-wiz-permits]');
		if (permits) permits.hidden = !groups.length;

		// Fields: visibility first (auto add-ons look at visible answers only).
		var ctx = context(state());
		for (var pass = 0; pass < 2; pass++) {
			fieldWrappers().forEach(function (w) {
				var vis = fieldVisible(w, st, ctx);
				w.hidden = !vis;
				$$('input, select, textarea', w).forEach(function (el) { el.disabled = !vis; });
			});
			st = state();
			fieldWrappers().forEach(function (w) { if (w.hidden) delete st.values[fieldKey(w)]; });
			ctx = context(st);
		}

		// Auto add-ons.
		$$('[data-addon]').forEach(function (lbl) {
			var id = lbl.getAttribute('data-addon');
			var addon = D.addons[id];
			if (!addon || !addon.rule) return;
			var input = lbl.querySelector('input');
			var on = ruleMatches(addon.rule, ctx);
			if (on && !autoApplied[id]) {
				input.checked = true;
				autoApplied[id] = true;
			} else if (!on && autoApplied[id]) {
				input.checked = false;
				delete autoApplied[id];
			}
			lbl.classList.toggle('is-locked', on && addon.locked);
			if (on && addon.locked) input.checked = true;
			var autoBadge = lbl.querySelector('[data-wiz-auto]');
			var lockBadge = lbl.querySelector('[data-wiz-locked]');
			if (autoBadge) autoBadge.hidden = !(on && !addon.locked && input.checked);
			if (lockBadge) lockBadge.hidden = !(on && addon.locked);
		});

		// Required marks that depend on the selection.
		st = state();
		fieldWrappers().forEach(function (w) {
			var label = w.querySelector('.field-label');
			if (!label || w.getAttribute('data-required') === '1') return;
			var star = label.querySelector('.req-dyn');
			var req = fieldRequired(w, st);
			if (req && !star) {
				star = document.createElement('span');
				star.className = 'required req-dyn';
				star.textContent = ' *';
				label.appendChild(star);
			} else if (!req && star) {
				star.remove();
			}
		});
		var companyStar = $('[data-wiz-company-star]');
		if (companyStar) companyStar.hidden = ['ceg', 'onkormanyzat'].indexOf(st.clientType) === -1;

		$$('.wiz-docgroup').forEach(function (g) {
			g.hidden = !$$('[data-bma-field]', g).some(function (w) { return !w.hidden; });
		});

		syncChoiceClasses();
		renderMini(st);
	}

	/* ---------------- validation ---------------- */

	function setError(w, msg) {
		if (!w) return;
		var slot = w.querySelector('.bma-field-error');
		w.classList.toggle('has-error', !!msg);
		if (slot) {
			slot.textContent = msg || '';
			slot.hidden = !msg;
		}
	}

	function clearErrors(stepEl) {
		$$('[data-bma-field]', stepEl).forEach(function (w) { setError(w, ''); });
		showAlert('');
	}

	function showAlert(msg) {
		var a = document.getElementById('bma-wiz-alert');
		a.textContent = msg || '';
		a.hidden = !msg;
	}

	function validateStep(n) {
		var stepEl = $('.wiz-step[data-step="' + n + '"]');
		var st = state();
		var errors = 0;
		var special = '';
		clearErrors(stepEl);

		var bad = function (w, msg) { setError(w, msg || S.required); errors++; };

		if (n === 1 && !st.clientType) bad($('[data-bma-field="client_type"]'));

		var needsPhase = Object.keys(effectivePhases(st)).some(function (id) { return !effectivePhases(st)[id]; });
		if (n === 2 && st.items.length && needsPhase) {
			bad($('[data-bma-field="design_phase"]'), S.noPhase);
			special = S.noPhase;
		}
		if (n === 3) {
			if (!st.items.length) {
				bad($('[data-bma-field="items"]'), S.noItems);
				special = S.noItems;
			} else if (needsPhase) {
				goTo(2, true);
				setError($('[data-bma-field="design_phase"]'), S.noPhase);
				showAlert(S.noPhase);
				return false;
			}
		}

		$$('[data-bma-field]', stepEl).forEach(function (w) {
			if (w.hidden || !w.querySelector('[name^="field_values["]')) return;
			var key = fieldKey(w);
			if (fieldRequired(w, st) && !hasValue(st.values[key])) bad(w);
		});

		if (n === 5 && !softWarned) {
			var plan = $('[data-bma-field="alaprajz"]');
			if (plan && !plan.hidden && !hasValue(st.values.alaprajz)) {
				softWarned = true;
				setError(plan, S.softFloorplan);
				if (!errors) {
					showAlert(S.softFloorplan);
					return false;
				}
			}
		}

		if (n === 6) {
			['name', 'phone'].forEach(function (k) {
				var el = form.elements[k];
				if (!el.value.trim()) bad($('[data-bma-field="' + k + '"]'));
			});
			var email = form.elements.email.value.trim();
			if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) bad($('[data-bma-field="email"]'), email ? S.badEmail : S.required);
			if (['ceg', 'onkormanyzat'].indexOf(st.clientType) !== -1 && !form.elements.company.value.trim()) bad($('[data-bma-field="company"]'));
			if (!form.elements.privacy.checked) bad($('[data-bma-field="privacy"]'));
		}

		if (errors) {
			showAlert(special && errors === 1 ? special : S.missing);
			var first = stepEl.querySelector('.has-error');
			if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
		return !errors;
	}

	/* ---------------- navigation ---------------- */

	function goTo(n, force) {
		n = Math.max(1, Math.min(STEPS, n));
		if (!force && n > current) {
			for (var s = current; s < n; s++) {
				if (!validateStep(s)) { show(s); return; }
			}
		}
		show(n);
	}

	function show(n) {
		current = n;
		reached = Math.max(reached, n);
		$$('.wiz-step').forEach(function (el) {
			el.classList.toggle('is-current', Number(el.getAttribute('data-step')) === n);
		});
		$$('[data-wiz-goto]', document).forEach(function (b) {
			var s = Number(b.getAttribute('data-wiz-goto'));
			if (!b.classList.contains('stepchip')) return;
			b.classList.toggle('is-now', s === n);
			b.classList.toggle('is-done', s < n || (s <= reached && s !== n));
			b.disabled = s > reached;
			if (s === n) b.setAttribute('aria-current', 'step'); else b.removeAttribute('aria-current');
		});
		$('[data-wiz-prev]').hidden = n === 1;
		$('[data-wiz-next]').hidden = n === STEPS;
		if (n === STEPS) renderReview();
		var top = document.querySelector('.bma-wiz-body');
		if (top && top.getBoundingClientRect().top < 0) top.scrollIntoView({ behavior: 'smooth' });
	}

	/* ---------------- summaries ---------------- */

	function esc(s) {
		return String(s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	function groupsOf(st) {
		var groups = {};
		var order = [];
		st.items.forEach(function (id) {
			var it = D.items[id];
			if (!it) return;
			if (!groups[it.service]) { groups[it.service] = []; order.push(it.service); }
			groups[it.service].push(id);
		});
		return order.map(function (sid) { return { sid: sid, items: groups[sid] }; });
	}

	function renderMini(st) {
		var box = $('[data-wiz-mini]', document);
		var count = $('[data-wiz-count]', document);
		if (count) count.textContent = String(st.items.length);
		if (!box) return;
		if (!st.items.length) {
			box.innerHTML = '<div class="sum-empty">' + esc(S.summaryEmpty) + '</div>';
			return;
		}
		var html = '';
		if (st.phase) html += '<div class="sum-item"><span><strong>' + esc(D.phases[st.phase]) + '</strong></span></div>';
		groupsOf(st).forEach(function (g) {
			html += '<div class="sum-item"><span><strong>' + esc(D.services[g.sid].short) + '</strong><span class="hint">' +
				esc(g.items.map(function (id) { return D.items[id].name; }).join(', ')) + '</span></span></div>';
		});
		if (st.addons.length) {
			html += '<div class="sum-item"><span><strong>' + esc(stepLabel(4)) + '</strong><span class="hint">' +
				esc(st.addons.map(function (id) { return D.addons[id] ? D.addons[id].name : ''; }).join(', ')) + '</span></span></div>';
		}
		box.innerHTML = html;
	}

	function stepLabel(n) {
		var chip = document.querySelector('.stepchip[data-wiz-goto="' + n + '"] span');
		return chip ? chip.textContent : '';
	}

	function fieldRows(stepEl) {
		var rows = [];
		$$('[data-bma-field]', stepEl).forEach(function (w) {
			if (w.hidden) return;
			var inputs = $$('[name^="field_values["]', w);
			if (!inputs.length) return;
			var label = w.querySelector('.field-label');
			var text = label ? label.childNodes[0].textContent.trim() : fieldKey(w);
			var val = '';
			var first = inputs[0];
			if (first.type === 'file') {
				val = inputs.reduce(function (names, el) {
					return names.concat(Array.prototype.map.call(el.files || [], function (f) { return f.name; }));
				}, []).join(', ');
			} else if (first.type === 'radio') {
				var c = inputs.filter(function (el) { return el.checked; })[0];
				val = c ? c.parentNode.textContent.trim() : '';
			} else if (first.tagName === 'SELECT') {
				val = first.value ? first.options[first.selectedIndex].text : '';
			} else {
				val = first.value.trim();
			}
			if (val) rows.push([text, val]);
		});
		return rows;
	}

	function block(step, title, rows) {
		var html = '<div class="wiz-rev"><div class="wiz-rev-head"><h3 class="h4">' + esc(title) + '</h3>' +
			'<button type="button" class="btn btn--outline btn--sm" data-wiz-goto="' + step + '">' + esc(form.getAttribute('data-edit') || '✎') + '</button></div>';
		if (!rows.length) html += '<p class="small muted">' + esc(S.none) + '</p>';
		rows.forEach(function (r) {
			html += '<div class="rev-row"><span class="small muted wiz-rev-label">' + esc(r[0]) + '</span><strong>' + esc(r[1]) + '</strong></div>';
		});
		return html + '</div>';
	}

	function renderReview() {
		var st = state();
		var ctx = context(st);
		var ph = effectivePhases(st);
		var out = '';

		var project = [];
		if (st.clientType) project.push([$('[data-bma-field="client_type"] .field-label').childNodes[0].textContent.trim(), D.clientTypes[st.clientType]]);
		out += block(1, stepLabel(1), project.concat(fieldRows($('.wiz-step[data-step="1"]'))));

		out += block(2, stepLabel(2), st.phase ? [[stepLabel(2), D.phases[st.phase]]] : []);

		var items = groupsOf(st).map(function (g) {
			var names = g.items.map(function (id) {
				var it = D.items[id];
				var extra = '';
				if (!it.hasPhase) extra = ' (' + S.noPhaseShort + ')';
				else if (st.itemPhases[id] && st.itemPhases[id] !== st.phase) extra = ' (' + D.phases[ph[id]] + ')';
				return it.name + extra;
			});
			return [D.services[g.sid].short, names.join(', ')];
		});
		out += block(3, stepLabel(3), items);

		var addons = st.addons.map(function (id) {
			var a = D.addons[id];
			var auto = a && a.rule && ruleMatches(a.rule, ctx);
			return [a ? a.name : '', auto ? S.auto : '✓'];
		});
		out += block(4, stepLabel(4), addons.concat(fieldRows($('.wiz-step[data-step="4"]'))));

		out += block(5, stepLabel(5), fieldRows($('.wiz-step[data-step="5"]')));

		var contact = [];
		['name', 'company', 'email', 'phone', 'tax_number', 'billing_address', 'note'].forEach(function (k) {
			var el = form.elements[k];
			if (!el || !el.value.trim()) return;
			var label = form.querySelector('label[for="' + el.id + '"]');
			contact.push([label ? label.childNodes[0].textContent.trim() : k, el.value.trim()]);
		});
		out += block(6, stepLabel(6), contact);

		document.getElementById('bma-wiz-review').innerHTML = out;
	}

	/* ---------------- submit ---------------- */

	function submit(e) {
		e.preventDefault();
		for (var s = 1; s < STEPS; s++) {
			if (!validateStep(s)) { show(s); return; }
		}
		var btn = form.querySelector('.wiz-submit');
		var label = btn.textContent;
		btn.disabled = true;
		btn.textContent = S.sending;

		var fd = new FormData(form);
		fd.set('action', 'bma_submit_quote_v2');
		var url = (window.bmaAj && window.bmaAj.ajaxUrl) || form.getAttribute('action').replace('admin-post.php', 'admin-ajax.php');

		fetch(url, { method: 'POST', body: fd, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (res && res.success && res.data.redirect) {
					window.location.href = res.data.redirect;
					return;
				}
				var d = (res && res.data) || {};
				btn.disabled = false;
				btn.textContent = label;
				var fields = d.fields || {};
				show(d.step || current);
				Object.keys(fields).forEach(function (k) { setError($('[data-bma-field="' + k + '"]'), fields[k]); });
				showAlert(d.message || S.sendError);
			})
			.catch(function () {
				btn.disabled = false;
				btn.textContent = label;
				showAlert(S.sendError);
			});
	}

	/* ---------------- wire up ---------------- */

	form.addEventListener('change', update);
	form.addEventListener('input', function (e) {
		if (e.target.matches('input[type="text"], input[type="number"], textarea')) update();
	});
	form.addEventListener('click', function (e) {
		if (e.target.closest('label.task.is-locked')) { e.preventDefault(); return; }
		var go = e.target.closest('[data-wiz-goto]');
		if (go) { e.preventDefault(); goTo(Number(go.getAttribute('data-wiz-goto')), true); }
	});
	document.querySelector('[data-wiz-steps]').addEventListener('click', function (e) {
		var go = e.target.closest('[data-wiz-goto]');
		if (go && !go.disabled) goTo(Number(go.getAttribute('data-wiz-goto')), Number(go.getAttribute('data-wiz-goto')) <= reached);
	});
	$('[data-wiz-next]').addEventListener('click', function () {
		if (!validateStep(current)) return;
		// Before the summary, re-check earlier steps (e.g. floor area became required in step 3).
		if (current === STEPS - 1) {
			for (var s = 1; s < current; s++) {
				if (!validateStep(s)) { show(s); return; }
			}
		}
		show(current + 1);
	});
	$('[data-wiz-prev]').addEventListener('click', function () { show(current - 1); });
	form.addEventListener('submit', submit);

	// Show the file names chosen in a drop zone.
	form.addEventListener('change', function (e) {
		if (e.target.type !== 'file') return;
		var drop = e.target.closest('.drop');
		if (!drop) return;
		var list = drop.querySelector('.wiz-files') || drop.appendChild(document.createElement('span'));
		list.className = 'wiz-files small';
		list.textContent = Array.prototype.map.call(e.target.files, function (f) { return f.name; }).join(', ');
	});

	update();
	show(1);
})();
