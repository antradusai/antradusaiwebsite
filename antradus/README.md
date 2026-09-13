# Antradus theme

The marketing and publishing theme for antradusai.com. It replaces every WPCode
snippet the site was running: the header and footer, the home page, the pricing
page, the plugin-features page, the blog index, the single-post design, the
contact and affiliate page, the welcome page, and the gallery carousel.

**Nothing here needs a snippet, a page builder, or a code editor.** Every word
and picture on the marketing pages is edited from **Appearance → Antradus
Content** in wp-admin, in **English and Arabic**.

---

## Installing

1. Use the `antradus-theme.zip` supplied alongside the folder, or rebuild it
   with `c:/xampp/php/php.exe build-zip.php`.

   > **Do not zip it with PowerShell's `Compress-Archive`.** Windows PowerShell
   > writes zip entries with backslashes (`antradus\style.css`), the zip format
   > requires forward slashes, and PHP — which is what WordPress unpacks the
   > upload with — then cannot find `antradus/style.css`. The upload fails with
   > *"The theme is missing the style.css stylesheet"* even though the file is
   > plainly there. The archive looks perfectly normal in Explorer, which is
   > what makes it a trap. `build-zip.php` writes forward slashes and checks
   > that entry exists before it reports success.

2. **Appearance → Themes → Add New → Upload Theme**, choose the zip, install,
   activate.
3. Go to **Appearance → Antradus Content**. The first tab, **Pages**, lists the
   ten pages the theme designs and tells you which of them exist. If any are
   missing, one button creates them as drafts.
4. **Settings → Reading**: set the front page to your Home page.

### Updating it later

**Uploading a new version of the theme cannot lose your content.** Your wording,
your plans and your Freemius setup live in the WordPress database; your images
live in the Media Library. Neither is part of the theme folder, so replacing the
folder replaces code and nothing else. There is nothing to back up first and
nothing to re-enter afterwards.

The only buttons that can replace your content are **Import**, **Reset** and
**Restore that page**, all on the Tools tab — and each takes a snapshot first,
which the **Undo** button on the same tab puts back.

There is one thing an update genuinely cannot do on its own: **change wording you
have already saved.** Once you save a tab, those words are yours and no update
overwrites them — which is what you want almost every time. The exception is
when the shipped wording changes because the product did: new plans, new prices,
a new feature list. **Tools → Bring back the shipped wording for one page** puts
a single page back to what the current version ships, in both languages, and
leaves every other page alone. It restores words only: your pictures, your
Freemius details, your form shortcodes and your page addresses are not touched.

---

## The one behaviour worth understanding

**A page only appears on the site once it is published.**

The theme knows ten pages: Home, Plugin features, Pricing, Docs, Blog,
Contact, Welcome, For publishers, For studios and Transcript Extractor. Every menu item, footer link and call-to-action button that
points at one of them is resolved through a single function. While a page is a
draft that function returns nothing, and the link is not rendered at all — not
greyed out, not broken, simply absent.

So you can write the Pricing page over three days without a half-finished page
being reachable, and publishing it puts every link back on its own. The **Pages**
tab shows the live status of all ten at a glance.

Buttons you configure yourself take part in this too: write a link target as
`page:pricing` rather than a full URL and it inherits the same behaviour.

---

## Two languages

The site is published in English and Arabic. There is no second page tree and no
translation plugin. The reader switches with the control in the header, which
adds `?lang=ar` to the address; every link the theme prints carries the language
forward, and `hreflang` tags tell search engines the two addresses are the same
page. Arabic pages are laid out right to left and set in IBM Plex Sans Arabic.

**The structure is shared; only the words are translated.**

| Shared, edited once on the English tab | Translated per language |
|---|---|
| Links and button targets | Every heading, paragraph and label |
| Images and logos | Feature lists and bullet points |
| Colours and the content width | Plan names, prices as written, badges |
| Freemius IDs, page slugs, licence counts | Table rows, questions and answers |
| Which plans exist, and in what order | **Form shortcodes** — one form per language |

That split is why adding a pricing plan is a one-time job: the plan exists in
both languages the moment you create it, untranslated at first — which is
visible and fixable — rather than missing from one of them.

**Right to left is done with logical CSS, not a mirrored stylesheet.** Edges,
padding and alignment are written as `inline-start` / `inline-end` and
`text-align: start`, so they follow the direction of the page instead of being
re-stated for Arabic in a second file. `rtl.css` is kept for the handful of
things direction alone cannot decide — prices and product names that must read
left to right inside an Arabic sentence, and the two diagram columns.

