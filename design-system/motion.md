# Motion Spec — DG Network Presentation

## Global Settings
- Curve: `cubic-bezier(.2, .7, .2, 1)`
- Durations: hero 900ms, section default 520ms, micro 220ms, hover 180ms.
- Delay staggering: 90ms increments for child elements unless noted.
- Scroll entry triggered via Intersection Observer (threshold 0.35, root margin `-15% 0px`).

## Section Animation Mapping
| Section | Class | Description | Duration | Delay |
| --- | --- | --- | --- | --- |
| Hero | `.anim-heroIn` | Perspective rise from 40px below, blur 16px → 0, opacity 0 → 1, background gradient sweep | 900ms | 0ms |
| 1️⃣ Brand Architecture | `.anim-riseIn` | Soft vertical rise (translateY 40px → 0) with opacity fade | 520ms | 60ms |
| 2️⃣ Tone & Visual Identity | `.anim-clipRight` | `clip-path: inset(0 0 0 100%)` → `inset(0 0 0 0)` with slight X skew | 520ms | 80ms |
| 3️⃣ Platform Strategy | `.anim-flipIn` | RotateX(-12deg) → 0 with depth perspective and fade | 520ms | 80ms |
| 4️⃣ Daily Posting Cadence | `.anim-skewIn` | SkewY(-8deg) + translateY(30px) → settle | 520ms | 80ms |
| 5️⃣ Hashtags & SEO | `.anim-circleIn` | Circular clip-path expand from 12% to 140% | 540ms | 100ms |
| 6️⃣ Content System | `.anim-tiltIn` | RotateZ(-3deg) + translateY(24px) → 0 | 500ms | 80ms |
| 7️⃣ Niche Playbooks | `.anim-zoomIn` | Scale 0.94 → 1.02 → 1, overshoot 1.02 at 60% | 520ms | 90ms |
| 8️⃣ AI & Automation | `.anim-wipeIn` | Polygonal clip-path from top-right to fill | 520ms | 80ms |
| 9️⃣ Growth Roadmap | `.anim-floatIn` | Float up 24px with overshoot and settle | 520ms | 90ms |
| 🔟 Viral Campaign | `.anim-scaleYIn` | ScaleY from 0.74 (origin top) → 1 | 520ms | 90ms |
| 11️⃣ Monetization | `.anim-blurIn` | Blur 18px → 0 + opacity 0 → 1 | 520ms | 110ms |
| 12️⃣ Operations | `.anim-boxIn` | Drop from -48px with shadow bloom (shadow intensity 0 → 0.45) | 540ms | 100ms |
| 13️⃣ Visual Mockups | `.anim-glowSweep` | Border gradient sweep with `background-position` animation + fade | 560ms | 90ms |
| 14️⃣ Closing | `.anim-rippleIn` | Radial gradient opacity & scale from 0.6 → 1.1 → 1 | 560ms | 100ms |

## Micro-interactions
- **Magnetic FABs**: On pointer move, update `--magnet-x`, `--magnet-y`; use `transform: translate(var(--magnet-x), var(--magnet-y)) scale(var(--hover-scale, 1))`. Reset on leave.
- **Card Hover**: `translateY(-6px)`, `box-shadow: var(--shadow-card)`, apply `filter: drop-shadow(0 0 14px rgba(45,125,246,0.35))`.
- **Dot Active Halo**: Keyframe `dotPulse` to animate halo radius 24px → 36px with fade 0.5 → 0.
- **Gradient Ribbon**: `background-position: 0%` → `100%` in 3.6s loop.
- **Progress Bar**: `transition: transform 240ms var(--motion-curve)`.

## Background & FX
- **Starfield**: 3 layers, 160 stars each. Layer speeds: 0.05, 0.1, 0.18 relative to scroll delta. Stars twinkle via alpha noise using `Math.sin` & `performance.now()`.
- **Gooey Orbs**: 4 orbs with CSS animation `orbFloat` (duration 18s, 22s, 26s, 30s). Colors gradient rotate between blue, violet, cyan, red. Filter defined in SVG `<filter id="goo">` with `feGaussianBlur stdDeviation="18"` and `feColorMatrix` values to amplify alpha.
- **Parallax**: `.orb` uses `transform: translate3d(calc(var(--mouse-x) * 6px), calc(var(--mouse-y) * 6px), 0)` updated with pointer move.

## Reduced Motion
- When `prefers-reduced-motion: reduce`, remove transforms, set animations to `none`, and fallback to `opacity` transitions 220ms. Canvas starfield stops updating; background replaced with static gradient.

Refer to `motion-storyboard.md` for frame-by-frame staging.
