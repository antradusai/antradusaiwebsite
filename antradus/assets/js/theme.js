/*
 * Antradus theme - front-end behaviour.
 *
 * Six small things: the mobile menu, the sticky-header shadow, smooth in-page
 * anchors, the flow lines on the compatibility diagram, the documentation
 * search, and the checkout.
 *
 * The checkout is the only part with any real logic, and the shape of it is
 * deliberate: the Buy button is already a working link to the hosted checkout
 * page before this file runs. JavaScript only upgrades it to the overlay when
 * the library is genuinely present. If an ad blocker eats the library, if a
 * delay-JS optimizer defers it, or if the script never loads at all, the click
 * follows the link and the customer still reaches a real checkout. The overlay
 * is never a single point of failure.
 */

(function () {
	'use strict';

	var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---------------------------------------------------------------- menu */

	var header = document.getElementById('ant-header');
	if (header) {
		var burger = header.querySelector('.ant-burger');
		var nav = header.querySelector('.ant-nav');

		var closeNav = function () {
			header.classList.remove('nav-open');
			if (burger) {
				burger.setAttribute('aria-expanded', 'false');
			}
		};

		if (burger && nav) {
			burger.addEventListener('click', function () {
				var open = header.classList.toggle('nav-open');
				burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			});

			nav.addEventListener('click', function (e) {
				if (e.target.closest('a')) {
					closeNav();
				}
			});

			document.addEventListener('keydown', function (e) {
				if (e.key === 'Escape') {
					closeNav();
				}
			});

			document.addEventListener('click', function (e) {
				if (header.classList.contains('nav-open') && !header.contains(e.target)) {
					closeNav();
				}
			});
		}

		/* Shadow only once the page has actually moved under the header. */
		var stuck = false;
		var onScroll = function () {
			var now = window.scrollY > 8;
			if (now !== stuck) {
				stuck = now;
				header.classList.toggle('is-stuck', stuck);
			}
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* ------------------------------------------------------------- anchors */

	document.addEventListener('click', function (e) {
		var link = e.target.closest('a[href^="#"]');
		if (!link) {
			return;
		}
		var id = link.getAttribute('href').slice(1);
		if (!id) {
			return;
		}
		var target = document.getElementById(id);
		if (!target) {
			return;
		}
		e.preventDefault();
		target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
		if (history.replaceState) {
			history.replaceState(null, '', '#' + id);
		}
	});

	/* ---------------------------------------------------------- lightbox */

	/*
	 * The picture, opened over the page at the size it was uploaded.
	 *
	 * One lightbox serves every slider on the page: it is built the first time
	 * somebody opens one and reused after that, because two of these in a
	 * document is two things that can both think they own the keyboard.
	 *
	 * It is a dialog, so it behaves like one - Escape closes it, Tab cycles
	 * inside it rather than wandering off into the page underneath, the page
	 * cannot scroll while it is up, and focus goes back to whatever was clicked
	 * when it comes down.
	 */
	var lightbox = null;
	var lightboxOpen = false;

	function buildLightbox() {
		var root = document.createElement('div');
		root.className = 'ant-lb';
		root.setAttribute('role', 'dialog');
		root.setAttribute('aria-modal', 'true');
		root.hidden = true;

		root.innerHTML =
			'<div class="ant-lb-veil" data-lb-veil></div>' +
			'<button type="button" class="ant-lb-close" data-lb-close aria-label="' + t('close', 'Close') + '">' +
				'<svg viewBox="0 0 24 24" width="22" height="22" fill="none" aria-hidden="true">' +
				'<path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>' +
			'</button>' +
			'<button type="button" class="ant-lb-nav ant-lb-prev" data-lb-prev aria-label="' + t('prev', 'Previous image') + '">' +
				'<svg viewBox="0 0 24 24" width="26" height="26" fill="none" aria-hidden="true">' +
				'<path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
			'</button>' +
			'<figure class="ant-lb-figure">' +
				'<img class="ant-lb-img" alt="" data-lb-img>' +
				'<figcaption class="ant-lb-caption" data-lb-caption></figcaption>' +
			'</figure>' +
			'<button type="button" class="ant-lb-nav ant-lb-next" data-lb-next aria-label="' + t('next', 'Next image') + '">' +
				'<svg viewBox="0 0 24 24" width="26" height="26" fill="none" aria-hidden="true">' +
				'<path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
			'</button>' +
			'<p class="ant-lb-count" data-lb-count></p>';

		document.body.appendChild(root);

		var lb = {
			root: root,
			img: root.querySelector('[data-lb-img]'),
			caption: root.querySelector('[data-lb-caption]'),
			count: root.querySelector('[data-lb-count]'),
			prev: root.querySelector('[data-lb-prev]'),
			next: root.querySelector('[data-lb-next]'),
			close: root.querySelector('[data-lb-close]'),
			items: [],
			index: 0,
			opener: null,
			onClose: null
		};

		lb.show = function (n) {
			if (!lb.items.length) {
				return;
			}
			lb.index = (n + lb.items.length) % lb.items.length;
			var item = lb.items[lb.index];

			lb.img.classList.remove('is-ready');
			lb.img.onload = function () { lb.img.classList.add('is-ready'); };
			lb.img.src = item.full;
			if (lb.img.complete) {
				lb.img.classList.add('is-ready');
			}

			lb.caption.textContent = item.caption || '';
			lb.caption.hidden = !item.caption;
			lb.count.textContent = lb.items.length > 1
				? (lb.index + 1) + ' / ' + lb.items.length
				: '';

			var many = lb.items.length > 1;
			lb.prev.hidden = !many;
			lb.next.hidden = !many;

			// The next one is the one most likely to be asked for.
			if (many) {
				var ahead = new Image();
				ahead.src = lb.items[(lb.index + 1) % lb.items.length].full;
			}
		};

		lb.hide = function () {
			if (!lightboxOpen) {
				return;
			}
			lightboxOpen = false;
			root.hidden = true;
			document.documentElement.classList.remove('ant-lb-shut');
			document.documentElement.style.removeProperty('--ant-lb-gap');
			// Let go of the file so a long gallery does not sit in memory.
			lb.img.removeAttribute('src');

			if (lb.opener && document.contains(lb.opener)) {
				lb.opener.focus();
			}
			lb.opener = null;

			if (lb.onClose) {
				lb.onClose();
				lb.onClose = null;
			}
		};

		lb.prev.addEventListener('click', function () { lb.show(lb.index - 1); });
		lb.next.addEventListener('click', function () { lb.show(lb.index + 1); });
		lb.close.addEventListener('click', lb.hide);
		root.querySelector('[data-lb-veil]').addEventListener('click', lb.hide);

		root.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				e.preventDefault();
				lb.hide();
				return;
			}
			if (e.key === 'ArrowLeft') {
				lb.show(lb.index - 1);
				return;
			}
			if (e.key === 'ArrowRight') {
				lb.show(lb.index + 1);
				return;
			}
			if (e.key !== 'Tab') {
				return;
			}
			// Keep Tab inside the dialog: it is over the page, not in it.
			var stops = [lb.close, lb.prev, lb.next].filter(function (el) {
				return !el.hidden;
			});
			var at = stops.indexOf(document.activeElement);
			var to = e.shiftKey ? at - 1 : at + 1;
			if (at === -1 || to < 0 || to >= stops.length) {
				e.preventDefault();
				stops[e.shiftKey ? stops.length - 1 : 0].focus();
			}
		});

		var startX = null;
		root.addEventListener('pointerdown', function (e) { startX = e.clientX; });
		root.addEventListener('pointerup', function (e) {
			if (startX === null) {
				return;
			}
			var dx = e.clientX - startX;
			startX = null;
			if (Math.abs(dx) < 40 || lb.items.length < 2) {
				return;
			}
			var rtl = document.documentElement.getAttribute('dir') === 'rtl';
			lb.show(lb.index + ((rtl ? dx > 0 : dx < 0) ? 1 : -1));
		});

		return lb;
	}

	/**
	 * Open the lightbox on one picture out of a list.
	 *
	 * @param {Array}   items   [{ full, caption }]
	 * @param {number}  index   Which one to show first.
	 * @param {Element} opener  What to hand focus back to.
	 * @param {Function} onClose Called once it is closed.
	 */
	function openLightbox(items, index, opener, onClose) {
		if (!items.length) {
			return;
		}
		if (!lightbox) {
			lightbox = buildLightbox();
		}

		lightbox.items = items;
		lightbox.opener = opener || null;
		lightbox.onClose = onClose || null;

		/*
		 * Hiding the page's scrollbar makes the page wider by exactly its
		 * width, which shoves the whole layout sideways behind the veil. The
		 * gap is measured and given back as padding.
		 */
		var gap = window.innerWidth - document.documentElement.clientWidth;
		if (gap > 0) {
			document.documentElement.style.setProperty('--ant-lb-gap', gap + 'px');
		}
		document.documentElement.classList.add('ant-lb-shut');

		lightbox.root.hidden = false;
		lightboxOpen = true;
		lightbox.show(index);
		lightbox.close.focus();
	}

	/**
	 * A localized string, with the English as the fallback.
	 *
	 * @param {string} key Key in antradusI18n.
	 * @param {string} fallback English.
	 * @return {string}
	 */
	function t(key, fallback) {
		var all = window.antradusI18n || {};
		return all[key] || fallback;
	}

	/* ----------------------------------------------------------- sliders */

	/*
	 * The hero picture, when the slot holds more than one.
	 *
	 * Slides cross-fade in place - PHP has already stacked them - so there is no
	 * track to translate and nothing to mirror in Arabic. All this file decides
	 * is which slide is current.
	 *
	 * It advances on its own, and stops doing so the moment it has any reason
	 * to: the pointer is on it, something inside it has focus, the tab is in the
	 * background, the slider has been scrolled past, or the reader has asked
	 * their system for less motion. A picture that keeps changing behind you is
	 * the thing people dislike about sliders, and every one of those is a case
	 * where nobody is looking.
	 */
	Array.prototype.forEach.call(document.querySelectorAll('[data-slider]'), function (root) {
		var slides = root.querySelectorAll('[data-slide]');
		var captions = root.querySelectorAll('[data-slide-caption]');
		var dots = root.querySelectorAll('[data-slider-dot]');
		var prev = root.querySelector('[data-slider-prev]');
		var next = root.querySelector('[data-slider-next]');
		if (slides.length < 2) {
			return;
		}

		var delay = parseInt(root.getAttribute('data-slider-delay'), 10);
		if (!(delay >= 2000)) {
			delay = 6000;
		}

		var index = 0;
		var timer = null;
		var held = false;
		var seen = true;

		var openers = root.querySelectorAll('[data-slide-open]');

		/*
		 * Fill in a slide's address when it is about to be needed. The slides
		 * are stacked in one box, so all of them are in the viewport and
		 * loading="lazy" would not hold a single one back - this is what keeps
		 * a hero of eight full-resolution screenshots from being eight
		 * downloads before the page has finished painting.
		 */
		function load(n) {
			var slide = slides[(n + slides.length) % slides.length];
			var img = slide ? slide.querySelector('img[data-src]') : null;
			if (img) {
				img.src = img.getAttribute('data-src');
				img.removeAttribute('data-src');
			}
		}

		function show(n) {
			index = (n + slides.length) % slides.length;

			// This one now, and the next one before it is asked for, so a fade
			// never begins on an empty frame.
			load(index);
			load(index + 1);

			Array.prototype.forEach.call(slides, function (slide, i) {
				var current = i === index;
				slide.classList.toggle('is-current', current);
				// Hide the rest from assistive technology rather than only from
				// the eye: three stacked pictures are one picture at a time.
				if (current) {
					slide.removeAttribute('aria-hidden');
				} else {
					slide.setAttribute('aria-hidden', 'true');
				}
			});

			// Only the picture on show is a control worth tabbing to.
			Array.prototype.forEach.call(openers, function (opener, i) {
				if (i === index) {
					opener.removeAttribute('tabindex');
				} else {
					opener.setAttribute('tabindex', '-1');
				}
			});

			// The captions are stacked in one cell the same way the slides are,
			// so this is the same swap, not a second thing to keep in step.
			Array.prototype.forEach.call(captions, function (caption, i) {
				var current = i === index;
				caption.classList.toggle('is-current', current);
				if (current) {
					caption.removeAttribute('aria-hidden');
				} else {
					caption.setAttribute('aria-hidden', 'true');
				}
			});

			Array.prototype.forEach.call(dots, function (dot, i) {
				dot.classList.toggle('is-current', i === index);
				if (i === index) {
					dot.setAttribute('aria-current', 'true');
				} else {
					dot.removeAttribute('aria-current');
				}
			});
		}

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		function start() {
			stop();
			if (reduce || held || !seen || document.hidden || lightboxOpen) {
				return;
			}
			timer = window.setInterval(function () {
				show(index + 1);
			}, delay);
		}

		// Any deliberate move restarts the clock, so a slide somebody just
		// chose is not replaced half a second later.
		function go(n) {
			show(n);
			start();
		}

		if (prev) {
			prev.addEventListener('click', function () { go(index - 1); });
		}
		if (next) {
			next.addEventListener('click', function () { go(index + 1); });
		}
		Array.prototype.forEach.call(dots, function (dot, i) {
			dot.addEventListener('click', function () { go(i); });
		});

		root.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') {
				go(index - 1);
			} else if (e.key === 'ArrowRight') {
				go(index + 1);
			}
		});

		['pointerenter', 'focusin'].forEach(function (name) {
			root.addEventListener(name, function () {
				held = true;
				stop();
			});
		});
		['pointerleave', 'focusout'].forEach(function (name) {
			root.addEventListener(name, function () {
				held = false;
				start();
			});
		});

		document.addEventListener('visibilitychange', start);

		/*
		 * Swipe. A drag towards the end of the line asks for the next picture,
		 * which in Arabic is a drag to the right - the direction of "forward"
		 * is the direction the page reads, not a fixed side of the screen.
		 */
		var startX = null;
		var dragged = false;
		root.addEventListener('pointerdown', function (e) {
			startX = e.clientX;
			dragged = false;
		});
		root.addEventListener('pointerup', function (e) {
			if (startX === null) {
				return;
			}
			var dx = e.clientX - startX;
			startX = null;
			if (Math.abs(dx) < 40) {
				return;
			}
			// A swipe is not a click on the picture, and the click event that
			// follows this one has to know that.
			dragged = true;
			var rtl = document.documentElement.getAttribute('dir') === 'rtl';
			var forward = rtl ? dx > 0 : dx < 0;
			go(forward ? index + 1 : index - 1);
		});
		root.addEventListener('pointercancel', function () { startX = null; });

		/*
		 * The picture opens at the size it was uploaded, in a lightbox that
		 * carries the whole slot - so somebody who wants a proper look at slide
		 * two can go on to three without closing it and clicking again. The
		 * inline slider stops advancing while that is up and picks up where it
		 * left off, on whichever slide the lightbox was closed on.
		 */
		var items = Array.prototype.map.call(openers, function (opener) {
			return {
				full: opener.getAttribute('data-full') || '',
				caption: opener.getAttribute('data-caption') || ''
			};
		});

		Array.prototype.forEach.call(openers, function (opener, i) {
			opener.addEventListener('click', function () {
				if (dragged) {
					dragged = false;
					return;
				}
				stop();
				openLightbox(items, i, opener, function () {
					if (lightbox) {
						go(lightbox.index);
					} else {
						start();
					}
				});
			});
		});

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					seen = entry.isIntersecting;
					start();
				});
			}, { threshold: 0.25 }).observe(root);
		}

		show(0);
		start();
	});

	/* --------------------------------------------------------- flow lines */

	/*
	 * The compatibility diagram: a curve from every source pill into the hub,
	 * and out of the hub to every destination pill.
	 *
	 * These are measured, not authored. How many pills there are is a setting,
	 * how tall each one is depends on its words, and everything moves at every
	 * breakpoint - so the only version that is ever correct is one drawn from
	 * getBoundingClientRect() after layout, and redrawn when layout changes.
	 *
	 * The curve is a cubic bezier whose control points are pulled horizontally
	 * towards the middle. That is what makes the line leave the pill flat,
	 * bend once, and arrive at the hub flat, instead of turning a corner.
	 */
	/*
	 * There can be more than one on a site - the home page draws the whole
	 * picture, an audience page draws its own subset - and each one measures
	 * itself, so every diagram gets its own state rather than sharing one.
	 */
	Array.prototype.forEach.call(document.querySelectorAll('[data-compat]'), function (diagram) {
		var svg = diagram.querySelector('[data-compat-flow]');
		var hub = diagram.querySelector('.ant-compat-mark');
		if (!svg || !hub) {
			return;
		}

		var NS = 'http://www.w3.org/2000/svg';
		var drawn = false;

		var path = function (from, to, rtl) {
			// Pull the control points about two-thirds of the way across the
			// gap. Less than half and the curve kinks; more and it overshoots.
			var pull = Math.max(40, Math.abs(to.x - from.x) * 0.55);
			var c1 = from.x + (rtl ? -pull : pull);
			var c2 = to.x + (rtl ? pull : -pull);
			return 'M ' + from.x + ' ' + from.y +
				' C ' + c1 + ' ' + from.y + ', ' + c2 + ' ' + to.y + ', ' + to.x + ' ' + to.y;
		};

		var draw = function () {
			var box = diagram.getBoundingClientRect();

			// The columns stack below this width and there is no left-to-right
			// to draw, so the layer stays empty rather than drawing nonsense.
			var stacked = window.getComputedStyle(diagram).getPropertyValue('--ant-compat-flow').trim() === 'off';

			while (svg.firstChild) {
				svg.removeChild(svg.firstChild);
			}
			svg.setAttribute('viewBox', '0 0 ' + Math.round(box.width) + ' ' + Math.round(box.height));
			svg.setAttribute('width', Math.round(box.width));
			svg.setAttribute('height', Math.round(box.height));

			if (stacked || box.width < 2) {
				return;
			}

			var rtl = document.documentElement.getAttribute('dir') === 'rtl';
			var hubBox = hub.getBoundingClientRect();
			var hubMid = {
				x: hubBox.left - box.left + hubBox.width / 2,
				y: hubBox.top - box.top + hubBox.height / 2
			};

			var pills = diagram.querySelectorAll('.ant-compat-item');
			var total = pills.length;
			var index = 0;
			var pulses = [];

			Array.prototype.forEach.call(pills, function (pill) {
				var side = pill.closest('.ant-compat-col--out') ? 'out' : 'in';
				var r = pill.getBoundingClientRect();
				var y = r.top - box.top + r.height / 2;

				// Leave from the edge that faces the hub. In Arabic the whole
				// diagram mirrors, so "the edge facing the hub" flips with it.
				var inward = (side === 'in') !== rtl;
				var x = inward
					? (r.right - box.left)
					: (r.left - box.left);

				// Stop just short of the hub's own edge so the line tucks
				// under the tile instead of ending in the middle of the logo.
				var stop = {
					x: hubMid.x + (inward ? -hubBox.width / 2 : hubBox.width / 2) + (inward ? 4 : -4),
					y: hubMid.y
				};

				var from = side === 'in' ? { x: x + (inward ? 2 : -2), y: y } : stop;
				var to = side === 'in' ? stop : { x: x + (inward ? 2 : -2), y: y };

				var d = path(from, to, side === 'in' ? !inward : inward);

				var el = document.createElementNS(NS, 'path');
				el.setAttribute('d', d);
				el.setAttribute('class', 'ant-compat-line ant-compat-line--' + side);
				el.setAttribute('fill', 'none');

				if (!reduce && !drawn) {
					// Grow each line in, staggered, so the eye follows the
					// direction of travel rather than seeing a finished web.
					el.style.setProperty('--ant-flow-delay', (index / Math.max(1, total) * 0.5).toFixed(2) + 's');
					el.classList.add('is-drawing');
				}

				svg.appendChild(el);

				/*
				 * A second copy of the same curve carrying one short dash that
				 * travels along it, so the diagram shows direction rather than
				 * stating it. Every path is already authored in the direction
				 * of travel - an inbound one starts at its pill and ends at the
				 * hub, an outbound one starts at the hub - so a single
				 * animation that walks the dash from the start of the path to
				 * the end sends both sides the right way, in Arabic too, with
				 * no per-side special case.
				 *
				 * pathLength="100" re-bases the dash maths on a percentage of
				 * the curve instead of its real length, so the long lines to
				 * the far pills and the short ones next to the hub carry a dash
				 * of the same visual size and take the same time to cross.
				 */
				if (!reduce) {
					var pulse = document.createElementNS(NS, 'path');
					pulse.setAttribute('d', d);
					pulse.setAttribute('class', 'ant-compat-pulse ant-compat-pulse--' + side);
					pulse.setAttribute('fill', 'none');
					pulse.setAttribute('pathLength', '100');
					// Negative, so every line is already mid-journey on the
					// first frame instead of the whole diagram starting at once.
					// Spread across the whole 4.6s cycle: any two lines that
					// started together would read as a pulse, not a flow.
					pulse.style.setProperty('--ant-pulse-delay', '-' + (index / Math.max(1, total) * 4.6).toFixed(2) + 's');
					pulses.push(pulse);
				}

				index++;
			});

			// Pulses last: on top of every line, not just the ones drawn before.
			pulses.forEach(function (pulse) {
				svg.appendChild(pulse);
			});

			drawn = true;
		};

		var schedule = (function () {
			var pending = null;
			return function () {
				if (pending) {
					cancelAnimationFrame(pending);
				}
				pending = requestAnimationFrame(function () {
					pending = null;
					draw();
				});
			};
		})();

		/*
		 * Draw when it is first looked at, so the animation is seen - and keep
		 * watching after that, because the travelling dots run forever and
		 * should not run while the diagram is scrolled away. The observer stays
		 * connected; `is-live` is what starts and stops them.
		 */
		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting && !drawn) {
						schedule();
					}
					diagram.classList.toggle('is-live', entry.isIntersecting);
				});
			}, { rootMargin: '0px 0px -15% 0px' });
			io.observe(diagram);
		} else {
			schedule();
			diagram.classList.add('is-live');
		}

		window.addEventListener('resize', schedule, { passive: true });

		// Web fonts land after first paint and change every pill's height.
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(schedule);
		}
		if ('ResizeObserver' in window) {
			new ResizeObserver(schedule).observe(diagram);
		}
	});

	/* -------------------------------------------------------- docs search */

	/*
	 * Filters the cards already on the page. The form still submits to ?dq=
	 * for anyone without JavaScript, and both halves match the same way -
	 * a case-insensitive substring of the title plus the summary - so a
	 * shared ?dq= link and a typed search show the same guides.
	 */
	(function () {
		var docs = document.querySelector('[data-docs]');
		if (!docs) {
			return;
		}
		var input = docs.querySelector('[data-docs-input]');
		var clear = docs.querySelector('[data-docs-clear]');
		var empty = docs.querySelector('[data-docs-empty]');
		var chips = docs.querySelectorAll('[data-docs-filter]');
		var groups = docs.querySelectorAll('[data-docs-group]');
		if (!input) {
			return;
		}

		var category = '';

		var apply = function () {
			var term = input.value.trim().toLowerCase();
			var shown = 0;

			Array.prototype.forEach.call(groups, function (group) {
				var inCategory = !category || group.getAttribute('data-docs-group') === category;
				var visible = 0;

				Array.prototype.forEach.call(group.querySelectorAll('.ant-doc-card'), function (card) {
					var haystack = (card.getAttribute('data-doc-title') || '').toLowerCase();
					var match = inCategory && (!term || haystack.indexOf(term) !== -1);
					card.hidden = !match;
					if (match) {
						visible++;
					}
				});

				group.hidden = visible === 0;
				var count = group.querySelector('.ant-docs-group-title span');
				if (count) {
					count.textContent = String(visible);
				}
				shown += visible;
			});

			if (empty) {
				empty.hidden = shown !== 0;
			}
			if (clear) {
				clear.hidden = input.value === '';
			}
		};

		input.addEventListener('input', apply);
		input.addEventListener('search', apply);

		if (clear) {
			clear.addEventListener('click', function () {
				input.value = '';
				input.focus();
				apply();
			});
		}

		Array.prototype.forEach.call(chips, function (chip) {
			chip.addEventListener('click', function () {
				category = chip.getAttribute('data-docs-filter') || '';
				Array.prototype.forEach.call(chips, function (other) {
					other.classList.toggle('is-current', other === chip);
				});
				apply();
			});
		});

		apply();
	})();

	/* ------------------------------------------------------------ checkout */

	var buttons = document.querySelectorAll('[data-checkout]');
	if (!buttons.length || typeof window.antradusCheckout === 'undefined') {
		return;
	}

	var cfg = window.antradusCheckout;

	/*
	 * One handler per product+key pair, not one for the whole page: a plan can
	 * carry its own Freemius product, which is how a second product - or a
	 * white-label build - can be sold from the same pricing table.
	 */
	var handlers = {};
	var requested = false;

	function buildHandler(product, key, image) {
		var id = product + '|' + key;
		if (handlers[id]) {
			return handlers[id];
		}
		if (!window.FS || !window.FS.Checkout) {
			return null;
		}
		handlers[id] = new window.FS.Checkout({
			product_id: product,
			public_key: key,
			image: image || undefined
		});
		return handlers[id];
	}

	function libraryReady() {
		return !!(window.FS && window.FS.Checkout);
	}

	/*
	 * Three chances at the library, then give up gracefully:
	 *   1. it is already here (the enqueued copy),
	 *   2. inject a tag and wait for it,
	 *   3. a tag exists but has not executed yet - poll briefly.
	 */
	function loadCheckout(done) {
		if (libraryReady()) {
			done(true);
			return;
		}
		if (!requested) {
			requested = true;
			var s = document.createElement('script');
			s.src = 'https://checkout.freemius.com/js/v1/';
			s.onload = function () { done(libraryReady()); };
			s.onerror = function () { done(false); };
			document.head.appendChild(s);
			return;
		}
		var waited = 0;
		var timer = setInterval(function () {
			waited += 200;
			if (libraryReady()) {
				clearInterval(timer);
				done(true);
			} else if (waited >= 4000) {
				clearInterval(timer);
				done(false);
			}
		}, 200);
	}

	Array.prototype.forEach.call(buttons, function (btn) {
		var planId = btn.getAttribute('data-checkout');
		var planName = btn.getAttribute('data-plan-name') || '';
		var product = btn.getAttribute('data-fs-product') || cfg.product;
		var key = btn.getAttribute('data-fs-key') || cfg.key;
		var image = btn.getAttribute('data-fs-image') || cfg.logo;
		var licenses = parseInt(btn.getAttribute('data-licenses') || '', 10);
		if (!(licenses > 0)) {
			licenses = 0;
		}
		var trial = btn.getAttribute('data-trial') || '';
		if ('free' !== trial && 'paid' !== trial) {
			trial = '';
		}
		var card = btn.closest('.ant-plan');
		var errorBox = card ? card.querySelector('[data-plan-error]') : null;

		if (!product || !key) {
			return; // Nothing configured: the plain link is the whole feature.
		}

		function showError() {
			if (!errorBox) {
				return;
			}
			errorBox.textContent = cfg.error || '';
			errorBox.hidden = false;
		}

		btn.addEventListener('click', function (e) {
			// A modified click is the reader asking for a new tab. Let it be.
			if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) {
				return;
			}
			e.preventDefault();

			var href = btn.getAttribute('href');
			btn.setAttribute('aria-busy', 'true');

			loadCheckout(function (ok) {
				btn.removeAttribute('aria-busy');

				var handler = ok ? buildHandler(product, key, image) : null;

				if (!handler) {
					// The overlay is unavailable - use the real page instead.
					if (href) {
						window.location.href = href;
					} else {
						showError();
					}
					return;
				}

				if (errorBox) {
					errorBox.hidden = true;
				}

				var opts = {
					name: planName,
					plan_id: planId
				};

				/*
				 * A licence count is sent only when the plan names one. Freemius
				 * checks the price and the quantity together, so a plan sold as
				 * "up to five sites" has no one-site price and answers a request
				 * for one with "Invalid pricing" instead of opening. Sending no
				 * count lets the plan sell at the price it actually has, which is
				 * right for every plan that has one price.
				 */
				if (licenses) {
					opts.licenses = licenses;
				}

				/*
				 * A trial happens only when the checkout is asked for one. Freemius
				 * having a trial on the plan is not enough - open it without this and
				 * the customer is charged today, whatever the button promised.
				 */
				if (trial) {
					opts.trial = trial;
				}

				handler.open(opts);
			});
		});
	});
})();
