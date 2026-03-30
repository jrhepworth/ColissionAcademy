# Collision Academy Theme

Collision Academy is a custom WordPress theme for a specialist publication covering forensic collision investigation, vehicle technology, and expert witness practice.

The theme is intentionally custom-built. There are no page builders, no Full Site Editing theme dependencies, and no plugin lock-in for the front-end.

## Design Direction

This theme uses a light visual system rather than dark mode.

Light mode was chosen for three practical reasons:

1. Long-form technical reading is easier and less fatiguing on a light background for most users, especially during office hours.
2. Print output is cleaner and cheaper, which matters for a professional/legal audience.
3. The combination of off-white surfaces, deep blue accents, and restrained typography feels precise and credible without becoming sterile.

Typography choices:

- `IBM Plex Sans` for interface text, headings, navigation, cards, and UI labels.
- `Source Serif 4` for article bodies and long-form reading.

Colour direction:

- Warm off-white background for a premium editorial feel.
- Deep blue accent for authority and trust.
- Neutral dark ink for legibility.

## Minimum Requirements

- WordPress 6.4 or newer
- PHP 8.1 or newer
- MySQL 8.0+ or MariaDB 10.4+
- HTTPS enabled before WordPress is installed

## Theme Contents

Core templates:

- `style.css`: WordPress theme header and metadata.
- `functions.php`: theme setup, asset loading, database creation, cron scheduling, AJAX registration, security helpers.
- `index.php`: WordPress fallback template.
- `front-page.php`: homepage layout.
- `archive.php`: blog / article archive.
- `single.php`: single article layout.
- `category.php`: category archive.
- `tag.php`: tag archive.
- `page.php`: generic static page template.
- `page-about.php`: About page template.
- `page-contact.php`: Contact page template.
- `search.php`: styled search results.
- `searchform.php`: reusable custom search form.
- `404.php`: not-found page.
- `header.php`: site header, sticky nav, search toggle, progress bar container.
- `footer.php`: footer, footer newsletter CTA, footer nav.
- `sidebar.php`: optional archive sidebar.
- `comments.php`: custom WordPress comments output and form.

Assets:

- `assets/css/main.css`: all front-end styling including responsive rules and print styles.
- `assets/js/main.js`: mobile nav, expandable search, reading progress bar, AJAX forms, copy-link behaviour.
- `assets/images/og-default.svg`: placeholder Open Graph image. Replace with a 1200×630 PNG or JPG before launch for best social sharing compatibility.

Feature modules:

- `inc/helpers.php`: reading time, pagination, category helpers, shared utilities.
- `inc/cpt.php`: `course` and `resource` CPT registration plus temporary “Coming Soon” output.
- `inc/newsletter.php`: newsletter handling, contact form handling, admin subscribers screen, CSV export.
- `inc/audit-log.php`: audit log helpers and admin audit screen.
- `inc/seo.php`: canonical URLs, Open Graph/Twitter tags, Plausible hook.

Reusable template parts:

- `template-parts/card-post.php`: article card component.
- `template-parts/hero.php`: homepage hero section.
- `template-parts/author-bio.php`: author box.
- `template-parts/newsletter-form.php`: reusable newsletter signup form.
- `template-parts/related-posts.php`: related articles block.

Beginner helpers:

- `setup-categories-wp-cli.txt`: optional WP-CLI commands to create the recommended launch categories.
- `sample-first-post.txt`: sample launch article content to paste into WordPress and test the layout.

## Important Settings Inside The Theme

Open `functions.php` if you want to change these defaults:

- `COLLISION_ACADEMY_VERSION`: theme version for cache busting.
- `CA_SIDEBAR_ENABLED`: set to `true` if you want the sidebar on archive pages.
- `COLLISION_ACADEMY_IP_SALT`: define this in `wp-config.php` for IP hashing.
- `CA_PLAUSIBLE_DOMAIN`: define this in `wp-config.php` to enable Plausible Analytics.

## What The Theme Creates Automatically

When the theme is activated it creates:

- `wp_ca_subscribers`: newsletter subscribers table.
- `wp_ca_audit_log`: audit log table.
- Daily WP-Cron cleanup jobs for:
  - clearing subscriber `ip_hash` values after 30 days
  - deleting audit log records older than 90 days

## Security Notes

This theme includes:

- Nonces on newsletter and contact form submissions
- Honeypot fields on both public forms
- Rate limiting with WordPress transients: maximum 3 attempts per IP hash per hour per form
- Hashed IP storage only, never raw IP addresses
- Audit logging for failed submissions and CSV exports
- REST user endpoint restriction for logged-out visitors

Important GDPR note:

Raw IP addresses are personal data under UK GDPR. This theme stores a SHA-256 hash of the IP address plus a server-side salt instead. That allows abuse detection without storing the original address.

## Step-By-Step Installation Guide

