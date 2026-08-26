<?php
/**
 * Antradus theme - the documentation hub.
 *
 * The plugin publishes the guides as an `antradus_doc` post type and offers a
 * shortcode that lists them. The theme used to just print that shortcode's
 * output inside a panel, which is why the page looked like a list of links
 * wearing a card: the markup was not ours, so neither was the design.
 *
 * This file renders the hub itself, from the same post type, so the page is a
 * designed thing: a search box that filters as you type, category chips, and a
 * card per guide with its summary. If the plugin is not active - or has no
 * published guides - nothing here runs and the page falls back to whatever is
 * in the editor, exactly as before.
 *
 * The search works twice over on purpose. JavaScript filters the cards that
 * are already on the page, which is instant and keeps the category counts
 * honest; and the form still submits to ?dq= for anyone without JavaScript, a
 * shared link, or a search engine following it. Same results either way.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp', 'antradus_docs_force_default_lang', 1 );
/**
 * The documentation is published in one language only.
 *
 * The guides come from the Antradus AI plugin and are written in English; the
 * hub page around them is the only part the theme could translate, and a page
 * of Arabic headings wrapped around English guides reads worse than a page that
 * is simply in English. So the Docs link stays in the Arabic menu - people
 * looking for documentation should be able to reach it - and the page it opens
 * says what it is by being English throughout, right-to-left included.
 *
 * Hooked on `wp`, which is after the query is known and before anything is
 * enqueued or printed, so the html lang and dir attributes, the stylesheet and
 * the wording all agree. The language context beats the ?lang= parameter, so
 * arriving with ?lang=ar changes nothing here.
 */
function antradus_docs_force_default_lang() {
	if ( antradus_docs_is_english_only() ) {
		antradus_set_lang_context( antradus_default_lang() );
	}
}

/**
 * Is this request one of the English-only documentation pages?
 *
 * Also asked by the language switch, so that "العربية" on a guide offers the
 * Arabic home page rather than a link that reloads the same English page.
 *
 * @return bool
 */
function antradus_docs_is_english_only() {
	if ( is_admin() ) {
		return false;
	}
	return is_singular( 'antradus_doc' )
		|| is_tax( 'antradus_doc_cat' )
		|| ( function_exists( 'antradus_current_page_key' ) && 'docs' === antradus_current_page_key() );
}

const ANTRADUS_DOC_TYPE = 'antradus_doc';
const ANTRADUS_DOC_TAX  = 'antradus_doc_cat';

/**
 * Can we render the hub ourselves?
 *
 * @return bool
 */
