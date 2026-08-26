<?php
/**
 * Antradus theme - the pricing cards and the checkout.
 *
 * Plans come from the settings, so adding a tier is filling in a form and
 * removing one is deleting a card. Nothing about a plan is hard-coded here.
 *
 * On the checkout: the button is a real link to the hosted Freemius checkout
 * page first, and only becomes an overlay when the library is genuinely ready.
 * That ordering is deliberate - it is what stops an ad blocker, a delay-JS
 * optimizer or a missing enqueue from turning the Buy button into a dead end.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * The configured plans.
 *
 * @return array
 */
function antradus_plans() {
	return antradus_rows( 'price_plans' );
}

/* ===========================================================================
 * Reading a pasted Freemius snippet
 * ========================================================================= */

/**
 * Pull the four values we need out of the code Freemius gives you.
 *
 * Freemius hands you a block of HTML and JavaScript to paste. Pasting it into
 * a settings field and having the theme run it verbatim would mean any future
 * administrator - or anyone who reached that screen - could put arbitrary
 * JavaScript on the pricing page, which is a stored cross-site scripting hole
 * with a form attached. So the snippet is never executed and never printed.
 * It is read for four values and nothing else:
 *
 *     product_id   plan_id   public_key   image
 *
 * The licence count is deliberately not one of them. Freemius fills the
 * sample block with a quantity and a logo URL that belong to nobody - see
 * antradus_plan_checkout() for where the real number comes from.
 *
 * The theme then writes its own checkout call from those, escaped. You get to
 * paste what Freemius gave you; the page runs code we wrote.
 *
 * Values are matched in both the object form (product_id: '123') and the query
 * form (?product_id=123), because Freemius shows both depending on where in
 * the dashboard you copy from.
 *
 * @param string $text Pasted snippet.
 * @return array{product:string,plan:string,key:string,image:string}
 */
function antradus_parse_freemius_snippet( $text ) {
	$out = array(
		'product' => '',
		'plan'    => '',
		'key'     => '',
		'image'   => '',
	);

	$text = (string) $text;
	if ( '' === trim( $text ) ) {
		return $out;
	}

	$map = array(
		'product' => 'product_id',
		'plan'    => 'plan_id',
		'key'     => 'public_key',
		'image'   => 'image',
	);

	foreach ( $map as $slot => $name ) {
		// product_id: '123'  /  "product_id" : "123"  /  product_id=123
		if ( preg_match( '/[\'"]?' . preg_quote( $name, '/' ) . '[\'"]?\s*[:=]\s*[\'"]?([^\'",;&\s}]+)/i', $text, $m ) ) {
			$out[ $slot ] = trim( $m[1] );
		}
	}

	// IDs are digits. Anything else came from a comment or a stray match.
	foreach ( array( 'product', 'plan' ) as $slot ) {
		if ( '' !== $out[ $slot ] && ! ctype_digit( $out[ $slot ] ) ) {
			$out[ $slot ] = '';
		}
	}
	if ( '' !== $out['key'] && ! preg_match( '/^pk_[A-Za-z0-9]+$/', $out['key'] ) ) {
		$out['key'] = '';
	}
	if ( '' !== $out['image'] ) {
		/*
		 * Freemius fills the sample block with a placeholder logo on a domain
		 * nobody owns. Passing it on puts a broken image at the top of the
		 * checkout, so it is dropped and the configured logo is used instead.
		 */
		$out['image'] = ( false !== strpos( $out['image'], 'your-plugin-site.com' ) ) ? '' : esc_url_raw( $out['image'] );
	}

	return $out;
}

/**
 * The account-wide Freemius values: the snippet if one is pasted, else the
 * three individual fields.
 *
 * @return array{product:string,key:string,image:string}
 */
function antradus_freemius_account() {
	static $account = null;
	if ( null !== $account ) {
		return $account;
	}

	$from_snippet = antradus_parse_freemius_snippet( antradus_opt( 'fs_snippet', '' ) );

	$account = array(
		'product' => '' !== $from_snippet['product'] ? $from_snippet['product'] : trim( (string) antradus_opt( 'fs_product_id', '' ) ),
		'key'     => '' !== $from_snippet['key'] ? $from_snippet['key'] : trim( (string) antradus_opt( 'fs_public_key', '' ) ),
		'image'   => '' !== $from_snippet['image'] ? $from_snippet['image'] : antradus_image_url( antradus_opt( 'fs_logo', '' ), 'medium' ),
	);
	return $account;
}