### 1. Install WordPress

1. Buy hosting and a domain, or point the domain `collisionacademy.co.uk` to your hosting account.
2. Make sure SSL is active first. Your host will usually show a section called “SSL”, “Security”, or “Certificates”.
3. Use your host’s WordPress installer. It is often called “One-click install”, “WordPress Toolkit”, or “Apps”.
4. When asked for the site title, enter `Collision Academy`.
5. Create an admin username, a strong password, and an email address you can access.

What you should see:

- A WordPress dashboard with a dark left-hand menu.
- Menu items like `Posts`, `Pages`, `Appearance`, `Plugins`, and `Settings`.

### 2. Upload The Theme

You have two options.

Option A: upload as a ZIP

1. Zip the `collision-academy` folder.
2. In WordPress go to `Appearance > Themes`.
3. Click `Add New`.
4. Click `Upload Theme`.
5. Select the ZIP file and click `Install Now`.

Option B: upload manually by FTP or your host’s File Manager

1. Open your hosting file manager.
2. Go to `wp-content/themes/`.
3. Upload the entire `collision-academy` folder there.

### 3. Activate The Theme

1. Go to `Appearance > Themes`.
2. Find `Collision Academy`.
3. Click `Activate`.

What happens on activation:

- the custom database tables are created
- the scheduled cleanup tasks are registered
- WordPress rewrite rules are flushed

### 4. Install Recommended Plugins

Install these plugins from `Plugins > Add New`:

- `Yoast SEO`: easiest beginner-friendly SEO plugin.
- `Wordfence Security`: firewall, login protection, malware scanning.
- `UpdraftPlus`: automated backups.
- `LiteSpeed Cache` if your host uses LiteSpeed.
- `WP Rocket` if you want a premium all-in-one caching option.
- `W3 Total Cache` if you want a free caching option on non-LiteSpeed hosting.

### 5. Create The Required Pages

Go to `Pages > Add New` and create these pages:

- `Home`
- `Articles`
- `About`
- `Contact`
- `Privacy Policy`
- `Terms` if needed

Assign templates:

- `Home`: leave as default page template. WordPress will use `front-page.php` when this page is set as the homepage.
- `About`: choose `About Page`.
- `Contact`: choose `Contact Page`.
- `Privacy Policy`: use default template.

What you should see:

- In the right-hand page settings, there is a `Template` dropdown.
- Choose the matching template before publishing.

### 6. Set The Homepage And Posts Page

1. Go to `Settings > Reading`.
2. Under `Your homepage displays`, choose `A static page`.
3. Set:
   - `Homepage`: `Home`
   - `Posts page`: `Articles`
4. Click `Save Changes`.

### 7. Set The Permalink Structure

1. Go to `Settings > Permalinks`.
2. Choose `Custom Structure`.
3. Enter:

```text
/articles/%postname%/
```

4. Click `Save Changes`.

This gives article URLs like:

```text
/articles/post-slug/
```

### 8. Create The Navigation Menus

1. Go to `Appearance > Menus`.
2. Create a menu called `Primary Navigation`.
3. Add `Home`, `Articles`, `About`, and `Contact`.
4. Assign it to `Primary Navigation`.
5. Create a second simple menu for the footer if you want one.

### 9. Add The Recommended Categories

You can create them manually under `Posts > Categories`, or use the optional commands in `setup-categories-wp-cli.txt`.

Suggested launch categories:

- Vehicle Dynamics
- Crash Reconstruction
- Evidence & Documentation
- Legal & Expert Witness
- Vehicle Technology
- Site News

### 10. Create Your First Post

1. Go to `Posts > Add New`.
2. Enter a title.
3. Add your content.
4. Choose a category and optional tags.
5. Add a featured image.
6. Click `Publish`.

If you want ready-made sample content, open `sample-first-post.txt` and paste it into the editor.

### 11. View Newsletter Signups

1. In the left-hand admin menu, click `Collision Academy`.
2. The default screen is `Subscribers`.
3. You can view names, emails, and signup timestamps.
4. Click `Export as CSV` to download the list.

Every CSV export is recorded in the audit log.

### 12. View Audit Logs

1. In the left-hand admin menu, click `Collision Academy > Audit Log`.
2. You will see failed newsletter/contact attempts and CSV export events.
3. The audit log automatically deletes records older than 90 days.
4. There is also a manual purge button if needed.

### 13. Configure The Contact Form Email Address

The contact form sends messages to the WordPress admin email address.

To change it:

1. Go to `Settings > General`.
2. Update `Administration Email Address`.
3. Save changes and confirm the new email if WordPress asks you to.

### 14. Enable Plausible Analytics

Preferred method:

Add this line to `wp-config.php` above the “That’s all, stop editing” line:

```php
define( 'CA_PLAUSIBLE_DOMAIN', 'collisionacademy.co.uk' );
```

