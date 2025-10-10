export function initHashRouter({ sections, onNavigate }) {
  function normalize(hash) {
    return hash.replace('#', '');
  }

  function handleHashChange() {
    const targetId = normalize(window.location.hash);
    if (!targetId) return;
    onNavigate(targetId);
  }

  window.addEventListener('hashchange', handleHashChange);

  function setHash(id) {
    if (!id) return;
    const current = normalize(window.location.hash);
    if (current === id) return;
    history.replaceState(null, '', `#${id}`);
  }

  function applyInitial() {
    const targetId = normalize(window.location.hash);
    if (targetId && sections.includes(targetId)) {
      onNavigate(targetId, { smooth: false });
    }
  }

  return { setHash, applyInitial };
}
