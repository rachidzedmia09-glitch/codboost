const qs = (sel, ctx = document) => ctx.querySelector(sel);
const qsa = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

const page = qs('.page');
const loader = qs('.page-loader');
const progressBar = qs('.progress-bar span');
const nav = qs('.main-nav');
const toggle = qs('.nav-toggle');
const yearEl = qs('#year');
const canvas = qs('#cosmos');

const setYear = () => {
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }
};

const handleNavToggle = () => {
  if (!toggle || !nav) return;
  toggle.addEventListener('click', () => {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!expanded));
    nav.classList.toggle('open');
  });
};

const updateProgress = () => {
  if (!progressBar) return;
  const doc = document.documentElement;
  const scrollTop = doc.scrollTop || document.body.scrollTop;
  const scrollHeight = doc.scrollHeight - doc.clientHeight;
  const progress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
  progressBar.style.transform = `translateX(${progress - 100}%)`;
};

const initScrollProgress = () => {
  updateProgress();
  window.addEventListener('scroll', updateProgress, { passive: true });
  window.addEventListener('resize', updateProgress);
};

const animatePageIn = () => {
  if (!page) return;
  page.setAttribute('data-transition-out', '');
  requestAnimationFrame(() => {
    page.removeAttribute('data-transition-out');
    page.setAttribute('data-transition-in', '');
    setTimeout(() => {
      page.removeAttribute('data-transition-in');
    }, 600);
  });
};

const revealElements = () => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.2 }
  );

  qsa('.hero-content, .overview-card, .brand-card, .mission-heading, .mission-card, .mission-aside, .mission-highlight, .closing-cta').forEach(
    (el, index) => {
      el.style.setProperty('--delay', `${index * 60}ms`);
      el.classList.add('will-reveal');
      observer.observe(el);
    }
  );
};

const bindSmoothScroll = () => {
  const links = qsa('a[data-scroll]');
  links.forEach((link) => {
    link.addEventListener('click', (event) => {
      const href = link.getAttribute('href');
      if (!href || !href.startsWith('#')) return;
      const target = qs(href);
      if (!target) return;
      event.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      if (nav?.classList.contains('open')) {
        nav.classList.remove('open');
        toggle?.setAttribute('aria-expanded', 'false');
      }
    });
  });
};

const initScrollSpy = () => {
  const sections = qsa('[data-nav-section]');
  const links = qsa('.main-nav a[data-scroll]');

  if (!sections.length || !links.length) return;

  const update = () => {
    const threshold = window.innerHeight * 0.35;
    let currentId = sections[0].id;

    sections.forEach((section) => {
      const rect = section.getBoundingClientRect();
      if (rect.top <= threshold && rect.bottom >= threshold) {
        currentId = section.id;
      }
    });

    links.forEach((link) => {
      const href = link.getAttribute('href');
      if (!href || !href.startsWith('#')) return;
      const id = href.slice(1);
      if (id === currentId) {
        link.setAttribute('aria-current', 'true');
      } else {
        link.removeAttribute('aria-current');
      }
    });
  };

  update();
  window.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update);
};

const initParallax = () => {
  const sections = qsa('[data-parallax]');
  if (!sections.length) return;

  const handle = () => {
    sections.forEach((section) => {
      const rect = section.getBoundingClientRect();
      const center = rect.top + rect.height / 2;
      const offset = (window.innerHeight / 2 - center) * 0.12;
      section.style.setProperty('--parallax-y', `${offset.toFixed(2)}px`);
    });
  };

  handle();
  window.addEventListener('scroll', handle, { passive: true });
  window.addEventListener('resize', handle);
};

const bindMissionSpark = () => {
  const missions = qsa('.mission');
  missions.forEach((mission) => {
    mission.addEventListener('pointermove', (event) => {
      const rect = mission.getBoundingClientRect();
      const x = ((event.clientX - rect.left) / rect.width) * 100;
      const y = ((event.clientY - rect.top) / rect.height) * 100;
      mission.style.setProperty('--spark-x', `${x.toFixed(2)}%`);
      mission.style.setProperty('--spark-y', `${y.toFixed(2)}%`);
    });

    mission.addEventListener('pointerleave', () => {
      mission.style.setProperty('--spark-x', '50%');
      mission.style.setProperty('--spark-y', '50%');
    });
  });
};

const initCanvas = () => {
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const stars = [];
  const STAR_COUNT = 160;
  const PIXEL_RATIO = window.devicePixelRatio || 1;

  const resize = () => {
    const { innerWidth, innerHeight } = window;
    canvas.width = innerWidth * PIXEL_RATIO;
    canvas.height = innerHeight * PIXEL_RATIO;
    ctx.scale(PIXEL_RATIO, PIXEL_RATIO);
  };

  const createStar = () => ({
    x: Math.random() * window.innerWidth,
    y: Math.random() * window.innerHeight,
    z: Math.random() * 0.6 + 0.4,
    vx: (Math.random() - 0.5) * 0.08,
    vy: (Math.random() - 0.5) * 0.08,
    radius: Math.random() * 1.6 + 0.2,
  });

  for (let i = 0; i < STAR_COUNT; i += 1) {
    stars.push(createStar());
  }

  const render = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const gradient = ctx.createRadialGradient(
      canvas.width / 2,
      canvas.height / 2,
      0,
      canvas.width / 2,
      canvas.height / 2,
      Math.max(canvas.width, canvas.height)
    );
    gradient.addColorStop(0, 'rgba(63, 130, 255, 0.18)');
    gradient.addColorStop(0.4, 'rgba(3, 4, 15, 0.9)');
    gradient.addColorStop(1, 'rgba(3, 4, 15, 1)');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    stars.forEach((star) => {
      ctx.beginPath();
      ctx.globalAlpha = star.z;
      ctx.fillStyle = `rgba(${120 + star.z * 100}, ${150 + star.z * 70}, 255, 0.9)`;
      ctx.shadowBlur = 12 * star.z;
      ctx.shadowColor = 'rgba(63, 130, 255, 0.6)';
      ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
      ctx.fill();

      star.x += star.vx;
      star.y += star.vy;

      if (star.x < -20 || star.x > window.innerWidth + 20 || star.y < -20 || star.y > window.innerHeight + 20) {
        Object.assign(star, createStar());
      }
    });

    requestAnimationFrame(render);
  };

  resize();
  render();
  window.addEventListener('resize', resize);
};

const attachHoverTilt = () => {
  const cards = qsa('[data-tilt]');
  const strength = 15;

  cards.forEach((card) => {
    card.addEventListener('mousemove', (event) => {
      const rect = card.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;
      const rotateX = ((y / rect.height - 0.5) * -strength).toFixed(2);
      const rotateY = ((x / rect.width - 0.5) * strength).toFixed(2);
      card.style.transform = `perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });

    card.addEventListener('mouseleave', () => {
      if (card.classList.contains('brand-card')) {
        card.style.transform = 'translateY(-12px) rotate3d(1, -1, 0, 8deg)';
        setTimeout(() => {
          card.style.transform = '';
        }, 200);
      } else {
        card.style.transform = '';
      }
    });
  });
};

const init = () => {
  setYear();
  handleNavToggle();
  initScrollProgress();
  bindSmoothScroll();
  initScrollSpy();
  revealElements();
  initCanvas();
  initParallax();
  bindMissionSpark();
  attachHoverTilt();
  animatePageIn();
  window.addEventListener('load', () => loader?.classList.remove('active'));
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