The theme will then output the Plausible script through `wp_head`.

Why Plausible is recommended:

- privacy-friendly by default
- no cookie banner normally required
- lightweight
- a better fit for a UK professional audience than heavier analytics plugins

### 15. Optional GA4 Alternative

If you prefer GA4, add your GA4 snippet through `wp_head` in a small custom plugin or a code-snippets workflow. Do not hardcode it directly into `header.php`.

### 16. Update WordPress And The Theme Safely

Before updates:

1. Take a full backup first.
2. If possible, test on a staging site.
3. Update plugins and WordPress core one at a time.
4. Re-test the homepage, articles, forms, and menus.

For theme updates:

1. Back up the site.
2. Upload the updated theme ZIP or replace the theme folder.
3. Clear any caches.
4. Re-test the site front end and the admin subscriber/audit screens.

## Beginner Checklist After Activation

- Set the site title to `Collision Academy`
- Set the tagline to `Forensic Collision Intelligence`
- Upload a custom logo if you want one
- Set homepage and posts page
- Set permalink structure
- Install the recommended plugins
- Create categories
- Publish the first article
- Add a featured image
- Set the privacy policy page under `Settings > Privacy`

## Accessibility Notes

The theme includes:

- visible focus states
- keyboard-accessible menus and search toggle
- semantic headings and structured content areas
- ARIA labels on navigation, search, and icon-only controls
- responsive tap targets

## Notes For Search

Launch search uses WordPress native search.

If search quality becomes a concern later, possible phase-two upgrades are:

- `ElasticPress` for Elasticsearch-backed relevance
- `Algolia` for hosted instant search

No code changes are required right now to keep those future options open.

## Caching And Performance Recommendations

Recommended setup:

- `LiteSpeed Cache` if your server is LiteSpeed
- `WP Rocket` if you want the easiest premium option
- `W3 Total Cache` if you want a free option
- `Cloudflare` free tier as an external CDN and caching layer

Cloudflare in plain English:

Cloudflare keeps copies of your site closer to visitors around the world. That means faster loading, some basic protection from bad traffic, and less strain on your server.

This theme is safe to use with caching plugins because it does not rely on PHP sessions or custom global state.

## Backups

Recommended plugin:

- `UpdraftPlus`

Recommended backup settings:

- daily schedule
- retain at least the last 7 daily backups
- include database, uploads, and theme files
- store backups offsite in Google Drive, Dropbox, or Amazon S3

If your host already includes daily backups:

- check how often they run
- check how many restore points they keep
- still consider UpdraftPlus for an extra offsite copy

## File Permissions

Safe common defaults:

- folders: `755`
- files: `644`

Make sure these remain writable where needed:

- `wp-content/`
- `wp-content/uploads/`

## XML-RPC Disable Step

If your host uses Apache with `.htaccess`, add this line:

```apache
RedirectMatch 403 /xmlrpc.php
```

If your host uses Nginx instead, ask the host to block `/xmlrpc.php` at the server level.

## Proxy And Rate Limit Note

The form rate limit uses `REMOTE_ADDR` intentionally. That is safer than trusting forwarded headers sent by visitors.

If you are behind a trusted reverse proxy such as Cloudflare and you want the real visitor IP to be used for rate limiting, configure that at the server level first. Do not change the theme code until the proxy headers are trusted and normalised correctly by the server.

## Editorial Guidance

Suggested writing standard:

- analytical
- evidence-based
- neutral in tone
- technically precise

Avoid:

- sensational headlines
- speculation without evidence
- casual or overly conversational phrasing

Recommended article structure:

1. introduction
2. methodology or context
3. findings
4. conclusion

Use diagrams, tables, and citations where they improve clarity.

## Notes On The Placeholder OG Image

The bundled `assets/images/og-default.svg` is a clean placeholder so the theme is complete out of the box.

Before launch, replace it with a proper 1200×630 PNG or JPG branded social card for best compatibility with LinkedIn, X, Slack, and other platforms.

## Notes On Minification

The theme ships with readable source files:

- `assets/css/main.css`
- `assets/js/main.js`

This was done so a beginner owner or developer can understand and edit the code more easily.

Before production launch you can optionally minify them as part of your deployment workflow, but it is not required for the theme to function.

## Extra Implementation Notes

The original brief did not explicitly list `searchform.php`, but it has been included to keep all WordPress search forms visually consistent across the site.

## Support Summary

If something looks wrong after installation, check these first:

1. Is the homepage set under `Settings > Reading`?
2. Is the posts page set to `Articles`?
3. Are permalinks saved as `/articles/%postname%/`?
4. Is the theme activated?
5. Is the site description set so the logo lockup shows the tagline correctly?
6. Is the admin email correct for the contact form?
7. Have you cleared caches after changing the theme?
