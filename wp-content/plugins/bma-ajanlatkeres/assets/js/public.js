(function ($) {
	'use strict';

	function postAjax(action, data) {
		data = data || {};
		data.action = action;
		data.nonce = bmaAj.nonce;
		return $.post(bmaAj.ajaxUrl, data);
	}

	// Add service to cart from single service page
	$('#bma-service-quote-form').on('submit', function (e) {
		e.preventDefault();
		var $form = $(this);
		var serviceId = $form.data('service-id');
		var selectedItems = [];
		$form.find('.bma-item-checkbox:checked').each(function () {
			selectedItems.push($(this).val());
		});

		var fieldValues = {};
		$form.find('[name^="field_values"]').each(function () {
			var $el = $(this);
			var name = $el.attr('name').replace('field_values[', '').replace(']', '');
			if ($el.attr('type') === 'checkbox' && !$el.is(':checked')) return;
			if ($el.attr('type') === 'checkbox') {
				fieldValues[name] = $el.val();
			} else {
				fieldValues[name] = $el.val();
			}
		});

		postAjax('bma_add_to_cart', {
			service_id: serviceId,
			selected_items: selectedItems,
			field_values: fieldValues
		}).done(function (res) {
			if (res.success && res.data.redirect) {
				if (window.bmaTrackEvent) bmaTrackEvent('service_added_to_cart', { service_id: serviceId });
				window.location.href = res.data.redirect;
			}
		});
	});

	// Quick start from services list
	$('.bma-start-quote').on('click', function () {
		var serviceId = $(this).data('service-id');
		postAjax('bma_add_to_cart', { service_id: serviceId, selected_items: [], field_values: {} })
			.done(function (res) {
				if (res.success && res.data.redirect) window.location.href = res.data.redirect;
			});
	});

	// Remove from cart
	$('.bma-remove-cart').on('click', function () {
		if (!confirm(bmaAj.strings.confirmRemove)) return;
		var key = $(this).data('cart-key');
		postAjax('bma_remove_from_cart', { cart_key: key }).done(function () {
			location.reload();
		});
	});

	// Submit quote request
	$('#bma-quote-submit-form').on('submit', function (e) {
		e.preventDefault();
		var $form = $(this);
		var $errors = $('#bma-form-errors');
		$errors.prop('hidden', true).empty();

		var data = {
			name: $form.find('[name="name"]').val(),
			email: $form.find('[name="email"]').val(),
			phone: $form.find('[name="phone"]').val(),
			company: $form.find('[name="company"]').val(),
			tax_number: $form.find('[name="tax_number"]').val(),
			billing_address: $form.find('[name="billing_address"]').val(),
			note: $form.find('[name="note"]').val(),
			privacy: $form.find('[name="privacy"]').is(':checked') ? 1 : 0,
			create_account: $form.find('[name="create_account"]').is(':checked') ? 1 : 0
		};

		postAjax('bma_submit_quote_request', data).done(function (res) {
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

	// Dynamic fields reload on item selection
	$('.bma-item-checkbox').on('change', function () {
		var serviceId = $('#bma-service-quote-form').data('service-id');
		var selected = [];
		$('.bma-item-checkbox:checked').each(function () { selected.push($(this).val()); });
		$.get(bmaAj.restUrl + 'services/' + serviceId + '/config').done(function (data) {
			// Fields already rendered server-side; future: partial reload via REST
		});
	});

})(jQuery);
