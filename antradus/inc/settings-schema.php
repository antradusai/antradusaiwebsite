<?php
/**
 * Antradus theme - the shape of the settings screen.
 *
 * One schema drives three things: what the admin screen renders, what the
 * sanitizer accepts, and what a saved option is allowed to contain. A field
 * that is not described here cannot be saved, which is the whole point.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon choices offered to every card repeater.
 *
 * @return array<string,string>
 */
function antradus_icon_choices() {
	$out = array();
	foreach ( array_keys( antradus_icon_set() ) as $name ) {
		$out[ $name ] = ucfirst( $name );
	}
	return $out;
}

/**
 * Link-target help text, repeated wherever a CTA is configured.
 *
 * @return string
 */
function antradus_link_help() {
	return __( 'A full URL, an in-page #anchor, or <code>page:pricing</code> / <code>page:publisher</code> / <code>page:studio</code> / <code>page:contact</code> / <code>page:blog</code> / <code>page:docs</code> / <code>page:features</code> / <code>page:welcome</code> / <code>page:home</code>. A <code>page:</code> link disappears from the site while that page is a draft.', 'antradus' );
}

/**
 * The trends band, as a schema section.
 *
 * Two lists, because the feature has two halves worth selling: what it finds,
 * and the check it runs before any of it reaches the queue. Both are repeaters
 * so the wording can be reordered without touching a template.
 *
 * @param string $key_prefix Option key prefix, e.g. 'pub_trends_'.
 * @param string $title      Section title in the admin screen.
 * @param string $blurb      Section blurb.
 * @param array  $icons      Icon choices.
 * @return array
 */
function antradus_trends_section( $key_prefix, $title, $blurb, $icons ) {
	return array(
		'title'  => $title,
		'blurb'  => $blurb,
		'fields' => array(
			array(
				'key'   => $key_prefix . 'eyebrow',
				'label' => __( 'Eyebrow', 'antradus' ),
				'type'  => 'text',
			),
			array(
				'key'   => $key_prefix . 'title',
				'label' => __( 'Heading', 'antradus' ),
				'type'  => 'text',
				'help'  => __( 'Clear this to remove the whole section.', 'antradus' ),
			),
			array(
				'key'   => $key_prefix . 'sub',
				'label' => __( 'Supporting paragraph', 'antradus' ),
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'    => $key_prefix . 'points',
				'label'  => __( 'What it does', 'antradus' ),
				'type'   => 'repeater',
				'single' => __( 'Point', 'antradus' ),
				'fields' => array(
					array(
						'key'     => 'icon',
						'label'   => __( 'Icon', 'antradus' ),
						'type'    => 'select',
						'options' => $icons,
					),
					array(
						'key'   => 'title',
						'label' => __( 'Point title', 'antradus' ),
						'type'  => 'text',
					),
					array(
						'key'   => 'text',
						'label' => __( 'One sentence', 'antradus' ),
						'type'  => 'textarea',
						'rows'  => 2,
					),
				),
			),
			array(
				'key'    => $key_prefix . 'steps',
				'label'  => __( 'How it goes', 'antradus' ),
				'type'   => 'repeater',
				'single' => __( 'Step', 'antradus' ),
				'fields' => array(
					array(
						'key'   => 'title',
						'label' => __( 'Step title', 'antradus' ),
						'type'  => 'text',
					),
					array(
						'key'   => 'text',
						'label' => __( 'What happens', 'antradus' ),
						'type'  => 'textarea',
						'rows'  => 3,
					),
				),
			),
			array(
				'key'   => $key_prefix . 'image',
				'label' => __( 'Illustration', 'antradus' ),
				'type'  => 'image',
			),
			array(
				'key'   => $key_prefix . 'note',
				'label' => __( 'Small note underneath', 'antradus' ),
				'type'  => 'textarea',
				'rows'  => 2,
			),
		),
	);
}

/**
 * The compatibility diagram, as a schema section.
 *
 * The home page draws one of these and each audience page may draw its own, in
 * its own vocabulary: the studio diagram lists podcast hosts, the home one
 * lists everything. Same three headings and same two columns every time, so
 * the description is written once and asked for per prefix.
 *
 * @param string $key_prefix Option key prefix: 'home_compat_' or 'std_compat_'.
 * @param string $title      Section title in the admin screen.
 * @param string $blurb      Section blurb, or ''.
 * @return array
 */
