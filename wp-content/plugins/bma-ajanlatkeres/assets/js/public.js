(function ($) {
	'use strict';

	function postAjax(action, data) {
		data = data || {};
		data.action = action;
		data.nonce = bmaAj.nonce;
		return $.ajax({
			url: bmaAj.ajaxUrl,
			type: 'POST',
			data: data,
			xhrFields: { withCredentials: true }
		});
	}

	function postFormData(action, formData) {
		formData.append('action', action);
		formData.append('nonce', bmaAj.nonce);
		return $.ajax({
			url: bmaAj.ajaxUrl,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			xhrFields: { withCredentials: true }
		});
	}

	// Mobile nav
	$('#bma-nav-toggle').on('click', function () {
		var $btn = $(this);
		var $inner = $('#bma-header-inner');
		var open = !$inner.hasClass('is-nav-open');
		$inner.toggleClass('is-nav-open', open);
		$btn.attr('aria-expanded', open ? 'true' : 'false');
		$btn.attr('aria-label', open ? 'Menü bezárása' : 'Menü megnyitása');
	});

	// Add service(s) to cart handled by selection.js on service pages.

	// Remove from cart (service group or single item line).
	$(document).on('click', '.bma-remove-cart', function (e) {
		e.preventDefault();
		if (!confirm(bmaAj.strings.confirmRemove)) return;
		var $btn = $(this);
		var itemId = $btn.data('item-id');
		var key = $btn.data('cart-key');
		var payload = {};
		if (itemId) {
			payload.item_id = itemId;
		} else if (key) {
			payload.cart_key = key;
		} else {
			return;
		}
		postAjax('bma_remove_from_cart', payload).done(function () {
			location.reload();
		});
	});

	// Drop zone: click / drag to file input
	$(document).on('click', '[data-bma-drop]', function (e) {
		if ($(e.target).is('input[type="file"]')) return;
		var $input = $(this).find('input[type="file"]').first();
		if ($input.length) $input.trigger('click');
	});
	$(document).on('dragover dragenter', '[data-bma-drop]', function (e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).addClass('is-dragover');
	});
	$(document).on('dragleave drop', '[data-bma-drop]', function (e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).removeClass('is-dragover');
		if (e.type === 'drop' && e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files.length) {
			var input = $(this).find('input[type="file"]').get(0);
			if (input) {
				input.files = e.originalEvent.dataTransfer.files;
				$(input).trigger('change');
			}
		}
	});

	// Submit quote request with unified fields (multipart)
	$('#bma-quote-submit-form').on('submit', function (e) {
		e.preventDefault();
		var $form = $(this);
		var $errors = $('#bma-form-errors');
		$errors.prop('hidden', true).empty();

		if (!$form.find('[name="privacy"]').is(':checked')) {
			$errors.prop('hidden', false).text(bmaAj.strings.required || 'Az adatvédelmi hozzájárulás kötelező.');
			return;
		}

		var formData = new FormData(this);
		formData.set('privacy', $form.find('[name="privacy"]').is(':checked') ? 1 : 0);
		formData.set('create_account', $form.find('[name="create_account"]').is(':checked') ? 1 : 0);

		postFormData('bma_submit_quote_request', formData).done(function (res) {
			if (res.success && res.data.redirect) {
				if (window.bmaTrackEvent) bmaTrackEvent('quote_request_submitted');
				window.location.href = res.data.redirect;
			} else {
				$errors.prop('hidden', false).text(res.data && res.data.message ? res.data.message : bmaAj.strings.required);
			}
		}).fail(function () {
			$errors.prop('hidden', false).text(bmaAj.strings.uploadError);
		});
	});

	// Accept quote → payment page
	$('.bma-accept-quote').on('click', function () {
		var $btn = $(this);
		postAjax('bma_accept_quote', {
			request_id: $btn.data('request'),
			quote_id: $btn.data('quote')
		}).done(function (res) {
			if (res.success && res.data.redirect) {
				window.location.href = res.data.redirect;
			}
		});
	});

	// Stripe checkout
	$('.bma-stripe-pay').on('click', function () {
		var $btn = $(this);
		$btn.prop('disabled', true).text(bmaAj.strings.processing || 'Átirányítás…');
		postAjax('bma_create_stripe_session', {
			request_id: $btn.data('request'),
			quote_id: $btn.data('quote')
		}).done(function (res) {
			if (res.success && res.data.redirect) {
				window.location.href = res.data.redirect;
			} else {
				alert(res.data && res.data.message ? res.data.message : bmaAj.strings.uploadError);
				$btn.prop('disabled', false).text('Kártyás fizetés (Stripe)');
			}
		}).fail(function () {
			$btn.prop('disabled', false);
			alert(bmaAj.strings.uploadError);
		});
	});

	// Client portal document upload
	$('#bma-client-upload-form').on('submit', function (e) {
		e.preventDefault();
		var $form = $(this);
		var $status = $('#bma-upload-status');
		var formData = new FormData(this);
		formData.append('action', 'bma_upload_document');
		formData.append('nonce', bmaAj.nonce);

		$status.text(bmaAj.strings.uploading || 'Feltöltés…');

		$.ajax({
			url: bmaAj.ajaxUrl,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false
		}).done(function (res) {
			if (res.success) {
				$status.text(bmaAj.strings.uploadSuccess || 'Feltöltés sikeres.');
				setTimeout(function () { location.reload(); }, 800);
			} else {
				$status.text(res.data && res.data.message ? res.data.message : bmaAj.strings.uploadError);
			}
		}).fail(function () {
			$status.text(bmaAj.strings.uploadError);
		});
	});

	// Contact form: quote hint for service topics
	$(document).on('change', '[data-bma-topic]', function () {
		var $sel = $(this);
		var topics = ($sel.attr('data-quote-topics') || '').split(',');
		var val = $sel.val();
		var show = topics.indexOf(val) !== -1;
		$('[data-bma-quote-hint]').prop('hidden', !show);
	});

	// Contact map (OpenStreetMap + Leaflet), gated by consent / explicit load
	function initContactMap() {
		var root = document.querySelector('[data-bma-map]');
		if (!root || typeof L === 'undefined' || root.getAttribute('data-map-ready') === '1') {
			return;
		}

		var canvas = root.querySelector('[data-bma-map-canvas]');
		var note = root.querySelector('[data-bma-map-note]');
		var loadBtn = root.querySelector('[data-bma-map-load]');
		if (!canvas) {
			return;
		}

		var lat = parseFloat(root.getAttribute('data-lat'));
		var lng = parseFloat(root.getAttribute('data-lng'));
		var zoom = parseInt(root.getAttribute('data-zoom'), 10) || 15;
		var label = root.getAttribute('data-label') || '';
		var address = root.getAttribute('data-address') || '';

		canvas.hidden = false;
		if (note) {
			note.hidden = true;
		}
		if (loadBtn) {
			loadBtn.hidden = true;
		}

		var map = L.map(canvas, {
			scrollWheelZoom: false,
			attributionControl: true
		}).setView([lat, lng], zoom);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
		}).addTo(map);

		var icon = L.divIcon({
			className: 'bma-map-marker',
			html: '<span class="bma-map-marker-pin"></span>',
			iconSize: [28, 36],
			iconAnchor: [14, 34],
			popupAnchor: [0, -28]
		});

		var popup = label + (address ? '<br>' + address : '');
		L.marker([lat, lng], { icon: icon }).addTo(map).bindPopup(popup);

		root.setAttribute('data-map-ready', '1');
		setTimeout(function () {
			map.invalidateSize();
		}, 100);
	}

	window.bmaLoadContactMap = function () {
		try {
			var raw = localStorage.getItem('bma_cookie_consent');
			var c = raw ? JSON.parse(raw) : {};
			c.map = 1;
			if (!c.necessary) {
				c.necessary = 1;
			}
			localStorage.setItem('bma_cookie_consent', JSON.stringify(c));
		} catch (e) { /* ignore */ }
		initContactMap();
	};

	$(document).on('click', '[data-bma-map-load]', function (e) {
		e.preventDefault();
		window.bmaLoadContactMap();
	});

	try {
		var stored = localStorage.getItem('bma_cookie_consent');
		if (stored) {
			var consent = JSON.parse(stored);
			if (consent.map) {
				initContactMap();
			}
		}
	} catch (e) { /* ignore */ }

	// Reference gallery lightbox
	var $lb = $('[data-bma-lightbox-dialog]');
	if ($lb.length) {
		var dialog = $lb.get(0);
		var $img = $lb.find('[data-bma-lightbox-img]');
		$(document).on('click', '[data-bma-lightbox]', function (e) {
			e.preventDefault();
			var src = $(this).attr('data-bma-lightbox');
			if (!src || !dialog || typeof dialog.showModal !== 'function') return;
			$img.attr('src', src);
			dialog.showModal();
		});
		$lb.on('click', '[data-bma-lightbox-close]', function () {
			if (dialog.open) dialog.close();
		});
		$lb.on('click', function (e) {
			if (e.target === dialog && dialog.open) dialog.close();
		});
	}

})(jQuery);
