# antradusai.com website

Source for the **Antradus** WordPress theme that powers
[antradusai.com](https://antradusai.com/) - the marketing and publishing site
for Antradus AI, the podcast-to-article system for WordPress.

Current version: **2.11.0**

## What is here

```
antradus/     the theme source (install this)
```

The theme replaces every WPCode snippet the site used to run: header and
footer, home page, pricing, plugin features, blog index, single-post design,
contact and affiliate page, welcome page, and the gallery carousel. Nothing
needs a snippet, a page builder, or a code editor.

Every word and image on the marketing pages is edited from
**Appearance -> Antradus Content** in wp-admin, in **English and Arabic**.

## Installing

Build the zip with the script below, then in wp-admin go to
**Appearance -> Themes -> Add New -> Upload Theme**, choose the zip, install
and activate. Full instructions, the settings reference and the architecture
notes live in [`antradus/README.md`](antradus/README.md).

```sh
c:/xampp/php/php.exe build-zip.php
```

**Build it with that script, never with Explorer's "Send to → Compressed
folder" or PowerShell's `Compress-Archive`.** Both write zip entries with
backslashes (`antradus\style.css`), and WordPress unpacks with PHP, which needs
forward slashes — so it cannot find the stylesheet and rejects the upload with
*"The theme is missing the style.css stylesheet."* The archive looks perfectly
fine in Explorer, which is what makes it a trap.

The script verifies the finished file the way WordPress does — it unpacks it,
checks that exactly one folder is at the top, and reads the theme header out of
the `style.css` inside it — and refuses to leave a package that would be
rejected. It prints the size and a sha256 of what it built:

```
antradus-theme.zip
  Antradus 2.11.0
  60 files, 212,461 bytes
  sha256 7aecbeb6…
  verified: unpacks to one folder 'antradus' with a readable style.css
```

If an upload ever fails again, compare that byte count against the file the
browser is about to send. They have to match — and note that GitHub's green
**Code → Download ZIP** button gives you the whole *repository*, which is not a
theme and will be rejected with that exact message.

## Notes

- Language is switched with `?lang=ar` - no rewrite rules and no cookie.
- Posts are not translated, they are tagged: a post tagged `ar` shows on the
  Arabic blog only. Documentation is forced to English.
- `antradus-theme.zip` is a build artifact and is deliberately not tracked.

## License

GPL-2.0-or-later, the same as WordPress.
