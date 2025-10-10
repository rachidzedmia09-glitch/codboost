# Motion Storyboard — DG Network Presentation

Times in milliseconds relative to section activation.

## Hero (`.anim-heroIn`)
- 0ms: Section box opacity 0, translateY(80px), blur(16px), perspective 1200px.
- 120ms: Gooey orbs fade to 0.4 opacity, begin drift.
- 360ms: Box reaches translateY(0), blur 0; gradient ribbon sweeps across top edge.
- 720ms: Headline scale overshoots 1.04 then settles.
- 900ms: CTA chips glow pulse completes.

## Section Pattern (example `.anim-riseIn`)
- 0ms: Section card translateY(40px), opacity 0.
- 120ms: Language chips fade in from either side (LTR from left, RTL from right).
- 260ms: Body text appears with staggered 90ms per paragraph.
- 520ms: Brand grid or bullet icons fade in with scale 0.92 → 1.

## `.anim-clipRight`
- 0ms: Clip inset from right 100%.
- 160ms: Clip exposes 40% width.
- 320ms: Clip exposes 100%, slight skew -2deg resets.
- 520ms: Glow sweep accent completes.

## `.anim-flipIn`
- 0ms: RotateX(-12deg), opacity 0.
- 200ms: Rotation halves, opacity 0.6.
- 360ms: Rotation 0deg, drop shadow intensifies.
- 520ms: Content settled, micro bounce of 1.02 scale resolves.

## `.anim-skewIn`
- 0ms: translateY(30px), skewY(-8deg).
- 200ms: skew reduces to -3deg.
- 360ms: skew 0deg, opacity 1.

## `.anim-circleIn`
- 0ms: clip-path circle(12% at 82% 24%).
- 180ms: circle expands to 90%.
- 360ms: circle 140%, reveals entire content.
- 540ms: circle fades, replaced by static border.

## `.anim-tiltIn`
- 0ms: rotateZ(-3deg), translateY(24px), opacity 0.
- 180ms: rotateZ(-1deg), opacity 0.7.
- 360ms: rotateZ(0deg), opacity 1.

## `.anim-zoomIn`
- 0ms: scale 0.94.
- 180ms: scale 1.02.
- 360ms: scale 0.98.
- 520ms: scale 1.0 stable.

## `.anim-wipeIn`
- 0ms: clip-path polygon(0 0, 100% 0, 100% 0, 0 100%).
- 260ms: polygon reveals diagonal 70%.
- 520ms: polygon fully reveals, accent line sweeps across bottom edge.

## `.anim-floatIn`
- 0ms: translateY(24px), opacity 0.
- 260ms: translateY(-8px), opacity 1.
- 520ms: settle translateY(0).

## `.anim-scaleYIn`
- 0ms: scaleY(0.74) transform-origin top.
- 180ms: scaleY(1.08).
- 360ms: scaleY(0.96).
- 520ms: scaleY(1.0).

## `.anim-blurIn`
- 0ms: filter blur(18px), opacity 0.
- 260ms: blur(8px), opacity 0.7.
- 520ms: blur(0), opacity 1.

## `.anim-boxIn`
- 0ms: translateY(-48px), opacity 0.
- 200ms: translateY(8px), opacity 0.9, box-shadow intensifies to 80%.
- 540ms: translateY(0), box-shadow stabilized.

## `.anim-glowSweep`
- 0ms: border gradient anchored at 0%.
- 180ms: gradient sweeps to 40%.
- 360ms: gradient sweeps to 100%.
- 560ms: gradient loops, subtle neon glow pulses every 1.8s.

## `.anim-rippleIn`
- 0ms: radial gradient scale 0.6, opacity 0.
- 260ms: scale 1.1, opacity 0.8.
- 560ms: scale 1.0, opacity 1. Gradient fades to subtle overlay.

### Micro Storyboards
- Magnetic FAB: pointer enters → button scales 1.08 (120ms) → follows pointer with lag (ease-out). Pointer leaves → resets 200ms.
- Nav dot activation: `dotPulse` halo extends 400ms; `::after` ring rotates 1 turn in 1.2s.
- Progress bar: On scroll, width transitions 240ms; on section snap, quick 160ms ease-out to new value.

### Reduced Motion Storyboard
- All transforms replaced with `opacity` fade (0 → 1). Starfield animation paused; gooey orbs static with 60% opacity.
