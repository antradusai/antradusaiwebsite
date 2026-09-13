<?php
/**
 * Antradus theme - the content settings screen.
 *
 * Every editable string on the site is described by antradus_settings_schema().
 * This file renders that schema and sanitizes what comes back. Nothing outside
 * the schema can be saved.
 *
 * Three things are worth knowing before changing anything here.
 *
 * 1. The screen shows one tab at a time but posts a single option. Saving one
 *    tab must not blank out the others, so the form declares which keys were on
 *    screen (the __present list) and the sanitizer only touches those. That is
 *    also what lets an unticked checkbox - which posts nothing at all - be
 *    recorded as off rather than left as it was.
 *
 * 2. There are two option rows, one per language, and both are registered to
 *    the same settings group. Which one a save lands in is decided by the field
 *    names the form printed, which is decided by the language tab. The Arabic
 *    row only ever accepts words; a URL or an image posted into it is dropped
 *    on the floor by the sanitizer, so the two languages cannot drift apart.
 *
 * 3. Nothing on this screen can be reached without manage_options AND a valid
 *    nonce. The capability check is antradus_require_admin(), which lives in
 *    inc/security.php so that the menu, the page and all four admin-post
 *    handlers cannot disagree about who is allowed in.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/* ===========================================================================
 * 1. Registration
 * ========================================================================= */

add_action( 'admin_menu', 'antradus_settings_menu' );
/**
 * Add the screen under Appearance.
 */
function antradus_settings_menu() {
	add_theme_page(
		__( 'Antradus Content', 'antradus' ),
		__( 'Antradus Content', 'antradus' ),
		antradus_settings_cap(),
		'antradus-content',
		'antradus_settings_page'
	);
}

add_action( 'admin_init', 'antradus_settings_register' );
/**
 * One registration per language, both in the same group.
 */
function antradus_settings_register() {
	register_setting(
		'antradus_content_group',
		ANTRADUS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'antradus_sanitize_options',
			'default'           => array(),
		)
	);

	foreach ( antradus_languages() as $code => $unused ) {
		if ( antradus_default_lang() === $code ) {
			continue;
		}
		register_setting(
			'antradus_content_group',
			antradus_option_name( $code ),
			array(
				'type'              => 'array',
				'sanitize_callback' => 'antradus_sanitize_translation',
				'default'           => array(),
			)
		);
	}
}

/**
 * Which language the screen is editing right now.
 *
 * @return string
 */
function antradus_settings_editing_lang() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- navigation only.
	$asked = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : '';
	return antradus_is_lang( $asked ) ? $asked : antradus_default_lang();
}

add_action( 'admin_enqueue_scripts', 'antradus_settings_assets' );
/**
 * Media library, the settings stylesheet and the repeater script.
 *
 * @param string $hook Current admin page.
 */