**Appearance → Antradus Content → العربية** switches the whole screen into
translation mode. Only the fields worth translating are shown, each with the
English underneath it, and the counter in the language bar tells you how much is
left. **A field you leave empty falls back to English on the site**, so a
half-finished translation still reads properly.

The theme's own words — "Skip to content", "3 min read", the menu labels — are
in `inc/strings-ar.php`, a plain PHP array rather than a compiled `.mo`, so
correcting one is editing a line.

### Publishing Arabic, or not yet

**Arabic ships switched off.** That is the state a site is really in on launch
day: the English pages are finished and the translation is not. Go live in
English, finish the Arabic afterwards, and turn it on when it is ready.

The switch is **Appearance → Antradus Content → Brand & header → Languages**.

While it is off:

- the language button is gone from the header and the footer;
- `?lang=ar` **redirects** to the English address — a 302, because the language
  is coming back. Rendering English at a second address instead would leave the
  same page on two URLs for a search engine to pick between;
- no `hreflang` tag advertises an Arabic version, so nothing is crawled that
  answers in the wrong language.

**Nothing is deleted, and nothing stops.** The Arabic tab and every word already
written on it stay exactly where they are, the counter keeps counting, and the
"not published" chip in the language bar says so on every tab so a long
translating session never forgets where the words are going.

**You can still read the whole site in Arabic while signed in.** An
administrator following `?lang=ar` gets the real Arabic page, at the real width,
laid out right to left — with an unmissable strip across the top saying it is a
preview and that visitors are being sent to the English page. Proof-reading a
translation in a settings table is not the same as reading it as a page, and
this is the difference between the two.

Turning it on publishes every Arabic page at once. There is no per-page state
and nothing to migrate.

---

## Editing content

**Appearance → Antradus Content**, one tab per page.

- **Headings**: wrap a phrase in `*asterisks*` to set it in the accent style —
  `See what Antradus *creates* from one episode`. In English that is the serif
  italic; in Arabic, which has no italic, it becomes the same face in the brand
  colour.
- **Clearing a heading hides its whole section.** That is how you remove a
  section you do not want.
- **Section order.** The Publishers and Studios tabs open with a *Section
  order* list: the sections of that page, top to bottom, each with a pair of
  arrows. The hero is always first and is not in the list. Reordering never
  turns a section on — one with an empty heading still prints nothing — so this
  decides arrangement only, and it is shared by both languages because an order
  is structure rather than words. A section added to the theme later appears in
  the list on its own, at the bottom, without anyone having to re-save.
- **Images**: every image slot has a media picker. An empty slot renders a
  labelled dashed placeholder on the site naming the setting that fills it, so
  an unfinished page tells you what it needs instead of showing a broken frame.
- **Every picture on the site is served at the size it was uploaded.** Not the
  `large` copy, not the `medium` one, and not the `-scaled` copy WordPress
  quietly makes of anything wider than 2560px — the file itself, with no
  `srcset` offering the browser anything smaller. That holds for the theme's
  own image slots, post featured images, gallery carousels and pictures inside
  an article alike, and it is the opposite of what a photography site wants.
  Nearly every picture here is a screenshot of an interface: the point of it is
  the text inside it, and a 300px-wide copy stretched across a card is mush.
  Reading beats bytes on this site — so upload pictures at the size you want
  them read at, because nothing downstream will shrink them for you. wp-admin
  is unaffected: the settings screen, the media library and the block editor
  still use the small copies.
- **A picture on its own is shown whole, at its own shape.** Hero pictures,
  the demo frame, the GEO and trends illustrations, the Features and newsletter
  pictures — none of them is cropped to fit a frame, so a wide screenshot stays
  wide and a tall one makes its panel taller. Two things still hold a shape on
  purpose: a **slider**, whose slides are stacked in one box and need a height
  before the second picture has loaded, and a **card in a grid** — feature
  cards, group pictures, article thumbnails — where the pictures line up with
  each other and one odd shape would break the row. The ratio on an empty slot
  is what the placeholder is drawn at; it was never a promise about your file.
