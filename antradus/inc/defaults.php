<?php
/**
 * Antradus theme - the shipped content.
 *
 * Every string, list and image slot on the site starts here, and every one of
 * them is editable from Antradus Content in wp-admin. Nothing in the templates
 * is hard-coded copy: if you want to change a word, change it here or, better,
 * change it in the settings screen.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values for every setting.
 *
 * @return array
 */
function antradus_default_options() {
	return array_merge(
		antradus_defaults_brand(),
		antradus_defaults_home(),
		antradus_defaults_publisher(),
		antradus_defaults_studio(),
		antradus_defaults_features(),
		antradus_defaults_pricing(),
		antradus_defaults_blog(),
		antradus_defaults_contact(),
		antradus_defaults_welcome(),
		antradus_defaults_docs(),
		antradus_defaults_footer(),
		antradus_defaults_security(),
		antradus_defaults_seo()
	);
}

/* ===========================================================================
 * Brand and global chrome
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_brand() {
	return array(
		'brand_name'        => 'Antradus AI',
		'brand_logo'        => '',
		'brand_accent'      => '#1f6feb',
		'brand_width'       => '1560',
		'nav_cta_label'     => 'Get started',
		'nav_cta_target'    => 'page:pricing',
		'nav_extra'         => '',
		'announce_show'     => '1',
		'announce_text'     => 'Antradus 2.7 is out - two plans now: Publisher for a website, Studio for a whole show.',
		'announce_link'     => 'page:blog',
		'announce_link_txt' => 'Read the release notes',

		/*
		 * Arabic ships switched OFF, which is the state a site is actually in
		 * on the day it launches: the English pages are finished and the
		 * translation is not. Off means readers never reach it - no language
		 * button, no hreflang, and ?lang=ar redirects - while the Arabic tab in
		 * wp-admin stays exactly where it was and an administrator can preview
		 * the whole site in Arabic on the real domain. One tick publishes it.
		 */
		'lang_ar_publish'   => '',

		// Slug overrides for the seven designed pages.
		'slug_home'         => 'home',
		'slug_publisher'    => 'for-publishers',
		'slug_studio'       => 'for-studios',
		'slug_features'     => 'plugin-features',
		'slug_pricing'      => 'pricing',
		'slug_docs'         => 'docs',
		'slug_blog'         => 'blog',
		'slug_contact'      => 'contact',
		'slug_welcome'      => 'welcome',
	);
}

