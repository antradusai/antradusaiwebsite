<?php
/**
 * Antradus theme - the article index.
 *
 * Server-side search, category filtering and pagination, so it scales to any
 * number of posts and every state is a real, shareable URL:
 *   /blog/?q=term  ·  /blog/?topic=category-slug  ·  /blog/?pg=2
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * What the reader asked for, cleaned up.
 *
 * @return array{q:string,topic:string,page:int}
 */
function antradus_blog_request() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- a public, read-only listing.
	$q     = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	$topic = isset( $_GET['topic'] ) ? sanitize_title( wp_unslash( $_GET['topic'] ) ) : '';
	$page  = isset( $_GET['pg'] ) ? max( 1, (int) $_GET['pg'] ) : 1;
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	return array(
		'q'     => $q,
		'topic' => $topic,
		'page'  => $page,
	);
}

/**
 * The tag that marks a post as written in a language.
 *
 * The marketing pages are translated field by field; posts are not. An article
 * is written once, in one language, and carries a tag saying which. English is
 * the unmarked case - it is the default language, so tagging every English post
 * to say so would be work with no reader on the other end.
 *
 * @param string $lang Language code.
 * @return string Tag slug, or '' for the default language.
 */
function antradus_blog_lang_tag( $lang = null ) {
	$lang = ( null === $lang ) ? antradus_lang() : $lang;
	return antradus_default_lang() === $lang ? '' : $lang;
}

/**
 * Keep an article list to the language being read.
 *
 * Arabic shows **only** posts tagged `ar`. English shows everything else -
 * because the alternative is Arabic articles appearing, untranslated and
 * right-to-left, in the middle of the English index.
 *
 * Tag the post `ar` and it moves to the Arabic blog; remove the tag and it
 * moves back. There is no third state and nothing to keep in sync.
 *
 * @return array A tax_query, or array() for no constraint.
 */
function antradus_blog_lang_tax_query() {
	$tag = antradus_blog_lang_tag();

	// Every language other than the default: only its own posts.
	if ( '' !== $tag ) {
		return array(
			array(
				'taxonomy' => 'post_tag',
				'field'    => 'slug',
				'terms'    => array( $tag ),
				'operator' => 'IN',
			),
		);
	}

	// The default language: everything that is not claimed by another one.
	$others = array();
	foreach ( array_keys( antradus_languages() ) as $code ) {
		$other = antradus_blog_lang_tag( $code );
		if ( '' !== $other ) {
			$others[] = $other;
		}
	}
	if ( ! $others ) {
		return array();
	}

	return array(
		array(
			'taxonomy' => 'post_tag',
			'field'    => 'slug',
			'terms'    => $others,
			'operator' => 'NOT IN',
		),
	);
}

/**
 * The query behind the index.
 *
 * @return WP_Query
 */
function antradus_blog_query() {
	$req  = antradus_blog_request();
	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => max( 1, (int) antradus_opt( 'blog_per_page', '12' ) ),
		'paged'          => $req['page'],
	);
	if ( '' !== $req['topic'] ) {
		$args['category_name'] = $req['topic'];
	}
	if ( '' !== $req['q'] ) {
		$args['s'] = $req['q'];
	}
	$tax = antradus_blog_lang_tax_query();
	if ( $tax ) {
		$args['tax_query'] = $tax; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- the whole point of the index.
	}
	return new WP_Query( $args );
}

/**
 * The categories that actually have something to show in this language.
 *
 * Listing every category would offer the Arabic reader chips that lead to an
 * empty page, which is worse than not offering them. Two queries: the IDs of
 * the posts this language can see, then the categories those posts are in.
 *
 * @return array<int,WP_Term>
 */