- **The hero picture on Home, Publishers and Studios is a slider.** The slot is
  called *Hero pictures* and takes up to eight: add one and it is the still
  picture it always was, add a second and the frame starts fading between them
  every six seconds, with arrows, dots, swipe and the arrow keys. The
  thumbnails in wp-admin are the slide order — move one with its arrows and the
  page follows. It stops advancing whenever nobody is watching: the pointer is
  on it, something in it has focus, the tab is in the background, it has been
  scrolled past, or the reader asked their system for less motion.
- **A caption under a slide** is the picture's own caption, written in the media
  library rather than in a settings field, so it travels with the file. Slides
  without one show nothing and the row keeps its height, so the page never
  jumps between slides. Because it belongs to the file and not to the page, it
  is the *same* caption in both languages — an Arabic page shows the caption as
  it was written, which is worth knowing before you write one in English.
- **Click a thumbnail** in *Hero pictures* and WordPress's own media modal opens
  on that picture — which is where you write its caption, alt text and title.
  That is the point of clicking it: the caption a slide shows belongs to the
  file, so it is edited where the file is, and there is no second field here to
  disagree with it. Pick a *different* picture before closing and it replaces
  that one, keeping its place in the slide order.
- **Clicking a slide on the site** opens it in a lightbox at the size it was
  uploaded, with the rest of the slot's pictures behind the arrows — so a reader
  who wants a proper look at slide two can go on to three without closing it.
  Escape, the close button or a click outside brings them back, and the slider
  carries on from whichever slide they were looking at.
- **A slider loads one slide at a time.** Full-resolution pictures cost weight,
  and stacking eight of them in one frame would spend it all at once —
  `loading="lazy"` is no help there, because every slide is in the viewport from
  the first frame. So only the first slide has an address in the HTML, and the
  script fills in each of the others just before it is needed.
- **Lists** (feature bullets, trust lines, footer links) are one item per line.
  Footer and menu links are `Label | target`.
- **A picture on a feature group.** On the Publishers and Studios tabs, every
  group under *What it does for them* has its own **Picture**, shown across the
  top of that card. Leave it empty and the card looks exactly as it did before,
  so you can illustrate one group without having to illustrate all six. Like
  every other image here it is shared by both languages — you choose it once on
  the English tab and the Arabic page shows the same picture, because a
  screenshot is not translated.
- **The trends band** on an audience page (*Writing from what is trending*) is
  two lists: what the feature does, and the numbered steps of how it goes. The
  steps number themselves from their order, so reordering them renumbers the
  rest. Clear its heading to leave the band off a page — today only the
  publisher page carries it.
- **The in-and-out diagram** on an audience page works like the home page's,
  under that page's own *What it plugs into* section, so the studio page can
  list the podcast hosts and leave out the keyword sources. Clear its heading
  to leave the diagram off that page.
- **Forms** are a shortcode you paste, and **each language has its own**. An
  Arabic form has Arabic labels, Arabic error messages and an Arabic
  confirmation email — it is a different form, not a translation of one, so the
  shortcode lives on the العربية tab beside the words. Leave it empty there and
  the Arabic page falls back to the English form rather than showing none.
  Contact, affiliate and welcome forms all work this way.

Saving one tab never touches the others, and saving one language never touches
the other.

---

## Moving a site to another site

**Antradus Content → Tools → Move this site to another one.**

Download the file, upload it on the other site's Import box. It carries both
languages, every plan, and — with the box ticked — **the images themselves**,
encoded inside the file.

That last part is the point. An image setting stores an attachment ID, and ID 42
on your laptop is a different picture on the live server. Carrying the actual
bytes is also what makes the journey people actually make work: a live server
cannot fetch `http://localhost/…/logo.png`, so a URL-only export fails on
exactly the transfer you wanted it for.

On arrival each image is, in order: reused if a previous import already brought
it across, reused if it is already in the Media Library, written in from the
embedded bytes, downloaded from its URL, or — failing all of that — left as a
link to the other site, which still displays. The confirmation notice says which
happened, and warns you about any that ended up as links.

Files over 2 MB are left as URLs rather than embedded, so building an export
cannot exhaust PHP's memory.

---

## Pricing plans

**Antradus Content → Pricing → The plans.**

Each plan is a card you can add, reorder or delete. The order in the settings is
the order on the page, and the same plans render on the home page.

Per plan you set the name, the price, the billing line, the site chip, the
feature list, and what the button does:

- **Go to a link** — an ordinary link, including `page:contact`.
- **Open the Freemius checkout** — either fill in the plan's Freemius ID and
  set the product ID and public key once on the same tab, **or paste the code
  block Freemius gives you** straight into the plan and let the theme read the
  IDs out of it.