/* ===========================================================================
 * Home
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_home() {
	return array(
		/* ---- hero ---- */
		'home_hero_badge'       => 'The first B2B podcast-to-article system for WordPress',
		// Named both sources on purpose. "One episode in" was the strongest
		// line on the page and also the reason somebody without a podcast
		// stopped reading it - which is half the people the site now sells to.
		'home_hero_title'       => "An episode, or a keyword.\n*A publishing week out.*",
		'home_hero_sub'         => 'Antradus AI writes for two kinds of team: publishers filling a website from keywords and clusters, and studios turning every episode into an article, show notes, quote cards and a newsletter. One desk, inside WordPress, on your own API keys.',
		'home_hero_cta1'        => 'See pricing',
		'home_hero_cta1_url'    => 'page:pricing',
		// Cleared: the hero carries one call to action. Fill this in and the
		// second button comes back - an empty label renders nothing at all.
		'home_hero_cta2'        => '',
		'home_hero_cta2_url'    => 'https://wordpress.org/plugins/antradus-ai-lite/',
		'home_hero_note'        => 'Bring your own API keys - no markup, no per-word fees, no monthly seat.',
		'home_hero_image'       => '',
		'home_hero_chips'       => "Episode to article\nShow notes and chapters\nQuote cards\nNewsletter draft\nSEO and GEO ready",
		'home_hero_proof'       => 'Built on the Antradus AI plugin - Classic and Block editors, five AI providers, your own API keys.',

		/*
		 * The fork, in the first screenful. A visitor who cannot tell whether
		 * a site is for them stops reading, and Antradus is sold to two people
		 * whose problems have nothing in common - a content calendar and a
		 * back catalogue. Two links, and each of them stops guessing.
		 */
		'home_hero_paths_label' => 'Which one are you?',
		'home_hero_paths'       => array(
			array(
				'icon'    => 'pen',
				'label'   => "I'm a publisher",
				'text'    => 'A website to fill',
				'cta_url' => 'page:publisher',
			),
			array(
				'icon'    => 'mic',
				'label'   => "I'm a studio",
				'text'    => 'A show to write up',
				'cta_url' => 'page:studio',
			),
		),

		/* ---- stats ---- */
		'home_stats'         => array(
			array(
				'value' => '1',
				'label' => 'Episode in',
			),
			array(
				'value' => '12+',
				'label' => 'Publishable assets out',
			),
			array(
				'value' => '5',
				'label' => 'AI providers, your keys',
			),
			array(
				'value' => '17',
				'label' => 'Languages',
			),
		),

		/* ---- publisher or studio ---- */
		'home_fork_eyebrow'  => 'Two ways in',
		'home_fork_title'    => 'One desk. *Two kinds of publisher.*',
		'home_fork_sub'      => 'The job is the same either way - a finished, human-sounding article inside WordPress. What differs is where the material comes from, and that is what decides your plan.',
		'home_fork_cards'    => array(
			array(
				'name'     => 'Publisher',
				'title'    => "I'm a publisher",
				'text'     => 'You run a website, and the content calendar is always further ahead than the writing.',
				'items'    => "Bulk publishing - 200 keywords or a CSV\nSEO clusters and automatic internal linking\nHuman Voice and the offline Human Check\nGEO structure, schema and AI-referral counting",
				'price'    => '$275 a month - up to 5 sites - 7 days free',
				'cta'      => 'See what Publisher does',
				'cta_url'  => 'page:publisher',
				'alt'      => 'Compare every plan',
				'alt_url'  => 'page:pricing',
			),
			array(
				'name'     => 'Studio',
				'title'    => "I'm a studio",
				'text'     => 'You run a podcast or a video show, and every episode should be a week of content instead of an invoice.',
				'items'    => "Episode to article, with every quote verified\nShow notes, timestamped chapters and quote cards\nNewsletter drafts and Instagram carousels\nEverything Publisher does, on top",
				'price'    => '$1,500 a month - starts with a demo',
				'cta'      => 'See what Studio does',
				'cta_url'  => 'page:studio',
				'alt'      => 'Compare every plan',
				'alt_url'  => 'page:pricing',
			),
		),
		'home_fork_note'     => 'Both run on the same plugin, in the same WordPress, on your own API keys. Lite is free either way.',

		/* ---- logo strip ---- */
		'home_logos_title'   => 'Built for the publishers, networks and agencies who ship on a schedule',
		'home_logos'         => array(
			array(
				'name'  => 'Your client logo',
				'image' => '',
			),
			array(
				'name'  => 'Your client logo',
				'image' => '',
			),
			array(
				'name'  => 'Your client logo',
				'image' => '',
			),
			array(
				'name'  => 'Your client logo',
				'image' => '',
			),
			array(
				'name'  => 'Your client logo',
				'image' => '',
			),
			array(
				'name'  => 'Your client logo',
				'image' => '',
			),
		),

		/* ---- product demo ---- */
		'home_demo_eyebrow'  => 'Product demo',
		'home_demo_title'    => 'See what Antradus *creates* from one episode',
		'home_demo_sub'      => 'Paste a YouTube or Spotify link. Everything below comes out of the same transcript, verified against what was actually said.',
		'home_demo_image'    => '',
		'home_demo_caption'  => 'Episode 127 - "The AI Inflection Point" - 58 min',
		'home_demo_cards'    => array(
			array(
				'icon'  => 'pen',
				'title' => 'The article',
				'text'  => 'A complete, sourced piece in your house style - not a transcript with paragraph breaks.',
			),
			array(
				'icon'  => 'quote',
				'title' => 'Verified quotes',
				'text'  => 'Every quotation is checked word-for-word against the recording before it is used.',
			),
			array(
				'icon'  => 'calendar',
				'title' => 'Show notes and chapters',
				'text'  => 'Timestamped chapters and a description ready to paste into your podcast host.',
			),
			array(
				'icon'  => 'image',
				'title' => 'Quote cards',
				'text'  => 'Drawn, not generated - so the words on the card are exactly the words that were said.',
			),
			array(
				'icon'  => 'mail',
				'title' => 'Newsletter draft',
				'text'  => 'A draft in Mailchimp, Kit or beehiiv. Nothing is ever sent for you.',
			),
			array(
				'icon'  => 'tag',
				'title' => 'SEO and GEO metadata',
				'text'  => 'Focus keyphrase, meta title and description, straight into Yoast or Rank Math.',
			),
		),

		/* ---- core features ---- */
		'home_feat_eyebrow'  => 'Core features',
		'home_feat_title'    => 'A publishing desk, *not another AI toy*',
		'home_feat_sub'      => 'The four things that decide whether AI content is worth publishing - and what Antradus does about each of them.',
		'home_feat_cards'    => array(
			array(
				'icon'  => 'brain',
				'title' => 'Human Voice',
				'text'  => 'A prompt layer plus an offline check that reads for the tells: flat sentence rhythm, hedging, passive voice, the phrases every model reaches for. It reports them before your readers do.',
				'image' => '',
			),
			array(
				'icon'  => 'search',
				'title' => 'GEO, SEO and AIO ready',
				'text'  => 'Answer-first structure for AI search, schema written into Yoast and Rank Math rather than over them, and a robots.txt audit that separates training crawlers from the search crawlers you cannot afford to block.',
				'image' => '',
			),
			array(
				'icon'  => 'stack',
				'title' => 'Clusters and bulk',
				'text'  => 'A pillar and its spokes planned together, queued as one batch, drip-scheduled and internally linked - or 200 keywords at once from a CSV.',
				'image' => '',
			),
			array(
				'icon'  => 'lang',
				'title' => 'Seventeen languages',
				'text'  => 'Publish the same episode for every market you sell into, in the voice you already use there.',
				'image' => '',
			),
		),

		/* ---- where it runs ---- */
		'home_run_eyebrow'   => 'Editors, queue, keys',
		'home_run_title'     => 'It runs where your publishing *already lives*',
		'home_run_sub'       => 'No new SaaS to log into, no export step, no second content calendar. It is a plugin in the WordPress you already run.',
		'home_run_cards'     => array(
			array(
				'icon'  => 'code',
				'title' => 'Both editors',
				'text'  => "Classic and Block, feature for feature\nA sidebar panel in Gutenberg\nHighlight text to rewrite or translate",
			),
			array(
				'icon'  => 'clock',
				'title' => 'A real queue',
				'text'  => "Jobs survive a closed browser\nWP-Cron picks up where you left off\nDrip-schedule across weeks",
			),
			array(
				'icon'  => 'shield',
				'title' => 'Your keys, your data',
				'text'  => "OpenAI, Anthropic, Gemini, OpenRouter, DeepSeek\nBilled by them, not resold by us\nNothing leaves your site but the prompt",
			),
		),

		/* ---- compatibility ---- */
		'home_compat_eyebrow' => 'Compatibility',
		'home_compat_title'   => 'Works with the podcasts and *tools you already use*',
		'home_compat_sub'     => 'Bring an episode in from wherever it lives, and send what comes out to wherever your audience is.',
		'home_compat_in'      => array(
			array(
				'name'  => 'YouTube',
				'image' => '',
			),
			array(
				'name'  => 'Spotify',
				'image' => '',
			),
			array(
				'name'  => 'Apple Podcasts',
				'image' => '',
			),
			array(
				'name'  => 'RSS feed',
				'image' => '',
			),
			array(
				'name'  => 'Any article URL',
				'image' => '',
			),
			array(
				'name'  => 'A keyword',
				'image' => '',
			),
		),
		'home_compat_out'     => array(
			array(
				'name'  => 'WordPress posts',
				'image' => '',
			),
			array(
				'name'  => 'Yoast SEO',
				'image' => '',
			),
			array(
				'name'  => 'Rank Math',
				'image' => '',
			),
			array(
				'name'  => 'Mailchimp',
				'image' => '',
			),
			array(
				'name'  => 'Kit',
				'image' => '',
			),
			array(
				'name'  => 'beehiiv',
				'image' => '',
			),
			array(
				'name'  => 'Instagram',
				'image' => '',
			),
			array(
				'name'  => 'ZIP export',
				'image' => '',
			),
		),
		'home_compat_center'  => '',

		/* ---- GEO band ---- */
		'home_geo_eyebrow'    => 'GEO / SEO / AIO',
		'home_geo_title'      => 'Written for humans. *Structured for AI answers.*',
		'home_geo_sub'        => 'Being quoted by an AI assistant is the new front page. Antradus writes for that without giving up the ranking you already have.',
		'home_geo_cards'      => array(
			array(
				'icon'  => 'faq',
				'title' => 'Answer-first structure',
				'text'  => 'Sections that state the answer, then support it - the shape assistants actually quote.',
			),
			array(
				'icon'  => 'link',
				'title' => 'Schema, not a second graph',
				'text'  => 'Article and FAQ data is written into Yoast and Rank Math and never overwrites what they already set.',
			),
			array(
				'icon'  => 'globe',
				'title' => 'Crawler audit',
				'text'  => 'Blocking GPTBot costs you nothing. Blocking OAI-SearchBot deletes you from AI answers. Antradus tells you which you did.',
			),
			array(
				'icon'  => 'chart',
				'title' => 'AI referral counting',
				'text'  => 'A daily count of visits arriving from AI assistants. No IPs, no user agents, nothing identifying.',
			),
		),
		'home_geo_image'      => '',

		/* ---- use cases ---- */
		'home_use_eyebrow'    => 'Use cases',
		'home_use_title'      => 'Built for teams who publish *on a schedule*',
		'home_use_sub'        => 'Whether the bottleneck is your editorial budget, your freelancer queue or the eleven episodes nobody has written up yet.',
		'home_use_cards'      => array(
			array(
				'icon'  => 'building',
				'title' => 'B2B publishers',
				'text'  => 'Turn a weekly show into a weekly feature without adding a line to the editorial payroll.',
			),
			array(
				'icon'  => 'mic',
				'title' => 'Podcast networks',
				'text'  => 'Every episode across every show gets notes, chapters, cards and a post - the same day it drops.',
			),
			array(
				'icon'  => 'users',
				'title' => 'Agencies',
				'text'  => 'Queue a month of client content, drip it out, and bill from per-post cost reporting.',
			),
			array(
				'icon'  => 'rocket',
				'title' => 'Marketing teams',
				'text'  => 'One webinar becomes a pillar page, six spokes, a newsletter and a fortnight of social.',
			),
			array(
				'icon'  => 'chart',
				'title' => 'SEO leads',
				'text'  => 'Plan clusters, link them automatically, and watch the AI-referral counter alongside your rankings.',
			),
			array(
				'icon'  => 'pen',
				'title' => 'Solo creators',
				'text'  => 'The output of a small team, without adding one to the payroll.',
			),
		),

		/* ---- cost band ---- */
		'home_cost_eyebrow'   => 'The economics',
		'home_cost_title'     => 'What a publishing desk *actually costs*',
		'home_cost_sub'       => 'The case for Antradus is not that AI is clever. It is that a finished episode write-up currently costs you a freelancer invoice, every single week.',
		'home_cost_rows'      => array(
			array(
				'label' => 'Freelance writer, one episode write-up',
				'them'  => '$250 - $600 per episode',
				'us'    => 'Included',
			),
			array(
				'label' => 'Show notes and chapters',
				'them'  => 'A producer hour, every episode',
				'us'    => 'Included',
			),
			array(
				'label' => 'Quote cards for social',
				'them'  => 'A designer, or a template nobody updates',
				'us'    => 'Included',
			),
			array(
				'label' => 'Newsletter write-up',
				'them'  => 'Another brief, another invoice',
				'us'    => 'Included',
			),
			array(
				'label' => 'AI content credits',
				'them'  => 'Resold to you with a markup',
				'us'    => 'Your own API keys, at cost',
			),
			array(
				'label' => 'Licence',
				'them'  => 'Per seat - every new writer adds another one',
				'us'    => 'One licence, up to five sites',
			),
		),
		'home_cost_note'      => 'Figures on the left are typical market rates, shown for comparison - not a quote.',

		/* ---- pricing teaser ---- */
		'home_price_show'     => '1',
		'home_price_eyebrow'  => 'Pricing',
		'home_price_title'    => 'Free to start. *Two ways to scale.*',
		'home_price_sub'      => 'Lite is free forever on WordPress.org. Publisher runs a website at scale, with a 7-day free trial. Studio adds the whole show pipeline on top.',

		/* ---- FAQ ---- */
		'home_faq_eyebrow'    => 'Questions',
		'home_faq_title'      => 'The things people *ask first*',
		'home_faq_sub'        => '',
		'home_faq_items'      => array(
			array(
				'q' => 'Does it just paste the transcript into a post?',
				'a' => 'No. The transcript is the source, not the draft. Antradus writes an original article from it in your house style, and every direct quotation is verified word-for-word against the recording before it is allowed into the piece. If a quote does not match, it is dropped rather than repaired.',
			),
			array(
				'q' => 'Will readers be able to tell it was written by AI?',
				'a' => 'That is what Human Voice is for. It shapes the writing as it is generated, then runs an offline check for the tells - flat sentence rhythm, hedging, passive voice, the stock phrases every model reaches for - and reports them so you can fix them before publishing. It is a check you run, not a claim we make.',
			),
			array(
				'q' => 'What does it cost to run?',
				'a' => 'You bring your own API key from OpenAI, Anthropic, Google, OpenRouter or DeepSeek, and you pay them directly at their published rates. We resell you nothing and take no cut, so there are no credits, no per-word fees and no monthly cap.',
			),
			array(
				'q' => 'Do I need a podcast to use it?',
				'a' => 'No. Podcast episodes are what it does best, but it writes just as happily from a keyword, any article URL, two URLs merged into one piece, or a YouTube video that is not a podcast at all.',
			),
			array(
				'q' => 'Does it work with my SEO plugin?',
				'a' => 'Yes - Yoast and Rank Math, in both editors. Antradus fills their fields and writes into their schema. It never rebuilds sitemaps, canonicals or the schema graph, because those are theirs to own.',
			),
			array(
				'q' => 'Is anything published automatically?',
				'a' => 'Only if you ask for it. Articles can be left as drafts for review, newsletters are always created as drafts and never sent, and the one place publishing is immediate - Instagram, which has no draft concept - is behind an explicit confirmation.',
			),
		),

		/* ---- closing CTA ---- */
		'home_cta_title'      => 'Your next episode is *already a week of content*',
		'home_cta_sub'        => 'Install Lite and write your first article in ten minutes. Move to Publisher when the queue is what you need, and to Studio when it is a whole show.',
		'home_cta_btn1'       => 'See pricing',
		'home_cta_btn1_url'   => 'page:pricing',
		'home_cta_btn2'       => 'Talk to us',
		'home_cta_btn2_url'   => 'page:contact',
		'home_cta_note'       => 'Secure checkout by Freemius - 7 days free on Publisher - your API keys, your control',
	);
}

