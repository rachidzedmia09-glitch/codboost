# DG Network Presentation Site — Design & Motion System

This document is the developer handoff for the DG Network one-page bilingual presentation experience. It includes the design language, motion principles, accessibility standards, and build-ready specs for the responsive, scroll-snapping interface.

## 1. Design Tokens

| Token | Value | Usage |
| --- | --- | --- |
| `--bg` | `#05070f` | Global background, canvas fill |
| `--ink` | `#e9eef7` | Primary typography |
| `--muted` | `#9aa4b3` | Secondary copy, meta text |
| `--line` | `rgba(255, 255, 255, 0.14)` | Divider strokes, card outlines |
| `--glass` | `rgba(255, 255, 255, 0.06)` | Glass cards, nav background |
| `--blue` | `#2d7df6` | Accents, highlights |
| `--red` | `#ff3b3b` | CTA highlights, danger cues |
| `--violet` | `#7c4dff` | Glow sweeps, gradient ribbons |
| `--cyan` | `#22d3ee` | Gooey orb highlight |
| `--amber` | `#f59e0b` | Status chip |
| `--purple` | `#8b5cf6` | Gradient depth |

### Density & Spacing Scale (px)
- Scale: `4 • 8 • 12 • 16 • 24 • 32 • 48 • 64 • 96`

### Radii
- `--radius-xs: 6px`
- `--radius-sm: 12px`
- `--radius-md: 20px`
- `--radius-lg: 28px`
- `--radius-pill: 999px`

### Shadows & Glows
- `--shadow-soft: 0 12px 40px rgba(8, 12, 24, 0.45)`
- `--shadow-card: 0 24px 60px rgba(12, 18, 36, 0.65)`
- `--glow-blue: 0 0 40px rgba(45, 125, 246, 0.55)`
- `--glow-red: 0 0 36px rgba(255, 59, 59, 0.45)`
- `--glow-violet: 0 0 60px rgba(124, 77, 255, 0.35)`

### Typography
- Base font stack: `"Inter", "SF Pro Display", "Segoe UI", "Roboto", "Helvetica Neue", system-ui, sans-serif`
- `--font-size-xs: 0.75rem`
- `--font-size-sm: 0.875rem`
- `--font-size-md: 1rem`
- `--font-size-lg: 1.25rem`
- `--font-size-xl: 1.5rem`
- `--font-size-2xl: 1.875rem`
- `--font-size-3xl: 2.5rem`
- `--font-size-hero: clamp(2.5rem, 5vw, 4rem)`
- Font weights: regular 400, medium 500, semibold 600, bold 700

### Motion
- Global timing function: `cubic-bezier(.2, .7, .2, 1)`
- Duration scale: 220ms (micro) · 360ms (default) · 520ms (section in) · 900ms (hero)

Token exports are provided in [`tokens.css`](./tokens.css) and [`tokens.json`](./tokens.json).

## 2. Layout & Grid
- Max content width: `1180px`
- Scroll container: `scroll-snap-type: y mandatory` with full viewport sections.
- Gutters: `24px` desktop, `16px` tablet, `12px` mobile.
- Glass section box: centered, `max-width: 960px` with `padding: clamp(24px, 4vw, 48px)` and `backdrop-filter: blur(22px)`.
- Two-column bilingual layout using CSS grid `grid-template-columns: repeat(auto-fit, minmax(280px, 1fr))` with `gap: 24px`.
- Direction attributes: `dir="ltr"` on English column, `dir="rtl"` on Arabic column. Mirror alignment on mobile stack.
- Navigation aids:
  - Left rail: stacked scroll dots (`nav[aria-label="Section dots"]`) with active halo.
  - Bottom right: previous/next floating action buttons (FAB) with magnetic hover.
  - Top: progress bar pinned to `top: 0` representing scroll progress.

Redline measurements and responsive breakpoints are documented in [`redlines.md`](./redlines.md).

## 3. Section Architecture
The page contains a hero plus 14 scroll-snapped sections. Each section uses `.section` + a unique animation utility class to trigger its entrance motion when intersecting the viewport. Content is wrapped inside `.section-box` glass containers. Section identifiers:

