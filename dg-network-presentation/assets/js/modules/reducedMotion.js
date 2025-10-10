export function initReducedMotion(root = document.documentElement) {
  const media = window.matchMedia('(prefers-reduced-motion: reduce)');

  function applyPreference(event) {
    const prefersReduce = event.matches;
    root.dataset.motion = prefersReduce ? 'reduce' : 'normal';
  }

  applyPreference(media);
  media.addEventListener('change', applyPreference);
}