/* ===========================================================================
 * The "for publishers" page
 *
 * The half of the plugin somebody with a website cares about: volume, plan,
 * links, reporting. Not one word about episodes until the card at the bottom
 * that sends a reader to the other page.
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_publisher() {
	return array(
		'pub_eyebrow'       => 'For publishers',
		'pub_title'         => 'You run the website. *We run the desk.*',
		'pub_sub'           => 'Publisher is Antradus for a site that has to keep publishing: keywords, clusters, bulk queues, internal links and the reporting that shows what it cost. No podcast required, and nothing to log into but WordPress.',
		'pub_cta1'          => 'See the plan',
		'pub_cta1_url'      => '#plan',
		'pub_cta2'          => 'Every feature, in full',
		'pub_cta2_url'      => 'page:features',
		'pub_note'          => '$275 a month for up to five sites, with seven days free. Your own API keys, billed by the provider at their rates.',
		'pub_hero_image'    => '',

		'pub_signals_title' => 'This is you if',
		'pub_signals'       => "The content calendar is always further ahead than the writing\nYou have a keyword list, a plan, or two hundred pages of gaps\nYou care about ranking - and now about being quoted by AI assistants too\nYour writers cost more than your software, and both are going up\nYou do not have a podcast, or you do and the website still comes first",

		'pub_groups_eyebrow' => 'What you get',
		'pub_groups_title'   => 'Everything a site needs *to keep publishing*',
		'pub_groups_sub'     => 'Six groups, and every one of them is in Publisher. Lite covers the writing; this is what covers the schedule.',
		'pub_groups'         => array(
			array(
				'icon'  => 'stack',
				'title' => 'Publishing at volume',
				'items' => "Bulk publishing - 200 keywords at once, or a CSV import\nDrip-scheduling across days or weeks\nA queue that survives a closed browser, on WP-Cron\nDraft review, retries and per-job error reporting\nAutomatic categorisation into the categories you already have",
			),
			array(
				'icon'  => 'merge',
				'title' => 'Planning the site, not the post',
				'items' => "SEO clusters - a pillar and its spokes, planned together\nAutomatic internal linking as each piece is written\nInbound link suggestions for the posts nobody points at\nAI topic ideas drawn from your own niche\nSaved plans you can correct and re-run",
			),
			array(
				'icon'  => 'brain',
				'title' => 'Sounding like your own writers',
				'items' => "The Human Voice prompt layer - off, natural or strong\nAn offline Human Check for rhythm, hedging and stock phrases\nAn editable banned-phrase list\nA brand voice profile learned from your published posts\nHumanize a post you already published, in place\nYour own system prompt, applied to every article",
			),
			array(
				'icon'  => 'search',
				'title' => 'Found by search, and by AI',
				'items' => "Focus keyphrase, meta title and description into Yoast or Rank Math\nSEO metadata for posts you published years ago\nAnswer-first structure, which is the shape assistants quote\nArticle and FAQ schema written into your SEO plugin, never over it\nAn AI-crawler audit of robots.txt with a copy-paste fix\nA daily count of readers arriving from AI assistants",
			),
			array(
				'icon'  => 'globe',
				'title' => 'Written from anything',
				'items' => "A single keyword\nAny article URL, with an honest refusal when the page could not be read\nTwo sources merged into one original piece\nLive web search first, for news from hours ago\nA YouTube video, via the companion extension\nWooCommerce products written from a product photo",
			),
			array(
				'icon'  => 'chart',
				'title' => 'Knowing what it cost',
				'items' => "Words, images and tokens logged per post\nEstimated cost per post, with editable model prices\nAttribution back to the post that spent it\nUsage history you can purge whenever you like",
			),
		),

		'pub_trends_eyebrow' => 'Google Trends',
		'pub_trends_title'   => 'Write about it *while people are still searching for it*',
		'pub_trends_sub'     => 'Antradus reads what is trending in your country right now, then does the part a trend list never does: it opens the news behind each one and checks the story was actually published today before it will write a word about it.',
		'pub_trends_points'  => array(
			array(
				'icon'  => 'chart',
				'title' => 'Today\'s searches, by country',
				'text'  => 'What is rising right now where your readers are, not a global list you have to translate into your own market.',
			),
			array(
				'icon'  => 'shield',
				'title' => 'Verified before it is written',
				'text'  => 'Each trend is traced back to a real article and rejected unless it was published today. A rejection tells you which trend and why, rather than quietly writing from nothing.',
			),
			array(
				'icon'  => 'clock',
				'title' => 'Today means today where you are',
				'text'  => 'Freshness is judged in your site\'s own timezone, so an article filed late in the evening is not counted as yesterday\'s news.',
			),
			array(
				'icon'  => 'link',
				'title' => 'The sources travel with the post',
				'text'  => 'Every article it writes this way carries the sources it was written from, one per publication, listed at the end.',
			),
			array(
				'icon'  => 'stack',
				'title' => 'Straight into the same queue',
				'text'  => 'Tick the trends worth covering and send them all at once. They drip out on the schedule you already use - nothing new to learn.',
			),
		),
		'pub_trends_steps'   => array(
			array(
				'title' => 'Pick the country',
				'text'  => 'Antradus lists what is being searched there right now, refreshed through the day.',
			),
			array(
				'title' => 'See what is behind it',
				'text'  => 'It opens the news for a trend and shows you the verdict on each source - published today, or refused and why.',
			),
			array(
				'title' => 'Tick the ones worth writing',
				'text'  => 'A trend with nothing solid behind it is left out. You are choosing from what already passed the check.',
			),
			array(
				'title' => 'Send them to the queue',
				'text'  => 'One click queues the lot as ordinary articles - drafts, drip-scheduling, SEO fill and internal links, exactly as usual.',
			),
		),
		'pub_trends_image'   => '',
		'pub_trends_note'    => 'Trending data comes from your own SerpApi key, on their free or paid tier. Antradus resells you nothing.',

		'pub_flow_title' => 'A publishing week, *on Publisher*',
		'pub_flow_sub'   => 'The same four moves every week, whether it is four articles or forty.',
		'pub_flow'       => array(
			array(
				'title' => 'Plan the cluster',
				'text'  => 'Give it a pillar keyword. It comes back with the pillar, its spokes and how they should link to each other.',
			),
			array(
				'title' => 'Send it to the queue',
				'text'  => 'Queue the whole cluster and drip it across the fortnight. Close the browser - WP-Cron carries on without you.',
			),
			array(
				'title' => 'Edit what arrives',
				'text'  => 'Drafts land in your editor. The Human Check names the paragraphs that read like a machine, before a reader does.',
			),
			array(
				'title' => 'Publish and count',
				'text'  => 'Yoast or Rank Math is already filled in, the internal links are already made, and Reporting shows what each post cost.',
			),
		),

		'pub_plan_eyebrow' => 'The plan',
		'pub_plan_title'   => 'All of that is *Publisher*',
		'pub_plan_sub'     => 'One plan, up to five sites, seven days free. Lite stays free underneath it, and Studio adds the show on top.',
		'pub_plan_names'   => 'Publisher',
		'pub_plan_note'    => 'You pay OpenAI, Anthropic, Google, OpenRouter or DeepSeek directly for the writing itself. We resell you nothing, so there are no credits and no per-word fees.',
		'pub_plan_more'    => 'Compare every plan, feature by feature',

		'pub_switch_title'   => 'Actually, you run a *show*?',
		'pub_switch_text'    => 'If your material is episodes rather than keywords, Studio is the page you want - it adds the whole podcast pipeline on top of everything here.',
		'pub_switch_btn'     => 'See what Studio does',
		'pub_switch_btn_url' => 'page:studio',
	);
}

/* ===========================================================================
 * The "for studios" page
 *
 * The other half: an episode in, a week of content out. It says plainly that
 * it contains Publisher, because it does and because that is the reason for
 * the price difference.
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_studio() {
	return array(
		'std_eyebrow'       => 'For studios',
		'std_title'         => 'You run a show. *Antradus writes the week around it.*',
		'std_sub'           => 'Studio is everything Publisher does, plus the podcast pipeline: one episode becomes an article, show notes, chapters, quote cards, a newsletter draft and a fortnight of social - with every quotation checked against the recording.',
		'std_cta1'          => 'See the plan',
		'std_cta1_url'      => '#plan',
		'std_cta2'          => 'Book a demo',
		'std_cta2_url'      => 'page:contact',
		'std_note'          => '$1,500 a month. It starts with a demo, because your back catalogue is the interesting part of the conversation.',
		'std_hero_image'    => '',

		'std_signals_title' => 'This is you if',
		'std_signals'       => "You publish episodes, and the write-up is always the thing that slips\nThere is a back catalogue nobody has turned into pages\nAn episode write-up costs you a freelancer invoice, every week\nShow notes, chapters and quote cards are three separate jobs\nYou run more than one show - or more than one client's show",

		'std_groups_eyebrow' => 'What you get',
		'std_groups_title'   => 'One episode in. *A week of content out.*',
		'std_groups_sub'     => 'Everything below comes out of the same transcript, and nothing is invented on top of it.',
		'std_groups'         => array(
			array(
				'icon'  => 'mic',
				'title' => 'The episode goes in',
				'items' => "YouTube, Spotify, Apple Podcasts or an RSS feed\nA whole back catalogue, published on a schedule\nSaved Plans - every correction kept, and re-running costs nothing extra\nOne show or twenty, each with its own voice and style",
			),
			array(
				'icon'  => 'pen',
				'title' => 'The article comes out',
				'items' => "A finished piece in your house style, not a tidied transcript\nEvery direct quotation checked word-for-word, or dropped\nAnswer-first structure, so assistants quote it rather than skip it\nThe angle and the outline shown to you before a word is written",
			),
			array(
				'icon'  => 'calendar',
				'title' => 'The show furniture',
				'items' => "Timestamped chapters\nA description ready to paste into your podcast host\nAn episode summary for the feed\nGuest names and topics picked out of the recording",
			),
			array(
				'icon'  => 'image',
				'title' => 'The week around it',
				'items' => "Quote cards drawn with real type, never an image model\nNewsletter drafts for Kit, Mailchimp and beehiiv - never sent for you\nInstagram carousels, behind an explicit confirmation\nA ZIP of every asset, for whatever we do not publish to",
			),
			array(
				'icon'  => 'lang',
				'title' => 'Every market you sell into',
				'items' => "Seventeen languages, in the voice you already use there\nThe same episode published once per market\nRight-to-left handled properly, not bolted on\nTranslate an existing article without rewriting it",
			),
			array(
				'icon'  => 'stack',
				'title' => 'And all of Publisher',
				'items' => "Bulk publishing, SEO clusters and internal linking\nHuman Voice and the offline Human Check\nGEO structure, the crawler audit and AI-referral counting\nYoast and Rank Math auto-fill in both editors\nCost reporting per post\nA success manager",
			),
		),

		'std_flow_title' => 'What happens to *one episode*',
		'std_flow_sub'   => 'From a link to a week of scheduled content, with a stop for you in the middle.',
		'std_flow'       => array(
			array(
				'title' => 'Paste the link',
				'text'  => 'A YouTube or Spotify URL, an RSS feed or a file. Antradus transcribes it and reads it - it does not paste it.',
			),
			array(
				'title' => 'Approve the plan',
				'text'  => 'You see the angle, the outline and the quotations it intends to use. Correct it, and the correction is saved for good.',
			),
			array(
				'title' => 'Collect the week',
				'text'  => 'The article, show notes, chapters, quote cards, a newsletter draft and the social posts - all from that one transcript.',
			),
			array(
				'title' => 'Publish on your schedule',
				'text'  => 'Drafts for review, dripped across the week, in as many languages as you sell in. Nothing goes out unasked.',
			),
		),

		'std_compat_eyebrow' => 'Compatibility',
		'std_compat_title'   => 'Wherever the show lives, *and wherever the week goes*',
		'std_compat_sub'     => 'Point Antradus at the episode where it already is, and it writes into the tools you already publish with.',
		'std_compat_center'  => '',
		'std_compat_in'      => array(
			array(
				'name'  => 'YouTube',
				'image' => '',
			),
			array(
				'name'  => 'Spotify',
				'image' => '',
			),
			array(
				'name'  => 'Apple Podcasts',
				'image' => '',
			),
			array(
				'name'  => 'An RSS feed',
				'image' => '',
			),
			array(
				'name'  => 'An audio or video file',
				'image' => '',
			),
		),
		'std_compat_out'     => array(
			array(
				'name'  => 'WordPress posts',
				'image' => '',
			),
			array(
				'name'  => 'Show notes and chapters',
				'image' => '',
			),
			array(
				'name'  => 'Quote cards',
				'image' => '',
			),
			array(
				'name'  => 'Mailchimp',
				'image' => '',
			),
			array(
				'name'  => 'Kit',
				'image' => '',
			),
			array(
				'name'  => 'beehiiv',
				'image' => '',
			),
			array(
				'name'  => 'Instagram',
				'image' => '',
			),
			array(
				'name'  => 'ZIP export',
				'image' => '',
			),
		),

		'std_plan_eyebrow' => 'The plan',
		'std_plan_title'   => 'All of that is *Studio*',
		'std_plan_sub'     => 'Sold after a demo rather than a checkout, because a show and its back catalogue are worth looking at first. Managed is the same thing with us doing the work.',
		'std_plan_names'   => 'Studio, Managed',
		'std_plan_note'    => 'Studio contains Publisher in full. The writing itself is billed to you by your own AI provider, at their published rates.',
		'std_plan_more'    => 'Compare every plan, feature by feature',

		'std_switch_title'   => 'No podcast, just a *website*?',
		'std_switch_text'    => 'If your material is keywords and pages rather than episodes, Publisher is the page you want - and it is a third of the price.',
		'std_switch_btn'     => 'See what Publisher does',
		'std_switch_btn_url' => 'page:publisher',
	);
}

/* ===========================================================================
 * Plugin features page
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_features() {
	return array(
		'feat_eyebrow'     => 'Plugin features',
		'feat_title'       => 'Everything in the box, *in plain language*',
		'feat_sub'         => 'The full feature list, split by what it does for you. Lite is free forever; Publisher adds the automation; Studio adds the show.',
		'feat_hero_image'  => '',
		'feat_content_top' => 'Screenshots and walkthrough',
		'feat_content_sub' => 'Everything you add to this page in the editor - galleries included - renders here, in the site design.',
		'feat_groups'      => array(
			array(
				'title' => 'From a podcast episode',
				'icon'  => 'mic',
				'items' => "Episode to article - a finished piece, not a tidied transcript\nVerified quotations - checked word-for-word or dropped\nShow notes and timestamped chapters\nQuote cards drawn with real type, never an image model\nNewsletter drafts for Mailchimp, Kit and beehiiv\nInstagram carousels, behind an explicit confirmation\nSaved episode plans you can correct and re-run",
			),
			array(
				'title' => 'From anything else',
				'icon'  => 'globe',
				'items' => "Full articles from a single keyword\nArticles from any URL, with an honest refusal when the page could not really be read\nMerge two sources into one original piece\nYouTube video to article via the companion extension\nLive web search before writing, for news from hours ago\nWooCommerce products written from a product photo",
			),
			array(
				'title' => 'Sounding human',
				'icon'  => 'brain',
				'items' => "Human Voice prompt layer, off / natural / strong\nAn offline Human Check - rhythm, hedging, passive voice, stock phrases\nAn editable banned-phrase list\nA brand voice profile learned from your own published posts\nHumanize an existing post in place\nFive styles, five tones, seventeen languages\nYour own system prompt, applied to every article",
			),
			array(
				'title' => 'Found by search and by AI',
				'icon'  => 'search',
				'items' => "Focus keyphrase, meta title and description into Yoast and Rank Math\nSEO metadata for posts you published years ago\nAnswer-first GEO structure for AI assistants\nArticle and FAQ schema written into your SEO plugin, never over it\nAn AI-crawler audit of robots.txt with a copy-paste fix\nA daily count of visits arriving from AI assistants\nAutomatic internal linking, and inbound suggestions for orphans\nSEO clusters - a pillar and its spokes, planned and linked together",
			),
			array(
				'title' => 'At volume',
				'icon'  => 'stack',
				'items' => "Bulk publishing - 200 keywords at once, or a CSV import\nDrip-scheduling across days or weeks\nDraft review, retries and per-job error reporting\nA queue that survives a closed browser, via WP-Cron\nAI topic suggestions drawn from your own niche\nAutomatic categorisation into the categories you already have",
			),
			array(
				'title' => 'Pictures',
				'icon'  => 'image',
				'items' => "AI featured images with ten editable style presets\nAlbums of up to nine distinct scenes from your article\nSquare, landscape or portrait, mapped correctly per provider\nColour cast to match your brand\nAutomatic WebP conversion, alt text, caption and title",
			),
			array(
				'title' => 'Knowing what it cost',
				'icon'  => 'chart',
				'items' => "Words, images and tokens logged per post\nEstimated cost per post, with editable model prices\nAttribution back to the post that spent it\nUsage history you can purge whenever you like",
			),
			array(
				'title' => 'Safety and control',
				'icon'  => 'shield',
				'items' => "SSRF-protected URL fetching\nAn honest refusal rather than an article about a page it could not read\nNothing sent anywhere but your chosen AI provider\nNo account required, no telemetry, no content stored by us\nUninstall removes every option and table it created",
			),
		),
		'feat_cta_title'   => 'Want the short version?',
		'feat_cta_sub'     => 'Lite covers writing. Publisher covers the queue, the linking and the reporting. Studio covers the show.',
		'feat_cta_btn'     => 'Compare the plans',
		'feat_cta_btn_url' => 'page:pricing',
	);
}

/* ===========================================================================
 * Pricing
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_pricing() {
	return array(
		'price_eyebrow'      => 'Pricing',
		'price_title'        => 'Start free. *Scale when it pays.*',
		'price_sub'          => 'Lite writes articles and always will, for nothing. Publisher runs a website at scale. Studio runs a whole show. Your API keys, your sites, your control.',
		'price_note'         => 'Lite is free forever - no account, no card. Publisher is $275 a month for up to five sites, with a 7-day free trial. Studio is $1,500 a month and starts with a demo.',

		/*
		 * Four prices is the moment somebody stops comparing and starts
		 * wondering which two of them are even meant for them. That is a
		 * question about features, not about money, so it is answered by the
		 * two pages that talk about features.
		 */
		'price_paths_label'  => 'Not sure which of these is yours?',
		'price_paths'        => array(
			array(
				'icon'    => 'pen',
				'label'   => "I'm a publisher",
				'text'    => 'A website to fill',
				'cta_url' => 'page:publisher',
			),
			array(
				'icon'    => 'mic',
				'label'   => "I'm a studio",
				'text'    => 'A show to write up',
				'cta_url' => 'page:studio',
			),
		),

		// Freemius wiring. Only the plans that opt into checkout use it.
		'fs_product_id'      => '34895',
		'fs_public_key'      => 'pk_9e0c5c467f6e7af2722489fc3c721',
		'fs_snippet'         => '',
		'fs_logo'            => '',

		'price_plans'        => array(
			array(
				'name'     => 'Lite',
				'sub'      => 'Free forever',
				'badge'    => '',
				'featured' => '',
				'mode'     => 'amount',
				'currency' => '$',
				'amount'   => '0',
				'cents'    => '',
				'unit'     => '',
				'billed'   => 'Free forever',
				'chip'     => 'Unlimited sites',
				'intro'    => 'Includes',
				'features' => "Full articles from a keyword or any URL\nSEO meta with one-click copy\nFeatured image and ten style presets\nHighlight-text AI transforms\nFAQ sections, topic ideas, 17 languages\nAll five AI providers - your own keys\nClassic and Block editors",
				'cta'      => 'Download free',
				'cta_type' => 'link',
				'cta_url'  => 'https://wordpress.org/plugins/antradus-ai-lite/',
				'plan_id'  => '',
				'licenses' => '',
				'trial'    => '',
				'trial_text' => '',
				'fs_snippet' => '',
				'note'     => 'Hosted on WordPress.org - installs from your dashboard',
			),
			array(
				'name'     => 'Publisher',
				'sub'      => 'Everything for a website',
				'badge'    => 'Most popular',
				'featured' => '1',
				'mode'     => 'amount',
				'currency' => '$',
				'amount'   => '275',
				'cents'    => '',
				'unit'     => '/month',
				'billed'   => 'Billed monthly - cancel any time',
				'chip'     => 'Up to 5 sites',
				'intro'    => 'Everything in Lite, plus',
				'features' => "Bulk publishing - 200 keywords or a CSV\nSEO clusters and automatic internal linking\nHuman Voice and the Human Check\nAI Search (GEO), crawler audit and referral counting\nYouTube to Article for a single video\nLive web search and merge-two-sources\nAI albums and WooCommerce products\nYoast and Rank Math auto-fill, plus Reporting\nPriority support",
				'cta'      => 'Book now',
				'cta_type' => 'freemius',
				'cta_url'  => '',
				'plan_id'  => '63082',
				'licenses' => '',
				'trial'    => 'paid',
				'trial_text' => 'or start a 7-day free trial',
				'fs_snippet' => '',
				'note'     => 'Booking bills you today. The trial takes a card and charges nothing until day 8.',
			),
			array(
				'name'     => 'Studio',
				'sub'      => 'Everything for a whole show',
				'badge'    => '',
				'featured' => '',
				'mode'     => 'amount',
				'currency' => '$',
				'amount'   => '1,500',
				'cents'    => '',
				'unit'     => '/month',
				'billed'   => 'Billed monthly - starts with a demo',
				'chip'     => 'Podcast and video shows',
				'intro'    => 'Everything in Publisher, plus',
				'features' => "Podcast episode to article, with verified quotes\nA whole back catalogue, on a schedule\nShow notes, chapters and timestamps\nSaved Plans - every correction kept, nothing re-billed\nQuote cards drawn from the words that were said\nNewsletter drafts for Kit, Mailchimp and beehiiv\nInstagram carousels and a ZIP of every asset\nA success manager",
				'cta'      => 'Book a demo',
				'cta_type' => 'link',
				'cta_url'  => 'page:contact',
				'plan_id'  => '',
				'licenses' => '',
				'trial'    => '',
				'trial_text' => '',
				'fs_snippet' => '',
				'note'     => 'We look at your show first - there is usually a real conversation to have',
			),
			array(
				'name'     => 'Managed',
				'sub'      => 'We run it for you',
				'badge'    => '',
				'featured' => '',
				'mode'     => 'custom',
				'currency' => '$',
				'amount'   => 'Talk to us',
				'cents'    => '',
				'unit'     => '',
				'billed'   => 'Quoted per show',
				'chip'     => 'Done for you',
				'intro'    => 'Everything in Studio, plus',
				'features' => "We build the site, or improve the one you have\nTen back-catalogue episodes published a month\nYour brand, your voice, set up with you\nOnboarding and migration handled\nInvoicing instead of a card\nA direct line to the people who build it",
				'cta'      => 'Talk to us',
				'cta_type' => 'link',
				'cta_url'  => 'page:contact',
				'plan_id'  => '',
				'licenses' => '',
				'trial'    => '',
				'trial_text' => '',
				'fs_snippet' => '',
				'note'     => 'Tell us about your show and we will scope it with you',
			),
		),

		'price_cmp_show'     => '1',
		'price_cmp_title'    => 'What each plan *actually does*',
		'price_cmp_sub'      => 'Lite is the full AI writing toolkit, free forever. Publisher adds everything for running a website at scale. Studio adds the show.',
		'price_cmp_col_a'    => 'Lite',
		'price_cmp_col_a_sub' => 'Free',
		'price_cmp_col_b'    => 'Publisher',
		'price_cmp_col_b_sub' => '$275/month',
		'price_cmp_col_c'    => 'Studio',
		'price_cmp_col_c_sub' => '$1,500/month',
		'price_cmp_rows'     => array(
			array(
				'group' => 'The writing toolkit - in every plan',
				'name'  => '',
				'text'  => '',
				'a'     => '',
				'b'     => '',
				'c'     => '',
			),
			array(
				'group' => '',
				'name'  => 'Full article generation',
				'text'  => 'Complete, SEO-ready articles from a keyword or any external URL.',
				'a'     => 'yes',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'SEO meta generation',
				'text'  => 'Focus keyphrase, meta title and meta description with one-click copy.',
				'a'     => 'yes',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'In-editor AI highlights',
				'text'  => 'Rewrite, expand, shorten, fix grammar, translate - or run your own instruction.',
				'a'     => 'yes',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Featured image generation',
				'text'  => 'Ten editable style presets and a colour cast, straight into the Media Library.',
				'a'     => 'yes',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => '5 styles x 5 tones x 17 languages',
				'text'  => 'Match your voice, your audience and your market.',
				'a'     => 'yes',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Bring your own API keys',
				'text'  => 'OpenAI, Anthropic, Gemini, OpenRouter and DeepSeek - you control the cost.',
				'a'     => 'yes',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => 'Running a website at scale',
				'name'  => '',
				'text'  => '',
				'a'     => '',
				'b'     => '',
				'c'     => '',
			),
			array(
				'group' => '',
				'name'  => 'Bulk publishing',
				'text'  => 'Up to 200 keywords at once or a CSV import - drip-schedule, review, retry.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'SEO clusters and internal linking',
				'text'  => 'A pillar and its spokes, planned together and linked automatically.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Human Voice and Human Check',
				'text'  => 'The prompt layer, the banned-phrase list and the offline lint.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'AI Search (GEO) toolkit',
				'text'  => 'Answer-first structure, schema, the crawler audit and AI-referral counting.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'YouTube to Article',
				'text'  => 'One video becomes an article, from the post editor.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Live web search',
				'text'  => 'The AI searches the internet first, so you can cover news from hours ago.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Yoast and Rank Math auto-fill',
				'text'  => 'Your SEO plugin fields filled in automatically, in both editors.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Reporting dashboard',
				'text'  => 'Words, images, tokens and estimated cost per post.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
			array(
				'group' => 'Running a show',
				'name'  => '',
				'text'  => '',
				'a'     => '',
				'b'     => '',
				'c'     => '',
			),
			array(
				'group' => '',
				'name'  => 'Whole episodes to articles',
				'text'  => 'A back catalogue turned into articles, with every quotation checked word-for-word against the recording.',
				'a'     => 'no',
				'b'     => 'no',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Show notes and chapters',
				'text'  => 'Timestamped chapters and a description ready for your podcast host.',
				'a'     => 'no',
				'b'     => 'no',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Saved Plans',
				'text'  => 'Every correction you made is kept, so opening an episode again costs nothing.',
				'a'     => 'no',
				'b'     => 'no',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Quote cards',
				'text'  => 'Drawn with real type, so the words on the card are the words that were said.',
				'a'     => 'no',
				'b'     => 'no',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Newsletter and social drafts',
				'text'  => 'Mailchimp, Kit and beehiiv drafts, plus Instagram carousels. Nothing is sent for you.',
				'a'     => 'no',
				'b'     => 'no',
				'c'     => 'yes',
			),
			array(
				'group' => '',
				'name'  => 'Priority support',
				'text'  => 'Priority email handling, and a success manager on Studio.',
				'a'     => 'no',
				'b'     => 'yes',
				'c'     => 'yes',
			),
		),
		'price_faq_title'    => 'Before you *buy*',
		'price_faq_items'    => array(
			array(
				'q' => 'Publisher or Studio - which one do I need?',
				'a' => 'If you have a website, Publisher. If you have a podcast or a video show, Studio. Publisher already turns a single video into an article from the post editor, so you can try that shape before deciding; Studio is that for an entire back catalogue, on a schedule, with every quotation checked against the recording.',
			),
			array(
				'q' => 'What does the AI itself cost?',
				'a' => 'Whatever your provider charges you, billed by them directly. We never resell tokens, so there is no markup between you and OpenAI, Anthropic, Google, OpenRouter or DeepSeek.',
			),
			array(
				'q' => 'Can I try it first?',
				'a' => 'Publisher has a 7-day free trial - a card is required, and you are billed only if you stay past day 7. Studio starts with a demo instead, because there is usually a real conversation to have about your show first. Lite is free forever and runs the same writing engine, so you can judge the output before any of this.',
			),
			array(
				'q' => 'I run more than one site.',
				'a' => 'Publisher covers up to five. Beyond that, or if you want us running the publishing for you, that is Managed - get in touch and we will scope it with you.',
			),
			array(
				'q' => 'Do I install a different plugin for each plan?',
				'a' => 'No. There is one plugin. Lite comes from WordPress.org; Publisher and Studio are the same download, and your licence key decides which features appear. Moving between plans means changing a key, never reinstalling anything, and your settings and API keys carry over.',
			),
			array(
				'q' => 'I bought Antradus AI Pro before these plans existed.',
				'a' => 'Nothing changes for you. The original licence carries the full Studio feature set, permanently, and you do not need to do anything to keep it. No feature was taken away from anyone who had already paid.',
			),
		),
		'price_trust'        => "7-day free trial on Publisher\nSecure checkout by Freemius\nYour own API keys, billed at cost\nPrices in USD",
		'price_sales_text'   => 'Running more than five sites, want an invoice instead of a card, or would rather we ran the publishing for you?',
		'price_sales_btn'    => 'Talk to us',
		'price_sales_url'    => 'page:contact',
	);
}

