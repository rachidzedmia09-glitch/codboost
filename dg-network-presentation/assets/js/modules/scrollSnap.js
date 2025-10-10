export function initScrollSnap({ slides, onActivate }) {
  if (!slides.length) {
    return { scrollToSection: () => {} };
  }

  let activeId = slides[0].id;
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const { id } = entry.target;
          if (id && id !== activeId) {
            activeId = id;
            onActivate(entry.target);
          }
        }
      });
    },
    {
      threshold: 0.6,
    }
  );

  slides.forEach((slide) => observer.observe(slide));

  function scrollToSection(id) {
    const target = slides.find((slide) => slide.id === id);
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      activeId = id;
      onActivate(target);
    }
  }

  return { scrollToSection };
}