function antradus_settings_assets( $hook ) {
	if ( 'appearance_page_antradus-content' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style(
		'antradus-admin',
		get_template_directory_uri() . '/assets/css/admin.css',
		array(),
		ANTRADUS_VERSION
	);
	wp_enqueue_script(
		'antradus-admin',
		get_template_directory_uri() . '/assets/js/admin.js',
		array( 'jquery' ),
		ANTRADUS_VERSION,
		true
	);
	wp_localize_script(
		'antradus-admin',
		'antradusAdmin',
		array(
			'chooseImage'  => __( 'Choose an image', 'antradus' ),
			'useImage'     => __( 'Use this image', 'antradus' ),
			'chooseImages' => __( 'Add pictures', 'antradus' ),
			'useImages'    => __( 'Add these pictures', 'antradus' ),
			'moveEarlier'  => __( 'Move earlier', 'antradus' ),
			'moveLater'    => __( 'Move later', 'antradus' ),
			'dropImage'    => __( 'Remove this picture', 'antradus' ),
			'editImage'    => __( 'Open this picture in the media library', 'antradus' ),
			'doneImage'    => __( 'Done', 'antradus' ),
			'confirmClear' => __( 'Remove every picture from this slot?', 'antradus' ),
			'confirmDrop'  => __( 'Remove this item?', 'antradus' ),
			'noImage'      => __( 'No picture', 'antradus' ),
		)
	);
}

/* ===========================================================================
 * 2. The screen
 * ========================================================================= */

/**
 * Render the settings screen.
 */
function antradus_settings_page() {
	antradus_require_admin();

	$lang    = antradus_settings_editing_lang();
	$is_base = ( antradus_default_lang() === $lang );

	// Field values are read through antradus_opt(), which follows the current
	// language - so put the screen in the language it is editing.
	antradus_set_lang_context( $lang );

	$schema  = antradus_settings_schema();
	$tabs    = array_keys( $schema );
	$current = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'pages'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- navigation only.
	if ( ! in_array( $current, $tabs, true ) ) {
		$current = 'pages';
	}
	$tab = $schema[ $current ];

	$tab_url = static function ( $key, $lang ) {
		$args = array(
			'page' => 'antradus-content',
			'tab'  => $key,
		);
		if ( antradus_default_lang() !== $lang ) {
			$args['lang'] = $lang;
		}
		return admin_url( 'themes.php?' . build_query( $args ) );
	};
	?>
	<div class="wrap antradus-settings<?php echo $is_base ? '' : ' antradus-settings--translating'; ?>">
		<h1 class="antradus-title">
			<span class="antradus-title-mark" aria-hidden="true"></span>
			<?php esc_html_e( 'Antradus Content', 'antradus' ); ?>
		</h1>
		<p class="antradus-lede">
			<?php esc_html_e( 'Every word and picture on the marketing pages, in one place. Nothing here needs a snippet, a page builder or a code editor.', 'antradus' ); ?>
		</p>

		<?php
		/*
		 * The language bar sits above the section tabs because it changes what
		 * every one of them contains. Putting it below would read as a filter
		 * on the current tab rather than a mode the whole screen is in.
		 */
		?>
		<div class="antradus-langbar">
			<span class="antradus-langbar-label"><?php esc_html_e( 'Editing', 'antradus' ); ?></span>
			<?php
			foreach ( antradus_languages() as $code => $def ) :
				$progress = antradus_translation_progress( $code );
				?>
				<a class="antradus-langbtn <?php echo $code === $lang ? 'is-current' : ''; ?>"
					href="<?php echo esc_url( $tab_url( $current, $code ) ); ?>"
					lang="<?php echo esc_attr( $code ); ?>">
					<?php echo esc_html( $def['native'] ); ?>
					<?php if ( antradus_default_lang() !== $code ) : ?>
						<span class="antradus-langbtn-count">
							<?php
							printf(
								'%s / %s',
								esc_html( number_format_i18n( $progress['done'] ) ),
								esc_html( number_format_i18n( $progress['total'] ) )
							);
							?>
						</span>
						<?php
						/*
						 * Said on every tab, not only on the one holding the
						 * switch. Somebody translating the pricing table for an
						 * hour should not have to remember that none of it is
						 * reaching anybody yet.
						 */
						if ( ! antradus_lang_is_published( $code ) ) :
							?>
							<span class="antradus-langbtn-off"><?php esc_html_e( 'not published', 'antradus' ); ?></span>
						<?php endif; ?>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>

		<?php if ( ! $is_base ) : ?>
			<div class="antradus-translating-note">
				<strong><?php esc_html_e( 'You are writing the translation.', 'antradus' ); ?></strong>
				<?php esc_html_e( 'Only the words are shown - links, images, colours, shortcodes, plan IDs and page slugs are shared by both languages and are edited on the English tab. Leave a field empty and the English wording is used on the Arabic page, so a half-finished translation still reads.', 'antradus' ); ?>
				<?php if ( ! antradus_lang_is_published( $lang ) ) : ?>
					<br>
					<strong><?php esc_html_e( 'This language is switched off.', 'antradus' ); ?></strong>
					<?php esc_html_e( 'Nothing you write here reaches a visitor yet, and nothing is lost while it waits. Take as long as you need, then publish it in one tick on the Brand &amp; header tab.', 'antradus' ); ?>
					<a href="<?php echo esc_url( admin_url( 'themes.php?page=antradus-content&tab=brand' ) ); ?>"><?php esc_html_e( 'Go to the switch', 'antradus' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<h2 class="nav-tab-wrapper antradus-tabs">
			<?php foreach ( $schema as $key => $def ) : ?>
				<?php
				// Housekeeping is not a translation job.
				if ( ! $is_base && in_array( $key, array( 'pages', 'tools', 'security' ), true ) ) {
					continue;
				}
				?>
				<a class="nav-tab <?php echo $key === $current ? 'nav-tab-active' : ''; ?>"
					href="<?php echo esc_url( $tab_url( $key, $lang ) ); ?>">
					<?php echo wp_kses( $def['label'], array() ); ?>
				</a>
			<?php endforeach; ?>
		</h2>

		<?php if ( ! empty( $tab['blurb'] ) && $is_base ) : ?>
			<p class="antradus-blurb"><?php echo wp_kses( $tab['blurb'], antradus_admin_kses() ); ?></p>
		<?php endif; ?>

		<?php
		/*
		 * Only wrap the page in the settings form when this tab actually has
		 * something to save. Tools has no fields at all - it is four buttons
		 * that each post to admin-post.php - and wrapping those in a form
		 * pointing at options.php nested one form inside another. HTML has no
		 * such thing: the browser drops the inner tags, and every one of those
		 * buttons then submitted the settings form instead, which is why
		 * Export and Reset dumped people on the raw All Settings screen.
		 */
		$has_fields = false;
		foreach ( $tab['sections'] as $section ) {
			$fields = isset( $section['fields'] ) ? $section['fields'] : array();
			if ( ! $is_base ) {
				$fields = array_filter( $fields, 'antradus_field_is_translatable' );
			}
			if ( $fields ) {
				$has_fields = true;
				break;
			}
		}

		if ( $has_fields ) {
			echo '<form method="post" action="options.php" class="antradus-form">';
			settings_fields( 'antradus_content_group' );
		}

		$present = array();
		foreach ( $tab['sections'] as $section ) {
			$present = array_merge( $present, antradus_render_section( $section, $lang ) );
		}

		if ( $has_fields ) {
			printf(
				'<input type="hidden" name="%s[__present]" value="%s">',
				esc_attr( antradus_option_name( $lang ) ),
				esc_attr( implode( ',', $present ) )
			);
			if ( $present ) {
				echo '<div class="antradus-save">';
				submit_button( __( 'Save changes', 'antradus' ), 'primary large', 'submit', false );
				echo '</div>';
			}
			echo '</form>';
		}
		?>
	</div>
	<?php
	antradus_set_lang_context( null );
}

/**
 * HTML allowed inside admin help text.
 *
 * @return array
 */
function antradus_admin_kses() {
	return array(
		'code'   => array(),
		'strong' => array(),
		'em'     => array(),
		'br'     => array(),
		'a'      => array(
			'href'   => array(),
			'target' => array(),
			'rel'    => array(),
		),
	);
}

/**
 * One panel of fields.
 *
 * @param array  $section Section definition.
 * @param string $lang    Language being edited.
 * @return string[] The keys actually rendered.
 */
function antradus_render_section( $section, $lang ) {
	$is_base = ( antradus_default_lang() === $lang );
	$fields  = isset( $section['fields'] ) ? $section['fields'] : array();

	// A translation tab shows only the fields a translator can act on. A
	// section left with nothing to translate is not rendered as an empty card.
	if ( ! $is_base ) {
		$fields = array_values( array_filter( $fields, 'antradus_field_is_translatable' ) );
		if ( ! $fields ) {
			return array();
		}
	}

	echo '<div class="antradus-card">';
	if ( ! empty( $section['title'] ) ) {
		echo '<h2 class="antradus-card-title">' . esc_html( $section['title'] ) . '</h2>';
	}
	if ( ! empty( $section['blurb'] ) && $is_base ) {
		echo '<p class="antradus-card-blurb">' . wp_kses( $section['blurb'], antradus_admin_kses() ) . '</p>';
	}

	if ( $is_base && ! empty( $section['render'] ) && is_callable( $section['render'] ) ) {
		call_user_func( $section['render'] );
	}

	$present = array();
	if ( $fields ) {
		echo '<table class="form-table antradus-table" role="presentation"><tbody>';
		foreach ( $fields as $field ) {
			antradus_render_field( $field, $lang );
			$present[] = $field['key'];
		}
		echo '</tbody></table>';
	}
	echo '</div>';

	return $present;
}

/**
 * One field row.
 *
 * @param array  $field Field definition.
 * @param string $lang  Language being edited.
 */
function antradus_render_field( $field, $lang ) {
	$key   = $field['key'];
	$type  = isset( $field['type'] ) ? $field['type'] : 'text';
	$value = antradus_field_raw_value( $key, $lang );
	$id    = 'ant-f-' . $key;
	$name  = antradus_option_name( $lang ) . '[' . $key . ']';

	/*
	 * A repeater is not a label-and-a-control, it is a stack of cards. It used
	 * to be given a <th> that CSS then hid - which quietly moved its <td> into
	 * the first column of the table, so every repeater rendered inside a 210px
	 * gutter with its fields clipped. Spanning both columns is the fix: there
	 * is no hidden cell to collapse, so there is no narrow column to fall into.
	 */
	if ( 'repeater' === $type ) {
		echo '<tr class="antradus-row antradus-row--wide"><td colspan="2">';
		echo '<p class="antradus-rep-label">' . esc_html( $field['label'] ) . '</p>';
		antradus_render_repeater( $field, $lang );
		if ( ! empty( $field['help'] ) ) {
			echo '<p class="description">' . wp_kses( $field['help'], antradus_admin_kses() ) . '</p>';
		}
		echo '</td></tr>';
		return;
	}

	echo '<tr class="antradus-row">';
	echo '<th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th>';
	echo '<td>';

	switch ( $type ) {
		case 'textarea':
		case 'code':
			printf(
				'<textarea id="%1$s" name="%2$s" rows="%3$d" class="large-text %4$s"%5$s>%6$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				isset( $field['rows'] ) ? (int) $field['rows'] : 4,
				'code' === $type ? 'antradus-code' : 'code-ish',
				'code' === $type ? ' spellcheck="false"' : '',
				esc_textarea( is_string( $value ) ? $value : '' )
			);
			break;

		case 'checkbox':
			printf(
				'<label class="antradus-check"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> <span>%4$s</span></label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( '1', (string) $value, false ),
				esc_html( isset( $field['cbtxt'] ) ? $field['cbtxt'] : __( 'Enabled', 'antradus' ) )
			);
			break;

		case 'select':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( (array) $field['options'] as $opt_val => $opt_label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $opt_val ),
					selected( (string) $opt_val, (string) $value, false ),
					esc_html( $opt_label )
				);
			}
			echo '</select>';
			break;

		case 'color':
			printf(
				'<span class="antradus-color"><input type="color" id="%1$s" name="%2$s" value="%3$s">'
				. '<input type="text" class="antradus-color-text" value="%3$s" aria-label="%4$s"></span>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ? $value : '#1f6feb' ),
				esc_attr__( 'Colour value', 'antradus' )
			);
			break;

		case 'number':
			printf(
				'<input type="number" id="%1$s" name="%2$s" value="%3$s" class="small-text" min="1" max="100">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			break;

		case 'image':
			antradus_render_image_field( $id, $name, (string) $value );
			break;

		case 'images':
			antradus_render_images_field( $id, $name, (string) $value );
			break;

		case 'order':
			antradus_render_order_field( $name, (string) $value, $field );
			break;

		default:
			printf(
				'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="regular-text antradus-wide">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( is_string( $value ) ? $value : '' )
			);
	}

	// While translating, show the English underneath - a translator should not
	// have to open a second tab to see what they are translating.
	antradus_render_source_hint( $key, null, $lang );

	if ( ! empty( $field['help'] ) ) {
		echo '<p class="description">' . wp_kses( $field['help'], antradus_admin_kses() ) . '</p>';
	}
	echo '</td></tr>';
}

/**
 * The stored value for a field in one language, without any fallback.
 *
 * antradus_opt() deliberately falls back to English so the site always reads.
 * The editor must show the opposite: an empty Arabic box means "not translated
 * yet", and pre-filling it with English would hide exactly the thing the
 * translator is looking for.
 *
 * @param string $key  Field key.
 * @param string $lang Language.
 * @return mixed
 */
function antradus_field_raw_value( $key, $lang ) {
	if ( antradus_default_lang() === $lang ) {
		return antradus_opt( $key, '' );
	}
	$stored = get_option( antradus_option_name( $lang ), array() );
	$stored = is_array( $stored ) ? $stored : array();
	if ( array_key_exists( $key, $stored ) ) {
		return $stored[ $key ];
	}
	$shipped = antradus_default_options_for( $lang );
	return array_key_exists( $key, $shipped ) ? $shipped[ $key ] : '';
}

/**
 * Print the English wording under a translation field.
 *
 * @param string      $key     Field key.
 * @param string|null $sub_key Repeater sub-key, when inside a row.
 * @param string      $lang    Language being edited.
 * @param int         $index   Row index, when inside a repeater.
 */
function antradus_render_source_hint( $key, $sub_key, $lang, $index = -1 ) {
	if ( antradus_default_lang() === $lang ) {
		return;
	}
	antradus_set_lang_context( antradus_default_lang() );
	$source = ( null === $sub_key )
		? antradus_opt( $key, '' )
		: antradus_cell( antradus_row_at( $key, $index ), $sub_key, '' );
	antradus_set_lang_context( $lang );

	if ( ! is_string( $source ) || '' === trim( $source ) ) {
		return;
	}
	printf(
		'<p class="antradus-source" lang="en" dir="ltr"><span>%1$s</span> %2$s</p>',
		esc_html__( 'English', 'antradus' ),
		esc_html( $source )
	);
}

/**
 * One row out of a repeater, by index.
 *
 * @param string $key   Repeater key.
 * @param int    $index Row index.
 * @return array
 */
function antradus_row_at( $key, $index ) {
	$rows = antradus_opt( $key, array() );
	$rows = is_array( $rows ) ? array_values( $rows ) : array();
	return isset( $rows[ $index ] ) && is_array( $rows[ $index ] ) ? $rows[ $index ] : array();
}

/**
 * The media picker.
 *
 * @param string $id    Field id.
 * @param string $name  Field name.
 * @param string $value Stored value (attachment id or URL).
 */
function antradus_render_image_field( $id, $name, $value ) {
	$url = antradus_image_url( $value, 'medium' );
	echo '<div class="antradus-image">';
	echo '<div class="antradus-image-preview">';
	if ( $url ) {
		printf( '<img src="%s" alt="">', esc_url( $url ) );
	} else {
		echo '<span class="antradus-image-empty">' . esc_html__( 'No image yet - a labelled placeholder is shown on the site.', 'antradus' ) . '</span>';
	}
	echo '</div>';
	printf(
		'<input type="hidden" class="antradus-image-value" id="%1$s" name="%2$s" value="%3$s">',
		esc_attr( $id ),
		esc_attr( $name ),
		esc_attr( $value )
	);
	echo '<p class="antradus-image-actions">';
	echo '<button type="button" class="button antradus-image-pick">' . esc_html__( 'Choose image', 'antradus' ) . '</button> ';
	echo '<button type="button" class="button-link antradus-image-clear">' . esc_html__( 'Remove', 'antradus' ) . '</button>';
	echo '</p>';
	echo '</div>';
}

/**
 * The media picker that takes more than one picture.
 *
 * The list is the field: the thumbnails on screen, left to right, are the
 * slides the page will show, in that order. Each one carries its own value in a
 * data attribute and the hidden input is rewritten from the row on every
 * change, so reordering is moving a thumbnail rather than retyping anything -
 * the same arrows the repeater rows and the section order already use.
 *
 * Clicking a thumbnail opens it in WordPress's own media modal, which is where
 * the caption, the alt text and the title are edited - the caption a slide
 * shows on the site is the picture's own, so the place to write it is the place
 * the picture lives, not a second field here that could disagree with it.
 * Choosing a different picture in that modal swaps this one for it, in place.
 *
 * This is a top-level field only. A repeater sub-field stays a single image:
 * the export would have to describe a list inside a row inside a language, and
 * nothing on the site needs it.
 *
 * @param string $id    Field id.
 * @param string $name  Field name.
 * @param string $value Stored comma-separated list.
 */
function antradus_render_images_field( $id, $name, $value ) {
	$items = antradus_image_list( $value );

	echo '<div class="antradus-images">';
	echo '<ul class="antradus-images-list">';

	foreach ( $items as $item ) {
		$url = antradus_image_url( $item, 'medium' );
		if ( '' === $url ) {
			continue;
		}
		printf(
			'<li class="antradus-images-item" data-value="%1$s">'
			. '<button type="button" class="antradus-images-edit" aria-label="%2$s"><img src="%3$s" alt=""></button>'
			. '<span class="antradus-images-tools">'
			. '<button type="button" class="antradus-images-move" data-dir="up" aria-label="%4$s">&#8592;</button>'
			. '<button type="button" class="antradus-images-move" data-dir="down" aria-label="%5$s">&#8594;</button>'
			. '<button type="button" class="antradus-images-drop" aria-label="%6$s">&times;</button>'
			. '</span></li>',
			esc_attr( $item ),
			esc_attr__( 'Open this picture in the media library', 'antradus' ),
			esc_url( $url ),
			esc_attr__( 'Move earlier', 'antradus' ),
			esc_attr__( 'Move later', 'antradus' ),
			esc_attr__( 'Remove this picture', 'antradus' )
		);
	}

	echo '</ul>';

	printf(
		'<p class="antradus-images-empty"%1$s>%2$s</p>',
		$items ? ' hidden' : '',
		esc_html__( 'No pictures yet - a labelled placeholder is shown on the site.', 'antradus' )
	);

	printf(
		'<input type="hidden" class="antradus-images-value" id="%1$s" name="%2$s" value="%3$s" data-max="%4$d">',
		esc_attr( $id ),
		esc_attr( $name ),
		esc_attr( implode( ',', $items ) ),
		(int) ANTRADUS_MAX_SLIDES
	);

	echo '<p class="antradus-image-actions">';
	echo '<button type="button" class="button antradus-images-pick">' . esc_html__( 'Add pictures', 'antradus' ) . '</button> ';
	echo '<button type="button" class="button-link antradus-images-clear">' . esc_html__( 'Remove all', 'antradus' ) . '</button>';
	echo '</p>';
	echo '</div>';
}

/**
 * Put a page's sections in the order the editor wants them.
 *
 * A list of every section with a pair of arrows, which is the same control the
 * repeater rows already use - an editor who has reordered a feature group knows
 * how to reorder a section. The stored value is a comma-separated list of keys.
 *
 * The list is rebuilt from antradus_audience_sections_list() every time rather
 * than from what was saved, so a section added to the theme later turns up here
 * on its own. Order is structure, not words, so this never appears on a
 * translation tab.
 *
 * @param string $name   Field name attribute.
 * @param string $value  Stored comma-separated order.
 * @param array  $field  Field definition, including 'choices'.
 */
function antradus_render_order_field( $name, $value, $field ) {
	$choices = isset( $field['choices'] ) && is_array( $field['choices'] ) ? $field['choices'] : array();
	if ( ! $choices ) {
		return;
	}

	// The same resolution the front end uses, so the screen cannot show an
	// order the page would not actually render.
	$order = array();
	foreach ( array_filter( array_map( 'trim', explode( ',', $value ) ) ) as $key ) {
		if ( isset( $choices[ $key ] ) && ! in_array( $key, $order, true ) ) {
			$order[] = $key;
		}
	}
	foreach ( array_keys( $choices ) as $key ) {
		if ( ! in_array( $key, $order, true ) ) {
			$order[] = $key;
		}
	}

	echo '<ol class="antradus-order">';
	foreach ( $order as $position => $key ) {
		echo '<li class="antradus-order-row">';
		echo '<span class="antradus-order-num" aria-hidden="true">' . esc_html( (string) ( $position + 1 ) ) . '</span>';
		echo '<span class="antradus-order-name">' . esc_html( $choices[ $key ] ) . '</span>';
		printf(
			'<input type="hidden" class="antradus-order-key" name="%1$s[]" value="%2$s">',
			esc_attr( $name ),
			esc_attr( $key )
		);
		echo '<span class="antradus-order-tools">';
		echo '<button type="button" class="button-link antradus-order-up" title="' . esc_attr__( 'Move up', 'antradus' ) . '">&uarr;</button>';
		echo '<button type="button" class="button-link antradus-order-down" title="' . esc_attr__( 'Move down', 'antradus' ) . '">&darr;</button>';
		echo '</span>';
		echo '</li>';
	}
	echo '</ol>';
}

/* ===========================================================================
 * 3. Repeaters
 * ========================================================================= */

/**
 * A repeatable list of rows, plus the template the Add button clones.
 *
 * While translating, the row count is fixed: how many plans there are is a
 * decision, not a translation, and letting the two languages disagree about it
 * is how you get an Arabic pricing table with a plan the English one has never
 * heard of. So the Add and Remove controls only exist on the English tab.
 *
 * @param array  $field Field definition.
 * @param string $lang  Language being edited.
 */
function antradus_render_repeater( $field, $lang ) {
	$is_base = ( antradus_default_lang() === $lang );
	$key     = $field['key'];
	$one     = isset( $field['single'] ) ? $field['single'] : __( 'Item', 'antradus' );
	$wide    = ! empty( $field['wide'] ) ? ' antradus-rep--wide' : '';

	if ( $is_base ) {
		$rows = antradus_opt( $key, array() );
		$rows = is_array( $rows ) ? array_values( $rows ) : array();
	} else {
		// The structure comes from English; the words come from here.
		antradus_set_lang_context( antradus_default_lang() );
		$structure = antradus_opt( $key, array() );
		antradus_set_lang_context( $lang );
		$structure = is_array( $structure ) ? array_values( $structure ) : array();

		$stored = antradus_field_raw_value( $key, $lang );
		$stored = is_array( $stored ) ? array_values( $stored ) : array();

		$rows = array();
		foreach ( $structure as $index => $unused ) {
			$rows[] = isset( $stored[ $index ] ) && is_array( $stored[ $index ] ) ? $stored[ $index ] : array();
		}
	}

	printf(
		'<div class="antradus-rep%s%s" data-key="%s" data-single="%s">',
		esc_attr( $wide ),
		$is_base ? '' : ' antradus-rep--locked',
		esc_attr( $key ),
		esc_attr( $one )
	);

	echo '<div class="antradus-rep-rows">';
	if ( ! $rows ) {
		echo '<p class="antradus-rep-none">' . esc_html__( 'Nothing here yet.', 'antradus' ) . '</p>';
	}
	foreach ( $rows as $index => $row ) {
		antradus_render_repeater_row( $field, (int) $index, is_array( $row ) ? $row : array(), $lang );
	}
	echo '</div>';

	if ( $is_base ) {
		printf(
			'<p class="antradus-rep-add"><button type="button" class="button antradus-rep-add-btn">%s</button></p>',
			esc_html( sprintf( /* translators: %s: item name, e.g. "Plan". */ __( '+ Add %s', 'antradus' ), $one ) )
		);

		echo '<script type="text/html" class="antradus-rep-tpl">';
		antradus_render_repeater_row( $field, -1, array(), $lang );
		echo '</script>';
	}

	echo '</div>';
}

/**
 * One repeater row.
 *
 * Index -1 renders the template, where __i__ stands in for the real index.
 *
 * @param array  $field Field definition.
 * @param int    $index Row index, or -1 for the template.
 * @param array  $row   Saved values.
 * @param string $lang  Language being edited.
 */
function antradus_render_repeater_row( $field, $index, $row, $lang ) {
	$is_base = ( antradus_default_lang() === $lang );
	$idx     = ( -1 === $index ) ? '__i__' : (string) $index;
	$base    = antradus_option_name( $lang ) . '[' . $field['key'] . '][' . $idx . ']';
	$one     = isset( $field['single'] ) ? $field['single'] : __( 'Item', 'antradus' );

	$subs = $field['fields'];
	if ( ! $is_base ) {
		$subs = array_values( array_filter( $subs, 'antradus_field_is_translatable' ) );
	}

	// The row's title in the collapsed header: its own first words, or - while
	// translating an untouched row - the English ones, so the list is readable
	// before any of it has been translated.
	$title = '';
	foreach ( $subs as $sub ) {
		if ( in_array( $sub['type'], array( 'text', 'textarea' ), true ) && ! empty( $row[ $sub['key'] ] ) ) {
			$title = wp_trim_words( (string) $row[ $sub['key'] ], 8, '...' );
			break;
		}
	}
	if ( '' === $title && ! $is_base && $index >= 0 ) {
		antradus_set_lang_context( antradus_default_lang() );
		$source = antradus_row_at( $field['key'], $index );
		antradus_set_lang_context( $lang );
		foreach ( $subs as $sub ) {
			if ( ! empty( $source[ $sub['key'] ] ) && is_string( $source[ $sub['key'] ] ) ) {
				$title = wp_trim_words( $source[ $sub['key'] ], 8, '...' );
				break;
			}
		}
	}

	echo '<div class="antradus-rep-row">';
	echo '<div class="antradus-rep-head">';
	echo '<button type="button" class="antradus-rep-toggle" aria-expanded="false">';
	echo '<span class="antradus-rep-handle" aria-hidden="true"></span>';
	echo '<span class="antradus-rep-name">' . esc_html( $title ? $title : $one ) . '</span>';
	echo '</button>';
	if ( $is_base ) {
		echo '<span class="antradus-rep-tools">';
		echo '<button type="button" class="button-link antradus-rep-up" title="' . esc_attr__( 'Move up', 'antradus' ) . '">&uarr;</button>';
		echo '<button type="button" class="button-link antradus-rep-down" title="' . esc_attr__( 'Move down', 'antradus' ) . '">&darr;</button>';
		echo '<button type="button" class="button-link antradus-rep-drop" title="' . esc_attr__( 'Remove', 'antradus' ) . '">&times;</button>';
		echo '</span>';
	}
	echo '</div>';

	echo '<div class="antradus-rep-body">';
	foreach ( $subs as $sub ) {
		$sub_name = $base . '[' . $sub['key'] . ']';
		$sub_val  = isset( $row[ $sub['key'] ] ) ? $row[ $sub['key'] ] : '';
		$sub_id   = 'ant-r-' . $field['key'] . '-' . $idx . '-' . $sub['key'];

		echo '<div class="antradus-rep-field antradus-rep-field--' . esc_attr( $sub['type'] ) . '">';
		echo '<label for="' . esc_attr( $sub_id ) . '">' . esc_html( $sub['label'] ) . '</label>';

		switch ( $sub['type'] ) {
			case 'textarea':
			case 'code':
				printf(
					'<textarea id="%1$s" name="%2$s" rows="%3$d" class="%4$s"%5$s>%6$s</textarea>',
					esc_attr( $sub_id ),
					esc_attr( $sub_name ),
					isset( $sub['rows'] ) ? (int) $sub['rows'] : 3,
					'code' === $sub['type'] ? 'antradus-code' : '',
					'code' === $sub['type'] ? ' spellcheck="false"' : '',
					esc_textarea( is_string( $sub_val ) ? $sub_val : '' )
				);
				break;

			case 'select':
				printf( '<select id="%1$s" name="%2$s">', esc_attr( $sub_id ), esc_attr( $sub_name ) );
				foreach ( (array) $sub['options'] as $opt_val => $opt_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $opt_val ),
						selected( (string) $opt_val, (string) $sub_val, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'checkbox':
				printf(
					'<label class="antradus-check"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> <span>%4$s</span></label>',
					esc_attr( $sub_id ),
					esc_attr( $sub_name ),
					checked( '1', (string) $sub_val, false ),
					esc_html( isset( $sub['cbtxt'] ) ? $sub['cbtxt'] : __( 'Yes', 'antradus' ) )
				);
				break;

			case 'image':
				antradus_render_image_field( $sub_id, $sub_name, (string) $sub_val );
				break;

			default:
				printf(
					'<input type="text" id="%1$s" name="%2$s" value="%3$s">',
					esc_attr( $sub_id ),
					esc_attr( $sub_name ),
					esc_attr( is_string( $sub_val ) ? $sub_val : '' )
				);
		}

		if ( $index >= 0 ) {
			antradus_render_source_hint( $field['key'], $sub['key'], $lang, $index );
		}

		if ( ! empty( $sub['help'] ) && $is_base ) {
			echo '<p class="description">' . wp_kses( $sub['help'], antradus_admin_kses() ) . '</p>';
		}
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
}

/* ===========================================================================
 * 4. Sanitizing
 * ========================================================================= */

/**
 * Sanitize the main option.
 *
 * Only fields that were actually on screen are replaced. Everything else keeps
 * whatever it already had, so saving the Pricing tab cannot empty the Home tab.
 *
 * @param mixed $input Raw posted value.
 * @return array
 */
function antradus_sanitize_options( $input ) {
	return antradus_sanitize_into( ANTRADUS_OPTION, $input, false );
}

/**
 * Sanitize a translation option: words only.
 *
 * @param mixed $input Raw posted value.
 * @return array
 */
function antradus_sanitize_translation( $input ) {
	$lang = antradus_settings_editing_lang();
	if ( antradus_default_lang() === $lang ) {
		// A translation row was posted while the English tab was open. Nothing
		// legitimate does that, so change nothing.
		$existing = get_option( antradus_option_name( 'ar' ), array() );
		return is_array( $existing ) ? $existing : array();
	}
	return antradus_sanitize_into( antradus_option_name( $lang ), $input, true );
}

/**
 * The shared body of both sanitizers.
 *
 * @param string $option     Option name being written.
 * @param mixed  $input      Raw posted value.
 * @param bool   $words_only Drop anything that is not translatable.
 * @return array
 */
function antradus_sanitize_into( $option, $input, $words_only ) {
	$existing = get_option( $option, array() );
	$existing = is_array( $existing ) ? $existing : array();
	$input    = is_array( $input ) ? $input : array();

	$present = array();
	if ( isset( $input['__present'] ) ) {
		$present = array_filter( array_map( 'sanitize_key', explode( ',', (string) $input['__present'] ) ) );
		unset( $input['__present'] );
	}

	// No declaration of what was on screen means a programmatic save: take the
	// keys that were actually sent and leave the rest alone.
	if ( ! $present ) {
		$present = array_keys( $input );
	}

	$schema = antradus_schema_fields();
	$out    = $existing;

	foreach ( $present as $key ) {
		if ( ! isset( $schema[ $key ] ) ) {
			continue;
		}
		$field = $schema[ $key ];
		if ( $words_only && ! antradus_field_is_translatable( $field ) ) {
			continue;
		}
		$raw         = isset( $input[ $key ] ) ? $input[ $key ] : null;
		$out[ $key ] = antradus_sanitize_value( $field, $raw, $words_only );
	}

	// Drop anything that is no longer a known field, so the row cannot rot.
	foreach ( array_keys( $out ) as $key ) {
		if ( ! isset( $schema[ $key ] ) || ( $words_only && ! antradus_field_is_translatable( $schema[ $key ] ) ) ) {
			unset( $out[ $key ] );
		}
	}

	return $out;
}

/**
 * Sanitize one value according to its field type.
 *
 * @param array $field      Field definition.
 * @param mixed $raw        Posted value.
 * @param bool  $words_only Inside a translation save.
 * @return mixed
 */
function antradus_sanitize_value( $field, $raw, $words_only = false ) {
	$type = isset( $field['type'] ) ? $field['type'] : 'text';

	switch ( $type ) {
		case 'checkbox':
			return ( '1' === (string) $raw ) ? '1' : '';

		case 'textarea':
			return sanitize_textarea_field( (string) $raw );

		case 'code':
			/*
			 * A pasted third-party snippet, kept as typed so the person who
			 * pasted it recognises it when they come back. It is never printed
			 * to a page and never executed: the theme reads four values out of
			 * it with a regular expression and writes its own markup from
			 * those. In wp-admin it only ever appears inside a textarea, via
			 * esc_textarea(). So the safe handling here is to strip the control
			 * characters that could break out of an attribute and to cap the
			 * length, not to strip the tags that make it readable.
			 */
			$raw = (string) wp_check_invalid_utf8( (string) $raw );
			$raw = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $raw );
			return (string) mb_substr( (string) $raw, 0, 8000 );

		case 'select':
			$raw = (string) $raw;
			return isset( $field['options'][ $raw ] ) ? $raw : (string) key( $field['options'] );

		case 'color':
			$hex = sanitize_hex_color( (string) $raw );
			return $hex ? $hex : '';

		case 'number':
			return (string) max( 1, min( 100, (int) $raw ) );

		case 'image':
			$raw = trim( (string) $raw );
			if ( '' === $raw ) {
				return '';
			}
			return ctype_digit( $raw ) ? (string) absint( $raw ) : esc_url_raw( $raw );

		case 'images':
			/*
			 * Stored as one comma-separated list, which is also why a comma can
			 * never be part of a value: it is the separator. Every entry is
			 * sanitized exactly as a single image would be, the same picture
			 * twice is a mistake rather than a slide, and the count is capped -
			 * a slider is a handful of pictures, and a posted list of a thousand
			 * is not an editor's doing.
			 */
			$items = is_array( $raw ) ? $raw : explode( ',', (string) $raw );
			$out   = array();
			foreach ( $items as $one ) {
				$one = trim( (string) $one );
				if ( '' === $one ) {
					continue;
				}
				$one = ctype_digit( $one ) ? (string) absint( $one ) : esc_url_raw( str_replace( ',', '', $one ) );
				if ( '' === $one || in_array( $one, $out, true ) ) {
					continue;
				}
				$out[] = $one;
				if ( count( $out ) >= ANTRADUS_MAX_SLIDES ) {
					break;
				}
			}
			return implode( ',', $out );

		case 'order':
			/*
			 * Arrives as the list of keys in the order they were left on
			 * screen. Only keys this field actually offers are kept, and any it
			 * offers that did not arrive are appended - so a posted order can
			 * never drop a section off the page or smuggle one in.
			 */
			$choices = isset( $field['choices'] ) && is_array( $field['choices'] ) ? $field['choices'] : array();
			$posted  = is_array( $raw ) ? $raw : explode( ',', (string) $raw );
			$out     = array();
			foreach ( $posted as $key ) {
				$key = sanitize_key( (string) $key );
				if ( isset( $choices[ $key ] ) && ! in_array( $key, $out, true ) ) {
					$out[] = $key;
				}
			}
			foreach ( array_keys( $choices ) as $key ) {
				if ( ! in_array( $key, $out, true ) ) {
					$out[] = $key;
				}
			}
			return implode( ',', $out );

		case 'repeater':
			$rows = is_array( $raw ) ? $raw : array();
			$out  = array();
			foreach ( $rows as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$clean = array();
				foreach ( $field['fields'] as $sub ) {
					if ( $words_only && ! antradus_field_is_translatable( $sub ) ) {
						continue;
					}
					$val                  = isset( $row[ $sub['key'] ] ) ? $row[ $sub['key'] ] : null;
					$clean[ $sub['key'] ] = antradus_sanitize_value( $sub, $val, $words_only );
				}
				$out[] = $clean;
			}
			return array_values( $out );

		default:
			// Plain text, but shortcodes and simple targets must survive intact.
			return sanitize_text_field( (string) $raw );
	}
}

/* ===========================================================================
 * 5. The hand-written panels
 * ========================================================================= */

/**
 * Page status table, with a button that creates whatever is missing.
 */
function antradus_render_pages_panel() {
	$pages = antradus_pages();
	echo '<table class="widefat striped antradus-pages"><thead><tr>';
	echo '<th>' . esc_html__( 'Page', 'antradus' ) . '</th>';
	echo '<th>' . esc_html__( 'Status', 'antradus' ) . '</th>';
	echo '<th>' . esc_html__( 'Shown on the site', 'antradus' ) . '</th>';
	echo '<th>' . esc_html__( 'Edit', 'antradus' ) . '</th>';
	echo '</tr></thead><tbody>';

	$missing = 0;
	foreach ( $pages as $key => $def ) {
		$page = antradus_page_object( $key );
		$live = antradus_page_is_live( $key );

		if ( $page ) {
			$status = get_post_status_object( $page->post_status );
			$label  = $status ? $status->label : $page->post_status;
		} else {
			$label = __( 'Not created', 'antradus' );
			++$missing;
		}

		echo '<tr>';
		echo '<td><strong>' . esc_html( $def['label'] ) . '</strong><br><code>/' . esc_html( antradus_opt( 'slug_' . $key, $def['slug'] ) ) . '/</code></td>';
		echo '<td>' . esc_html( $label ) . '</td>';
		echo '<td>';
		if ( $live ) {
			echo '<span class="antradus-pill antradus-pill--on">' . esc_html__( 'Linked everywhere', 'antradus' ) . '</span>';
		} else {
			echo '<span class="antradus-pill antradus-pill--off">' . esc_html__( 'Hidden', 'antradus' ) . '</span>';
		}
		echo '</td>';
		echo '<td>';
		if ( $page ) {
			printf(
				'<a href="%s">%s</a> &middot; <a href="%s" target="_blank" rel="noopener">%s</a>',
				esc_url( (string) get_edit_post_link( $page->ID ) ),
				esc_html__( 'Edit', 'antradus' ),
				esc_url( (string) get_permalink( $page->ID ) ),
				esc_html__( 'View', 'antradus' )
			);
		} else {
			echo '&mdash;';
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';

	echo '<p class="antradus-card-blurb">';
	esc_html_e( 'A page marked Hidden is not broken - it is simply a draft, so the theme leaves it out of the menu, the footer and any button aiming at it. Publish it and every one of those links comes back on its own.', 'antradus' );
	echo '</p>';

	if ( $missing ) {
		/*
		 * A link, not a form. This panel is printed inside the settings form,
		 * and HTML has no nested forms - the browser throws the inner tags away
		 * and the button ends up submitting the OUTER form to options.php,
		 * which is how "create the missing pages" used to land people on the
		 * raw All Settings screen. admin-post.php answers GET as happily as
		 * POST, and the nonce still has to be right.
		 */
		printf(
			'<p class="antradus-inline-form"><a class="button button-secondary" href="%1$s">%2$s</a> <span class="description">%3$s</span></p>',
			esc_url(
				wp_nonce_url(
					add_query_arg( 'action', 'antradus_make_pages', admin_url( 'admin-post.php' ) ),
					'antradus_make_pages'
				)
			),
			esc_html(
				sprintf(
					/* translators: %d: number of pages. */
					_n( 'Create the %d missing page as a draft', 'Create the %d missing pages as drafts', $missing, 'antradus' ),
					$missing
				)
			),
			esc_html__( 'They are created as drafts, so nothing appears on the site until you publish it.', 'antradus' )
		);
	}
}

/**
 * Which languages are live, which are still being written.
 *
 * Printed above the switch rather than below it, because the question people
 * arrive with is "what is the site doing right now" and the tick box only
 * answers "what would I like it to do".
 */
function antradus_render_languages_panel() {
	echo '<table class="widefat striped antradus-pages"><thead><tr>';
	echo '<th>' . esc_html__( 'Language', 'antradus' ) . '</th>';
	echo '<th>' . esc_html__( 'Visitors see it', 'antradus' ) . '</th>';
	echo '<th>' . esc_html__( 'Words written', 'antradus' ) . '</th>';
	echo '<th>' . esc_html__( 'Look at it', 'antradus' ) . '</th>';
	echo '</tr></thead><tbody>';

	foreach ( antradus_languages() as $code => $def ) {
		$live     = antradus_lang_is_published( $code );
		$progress = antradus_translation_progress( $code );
		$is_base  = ( antradus_default_lang() === $code );

		echo '<tr>';
		printf(
			'<td><strong lang="%1$s">%2$s</strong><br><code>%3$s</code></td>',
			esc_attr( $code ),
			esc_html( $def['native'] ),
			esc_html( $is_base ? home_url( '/' ) : '?lang=' . $code )
		);

		echo '<td>';
		if ( $live ) {
			echo '<span class="antradus-pill antradus-pill--on">' . esc_html__( 'Published', 'antradus' ) . '</span>';
		} else {
			echo '<span class="antradus-pill antradus-pill--off">' . esc_html__( 'Switched off', 'antradus' ) . '</span>';
		}
		echo '</td>';

		echo '<td>';
		if ( $is_base ) {
			echo '&mdash;';
		} else {
			printf(
				'%s / %s',
				esc_html( number_format_i18n( $progress['done'] ) ),
				esc_html( number_format_i18n( $progress['total'] ) )
			);
		}
		echo '</td>';

		printf(
			'<td><a href="%1$s" target="_blank" rel="noopener">%2$s</a></td>',
			esc_url( antradus_localize_url( home_url( '/' ), $code ) ),
			esc_html( $live ? __( 'View', 'antradus' ) : __( 'Preview', 'antradus' ) )
		);
		echo '</tr>';
	}
	echo '</tbody></table>';

	echo '<p class="antradus-card-blurb">';
	esc_html_e( 'Switched off does not mean deleted. The Arabic tab and every word on it stay exactly where they are, and while you are signed in you can still walk the whole site in Arabic - a strip across the top says so, so you never mistake your own preview for what a visitor gets. Everybody else who asks for an Arabic address is sent to the English one.', 'antradus' );
	echo '</p>';
}

/**
 * The search metadata, and the button that hands it to Rank Math.
 *
 * This panel is printed inside the settings form - the tab has real fields -
 * so the two actions are nonced links to admin-post.php rather than a form of
 * their own. HTML has no nested forms and the browser silently drops the inner
 * tags, which would make both buttons save the settings instead.
 *
 * Two links rather than one button and a tick box, for the same reason: a
 * checkbox here would be posted to options.php by the Save button and stored
 * as a setting nobody asked for. Two links also make the difference between
 * the two outcomes impossible to miss.
 */
function antradus_render_seo_panel() {
	$has_rank_math = antradus_seo_rank_math_active();

	if ( $has_rank_math ) {
		echo '<p class="antradus-card-blurb">';
		printf(
			/* translators: %s: Rank Math version. */
			esc_html__( 'Rank Math %s is running. The values below are written into its own fields - focus keyword, SEO title and SEO description - so its analysis picks them up and its preview shows them. Nothing is stored twice.', 'antradus' ),
			esc_html( defined( 'RANK_MATH_VERSION' ) ? RANK_MATH_VERSION : '' )
		);
		echo '</p>';
	} else {
		echo '<div class="notice notice-warning inline"><p>';
		esc_html_e( 'Rank Math is not active. The button still works - the values are written onto the pages as the meta Rank Math reads, so activating it later picks them all up at once. In the meantime the theme prints the description and the sharing tags itself, so no page goes out without them.', 'antradus' );
		echo '</p></div>';
	}

	// What pressing it would do, before anybody presses it.
	$ready   = 0;
	$kept    = 0;
	$missing = array();
	$pages   = antradus_pages();

	echo '<table class="widefat striped antradus-pages"><thead><tr>';
	echo '<th>' . esc_html__( 'Page', 'antradus' ) . '</th>';
	echo '<th>' . esc_html__( 'Focus keyword', 'antradus' ) . '</th>';
	echo '<th>' . esc_html__( 'In Rank Math', 'antradus' ) . '</th>';
	echo '</tr></thead><tbody>';

	foreach ( $pages as $key => $def ) {
		$plan  = antradus_seo_plan_page( $key, false );
		$focus = antradus_seo_value( $key, 'focus', antradus_default_lang() );

		switch ( $plan['status'] ) {
			case 'nopage':
				$missing[] = $def['label'];
				$pill      = '<span class="antradus-pill antradus-pill--off">' . esc_html__( 'No page yet', 'antradus' ) . '</span>';
				break;
			case 'write':
				++$ready;
				$pill = '<span class="antradus-pill antradus-pill--on">' . esc_html__( 'Ready to send', 'antradus' ) . '</span>';
				break;
			case 'kept':
				++$kept;
				$pill = '<span class="antradus-pill antradus-pill--off">' . esc_html__( 'You have your own', 'antradus' ) . '</span>';
				break;
			default:
				$pill = '<span class="antradus-pill antradus-pill--on">' . esc_html__( 'Already matches', 'antradus' ) . '</span>';
		}

		echo '<tr>';
		echo '<td><strong>' . esc_html( $def['label'] ) . '</strong></td>';
		echo '<td>' . ( '' !== $focus ? '<code>' . esc_html( $focus ) . '</code>' : '&mdash;' ) . '</td>';
		echo '<td>' . wp_kses( $pill, array( 'span' => array( 'class' => array() ) ) ) . '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';

	$link = static function ( $overwrite ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action'    => 'antradus_seo_push',
					'overwrite' => $overwrite ? '1' : '0',
				),
				admin_url( 'admin-post.php' )
			),
			'antradus_seo_push'
		);
	};

	echo '<p class="antradus-inline-form">';
	printf(
		'<a class="button button-primary" href="%1$s">%2$s</a> ',
		esc_url( $link( false ) ),
		esc_html__( 'Send these to Rank Math', 'antradus' )
	);
	printf(
		'<a class="button" href="%1$s" onclick="return confirm(%2$s);">%3$s</a>',
		esc_url( $link( true ) ),
		esc_attr( (string) wp_json_encode( __( 'This replaces the focus keyword, title and description already saved in Rank Math on all ten pages. Continue?', 'antradus' ) ) ),
		esc_html__( 'Replace what is already there', 'antradus' )
	);
	echo '</p>';

	echo '<p class="description">';
	esc_html_e( 'The first button only fills fields Rank Math has left empty, so anything you have already written there survives. The second overwrites all ten pages with the wording on this tab. A page has to exist before it can be given metadata - create the missing ones on the Pages tab first.', 'antradus' );
	echo '</p>';

	if ( $missing ) {
		echo '<p class="description"><strong>' . esc_html__( 'Skipped, because the page does not exist yet:', 'antradus' ) . '</strong> ';
		echo esc_html( implode( ', ', $missing ) );
		echo '</p>';
	}
	if ( $kept ) {
		echo '<p class="description">';
		printf(
			/* translators: %d: number of pages. */
			esc_html( _n( '%d page already has wording of its own in Rank Math. The first button will leave it alone.', '%d pages already have wording of their own in Rank Math. The first button will leave them alone.', $kept, 'antradus' ) ),
			(int) $kept
		);
		echo '</p>';
	}

	echo '<h3>' . esc_html__( 'The Arabic set', 'antradus' ) . '</h3>';
	echo '<p class="description">';
	esc_html_e( 'Both languages live at the same page - /pricing/ and /pricing/?lang=ar - and Rank Math stores one title and one description per page, so there is nowhere to put a second language. The Arabic tab here holds it instead, and the theme serves it on Arabic pages through Rank Math\'s own filters. It also fixes the canonical, which would otherwise point every Arabic page at its English twin and ask Google to ignore the translation. None of it does anything while Arabic is switched off.', 'antradus' );
	echo '</p>';
}

/**
 * Export, import, restore and reset.
 */
function antradus_render_tools_panel() {
	$json = wp_json_encode( antradus_export_payload( false ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	echo '<h3>' . esc_html__( 'Move this site to another one', 'antradus' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Download the file here, upload it on the other site, done. It carries both languages, every plan, and - if you leave the box ticked - the images themselves, so it works from a laptop the live server cannot reach.', 'antradus' ) . '</p>';

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="antradus-inline-form">';
	wp_nonce_field( 'antradus_export' );
	echo '<input type="hidden" name="action" value="antradus_export">';
	echo '<p><label class="antradus-check"><input type="checkbox" name="with_media" value="1" checked> <span>'
		. esc_html__( 'Include the images in the file', 'antradus' ) . '</span></label></p>';
	echo '<p><button type="submit" class="button button-primary">' . esc_html__( 'Download the export file', 'antradus' ) . '</button> ';
	echo '<span class="description">' . esc_html__( 'Untick the box for a small, text-only file - the other site will then link to this one for its pictures.', 'antradus' ) . '</span></p>';
	echo '</form>';

	echo '<h3>' . esc_html__( 'Import', 'antradus' ) . '</h3>';
	echo '<form method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'antradus_import' );
	echo '<input type="hidden" name="action" value="antradus_import">';
	echo '<p><input type="file" name="payload_file" accept=".json,application/json"></p>';
	echo '<p class="description">' . esc_html__( 'Or paste an export instead:', 'antradus' ) . '</p>';
	echo '<textarea name="payload" class="large-text code" rows="5" placeholder="' . esc_attr__( 'Paste an export here', 'antradus' ) . '"></textarea>';
	echo '<p><button type="submit" class="button">' . esc_html__( 'Import', 'antradus' ) . '</button> ';
	echo '<span class="description">' . esc_html__( 'This replaces every setting. The current wording is snapshotted first, so Undo below can bring it back. Images arrive in your Media Library; importing the same file twice reuses them rather than making copies.', 'antradus' ) . '</span></p>';
	echo '</form>';

	echo '<h3>' . esc_html__( 'Copy the wording only', 'antradus' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'The same content without the images, for keeping a quick backup before a big edit.', 'antradus' ) . '</p>';
	printf( '<textarea class="large-text code" rows="6" readonly onclick="this.select()">%s</textarea>', esc_textarea( (string) $json ) );

	$backup = get_option( ANTRADUS_OPTION . '_backup', array() );
	echo '<h3>' . esc_html__( 'Undo', 'antradus' ) . '</h3>';
	if ( is_array( $backup ) && ! empty( $backup['content'] ) ) {
		echo '<p class="description">';
		printf(
			/* translators: %s: date and time. */
			esc_html__( 'A snapshot was taken before the last import or reset, on %s.', 'antradus' ),
			esc_html( isset( $backup['saved'] ) ? (string) $backup['saved'] : '' )
		);
		echo '</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'antradus_restore' );
		echo '<input type="hidden" name="action" value="antradus_restore">';
		echo '<p><button type="submit" class="button">' . esc_html__( 'Restore that snapshot', 'antradus' ) . '</button></p>';
		echo '</form>';
	} else {
		echo '<p class="description">' . esc_html__( 'Nothing to undo yet. A snapshot is taken automatically before an import or a reset.', 'antradus' ) . '</p>';
	}

	echo '<h3>' . esc_html__( 'Bring back the shipped wording for one page', 'antradus' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'A theme update ships new wording, but it cannot overwrite what you have already saved - your words win, which is what you want almost every time. The exception is when the shipped content changes because the product did: new plans, new prices, a new feature list. This puts one page back to what the current version ships, in both languages, and leaves every other page alone.', 'antradus' ) . '</p>';
	echo '<p class="description">' . esc_html__( 'It restores the words. Your pictures, your Freemius details, your form shortcodes and your page addresses are not touched.', 'antradus' ) . '</p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="antradus-inline-form" onsubmit="return confirm(' . esc_attr( (string) wp_json_encode( __( 'This replaces your wording on that page with the wording this version ships. Undo above can put it back. Continue?', 'antradus' ) ) ) . ');">';
	wp_nonce_field( 'antradus_restore_section' );
	echo '<input type="hidden" name="action" value="antradus_restore_section">';
	echo '<p><select name="section">';
	foreach ( antradus_content_tabs() as $key => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $key ),
			selected( 'pricing', $key, false ),
			esc_html( $label )
		);
	}
	echo '</select> ';
	echo '<button type="submit" class="button">' . esc_html__( 'Restore that page', 'antradus' ) . '</button></p>';
	echo '<p class="description">' . esc_html__( 'A snapshot is taken first, so Undo above puts your version back.', 'antradus' ) . '</p>';
	echo '</form>';

	echo '<h3>' . esc_html__( 'Start again', 'antradus' ) . '</h3>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" onsubmit="return confirm(' . esc_attr( (string) wp_json_encode( __( 'This deletes every change you have made to the wording and images. Continue?', 'antradus' ) ) ) . ');">';
	wp_nonce_field( 'antradus_reset' );
	echo '<input type="hidden" name="action" value="antradus_reset">';
	echo '<p><button type="submit" class="button button-link-delete">' . esc_html__( 'Reset everything to the shipped content', 'antradus' ) . '</button></p>';
	echo '</form>';
}

/**
 * What a theme update does and does not touch.
 */
function antradus_render_safety_panel() {
	echo '<p class="antradus-card-blurb">';
	esc_html_e( 'Your wording, your images and your plans are stored in the WordPress database, not in the theme folder. Uploading a new version of the theme replaces the folder and nothing else, so none of it is touched by an update - there is nothing to back up first and nothing to re-enter afterwards.', 'antradus' );
	echo '</p>';
	echo '<p class="antradus-card-blurb">';
	esc_html_e( 'Images are the same story: the media picker stores the ID of a file in your Media Library, and the Media Library is not part of the theme either. The only two things that can clear your content are the Import and Reset buttons on this tab, and both take a snapshot first.', 'antradus' );
	echo '</p>';

	$rows = array(
		__( 'Wording, in both languages', 'antradus' )      => __( 'Kept - stored in the database', 'antradus' ),
		__( 'Images and logos', 'antradus' )                => __( 'Kept - stored in the Media Library', 'antradus' ),
		__( 'Pricing plans and Freemius setup', 'antradus' ) => __( 'Kept - stored in the database', 'antradus' ),
		__( 'Pages, posts and documentation', 'antradus' )  => __( 'Kept - they are WordPress content', 'antradus' ),
		__( 'The theme files themselves', 'antradus' )      => __( 'Replaced - that is the update', 'antradus' ),
	);
	echo '<table class="widefat striped antradus-pages"><tbody>';
	foreach ( $rows as $what => $happens ) {
		echo '<tr><td><strong>' . esc_html( $what ) . '</strong></td><td>' . esc_html( $happens ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

/* ===========================================================================
 * 6. The admin-post actions
 * ========================================================================= */

/**
 * Snapshot both languages before something destructive.
 */
function antradus_snapshot() {
	$content = array();
	foreach ( antradus_languages() as $code => $unused ) {
		$content[ $code ] = get_option( antradus_option_name( $code ), array() );
	}
	update_option(
		ANTRADUS_OPTION . '_backup',
		array(
			'saved'   => current_time( 'mysql' ),
			'version' => ANTRADUS_VERSION,
			'content' => $content,
		),
		false
	);
}

/**
 * Where to send the browser after an action.
 *
 * @param string $tab  Tab key.
 * @param array  $args Extra query args.
 */
function antradus_settings_redirect( $tab, $args = array() ) {
	wp_safe_redirect(
		add_query_arg(
			array_merge(
				array(
					'page' => 'antradus-content',
					'tab'  => $tab,
				),
				$args
			),
			admin_url( 'themes.php' )
		)
	);
	exit;
}

add_action( 'admin_post_antradus_make_pages', 'antradus_handle_make_pages' );
/**
 * Create any of the seven pages that do not exist yet, as drafts.
 */
function antradus_handle_make_pages() {
	antradus_require_admin( 'antradus_make_pages' );

	$made = 0;
	foreach ( antradus_pages() as $key => $def ) {
		if ( antradus_page_object( $key ) ) {
			continue;
		}
		$slug = antradus_opt( 'slug_' . $key, $def['slug'] );
		$id   = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'draft',
				'post_title'   => $def['label'],
				'post_name'    => $slug,
				'post_content' => '',
				'meta_input'   => array( '_wp_page_template' => 'antradus-' . $key . '.php' ),
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			++$made;
		}
	}

	antradus_settings_redirect( 'pages', array( 'antradus_made' => $made ) );
}

add_action( 'admin_post_antradus_import', 'antradus_handle_import' );
/**
 * Replace every setting from an uploaded or pasted export.
 *
 * The reading, the media rebuilding and the writing all live in inc/transfer.php;
 * this is only the request half - check who is asking, do it, say what happened.
 */
function antradus_handle_import() {
	antradus_require_admin( 'antradus_import' );

	$data   = antradus_read_import_payload();
	$report = $data ? antradus_apply_import( $data ) : array( 'ok' => false );

	antradus_settings_redirect(
		'tools',
		array(
			'antradus_import' => $report['ok'] ? 1 : 0,
			'ant_created'     => isset( $report['created'] ) ? (int) $report['created'] : 0,
			'ant_reused'      => isset( $report['reused'] ) ? (int) $report['reused'] : 0,
			'ant_linked'      => isset( $report['linked'] ) ? (int) $report['linked'] : 0,
		)
	);
}

add_action( 'admin_post_antradus_restore', 'antradus_handle_restore' );
/**
 * Put the pre-import snapshot back.
 */
function antradus_handle_restore() {
	antradus_require_admin( 'antradus_restore' );

	$backup = get_option( ANTRADUS_OPTION . '_backup', array() );
	$ok     = 0;

	if ( is_array( $backup ) && ! empty( $backup['content'] ) && is_array( $backup['content'] ) ) {
		foreach ( $backup['content'] as $code => $values ) {
			if ( antradus_is_lang( $code ) && is_array( $values ) ) {
				update_option( antradus_option_name( $code ), $values );
				$ok = 1;
			}
		}
	}

	antradus_settings_redirect( 'tools', array( 'antradus_restored' => $ok ) );
}

add_action( 'admin_post_antradus_restore_section', 'antradus_handle_restore_section' );
/**
 * Drop one tab's saved keys so that tab falls back to the shipped content.
 *
 * Deleting the keys rather than writing the new defaults over them is the
 * point: a key that is absent inherits whatever the theme ships from now on,
 * so the next update reaches this page too, instead of freezing today's words
 * into the database a second time.
 */
function antradus_handle_restore_section() {
	antradus_require_admin( 'antradus_restore_section' );

	$section = isset( $_POST['section'] ) ? sanitize_key( wp_unslash( $_POST['section'] ) ) : '';
	$tabs    = antradus_content_tabs();
	if ( ! isset( $tabs[ $section ] ) ) {
		antradus_settings_redirect( 'tools', array( 'antradus_section' => 0 ) );
	}

	/*
	 * Words only. The tab also owns wiring - the Freemius product and key,
	 * image IDs, page slugs, link targets - and the shipped default for every
	 * one of those is either empty or ours, so restoring them would quietly
	 * delete the picture somebody picked or replace their checkout details
	 * with ours.
	 *
	 * The form shortcodes are the awkward case: they are words now, since a
	 * form is written in a language, but the shipped default for one is this
	 * site's own Forminator ID and restoring it would point somebody else's
	 * contact page at our form. They carry 'restore' => false for that reason,
	 * which antradus_field_is_restorable() reads on top of the translation
	 * rule - one question asked in one place, not a second list to keep.
	 */
	$schema  = antradus_schema_fields();
	$keys    = array();
	foreach ( antradus_tab_field_keys( $section ) as $key ) {
		if ( isset( $schema[ $key ] ) && antradus_field_is_restorable( $schema[ $key ] ) ) {
			$keys[] = $key;
		}
	}
	$removed = 0;

	antradus_snapshot();

	foreach ( antradus_languages() as $code => $unused ) {
		$name  = antradus_option_name( $code );
		$saved = get_option( $name, array() );
		if ( ! is_array( $saved ) ) {
			continue;
		}
		$before = count( $saved );
		foreach ( $keys as $key ) {
			unset( $saved[ $key ] );
		}
		if ( count( $saved ) !== $before ) {
			$removed += $before - count( $saved );
			update_option( $name, $saved );
		}
	}

	antradus_settings_redirect(
		'tools',
		array(
			'antradus_section' => 1,
			'antradus_fields'  => $removed,
		)
	);
}

add_action( 'admin_post_antradus_reset', 'antradus_handle_reset' );
/**
 * Delete both option rows so the shipped content comes back.
 */
function antradus_handle_reset() {
	antradus_require_admin( 'antradus_reset' );

	antradus_snapshot();
	foreach ( antradus_languages() as $code => $unused ) {
		delete_option( antradus_option_name( $code ) );
	}

	antradus_settings_redirect( 'tools', array( 'antradus_reset' => 1 ) );
}

add_action( 'admin_post_antradus_seo_push', 'antradus_handle_seo_push' );
/**
 * Copy the English search metadata into Rank Math.
 *
 * Deliberately not snapshotted. Nothing on this site is being changed - the
 * theme's own settings are untouched - and what it writes lands in Rank Math's
 * fields, where it is visible, editable and undoable by the person who owns
 * them. A snapshot of the theme options would restore nothing that this button
 * altered.
 */
function antradus_handle_seo_push() {
	antradus_require_admin( 'antradus_seo_push' );

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- checked above.
	$overwrite = isset( $_GET['overwrite'] ) && '1' === sanitize_key( wp_unslash( $_GET['overwrite'] ) );

	$report = antradus_seo_push_to_rank_math( $overwrite );

	antradus_settings_redirect(
		'seo',
		array(
			'antradus_seo'  => '1',
			'ant_seo_pages' => (int) $report['pages'],
			'ant_seo_flds'  => (int) $report['fields'],
			'ant_seo_kept'  => (int) $report['kept'],
			'ant_seo_gone'  => count( $report['missing'] ),
		)
	);
}

add_action( 'admin_notices', 'antradus_admin_notices' );
/**
 * Confirmations for the actions above.
 */
function antradus_admin_notices() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'appearance_page_antradus-content' !== $screen->id ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only confirmations after a redirect.
	if ( isset( $_GET['antradus_made'] ) ) {
		$made = (int) $_GET['antradus_made'];
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %d: number of pages created. */
					_n( '%d page created as a draft. Publish it when you are ready for it to appear.', '%d pages created as drafts. Publish them when you are ready for them to appear.', max( 1, $made ), 'antradus' ),
					$made
				)
			)
		);
	}
	if ( isset( $_GET['antradus_import'] ) ) {
		$ok = '1' === (string) $_GET['antradus_import'];

		if ( ! $ok ) {
			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				esc_html__( 'That did not look like an Antradus export, so nothing was changed.', 'antradus' )
			);
		} else {
			$created = isset( $_GET['ant_created'] ) ? (int) $_GET['ant_created'] : 0;
			$reused  = isset( $_GET['ant_reused'] ) ? (int) $_GET['ant_reused'] : 0;
			$linked  = isset( $_GET['ant_linked'] ) ? (int) $_GET['ant_linked'] : 0;

			$lines = array( __( 'Settings imported. The previous wording was snapshotted first - Undo is on this tab.', 'antradus' ) );

			if ( $created || $reused ) {
				$lines[] = sprintf(
					/* translators: 1: images added, 2: images already present. */
					__( 'Images: %1$d added to the Media Library, %2$d already here and reused.', 'antradus' ),
					$created,
					$reused
				);
			}
			// The one outcome worth calling out: a picture that is showing, but
			// from the other site, so it breaks the day that site goes away.
			if ( $linked ) {
				$lines[] = sprintf(
					/* translators: %d: number of images. */
					_n(
						'%d image could not be copied and is being loaded from the other site. Re-pick it here when you can.',
						'%d images could not be copied and are being loaded from the other site. Re-pick them here when you can.',
						$linked,
						'antradus'
					),
					$linked
				);
			}

			printf(
				'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
				$linked ? 'warning' : 'success',
				esc_html( implode( ' ', $lines ) )
			);
		}
	}
	if ( isset( $_GET['antradus_restored'] ) ) {
		$ok = '1' === (string) $_GET['antradus_restored'];
		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			$ok ? 'success' : 'error',
			esc_html(
				$ok
					? __( 'The snapshot is back.', 'antradus' )
					: __( 'There was no snapshot to restore.', 'antradus' )
			)
		);
	}
	if ( isset( $_GET['antradus_section'] ) ) {
		$ok     = '1' === (string) $_GET['antradus_section'];
		$fields = isset( $_GET['antradus_fields'] ) ? (int) $_GET['antradus_fields'] : 0;

		if ( ! $ok ) {
			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				esc_html__( 'That is not a page with wording in it, so nothing was changed.', 'antradus' )
			);
		} elseif ( ! $fields ) {
			printf(
				'<div class="notice notice-info is-dismissible"><p>%s</p></div>',
				esc_html__( 'That page had nothing saved over the shipped wording, so it was already showing it.', 'antradus' )
			);
		} else {
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: %d: number of fields restored. */
						_n(
							'%d field is back to the wording this version ships. Undo is on this tab if that was not what you meant.',
							'%d fields are back to the wording this version ships. Undo is on this tab if that was not what you meant.',
							$fields,
							'antradus'
						),
						$fields
					)
				)
			);
		}
	}
	if ( isset( $_GET['antradus_reset'] ) ) {
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html__( 'Everything is back to the shipped content. Undo is on this tab if that was not what you meant.', 'antradus' )
		);
	}
	if ( isset( $_GET['antradus_seo'] ) ) {
		$seo_pages  = isset( $_GET['ant_seo_pages'] ) ? (int) $_GET['ant_seo_pages'] : 0;
		$seo_fields = isset( $_GET['ant_seo_flds'] ) ? (int) $_GET['ant_seo_flds'] : 0;
		$seo_kept   = isset( $_GET['ant_seo_kept'] ) ? (int) $_GET['ant_seo_kept'] : 0;
		$seo_gone   = isset( $_GET['ant_seo_gone'] ) ? (int) $_GET['ant_seo_gone'] : 0;

		$lines = array();
		if ( $seo_fields ) {
			$lines[] = sprintf(
				/* translators: 1: number of fields, 2: number of pages. */
				__( '%1$d fields written into Rank Math, across %2$d pages.', 'antradus' ),
				$seo_fields,
				$seo_pages
			);
		} else {
			$lines[] = __( 'Nothing needed writing - Rank Math already holds these words.', 'antradus' );
		}
		if ( $seo_kept ) {
			$lines[] = sprintf(
				/* translators: %d: number of fields left alone. */
				__( '%d field you had already written was left alone. Use "Replace what is already there" if you want ours instead.', 'antradus' ),
				$seo_kept
			);
		}
		if ( $seo_gone ) {
			$lines[] = sprintf(
				/* translators: %d: number of pages. */
				__( '%d page does not exist yet and was skipped - create it on the Pages tab, then press this again.', 'antradus' ),
				$seo_gone
			);
		}

		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( implode( ' ', $lines ) )
		);
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
}