function antradus_blog_categories() {
	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);
	$tax = antradus_blog_lang_tax_query();
	if ( $tax ) {
		$args['tax_query'] = $tax; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- see above.
	}

	$ids = get_posts( $args );
	if ( ! $ids ) {
		return array();
	}

	/*
	 * One row per post-and-term pair, so the number on each chip can be counted
	 * from the posts this language actually has. A term's own ->count is the
	 * whole site's: on the Arabic blog it said 29 beside a chip that opened two
	 * articles.
	 */
	$pairs = wp_get_object_terms(
		$ids,
		'category',
		array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'fields'  => 'all_with_object_id',
		)
	);
	if ( is_wp_error( $pairs ) || ! $pairs ) {
		return array();
	}

	$terms = array();
	foreach ( $pairs as $term ) {
		if ( ! isset( $terms[ $term->term_id ] ) ) {
			$copy                     = clone $term;
			$copy->count              = 0;
			$terms[ $term->term_id ]  = $copy;
		}
		++$terms[ $term->term_id ]->count;
	}

	return array_values( $terms );
}

/**
 * The base URL the filters link back to.
 *
 * @return string
 */
function antradus_blog_base_url() {
	$url = antradus_page_url( 'blog' );
	if ( '' === $url ) {
		$url = get_permalink();
	}
	if ( ! $url ) {
		$url = home_url( '/' );
	}
	return $url;
}

/**
 * One card in the article grid. Expects to be inside the loop.
 */
function antradus_post_card() {
	$post_id  = get_the_ID();
	$cats     = get_the_category( $post_id );
	$badge    = $cats ? $cats[0]->name : '';
	$excerpt  = get_the_excerpt( $post_id );
	$title    = get_the_title( $post_id );
	$initial  = function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $title, 0, 1 ) ) : strtoupper( substr( $title, 0, 1 ) );
	$minutes  = antradus_reading_minutes( $post_id );
	?>
	<a class="ant-card" href="<?php echo esc_url( antradus_localize_url( get_permalink() ) ); ?>">
		<span class="ant-card-thumb">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				the_post_thumbnail(
					'full',
					array(
						'loading' => 'lazy',
						'alt'     => '',
					)
				);
			} else {
				echo '<span class="ant-card-initial" aria-hidden="true">' . esc_html( $initial ) . '</span>';
			}
			?>
			<?php if ( $badge ) : ?>
				<span class="ant-card-cat"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
		</span>
		<span class="ant-card-body">
			<span class="ant-card-title"><?php echo esc_html( $title ); ?></span>
			<?php if ( $excerpt ) : ?>
				<span class="ant-card-excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $excerpt ), 24, '…' ) ); ?></span>
			<?php endif; ?>
			<span class="ant-card-meta">
				<span><?php echo esc_html( get_the_date() ); ?></span>
				<span class="ant-dot" aria-hidden="true"></span>
				<span>
					<?php
					printf(
						/* translators: %d: number of minutes. */
						esc_html( _n( '%d min read', '%d min read', $minutes, 'antradus' ) ),
						(int) $minutes
					);
					?>
				</span>
			</span>
		</span>
	</a>
	<?php
}

/**
 * Pagination for the index.
 *
 * @param WP_Query $query The listing query.
 */
function antradus_blog_pagination( $query ) {
	$max = (int) $query->max_num_pages;
	if ( $max < 2 ) {
		return;
	}
	$req   = antradus_blog_request();
	$extra = array();
	if ( '' !== $req['topic'] ) {
		$extra['topic'] = $req['topic'];
	}
	if ( '' !== $req['q'] ) {
		$extra['q'] = $req['q'];
	}
	/*
	 * The language rides in add_args, not in the base. paginate_links() builds
	 * its base by appending to the URL it is handed, so a base that already
	 * carried ?lang=ar produced /blog/?lang=ar/?pg=2 - a page-2 link that threw
	 * the reader back to the English index, which now holds different articles.
	 */
	if ( antradus_default_lang() !== antradus_lang() ) {
		$extra['lang'] = antradus_lang();
	}

	$links = paginate_links(
		array(
			'base'      => trailingslashit( remove_query_arg( 'lang', antradus_blog_base_url() ) ) . '%_%',
			'format'    => '?pg=%#%',
			'current'   => $req['page'],
			'total'     => $max,
			'add_args'  => $extra,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'end_size'  => 1,
			'mid_size'  => 1,
			'type'      => 'plain',
		)
	);

	if ( $links ) {
		echo '<nav class="ant-pagination" aria-label="' . esc_attr__( 'Article pages', 'antradus' ) . '">';
		echo wp_kses_post( $links );
		echo '</nav>';
	}
}

