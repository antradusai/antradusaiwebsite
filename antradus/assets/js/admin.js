/*
 * Antradus theme - the settings screen.
 *
 * Media pickers, repeater rows, and the collapse behaviour that keeps a long
 * tab readable.
 *
 * Repeater rows carry their index in every input name. Adding a row clones a
 * template where the index is the literal __i__ and swaps it for a number that
 * is not already in use. Removing a row leaves a gap in the numbering, which is
 * fine: the sanitizer runs array_values() over what arrives, so the stored list
 * is always contiguous no matter what the form posted.
 */

(function ($) {
	'use strict';

	var strings = window.antradusAdmin || {};

	/* ------------------------------------------------------------- media */

	$(document).on('click', '.antradus-image-pick', function (e) {
		e.preventDefault();

		var $wrap = $(this).closest('.antradus-image');
		var $value = $wrap.find('.antradus-image-value');
		var $preview = $wrap.find('.antradus-image-preview');

		var frame = wp.media({
			title: strings.chooseImage || 'Choose an image',
			button: { text: strings.useImage || 'Use this image' },
			library: { type: 'image' },
			multiple: false
		});

		frame.on('select', function () {
			var item = frame.state().get('selection').first().toJSON();
			var url = item.url;
			if (item.sizes && item.sizes.medium) {
				url = item.sizes.medium.url;
			}
			$value.val(item.id);
			$preview.html($('<img>', { src: url, alt: '' }));
		});

		frame.open();
	});

	$(document).on('click', '.antradus-image-clear', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.antradus-image');
		$wrap.find('.antradus-image-value').val('');
		$wrap.find('.antradus-image-preview').html(
			$('<span>', {
				'class': 'antradus-image-empty',
				text: strings.noImage || 'No image yet.'
			})
		);
	});

	/* ------------------------------------------------------ media, plural */

	/*
	 * A slider slot. The thumbnails on screen are the field - the hidden input
	 * is rewritten from them after every add, move and remove - so there is one
	 * place the order lives and it is the one the editor is looking at.
	 */

	function imagesSync($wrap) {
		var values = $wrap.find('.antradus-images-item').map(function () {
			return $(this).attr('data-value');
		}).get();

		$wrap.find('.antradus-images-value').val(values.join(','));
		$wrap.find('.antradus-images-empty').prop('hidden', values.length > 0);
		$wrap.find('.antradus-images-value').trigger('change');
	}

	function imagesItem(value, url) {
		var $tools = $('<span>', { 'class': 'antradus-images-tools' });

		$tools.append($('<button>', {
			type: 'button',
			'class': 'antradus-images-move',
			'data-dir': 'up',
			'aria-label': strings.moveEarlier || 'Move earlier',
			html: '&#8592;'
		}));
		$tools.append($('<button>', {
			type: 'button',
			'class': 'antradus-images-move',
			'data-dir': 'down',
			'aria-label': strings.moveLater || 'Move later',
			html: '&#8594;'
		}));
		$tools.append($('<button>', {
			type: 'button',
			'class': 'antradus-images-drop',
			'aria-label': strings.dropImage || 'Remove this picture',
			html: '&times;'
		}));

		return $('<li>', { 'class': 'antradus-images-item', 'data-value': String(value) })
			.append($('<img>', { src: url, alt: '' }))
			.append($tools);
	}

	$(document).on('click', '.antradus-images-pick', function (e) {
		e.preventDefault();

		var $wrap = $(this).closest('.antradus-images');
		var $list = $wrap.find('.antradus-images-list');
		var max = parseInt($wrap.find('.antradus-images-value').data('max'), 10) || 8;

		var frame = wp.media({
			title: strings.chooseImages || 'Add pictures',
			button: { text: strings.useImages || 'Add these pictures' },
			library: { type: 'image' },
			multiple: 'add'
		});

		frame.on('select', function () {
			// Whatever was picked is appended, in the order it was picked, and
			// anything already in the list is ignored rather than repeated.
			frame.state().get('selection').each(function (item) {
				var data = item.toJSON();
				var url = data.url;

				if ($list.children('[data-value="' + data.id + '"]').length) {
					return;
				}
				if ($list.children().length >= max) {
					return;
				}
				if (data.sizes && data.sizes.medium) {
					url = data.sizes.medium.url;
				}
				$list.append(imagesItem(data.id, url));
			});
			imagesSync($wrap);
		});

		frame.open();
	});

	$(document).on('click', '.antradus-images-move', function (e) {
		e.preventDefault();
		var $item = $(this).closest('.antradus-images-item');

		if ($(this).attr('data-dir') === 'up') {
			$item.prev('.antradus-images-item').before($item);
		} else {
			$item.next('.antradus-images-item').after($item);
		}
		imagesSync($item.closest('.antradus-images'));
	});

	$(document).on('click', '.antradus-images-drop', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.antradus-images');
		$(this).closest('.antradus-images-item').remove();
		imagesSync($wrap);
	});

	$(document).on('click', '.antradus-images-clear', function (e) {
		e.preventDefault();
		if (!window.confirm(strings.confirmClear || 'Remove every picture from this slot?')) {
			return;
		}
		var $wrap = $(this).closest('.antradus-images');
		$wrap.find('.antradus-images-list').empty();
		imagesSync($wrap);
	});

	/* ---------------------------------------------------------- repeaters */

	function nextIndex($rep) {
		var highest = -1;
		$rep.find('.antradus-rep-rows [name]').each(function () {
			var match = /\[(\d+)\]/.exec(this.name);
			if (match) {
				highest = Math.max(highest, parseInt(match[1], 10));
			}
		});
		return highest + 1;
	}

	$(document).on('click', '.antradus-rep-add-btn', function (e) {
		e.preventDefault();

		var $rep = $(this).closest('.antradus-rep');
		var template = $rep.children('.antradus-rep-tpl').html() || '';
		var index = nextIndex($rep);
		var $row = $(template.split('__i__').join(String(index)));

		$rep.find('.antradus-rep-rows').append($row);
		$row.addClass('is-open').find('.antradus-rep-toggle').attr('aria-expanded', 'true');
		$row.find('input[type="text"]').first().trigger('focus');
	});

	$(document).on('click', '.antradus-rep-drop', function (e) {
		e.preventDefault();
		if (!window.confirm(strings.confirmDrop || 'Remove this item?')) {
			return;
		}
		$(this).closest('.antradus-rep-row').remove();
	});

	$(document).on('click', '.antradus-rep-toggle', function (e) {
		e.preventDefault();
		var $row = $(this).closest('.antradus-rep-row');
		var open = $row.toggleClass('is-open').hasClass('is-open');
		$(this).attr('aria-expanded', open ? 'true' : 'false');
	});

	/*
	 * Moving a row means moving the DOM node. The input names keep their old
	 * indexes, which would fight the new order - so after any move, every input
	 * in the repeater is renumbered from its position on screen.
	 */
	function renumber($rep) {
		var key = $rep.data('key');
		$rep.find('.antradus-rep-rows > .antradus-rep-row').each(function (position) {
			$(this).find('[name]').each(function () {
				this.name = this.name.replace(
					new RegExp('\\[' + key + '\\]\\[\\d+\\]'),
					'[' + key + '][' + position + ']'
				);
			});
		});
	}

	$(document).on('click', '.antradus-rep-up, .antradus-rep-down', function (e) {
		e.preventDefault();
		var $row = $(this).closest('.antradus-rep-row');
		var $rep = $row.closest('.antradus-rep');

		if ($(this).hasClass('antradus-rep-up')) {
			$row.prev('.antradus-rep-row').before($row);
		} else {
			$row.next('.antradus-rep-row').after($row);
		}
		renumber($rep);
	});

	/* ------------------------------------------------------------- colour */

	$(document).on('input', 'input[type="color"]', function () {
		$(this).siblings('.antradus-color-text').val(this.value);
	});

	$(document).on('change', '.antradus-color-text', function () {
		var value = $.trim(this.value);
		if (/^#[0-9a-f]{6}$/i.test(value)) {
			$(this).siblings('input[type="color"]').val(value);
		} else {
			this.value = $(this).siblings('input[type="color"]').val();
		}
	});

	/* -------------------------------------------------- live row headings */

	$(document).on('input', '.antradus-rep-body input[type="text"]', function () {
		var $row = $(this).closest('.antradus-rep-row');
		var $first = $row.find('.antradus-rep-body input[type="text"]').first();
		if (this !== $first[0]) {
			return;
		}
		var $name = $row.find('.antradus-rep-name');
		var value = $.trim(this.value);
		$name.text(value || $row.closest('.antradus-rep').data('single') || 'Item');
	});

	/* ----------------------------------------------------- section order */

	/*
	 * Moving a section is moving its row: the hidden input travels with it, and
	 * the form posts the keys in the order they are left on screen. Only the
	 * visible numbers have to be redrawn afterwards.
	 */
	function renumberOrder($list) {
		$list.find('.antradus-order-num').each(function (i) {
			this.textContent = String(i + 1);
		});
	}

	$(document).on('click', '.antradus-order-up, .antradus-order-down', function (e) {
		e.preventDefault();
		var $row = $(this).closest('.antradus-order-row');
		var $list = $row.closest('.antradus-order');

		if ($(this).hasClass('antradus-order-up')) {
			$row.prev('.antradus-order-row').before($row);
		} else {
			$row.next('.antradus-order-row').after($row);
		}
		renumberOrder($list);
	});

	/* ------------------------------------------------- unsaved-work guard */

	var dirty = false;
	$(document).on('change input', '.antradus-form :input', function () {
		dirty = true;
	});
	$(document).on('submit', '.antradus-form', function () {
		dirty = false;
	});
	$(window).on('beforeunload', function () {
		if (dirty) {
			return 'You have unsaved changes.';
		}
	});
})(jQuery);
