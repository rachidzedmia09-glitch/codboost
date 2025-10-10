# Redlines & Responsive Specs

All measurements in pixels unless noted.

## Viewport ≥ 1280px
- Section height: `100vh`.
- `.page` max width: `1180px`, centered with `margin: 0 auto`.
- `.section-box`: width `960px`, padding `48px`, corner radius `28px`.
- `.section-header`: bottom margin `32px`.
- `.section-title`: line-height `1.2`, letter-spacing `-0.01em`.
- `.bilingual` grid: columns `1fr 1fr`, gap `32px`.
- `.brand-grid`: `grid-template-columns: repeat(4, minmax(0, 1fr))`, gap `24px`.
- FAB group offset: `bottom: 48px`, `right: 48px`.

## Viewport 1024px–1279px
- `.section-box` padding `40px`.
- `.bilingual` gap `28px`.
- `.brand-grid`: `repeat(3, minmax(0, 1fr))`.
- FAB group offset: `bottom: 40px`, `right: 40px`.

## Viewport 768px–1023px
- `.section-box` width `92vw`, padding `32px`.
- `.section-title`: `font-size: clamp(1.75rem, 3vw, 2rem)`.
- `.bilingual`: switches to `grid-template-columns: 1fr`, each column stacking with `gap: 20px`.
- `.brand-grid`: `repeat(2, minmax(0, 1fr))`.
- Progress bar height reduces to `3px`.

## Viewport ≤ 767px
- `.section`: `padding: 24px 0` to allow breathing room for snap.
- `.section-box`: width `94vw`, padding `24px`, radius `20px`.
- `.section-header`: `flex-direction: column`, gap `12px`.
- `.section-title`: `font-size: clamp(1.5rem, 8vw, 1.75rem)`.
- `.bilingual`: stacked columns with `gap: 16px`; `text-align` left for LTR and right for RTL maintained with `text-align: start`.
- `.brand-grid`: `repeat(2, minmax(0, 1fr))` for ≥480px, single column below 480px.
- FAB group: `bottom: 24px`, `right: 24px`; buttons scale to `56px`.

## Spacing
- Vertical spacing between sections: `scroll-snap-align: start`; starfield extends behind all sections.
- Section header to body: `24px`.
- Paragraph spacing: `16px`.

## Line Heights
- Body text: `1.6`.
- Chips / labels: `1`.

## Iconography
- Brand icons: `56px` square; wordmarks max height `32px`.
- Section index chips: `40px` height, padding `0 16px`.

## Interaction Targets
- Nav dots: `24px` active area with `::before` to expand to `44px` for touch.
- FABs: `64px` circle desktop (`56px` mobile), `min-hit-area: 48px` via pseudo-element.

All values annotated in `index.html` comments for quick reference.
