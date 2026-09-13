# antradusai.com website

Source for the **Antradus** WordPress theme that powers
[antradusai.com](https://antradusai.com/) - the marketing and publishing site
for Antradus AI, the podcast-to-article system for WordPress.

Current version: **2.13.0**

## What is here

```
antradus/     the theme source (install this)
```

The theme replaces every WPCode snippet the site used to run: header and
footer, home page, the two audience pages (for publishers, for studios),
pricing, plugin features, docs hub, blog index, single-post design, contact and
affiliate page, welcome page, and the gallery carousel. Nothing needs a
snippet, a page builder, or a code editor.

It also designs `/transcript-extractor/`, the page for the **Transcript
Extractor** Chrome extension - a separate product from the plugin, sold on its
own through Freemius, with its own settings tab, plan cards and structured data.

Every word and image on the marketing pages is edited from
**Appearance -> Antradus Content** in wp-admin, in **English and Arabic**.

## Installing

Zip the `antradus/` folder, then in wp-admin go to
**Appearance -> Themes -> Add New -> Upload Theme**, choose the zip, install
and activate. Full instructions, the settings reference and the architecture
notes live in [`antradus/README.md`](antradus/README.md).

```sh
zip -r antradus-theme.zip antradus
```

On Windows, do not use PowerShell's `Compress-Archive`: it writes entries with
backslashes, and WordPress then reports that the theme is missing its
`style.css`. Any tool that writes `antradus/style.css` with a forward slash works.

## Notes

- Language is switched with `?lang=ar` - no rewrite rules and no cookie.
- Posts are not translated, they are tagged: a post tagged `ar` shows on the
  Arabic blog only. Documentation is forced to English.
- `antradus-theme.zip` is a build artifact and is deliberately not tracked.

## License

GPL-2.0-or-later, the same as WordPress.