/**
 * Everything one plan's Buy button needs, or null when it is not a checkout.
 *
 * A plan's own snippet wins over its own plan ID, which wins over nothing; the
 * product and key fall back to the account. That ordering means you can paste
 * one snippet per plan and never fill in another field, or fill in one plan ID
 * and inherit the rest - both work, and mixing them works too.
 *
 * The licence count is the exception: it is read from the plan's own field
 * and never from a pasted snippet, because the block Freemius hands you
 * carries a sample "licenses" number the same way it carries a logo on
 * your-plugin-site.com. Unless a plan names a count, none is sent at all -
 * Freemius validates the price and the quantity together, so a plan priced
 * for five sites has no one-site price and refuses a request for one.
 *
 * The trial is the same kind of switch: Freemius charges today unless the
 * checkout is opened asking for one, so a plan whose button promises seven
 * free days has to say so here as well.
 *
 * @param array $plan Plan row.
 * @return array{product:string,key:string,plan:string,image:string,licenses:string,trial:string}|null
 */
function antradus_plan_checkout( $plan ) {
	if ( 'freemius' !== antradus_cell( $plan, 'cta_type' ) ) {
		return null;
	}

	$account = antradus_freemius_account();
	$snippet = antradus_parse_freemius_snippet( antradus_cell( $plan, 'fs_snippet' ) );

	$plan_id  = '' !== $snippet['plan'] ? $snippet['plan'] : trim( (string) antradus_cell( $plan, 'plan_id' ) );
	$product  = '' !== $snippet['product'] ? $snippet['product'] : $account['product'];
	$key      = '' !== $snippet['key'] ? $snippet['key'] : $account['key'];
	$image    = '' !== $snippet['image'] ? $snippet['image'] : $account['image'];
	$licenses = trim( (string) antradus_cell( $plan, 'licenses' ) );

	// Anything that is not a positive whole number is no licence count at all.
	if ( ! ctype_digit( $licenses ) || '0' === $licenses ) {
		$licenses = '';
	}

	$trial = antradus_cell( $plan, 'trial' );
	if ( ! in_array( $trial, array( 'free', 'paid' ), true ) ) {
		$trial = '';
	}

	if ( '' === $plan_id || '' === $product || '' === $key ) {
		return null; // Not enough to reach a real checkout - so do not pretend.
	}

	return array(
		'product'  => $product,
		'key'      => $key,
		'plan'     => $plan_id,
		'image'    => $image,
		'licenses' => $licenses,
		'trial'    => $trial,
	);
}

/**
 * Does any plan want the Freemius overlay?
 *
 * @return bool
 */