/* ===========================================================================
 * Blog
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_blog() {
	return array(
		'blog_eyebrow'     => 'The Antradus blog',
		'blog_title'       => 'Publishing, *out loud*',
		'blog_sub'         => 'What we are learning about AI content, podcast publishing, and being found by search engines and assistants alike.',
		'blog_per_page'    => '12',
		'blog_show_search' => '1',
		'blog_show_cats'   => '1',
		'blog_empty'       => 'Nothing matches that yet.',
		'single_back'      => 'All articles',
		'single_rail'      => 'More to read',
		'single_cta_show'  => '1',
		'single_cta_title' => 'Turn your next episode into this',
		'single_cta_sub'   => 'Antradus AI writes the article, the notes, the cards and the newsletter - inside WordPress.',
		'single_cta_btn'   => 'See pricing',
		'single_cta_url'   => 'page:pricing',
	);
}

/* ===========================================================================
 * Contact
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_contact() {
	return array(
		'contact_eyebrow'   => 'We are listening',
		'contact_title'     => 'Talk to *a human*',
		'contact_sub'       => 'Questions about plans, features, licensing or your setup? Send a message and a real person replies.',
		'contact_form'      => '[forminator_form id="90292"]',
		'contact_form_head' => 'Send a message',
		'contact_form_hint' => 'Fill in the form and it lands straight in our inbox.',
		'contact_cards'     => array(
			array(
				'icon'  => 'mail',
				'title' => 'Email support',
				'text'  => 'For support, pre-sales and billing - or just reply to any of our emails.',
				'link'  => 'mailto:support@antradusai.com',
				'label' => 'support@antradusai.com',
			),
			array(
				'icon'  => 'clock',
				'title' => 'Response time',
				'text'  => 'We reply within one business day. Publisher and Studio licences jump the queue.',
				'link'  => '',
				'label' => '',
			),
			array(
				'icon'  => 'bulb',
				'title' => 'Getting help faster',
				'text'  => 'For technical issues, include your site URL, WordPress version, the AI provider you use and what you tried. It usually saves a whole round trip.',
				'link'  => '',
				'label' => '',
			),
			array(
				'icon'  => 'tag',
				'title' => 'Not sure which plan fits?',
				'text'  => 'Ask before you buy. We would rather tell you Lite is enough than sell you a plan you do not need.',
				'link'  => 'page:pricing',
				'label' => 'See the plans',
			),
		),
		'aff_show'          => '1',
		'aff_eyebrow'       => 'Partner programme',
		'aff_title'         => 'Earn on *every referral*',
		'aff_sub'           => 'Recommend Antradus AI to your audience and earn a commission on every sale you refer. Tracking, attribution and payouts are handled by Freemius.',
		'aff_steps'         => array(
			array(
				'num'   => '01',
				'title' => 'Apply',
				'text'  => 'Use the form below. Tell us about your site, channel or audience, and how you plan to share Antradus.',
			),
			array(
				'num'   => '02',
				'title' => 'Share',
				'text'  => 'Once approved you get a unique referral link. Put it in reviews, tutorials, videos or your newsletter.',
			),
			array(
				'num'   => '03',
				'title' => 'Earn',
				'text'  => 'Every purchase through your link is tracked by Freemius and credited to you, automatically.',
			),
		),
		'aff_form'          => '[forminator_form id="90294"]',
		'aff_form_head'     => 'Apply to the programme',
		'aff_form_hint'     => 'Start your message with "Affiliate application" and include a link to your site, channel or audience. We review every application personally and reply within 48 hours.',
		'contact_trust'     => "support@antradusai.com\nPayouts by Freemius\nReal humans, real replies",
	);
}

/* ===========================================================================
 * Welcome / newsletter
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_welcome() {
	return array(
		'welcome_eyebrow'  => 'Stay in range',
		'welcome_title'    => 'Subscribe',
		'welcome_sub'      => 'Release notes, publishing tactics and the occasional honest post-mortem. No noise.',
		'welcome_image'    => '',
		'welcome_form'     => '',
		'welcome_bullets'  => "What shipped, and why it shipped that way\nWhat is working in AI search right now\nNo more than one email a week",
		'welcome_note'     => 'Unsubscribe in one click, any time.',
	);
}

/* ===========================================================================
 * Docs
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_docs() {
	return array(
		'docs_eyebrow'      => 'Documentation',
		'docs_title'        => 'Every feature, *written down*',
		'docs_sub'          => 'Guides for each part of the plugin, in the same plain language as the interface.',
		'docs_note'         => 'The guides are published by the Antradus AI plugin. Once it has synced them, they appear here on their own - there is nothing to add to this page.',
		'docs_search_label' => 'Search the guides',
		'docs_search_hint'  => 'Type to filter every guide',
		'docs_all_label'    => 'All',
		'docs_empty'        => 'No guide matches that.',
	);
}

/* ===========================================================================
 * Security and comments
 * ========================================================================= */