function antradus_compat_section( $key_prefix, $title, $blurb = '' ) {
	$column = static function ( $key, $label, $single ) {
		return array(
			'key'    => $key,
			'label'  => $label,
			'type'   => 'repeater',
			'single' => $single,
			'fields' => array(
				array(
					'key'   => 'name',
					'label' => __( 'Name', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => 'image',
					'label' => __( 'Icon image', 'antradus' ),
					'type'  => 'image',
				),
			),
		);
	};

	$section = array(
		'title'  => $title,
		'fields' => array(
			array(
				'key'   => $key_prefix . 'eyebrow',
				'label' => __( 'Eyebrow', 'antradus' ),
				'type'  => 'text',
			),
			array(
				'key'   => $key_prefix . 'title',
				'label' => __( 'Heading', 'antradus' ),
				'type'  => 'text',
				'help'  => __( 'Clear this to remove the whole diagram.', 'antradus' ),
			),
			array(
				'key'   => $key_prefix . 'sub',
				'label' => __( 'Supporting paragraph', 'antradus' ),
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => $key_prefix . 'center',
				'label' => __( 'Centre image', 'antradus' ),
				'type'  => 'image',
				'help'  => __( 'The mark in the middle of the diagram. Your logo is used when this is empty.', 'antradus' ),
			),
			$column( $key_prefix . 'in', __( 'Sources - the left column', 'antradus' ), __( 'Source', 'antradus' ) ),
			$column( $key_prefix . 'out', __( 'Destinations - the right column', 'antradus' ), __( 'Destination', 'antradus' ) ),
		),
	);

	if ( '' !== $blurb ) {
		$section['blurb'] = $blurb;
	}

	return $section;
}

/**
 * The "which one are you?" chooser, as a schema section.
 *
 * The same two links are offered in the home hero and under the pricing cards,
 * so the fields that describe them are written once and asked for twice. Only
 * the key prefix differs, which is why this takes one.
 *
 * @param string $prefix Option key prefix, e.g. 'home_hero_' or 'price_'.
 * @param string $title  Section heading in wp-admin.
 * @param string $blurb  Section explanation.
 * @param array  $icons  Icon choices.
 * @param string $link   Link-target help text.
 * @return array
 */
function antradus_paths_section( $prefix, $title, $blurb, $icons, $link ) {
	return array(
		'title'  => $title,
		'blurb'  => $blurb,
		'fields' => array(
			array(
				'key'   => $prefix . 'paths_label',
				'label' => __( 'Line above the links', 'antradus' ),
				'type'  => 'text',
				'help'  => __( 'Clear the links below to remove the whole block.', 'antradus' ),
			),
			array(
				'key'    => $prefix . 'paths',
				'label'  => __( 'The choices', 'antradus' ),
				'type'   => 'repeater',
				'single' => __( 'Choice', 'antradus' ),
				'fields' => array(
					array(
						'key'     => 'icon',
						'label'   => __( 'Icon', 'antradus' ),
						'type'    => 'select',
						'options' => $icons,
					),
					array(
						'key'   => 'label',
						'label' => __( 'Label', 'antradus' ),
						'type'  => 'text',
					),
					array(
						'key'   => 'text',
						'label' => __( 'One line under it', 'antradus' ),
						'type'  => 'text',
					),
					array(
						'key'   => 'cta_url',
						'label' => __( 'Where it goes', 'antradus' ),
						'type'  => 'text',
						'help'  => $link,
					),
				),
			),
		),
	);
}

/**
 * One of the two audience pages, as a schema tab.
 *
 * "For publishers" and "For studios" are the same page told twice: same hero,
 * same panel of signals, same feature groups, same numbered flow, same plan
 * card, same way back out. Only the words differ, and the words live in the
 * settings - so the two tabs are generated from one description rather than
 * kept in step by hand.
 *
 * @param string $prefix Option key prefix: 'pub_' or 'std_'.
 * @param array  $icons  Icon choices.
 * @param string $link   Link-target help text.
 * @return array
 */
function antradus_audience_sections( $prefix, $icons, $link ) {
	return array(
		array(
			'title'  => __( '1. Hero', 'antradus' ),
			'fields' => array(
				array(
					'key'   => $prefix . 'eyebrow',
					'label' => __( 'Eyebrow', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => $prefix . 'title',
					'label' => __( 'Heading', 'antradus' ),
					'type'  => 'text',
					'help'  => __( 'Wrap a phrase in *asterisks* for the serif italic accent.', 'antradus' ),
				),
				array(
					'key'   => $prefix . 'sub',
					'label' => __( 'Supporting paragraph', 'antradus' ),
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'   => $prefix . 'cta1',
					'label' => __( 'Primary button text', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => $prefix . 'cta1_url',
					'label' => __( 'Primary button link', 'antradus' ),
					'type'  => 'text',
					'help'  => $link . ' ' . __( 'Use <code>#plan</code> to jump to the plan section further down this page.', 'antradus' ),
				),
				array(
					'key'   => $prefix . 'cta2',
					'label' => __( 'Secondary button text', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => $prefix . 'cta2_url',
					'label' => __( 'Secondary button link', 'antradus' ),
					'type'  => 'text',
					'help'  => $link,
				),
				array(
					'key'   => $prefix . 'note',
					'label' => __( 'Small note under the buttons', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => $prefix . 'hero_image',
					'label' => __( 'Hero image', 'antradus' ),
					'type'  => 'image',
				),
			),
		),
		array(
			'title'  => __( '2. Is this you?', 'antradus' ),
			'blurb'  => __( 'The short panel that lets a reader recognise themselves before they read a feature list. Clear the list to remove the panel.', 'antradus' ),
			'fields' => array(
				array(
					'key'   => $prefix . 'signals_title',
					'label' => __( 'Panel heading', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => $prefix . 'signals',
					'label' => __( 'The signs', 'antradus' ),
					'type'  => 'textarea',
					'rows'  => 6,
					'help'  => __( 'One per line.', 'antradus' ),
				),
			),
		),
		array(
			'title'  => __( '3. What it does for them', 'antradus' ),
			'blurb'  => __( 'The features that matter to this audience, grouped. This is the half of the plugin they came to read about - the other audience gets the other half on their own page.', 'antradus' ),
			'fields' => array(
				array(
					'key'   => $prefix . 'groups_eyebrow',
					'label' => __( 'Eyebrow', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => $prefix . 'groups_title',
					'label' => __( 'Heading', 'antradus' ),
					'type'  => 'text',
					'help'  => __( 'Clear this to remove the whole section.', 'antradus' ),
				),
				array(
					'key'   => $prefix . 'groups_sub',
					'label' => __( 'Supporting line', 'antradus' ),
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'    => $prefix . 'groups',
					'label'  => __( 'Feature groups', 'antradus' ),
					'type'   => 'repeater',
					'single' => __( 'Group', 'antradus' ),
					'fields' => array(
						array(
							'key'     => 'icon',
							'label'   => __( 'Icon', 'antradus' ),
							'type'    => 'select',
							'options' => $icons,
						),
						array(
							'key'   => 'title',
							'label' => __( 'Group title', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'items',
							'label' => __( 'Features', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 7,
							'help'  => __( 'One per line.', 'antradus' ),
						),
						array(
							'key'   => 'image',
							'label' => __( 'Picture', 'antradus' ),
							'type'  => 'image',
							'help'  => __( 'Optional, and shown across the top of this card. Shared by both languages, because a screenshot is not translated.', 'antradus' ),
						),
					),
				),
			),
		),
		antradus_trends_section(
			$prefix . 'trends_',
			__( '4. Writing from what is trending', 'antradus' ),
			__( 'The Google Trends band. Leave the heading empty on a page this does not belong on - it is written for the audience that publishes against a news cycle. The selling point is the verification, not the trend list: say plainly that a trend is checked against a real article published today before it can be queued.', 'antradus' ),
			$icons
		),
		array(
			'title'  => __( '5. How it actually goes', 'antradus' ),
			'blurb'  => __( 'The numbered steps. They are numbered by position, so adding or reordering a step renumbers the rest on its own.', 'antradus' ),
			'fields' => array(
				array(
					'key'   => $prefix . 'flow_title',
					'label' => __( 'Heading', 'antradus' ),
					'type'  => 'text',
					'help'  => __( 'Clear this to remove the whole section.', 'antradus' ),
				),
				array(
					'key'   => $prefix . 'flow_sub',
					'label' => __( 'Supporting line', 'antradus' ),
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'    => $prefix . 'flow',
					'label'  => __( 'Steps', 'antradus' ),
					'type'   => 'repeater',
					'single' => __( 'Step', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'title',
							'label' => __( 'Step title', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'text',
							'label' => __( 'What happens', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
			),
		),
		antradus_compat_section(
			$prefix . 'compat_',
			__( '6. What it plugs into', 'antradus' ),
			__( 'The same in-and-out diagram the home page draws, in this audience\'s own vocabulary - so this page can list the sources and destinations they actually use and leave out the rest. Clear the heading to leave the diagram off this page entirely.', 'antradus' )
		),
		array(
			'title'  => __( '7. The plan this maps to', 'antradus' ),
			'blurb'  => __( 'This section does not hold a price. Name a plan from the <strong>Pricing</strong> tab and that plan\'s real card is rendered here - the same price, the same button, the same free trial. There is one place a price is written on this site, and it is not here.', 'antradus' ),
			'fields' => array(
				array(
					'key'   => $prefix . 'plan_eyebrow',
					'label' => __( 'Eyebrow', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => $prefix . 'plan_title',
					'label' => __( 'Heading', 'antradus' ),
					'type'  => 'text',
					'help'  => __( 'Clear this to remove the whole section.', 'antradus' ),
				),
				array(
					'key'   => $prefix . 'plan_sub',
					'label' => __( 'Supporting line', 'antradus' ),
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => $prefix . 'plan_names',
					'label' => __( 'Which plans to show', 'antradus' ),
					'type'  => 'text',
					'i18n'  => false,
					'help'  => __( 'Plan names from the Pricing tab, separated by commas - for example <code>Publisher</code> or <code>Studio, Managed</code>. Written once for both languages: a plan is matched by its English name as well as its translated one, so the Arabic page finds the same card. A name that matches no plan is simply left out.', 'antradus' ),
				),
				array(
					'key'   => $prefix . 'plan_note',
					'label' => __( 'Note under the card', 'antradus' ),
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => $prefix . 'plan_more',
					'label' => __( 'Link to the full pricing page', 'antradus' ),
					'type'  => 'text',
					'help'  => __( 'The words only - it always points at your Pricing page, and disappears while that page is a draft.', 'antradus' ),
				),
			),
		),
		array(
			'title'  => __( '8. The way back out', 'antradus' ),
			'blurb'  => __( 'A page that asks somebody to identify themselves has to let the ones who guessed wrong leave without the back button. This is the card at the bottom that points at the other audience page.', 'antradus' ),
			'fields' => array(
				array(
					'key'   => $prefix . 'switch_title',
					'label' => __( 'Heading', 'antradus' ),
					'type'  => 'text',
					'help'  => __( 'Clear this to remove the card.', 'antradus' ),
				),
				array(
					'key'   => $prefix . 'switch_text',
					'label' => __( 'Supporting line', 'antradus' ),
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => $prefix . 'switch_btn',
					'label' => __( 'Button text', 'antradus' ),
					'type'  => 'text',
				),
				array(
					'key'   => $prefix . 'switch_btn_url',
					'label' => __( 'Button link', 'antradus' ),
					'type'  => 'text',
					'help'  => $link,
				),
			),
		),
	);
}

/**
 * The full settings schema.
 *
 * @return array
 */
function antradus_settings_schema() {
	$icons = antradus_icon_choices();
	$link  = antradus_link_help();

	return array(

		/* =================================================================
		 * Pages
		 * ============================================================== */
		'pages'    => array(
			'label'    => __( 'Pages', 'antradus' ),
			'blurb'    => __( 'The nine pages this theme designs. A page only appears in the menu, the footer and any button pointing at it once it is <strong>published</strong> - leave it as a draft and the whole site simply stops linking to it.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'Page status', 'antradus' ),
					'render' => 'antradus_render_pages_panel',
					'fields' => array(),
				),
				array(
					'title'  => __( 'Slugs', 'antradus' ),
					'blurb'  => __( 'Only change these if your pages already live at different addresses.', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'slug_home',
							'label' => __( 'Home', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'slug_publisher',
							'label' => __( 'For publishers', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'slug_studio',
							'label' => __( 'For studios', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'slug_features',
							'label' => __( 'Plugin features', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'slug_pricing',
							'label' => __( 'Pricing', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'slug_docs',
							'label' => __( 'Docs', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'slug_blog',
							'label' => __( 'Blog', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'slug_contact',
							'label' => __( 'Contact', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'slug_welcome',
							'label' => __( 'Welcome', 'antradus' ),
							'type'  => 'text',
						),
					),
				),
			),
		),

		/* =================================================================
		 * Brand
		 * ============================================================== */
		'brand'    => array(
			'label'    => __( 'Brand &amp; header', 'antradus' ),
			'blurb'    => __( 'The logo, the accent colour, the header button and the notice bar that runs across the top of every page.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'Identity', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'brand_name',
							'label' => __( 'Site name', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'brand_logo',
							'label' => __( 'Logo', 'antradus' ),
							'type'  => 'image',
							'help'  => __( 'A square mark works best. Leave empty to use the site title on its own.', 'antradus' ),
						),
						array(
							'key'     => 'brand_accent',
							'label'   => __( 'Accent colour', 'antradus' ),
							'type'    => 'color',
							'help'    => __( 'The blue everything else is derived from.', 'antradus' ),
						),
						array(
							'key'     => 'brand_width',
							'label'   => __( 'Content width', 'antradus' ),
							'type'    => 'select',
							'options' => array(
								'1320' => __( '1320px - snug', 'antradus' ),
								'1440' => __( '1440px', 'antradus' ),
								'1560' => __( '1560px - wide (default)', 'antradus' ),
								'1720' => __( '1720px - very wide', 'antradus' ),
								'1880' => __( '1880px - widest', 'antradus' ),
								'full' => __( 'Edge to edge - the whole screen, less a margin', 'antradus' ),
							),
							'help'    => __( 'Section backgrounds always run the full width of the screen. This only sets how wide the content inside them is allowed to get. Articles keep their own reading measure whatever you choose here, because a line of text 1800px long is not readable.', 'antradus' ),
						),
					),
				),
				array(
					'title'  => __( 'Header button', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'nav_cta_label',
							'label' => __( 'Button text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'nav_cta_target',
							'label' => __( 'Button link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
						array(
							'key'   => 'nav_extra',
							'label' => __( 'Extra menu links', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 4,
							'help'  => __( 'Optional. One per line, as <code>Label | target</code>. These are added after the seven pages. To replace the menu entirely instead, assign a menu to the Primary location under Appearance &rarr; Menus.', 'antradus' ),
						),
					),
				),
				array(
					'title'  => __( 'Announcement bar', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'announce_show',
							'label' => __( 'Show the bar', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Display a notice strip above the header', 'antradus' ),
						),
						array(
							'key'   => 'announce_text',
							'label' => __( 'Message', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'announce_link_txt',
							'label' => __( 'Link text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'announce_link',
							'label' => __( 'Link target', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
					),
				),
			),
		),

		/* =================================================================
		 * Home
		 * ============================================================== */
		'home'     => array(
			'label'    => __( 'Home', 'antradus' ),
			'blurb'    => __( 'The front page, section by section, in the order they appear. Mark one phrase in a heading with *asterisks* to set it in the italic serif accent.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( '1. Hero', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_hero_badge',
							'label' => __( 'Badge above the heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_hero_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
							'help'  => __( 'Line breaks are kept. Wrap a phrase in *asterisks* for the serif italic.', 'antradus' ),
						),
						array(
							'key'   => 'home_hero_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 4,
						),
						array(
							'key'   => 'home_hero_cta1',
							'label' => __( 'Primary button text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_hero_cta1_url',
							'label' => __( 'Primary button link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
						array(
							'key'   => 'home_hero_cta2',
							'label' => __( 'Secondary button text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_hero_cta2_url',
							'label' => __( 'Secondary button link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
						array(
							'key'   => 'home_hero_note',
							'label' => __( 'Small note under the buttons', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_hero_image',
							'label' => __( 'Hero image', 'antradus' ),
							'type'  => 'image',
							'help'  => __( 'A screenshot of the plugin at work reads best here. Around 1200 x 900.', 'antradus' ),
						),
						array(
							'key'   => 'home_hero_chips',
							'label' => __( 'Floating labels on the image', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 5,
							'help'  => __( 'One per line. Up to five are shown.', 'antradus' ),
						),
						array(
							'key'   => 'home_hero_proof',
							'label' => __( 'Reassurance line', 'antradus' ),
							'type'  => 'text',
						),
					),
				),
				antradus_paths_section(
					'home_hero_',
					__( '1b. Which one are you?', 'antradus' ),
					__( 'Two links in the hero, one per audience. This is what tells somebody in the first screenful that the site is for both a website and a show - and sends them to the page written for whichever they are.', 'antradus' ),
					$icons,
					$link
				),
				array(
					'title'  => __( '2. Numbers', 'antradus' ),
					'fields' => array(
						array(
							'key'    => 'home_stats',
							'label'  => __( 'Statistics', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Statistic', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'value',
									'label' => __( 'Number', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'label',
									'label' => __( 'Caption', 'antradus' ),
									'type'  => 'text',
								),
							),
						),
					),
				),
				array(
					'title'  => __( '3. Publisher or studio', 'antradus' ),
					'blurb'  => __( 'The fork, in full: one card per audience, with what that audience gets, what it costs and a button to the page written for them. It is a repeater, so a third audience would be a card rather than a code change.', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_fork_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_fork_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'Clear this to remove the whole section.', 'antradus' ),
						),
						array(
							'key'   => 'home_fork_sub',
							'label' => __( 'Supporting line', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'    => 'home_fork_cards',
							'label'  => __( 'The audiences', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Audience', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'name',
									'label' => __( 'Plan chip', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'The plan this audience ends up on, e.g. Publisher.', 'antradus' ),
								),
								array(
									'key'   => 'title',
									'label' => __( 'Card heading', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'One or two sentences', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 3,
								),
								array(
									'key'   => 'items',
									'label' => __( 'What they get', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 5,
									'help'  => __( 'One per line. Four reads best.', 'antradus' ),
								),
								array(
									'key'   => 'price',
									'label' => __( 'Price line', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'cta',
									'label' => __( 'Button text', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'cta_url',
									'label' => __( 'Button link', 'antradus' ),
									'type'  => 'text',
									'help'  => $link,
								),
								array(
									'key'   => 'alt',
									'label' => __( 'Quiet link beside it', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'alt_url',
									'label' => __( 'Where the quiet link goes', 'antradus' ),
									'type'  => 'text',
									'help'  => $link,
								),
							),
						),
						array(
							'key'   => 'home_fork_note',
							'label' => __( 'Note under the cards', 'antradus' ),
							'type'  => 'text',
						),
					),
				),
				array(
					'title'  => __( '4. Logo strip', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_logos_title',
							'label' => __( 'Strip heading', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'Clear this to hide the whole strip.', 'antradus' ),
						),
						array(
							'key'    => 'home_logos',
							'label'  => __( 'Logos', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Logo', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'name',
									'label' => __( 'Name', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'image',
									'label' => __( 'Logo image', 'antradus' ),
									'type'  => 'image',
									'help'  => __( 'Leave empty and the name is shown as a placeholder tile instead.', 'antradus' ),
								),
							),
						),
					),
				),
				array(
					'title'  => __( '5. Product demo', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_demo_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_demo_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_demo_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'home_demo_image',
							'label' => __( 'Demo screenshot', 'antradus' ),
							'type'  => 'image',
							'help'  => __( 'The wide screenshot inside the glass panel. Around 1400 x 900.', 'antradus' ),
						),
						array(
							'key'   => 'home_demo_caption',
							'label' => __( 'Caption under the screenshot', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'    => 'home_demo_cards',
							'label'  => __( 'What comes out', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Output', 'antradus' ),
							'fields' => array(
								array(
									'key'     => 'icon',
									'label'   => __( 'Icon', 'antradus' ),
									'type'    => 'select',
									'options' => $icons,
								),
								array(
									'key'   => 'title',
									'label' => __( 'Title', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'Description', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 2,
								),
							),
						),
					),
				),
				array(
					'title'  => __( '6. Core features', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_feat_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_feat_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_feat_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'    => 'home_feat_cards',
							'label'  => __( 'Feature cards', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Feature', 'antradus' ),
							'fields' => array(
								array(
									'key'     => 'icon',
									'label'   => __( 'Icon', 'antradus' ),
									'type'    => 'select',
									'options' => $icons,
								),
								array(
									'key'   => 'title',
									'label' => __( 'Title', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'Description', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 3,
								),
								array(
									'key'   => 'image',
									'label' => __( 'Illustration', 'antradus' ),
									'type'  => 'image',
									'help'  => __( 'Optional. A placeholder is shown when this is empty.', 'antradus' ),
								),
							),
						),
					),
				),
				array(
					'title'  => __( '7. Where it runs', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_run_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_run_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_run_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'    => 'home_run_cards',
							'label'  => __( 'Columns', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Column', 'antradus' ),
							'fields' => array(
								array(
									'key'     => 'icon',
									'label'   => __( 'Icon', 'antradus' ),
									'type'    => 'select',
									'options' => $icons,
								),
								array(
									'key'   => 'title',
									'label' => __( 'Title', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'Bullet points', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 4,
									'help'  => __( 'One per line.', 'antradus' ),
								),
							),
						),
					),
				),
				antradus_compat_section( 'home_compat_', __( '8. Compatibility', 'antradus' ) ),
				array(
					'title'  => __( '9. GEO / SEO / AIO', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_geo_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_geo_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_geo_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'home_geo_image',
							'label' => __( 'Illustration', 'antradus' ),
							'type'  => 'image',
						),
						array(
							'key'    => 'home_geo_cards',
							'label'  => __( 'Points', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Point', 'antradus' ),
							'fields' => array(
								array(
									'key'     => 'icon',
									'label'   => __( 'Icon', 'antradus' ),
									'type'    => 'select',
									'options' => $icons,
								),
								array(
									'key'   => 'title',
									'label' => __( 'Title', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'Description', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 2,
								),
							),
						),
					),
				),
				array(
					'title'  => __( '10. Use cases', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_use_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_use_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_use_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'    => 'home_use_cards',
							'label'  => __( 'Audiences', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Audience', 'antradus' ),
							'fields' => array(
								array(
									'key'     => 'icon',
									'label'   => __( 'Icon', 'antradus' ),
									'type'    => 'select',
									'options' => $icons,
								),
								array(
									'key'   => 'title',
									'label' => __( 'Title', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'Description', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 2,
								),
							),
						),
					),
				),
				array(
					'title'  => __( '11. The economics', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_cost_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_cost_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'Clear this to hide the section.', 'antradus' ),
						),
						array(
							'key'   => 'home_cost_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'    => 'home_cost_rows',
							'label'  => __( 'Comparison rows', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Row', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'label',
									'label' => __( 'Line item', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'them',
									'label' => __( 'The usual way', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'us',
									'label' => __( 'With Antradus', 'antradus' ),
									'type'  => 'text',
								),
							),
						),
						array(
							'key'   => 'home_cost_note',
							'label' => __( 'Footnote', 'antradus' ),
							'type'  => 'text',
						),
					),
				),
				array(
					'title'  => __( '12. Pricing preview', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_price_show',
							'label' => __( 'Show the plans', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Show the pricing cards on the home page', 'antradus' ),
							'help'  => __( 'The cards themselves are edited on the Pricing tab - they are the same plans.', 'antradus' ),
						),
						array(
							'key'   => 'home_price_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_price_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_price_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
				array(
					'title'  => __( '13. Questions', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_faq_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_faq_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_faq_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'    => 'home_faq_items',
							'label'  => __( 'Questions', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Question', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'q',
									'label' => __( 'Question', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'a',
									'label' => __( 'Answer', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 4,
								),
							),
						),
					),
				),
				array(
					'title'  => __( '14. Closing call to action', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'home_cta_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_cta_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'home_cta_btn1',
							'label' => __( 'Primary button text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_cta_btn1_url',
							'label' => __( 'Primary button link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
						array(
							'key'   => 'home_cta_btn2',
							'label' => __( 'Secondary button text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'home_cta_btn2_url',
							'label' => __( 'Secondary button link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
						array(
							'key'   => 'home_cta_note',
							'label' => __( 'Trust line', 'antradus' ),
							'type'  => 'text',
						),
					),
				),
			),
		),

		/* =================================================================
		 * The two audience pages
		 *
		 * Both tabs are built from one description, because the pages are one
		 * design with two vocabularies. Everything that differs between them
		 * is a word, and every word is here.
		 * ============================================================== */
		'publisher' => array(
			'label'    => __( 'Publishers', 'antradus' ),
			'blurb'    => __( 'The page for somebody who runs a <strong>website</strong>: keywords, clusters, bulk queues and internal links. It ends on the plan that covers all of it, which is the card from the Pricing tab rather than a second copy of the price.', 'antradus' ),
			'sections' => antradus_audience_sections( 'pub_', $icons, $link ),
		),

		'studio'    => array(
			'label'    => __( 'Studios', 'antradus' ),
			'blurb'    => __( 'The page for somebody who runs a <strong>show</strong>: an episode becomes an article, show notes, chapters, quote cards and a newsletter. Same shape as the Publishers tab, different half of the plugin.', 'antradus' ),
			'sections' => antradus_audience_sections( 'std_', $icons, $link ),
		),

		/* =================================================================
		 * Features page
		 * ============================================================== */
		'features' => array(
			'label'    => __( 'Features page', 'antradus' ),
			'blurb'    => __( 'The Plugin features page. Whatever you put in the page itself in the editor - screenshots, a gallery - is rendered inside the design, between the hero and the feature groups.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'Hero', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'feat_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'feat_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'feat_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'feat_hero_image',
							'label' => __( 'Hero image', 'antradus' ),
							'type'  => 'image',
						),
					),
				),
				array(
					'title'  => __( 'Your page content', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'feat_content_top',
							'label' => __( 'Heading above your content', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'Clear this to drop the heading and show your content on its own.', 'antradus' ),
						),
						array(
							'key'   => 'feat_content_sub',
							'label' => __( 'Supporting line', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 2,
						),
					),
				),
				array(
					'title'  => __( 'Feature groups', 'antradus' ),
					'fields' => array(
						array(
							'key'    => 'feat_groups',
							'label'  => __( 'Groups', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Group', 'antradus' ),
							'fields' => array(
								array(
									'key'     => 'icon',
									'label'   => __( 'Icon', 'antradus' ),
									'type'    => 'select',
									'options' => $icons,
								),
								array(
									'key'   => 'title',
									'label' => __( 'Group title', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'items',
									'label' => __( 'Features', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 7,
									'help'  => __( 'One per line.', 'antradus' ),
								),
							),
						),
					),
				),
				array(
					'title'  => __( 'Closing call to action', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'feat_cta_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'feat_cta_sub',
							'label' => __( 'Supporting line', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'   => 'feat_cta_btn',
							'label' => __( 'Button text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'feat_cta_btn_url',
							'label' => __( 'Button link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
					),
				),
			),
		),

		/* =================================================================
		 * Pricing
		 * ============================================================== */
		'pricing'  => array(
			'label'    => __( 'Pricing', 'antradus' ),
			'blurb'    => __( 'Add, remove or reorder plans here and both the Pricing page and the home page follow. A plan set to <strong>Freemius checkout</strong> opens the hosted overlay; every other plan is an ordinary link.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'Hero', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'price_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'price_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'price_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'price_note',
							'label' => __( 'Note above the cards', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 2,
						),
					),
				),
				antradus_paths_section(
					'price_',
					__( 'Which one are you?', 'antradus' ),
					__( 'The same two links the home hero offers, printed under the cards. Somebody looking at four prices is usually asking which of them is meant for them, and that question is answered in features rather than in money - so this points at the two pages that answer it.', 'antradus' ),
					$icons,
					$link
				),
				array(
					'title'  => __( 'The plans', 'antradus' ),
					'blurb'  => __( 'Drag is not needed - the order here is the order on the page. Delete a card to remove the plan; add one to introduce a new tier.', 'antradus' ),
					'fields' => array(
						array(
							'key'    => 'price_plans',
							'label'  => __( 'Plans', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Plan', 'antradus' ),
							'wide'   => true,
							'fields' => array(
								array(
									'key'   => 'name',
									'label' => __( 'Plan name', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'sub',
									'label' => __( 'One-line description', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'badge',
									'label' => __( 'Badge', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'Optional, e.g. "Most popular".', 'antradus' ),
								),
								array(
									'key'   => 'featured',
									'label' => __( 'Highlight this plan', 'antradus' ),
									'type'  => 'checkbox',
									'cbtxt' => __( 'Draw it larger, with the accent border', 'antradus' ),
								),
								array(
									'key'     => 'mode',
									'label'   => __( 'Price style', 'antradus' ),
									'type'    => 'select',
									'options' => array(
										'amount' => __( 'A number', 'antradus' ),
										'custom' => __( 'Words instead of a number', 'antradus' ),
									),
								),
								array(
									'key'   => 'currency',
									'label' => __( 'Currency symbol', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'amount',
									'label' => __( 'Amount', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'With "Words instead of a number" selected, whatever you type here is shown as-is, e.g. "Let us talk".', 'antradus' ),
								),
								array(
									'key'   => 'cents',
									'label' => __( 'Decimals', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'unit',
									'label' => __( 'Unit', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'e.g. /mo', 'antradus' ),
								),
								array(
									'key'   => 'billed',
									'label' => __( 'Billing line', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'chip',
									'label' => __( 'Site-count chip', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'intro',
									'label' => __( 'Feature list heading', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'features',
									'label' => __( 'Features', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 8,
									'help'  => __( 'One per line.', 'antradus' ),
								),
								array(
									'key'   => 'cta',
									'label' => __( 'Button text', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'     => 'cta_type',
									'label'   => __( 'Button behaviour', 'antradus' ),
									'type'    => 'select',
									'options' => array(
										'link'     => __( 'Go to a link', 'antradus' ),
										'freemius' => __( 'Open the Freemius checkout', 'antradus' ),
									),
								),
								array(
									'key'   => 'cta_url',
									'label' => __( 'Button link', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'Used when the behaviour is "Go to a link".', 'antradus' ),
								),
								array(
									'key'   => 'plan_id',
									'label' => __( 'Freemius plan ID', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'Required for the checkout, unless you paste the snippet below instead. It must be a plan that really exists in your Freemius dashboard - a wrong ID renders fine and then fails at checkout.', 'antradus' ),
								),
								array(
									'key'   => 'licenses',
									'label' => __( 'Licences', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'How many sites this plan is sold for. Leave it empty - then the checkout asks for no particular quantity and the plan sells at the price it has, which is what you want almost every time. Fill it in only if the checkout answers "Invalid pricing": Freemius checks the price and the quantity together, so a plan sold as "up to 5 sites" has no one-site price to sell you. The <code>licenses</code> line in the code Freemius gives you is a sample number, not your plan, and is deliberately ignored.', 'antradus' ),
								),
								array(
									'key'     => 'trial',
									'label'   => __( 'Free trial', 'antradus' ),
									'type'    => 'select',
									'options' => array(
										''     => __( 'No trial - charge today', 'antradus' ),
										'paid' => __( 'Free trial, card required', 'antradus' ),
										'free' => __( 'Free trial, no card', 'antradus' ),
									),
									'help'  => __( 'A trial only happens when the checkout asks for one. Having set a trial on the plan in Freemius is not enough - leave this on "charge today" and the customer is billed the full price the moment they buy, however many free days the note underneath promises. Only pick a trial the plan really has.', 'antradus' ),
								),
								array(
									'key'   => 'trial_text',
									'label' => __( 'Trial link under the button', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'Words for a second, quieter link below the button - "or start a 7-day free trial". Write them and the button stops offering the trial and buys the plan outright, so the two sit side by side and the reader picks. Leave them empty and the trial stays on the button itself. Either way it needs a trial chosen above to appear at all.', 'antradus' ),
								),
								array(
									'key'   => 'fs_snippet',
									'label' => __( 'Or paste the Freemius code', 'antradus' ),
									'type'  => 'code',
									'rows'  => 8,
									'help'  => __( 'Paste the whole block Freemius gives you - <code>&lt;script&gt;</code> tags and all. The theme reads four values out of it (<code>product_id</code>, <code>plan_id</code>, <code>public_key</code>, <code>image</code>) and wires the button up itself; the pasted code is never run and never printed on the page. Anything you fill in here wins over the plan ID above and over the account settings below, so a plan can sell a different product entirely.', 'antradus' ),
								),
								array(
									'key'   => 'note',
									'label' => __( 'Note under the button', 'antradus' ),
									'type'  => 'text',
								),
							),
						),
					),
				),
				array(
					'title'  => __( 'Freemius account', 'antradus' ),
					'blurb'  => __( 'Only needed if at least one plan uses the checkout. Fill in the three fields, or just paste the code Freemius gave you and let the theme read them out of it. A plan that carries its own pasted code ignores all of this.', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'fs_snippet',
							'label' => __( 'Paste the Freemius code', 'antradus' ),
							'type'  => 'code',
							'rows'  => 10,
							'help'  => __( 'The whole block, exactly as Freemius gives it to you. Only <code>product_id</code>, <code>public_key</code> and <code>image</code> are read from it here - the plan ID comes from each plan. Nothing pasted here is ever executed or printed to the page; the theme writes its own checkout call from the values it finds.', 'antradus' ),
						),
						array(
							'key'   => 'fs_product_id',
							'label' => __( 'Product ID', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'Used when there is no pasted code above.', 'antradus' ),
						),
						array(
							'key'   => 'fs_public_key',
							'label' => __( 'Public key', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'The public key only - never the secret key. It is meant to be visible in the page source; the secret key never belongs in a browser.', 'antradus' ),
						),
						array(
							'key'   => 'fs_logo',
							'label' => __( 'Checkout logo', 'antradus' ),
							'type'  => 'image',
						),
					),
				),
				array(
					'title'  => __( 'Comparison table', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'price_cmp_show',
							'label' => __( 'Show the table', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Show the feature comparison', 'antradus' ),
						),
						array(
							'key'   => 'price_cmp_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'price_cmp_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'   => 'price_cmp_col_a',
							'label' => __( 'First column heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'price_cmp_col_a_sub',
							'label' => __( 'First column sub-heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'price_cmp_col_b',
							'label' => __( 'Second column heading', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'This is the highlighted column - put the plan most people choose here.', 'antradus' ),
						),
						array(
							'key'   => 'price_cmp_col_b_sub',
							'label' => __( 'Second column sub-heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'price_cmp_col_c',
							'label' => __( 'Third column heading', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'Optional. Leave this empty and the table has two columns, and the third answer on every row below is ignored.', 'antradus' ),
						),
						array(
							'key'   => 'price_cmp_col_c_sub',
							'label' => __( 'Third column sub-heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'    => 'price_cmp_rows',
							'label'  => __( 'Rows', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Row', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'group',
									'label' => __( 'Group heading', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'Fill this in and the row becomes a full-width divider. Leave the rest of the row empty.', 'antradus' ),
								),
								array(
									'key'   => 'name',
									'label' => __( 'Feature', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'Explanation', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 2,
								),
								array(
									'key'     => 'a',
									'label'   => __( 'First column', 'antradus' ),
									'type'    => 'select',
									'options' => array(
										'yes' => __( 'Included', 'antradus' ),
										'no'  => __( 'Not included', 'antradus' ),
									),
								),
								array(
									'key'     => 'b',
									'label'   => __( 'Second column', 'antradus' ),
									'type'    => 'select',
									'options' => array(
										'yes' => __( 'Included', 'antradus' ),
										'no'  => __( 'Not included', 'antradus' ),
									),
								),
								array(
									'key'     => 'c',
									'label'   => __( 'Third column', 'antradus' ),
									'type'    => 'select',
									'options' => array(
										'yes' => __( 'Included', 'antradus' ),
										'no'  => __( 'Not included', 'antradus' ),
									),
								),
							),
						),
					),
				),
				array(
					'title'  => __( 'Questions and footer', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'price_faq_title',
							'label' => __( 'Questions heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'    => 'price_faq_items',
							'label'  => __( 'Questions', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Question', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'q',
									'label' => __( 'Question', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'a',
									'label' => __( 'Answer', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 3,
								),
							),
						),
						array(
							'key'   => 'price_sales_text',
							'label' => __( 'Sales strip text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'price_sales_btn',
							'label' => __( 'Sales strip button', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'price_sales_url',
							'label' => __( 'Sales strip link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
						array(
							'key'   => 'price_trust',
							'label' => __( 'Trust line', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 4,
							'help'  => __( 'One item per line.', 'antradus' ),
						),
					),
				),
			),
		),

		/* =================================================================
		 * Blog
		 * ============================================================== */
		'blog'     => array(
			'label'    => __( 'Blog &amp; posts', 'antradus' ),
			'blurb'    => __( 'The article index and the single-post design your existing posts are shown in. Nothing here changes your posts - only how they are presented.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'The index', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'blog_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'blog_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'blog_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'blog_per_page',
							'label' => __( 'Articles per page', 'antradus' ),
							'type'  => 'number',
						),
						array(
							'key'   => 'blog_show_search',
							'label' => __( 'Search box', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Let readers search the articles', 'antradus' ),
						),
						array(
							'key'   => 'blog_show_cats',
							'label' => __( 'Category filter', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Show a row of category chips', 'antradus' ),
						),
						array(
							'key'   => 'blog_empty',
							'label' => __( 'Empty-results message', 'antradus' ),
							'type'  => 'text',
						),
					),
				),
				array(
					'title'  => __( 'Single article', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'single_back',
							'label' => __( 'Back link text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'single_rail',
							'label' => __( 'Sidebar heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'single_cta_show',
							'label' => __( 'Call to action', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Show a promo box at the end of every article', 'antradus' ),
						),
						array(
							'key'   => 'single_cta_title',
							'label' => __( 'Promo heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'single_cta_sub',
							'label' => __( 'Promo text', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'   => 'single_cta_btn',
							'label' => __( 'Promo button', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'single_cta_url',
							'label' => __( 'Promo link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
					),
				),
			),
		),

		/* =================================================================
		 * Contact
		 * ============================================================== */
		'contact'  => array(
			'label'    => __( 'Contact', 'antradus' ),
			'blurb'    => __( 'The contact page and the affiliate section beneath it. Both forms are whatever shortcode you paste in - Forminator, Contact Form 7, WPForms, anything - and both are per language, so an Arabic form built in Arabic is what an Arabic reader gets.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'Hero', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'contact_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'contact_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'contact_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
				array(
					'title'  => __( 'The form', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'contact_form_head',
							'label' => __( 'Panel heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'contact_form_hint',
							'label' => __( 'Panel hint', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'     => 'contact_form',
							'label'   => __( 'Form shortcode', 'antradus' ),
							'type'    => 'text',
							'help'    => __( 'Leave empty to use whatever the page itself contains. Each language keeps its own - paste the Arabic form on the العربية tab, and an Arabic reader gets the Arabic form. Left empty there, they get this one.', 'antradus' ),
							'restore' => false,
						),
					),
				),
				array(
					'title'  => __( 'Info cards', 'antradus' ),
					'fields' => array(
						array(
							'key'    => 'contact_cards',
							'label'  => __( 'Cards', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Card', 'antradus' ),
							'fields' => array(
								array(
									'key'     => 'icon',
									'label'   => __( 'Icon', 'antradus' ),
									'type'    => 'select',
									'options' => $icons,
								),
								array(
									'key'   => 'title',
									'label' => __( 'Title', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'Text', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 3,
								),
								array(
									'key'   => 'label',
									'label' => __( 'Link text', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'link',
									'label' => __( 'Link target', 'antradus' ),
									'type'  => 'text',
									'help'  => __( 'A URL, a mailto: address, or page:pricing.', 'antradus' ),
								),
							),
						),
					),
				),
				array(
					'title'  => __( 'Affiliate section', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'aff_show',
							'label' => __( 'Show it', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Include the affiliate programme below the contact form', 'antradus' ),
						),
						array(
							'key'   => 'aff_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'aff_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'aff_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'    => 'aff_steps',
							'label'  => __( 'Steps', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Step', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'num',
									'label' => __( 'Number', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'title',
									'label' => __( 'Title', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'text',
									'label' => __( 'Text', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 2,
								),
							),
						),
						array(
							'key'   => 'aff_form_head',
							'label' => __( 'Form panel heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'aff_form_hint',
							'label' => __( 'Form panel hint', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'     => 'aff_form',
							'label'   => __( 'Form shortcode', 'antradus' ),
							'type'    => 'text',
							'help'    => __( 'Each language keeps its own - paste the Arabic form on the العربية tab. Left empty there, the Arabic page shows this form.', 'antradus' ),
							'restore' => false,
						),
					),
				),
				array(
					'title'  => __( 'Footer line', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'contact_trust',
							'label' => __( 'Trust line', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
							'help'  => __( 'One item per line.', 'antradus' ),
						),
					),
				),
			),
		),

		/* =================================================================
		 * Welcome
		 * ============================================================== */
		'welcome'  => array(
			'label'    => __( 'Welcome', 'antradus' ),
			'blurb'    => __( 'The newsletter page. If you leave the shortcode empty, whatever the page itself contains is wrapped in the design instead - which is how the old snippet worked.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'The page', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'welcome_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'welcome_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'welcome_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'welcome_bullets',
							'label' => __( 'What subscribers get', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 4,
							'help'  => __( 'One per line.', 'antradus' ),
						),
						array(
							'key'   => 'welcome_image',
							'label' => __( 'Side image', 'antradus' ),
							'type'  => 'image',
						),
						array(
							'key'     => 'welcome_form',
							'label'   => __( 'Form shortcode', 'antradus' ),
							'type'    => 'text',
							'help'    => __( 'Leave empty to use the page content. Each language keeps its own - paste the Arabic form on the العربية tab. Left empty there, the Arabic page shows this form.', 'antradus' ),
							'restore' => false,
						),
						array(
							'key'   => 'welcome_note',
							'label' => __( 'Note under the form', 'antradus' ),
							'type'  => 'text',
						),
					),
				),
			),
		),

		/* =================================================================
		 * Docs
		 * ============================================================== */
		'docs'     => array(
			'label'    => __( 'Docs', 'antradus' ),
			'blurb'    => __( 'The documentation hub. The guides are written and published by the Antradus AI plugin; the theme lays them out, groups them by category and adds the search. There is nothing to add to the page itself.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'The page', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'docs_eyebrow',
							'label' => __( 'Eyebrow', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'docs_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'docs_sub',
							'label' => __( 'Supporting paragraph', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'docs_note',
							'label' => __( 'Shown when there are no guides yet', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 2,
						),
					),
				),
				array(
					'title'  => __( 'Search', 'antradus' ),
					'blurb'  => __( 'The box filters the guides as you type. It also works without JavaScript, as an ordinary search that reloads the page - both halves match the same way, so a shared link shows what the typing showed.', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'docs_search_label',
							'label' => __( 'Label for screen readers', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'docs_search_hint',
							'label' => __( 'Placeholder in the box', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'docs_all_label',
							'label' => __( 'Name of the "everything" chip', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'docs_empty',
							'label' => __( 'Shown when nothing matches', 'antradus' ),
							'type'  => 'text',
						),
					),
				),
			),
		),

		/* =================================================================
		 * Footer
		 * ============================================================== */
		'footer'   => array(
			'label'    => __( 'Footer', 'antradus' ),
			'blurb'    => __( 'The band at the bottom of every page. Links written as <code>page:</code> disappear on their own while that page is a draft.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'Closing strip', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'footer_cta_title',
							'label' => __( 'Heading', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'Clear this to hide the strip.', 'antradus' ),
						),
						array(
							'key'   => 'footer_cta_btn',
							'label' => __( 'Button text', 'antradus' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'footer_cta_url',
							'label' => __( 'Button link', 'antradus' ),
							'type'  => 'text',
							'help'  => $link,
						),
					),
				),
				array(
					'title'  => __( 'Columns', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'footer_tagline',
							'label' => __( 'Tagline', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'    => 'footer_cols',
							'label'  => __( 'Link columns', 'antradus' ),
							'type'   => 'repeater',
							'single' => __( 'Column', 'antradus' ),
							'fields' => array(
								array(
									'key'   => 'title',
									'label' => __( 'Column heading', 'antradus' ),
									'type'  => 'text',
								),
								array(
									'key'   => 'links',
									'label' => __( 'Links', 'antradus' ),
									'type'  => 'textarea',
									'rows'  => 6,
									'help'  => __( 'One per line, as <code>Label | target</code>.', 'antradus' ),
								),
							),
						),
						array(
							'key'   => 'footer_social',
							'label' => __( 'Social links', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 4,
							'help'  => __( 'One per line, as <code>Label | https://...</code>.', 'antradus' ),
						),
					),
				),
				array(
					'title'  => __( 'Bottom line', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'footer_trust',
							'label' => __( 'Trust line', 'antradus' ),
							'type'  => 'textarea',
							'rows'  => 3,
							'help'  => __( 'One item per line.', 'antradus' ),
						),
						array(
							'key'   => 'footer_legal',
							'label' => __( 'Copyright line', 'antradus' ),
							'type'  => 'text',
							'help'  => __( 'The year and the site name are added automatically.', 'antradus' ),
						),
					),
				),
			),
		),

		/* =================================================================
		 * Security
		 * ============================================================== */
		'security' => array(
			'label'    => __( 'Security', 'antradus' ),
			'blurb'    => __( 'All of these are on. Each one closes a way in that costs an attacker nothing to try, and none of them changes anything a reader sees. They are switches rather than fixed behaviour only because a site can always turn out to need one of them - not because any of them should be off.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'Ways in', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'sec_no_enum',
							'label' => __( 'Hide the account list', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Refuse /?author=1 and close the REST users endpoint to strangers', 'antradus' ),
							'help'  => __( 'A username is half a credential, and WordPress publishes them by default in two places. Logged-in editors keep the endpoint, so the block editor is unaffected.', 'antradus' ),
						),
						array(
							'key'   => 'sec_no_xmlrpc',
							'label' => __( 'Switch off XML-RPC', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Refuse XML-RPC, its pingback methods and system.multicall', 'antradus' ),
							'help'  => __( 'One XML-RPC request can try hundreds of passwords, which is why it is the door that gets knocked on. Leave this off only if you publish from a desktop or mobile app that needs it.', 'antradus' ),
						),
						array(
							'key'   => 'sec_login',
							'label' => __( 'Slow down password guessing', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'One message for every failed sign-in, and a pause after ten failures', 'antradus' ),
							'help'  => __( 'Ten tries from one address, then a fifteen-minute wait. Signing in successfully clears the count, so mistyping your own password a few times costs you nothing.', 'antradus' ),
						),
					),
				),
				array(
					'title'  => __( 'What we tell the browser', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'sec_headers',
							'label' => __( 'Send the security headers', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'nosniff, a referrer policy, SAMEORIGIN framing, and rel="noopener" on outbound links', 'antradus' ),
							'help'  => __( 'Deliberately not a Content-Security-Policy: on a site running eight plugins a strict policy breaks something on day one and gets switched off, which is worse than never having had it.', 'antradus' ),
						),
						array(
							'key'   => 'sec_clean_head',
							'label' => __( 'Stop announcing the version', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Remove the generator tag, the RSD and manifest links, and the version from core asset URLs', 'antradus' ),
							'help'  => __( 'This fixes nothing on its own. It does mean an automated sweep has to actually attempt an exploit to find out whether it is worth attempting - and attempts are the thing your logs can see.', 'antradus' ),
						),
					),
				),
				array(
					'title'  => __( 'Comments', 'antradus' ),
					'fields' => array(
						array(
							'key'   => 'comments_enable',
							'label' => __( 'Allow comments', 'antradus' ),
							'type'  => 'checkbox',
							'cbtxt' => __( 'Turn comments back on across the whole site', 'antradus' ),
							'help'  => __( 'Off by default. Off means the form is gone, the endpoints refuse a post, and the Comments menu disappears - not merely that the form is hidden. Existing comments are never deleted: they are hidden, and turning this back on brings them back exactly as they were.', 'antradus' ),
						),
					),
				),
				array(
					'title'  => __( 'Also worth knowing', 'antradus' ),
					'blurb'  => __( 'The theme also switches off the built-in Theme and Plugin file editors, by defining <code>DISALLOW_FILE_EDIT</code>. Those editors are the most useful thing an attacker can reach with a stolen administrator session, and this site is deployed as a zip rather than typed into a browser. Two things the theme cannot do for you, and your host can: refuse to run PHP inside <code>wp-content/uploads</code>, and put HTTPS in front of everything.', 'antradus' ),
					'fields' => array(),
				),
			),
		),

		/* =================================================================
		 * Tools
		 * ============================================================== */
		'tools'    => array(
			'label'    => __( 'Tools', 'antradus' ),
			'blurb'    => __( 'Housekeeping.', 'antradus' ),
			'sections' => array(
				array(
					'title'  => __( 'Updating the theme', 'antradus' ),
					'render' => 'antradus_render_safety_panel',
					'fields' => array(),
				),
				array(
					'title'  => __( 'Import, export, undo, reset', 'antradus' ),
					'render' => 'antradus_render_tools_panel',
					'fields' => array(),
				),
			),
		),
	);
}

/**
 * The field keys one tab owns.
 *
 * Used by the per-section restore on the Tools tab: bringing back the shipped
 * Pricing content should not touch the words on the home page.
 *
 * @param string $tab Tab key, e.g. 'pricing'.
 * @return string[]
 */
function antradus_tab_field_keys( $tab ) {
	$schema = antradus_settings_schema();
	if ( ! isset( $schema[ $tab ]['sections'] ) ) {
		return array();
	}
	$keys = array();
	foreach ( $schema[ $tab ]['sections'] as $section ) {
		foreach ( $section['fields'] as $field ) {
			if ( isset( $field['key'] ) ) {
				$keys[] = $field['key'];
			}
		}
	}
	return $keys;
}

/**
 * The tabs that hold words, in the order they are shown.
 *
 * Pages, Security and Tools are excluded: they hold switches and slugs, not
 * content, and "restore the shipped wording" means nothing for them.
 *
 * @return array<string,string> Tab key => label.
 */
function antradus_content_tabs() {
	$out = array();
	foreach ( antradus_settings_schema() as $key => $tab ) {
		if ( in_array( $key, array( 'pages', 'security', 'tools' ), true ) ) {
			continue;
		}
		if ( antradus_tab_field_keys( $key ) ) {
			$out[ $key ] = $tab['label'];
		}
	}
	return $out;
}

/**
 * Flatten the schema into key => field definition.
 *
 * @return array<string,array>
 */
function antradus_schema_fields() {
	static $flat = null;
	if ( null !== $flat ) {
		return $flat;
	}
	$flat = array();
	foreach ( antradus_settings_schema() as $tab ) {
		foreach ( $tab['sections'] as $section ) {
			foreach ( $section['fields'] as $field ) {
				$flat[ $field['key'] ] = $field;
			}
		}
	}
	return $flat;
}
