export function initKeyboardNav({ total, onRequestScroll }) {
  let currentIndex = 0;

  function setCurrent(index) {
    currentIndex = index;
  }

  function handleKeydown(event) {
    const { key } = event;
    if (["ArrowDown", "ArrowUp", "Home", "End"].includes(key)) {
      event.preventDefault();
    }

    if (key === "ArrowDown") {
      const next = Math.min(total - 1, currentIndex + 1);
      onRequestScroll(next);
    } else if (key === "ArrowUp") {
      const prev = Math.max(0, currentIndex - 1);
      onRequestScroll(prev);
    } else if (key === "Home") {
      onRequestScroll(0);
    } else if (key === "End") {
      onRequestScroll(total - 1);
    } else if (key === "Enter") {
      const active = document.activeElement;
      if (active && active.dataset && active.dataset.target) {
        onRequestScrollById(active.dataset.target);
      }
    }
  }

  function onRequestScrollById(id) {
    if (!id) return;
    const index = Array.from(document.querySelectorAll('.slide')).findIndex((slide) => slide.id === id);
    if (index >= 0) {
      onRequestScroll(index);
    }
  }

  document.addEventListener("keydown", handleKeydown);

  return { setCurrent };
}
