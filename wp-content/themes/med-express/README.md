# Med Express Delivery WordPress Theme

A clean, Elementor-ready WordPress theme crafted for Algerian courier, last-mile, and logistics companies. The layout keeps defaults lightweight so every section can be composed visually inside Elementor while still providing polished fallbacks for posts, archives, and search. The theme now ships with one-click demo import assets and guided plugin onboarding so you can replicate the showcase site in minutes.

## Key Features

- **Elementor-first workflow** – front page, pages, posts, header, footer, archives, search, and 404 screens can all be replaced with Elementor Theme Builder templates.
- **Minimal styling scaffold** – refreshed typography, navigation, and footer styles that stay out of the way of Elementor widgets.
- **Widget-ready footer** with three columns plus optional menu, ready for contact details or call-to-action blocks.
- **Responsive navigation** with accessible toggle behaviour for smaller screens.
- **Translation-ready** strings using the `med-express` text domain and support for custom logos.
- **One-click demo kit** – import Elementor layouts, pages, menus, and widgets that mirror the Med Express demo in a single action.
- **Plugin onboarding** – bundled integration with TGM Plugin Activation to prompt installation of Elementor, Header & Footer Builder, One Click Demo Import, Contact Form 7, and WP Mail SMTP.
- **Brand style guide** – Elementor template showcasing official logos, palette, typography, and component guidance for collaborators.

## Installation

1. Copy the `med-express` directory to your WordPress installation under `wp-content/themes/`.
2. In the WordPress admin, go to **Appearance → Themes** and activate “Med Express Delivery”.
3. Visit **Appearance → Install Plugins** to install/activate the required and recommended plugins (Elementor, Header & Footer Builder, One Click Demo Import, Contact Form 7, WP Mail SMTP).
4. Go to **Appearance → Import Demo Data** and trigger the “Med Express Elementor Demo” import to clone the showcase pages, Elementor Theme Builder templates, and widgets.
5. Fine-tune Elementor layouts, set your logo, and adjust contact details in the imported sections.
6. Configure menus and footer widgets from **Appearance → Menus** and **Appearance → Widgets** or replace them with custom Elementor templates.

## Recommended Plugins

- **Elementor** (required) for visual editing (Elementor Pro optionally unlocks Theme Builder conditions and dynamic content).
- **Elementor Header & Footer Builder** to quickly override theme locations without Elementor Pro.
- **One Click Demo Import** for replicating the demo kit bundled with the theme.
- **Contact Form 7** to power the contact call-to-action in the imported layouts.
- **WP Mail SMTP** to improve email deliverability for form notifications.
- **Yoast SEO** or **Rank Math** for SEO enhancements.

## Demo Import Assets

The starter kit consumed by One Click Demo Import lives in the theme at `demo/`:

- `content.xml` – base pages, blog post, and taxonomy terms.
- `widgets.json` – footer widget configuration to mirror the preview site.
- `customizer.dat` – placeholder Customizer data to ensure a clean import.
- `elementor/` – Elementor JSON exports for the home, services, pricing, contact, header, footer, and brand guide layouts.

Feel free to modify these files or add additional Elementor templates. Any `.json` file added to `demo/elementor/` will be imported automatically and can optionally target a page by including an `assign_to` block or define a Theme Builder location via the `location` key.

> **Note:** Elementor Pro or the free [Elementor Header & Footer Builder](https://wordpress.org/plugins/header-footer-elementor/) plugin is required to assign the imported header and footer templates to live theme locations. Without those plugins, the templates remain available in the Elementor Library for manual insertion.

## Development Notes

- Primary stylesheet is `assets/css/main.css` and is imported via `style.css`.
- JavaScript enhancements live in `assets/js/navigation.js` (menu toggle) and `assets/js/theme.js` (placeholder for optional scripts).
- Elementor integration helpers reside in `inc/elementor.php`, and template utilities remain in `inc/template-tags.php`.

Feel free to adapt branding colours, typography, or add additional Elementor template files as your project grows.