/**
 * Hardening defaults. Every one of them is on.
 *
 * @return array
 */
function antradus_defaults_security() {
	return array(
		'sec_no_enum'     => '1',
		'sec_no_xmlrpc'   => '1',
		'sec_login'       => '1',
		'sec_headers'     => '1',
		'sec_clean_head'  => '1',
		'comments_enable' => '',
	);
}

/* ===========================================================================
 * Search metadata
 * ========================================================================= */

/**
 * A focus keyword, a search title and a meta description for each page.
 *
 * Written to three constraints rather than to taste. The title stays under
 * about sixty characters, because Google measures pixels and cuts the rest.
 * The description stays under about a hundred and fifty-five, for the same
 * reason, and says something a person might act on rather than repeating the
 * title. And the focus keyword appears in both, near the front, because that
 * is what Rank Math scores and what a reader scanning ten blue links matches
 * their own question against.
 *
 * The keywords are the phrases somebody types when they are shopping, not the
 * ones we would choose to be described by: "podcast to article WordPress
 * plugin" rather than "publishing system". Where a page's own address already
 * carries the phrase - /pricing/, /for-studios/, /plugin-features/ - the
 * keyword is built around that word, which is one of the few SEO tests that
 * is genuinely free to pass.
 *
 * @return array
 */
