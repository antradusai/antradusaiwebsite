<?php
/**
 * Antradus theme - the interface words, in Arabic.
 *
 * Everything an editor writes lives in the settings. This file is for the
 * other kind of string: the words the theme itself says - "Skip to content",
 * "More to read", "3 min read", the menu labels, the empty states. They are
 * not content, so they do not belong in a settings screen; but they are still
 * words a reader sees, so they still have to be in Arabic.
 *
 * The usual answer is a compiled .mo file. This is a PHP array instead, for
 * one reason: a .mo is a binary you cannot open, diff or edit - which makes it
 * exactly the wrong shape for a file whose whole job is to be corrected later.
 * Here, changing a word is changing a line.
 *
 * It is wired in through the `gettext` filter, scoped to this theme's own text
 * domain and to front-end Arabic pages, so it can never reach wp-admin, another
 * plugin's strings, or an English page.
 *
 * To add a string: put the exact English source text on the left. It has to
 * match the __() call character for character, including punctuation.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * English source text => Arabic.
 *
 * @return array<string,string>
 */
function antradus_strings_ar() {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}

	$map = array(

		/* ---- navigation and chrome ---- */
		'Home'                     => 'الرئيسية',
		'Publishers'               => 'أصحاب المواقع',
		'Studios'                  => 'أصحاب البودكاست',
		'For publishers'           => 'لأصحاب المواقع',
		'For studios'              => 'لأصحاب البودكاست',
		'Features'                 => 'المزايا',
		'Plugin features'          => 'مزايا الإضافة',
		'Pricing'                  => 'الأسعار',
		'Docs'                     => 'التوثيق',
		'Blog'                     => 'المدوّنة',
		'Contact'                  => 'تواصل معنا',
		'Newsletter'               => 'النشرة البريدية',
		'Welcome'                  => 'أهلاً بك',
		'Skip to content'          => 'تخطَّ إلى المحتوى',
		'Menu'                     => 'القائمة',
		'Main'                     => 'الرئيسية',
		'Language'                 => 'اللغة',
		'English'                  => 'الإنجليزية',
		'Arabic'                   => 'العربية',
		'Logo'                     => 'الشعار',

		/* ---- the compatibility diagram ---- */
		'Bring it in from'         => 'يقرأ من',
		'Send it out to'           => 'ينشر إلى',

		/* ---- pricing ---- */
		'Feature'                  => 'الميزة',
		'Included'                 => 'مشمولة',
		'Not included'             => 'غير مشمولة',
		'Link not set'             => 'لم يُضبط الرابط',
		'Compare every feature, plan by plan' => 'قارن كل ميزة، اشتراكاً باشتراك',
		'The checkout could not open here. Use the link to open it in a new tab instead.' => 'تعذّر فتح صفحة الدفع هنا. استخدم الرابط لفتحها في تبويب جديد.',

		/* ---- articles ---- */
		'All articles'             => 'كل المقالات',
		'More to read'             => 'اقرأ أيضاً',
		'Latest articles'          => 'أحدث المقالات',
		'In %s'                    => 'في %s',
		'Nothing else here yet.'   => 'لا توجد مقالات أخرى بعد.',
		'Browse articles'          => 'تصفّح المقالات',
		'Read the blog'            => 'اقرأ المدوّنة',
		'Search articles'          => 'ابحث في المقالات',
		'Search again'             => 'ابحث مرة أخرى',
		'Search the site'          => 'ابحث في الموقع',
		'Clear filters'            => 'امسح عوامل التصفية',
		'Nothing matches that yet.' => 'لا نتائج مطابقة بعد.',
		'No matches'               => 'لا نتائج',
		'Results for %s'           => 'نتائج البحث عن %s',
		'Try a shorter phrase, or browse the articles instead.' => 'جرّب عبارة أقصر، أو تصفّح المقالات.',
		'Archive'                  => 'الأرشيف',
		'Article pages'            => 'صفحات المقالات',
		'All'                      => 'الكل',

		/* ---- documentation ---- */
		'Search'                   => 'بحث',
		'Search the guides'        => 'ابحث في الأدلة',
		'Type to filter every guide' => 'اكتب للبحث في الأدلة',
		'Clear the search'         => 'امسح البحث',
		'No guide matches that.'   => 'لا يوجد دليل يطابق ذلك.',
		'More guides'              => 'أدلة أخرى',

		/* ---- galleries ---- */
		'Image gallery'            => 'معرض الصور',
		'Previous image'           => 'الصورة السابقة',
		'Next image'               => 'الصورة التالية',
		'Show image %d'            => 'اعرض الصورة %d',

		/* ---- empty states and placeholders ---- */
		'Nothing here yet'         => 'لا يوجد شيء هنا بعد',
		'Image placeholder: %s'    => 'صورة مؤقتة: %s',
		'Add this image in Antradus Content' => 'أضف هذه الصورة من إعدادات محتوى أنترادوس',
		'%s illustration'          => 'رسم توضيحي لـ %s',
		'No form is showing here. Either the shortcode in Antradus Content points at a form plugin that is not active, or there is no form on this page yet.' => 'لا يظهر أي نموذج هنا. إمّا أن الرمز المختصر في إعدادات محتوى أنترادوس يشير إلى إضافة نماذج غير مفعّلة، وإمّا أنه لا يوجد نموذج في هذه الصفحة بعد.',

		/* ---- 404 ---- */
		'That page is not here'    => 'هذه الصفحة غير موجودة',
		'It may have moved, or it may not be published yet. Either way, here is the way back.' => 'ربما نُقلت، أو لم تُنشر بعد. في الحالتين، هذا هو طريق العودة.',
		'Go to the home page'      => 'اذهب إلى الصفحة الرئيسية',
		'Talk to us'               => 'تحدّث إلينا',

		/* ---- comments, when they are switched back on ---- */
		'Leave a comment'          => 'اترك تعليقاً',
		'Comments are closed'      => 'التعليقات مغلقة',
		'Comments are closed on this one.' => 'التعليقات مغلقة على هذا المقال.',
		'Comments are closed on this site.' => 'التعليقات مغلقة على هذا الموقع.',

		/* ---- sign-in messages ---- */
		'Those details were not right. Please try again.' => 'بيانات الدخول غير صحيحة. حاول مرة أخرى.',
		'Too many failed sign-in attempts from this address. Try again in about fifteen minutes.' => 'تجاوزت عدد محاولات الدخول المسموح بها. حاول مرة أخرى بعد خمس عشرة دقيقة.',
	);

	return $map;
}