function antradus_needs_checkout() {
	foreach ( antradus_plans() as $plan ) {
		if ( antradus_plan_checkout( $plan ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Will this request render the pricing cards?
 *
 * @return bool
 */
function antradus_shows_plans() {
	$key = antradus_current_page_key();
	if ( 'pricing' === $key ) {
		return true;
	}
	if ( 'home' === $key && antradus_on( 'home_price_show', true ) ) {
		return true;
	}
	return false;
}

add_action( 'wp_enqueue_scripts', 'antradus_checkout_assets', 20 );
/**
 * Load the checkout library on the pages that can open it.
 *
 * It is enqueued in the head and in the footer under the same handle -
 * WordPress prints it once, but a theme or optimizer that swallows one copy
 * still leaves the other. The click handler can also load it on demand.
 */
function antradus_checkout_assets() {
	if ( ! antradus_shows_plans() || ! antradus_needs_checkout() ) {
		return;
	}
	$src = 'https://checkout.freemius.com/js/v1/';
	wp_enqueue_script( 'freemius-checkout', $src, array(), null, false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- third-party endpoint, unversioned by design.
	wp_enqueue_script( 'freemius-checkout', $src, array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- deliberate second registration; see the note above.

	$account = antradus_freemius_account();

	wp_localize_script(
		'antradus',
		'antradusCheckout',
		array(
			'product' => $account['product'],
			'key'     => $account['key'],
			'logo'    => $account['image'],
			'error'   => __( 'The checkout could not open here. Use the link to open it in a new tab instead.', 'antradus' ),
		)
	);
}

/**
 * The hosted checkout URL for a plan - the fallback the button points at.
 *
 * @param string $plan_id  Freemius plan id.
 * @param string $product  Freemius product id, or '' for the account default.
 * @param string $licenses Licence count, or '' to let the plan price itself.
 * @param string $trial    'paid', 'free', or '' for no trial.
 * @return string
 */
function antradus_checkout_url( $plan_id, $product = '', $licenses = '', $trial = '' ) {
	$account = antradus_freemius_account();
	$product = trim( (string) $product );
	$product = ( '' !== $product ) ? $product : $account['product'];
	$plan_id = trim( (string) $plan_id );
	if ( '' === $product || '' === $plan_id ) {
		return '';
	}
	$url      = 'https://checkout.freemius.com/product/' . rawurlencode( $product ) . '/plan/' . rawurlencode( $plan_id ) . '/';
	$licenses = trim( (string) $licenses );

	if ( '' !== $licenses ) {
		$url = add_query_arg( 'licenses', $licenses, $url );
	}
	if ( '' !== $trial ) {
		$url = add_query_arg( 'trial', $trial, $url );
	}

	return $url;
}

/**
 * The data-* attributes that turn an ordinary link into the overlay.
 *
 * Two links per card can carry them - the button and the trial link under it -
 * and they differ in one value, so they are built in one place.
 *
 * @param array  $checkout Checkout details from antradus_plan_checkout().
 * @param string $name     Plan name, shown at the top of the checkout.
 * @param string $trial    'paid', 'free' or '' for a straight purchase.
 * @return string Escaped attributes, ready to print.
 */
function antradus_checkout_attrs( $checkout, $name, $trial ) {
	return ' data-checkout="' . esc_attr( $checkout['plan'] ) . '"'
		. ' data-fs-product="' . esc_attr( $checkout['product'] ) . '"'
		. ' data-fs-key="' . esc_attr( $checkout['key'] ) . '"'
		. ' data-fs-image="' . esc_attr( $checkout['image'] ) . '"'
		. ' data-licenses="' . esc_attr( $checkout['licenses'] ) . '"'
		. ' data-trial="' . esc_attr( $trial ) . '"'
		. ' data-plan-name="' . esc_attr( $name ) . '"';
}

/**
 * Render the plan cards.
 *
 * @param array $args compact => bool, to drop the long feature lists.
 */
function antradus_render_plans( $args = array() ) {
	$plans = antradus_plans();
	if ( ! $plans ) {
		return;
	}
	$args    = wp_parse_args( $args, array( 'compact' => false ) );
	$columns = min( 4, max( 1, count( $plans ) ) );

	printf( '<div class="ant-plans ant-plans--%d">', (int) $columns );

	foreach ( $plans as $plan ) {
		$featured = '1' === antradus_cell( $plan, 'featured' );
		$mode     = antradus_cell( $plan, 'mode', 'amount' );
		$checkout = antradus_plan_checkout( $plan );

		/*
		 * A trial that has been given its own words takes the trial off the
		 * button: the button then buys the plan outright and the link under it
		 * starts the trial. They are two checkouts of the same plan, and which
		 * one a reader wants is not something the page should decide for them.
		 * Leaving those words empty puts the trial back on the button.
		 */
		$trial_text = antradus_cell( $plan, 'trial_text' );
		$split      = ( $checkout && '' !== $checkout['trial'] && '' !== $trial_text );

		$href  = '';
		$attrs = '';
		if ( $checkout ) {
			/*
			 * The button is a real link to the hosted checkout page before any
			 * JavaScript runs; the data-* attributes are what upgrade it to the
			 * overlay. Each plan carries its own product and key, so two plans
			 * from two different Freemius products can sit on one page.
			 */
			$buys  = $split ? '' : $checkout['trial'];
			$href  = antradus_checkout_url( $checkout['plan'], $checkout['product'], $checkout['licenses'], $buys );
			$attrs = antradus_checkout_attrs( $checkout, antradus_cell( $plan, 'name' ), $buys );
		} else {
			$href = antradus_link( antradus_cell( $plan, 'cta_url' ) );
		}

		echo '<article class="ant-plan' . ( $featured ? ' is-featured' : '' ) . '">';

		if ( $featured ) {
			echo '<span class="ant-plan-glow" aria-hidden="true"></span>';
		}
		$badge = antradus_cell( $plan, 'badge' );
		if ( '' !== $badge ) {
			echo '<span class="ant-plan-badge">' . esc_html( $badge ) . '</span>';
		}

		echo '<h3 class="ant-plan-name">' . esc_html( antradus_cell( $plan, 'name' ) ) . '</h3>';

		$sub = antradus_cell( $plan, 'sub' );
		if ( '' !== $sub ) {
			echo '<p class="ant-plan-sub">' . esc_html( $sub ) . '</p>';
		}

		echo '<div class="ant-plan-price">';
		if ( 'custom' === $mode ) {
			echo '<span class="ant-plan-words">' . esc_html( antradus_cell( $plan, 'amount', __( 'Talk to us', 'antradus' ) ) ) . '</span>';
		} else {
			$amount = antradus_cell( $plan, 'amount', '0' );
			echo '<span class="ant-plan-figure' . ( strlen( $amount ) > 3 ? ' is-long' : '' ) . '">';
			echo '<span class="ant-plan-cur">' . esc_html( antradus_cell( $plan, 'currency', '$' ) ) . '</span>';
			echo '<span class="ant-plan-amount">' . esc_html( $amount ) . '</span>';
			$cents = antradus_cell( $plan, 'cents' );
			if ( '' !== $cents ) {
				echo '<span class="ant-plan-cents">' . esc_html( $cents ) . '</span>';
			}
			$unit = antradus_cell( $plan, 'unit' );
			if ( '' !== $unit ) {
				echo '<span class="ant-plan-unit">' . esc_html( $unit ) . '</span>';
			}
			echo '</span>';
		}
		$billed = antradus_cell( $plan, 'billed' );
		if ( '' !== $billed ) {
			echo '<p class="ant-plan-billed">' . esc_html( $billed ) . '</p>';
		}
		echo '</div>';

		$chip = antradus_cell( $plan, 'chip' );
		if ( '' !== $chip ) {
			echo '<div class="ant-plan-chip">' . esc_html( $chip ) . '</div>';
		}

		$features = antradus_lines( antradus_cell( $plan, 'features' ) );
		if ( $features && ! $args['compact'] ) {
			$intro = antradus_cell( $plan, 'intro' );
			echo '<div class="ant-plan-list">';
			if ( '' !== $intro ) {
				echo '<p class="ant-plan-list-head">' . esc_html( $intro ) . '</p>';
			}
			echo '<ul>';
			foreach ( $features as $line ) {
				echo '<li>' . antradus_icon( 'check', 17 ) . '<span>' . esc_html( $line ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput -- icon markup is static.
			}
			echo '</ul></div>';
		}

		$cta = antradus_cell( $plan, 'cta' );
		if ( '' !== $cta && '' !== $href ) {
			printf(
				'<a class="ant-btn ant-btn--%1$s ant-plan-cta" href="%2$s"%3$s>%4$s</a>',
				$featured ? 'primary' : 'soft',
				esc_url( $href ),
				$attrs, // phpcs:ignore WordPress.Security.EscapeOutput -- built with esc_attr() above.
				esc_html( $cta )
			);
		} elseif ( '' !== $cta ) {
			// A button whose destination is missing says so rather than lying.
			echo '<span class="ant-plan-cta is-disabled">' . esc_html__( 'Link not set', 'antradus' ) . '</span>';
		}

		if ( $split ) {
			printf(
				'<p class="ant-plan-trial"><a class="ant-plan-trial-link" href="%1$s"%2$s>%3$s</a></p>',
				esc_url( antradus_checkout_url( $checkout['plan'], $checkout['product'], $checkout['licenses'], $checkout['trial'] ) ),
				antradus_checkout_attrs( $checkout, antradus_cell( $plan, 'name' ), $checkout['trial'] ), // phpcs:ignore WordPress.Security.EscapeOutput -- built with esc_attr() above.
				esc_html( $trial_text )
			);
		}

		$note = antradus_cell( $plan, 'note' );
		if ( '' !== $note ) {
			echo '<p class="ant-plan-note">' . esc_html( $note ) . '</p>';
		}

		echo '<p class="ant-plan-error" data-plan-error hidden></p>';
		echo '</article>';
	}

	echo '</div>';
}

/**
 * The plan comparison table.
 *
 * Two columns or three, decided by whether the third one has been given a
 * heading. That is the same rule as everywhere else in this theme - clearing a
 * heading removes what it names - and it is what lets a site that was set up
 * against the old two-plan table keep working untouched: no heading, no third
 * column, no row of empty dashes nobody asked for.
 */
function antradus_render_compare() {
	$rows = antradus_rows( 'price_cmp_rows' );
	if ( ! $rows || ! antradus_on( 'price_cmp_show', true ) ) {
		return;
	}

	// 'b' is the highlighted column: the plan most people are choosing.
	$cols = array( 'a', 'b' );
	if ( '' !== trim( (string) antradus_opt( 'price_cmp_col_c', '' ) ) ) {
		$cols[] = 'c';
	}
	$span = count( $cols ) + 1;
	?>
	<div class="ant-cmp-scroll">
		<table class="ant-cmp ant-cmp--<?php echo (int) count( $cols ); ?>">
			<thead>
				<tr>
					<th scope="col" class="ant-cmp-f"><?php esc_html_e( 'Feature', 'antradus' ); ?></th>
					<?php foreach ( $cols as $col ) : ?>
						<th scope="col" class="ant-cmp-c<?php echo 'b' === $col ? ' ant-cmp-pro' : ''; ?>">
							<?php echo esc_html( antradus_opt( 'price_cmp_col_' . $col, '' ) ); ?>
							<em><?php echo esc_html( antradus_opt( 'price_cmp_col_' . $col . '_sub', '' ) ); ?></em>
						</th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $rows as $row ) :
					$group = antradus_cell( $row, 'group' );
					if ( '' !== $group ) :
						?>
						<tr class="ant-cmp-group"><td colspan="<?php echo (int) $span; ?>"><?php echo esc_html( $group ); ?></td></tr>
						<?php
						continue;
					endif;
					$name = antradus_cell( $row, 'name' );
					if ( '' === $name ) {
						continue;
					}
					?>
					<tr>
						<td class="ant-cmp-f">
							<b><?php echo esc_html( $name ); ?></b>
							<?php if ( antradus_cell( $row, 'text' ) ) : ?>
								<span><?php echo esc_html( antradus_cell( $row, 'text' ) ); ?></span>
							<?php endif; ?>
						</td>
						<?php foreach ( $cols as $col ) : ?>
							<td class="ant-cmp-c<?php echo 'b' === $col ? ' ant-cmp-pro' : ''; ?>">
								<?php if ( 'yes' === antradus_cell( $row, $col, 'no' ) ) : ?>
									<span class="ant-cmp-yes" aria-label="<?php esc_attr_e( 'Included', 'antradus' ); ?>">
										<?php echo antradus_icon( 'check', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
									</span>
								<?php else : ?>
									<span class="ant-cmp-no" aria-label="<?php esc_attr_e( 'Not included', 'antradus' ); ?>">&mdash;</span>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * A list of questions rendered as native disclosure widgets.
 *
 * @param string $key Repeater option key.
 */
function antradus_render_faq( $key ) {
	$items = antradus_rows( $key );
	if ( ! $items ) {
		return;
	}
	echo '<div class="ant-faq">';
	foreach ( $items as $item ) {
		$q = antradus_cell( $item, 'q' );
		$a = antradus_cell( $item, 'a' );
		if ( '' === $q ) {
			continue;
		}
		echo '<details class="ant-faq-item">';
		echo '<summary><span>' . esc_html( $q ) . '</span>';
		echo '<span class="ant-faq-mark" aria-hidden="true"></span></summary>';
		echo '<div class="ant-faq-body"><p>' . esc_html( $a ) . '</p></div>';
		echo '</details>';
	}
	echo '</div>';
}