1. Hero — `.hero` + `.anim-heroIn`
2. Brand Architecture — `.section-architecture` + `.anim-riseIn`
3. Tone & Visual Identity — `.section-identity` + `.anim-clipRight`
4. Platform Strategy — `.section-platform` + `.anim-flipIn`
5. Daily Posting Cadence — `.section-cadence` + `.anim-skewIn`
6. Hashtags & SEO System — `.section-hashtags` + `.anim-circleIn`
7. Content System & Repurposing — `.section-content` + `.anim-tiltIn`
8. Niche Playbooks — `.section-playbooks` + `.anim-zoomIn`
9. AI & Automation Stack — `.section-automation` + `.anim-wipeIn`
10. Growth Roadmap — `.section-growth` + `.anim-floatIn`
11. Monthly Viral Campaign Blueprint — `.section-campaign` + `.anim-scaleYIn`
12. Monetization — Phase 2 — `.section-monetization` + `.anim-blurIn`
13. Operations & Workflow — `.section-operations` + `.anim-boxIn`
14. Visual Mockups & Templates — `.section-mockups` + `.anim-glowSweep`
15. Closing — `.section-closing` + `.anim-rippleIn`

Animation sequencing and durations are defined in [`motion.md`](./motion.md).

## 4. Components
- **Scroll Dot Nav**: `button.dot` with ARIA labels; active dot uses glowing halo + scale.
- **Progress Bar**: `.progress-track` with `.progress-thumb` width updated via scroll ratio.
- **Section Header**: `<header class="section-header">` with bilingual chips.
- **Chip / Pill**: `.chip` (glass background, uppercase label).
- **Glass Card**: `.glass-card` for content groupings, includes subtle border and ambient glow.
- **Bilingual Panel**: `.bilingual` grid containing `.lang-column` (dir attribute, language label via `.lang-chip`).
- **Brand Grid**: `.brand-grid` houses `.brand-card` with remote icon + wordmark, fallback skeleton gradient.
- **Magnetic FAB**: `.fab` with JS-driven translate to pointer.
- **Animated Ribbon**: `ribbon.svg` used as pseudo-element accent under section headers.

Interaction specifications and variants are described in [`components.md`](./components.md).

## 5. Motion & Effects
- Background: `<canvas id="starfield">` renders three depth layers of stars with parallax tied to scroll.
- Gooey orbs: `<div class="orb" data-speed="0.4">` layered with `filter: url(#goo)` (see `gooey-filter` in `index.html`). Colors animate via gradient keyframes.
- Section entry animations triggered via Intersection Observer; fallback to simple fade for `prefers-reduced-motion`.
- Micro-interactions: hover lifts on `.glass-card`, gradient border sweep on `.section-box::after`, `magneticHover` utility for FABs.

Detailed easing, delay, and sequencing are captured in [`motion.md`](./motion.md) and the storyboard references in [`motion-storyboard.md`](./motion-storyboard.md).

## 6. Accessibility & Performance
- Contrast ratios checked for WCAG AA.
- Focus states: dual ring (solid + glow), consistent on nav dots, buttons, cards.
- Keyboard navigation: arrow keys cycle sections, Enter activates nav. Provided in `scripts.js`.
- Motion reduction: checks `prefers-reduced-motion` and switches animations to opacity/translate fallback while disabling parallax + magnetic effects.
- Lazy loading remote assets with `loading="lazy"` and `decoding="async"`.
- Canvas optimized with `requestAnimationFrame` and pooling.

## 7. Assets & References
- Brand icons + logos reference remote URLs (listed in [`components.md`](./components.md)). Provide offline placeholders if needed.
- SVG exports for ribbon, radial mask, and dot indicator live in [`svg/`](./svg).
- Content copy embedded verbatim in `index.html` within bilingual panels.

## 8. Build Checklist
1. Include `styles.css`, `tokens.css`, and `scripts.js`.
2. Mount `<canvas id="starfield">` before `.page` container.
3. Ensure `lang="en"` on `<html>` with `<body data-theme="dg-network">`.
4. Inject gooey filter definitions and gradient defs from `index.html`.
5. Confirm Intersection Observer root margin matches `-15% 0px` to trigger animations when section enters center viewport.
6. Validate responsive states at 1280px, 1024px, 768px, 480px.

For implementation questions, reference the inline comments within the CSS and JS files.
