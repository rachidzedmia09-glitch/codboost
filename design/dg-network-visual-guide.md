# DG Network One-Page Presentation — Visual & Interaction Guide

## 1. Brand Narrative & Experience Principles
- **Positioning:** DG Network is an Algerian-born digital ecosystem that orchestrates five niche brands under one bold, tech-forward umbrella.
- **Experience Pillars:**
  1. **Bold Minimalism:** Immersive dark canvas (#0a0a0a) with high-contrast callouts and ample white space for focus.
  2. **Narrative Motion:** Scroll-triggered SVG stories that reveal the network structure and roadmap step by step.
  3. **Bilingual Clarity:** Every element supports both English (LTR) and Arabic (RTL) via mirrored layouts, dual-language typography, and responsive spacing tokens.

---

## 2. Visual Design System

### 2.1 Color Palette
| Role | Hex | Usage |
| --- | --- | --- |
| **Primary Black** | `#0a0a0a` | Background canvas, hero, footer |
| **Primary Blue** | `#0066ff` | CTAs, interactive accents, hover states |
| **Primary Red** | `#ff0033` | Highlights, alerts, pulse glow |
| **Secondary Dark Gray** | `#1a1a1a` | Section surfaces, cards |
| **Secondary Light Gray** | `#f0f0f0` | Text backgrounds in light mode toggles, dividers |
| **White** | `#ffffff` | Primary text on dark background |

### 2.2 Niche Brand Accent Colors
| Brand | Accent Hex | Motion Glow |
| --- | --- | --- |
| **DGSPORTS** | `#ff6b00` | `rgba(255, 107, 0, 0.55)` |
| **DGTECH** | `#00d9ff` | `rgba(0, 217, 255, 0.55)` |
| **DGMOVIES** | `#ff3366` | `rgba(255, 51, 102, 0.55)` |
| **DGANIME** | `#ff7bff` | `rgba(255, 123, 255, 0.55)` |
| **DGGAMING** | `#aaff00` | `rgba(170, 255, 0, 0.55)` |

### 2.3 Typography
- **Heading Family:** `"Space Grotesk", "IBM Plex Sans Arabic", "Segoe UI", sans-serif;`
  - Variable weight 400–700 for English; `font-feature-settings: "tnum"` for numeric stats.
- **Body Family:** `"Inter", "IBM Plex Sans Arabic", "Noto Sans Arabic", sans-serif;`
  - Optimized for readability with 1.6 line height.
- **Arabic Styling:** Apply `font-variation-settings: 'wght' 500` for headings, `text-align: right`, and mirrored layout.

| Token | Size (rem) | Use |
| --- | --- | --- |
| `--fs-h1` | 3.2 (clamp 2.4–4.0) | Hero bilingual headline |
| `--fs-h2` | 2.4 (clamp 1.8–3.0) | Section titles |
| `--fs-h3` | 1.8 | Subheadings / milestone titles |
| `--fs-body-lg` | 1.125 | Lead paragraphs |
| `--fs-body` | 1.0 | Standard paragraphs |
| `--fs-meta` | 0.875 | Labels, captions |

### 2.4 Spacing & Layout Tokens
- Base unit `--space-1` = 0.5rem. Modular scale: 1, 2, 3, 5, 8, 13 units.
- Section padding: `clamp(4rem, 10vw, 7rem)` top/bottom.
- Grid gap defaults: `min(3vw, 2.5rem)`.

---

## 3. Page Layout Blueprint
The page is a vertical narrative built with CSS Grid (desktop) and Flexbox (mobile). Each section includes an English block on the left and Arabic on the right (mirrored on mobile).

| # | Section | Layout | Content Highlights |
| --- | --- | --- | --- |
| 1 | **Hero** | `grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));` Logo column centered; copy column flexed. | Pulsating DG logo, bilingual headline, CTA buttons (primary blue, secondary outline). |
| 2 | **Brand Architecture** | Two-column grid with SVG node graph occupying full width (`grid-column: 1 / -1`). | Animated hub-and-spoke visualization, brand blurbs. |
| 3 | **Tone & Visual Identity** | Flex row on desktop; stacked on mobile. | Animated color palette, voice descriptors, image mood boards (dark cards). |
| 4 | **Platform Strategy** | 3x2 card grid using Flexbox wrap. | Icon buttons with pop-in animation and hover transform. |
| 5 | **Daily Cadence & Hashtags** | Split grid; timeline for cadence, stacked cards for hashtag system. | Data bars and bilingual copy. |
| 6 | **Content System & Playbooks** | Accordion or tabbed interface (CSS Grid for cards). | Pillars per niche with accent-coded chips. |
| 7 | **AI & Automation Stack** | Horizontal scroll cards on mobile, three-column grid on desktop. | Tool icons, descriptions. |
| 8 | **Growth Roadmap** | Vertical timeline (`grid-auto-rows: minmax(120px, auto)`). | Animated path drawing with sequential milestones. |
| 9 | **Monetization & Operations** | Dual-column layout with list groups. | CTA for partnerships. |
| 10 | **Closing CTA** | Centered flex column. | Inspirational quote, contact buttons. |

---

## 4. Animation & Micro-Interaction Specification
All animations use cubic-bezier `(0.6, 0.05, 0.2, 1)` and respect `prefers-reduced-motion`.

### 4.1 Hero Pulsing Logo & Background
- **DG Logo SVG Pulse:**
  - Idle: Scale 1, blue stroke.
  - Scroll-in: `@keyframes pulse` scaling 1 → 1.08 → 1 with blue glow `box-shadow: 0 0 40px rgba(0, 102, 255, 0.55)`.
  - Loop every 4s with 2s delay.
- **Floating Geometrics:**
  - SVG rectangles and circles drifting on `transform: translateY(-15px)` to `translateY(15px)` over 8s, alternating direction. Reduced motion: static blurred shapes.

### 4.2 Brand Architecture Node Graph
- Uses `IntersectionObserver` to trigger `class="active"`.
- Lines draw via `stroke-dasharray`/`stroke-dashoffset` animation; nodes scale from 0.8 → 1 with glow.
- Hover on node:
  - Connected line thickens to 3px, color shifts to node accent.
  - Tooltip fades in with bilingual copy.
- Nodes remain highlighted once viewed.

### 4.3 Dynamic Color Palette Swipes
- Circular swatches arranged in a horizontal `flex` row.
- Scroll-in triggers `clip-path: inset(0 100% 0 0)` animation; gradient overlay slides left→right to fill color.
- Secondary animation for niche colors triggered sequentially, mimicking swiping cards.

### 4.4 Platform Strategy Icon Pops
- Icons start at 70% scale, opacity 0.
- On scroll, they spring to 100% with slight overshoot (scale 1.08 → 1).
- Hover: Icon background transitions from dark gray to brand blue or accent, with `transform: translateY(-6px)` and `box-shadow: 0 14px 30px rgba(0, 0, 0, 0.35)`.

### 4.5 Growth Roadmap Timeline
- Vertical line uses `stroke-dashoffset` to self-draw over scroll distance.
- Each milestone dot scales and emits outer glow. Text block slides in from left (English) and right (Arabic) with 250ms stagger.
- On hover/tap, milestone reveals metric badges (e.g., "25K Followers").

### 4.6 Micro-Interaction Extras
- **Language Toggle:** Smooth toggle that mirrors layout direction via `body.dir-rtl` class, animating padding swaps using CSS custom properties.
- **CTA Buttons:** Filled buttons have `box-shadow` ripple on hover; outline buttons invert colors on focus.
- **Accordion Tabs:** Expand/collapse with 200ms height transition and fade for text.

---

## 5. Mockup Description (Desktop & Mobile)

### 5.1 Desktop Narrative
1. **Hero:** Full-width dark background, center-left bilingual headline: “DG Network — Digital Growth Engine” / “شبكة DG — محرك النمو الرقمي”. CTA buttons: Primary (blue) “View Strategy” / “اطلع على الإستراتيجية”, Secondary outline “Download Deck” / “حمّل العرض”. Right side shows animated logo orb with floating polygons.
2. **Brand Architecture:** Dark card with central hub (blue) connected to five colored nodes. English copy below, Arabic mirrored on right using `dir="rtl"`. Hover tooltip example: “DGSPORTS — High-energy sports storytelling / حكايات رياضية مفعمة بالحماس”.
3. **Tone & Identity:** Split layout: left column has descriptive chips with icons; right features animated color swatches and a vertical mood board strip with hover zoom.
4. **Platform Strategy:** Six cards for Facebook, Instagram, TikTok, YouTube, Telegram, Communities. Each card bilingual, icons pop-in sequentially.
5. **Daily Cadence & Hashtags:** Left timeline listing posting cadence with luminous dots; right side has three cards (Discovery, Niche, Branded hashtags) with pill buttons.
6. **Content System & Playbooks:** Tabbed navigation with icons for each niche; selecting a tab animates grid of content pillars and hook statements (English left, Arabic right).
7. **AI & Automation:** Three-column grid of tool cards with logos, supporting copy, and accent underlines.
8. **Growth Roadmap:** Vertical timeline with quarter markers Q1–Q4; milestone stats animate in. Arabic text sits on the right of timeline, English left.
9. **Monetization & Operations:** Two side-by-side cards; operations card uses icon badges for team roles, monetization card lists revenue streams with accent icons.
10. **Closing CTA:** Centered statement overlaying subtle animated gradient; dual-language CTA button row and contact email link.

### 5.2 Mobile Narrative
- Sticky language toggle at top switches layout direction.
- Sections stack with `flex-direction: column-reverse` for Arabic to keep natural reading order.
- SVG animations scale responsively; timeline becomes horizontal scroll on small screens with snap points.

---

## 6. Component Code Snippets

### 6.1 Root CSS Variables & Typography
```css
:root {
  color-scheme: dark;
  --color-black: #0a0a0a;
  --color-blue: #0066ff;
  --color-red: #ff0033;
  --color-gray-900: #1a1a1a;
  --color-gray-100: #f0f0f0;
  --color-white: #ffffff;
  --brand-sports: #ff6b00;
  --brand-tech: #00d9ff;
  --brand-movies: #ff3366;
  --brand-anime: #ff7bff;
  --brand-gaming: #aaff00;
  --fs-h1: clamp(2.4rem, 3vw + 2rem, 4rem);
  --fs-h2: clamp(1.8rem, 2vw + 1.2rem, 3rem);
  --fs-h3: 1.8rem;
  --fs-body-lg: 1.125rem;
  --fs-body: 1rem;
  --fs-meta: 0.875rem;
  --space-1: 0.5rem;
  --space-2: 1rem;
  --space-3: 1.5rem;
  --space-5: 2.5rem;
  --space-8: 4rem;
  --space-13: 6.5rem;
}

body {
  font-family: "Inter", "IBM Plex Sans Arabic", "Noto Sans Arabic", sans-serif;
  background: var(--color-black);
  color: var(--color-white);
  line-height: 1.6;
  letter-spacing: 0.01em;
}

h1, h2, h3 {
  font-family: "Space Grotesk", "IBM Plex Sans Arabic", "Segoe UI", sans-serif;
  letter-spacing: -0.01em;
  margin-bottom: var(--space-2);
}

[dir="rtl"] h1, [dir="rtl"] h2, [dir="rtl"] h3 {
  letter-spacing: 0;
}
```

### 6.2 Hero SVG Logo & Background Shapes
```html
<section class="hero">
  <div class="hero__copy" lang="en">
    <h1>DG Network — Digital Growth Engine</h1>
    <p>Building Algeria's most agile multi-niche content ecosystem.</p>
    <div class="cta-group">
      <a class="btn btn--primary" href="#strategy">View Strategy</a>
      <a class="btn btn--outline" href="#download">Download Deck</a>
    </div>
  </div>
  <div class="hero__visual" aria-hidden="true">
    <svg class="hero-logo" viewBox="0 0 240 240">
      <defs>
        <radialGradient id="dg-glow" r="70%">
          <stop offset="0%" stop-color="#0066ff" stop-opacity="0.9" />
          <stop offset="100%" stop-color="#0066ff" stop-opacity="0" />
        </radialGradient>
      </defs>
      <circle cx="120" cy="120" r="110" fill="url(#dg-glow)" opacity="0.65" />
      <circle class="hero-logo__pulse" cx="120" cy="120" r="70" fill="none" stroke="#0066ff" stroke-width="6" />
      <path class="hero-logo__glyph" d="M70 80h40l20 40 20-40h40l-60 100z" fill="#ffffff" />
    </svg>
    <svg class="hero-shape hero-shape--1" viewBox="0 0 120 120">
      <rect x="20" y="20" width="80" height="80" rx="14" fill="rgba(0,102,255,0.4)" />
    </svg>
    <svg class="hero-shape hero-shape--2" viewBox="0 0 140 140">
      <circle cx="70" cy="70" r="40" fill="rgba(255,0,51,0.35)" />
    </svg>
  </div>
</section>
```

```css
.hero {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  align-items: center;
  padding: var(--space-13) var(--space-5);
  gap: var(--space-5);
  position: relative;
}

.hero-logo__pulse {
  animation: heroPulse 4s ease-in-out infinite 2s;
  filter: drop-shadow(0 0 20px rgba(0, 102, 255, 0.55));
}

@keyframes heroPulse {
  0%, 100% { transform: scale(1); opacity: 0.9; }
  50% { transform: scale(1.08); opacity: 0.5; }
}

.hero-shape {
  position: absolute;
  mix-blend-mode: screen;
  animation: float 8s ease-in-out infinite;
}

.hero-shape--1 { top: 12%; left: 65%; animation-delay: 1s; }
.hero-shape--2 { bottom: 8%; right: 20%; animation-direction: reverse; }

@keyframes float {
  0%, 100% { transform: translateY(-12px); }
  50% { transform: translateY(12px); }
}

@media (prefers-reduced-motion: reduce) {
  .hero-logo__pulse, .hero-shape { animation: none; }
}
```

### 6.3 Brand Architecture Interactive SVG
```html
<section id="brand-architecture" class="architecture" data-animate="graph">
  <svg viewBox="0 0 600 400" class="architecture__graph" role="img" aria-labelledby="graphTitle">
    <title id="graphTitle">DG Network brand architecture</title>
    <g class="node node--hub" data-brand="hub">
      <circle cx="300" cy="200" r="38" />
      <text x="300" y="205">DG</text>
    </g>
    <g class="node node--sports" data-brand="sports" data-tooltip="DGSPORTS — Energetic sports storytelling / حكايات رياضية مفعمة بالحماس">
      <circle cx="120" cy="80" r="28" />
      <text x="120" y="85">SPORTS</text>
      <path class="link" d="M262 188 C200 160 160 120 120 92" />
    </g>
    <g class="node node--tech" data-brand="tech" data-tooltip="DGTECH — Insightful & clean tech coverage / محتوى تقني واضح وعميق">
      <circle cx="500" cy="110" r="28" />
      <text x="500" y="115">TECH</text>
      <path class="link" d="M338 182 C380 160 440 140 500 122" />
    </g>
    <g class="node node--movies" data-brand="movies" data-tooltip="DGMOVIES — Cinematic spotlights / محتوى سينمائي أنيق">
      <circle cx="520" cy="300" r="28" />
      <text x="520" y="305">MOVIES</text>
      <path class="link" d="M340 220 C410 240 460 260 520 288" />
    </g>
    <g class="node node--anime" data-brand="anime" data-tooltip="DGANIME — Vibrant anime universes / عوالم أنمي نابضة">
      <circle cx="110" cy="280" r="28" />
      <text x="110" y="285">ANIME</text>
      <path class="link" d="M260 212 C200 230 160 250 110 268" />
    </g>
    <g class="node node--gaming" data-brand="gaming" data-tooltip="DGGAMING — Neon-charged gameplay / تجارب ألعاب متوهجة">
      <circle cx="300" cy="360" r="28" />
      <text x="300" y="365">GAMING</text>
      <path class="link" d="M300 238 V 332" />
    </g>
  </svg>
</section>
```

```css
.architecture {
  position: relative;
  padding: var(--space-8) 0;
}

.architecture__graph {
  width: 100%;
  max-width: 780px;
  margin: 0 auto;
}

.node circle {
  fill: var(--color-gray-900);
  stroke: var(--color-blue);
  stroke-width: 3;
  filter: drop-shadow(0 0 18px rgba(0, 102, 255, 0.3));
  transform-origin: center;
  transform: scale(0.8);
  opacity: 0;
  transition: transform 0.6s cubic-bezier(0.6, 0.05, 0.2, 1), opacity 0.6s;
}

.node text {
  fill: var(--color-white);
  font-size: 12px;
  text-anchor: middle;
  dominant-baseline: middle;
  font-weight: 600;
}

.link {
  fill: none;
  stroke: var(--color-blue);
  stroke-width: 2;
  stroke-dasharray: 220;
  stroke-dashoffset: 220;
  transition: stroke 0.3s;
}

.architecture.is-active .node circle {
  transform: scale(1);
  opacity: 1;
}

.architecture.is-active .link {
  animation: drawLine 1.2s ease forwards;
}

.node:hover circle {
  stroke-width: 4;
}

.node[data-brand="sports"] circle { stroke: var(--brand-sports); }
.node[data-brand="tech"] circle { stroke: var(--brand-tech); }
.node[data-brand="movies"] circle { stroke: var(--brand-movies); }
.node[data-brand="anime"] circle { stroke: var(--brand-anime); }
.node[data-brand="gaming"] circle { stroke: var(--brand-gaming); }

.node:hover .link {
  stroke-width: 3;
}

@keyframes drawLine {
  to { stroke-dashoffset: 0; }
}
```

```js
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('is-active');
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.4 });

document.querySelectorAll('[data-animate="graph"]').forEach(section => {
  observer.observe(section);
});

// Tooltip handling
const tooltip = document.createElement('div');
tooltip.className = 'graph-tooltip';
document.body.appendChild(tooltip);

document.querySelectorAll('.architecture .node').forEach(node => {
  node.addEventListener('pointerenter', () => {
    const text = node.dataset.tooltip;
    if (!text) return;
    tooltip.innerHTML = text;
    tooltip.classList.add('is-visible');
  });
  node.addEventListener('pointerleave', () => {
    tooltip.classList.remove('is-visible');
  });
  node.addEventListener('pointermove', event => {
    tooltip.style.setProperty('--x', `${event.clientX}px`);
    tooltip.style.setProperty('--y', `${event.clientY}px`);
  });
});
```

```css
.graph-tooltip {
  position: fixed;
  top: var(--y);
  left: var(--x);
  transform: translate(-50%, calc(-100% - 12px));
  background: rgba(10, 10, 10, 0.92);
  color: var(--color-white);
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  font-size: var(--fs-meta);
  max-width: 240px;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.2s ease;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.graph-tooltip.is-visible {
  opacity: 1;
}
```

### 6.4 Dynamic Color Palette Animation
```html
<section class="tone" id="tone">
  <div class="tone__colors" data-animate="swatches">
    <div class="swatch" data-color="black">
      <span>Black #0a0a0a</span>
    </div>
    <div class="swatch" data-color="blue">
      <span>Blue #0066ff</span>
    </div>
    <div class="swatch" data-color="red">
      <span>Red #ff0033</span>
    </div>
    <div class="swatch" data-color="sports">
      <span>DGSPORTS #ff6b00</span>
    </div>
    <div class="swatch" data-color="tech">
      <span>DGTECH #00d9ff</span>
    </div>
  </div>
</section>
```

```css
.tone__colors {
  display: flex;
  gap: var(--space-3);
  flex-wrap: wrap;
}

.swatch {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: var(--color-gray-900);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  clip-path: inset(0 100% 0 0);
  transition: clip-path 0.7s ease;
}

.swatch span {
  position: absolute;
  bottom: -1.2rem;
  font-size: var(--fs-meta);
}

.swatch::before {
  content: "";
  position: absolute;
  inset: 0;
  transform: translateX(-100%);
  transition: transform 0.7s ease;
}

.swatch[data-color="black"]::before { background: #0a0a0a; }
.swatch[data-color="blue"]::before { background: #0066ff; }
.swatch[data-color="red"]::before { background: #ff0033; }
.swatch[data-color="sports"]::before { background: #ff6b00; }
.swatch[data-color="tech"]::before { background: #00d9ff; }

.tone__colors.is-active .swatch {
  clip-path: inset(0 0 0 0);
}

.tone__colors.is-active .swatch::before {
  transform: translateX(0);
}
```

### 6.5 Platform Strategy Icons
```html
<ul class="platform-grid" data-animate="platforms">
  <li class="platform" data-platform="facebook">
    <svg aria-hidden="true"><use href="#icon-facebook" /></svg>
    <div class="platform__copy">
      <span lang="en">Facebook / Instagram</span>
      <span lang="ar">فيسبوك / إنستغرام</span>
    </div>
  </li>
  <!-- Repeat for TikTok, YouTube, Telegram, etc. -->
</ul>
```

```css
.platform-grid {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-3);
}

.platform {
  background: var(--color-gray-900);
  border-radius: 1.25rem;
  padding: var(--space-3);
  min-width: 220px;
  display: flex;
  align-items: center;
  gap: var(--space-2);
  opacity: 0;
  transform: scale(0.7);
  transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.45s;
}

.platform svg {
  width: 32px;
  height: 32px;
  fill: var(--color-blue);
}

.platforms-ready .platform {
  opacity: 1;
  transform: scale(1);
}

.platform:hover {
  transform: translateY(-6px) scale(1.02);
  background: linear-gradient(135deg, rgba(0, 102, 255, 0.15), rgba(255, 0, 51, 0.12));
  box-shadow: 0 14px 30px rgba(0, 0, 0, 0.35);
}
```

### 6.6 Growth Roadmap Timeline SVG
```html
<section class="roadmap" data-animate="timeline">
  <svg viewBox="0 0 200 800" class="roadmap__line" aria-hidden="true">
    <line x1="100" y1="20" x2="100" y2="780" stroke="#0066ff" stroke-width="3" stroke-linecap="round" stroke-dasharray="760" stroke-dashoffset="760" />
  </svg>
  <div class="roadmap__milestones">
    <article class="milestone" data-quarter="Q1">
      <div class="milestone__dot"></div>
      <h3 lang="en">Q1 Launch → 25K Followers</h3>
      <h3 lang="ar">الربع الأول — 25 ألف متابع</h3>
      <p lang="en">Ignite presence across all platforms with daily shorts.</p>
      <p lang="ar">إطلاق الوجود عبر كل المنصات مع مقاطع يومية.</p>
    </article>
    <!-- Repeat for Q2–Q4 -->
  </div>
</section>
```

```css
.roadmap {
  position: relative;
  display: grid;
  grid-template-columns: auto 1fr;
  gap: var(--space-5);
}

.roadmap__line line {
  transition: stroke-dashoffset 1.2s ease;
}

.roadmap.is-active .roadmap__line line {
  stroke-dashoffset: 0;
}

.milestone {
  position: relative;
  padding-left: var(--space-5);
  margin-bottom: var(--space-5);
  opacity: 0;
  transform: translateX(-24px);
  transition: transform 0.5s ease, opacity 0.5s ease;
}

.milestone[dir="rtl"] {
  padding-left: 0;
  padding-right: var(--space-5);
  transform: translateX(24px);
}

.roadmap.is-active .milestone {
  opacity: 1;
  transform: translateX(0);
}

.milestone__dot {
  position: absolute;
  left: 0;
  top: 0.6rem;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: var(--color-red);
  box-shadow: 0 0 16px rgba(255, 0, 51, 0.65);
}

.milestone[dir="rtl"] .milestone__dot {
  left: auto;
  right: 0;
}
```

---

## 7. Content Strategy Modules
Each content block includes bilingual copy wrappers:
```html
<div class="bilingual">
  <p lang="en">One video becomes many: TikTok → Reel → Short → Story → Carousel.</p>
  <p lang="ar" dir="rtl">فيديو واحد يُستخدم في عدة صيغ...</p>
</div>
```
- Use `[lang="ar"]` selectors to adjust font-weight and letter spacing.
- Mirror layout via `.page[dir="rtl"] .grid { direction: rtl; }` and `flex-direction: row-reverse` as needed.

---

## 8. Accessibility & Performance Considerations
- **Contrast:** All text on dark surfaces meets WCAG AA (blue/white on black). Brand accents used for outlines + glows, not body text alone.
- **Localization:** `lang` attributes and `dir` toggles for screen readers. Headings announced in both languages.
- **Performance:** Inline critical SVG, lazy-load background media, use `prefers-reduced-motion` fallback.
- **Keyboard Navigation:** Focus outlines use blue glow; tooltips accessible via focus events.

---

## 9. Implementation Checklist
1. Import Google Fonts (`Space Grotesk`, `Inter`, `IBM Plex Sans Arabic`, `Noto Sans Arabic`).
2. Build section wrappers using CSS Grid with mobile-first Flex fallbacks.
3. Attach `IntersectionObserver` triggers for hero, palette, platform icons, timeline.
4. Test bilingual toggle to ensure mirrored layout retains hierarchy.
5. Validate animations respect reduced motion preference.

---

## 10. Final CTA Copy
- **English:** “DG Network stands as the creative engine for Algeria — unified visuals, daily value, authentic voices.”
- **Arabic:** “شبكة DG هي المحرك الإبداعي للجزائر — هوية موحّدة، قيمة يومية، وأصوات حقيقية.”
- Primary CTA: “Let’s Build DG Network.” / “لننطلق مع DG Network.”