function antradus_defaults_seo() {
	return array(
		'seo_home_focus'      => 'podcast to article WordPress plugin',
		'seo_home_title'      => 'Podcast to Article WordPress Plugin | Antradus AI',
		'seo_home_desc'       => 'Antradus AI is the podcast to article WordPress plugin that turns one episode - or one keyword - into articles, show notes, quote cards and a newsletter.',

		'seo_publisher_focus' => 'AI content for publishers',
		'seo_publisher_title' => 'AI Content for Publishers, Inside WordPress | Antradus',
		'seo_publisher_desc'  => 'AI content for publishers who must keep shipping: keyword clusters, bulk queues, drip scheduling, internal links and cost reporting, all inside WordPress.',

		'seo_studio_focus'    => 'AI for podcast studios',
		'seo_studio_title'    => 'AI for Podcast Studios: One Episode, a Week of Content',
		'seo_studio_desc'     => 'AI for podcast studios: one episode becomes an article, show notes, chapters, quote cards and a newsletter, every quotation checked against the recording.',

		'seo_features_focus'  => 'AI writing plugin features',
		'seo_features_title'  => 'AI Writing Plugin Features, in Plain Language | Antradus',
		'seo_features_desc'   => 'Every AI writing plugin feature in plain language: episode to article, verified quotations, SEO metadata, bulk publishing, brand voice, your own API keys.',

		'seo_pricing_focus'   => 'Antradus AI pricing',
		'seo_pricing_title'   => 'Antradus AI Pricing: Free Lite, Publisher, Studio',
		'seo_pricing_desc'    => 'Antradus AI pricing: Lite is free forever, Publisher is $275 a month for five sites with a 7-day trial, Studio is $1,500 a month. Bring your own API keys.',

		'seo_docs_focus'      => 'Antradus AI docs',
		'seo_docs_title'      => 'Antradus AI Docs: Every Feature, Written Down',
		'seo_docs_desc'       => 'Antradus AI docs - a guide for every part of the plugin, in the same plain language as the interface. Setup, providers, publishing, SEO, images and costs.',

		'seo_blog_focus'      => 'AI publishing blog',
		'seo_blog_title'      => 'The Antradus AI Publishing Blog | Search, GEO, Podcasts',
		'seo_blog_desc'       => 'An AI publishing blog on writing that sounds human, podcast repurposing, and being found by search engines and AI assistants alike. Notes from Antradus.',

		'seo_contact_focus'   => 'contact Antradus AI',
		'seo_contact_title'   => 'Contact Antradus AI - Support, Sales and Affiliates',
		'seo_contact_desc'    => 'Contact Antradus AI about plans, features, licensing or your setup. A real person replies within one business day. Affiliate applications welcome too.',

		'seo_welcome_focus'   => 'Antradus AI newsletter',
		'seo_welcome_title'   => 'The Antradus AI Newsletter: Release Notes and Tactics',
		'seo_welcome_desc'    => 'Join the Antradus AI newsletter for release notes, publishing tactics and honest post-mortems. One email a week at most, and unsubscribe in one click.',
	);
}

