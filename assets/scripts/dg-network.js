const root = document.documentElement;
const body = document.body;
const themeToggle = document.querySelector('#theme-toggle');
const themeMenu = document.querySelector('#theme-menu');
const toast = document.querySelector('.toast');
const toastTrigger = document.querySelector('#toast-trigger');
const commandDialog = document.querySelector('#command-palette');
const commandToggle = document.querySelector('#command-palette-toggle');
const commandInput = document.querySelector('#command-search');
const commandResults = document.querySelector('.command-palette__results');
const navToggle = document.querySelector('.primary-nav__toggle');
const navList = document.querySelector('.primary-nav__list');
const scrollProgress = document.querySelector('.scroll-progress__bar');
const skeletonTemplate = document.querySelector('#skeleton-template');
const observedSections = [...document.querySelectorAll('[data-observe]')];
const parallaxNodes = [...document.querySelectorAll('[data-parallax]')];
const tiltNodes = [...document.querySelectorAll('[data-tilt]')];
const sections = [...document.querySelectorAll('[data-section]')];
const sectionAnchors = sections.map((section) => ({
  id: section.id,
  label: section.querySelector('h1, h2')?.textContent ?? section.id,
}));

let toastTimeout;
const prefersColorScheme = window.matchMedia('(prefers-color-scheme: dark)');
let prefersDark = prefersColorScheme.matches;

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

body.dataset.motion = prefersReducedMotion.matches ? 'reduced' : 'default';
body.dataset.themeActive = prefersDark ? 'dark' : 'light';

const applyTheme = (targetTheme) => {
  const resolved = targetTheme === 'auto' ? (prefersDark ? 'dark' : 'light') : targetTheme;
  if (document.startViewTransition) {
    document.startViewTransition(() => {
      root.setAttribute('data-theme', targetTheme);
      body.dataset.themeActive = resolved;
    });
  } else {
    root.setAttribute('data-theme', targetTheme);
    body.dataset.themeActive = resolved;
  }

  [...themeMenu.querySelectorAll('button')].forEach((btn) => {
    const isActive = btn.dataset.themeOption === targetTheme;
    btn.setAttribute('aria-pressed', String(isActive));
  });
};

const savedTheme = window.localStorage.getItem('dg-theme');
if (savedTheme) {
  applyTheme(savedTheme);
} else {
  applyTheme(root.getAttribute('data-theme') ?? 'auto');
}

prefersColorScheme.addEventListener('change', (event) => {
  prefersDark = event.matches;
  if ((root.getAttribute('data-theme') ?? 'auto') === 'auto') {
    body.dataset.themeActive = prefersDark ? 'dark' : 'light';
  }
});

if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    if (!themeMenu.matches(':popover-open')) {
      themeMenu.showPopover();
    } else {
      themeMenu.hidePopover();
    }
  });
}

if (themeMenu) {
  themeMenu.addEventListener('click', (event) => {
    const btn = event.target.closest('button[data-theme-option]');
    if (!btn) return;
    const option = btn.dataset.themeOption;
    applyTheme(option);
    window.localStorage.setItem('dg-theme', option);
    themeMenu.hidePopover();
  });
}

prefersReducedMotion.addEventListener('change', (event) => {
  body.dataset.motion = event.matches ? 'reduced' : 'default';
});

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
      } else {
        entry.target.classList.remove('is-visible');
      }
    });
  },
  {
    threshold: [0.15, 0.6],
  }
);

observedSections.forEach((node) => observer.observe(node.closest('.plan-section') ?? node));

const updateProgress = () => {
  const scrolled = window.scrollY;
  const height = document.documentElement.scrollHeight - window.innerHeight;
  const progress = Math.max(0, Math.min(1, scrolled / height));
  body.style.setProperty('--progress', `${progress * 100}%`);
  if (!scrollProgress || prefersReducedMotion.matches) return;
  scrollProgress.animate(
    {
      transform: [`scaleX(${progress})`],
    },
    {
      duration: 160,
      easing: 'ease-out',
      fill: 'forwards',
    }
  );
};

updateProgress();
window.addEventListener('scroll', updateProgress, { passive: true });
window.addEventListener('resize', updateProgress);

const parallaxHandler = () => {
  if (prefersReducedMotion.matches) return;
  const offsetY = window.scrollY;
  parallaxNodes.forEach((node) => {
    const speed = Number.parseFloat(node.dataset.speed ?? '0.08');
    const translate = offsetY * speed * -1;
    node.style.transform = `translate3d(0, ${translate}px, 0)`;
  });
};

parallaxHandler();
window.addEventListener('scroll', parallaxHandler, { passive: true });

