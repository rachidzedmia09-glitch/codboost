export function initProgressBar(progressEl, { total }) {
  const mq = window.matchMedia('(max-width: 768px)');
  let lastValue = 0;

  function applyProgress(index) {
    if (!progressEl) return;
    if (total <= 1) {
      setValue(100);
      return;
    }
    const ratio = (index / (total - 1)) * 100;
    lastValue = ratio;
    setValue(ratio);
  }

  function setValue(value) {
    lastValue = value;
    const clamped = Math.min(100, Math.max(0, value));
    if (mq.matches) {
      progressEl.style.width = `${clamped}%`;
      progressEl.style.height = '100%';
    } else {
      progressEl.style.height = `${clamped}%`;
      progressEl.style.width = '100%';
    }
  }

  mq.addEventListener('change', () => {
    setValue(lastValue);
  });

  return { applyProgress };
}