The pasted snippet is never executed and never printed to the page. The theme
reads four values from it — `product_id`, `plan_id`, `public_key`, `image` — and
writes its own checkout call from them. Running pasted JavaScript would make the
settings screen a way to put arbitrary script on the pricing page.

Two things in that block are Freemius's own sample values and are deliberately
ignored: the `licenses` number, which belongs to their example and not to your
plan, and the `your-plugin-site.com` logo, which is a domain nobody owns and was
drawing a broken image at the top of the checkout. Your product icon is used
instead.

Because each plan can carry its own snippet, two plans on one page can sell two
different Freemius products.

A checkout button is a *real link to the hosted Freemius checkout page* before
any JavaScript runs. The overlay is only used when the library is genuinely
loaded. If an ad blocker eats it, or an optimizer defers it, or the script never
arrives, the click follows the link and the customer still reaches a real
checkout. That ordering is deliberate — it is the failure that took the old
pricing snippet down in August 2026.

⚠️ A plan ID that does not exist in your Freemius dashboard will render
perfectly and then fail at checkout. That is the one thing to double-check.

⚠️ If the overlay opens and says **"Invalid pricing, please make sure the
pricing, licenses or the currency is valid"**, the plan was asked for a number
of sites it is not sold in. Freemius validates the price and the quantity
together, so a plan sold as "up to 5 sites" has no one-site price. Put that
number in the plan's **Licences** field — or leave the field empty, which sends
no count at all and lets the plan sell at whatever price it has. Empty is the
right answer for a plan with a single price; it is also what every plan set up
before theme 2.3.1 gets, because the count used to be hard-coded to 1.

### The free trial

**A trial only happens when the button asks for one.** Setting a trial on the
plan inside Freemius is not enough — the checkout charges the full price today
unless it is opened in trial mode. That is what the **Free trial** dropdown on
each plan does:

| Setting | What the customer sees |
|---|---|
| No trial — charge today | "Today's total $275.00" |
| Free trial, card required | "7-day free trial… Today's total $0.00", card taken, billed when the trial ends |
| Free trial, no card | The trial starts with no card at all |

Pick a trial the plan actually has in Freemius. A card that promises seven free
days beside a checkout that charges immediately is the worst of the three
states, and it is the state every plan is in until this dropdown is set — plans
saved before theme 2.3.1 default to "charge today", because an update never
overwrites settings you have already saved.

**Where the trial goes** is the next field, **Trial link under the button**:

- **Leave it empty** and the trial is *on the button*. One button, one path: it
  says "Start the free trial" and it opens the trial checkout.
- **Write words in it** — "or start a 7-day free trial" — and the trial moves
  *off* the button onto a quieter link underneath. The button then buys the plan
  outright, so "Book now" charges today and the link beside it starts the trial.
  Two checkouts of the same plan, and the reader picks.

That is what the shipped Publisher card does. Both are real links to the hosted
checkout before any JavaScript runs, and both fall back to it if the overlay
never loads.

⚠️ Whichever you choose, **the note under the button has to match**. It is the
line customers read last and quote back at you.

### The comparison table

Two columns or three. Give the **third column a heading** and it appears, using
each row's third answer; leave that heading empty and the table has two columns
and the third answer is ignored. The **second** column is the highlighted one —
put the plan most people choose there.

---

## The Transcript Extractor page

`/transcript-extractor/` sells a different product from every other page: the
Chrome extension that reads Spotify and YouTube transcripts, sold on its own
through Freemius. It has its own tab, **Antradus Content → Transcript
Extractor**, in both languages, and its own row on the SEO tab.

- **It is not in the main menu.** The menu belongs to the plugin. The page is
  linked from the footer's Product column, and from anywhere you write
  `page:transcript`. A site that saved its Footer tab before 2.13.0 keeps its
  own columns, so add `Transcript Extractor | page:transcript` to one yourself.
- **The plan buttons go to the Chrome Web Store, never to a checkout.** A plan
  is bought from the Plans screen inside the extension, where Freemius opens
  with the buyer's Google email locked - and that email is how the purchase
  finds the account. A checkout opened from this page under any other address
  would take the money and unlock nothing. The cards use the Pricing page's
  design, but their rows live on this tab and have no Freemius fields.
- **The footer's closing call to action is left off this page**, because it
  sells the plugin.