/**
 * The search box and the category chips.
 */
function antradus_blog_toolbar() {
	$req      = antradus_blog_request();
	$base     = antradus_blog_base_url();
	$search   = antradus_on( 'blog_show_search', true );
	$chips    = antradus_on( 'blog_show_cats', true );
	$cats     = $chips ? antradus_blog_categories() : array();

	if ( ! $search && ! $cats ) {
		return;
	}
	?>
	<div class="ant-toolbar">
		<?php if ( $search ) : ?>
			<form class="ant-search" method="get" action="<?php echo esc_url( $base ); ?>" role="search">
				<?php echo antradus_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
				<input type="search" name="q" value="<?php echo esc_attr( $req['q'] ); ?>"
					placeholder="<?php esc_attr_e( 'Search articles', 'antradus' ); ?>"
					aria-label="<?php esc_attr_e( 'Search articles', 'antradus' ); ?>" autocomplete="off">
				<?php if ( antradus_default_lang() !== antradus_lang() ) : ?>
					<?php /* A GET form throws away the query string on its own action, so
						 searching from an Arabic page would land back in English - and
						 now that the two languages hold different articles, it would
						 also search the wrong set. */ ?>
					<input type="hidden" name="lang" value="<?php echo esc_attr( antradus_lang() ); ?>">
				<?php endif; ?>
				<?php if ( '' !== $req['topic'] ) : ?>
					<input type="hidden" name="topic" value="<?php echo esc_attr( $req['topic'] ); ?>">
				<?php endif; ?>
			</form>
		<?php endif; ?>

		<?php if ( $cats ) : ?>
			<div class="ant-chips">
				<a class="ant-chip<?php echo '' === $req['topic'] ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( '' !== $req['q'] ? add_query_arg( 'q', $req['q'], $base ) : $base ); ?>">
					<?php esc_html_e( 'All', 'antradus' ); ?>
				</a>
				<?php
				foreach ( $cats as $cat ) :
					$args = '' !== $req['q']
						? array(
							'topic' => $cat->slug,
							'q'     => $req['q'],
						)
						: array( 'topic' => $cat->slug );
					?>
					<a class="ant-chip<?php echo $req['topic'] === $cat->slug ? ' is-active' : ''; ?>"
						href="<?php echo esc_url( add_query_arg( $args, $base ) ); ?>">
						<?php echo esc_html( $cat->name ); ?>
						<span class="ant-chip-count"><?php echo (int) $cat->count; ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * The whole article index: toolbar, count, grid, pagination.
 */
function antradus_render_blog_index() {
	$req   = antradus_blog_request();
	$query = antradus_blog_query();
	$total = (int) $query->found_posts;

	antradus_blog_toolbar();

	echo '<p class="ant-count">';
	if ( '' !== $req['q'] ) {
		printf(
			/* translators: 1: number of results, 2: search term. */
			esc_html( _n( '%1$d result for &ldquo;%2$s&rdquo;', '%1$d results for &ldquo;%2$s&rdquo;', $total, 'antradus' ) ),
			(int) $total,
			esc_html( $req['q'] )
		);
	} else {
		printf(
			/* translators: %d: number of articles. */
			esc_html( _n( '%d article', '%d articles', $total, 'antradus' ) ),
			(int) $total
		);
	}
	echo '</p>';

	if ( $query->have_posts() ) {
		echo '<div class="ant-grid ant-grid--cards">';
		while ( $query->have_posts() ) {
			$query->the_post();
			antradus_post_card();
		}
		echo '</div>';
		antradus_blog_pagination( $query );
		wp_reset_postdata();
	} else {
		echo '<div class="ant-empty">';
		echo '<p class="ant-empty-title">' . esc_html__( 'Nothing here yet', 'antradus' ) . '</p>';
		echo '<p>' . esc_html( antradus_opt( 'blog_empty', __( 'Nothing matches that yet.', 'antradus' ) ) ) . '</p>';
		printf(
			'<p><a class="ant-btn ant-btn--soft" href="%s">%s</a></p>',
			esc_url( antradus_blog_base_url() ),
			esc_html__( 'Clear filters', 'antradus' )
		);
		echo '</div>';
	}
}
