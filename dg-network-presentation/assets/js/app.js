import { initScrollSnap } from './modules/scrollSnap.js';
import { initKeyboardNav } from './modules/keyboardNav.js';
import { initProgressBar } from './modules/progressBar.js';
import { initReducedMotion } from './modules/reducedMotion.js';
import { initFocusRing } from './modules/focusRing.js';
import { initHashRouter } from './modules/hashRouter.js';

const slides = Array.from(document.querySelectorAll('.slide'));
const navButtons = Array.from(document.querySelectorAll('.dot-nav button'));
const progressEl = document.querySelector('[data-progress]');
const statusRegion = document.getElementById('section-status');
const body = document.body;

if (body) {
  body.dataset.printUrl = window.location.href;
}

initReducedMotion();
initFocusRing();

const { applyProgress } = initProgressBar(progressEl, { total: slides.length });
const keyboardNav = initKeyboardNav({
  total: slides.length,
  onRequestScroll: (index) => {
    const target = slides[index];
    if (target) {
      scrollToSection(target.id);
    }
  },
});

let updateHash = () => {};

function updateNavState(section) {
  const index = slides.indexOf(section);
  if (index >= 0) {
    applyProgress(index);
    keyboardNav.setCurrent(index);
  }

  navButtons.forEach((button) => {
    const isActive = button.dataset.target === section.id;
    button.setAttribute('aria-current', isActive ? 'true' : 'false');
  });
}

function announceSection(section) {
  if (!statusRegion) return;
  const announcement = section.dataset.title || section.querySelector('h2, h1')?.textContent || section.id;
  statusRegion.textContent = announcement;
}

function handleSectionActivate(section) {
  slides.forEach((slide) => {
    slide.dataset.active = slide === section ? 'true' : 'false';
  });
  updateNavState(section);
  announceSection(section);
  updateHash(section.id);
}

const { scrollToSection } = initScrollSnap({
  slides,
  onActivate: handleSectionActivate,
});

function navigateById(id, { smooth = true } = {}) {
  const target = slides.find((slide) => slide.id === id);
  if (!target) return;
  if (smooth) {
    scrollToSection(id);
  } else {
    target.scrollIntoView({ behavior: 'auto', block: 'start' });
    handleSectionActivate(target);
  }
}

const { setHash, applyInitial } = initHashRouter({
  sections: slides.map((slide) => slide.id),
  onNavigate: (id, options) => navigateById(id, options || {}),
});

updateHash = setHash;

navButtons.forEach((button) => {
  button.addEventListener('click', () => {
    navigateById(button.dataset.target);
    button.focus();
  });
});

const printButton = document.querySelector('[data-print]');
if (printButton) {
  printButton.addEventListener('click', () => {
    window.print();
  });
}

applyInitial();

if (!window.location.hash && slides[0]) {
  handleSectionActivate(slides[0]);
}

window.addEventListener('load', () => {
  if (window.location.hash) {
    const id = window.location.hash.replace('#', '');
    navigateById(id, { smooth: false });
  }
});
