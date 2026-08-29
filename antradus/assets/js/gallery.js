/*
 * Antradus theme - the showcase gallery.
 *
 * Click a thumbnail, use the arrows, or swipe. Keyboard arrows work once the
 * gallery has focus, which is why the region is focusable.
 */

(function () {
	'use strict';

	function init(root) {
		var img = root.querySelector('.antgl-main-img');
		var title = root.querySelector('.antgl-title');
		var strip = root.querySelector('.antgl-thumbs');
		var prev = root.querySelector('.antgl-prev');
		var next = root.querySelector('.antgl-next');
		if (!img || !strip) {
			return;
		}

		var thumbs = Array.prototype.slice.call(strip.children);
		if (thumbs.length < 2) {
			return;
		}
		var index = 0;

		function show(n) {
			index = (n + thumbs.length) % thumbs.length;
			var thumb = thumbs[index];
			var small = thumb.querySelector('img');

			img.style.opacity = '0';
			window.setTimeout(function () {
				// This theme serves every picture whole, with no srcset at all
				// (inc/images.php), but a plugin can put one back - and a
				// leftover srcset outranks src, so the browser would keep
				// choosing a candidate from the previous image. Clearing both
				// leaves src as the only answer.
				img.removeAttribute('srcset');
				img.removeAttribute('sizes');
				img.alt = small ? small.alt : '';
				img.onload = function () { img.style.opacity = '1'; };
				img.src = thumb.getAttribute('data-full');
				if (img.complete) {
					img.style.opacity = '1';
				}
			}, 180);

			if (title) {
				title.textContent = thumb.getAttribute('data-title') || '';
			}
			thumbs.forEach(function (el, i) {
				el.classList.toggle('is-active', i === index);
			});
			strip.scrollTo({
				left: thumb.offsetLeft - (strip.clientWidth - thumb.offsetWidth) / 2,
				behavior: 'smooth'
			});
		}

		thumbs.forEach(function (el, i) {
			el.addEventListener('click', function () {
				if (i !== index) {
					show(i);
				}
			});
		});

		if (prev) {
			prev.addEventListener('click', function () { show(index - 1); });
		}
		if (next) {
			next.addEventListener('click', function () { show(index + 1); });
		}

		root.setAttribute('tabindex', '0');
		root.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') {
				show(index - 1);
			} else if (e.key === 'ArrowRight') {
				show(index + 1);
			}
		});

		var startX = null;
		var stage = root.querySelector('.antgl-main');
		if (stage) {
			stage.addEventListener('pointerdown', function (e) { startX = e.clientX; });
			stage.addEventListener('pointerup', function (e) {
				if (startX === null) {
					return;
				}
				var dx = e.clientX - startX;
				startX = null;
				if (Math.abs(dx) > 40) {
					show(dx < 0 ? index + 1 : index - 1);
				}
			});
		}
	}

	Array.prototype.forEach.call(document.querySelectorAll('.antgl'), init);
})();
