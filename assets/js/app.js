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
  });
};

const transitionTo = (url) => {
  if (!page) {
    window.location.href = url;
    return;
  }
  page.removeAttribute('data-transition-in');
  page.setAttribute('data-transition-out', '');
  loader?.classList.add('active');
  setTimeout(() => {
    window.location.href = url;
  }, 450);
};

const bindTransitions = () => {
  qsa('a[data-transition]').forEach((link) => {
    link.addEventListener('click', (event) => {
      const url = link.getAttribute('href');
      if (!url || url.startsWith('#')) return;
      event.preventDefault();
      transitionTo(url);
    });
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

  qsa('.overview-card, .brand-card, .content-card, .content-aside, .holo-card, .playbook, .campaign-blueprint .split > div, .viral-blueprint, .operations-grid article, .closing-cta, .cta-inner').forEach(
    (el, index) => {
      el.style.setProperty('--delay', `${index * 60}ms`);
      el.classList.add('will-reveal');
      observer.observe(el);
    }
  );
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
  const cards = qsa('.brand-card');
  const strength = 15;

  cards.forEach((card) => {
    card.addEventListener('mousemove', (event) => {
      const rect = card.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;
      const rotateX = ((y / rect.height - 0.5) * -strength).toFixed(2);
      const rotateY = ((x / rect.width - 0.5) * strength).toFixed(2);
      card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });

    card.addEventListener('mouseleave', () => {
      card.style.transform = 'translateY(-12px) rotate3d(1, -1, 0, 8deg)';
      setTimeout(() => {
        card.style.transform = '';
      }, 200);
    });
  });
};

const init = () => {
  setYear();
  handleNavToggle();
  initScrollProgress();
  bindTransitions();
  revealElements();
  initCanvas();
  attachHoverTilt();
  animatePageIn();
  window.addEventListener('load', () => loader?.classList.remove('active'));
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