const tiltHandler = (event, element) => {
  const bounds = element.getBoundingClientRect();
  const centerX = bounds.left + bounds.width / 2;
  const centerY = bounds.top + bounds.height / 2;
  const x = event.clientX - centerX;
  const y = event.clientY - centerY;
  const rotateX = ((y / bounds.height) * -12).toFixed(2);
  const rotateY = ((x / bounds.width) * 12).toFixed(2);
  element.classList.add('is-tilting');
  element.style.transform = `perspective(1600px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(6px)`;
};

const resetTilt = (element) => {
  element.classList.remove('is-tilting');
  element.style.transform = '';
};

tiltNodes.forEach((node) => {
  node.addEventListener('pointermove', (event) => tiltHandler(event, node));
  node.addEventListener('pointerleave', () => resetTilt(node));
});

const showToast = () => {
  if (!toast) return;
  toast.hidden = false;
  requestAnimationFrame(() => toast.classList.add('is-visible'));
  window.clearTimeout(toastTimeout);
  toastTimeout = window.setTimeout(() => {
    toast.classList.remove('is-visible');
    toast.addEventListener(
      'transitionend',
      () => {
        toast.hidden = true;
      },
      { once: true }
    );
  }, 3200);
};

toastTrigger?.addEventListener('click', showToast);

if (navToggle) {
  navToggle.addEventListener('click', () => {
    const expanded = navToggle.getAttribute('aria-expanded') === 'true';
    navToggle.setAttribute('aria-expanded', String(!expanded));
    navList?.toggleAttribute('data-open', !expanded);
  });
}

const initCommandPalette = () => {
  if (!commandDialog) return;

  const buildResults = (filter = '') => {
    const fragment = document.createDocumentFragment();
    const filtered = sectionAnchors.filter(({ label }) => label.toLowerCase().includes(filter.toLowerCase()));

    filtered.forEach(({ id, label }) => {
      const item = document.createElement('li');
      item.dataset.sectionId = id;
      item.role = 'option';
      item.textContent = label;
      fragment.appendChild(item);
    });

    commandResults.innerHTML = '';
    commandResults.appendChild(fragment);

    if (!filtered.length) {
      const empty = document.createElement('li');
      empty.textContent = 'No sections found';
      empty.setAttribute('aria-disabled', 'true');
      commandResults.appendChild(empty);
    }
  };

  buildResults();

  const openDialog = () => {
    if (commandDialog.open) return;
    commandDialog.showModal();
    commandInput.value = '';
    buildResults();
    requestAnimationFrame(() => commandInput.focus());
  };

  const closeDialog = () => {
    commandDialog.close();
  };

  commandToggle?.addEventListener('click', openDialog);
  document.addEventListener('keydown', (event) => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
      event.preventDefault();
      openDialog();
    }

    if (event.key === 'Escape' && commandDialog.open) {
      closeDialog();
    }
  });

  commandDialog.addEventListener('cancel', (event) => {
    event.preventDefault();
    closeDialog();
  });

  commandInput.addEventListener('input', (event) => {
    const { value } = event.target;
    buildResults(value);
  });

  commandResults.addEventListener('click', (event) => {
    const item = event.target.closest('li[data-section-id]');
    if (!item) return;
    const target = document.getElementById(item.dataset.sectionId);
    if (target) {
      target.scrollIntoView({ behavior: prefersReducedMotion.matches ? 'auto' : 'smooth', block: 'start' });
    }
    closeDialog();
  });
};

initCommandPalette();

const loadSkeletons = () => {
  if (!skeletonTemplate?.content) return;
  const clones = Array.from({ length: 3 }, () => skeletonTemplate.content.firstElementChild.cloneNode(true));
  const futureSection = document.querySelector('#future .plan-section__content');
  if (!futureSection) return;
  const placeholderWrapper = document.createElement('div');
  placeholderWrapper.className = 'skeleton-wrapper';
  clones.forEach((clone) => placeholderWrapper.appendChild(clone));
  futureSection.prepend(placeholderWrapper);

  setTimeout(() => {
    placeholderWrapper.classList.add('is-loaded');
    placeholderWrapper.addEventListener(
      'transitionend',
      () => {
        placeholderWrapper.remove();
      },
      { once: true }
    );
  }, 1500);
};

window.addEventListener('load', loadSkeletons);

const lazyObserver = new IntersectionObserver(
  (entries, obs) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const target = entry.target;
        const src = target.dataset.src;
        if (src) {
          target.addEventListener(
            'load',
            () => {
              target.classList.add('is-loaded');
            },
            { once: true }
          );
          target.src = src;
          target.removeAttribute('data-src');
        } else {
          target.classList.add('is-loaded');
        }
        obs.unobserve(target);
      }
    });
  },
  {
    rootMargin: '150px',
    threshold: 0.1,
  }
);