/* ===========================================================================
 * Footer
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_footer() {
	return array(
		'footer_tagline'   => 'The first B2B podcast-to-article system for WordPress. One episode in, a publishing week out - on your own API keys.',
		'footer_cta_title' => 'Ready to stop paying for every piece you publish?',
		'footer_cta_btn'   => 'See pricing',
		'footer_cta_url'   => 'page:pricing',
		'footer_cols'      => array(
			array(
				'title' => 'Product',
				'links' => "For publishers | page:publisher\nFor studios | page:studio\nFeatures | page:features\nPricing | page:pricing\nDocs | page:docs\nDownload Lite | https://wordpress.org/plugins/antradus-ai-lite/",
			),
			array(
				'title' => 'Company',
				'links' => "Blog | page:blog\nContact | page:contact\nNewsletter | page:welcome\nAffiliates | page:contact",
			),
			array(
				'title' => 'Support',
				'links' => "Email us | mailto:support@antradusai.com\nDocumentation | page:docs\nWordPress.org | https://wordpress.org/plugins/antradus-ai-lite/",
			),
		),
		'footer_trust'     => "Your API keys, your control\nClassic and Block editors\nSecure checkout by Freemius",
		'footer_legal'     => 'All rights reserved.',
		'footer_social'    => '',
	);
}