function antradus_docs_available() {
	static $has = null;
	if ( null !== $has ) {
		return $has;
	}
	if ( ! post_type_exists( ANTRADUS_DOC_TYPE ) ) {
		$has = false;
		return $has;
	}
	$found = get_posts(
		array(
			'post_type'      => ANTRADUS_DOC_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	$has = ! empty( $found );
	return $has;
}

/**
 * The search term the reader arrived with.
 *
 * @return string
 */
function antradus_docs_query() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public search box, not an action.
	$q = isset( $_GET['dq'] ) ? sanitize_text_field( wp_unslash( $_GET['dq'] ) ) : '';
	return trim( $q );
}

/**
 * Category slug => position, taken from the plugin's own manifest.
 *
 * The plugin already decides what order its categories go in; asking it is
 * better than inventing a second answer that can disagree. When the function
 * is not there - an older plugin, or none - an empty map means "leave the
 * taxonomy's own ordering alone".
 *
 * @return array<string,int>
 */
function antradus_docs_category_order() {
	if ( ! function_exists( 'antradus_lite_docs_pages_by_category' ) ) {
		return array();
	}
	$order  = array();
	$groups = antradus_lite_docs_pages_by_category();
	if ( ! is_array( $groups ) ) {
		return array();
	}
	foreach ( array_values( $groups ) as $position => $group ) {
		$slug = isset( $group['category']['slug'] ) ? (string) $group['category']['slug'] : '';
		if ( '' !== $slug ) {
			$order[ $slug ] = $position;
		}
	}
	return $order;
}

/**
 * Every published guide, grouped by its category, in menu order.
 *
 * Guides with no category are grouped last under a heading of their own rather
 * than being dropped, because a guide nobody filed is still a guide somebody
 * needs to find.
 *
 * @return array<int,array{term:WP_Term|null,title:string,slug:string,posts:WP_Post[]}>
 */
function antradus_docs_groups() {
	static $groups = null;
	if ( null !== $groups ) {
		return $groups;
	}

	$posts = get_posts(
		array(
			'post_type'      => ANTRADUS_DOC_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 300,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);

	$terms = get_terms(
		array(
			'taxonomy'   => ANTRADUS_DOC_TAX,
			'hide_empty' => true,
		)
	);
	$terms = ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();

	/*
	 * Sort the categories the way the plugin's manifest sorts them, not
	 * alphabetically. "Getting Started" comes first because it is where you
	 * start, and no ordering a taxonomy can work out on its own will ever
	 * produce that - it would file the guides under B for Bulk.
	 */
	$order = antradus_docs_category_order();
	if ( $order ) {
		usort(
			$terms,
			static function ( $a, $b ) use ( $order ) {
				$rank_a = isset( $order[ $a->slug ] ) ? $order[ $a->slug ] : 999;
				$rank_b = isset( $order[ $b->slug ] ) ? $order[ $b->slug ] : 999;
				if ( $rank_a === $rank_b ) {
					return strcmp( $a->name, $b->name );
				}
				return $rank_a < $rank_b ? -1 : 1;
			}
		);
	}

	$buckets = array();
	foreach ( $terms as $term ) {
		$buckets[ $term->term_id ] = array(
			'term'  => $term,
			'title' => $term->name,
			'slug'  => $term->slug,
			'posts' => array(),
		);
	}
	$loose = array(
		'term'  => null,
		'title' => __( 'More guides', 'antradus' ),
		'slug'  => 'more',
		'posts' => array(),
	);

	foreach ( $posts as $post ) {
		$assigned = wp_get_object_terms( $post->ID, ANTRADUS_DOC_TAX );
		if ( is_wp_error( $assigned ) || ! $assigned ) {
			$loose['posts'][] = $post;
			continue;
		}
		$term_id = $assigned[0]->term_id;
		if ( isset( $buckets[ $term_id ] ) ) {
			$buckets[ $term_id ]['posts'][] = $post;
		} else {
			$loose['posts'][] = $post;
		}
	}

	$groups = array();
	foreach ( $buckets as $bucket ) {
		if ( $bucket['posts'] ) {
			$groups[] = $bucket;
		}
	}
	if ( $loose['posts'] ) {
		$groups[] = $loose;
	}

	return $groups;
}

/**
 * A one-line summary for a guide.
 *
 * The plugin writes an excerpt when it syncs; when it has not, the first
 * sentences of the guide are better than an empty card.
 *
 * @param WP_Post $post Guide.
 * @return string
 */
function antradus_doc_summary( $post ) {
	$text = trim( (string) $post->post_excerpt );
	if ( '' === $text ) {
		$text = wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ) );
	}
	$text = preg_replace( '/\s+/u', ' ', $text );
	return wp_trim_words( (string) $text, 24, '&hellip;' );
}

/**
 * Render the whole hub: search, chips, cards.
 */