- **Until you choose pictures, the page draws them.** The hero shows a drawing
  of the side panel, and the speaker section shows a Spotify transcript before
  and after the names. Both are built from the theme's colours. Add a
  screenshot to either slot and it takes the drawing's place.
- **Structured data.** The page prints `SoftwareApplication` - the product
  name, the install link and one offer per plan card - built from the tab, so a
  price changed there changes here. A `BreadcrumbList` is added only on a site
  with no SEO plugin, because Rank Math and Yoast print their own. No rating is
  printed: there are no reviews on this site to rate it with.
- **The facts are the extension's.** Three free transcripts, 50 and 150 a
  month, 2 and 4 hours, the prices and the 30-day free re-export all come from
  the extension's backend and its Freemius plans. When they change, change the
  tab - or, after a theme update that ships the new numbers, use **Tools →
  Bring back the shipped wording** on this page.

---

## Your posts

Nothing about your existing posts changes — only how they are presented.

**Which language a post belongs to is decided by one tag.** Articles are not
translated field by field the way the marketing pages are — an article is
written once, in one language. Tag a post **`ar`** and it appears on the Arabic
blog and nowhere else; leave the tag off and it appears on the English blog.
There is no third state, nothing to keep in sync, and moving a post between the
two is adding or removing that tag.

That rule applies everywhere posts are listed: the index, the category chips
(so Arabic never offers a chip that leads to an empty page), the search, and the
"more to read" rail under an article.

- **Blog index**: server-side search, category chips with counts, and
  pagination. Every state is a shareable URL: `?q=`, `?topic=`, `?pg=`.
- **Single post**: a white reading sheet on tinted glass, with a "more in this
  category" rail that follows Yoast's or Rank Math's primary category when one
  is set. Quotations are set as pull quotes — serif, display size, on a tinted
  sheet with an accent rule.
- **Galleries**: any gallery of two or more images — block or classic
  `[gallery]` — becomes a carousel with arrows, a caption and a thumbnail strip.
- **Docs**: the theme renders the hub itself from the guides the Antradus AI
  plugin publishes — grouped by category in the plugin's own order, with a
  search box that filters as you type and still works without JavaScript. A
  single guide keeps the plugin's contents list down one side and has nothing on
  the other. **The documentation is English only.** The guides arrive from the
  plugin in English, so the hub and every guide render in English and left to
  right whatever the rest of the site is set to. The Docs link stays in the
  Arabic menu — someone looking for documentation should be able to find it —
  and on those pages the language switch offers the Arabic *home* page, because
  there is no Arabic version of the page you are on.

**Comments are off**, everywhere, in all five places they have to be off: the
discussion setting, `comments_open()`, the form, the two endpoints that accept a
post, and the admin menu. No existing comment is deleted — they are hidden, and
the checkbox on the Security tab brings them back exactly as they were.

---

## Search metadata

The **SEO** tab holds a focus keyword, a search title and a meta description for
each of the ten designed pages, in both languages. They ship written rather
than blank, because a description nobody got round to writing is the usual
reason a page goes live with the first two lines of its hero in the search
results.

The titles are kept under about 60 characters and the descriptions under about
155, which is roughly where Google stops reading. Each focus keyword appears in
its own title and description, and — where the page's address already carries
the phrase, as `/pricing/` and `/for-studios/` and `/plugin-features/` do —
in the URL too.

### The button

**SEO → Send these to Rank Math** copies the English set onto the ten pages as
Rank Math's own post meta: `rank_math_focus_keyword`, `rank_math_title`,
`rank_math_description`. Nothing is stored twice and nothing has to be taught to
Rank Math — the metabox shows the values, the analysis scores them and the front
end renders them, exactly as if they had been typed in by hand. **From that
point on they are Rank Math's**, and this theme prints nothing at all on an
English page.

There are two buttons because there are two intentions:

| Button | What it does |
|---|---|
| **Send these to Rank Math** | Fills only the fields Rank Math has left empty. Anything you have already written there survives. |
| **Replace what is already there** | Overwrites all ten pages with the wording on this tab. |

The table above the buttons shows, before you press either, which pages are
ready, which already hold wording of your own, and which do not exist yet — a
page has to exist before it can be given metadata, so create the missing ones on
the Pages tab first.

Rank Math does not have to be active. The values are written as the meta Rank
Math reads, so activating it later picks up all of them at once, and until then
the theme prints the description and the sharing tags itself.

