(function ($) {
	'use strict';

	function renderGalleryPreview(ids) {
		var $preview = $('#bma-gallery-preview');
		$preview.empty();
		if (!ids.length) return;
		ids.forEach(function (id) {
			var attachment = wp.media.attachment(id);
			attachment.fetch().done(function () {
				var url = attachment.get('sizes') && attachment.get('sizes').thumbnail
					? attachment.get('sizes').thumbnail.url
					: attachment.get('url');
				$preview.append($('<img>', { src: url, alt: '' }));
			});
		});
	}

	$(document).on('click', '#bma-gallery-add', function (e) {
		e.preventDefault();
		var frame = wp.media({
			title: 'Projektgaléria',
			button: { text: 'Kiválasztás' },
			multiple: true,
			library: { type: 'image' }
		});
		frame.on('select', function () {
			var ids = frame.state().get('selection').map(function (att) {
				return att.id;
			});
			$('#bma_gallery').val(ids.join(','));
			renderGalleryPreview(ids);
		});
		var existing = ($('#bma_gallery').val() || '').split(',').filter(Boolean).map(Number);
		if (existing.length) {
			frame.on('open', function () {
				var selection = frame.state().get('selection');
				existing.forEach(function (id) {
					selection.add(wp.media.attachment(id));
				});
			});
		}
		frame.open();
	});

	$(document).on('click', '#bma-gallery-clear', function (e) {
		e.preventDefault();
		$('#bma_gallery').val('');
		$('#bma-gallery-preview').empty();
	});
})(jQuery);
