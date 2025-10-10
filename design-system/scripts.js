const page = document.querySelector('.page');
const sections = Array.from(document.querySelectorAll('.section'));
const dotNav = document.querySelector('.dot-nav');
const progressThumb = document.querySelector('.progress-thumb');
const fabGroup = document.querySelector('.fab-group');
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
let scrollOffset = 0;

function buildDots() {
  const frag = document.createDocumentFragment();
  sections.forEach((section, index) => {
    const dot = document.createElement('button');
    dot.className = 'dot';
    dot.type = 'button';
    dot.setAttribute('aria-label', `Go to ${section.querySelector('.section-title')?.textContent ?? 'section'}`);
    dot.dataset.target = section.id;
    dot.addEventListener('click', () => scrollToSection(section));
    frag.appendChild(dot);
  });
  dotNav.appendChild(frag);
}

function scrollToSection(section) {
  section?.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
}

function updateDots(activeIndex) {
  dotNav.querySelectorAll('.dot').forEach((dot, idx) => {
    const active = idx === activeIndex;
    dot.classList.toggle('is-active', active);
    dot.setAttribute('aria-current', active ? 'true' : 'false');
  });
}

function updateProgress() {
  const scrollable = page.scrollHeight - page.clientHeight;
  const ratio = scrollable > 0 ? page.scrollTop / scrollable : 0;
  progressThumb.style.setProperty('--progress', ratio.toFixed(4));
  scrollOffset = page.scrollTop;
}

function initIntersection() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('anim-start');
        const index = sections.indexOf(entry.target);
        if (index >= 0) {
          updateDots(index);
        }
      }
    });
  }, { root: page, threshold: 0.35, rootMargin: '-15% 0px' });

  sections.forEach((section) => observer.observe(section));
}

function initScrollTracking() {
  updateProgress();
  page.addEventListener('scroll', updateProgress, { passive: true });
}

function initKeyboardNav() {
  page.addEventListener('keydown', (event) => {
    const activeIdx = getActiveSectionIndex();
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      const next = sections[Math.min(sections.length - 1, activeIdx + 1)];
      scrollToSection(next);
    }
    if (event.key === 'ArrowUp') {
      event.preventDefault();
      const prev = sections[Math.max(0, activeIdx - 1)];
      scrollToSection(prev);
    }
  });
}

function getActiveSectionIndex() {
  let activeIndex = 0;
  sections.forEach((section, index) => {
    const rect = section.getBoundingClientRect();
    if (rect.top <= window.innerHeight / 2 && rect.bottom >= window.innerHeight / 2) {
      activeIndex = index;
    }
  });
  return activeIndex;
}

function initFabNav() {
  fabGroup.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-direction]');
    if (!button) return;
    const dir = button.dataset.direction;
    const activeIndex = getActiveSectionIndex();
    const targetIndex = dir === 'next' ? Math.min(sections.length - 1, activeIndex + 1) : Math.max(0, activeIndex - 1);
    scrollToSection(sections[targetIndex]);
  });
}

function initMagneticButtons() {
  if (prefersReducedMotion) return;
  const magneticButtons = fabGroup.querySelectorAll('.fab');
  magneticButtons.forEach((button) => {
    button.addEventListener('pointermove', (event) => {
      const rect = button.getBoundingClientRect();
      const offsetX = event.clientX - rect.left - rect.width / 2;
      const offsetY = event.clientY - rect.top - rect.height / 2;
      const strength = 18;
      button.style.setProperty('--magnet-x', `${offsetX / strength}px`);
      button.style.setProperty('--magnet-y', `${offsetY / strength}px`);
      button.style.transform = `translate(calc(var(--magnet-x, 0px)), calc(var(--magnet-y, 0px))) scale(1.05)`;
    });
    button.addEventListener('pointerleave', () => {
      button.style.removeProperty('--magnet-x');
      button.style.removeProperty('--magnet-y');
      button.style.transform = '';
    });
  });
}

function initStarfield() {
  if (prefersReducedMotion) return;
  const canvas = document.getElementById('starfield');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  let stars = [];
  const layers = [0.05, 0.1, 0.18];
  const STAR_COUNT = 160;
  let width = canvas.width = window.innerWidth;
  let height = canvas.height = window.innerHeight;

  function createStar(layer) {
    return {
      x: Math.random() * width,
      y: Math.random() * height,
      radius: Math.random() * 1.2 + 0.2,
      layer,
      twinkleSpeed: Math.random() * 0.002 + 0.001,
      twinkleOffset: Math.random() * 1000
    };
  }

  function buildStars() {
    stars = layers.flatMap((layer) => Array.from({ length: STAR_COUNT }, () => createStar(layer)));
  }

  function render(time) {
    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = '#05070f';
    ctx.fillRect(0, 0, width, height);

    stars.forEach((star) => {
      const alpha = 0.3 + Math.abs(Math.sin(time * star.twinkleSpeed + star.twinkleOffset)) * 0.7;
      const y = (star.y + scrollOffset * star.layer) % height;
      const x = (star.x + scrollOffset * star.layer * 0.2) % width;
      ctx.beginPath();
      ctx.fillStyle = `rgba(233, 238, 247, ${alpha.toFixed(2)})`;
      ctx.arc(x < 0 ? x + width : x, y < 0 ? y + height : y, star.radius, 0, Math.PI * 2);
      ctx.fill();
    });

    requestAnimationFrame(render);
  }

  function onResize() {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
    buildStars();
  }

  buildStars();
  requestAnimationFrame(render);
  window.addEventListener('resize', onResize);
}

function initParallaxOrbs() {
  if (prefersReducedMotion) return;
  const orbs = Array.from(document.querySelectorAll('.orb'));
  if (!orbs.length) return;

  document.addEventListener('pointermove', (event) => {
    const { innerWidth, innerHeight } = window;
    const ratioX = (event.clientX / innerWidth) - 0.5;
    const ratioY = (event.clientY / innerHeight) - 0.5;
    orbs.forEach((orb) => {
      const depth = Number(orb.dataset.depth ?? 0.3);
      orb.style.setProperty('--parallax-x', `${ratioX * depth * 80}px`);
      orb.style.setProperty('--parallax-y', `${ratioY * depth * 80}px`);
    });
  });
}

function init() {
  buildDots();
  initIntersection();
  initScrollTracking();
  initKeyboardNav();
  initFabNav();
  initMagneticButtons();
  initStarfield();
  initParallaxOrbs();
  updateDots(0);
  page.focus();
}

window.addEventListener('load', init);