### The Arabic half

Rank Math has nowhere to put it. Both languages live at the same post —
`/pricing/` and `/pricing/?lang=ar` — and post meta has room for one title and
one description. So the Arabic set stays on this tab, and the theme serves it on
Arabic pages through the SEO plugin's own filters (Rank Math and Yoast are both
handled).

It also **corrects the canonical**, which matters more than it sounds. Every
canonical is generated from the permalink, and the permalink of
`/pricing/?lang=ar` is `/pricing/`. Left alone, every Arabic page would tell
search engines that the real page is the English one — which is the instruction
to drop the translation from the index. On a published Arabic page the canonical
now points at itself.

None of it does anything while Arabic is switched off.

---

## Security

Everything on the **Security** tab is on, and none of it changes what a reader
sees. In short: the account list is not published (`?author=1` and the REST users
endpoint), XML-RPC answers 403, failed sign-ins get one vague message and a pause
after ten tries, the standard response headers are sent, and the WordPress
version is not announced. The theme also switches off the built-in file editors
by defining `DISALLOW_FILE_EDIT`.

There is deliberately **no Content-Security-Policy**: on a site running eight
plugins a strict policy breaks something on day one and gets switched off, which
is worse than never having had it.

Two things the theme cannot do and your host can: refuse to run PHP inside
`wp-content/uploads`, and put HTTPS in front of everything.

---

## Design

White, wide and glassmorphic. Section backgrounds always run the full width of
the screen; only the content inside them is constrained, and that width is a
setting (1320px up to edge-to-edge, defaulting to 1560px). Articles keep their
own reading measure whatever you choose, because a 1800px line of text is not
readable.

The compatibility diagram carries a **light travelling along each curve** —
inward from every source into the hub, outward from the hub to every
destination — so the picture shows the direction things move rather than
stating it. It runs only while the diagram is on screen, and not at all for
anyone who has asked their system for less motion.

**The order of the home page's sections is `template-parts/home.php`** — one
line per section, top to bottom. Moving a section is moving its line; a section
whose heading you have cleared renders nothing wherever its line sits.

The whole palette derives from **one** accent colour in the Brand tab. Change
the blue there and the buttons, gradients, glows, icon tiles, focus rings and
tinted bands all follow — there is no second place to edit.

---

## Files

```
functions.php            setup, assets, the derived palette, navigation
inc/defaults.php         every shipped string in English - the content
inc/defaults-ar.php      the same keys, in Arabic. Edit the two side by side
inc/i18n.php             which language, which fields translate, how they merge
inc/strings-ar.php       the theme's own interface words in Arabic
inc/helpers.php          options, the ten pages, images, render helpers
inc/images.php           the front end serves the file you uploaded, never a preset
inc/settings-schema.php  what the settings screen contains
inc/settings.php         rendering and sanitizing that schema
inc/security.php         the hardening, and the one capability check
inc/transfer.php         export and import, including carrying the images
inc/comments.php         comments, switched off in all five places
inc/pricing.php          plan cards, comparison table, FAQ, checkout wiring
inc/blog.php             the article index
inc/docs.php             the documentation hub and its search
inc/seo.php              the ten pages' search metadata, the Rank Math button,
                         and the Arabic half no SEO plugin has room for
inc/gallery.php          galleries to carousel
template-parts/          one file per home section, one per designed page
assets/css/theme.css     the design system
assets/css/rtl.css       right-to-left, loaded only on Arabic pages
assets/js/theme.js       menu, anchors, the hero slider and its lightbox, the
                         diagram's flow lines, docs search, checkout
```

Adding a field is three edits: a default in `inc/defaults.php`, its Arabic twin
in `inc/defaults-ar.php`, and an entry in `inc/settings-schema.php`. The screen,
the sanitizer, the translation editor and the stored option all follow from the
schema — a field that is not in it cannot be saved.

Whether a field is translated or shared is decided in one place,
`antradus_global_field_keys()` in `inc/i18n.php`: anything ending in `_url`,
`_target` or `_link`, anything starting with `slug_`, and a short named list.

A field of type `images` holds several pictures in one setting, stored as one
comma-separated list of attachment IDs so it travels through the same option
row, sanitizer and export as every other field. `antradus_slider()` renders it,
and it renders a slider only from the second picture onwards — one picture is
still one picture, with no arrows and no script. The comma is the separator, so
it can never be part of a value.