/**
 * Plural forms, for the handful of counted strings the theme prints.
 *
 * Arabic distinguishes one, two, a few (3-10) and many, where English has two
 * forms - so the singular/plural pair a __() call gives us is not enough to
 * pick from. Each entry is keyed by the English singular and carries all four.
 *
 * @return array<string,array<string,string>>
 */
function antradus_plurals_ar() {
	return array(
		'%d min read' => array(
			'one'  => 'دقيقة قراءة',
			'two'  => 'دقيقتا قراءة',
			'few'  => '%d دقائق قراءة',
			'many' => '%d دقيقة قراءة',
		),
		'%d article'  => array(
			'one'  => 'مقال واحد',
			'two'  => 'مقالان',
			'few'  => '%d مقالات',
			'many' => '%d مقالاً',
		),
		'%d match'    => array(
			'one'  => 'نتيجة واحدة',
			'two'  => 'نتيجتان',
			'few'  => '%d نتائج',
			'many' => '%d نتيجة',
		),
		'%d comment'  => array(
			'one'  => 'تعليق واحد',
			'two'  => 'تعليقان',
			'few'  => '%d تعليقات',
			'many' => '%d تعليقاً',
		),
		'%1$d result for &ldquo;%2$s&rdquo;' => array(
			'one'  => 'نتيجة واحدة عن &rdquo;%2$s&ldquo;',
			'two'  => 'نتيجتان عن &rdquo;%2$s&ldquo;',
			'few'  => '%1$d نتائج عن &rdquo;%2$s&ldquo;',
			'many' => '%1$d نتيجة عن &rdquo;%2$s&ldquo;',
		),
	);
}

/**
 * Which Arabic plural form a number takes.
 *
 * @param int $number Count.
 * @return string one|two|few|many
 */
function antradus_arabic_plural_form( $number ) {
	$number = (int) $number;
	if ( 1 === $number ) {
		return 'one';
	}
	if ( 2 === $number ) {
		return 'two';
	}
	$mod = $number % 100;
	if ( $mod >= 3 && $mod <= 10 ) {
		return 'few';
	}
	return 'many';
}

/**
 * Should this request use the Arabic interface words?
 *
 * The guard is not paranoia. Working out which language a page is in can
 * itself reach a __() call, and that __() would come straight back through
 * this filter - a loop with no bottom. While the question is being answered,
 * the answer is "no", which resolves that inner call to English and lets the
 * outer one finish normally.
 *
 * @return bool
 */
function antradus_use_arabic_strings() {
	static $asking = false;
	if ( $asking ) {
		return false;
	}
	$asking = true;
	$yes    = ! is_admin() && 'ar' === antradus_lang();
	$asking = false;
	return $yes;
}

add_filter( 'gettext', 'antradus_translate_string', 10, 3 );
/**
 * @param string $translation Current translation.
 * @param string $text        Source text.
 * @param string $domain      Text domain.
 * @return string
 */
function antradus_translate_string( $translation, $text, $domain ) {
	if ( 'antradus' !== $domain || ! antradus_use_arabic_strings() ) {
		return $translation;
	}
	$map = antradus_strings_ar();
	return isset( $map[ $text ] ) ? $map[ $text ] : $translation;
}

add_filter( 'ngettext', 'antradus_translate_plural', 10, 5 );
/**
 * @param string $translation Current translation.
 * @param string $single      Singular source.
 * @param string $plural      Plural source.
 * @param int    $number      Count.
 * @param string $domain      Text domain.
 * @return string
 */
function antradus_translate_plural( $translation, $single, $plural, $number, $domain ) {
	if ( 'antradus' !== $domain || ! antradus_use_arabic_strings() ) {
		return $translation;
	}
	$plurals = antradus_plurals_ar();
	if ( ! isset( $plurals[ $single ] ) ) {
		return $translation;
	}
	$form = antradus_arabic_plural_form( $number );
	return isset( $plurals[ $single ][ $form ] ) ? $plurals[ $single ][ $form ] : $translation;
}
