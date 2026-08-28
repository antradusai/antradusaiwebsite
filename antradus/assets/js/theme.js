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
