# Med Express Delivery WordPress Theme

A clean, Elementor-ready WordPress theme crafted for Algerian courier, last-mile, and logistics companies. The layout keeps defaults lightweight so every section can be composed visually inside Elementor while still providing polished fallbacks for posts, archives, and search.

## Key Features

- **Elementor-first workflow** – front page, pages, posts, header, footer, archives, search, and 404 screens can all be replaced with Elementor Theme Builder templates.
- **Minimal styling scaffold** – refreshed typography, navigation, and footer styles that stay out of the way of Elementor widgets.
- **Widget-ready footer** with three columns plus optional menu, ready for contact details or call-to-action blocks.
- **Responsive navigation** with accessible toggle behaviour for smaller screens.
- **Translation-ready** strings using the `med-express` text domain and support for custom logos.

## Installation

1. Copy the `med-express` directory to your WordPress installation under `wp-content/themes/`.
2. In the WordPress admin, go to **Appearance → Themes** and activate “Med Express Delivery”.
3. Install and activate **Elementor** (and **Elementor Pro** if you want to build custom headers/footers/archives).
4. Create your pages inside Elementor. Assign Elementor Theme Builder templates to override the default header, footer, archives, or search pages if desired.
5. Configure menus and footer widgets from **Appearance → Menus** and **Appearance → Widgets**.

## Recommended Plugins

- **Elementor** for visual editing (Pro unlocks Theme Builder locations for header, footer, archive, search, and 404 templates).
- **Contact Form 7** or **WPForms** for contact forms inside Elementor layouts.
- **Yoast SEO** or **Rank Math** for SEO enhancements.

## Development Notes

- Primary stylesheet is `assets/css/main.css` and is imported via `style.css`.
- JavaScript enhancements live in `assets/js/navigation.js` (menu toggle) and `assets/js/theme.js` (placeholder for optional scripts).
- Elementor integration helpers reside in `inc/elementor.php`, and template utilities remain in `inc/template-tags.php`.

Feel free to adapt branding colours, typography, or add additional Elementor template files as your project grows.
