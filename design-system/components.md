# Component Library — DG Network Presentation

This file documents component structure, states, and implementation notes.

## 1. Navigation
### Scroll Dots
- Element: `<button class="dot" type="button" aria-label="Go to section {n}">` inside `<nav aria-label="Section dots">`.
- States: default (opacity 0.42), hover (scale 1.1 + halo), active (scale 1.3 + glow + `.is-active` class), focus (outline + glow).
- Animation: halo uses `glowSweep` gradient border; transitions 220ms.

### Progress Bar
- Structure: `<div class="progress-track"><span class="progress-thumb" role="presentation"></span></div>` pinned to top.
- Width of `.progress-thumb` updated via `style.setProperty('--progress', ratio)` (0–1). Animation uses `transform: scaleX(var(--progress))`.

### Prev/Next FABs
- Container: `<div class="fab-group" role="group" aria-label="Section navigation">`.
- Buttons: `<button class="fab" data-direction="prev">` / `next`.
- Magnetic hover: JS calculates delta to pointer and updates CSS custom properties for translation (clamped at 18px).

## 2. Content Blocks
### Section Box
- `.section-box` uses glass background, `backdrop-filter: blur(22px)`, border `1px solid rgba(255,255,255,0.12)`.
- Each `.section` includes `.section-header` and `.section-body`.
- Pseudo-element `::after` renders animated gradient ribbon (uses `url('../svg/ribbon.svg')`).

### Section Header
```
<header class="section-header">
  <span class="chip chip--index">1️⃣</span>
  <h2 class="section-title">Brand Architecture</h2>
  <span class="chip chip--label">Dual Language</span>
</header>
```
- Titles use `font-size: var(--font-size-2xl)` desktop, `clamp(1.5rem, 4vw, 2rem)` responsive.

### Bilingual Panel
```
<div class="bilingual">
  <div class="lang-column" dir="ltr">
    <span class="lang-chip">English</span>
    <!-- copy -->
  </div>
  <div class="lang-column" dir="rtl">
    <span class="lang-chip">العربية</span>
    <!-- copy -->
  </div>
</div>
```
- Grid gap `24px` (desktop) / `16px` (mobile).
- `lang-chip` uses pill style with accent gradient.

### Glass Card & Chips
- `.glass-card` has `padding: 24px`, `border-radius: var(--radius-lg)`, gradient border using `border-image`.
- Hover: translates `-4px` on Y, increases glow.
- Chips: `.chip` uppercase, letter spacing `0.08em`, background `rgba(255,255,255,0.06)`, border `1px solid rgba(255,255,255,0.2)`.

## 3. Brand Grid
```
<div class="brand-grid">
  <article class="brand-card">
    <img class="brand-icon" src="https://codboost.pro/wp-content/uploads/2025/10/DGSPORTS-icon.png" alt="DGSPORTS icon" loading="lazy" decoding="async">
    <img class="brand-wordmark" src="https://codboost.pro/wp-content/uploads/2025/10/DGSPORTS-logo.png" alt="DGSPORTS logo" loading="lazy" decoding="async">
  </article>
  <!-- Repeat for DGANIME, DGMOVIES, DGGAMING -->
</div>
```
- `.brand-card` uses `.anim-boxIn` when appearing inside brand section; includes skeleton pseudo-element with shimmer (keyframes `skeletonPulse`).
- Provide placeholder for DGTECH using `.brand-card.brand-card--placeholder` with label text.

## 4. Status Badges
- `.badge` variant for highlight metrics using accent backgrounds (`--blue`, `--red`, `--violet`).
- Use `mix-blend-mode: screen` on accent icons to enhance glow.

## 5. Micro-interactions
- Buttons: `transition: transform 180ms var(--motion-curve), box-shadow 220ms`.
- Cards: `transform: translateY(0)` default, on hover `translateY(-6px)` and apply `box-shadow: var(--shadow-card)`.
- Nav dots: `::after` radial gradient expands on active.

## 6. Copy Embedding
- Verbatim bilingual copy lives in `index.html` and must not be altered; ensure `white-space: pre-line` for blocks that require preserved line breaks (hero summary).

Refer to `styles.css` for final token application and responsive adjustments.
