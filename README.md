![WordPress](https://img.shields.io/badge/WordPress-6.x-21759B?logo=wordpress&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-7.x-21759B?logo=wordpress&logoColor=white)

![PHP](https://img.shields.io/badge/PHP-8+-777BB4?logo=php&logoColor=white)
![ACF](https://img.shields.io/badge/ACF-Pro-orange)
![Node.js](https://img.shields.io/badge/Node.js-18.x-339933?logo=node.js&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white)
![SCSS](https://img.shields.io/badge/SCSS-CSS-CC6699?logo=sass&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)

# WordPress SZ Starter Theme

A modern, performance-first WordPress starter theme built with **Vite**. It is designed from the ground up to score well on Core Web Vitals, works out of the box with **ACF (Advanced Custom Fields)**, and includes ready-to-uncomment integration points for **Yoast SEO** and **Gravity Forms**.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Getting Started](#getting-started)
- [Build System (Vite)](#build-system-vite)
- [Project Structure](#project-structure)
- [Performance Optimizations](#performance-optimizations)
  - [CSS & JS Loading Strategy](#css--js-loading-strategy)
  - [Automatic WebP Conversion](#automatic-webp-conversion)
  - [The `rrp()` Responsive Picture Helper](#the-rrp-responsive-picture-helper)
  - [Analytics / GTM Loading Strategy](#analytics--gtm-loading-strategy)
  - [Other Speed & Cleanup Optimizations](#other-speed--cleanup-optimizations)
- [ACF Integration](#acf-integration)
- [ACF Blocks](#acf-blocks)
- [Yoast SEO Notes](#yoast-seo-notes)
- [Gravity Forms Notes](#gravity-forms-notes)
- [Theme Customizer](#theme-customizer)
- [Personalized Style for Admin Login Page](#personalized-style-for-admin-login-page)
- [License](#license)

## Features

- ⚡ **Vite-powered build pipeline** — SCSS/JS compilation, code splitting, source maps, and static asset copying.
- 🚀 **Performance-first architecture** — critical CSS inlining, async/preloaded stylesheets, deferred/module JS, idle-loaded analytics.
- 🖼️ **Automatic WebP conversion** for every uploaded JPG/PNG, served through a single, simple helper function.
- 🧩 **ACF-ready** — Some kind of "Gutenberg style" for ACF blocks, with per-block asset loading.
- 📊 **Privacy/performance-conscious GTM implementation** loaded only when the browser is idle or the user interacts with the page.
- 🧹 **WordPress cleanup out of the box** — disabled emojis, shortlinks, RSD/WLW links, comments support, XML-RPC clutter, and more.
- 🔐 **Custom admin login screen** (`my-admin/`).
- 🛠️ Sensible defaults for Customizer (logo, social links), custom widgets, nav menus, and accessible (WCAG) menu markup.

## Requirements

- WordPress 6.x+
- PHP 8.0+ (uses modern syntax such as `str_contains`, `str_ends_with`, arrow functions)
- Node.js 18+ and npm
- [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/) (required — the theme's blocks and Theme Settings depend on it)
- Optional: [Yoast SEO](https://yoast.com/wordpress/plugins/seo/), [Gravity Forms](https://www.gravityforms.com/)

## Getting Started

1. Copy/clone the theme into `wp-content/themes/theme-name/`.
2. Install front-end dependencies:

   ```bash
   npm install
   ```

3. Run the dev/watch build:

   ```bash
   npm run dev
   ```

4. Build production assets:

   ```bash
   npm run build
   ```

5. Activate the theme in **WordPress Admin → Appearance → Themes**.
6. Install and activate **ACF PRO**, then sync the included block field groups (see [ACF Integration](#acf-integration)).

## Build System (Vite)

The theme does not rely on WordPress' native asset pipeline for compilation — everything is bundled with **Vite** (`vite.config.js`), including a legacy build plugin, Sass (Dart Sass, `modern-compiler` API), Autoprefixer, and Terser for production minification.

Key points of the Vite configuration:

- **Output stays inside the theme root** (`outDir: '.'`), so compiled assets can be enqueued directly by PHP with no separate deploy step.
- **Fixed entry points** compile to predictable paths:
  - `src/scss/critical.scss` → `dist/css/critical.css`
  - `src/scss/general.scss` → `dist/css/general.css`
  - `src/scss/editor-styles.scss` → `dist/css/editor-styles.css`
  - `src/scss/pages/404.scss` → `dist/css/pages/404.css`
  - `src/js/general.js` → `dist/js/general.js`
  - `src/js/analytics.js` → `dist/js/analytics.js`
- **Dynamic block entries** — `buildBlockEntries()` scans `src/scss/blocks/*` and `src/js/blocks/*` and automatically generates a Vite entry for each file, so every new block file gets its own `blocks/{name}/block-style.css` and `blocks/{name}/block-script.js` without touching the config again.
- **Static assets** (`src/images/`) are copied to `dist/images/` via `vite-plugin-static-copy`.
- **Aliases**: `@` → `src/`, `~` → `node_modules/` (for legacy `~package` SCSS imports).
- Source maps are enabled only in development; Terser minification (with `drop_console: false`) is used for production.

npm scripts:

| Script | Description |
|---|---|
| `npm run dev` / `npm run watch` | Watches and rebuilds assets in development mode |
| `npm run build` / `npm run production` | Produces the optimized production build |

## Project Structure

```
sz-starter/
├── src/                     # Source files compiled by Vite
│   ├── scss/                # Stylesheets (critical, general, blocks, gutenberg, vendor…)
│   ├── js/                  # Scripts (general.js, analytics.js, blocks/…)
│   └── images/               # Static images (copied as-is to dist/images)
├── dist/                     # Compiled/production assets (generated, do not edit by hand)
├── blocks/                   # ACF Blocks (each with block.json, block.php, preview.php, compiled assets)
│   ├── full-image/
│   ├── swiper-carousel/
│   ├── contact/
│   ├── vertical-space/
│   └── Theme Settings - GTM Code.json   # ACF field group export for Theme Settings (Options page)
├── template-parts/           # Reusable template partials (header, footer, archive, icons…)
├── inc/                      # Theme functionality, split by concern
│   ├── helper-functions.php  # rrp(), WebP generation, logo, SVG inliner…
│   ├── customizer.php        # Theme Customizer bootstrap
│   ├── customizer/           # Individual Customizer sections (logo, social links)
│   ├── custom-widgets.php    # Widget registration
│   ├── custom-widgets/       # Individual widgets (social links, footer widgets)
│   ├── custom-post-types.php # CPT registration (opt-in, examples included)
│   ├── wordpress-cleanup.php # Menu/post class cleanup, excerpt, comment form tweaks
│   ├── body-class.php        # Device/browser/OS classes on <body>
│   ├── ajax-calls.php        # AJAX endpoint handlers
│   ├── tinymce.php           # Classic editor style formats
│   └── acf-blocks.php        # Registers every folder in /blocks as an ACF Block
├── my-admin/                 # Custom wp-login.php styling/behavior
├── favicon/                  # Favicon set + site.webmanifest
├── functions.php             # Theme bootstrap: enqueues, theme supports, cleanup hooks
├── header.php / footer.php   # Global document shell (critical CSS inlined here)
├── index.php / page.php / single.php / archive.php / search.php / 404.php / comments.php
├── theme.json                 # Gutenberg / Global Styles configuration
├── style.css / style-rtl.css  # Theme stylesheet header (required by WordPress) + RTL support
└── vite.config.js / package.json
```

## Performance Optimizations

This theme's central goal is speed. Nearly every decision in `functions.php` and `inc/helper-functions.php` exists to reduce render-blocking resources and unnecessary work.

### CSS & JS Loading Strategy

- **Critical CSS is inlined** directly in `<head>` (`header.php` reads `dist/css/critical.css` with `file_get_contents()` and prints it inside a `<style>` tag), so above-the-fold content never waits on an external stylesheet request.
- **Non-critical stylesheets are loaded asynchronously.** Any style handle ending in `-styles` (global styles, block styles, `404-styles`, and optionally Gravity Forms handles) is rewritten via the `style_loader_tag` filter into a `rel="preload" ... onload="this.rel='stylesheet'"` pattern, with a `<noscript>` fallback for JS-disabled browsers.
- **All JS is deferred.** `general.js` and `analytics.js` are enqueued with the native WordPress `['strategy' => 'defer', 'in_footer' => true]` script strategy. jQuery itself is de-registered and re-registered as deferred (only on the front end).
- **Scripts are output as ES Modules.** Any script handle ending in `-scripts` gets `type="module"` injected via the `script_loader_tag` filter, matching Vite's native ESM output.
- **Per-block asset loading.** `register_blocks_assets()` walks the parsed blocks of the current post and only enqueues the CSS/JS for the ACF blocks actually present on the page (with `filemtime()`-based cache busting and de-duplication), instead of loading every block's assets on every page.
- **The polyfill scripts** (`wp-polyfill`, `regenerator-runtime`) are deregistered to shave extra weight for modern browsers.
- **Fonts** are preconnected and preloaded (see `template-parts/general/head-fonts.php`) using the `preload` + `onload` swap-to-stylesheet trick to avoid blocking rendering.
- **Output is minified server-side too** — a `template_redirect` output buffer strips leading whitespace/empty lines from the final HTML before it's sent to the browser.

### Automatic WebP Conversion

`inc/helper-functions.php` hooks into `wp_generate_attachment_metadata` (admin-only, so it never impacts front-end performance) to automatically generate a `.webp` version of every uploaded JPG/PNG, for the original file **and** every registered intermediate size. PNGs are converted with alpha channel preservation; JPGs use the native WordPress image editor. Quality is set to `82` for both formats. A matching `delete_attachment` hook removes the generated WebP files when the original attachment is deleted, keeping the uploads folder clean.

### The `rrp()` Responsive Picture Helper

`rrp()` ("render responsive picture") is the theme's single entry point for outputting optimized, responsive images. Given one or two ACF image IDs (desktop and optionally mobile), it:

- Automatically serves the pre-generated `.webp` file when available, with the original format as a fallback (`<picture>` + `<source type="image/webp">` pattern).
- Builds `srcset` for both formats using WordPress' native attachment metadata.
- Supports independent desktop/mobile sources with a `768px` breakpoint when a mobile image is supplied.
- Exposes simple options (`picture_class`, `img_class`, `loading`, `fetchpriority`, `decoding`) with sensible lazy-loading defaults, while still allowing e.g. `fetchpriority: high` for above-the-fold hero images.

Usage example inside a block or template:

```php
//$fields['desktop_image'], $fields['mobile_image'] must return image ID ($fields['desktop_image'] can be replaced by get_post_thumbnail_id($post->ID) if you need the featured image of a post/page )
rrp( $fields['desktop_image'], $fields['mobile_image'] ?? null, [
    'picture_class' => 'bg',
    'img_class'     => 'cover',
    'loading'       => 'lazy',
] );
```

### Analytics / GTM Loading Strategy

`src/js/analytics.js` implements a lazy-loading strategy for Google Tag Manager designed to protect Core Web Vitals (particularly TBT/INP):

- GTM is **not** injected on page load. It's only fetched when the browser reports `requestIdleCallback` (with a `3000ms` timeout fallback) **or** as soon as the user actually interacts with the page (`scroll`, `mousemove`, `touchstart`, `click` — whichever fires first, each listener is `once` and `passive`).
- The GTM container ID is not hardcoded: it's injected from ACF's Theme Settings options page (`window.GTM_ID`, set in `header.php`) so it can be managed entirely from the WordPress admin.
- A separate `gtm_body_code` ACF field allows outputting the `<noscript>` GTM body snippet right after `wp_body_open()`.
- A `cookiebot_code` ACF field is also available for consent-management scripts, output in `<head>`.

### Other Speed & Cleanup Optimizations

- Emoji detection scripts/styles, DNS-prefetch hints, shortlinks, RSD, WLW manifest, and the WordPress generator meta tag are all removed from `<head>`.
- Comments are fully disabled (admin menu, admin bar, post type support, comment feeds, `comments_open`/`pings_open` filters) since most marketing/business sites don't use them — reducing both database queries and surface area.
- The old Tag widget/taxonomy is disabled by default on posts (`register_taxonomy('post_tag', [])`).
- The theme excludes itself from WordPress.org theme update checks (useful for custom/proprietary themes with a name collision).

## ACF Integration

This theme is **built around Advanced Custom Fields** and assumes ACF (ideally ACF PRO) is active:

- `inc/acf-blocks.php` automatically registers **every folder inside `/blocks`** as a native ACF Block via `register_block_type()` — no manual registration needed per block. To add a new block, just create a new folder with a `block.json` + `block.php` and it's picked up automatically on `init`.
- ACF field groups are saved as JSON in the stylesheet directory (`acf/settings/save_json` filter points to `/acf-json`), making field groups portable and version-controllable. When cloning this theme for a new project, sync the JSON field groups from the ACF admin screen.
- The included `Theme Settings - GTM Code.json` is an ACF field-group export for a **Theme Settings options page**, containing the Cookiebot code, GTM container ID, and GTM body `<noscript>` snippet fields referenced in `header.php`.
- Each block ships with its own `preview.php` for a friendlier editor experience; if a block doesn't define one, the theme falls back to `template-parts/parts/acf-block-preview.php`.
- Helper functions such as `rrp()` and `inline_svg_from_media()` are designed to consume ACF Image/File field values (attachment IDs) directly.

## ACF Blocks

Included starter blocks (all under `/blocks`):

| Block | Description |
|---|---|
| `full-image` | Full-width responsive image/banner block using `rrp()`, with an optional caption. |
| `swiper-carousel` | Carousel block powered by [Swiper](https://swiperjs.com/). |
| `contact` | Contact section block (pairs well with Gravity Forms — see below). |
| `vertical-space` | Utility spacer block for controlling space between sections. |

Each block folder follows the same convention:

```
blocks/{name}/
├── block.json          # Block registration + ACF config (renderTemplate)
├── block.php            # PHP render callback
├── preview.php           # Editor-only preview markup
├── block-style.css       # Compiled from src/scss/blocks/{name}.scss
└── block-script.js       # Compiled from src/js/blocks/{name}.js
```

Add a new block by creating a new folder (with `block.json` + `block.php`) and, if needed, matching `src/scss/blocks/{name}.scss` / `src/js/blocks/{name}.js` source files — Vite and `acf-blocks.php` will pick everything up automatically on the next build.

## Yoast SEO Notes

The theme is compatible with Yoast SEO out of the box (no conflicting title tags, since `add_theme_support('title-tag')` lets Yoast/WordPress manage `<title>`). `functions.php` includes commented-out snippets for common Yoast tweaks that can be enabled per project, such as disabling the `SearchAction` schema piece or forcing the WordPress search page to 404 (useful on sites where Yoast handles all indexable content and a native search UX isn't desired).

## Gravity Forms Notes

Several commented-out integration points are included in `functions.php` for projects using Gravity Forms:

- Swapping the default AJAX spinner for the theme's own SVG loader (`dist/images/svg/ajax-loader.svg`).
- Adding a `body` class while a form is submitting/validating (useful for loading states in CSS).
- Disabling the default confirmation-page anchor scroll.
- Dispatching a custom `formRendered` event, handy when a project uses a custom `<select>` library (e.g. Tom Select, already included as a dependency) on Gravity Forms fields.
- Async-loading Gravity Forms' own stylesheets by adding their handles to the `$async_handles` array in `sz_theme_global_enqueues()`.

Uncomment and adapt whichever snippets a given project needs.

## Theme Customizer

`inc/customizer.php` provides small factory helpers (`add_section_text_field`, `add_section_image_field`, `add_section_link_field`, `add_section_color_field`, `add_section_select_field`, etc.) to speed up adding new Customizer controls, plus two ready-made sections:

- **Site Logo** (`inc/customizer/site-logo.php`) — powers `html_site_logo()` in `inc/helper-functions.php`.
- **Social Links** (`inc/customizer/social-links.php`) — powers the bundled `Custom_Social_Links` widget and social icon template parts (`template-parts/icons/*.php`: Facebook, Instagram, LinkedIn, Twitter).

## Personalized Style for Admin Login Page

`my-admin/login.php` (loaded via `locate_template()` in `functions.php`) replaces the default `wp-login.php` branding with theme-specific styling (`login.css`) and behavior (`login.js`), including a themed favicon for wp-admin.

If you prefer the classic login, you can comment the line in functions.php to bring back the classic style for login page.

## License

Licensed under the GNU General Public License v2 or later, in line with WordPress' own licensing — see the `LICENSE` file for details.
