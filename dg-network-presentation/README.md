# DG Network Presentation

A bilingual (English + Arabic) scroll-snapping presentation website for the DG Network strategic plan. Built with semantic HTML, modular CSS, and vanilla ES modules to deliver a cinematic-yet-accessible storytelling experience.

## Getting Started

```bash
# serve locally
cd dg-network-presentation
python3 -m http.server 8080
```

Open `http://localhost:8080` in a modern browser to explore the presentation. JavaScript is required for enhanced navigation, progress tracking, and reduced-motion handling.

## Features

- Full-viewport sections with CSS scroll snap, keyboard navigation, and hash routing.
- Dual-column bilingual layout (LTR English / RTL Arabic) that stacks responsively on smaller screens.
- Brand visuals grid with five verticals, live remote logos, and graceful SVG fallbacks.
- Accessible enhancements: visible focus rings, aria-live section announcements, skip link, and print-ready layout.
- Respect for `prefers-reduced-motion`, lazy-loaded assets, and no third-party dependencies.
- Dedicated print stylesheet for exporting one section per A4 page via the built-in “🖨️ Export to PDF” action.

## Project Structure

- `index.html` – rendered presentation with embedded verbatim plan text.
- `assets/css/` – tokenized design system, layout, components, animations, and print rules.
- `assets/js/` – ES module bootstrap with observers, keyboard controls, and router utilities.
- `assets/img/placeholders/` – local SVG fallback for remote logos.
- `content/plan.verbatim.txt` – exact strategic plan text for regression/reference.

## Accessibility & Performance Notes

- Contrast ratios ≥ 4.5:1 and animation guards for reduced-motion users.
- Navigation dots are fully keyboard operable (Arrow/Home/End/Enter) with aria-current state.
- IntersectionObserver drives section announcements (`aria-live="polite"`) and progress bar updates.
- No external fonts or libraries; relies on the system font stack for optimal loading.

## Printing / PDF Export

Use the “🖨️ Export to PDF” button in the hero or your browser’s print command. The print stylesheet ensures each major section occupies a dedicated A4 page with simplified styling and a footer showing the source URL.

## License

Released under the MIT License. See [`LICENSE`](./LICENSE).
