export function initFocusRing(root = document.body) {
  function handleKey(e) {
    if (e.key === 'Tab') {
      root.classList.add('using-keyboard');
    }
  }

  function handlePointer() {
    root.classList.remove('using-keyboard');
  }

  window.addEventListener('keydown', handleKey, { passive: true });
  window.addEventListener('mousedown', handlePointer, { passive: true });
  window.addEventListener('touchstart', handlePointer, { passive: true });
}
