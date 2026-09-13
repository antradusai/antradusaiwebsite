<?php
/**
 * A single article: a white reading sheet with a "more to read" rail.
 *
 * Also serves the plugin's documentation post type, which uses its own category
 * taxonomy for the rail.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$antradus_id   = get_the_ID();
	$antradus_type = get_post_type();
	$antradus_doc  = ( 'antradus_doc' === $antradus_type );
	$antradus_term = $antradus_doc ? null : antradus_main_category( $antradus_id );

	if ( $antradus_doc ) {
		$antradus_terms = get_the_terms( $antradus_id, 'antradus_doc_cat' );
		if ( $antradus_terms && ! is_wp_error( $antradus_terms ) ) {
			$antradus_term = $antradus_terms[0];
		}
	}

	$antradus_back = $antradus_doc ? antradus_page_url( 'docs' ) : antradus_page_url( 'blog' );
	?>

	<div class="ant-reading-shell">
		<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
		<span class="ant-mesh" aria-hidden="true"></span>

		<div class="ant-wrap">
			<?php
			/*
			 * A guide already carries the plugin's full contents list down its
			 * own left side. Putting a second list of the same links in a rail
			 * on the right made the page a corridor between two menus, so the
			 * guide gets the whole measure to itself and the rail is for
			 * articles only.
			 */
			?>
			<div class="ant-reading-grid<?php echo $antradus_doc ? ' ant-reading-grid--solo' : ''; ?>">

				<div class="ant-reading-main">
					<?php if ( '' !== $antradus_back ) : ?>
						<a class="ant-back" href="<?php echo esc_url( $antradus_back ); ?>">
							<span aria-hidden="true">&larr;</span>
							<?php echo esc_html( antradus_opt( 'single_back', __( 'All articles', 'antradus' ) ) ); ?>
						</a>
					<?php endif; ?>

					<article id="post-<?php echo (int) $antradus_id; ?>" <?php post_class( 'ant-paper' ); ?>>

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="ant-paper-hero">
								<?php the_post_thumbnail( 'full', array( 'alt' => '' ) ); ?>
							</div>
						<?php endif; ?>

						<div class="ant-paper-inner">
							<?php if ( $antradus_term ) : ?>
								<a class="ant-paper-cat" href="<?php echo esc_url( (string) get_term_link( $antradus_term ) ); ?>">
									<?php echo esc_html( $antradus_term->name ); ?>
								</a>
							<?php endif; ?>

							<h1 class="ant-paper-title"><?php the_title(); ?></h1>

							<div class="ant-paper-meta">
								<span><?php echo esc_html( get_the_date() ); ?></span>
								<span class="ant-dot" aria-hidden="true"></span>
								<span>
									<?php
									$antradus_min = antradus_reading_minutes( $antradus_id );
									printf(
										/* translators: %d: number of minutes. */
										esc_html( _n( '%d min read', '%d min read', $antradus_min, 'antradus' ) ),
										(int) $antradus_min
									);
									?>
								</span>
							</div>

							<div class="ant-prose">
								<?php
								the_content();
								wp_link_pages(
									array(
										'before' => '<nav class="ant-pagelinks">',
										'after'  => '</nav>',
									)
								);
								?>
							</div>

							<?php if ( ! $antradus_doc ) : ?>
								<?php
								$antradus_tags = get_the_tag_list( '', '', '' );
								if ( $antradus_tags ) :
									?>
									<div class="ant-paper-tags"><?php echo wp_kses_post( $antradus_tags ); ?></div>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</article>

					<?php
					if ( ! $antradus_doc && antradus_on( 'single_cta_show', true ) && antradus_opt( 'single_cta_title', '' ) ) :
						$antradus_cta_url = antradus_link( antradus_opt( 'single_cta_url', '' ) );
						if ( '' !== $antradus_cta_url ) :
							?>
							<aside class="ant-glass ant-post-cta">
								<div>
									<b><?php echo esc_html( antradus_opt( 'single_cta_title', '' ) ); ?></b>
									<span><?php echo esc_html( antradus_opt( 'single_cta_sub', '' ) ); ?></span>
								</div>
								<?php antradus_button( antradus_opt( 'single_cta_btn', '' ), antradus_opt( 'single_cta_url', '' ), 'primary' ); ?>
							</aside>
							<?php
						endif;
					endif;
					?>

					<?php
					if ( comments_open() || get_comments_number() ) {
						echo '<div class="ant-paper ant-paper--comments"><div class="ant-paper-inner">';
						comments_template();
						echo '</div></div>';
					}
					?>
				</div>

				<?php if ( ! $antradus_doc ) : ?>
				<aside class="ant-reading-rail">
					<div class="ant-glass ant-rail">
						<p class="ant-rail-eyebrow"><?php echo esc_html( antradus_opt( 'single_rail', __( 'More to read', 'antradus' ) ) ); ?></p>
						<h2 class="ant-rail-title">
							<?php
							echo $antradus_term
								? esc_html( sprintf( /* translators: %s: category name. */ __( 'In %s', 'antradus' ), $antradus_term->name ) )
								: esc_html__( 'Latest articles', 'antradus' );
							?>
						</h2>

						<?php
						$antradus_args = array(
							'post_type'           => $antradus_type,
							'posts_per_page'      => 6,
							'post__not_in'        => array( $antradus_id ),
							'ignore_sticky_posts' => true,
							'no_found_rows'       => true,
						);
						if ( $antradus_term && ! $antradus_doc ) {
							$antradus_args['cat'] = $antradus_term->term_id;
						}
						/*
						 * "More to read" must stay in the language being read -
						 * an Arabic article followed by six English suggestions
						 * is a dead end. Guides are exempt: they are published
						 * in one language only.
						 */
						if ( ! $antradus_doc ) {
							$antradus_lang_tax = antradus_blog_lang_tax_query();
							if ( $antradus_lang_tax ) {
								$antradus_args['tax_query'] = $antradus_lang_tax; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- six related links on one page.
							}
						}
						if ( $antradus_term && $antradus_doc ) {
							$antradus_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- six related links on one page.
								array(
									'taxonomy' => 'antradus_doc_cat',
									'field'    => 'term_id',
									'terms'    => $antradus_term->term_id,
								),
							);
						}
						$antradus_rel = new WP_Query( $antradus_args );

						if ( $antradus_rel->have_posts() ) :
							while ( $antradus_rel->have_posts() ) :
								$antradus_rel->the_post();
								?>
								<a class="ant-rail-item" href="<?php echo esc_url( antradus_localize_url( get_permalink() ) ); ?>">
									<span class="ant-rail-thumb">
										<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail(
												'full',
												array(
													'loading' => 'lazy',
													'alt'     => '',
												)
											);
										} else {
											echo '<span class="ant-rail-mark" aria-hidden="true"></span>';
										}
										?>
									</span>
									<span>
										<span class="ant-rail-item-title"><?php the_title(); ?></span>
										<span class="ant-rail-item-date"><?php echo esc_html( get_the_date() ); ?></span>
									</span>
								</a>
								<?php
							endwhile;
							wp_reset_postdata();
						else :
							echo '<p class="ant-empty-note">' . esc_html__( 'Nothing else here yet.', 'antradus' ) . '</p>';
						endif;
						?>
					</div>
				</aside>
				<?php endif; ?>

			</div>
		</div>
	</div>

	<?php
endwhile;

get_footer();
