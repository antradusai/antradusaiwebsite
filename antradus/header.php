<?php
/**
 * The page head, the announcement bar and the site header.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_nav      = antradus_nav_items();
$antradus_has_menu = has_nav_menu( 'primary' );
$antradus_logo     = antradus_image_url( antradus_opt( 'brand_logo', '' ), 'medium' );
$antradus_name     = antradus_opt( 'brand_name', get_bloginfo( 'name' ) );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="ant-skip" href="#ant-main"><?php esc_html_e( 'Skip to content', 'antradus' ); ?></a>

<?php
$antradus_ann_url = antradus_link( antradus_opt( 'announce_link', '' ) );
$antradus_ann_txt = antradus_opt( 'announce_text', '' );
if ( antradus_on( 'announce_show', true ) && '' !== trim( $antradus_ann_txt ) ) :
	?>
	<div class="ant-announce">
		<div class="ant-announce-inner">
			<span class="ant-announce-dot" aria-hidden="true"></span>
			<span class="ant-announce-text"><?php echo esc_html( $antradus_ann_txt ); ?></span>
			<?php if ( '' !== $antradus_ann_url && antradus_opt( 'announce_link_txt', '' ) ) : ?>
				<a class="ant-announce-link" href="<?php echo esc_url( $antradus_ann_url ); ?>">
					<?php echo esc_html( antradus_opt( 'announce_link_txt', '' ) ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<header class="ant-header" id="ant-header">
	<div class="ant-header-inner">

		<a class="ant-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( $antradus_logo ) : ?>
				<img class="ant-brand-logo" src="<?php echo esc_url( $antradus_logo ); ?>" alt="" width="36" height="36">
			<?php else : ?>
				<span class="ant-brand-logo ant-brand-logo--mark" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none">
						<path d="M12 3v10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M7.5 7h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<circle cx="12" cy="4.4" r="1.9" stroke="currentColor" stroke-width="1.6"/>
						<path d="M5 13a7 7 0 0014 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
					</svg>
				</span>
			<?php endif; ?>
			<span class="ant-brand-name"><?php echo esc_html( $antradus_name ); ?></span>
		</a>

		<nav class="ant-nav" id="ant-nav" aria-label="<?php esc_attr_e( 'Main', 'antradus' ); ?>">
			<?php
			if ( $antradus_has_menu ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'ant-menu',
						'depth'          => 2,
						'fallback_cb'    => false,
					)
				);
			} elseif ( $antradus_nav ) {
				echo '<ul class="ant-menu">';
				foreach ( $antradus_nav as $item ) {
					printf(
						'<li class="%1$s"><a href="%2$s"%3$s>%4$s</a></li>',
						$item['current'] ? 'is-current' : '',
						esc_url( $item['url'] ),
						$item['current'] ? ' aria-current="page"' : '',
						esc_html( $item['label'] )
					);
				}
				echo '</ul>';
			}
			?>

			<?php
			/*
			 * The language switch is inside the nav as well as beside it. On a
			 * phone the nav is the only thing that opens, so a switch that
			 * lived only in the header row would be unreachable there; on a
			 * desktop this copy is hidden and the one in the header row shows.
			 */
			?>
			<?php antradus_lang_switch( 'ant-langs--nav' ); ?>
		</nav>

		<div class="ant-header-actions">
			<?php antradus_lang_switch( 'ant-langs--bar' ); ?>
			<?php antradus_button( antradus_opt( 'nav_cta_label', '' ), antradus_opt( 'nav_cta_target', '' ), 'primary' ); ?>
			<button type="button" class="ant-burger" aria-expanded="false" aria-controls="ant-nav"
				aria-label="<?php esc_attr_e( 'Menu', 'antradus' ); ?>">
				<span></span><span></span><span></span>
			</button>
		</div>

	</div>
</header>

<main class="ant-main" id="ant-main">
