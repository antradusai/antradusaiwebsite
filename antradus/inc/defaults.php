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
		antradus_defaults_features(),
		antradus_defaults_pricing(),
		antradus_defaults_blog(),
		antradus_defaults_contact(),
		antradus_defaults_welcome(),
		antradus_defaults_docs(),
		antradus_defaults_footer(),
		antradus_defaults_security()
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

		// Slug overrides for the seven designed pages.
		'slug_home'         => 'home',
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
		'home_hero_badge'    => 'The first B2B podcast-to-article system for WordPress',
		'home_hero_title'    => "One episode in.\n*A publishing week out.*",
		'home_hero_sub'      => 'Antradus AI turns a single podcast episode into a finished, human-sounding article - plus show notes, chapters, quote cards, a newsletter draft and social posts. All inside WordPress, on your own API keys.',
		'home_hero_cta1'     => 'See pricing',
		'home_hero_cta1_url' => 'page:pricing',
		// Cleared: the hero carries one call to action. Fill this in and the
		// second button comes back - an empty label renders nothing at all.
		'home_hero_cta2'     => '',
		'home_hero_cta2_url' => 'https://wordpress.org/plugins/antradus-ai-lite/',
		'home_hero_note'     => 'Bring your own API keys - no markup, no per-word fees, no monthly seat.',
		'home_hero_image'    => '',
		'home_hero_chips'    => "Episode to article\nShow notes and chapters\nQuote cards\nNewsletter draft\nSEO and GEO ready",
		'home_hero_proof'    => 'Built on the Antradus AI plugin - Classic and Block editors, five AI providers, your own API keys.',

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
 * Footer
 * ========================================================================= */

/**
 * @return array
 */
function antradus_defaults_footer() {
	return array(
		'footer_tagline'   => 'The first B2B podcast-to-article system for WordPress. One episode in, a publishing week out - on your own API keys.',
		'footer_cta_title' => 'Ready to stop paying for episode write-ups?',
		'footer_cta_btn'   => 'See pricing',
		'footer_cta_url'   => 'page:pricing',
		'footer_cols'      => array(
			array(
				'title' => 'Product',
				'links' => "Features | page:features\nPricing | page:pricing\nDocs | page:docs\nDownload Lite | https://wordpress.org/plugins/antradus-ai-lite/",
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