[...document.querySelectorAll('[data-lazy]')].forEach((node) => lazyObserver.observe(node));

const handleViewTransitions = () => {
  if (!document.startViewTransition) return;
  const transitions = document.querySelectorAll('a[href^="#"]');
  transitions.forEach((anchor) => {
    anchor.addEventListener('click', (event) => {
      const href = anchor.getAttribute('href');
      if (!href || href.length <= 1) return;
      const target = document.querySelector(href);
      if (!target) return;
      event.preventDefault();
      document.startViewTransition(() => {
        target.scrollIntoView({ behavior: prefersReducedMotion.matches ? 'auto' : 'smooth', block: 'start' });
      });
    });
  });
};

handleViewTransitions();

const scrollSpyObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      const id = entry.target.id;
      const navLink = document.querySelector(`.primary-nav__list a[href="#${id}"]`);
      if (!navLink) return;
      if (entry.isIntersecting) {
        document.querySelectorAll('.primary-nav__list a[aria-current="true"]').forEach((link) => link.removeAttribute('aria-current'));
        navLink.setAttribute('aria-current', 'true');
      }
    });
  },
  { threshold: 0.6 }
);

sections.forEach((section) => scrollSpyObserver.observe(section));

const inertHandler = () => {
  const open = commandDialog?.open || false;
  document.querySelectorAll('body > :not(dialog)').forEach((node) => {
    if (open) {
      node.inert = node !== commandDialog;
    } else {
      node.inert = false;
    }
  });
};

commandDialog?.addEventListener('close', inertHandler);
commandDialog?.addEventListener('cancel', inertHandler);
commandDialog?.addEventListener('show', inertHandler);

const initSmoothScrollSnap = () => {
  const container = document.querySelector('[data-scroll-snap]');
  if (!container) return;
  let isPointerDown = false;
  container.addEventListener('pointerdown', () => {
    isPointerDown = true;
  });
  container.addEventListener('pointerup', () => {
    isPointerDown = false;
  });
  container.addEventListener('scroll', () => {
    if (!isPointerDown || prefersReducedMotion.matches) return;
    container.style.scrollSnapType = 'none';
    window.clearTimeout(container.snapTimeout);
    container.snapTimeout = window.setTimeout(() => {
      container.style.scrollSnapType = 'x mandatory';
    }, 180);
  });
};

initSmoothScrollSnap();

const initCommandPaletteKeyboard = () => {
  if (!commandDialog) return;
  commandResults.addEventListener('mousemove', (event) => {
    const item = event.target.closest('li[data-section-id]');
    if (!item) return;
    commandResults.querySelectorAll('li[aria-selected="true"]').forEach((node) => node.removeAttribute('aria-selected'));
    item.setAttribute('aria-selected', 'true');
  });

  commandResults.addEventListener('keydown', (event) => {
    const items = [...commandResults.querySelectorAll('li[data-section-id]')];
    if (!items.length) return;
    const currentIndex = items.findIndex((item) => item.getAttribute('aria-selected') === 'true');
    let nextIndex = currentIndex;
    if (event.key === 'ArrowDown') {
      nextIndex = (currentIndex + 1) % items.length;
      event.preventDefault();
    } else if (event.key === 'ArrowUp') {
      nextIndex = (currentIndex - 1 + items.length) % items.length;
      event.preventDefault();
    } else if (event.key === 'Enter') {
      if (currentIndex >= 0) {
        items[currentIndex].click();
      }
    }

    if (nextIndex !== currentIndex && items[nextIndex]) {
      items.forEach((item) => item.removeAttribute('aria-selected'));
      items[nextIndex].setAttribute('aria-selected', 'true');
      items[nextIndex].scrollIntoView({ block: 'nearest' });
    }
  });
};

initCommandPaletteKeyboard();

commandResults?.setAttribute('tabindex', '-1');
commandDialog?.addEventListener('show', () => {
  requestAnimationFrame(() => commandResults?.focus());
});

const initProgressIndicator = () => {
  const indicator = document.querySelector('.scroll-progress__bar');
  if (!indicator || typeof CSS === 'undefined' || !CSS.registerProperty) return;
  try {
    CSS.registerProperty({
      name: '--progress',
      syntax: '<percentage>',
      inherits: false,
      initialValue: '0%'
    });
  } catch (error) {
    // Property already registered; ignore.
  }

  document.addEventListener('scroll', () => {
    const progress = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    indicator.style.setProperty('--progress', `${progress}%`);
  });
};

initProgressIndicator();