function antradus_render_docs_hub() {
	$groups = antradus_docs_groups();
	if ( ! $groups ) {
		return;
	}

	$query = antradus_docs_query();
	$total = 0;
	foreach ( $groups as $group ) {
		$total += count( $group['posts'] );
	}
	?>
	<div class="ant-docs" data-docs>

		<form class="ant-docs-search" method="get" action="<?php echo esc_url( antradus_page_url( 'docs' ) ); ?>" role="search">
			<?php if ( antradus_default_lang() !== antradus_lang() ) : ?>
				<input type="hidden" name="lang" value="<?php echo esc_attr( antradus_lang() ); ?>">
			<?php endif; ?>
			<label class="ant-docs-search-field">
				<span class="screen-reader-text"><?php echo esc_html( antradus_opt( 'docs_search_label', __( 'Search the guides', 'antradus' ) ) ); ?></span>
				<span class="ant-docs-search-ic" aria-hidden="true">
					<?php echo antradus_icon( 'search', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
				</span>
				<input type="search" name="dq" value="<?php echo esc_attr( $query ); ?>"
					placeholder="<?php echo esc_attr( antradus_opt( 'docs_search_hint', __( 'Type to filter every guide', 'antradus' ) ) ); ?>"
					autocomplete="off" data-docs-input>
				<button type="button" class="ant-docs-search-clear" data-docs-clear hidden
					aria-label="<?php esc_attr_e( 'Clear the search', 'antradus' ); ?>">&times;</button>
			</label>
			<button type="submit" class="ant-btn ant-btn--soft ant-docs-search-go">
				<?php esc_html_e( 'Search', 'antradus' ); ?>
			</button>
		</form>

		<div class="ant-docs-chips" data-docs-chips>
			<button type="button" class="ant-chip is-current" data-docs-filter="">
				<?php echo esc_html( antradus_opt( 'docs_all_label', __( 'All', 'antradus' ) ) ); ?>
				<span><?php echo esc_html( number_format_i18n( $total ) ); ?></span>
			</button>
			<?php foreach ( $groups as $group ) : ?>
				<button type="button" class="ant-chip" data-docs-filter="<?php echo esc_attr( $group['slug'] ); ?>">
					<?php echo esc_html( $group['title'] ); ?>
					<span><?php echo esc_html( number_format_i18n( count( $group['posts'] ) ) ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="ant-docs-groups">
			<?php
			foreach ( $groups as $group ) :
				$visible = 0;
				ob_start();
				foreach ( $group['posts'] as $post ) :
					$title   = get_the_title( $post );
					$summary = antradus_doc_summary( $post );

					// The server-side half of the search: with ?dq= set, only
					// matching guides are printed at all.
					if ( '' !== $query && false === stripos( $title . ' ' . $summary, $query ) ) {
						continue;
					}
					++$visible;
					?>
					<a class="ant-doc-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>"
						data-doc-title="<?php echo esc_attr( wp_strip_all_tags( $title . ' ' . $summary ) ); ?>">
						<span class="ant-doc-card-ic" aria-hidden="true">
							<?php echo antradus_icon( 'doc', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
						</span>
						<span class="ant-doc-card-body">
							<span class="ant-doc-card-title"><?php echo esc_html( $title ); ?></span>
							<?php if ( '' !== $summary ) : ?>
								<span class="ant-doc-card-sub"><?php echo esc_html( $summary ); ?></span>
							<?php endif; ?>
						</span>
						<span class="ant-doc-card-go" aria-hidden="true">&rarr;</span>
					</a>
					<?php
				endforeach;
				$cards = ob_get_clean();

				if ( ! $visible ) {
					continue;
				}
				?>
				<section class="ant-docs-group" data-docs-group="<?php echo esc_attr( $group['slug'] ); ?>">
					<h2 class="ant-docs-group-title">
						<?php echo esc_html( $group['title'] ); ?>
						<span><?php echo esc_html( number_format_i18n( $visible ) ); ?></span>
					</h2>
					<div class="ant-docs-cards">
						<?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput -- assembled from escaped parts above. ?>
					</div>
				</section>
				<?php
			endforeach;
			?>
		</div>

		<p class="ant-docs-empty" data-docs-empty <?php echo ( '' === $query ) ? 'hidden' : ''; ?>>
			<?php echo esc_html( antradus_opt( 'docs_empty', __( 'No guide matches that.', 'antradus' ) ) ); ?>
		</p>

	</div>
	<?php
}

/* ===========================================================================
 * Single guides
 * ========================================================================= */

add_filter( 'the_content', 'antradus_docs_drop_related', 30 );
/**
 * Take the plugin's "Related guides" block off the end of a guide.
 *
 * The guide already has the full contents list down its left side, and the
 * theme no longer prints a rail beside it, so a second list of the same links
 * under the article is noise on a page that is meant to be quiet.
 *
 * The plugin builds that block inside its own the_content filter at priority
 * 20; this runs at 30, on the markup that filter produced.
 *
 * @param string $html Post content.
 * @return string
 */
function antradus_docs_drop_related( $html ) {
	if ( ! is_singular( ANTRADUS_DOC_TYPE ) || is_admin() ) {
		return $html;
	}
	if ( false === strpos( $html, 'antradus-doc-related' ) ) {
		return $html;
	}
	return preg_replace( '~<aside class="antradus-doc-related">.*?</aside>~s', '', $html );
}
