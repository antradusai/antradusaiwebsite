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
				text: 'No image yet.'
			})
		);
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
