(function () {
  const scroller = document.getElementById('slides');
  const slides = Array.from(document.querySelectorAll('.slide'));
  const dots = Array.from(document.querySelectorAll('.dots button'));
  const progress = document.getElementById('progress');
  const prevBtn = document.querySelector('.fab.prev');
  const nextBtn = document.querySelector('.fab.next');

  let activeIndex = 0;

  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const index = slides.indexOf(entry.target);
          if (index !== -1) {
            setActive(index);
          }
          if (!entry.target.dataset.revealed) {
            entry.target.dataset.revealed = 'true';
            entry.target.querySelectorAll('.reveal').forEach((el, idx) => {
              const delay = idx ? Math.min(idx * 90, 360) : 0;
              if (delay) {
                el.style.transitionDelay = `${delay}ms`;
              }
              requestAnimationFrame(() => {
                el.classList.add('show');
              });
            });
          }
        }
      });
    },
    {
      root: scroller,
      threshold: 0.55,
    }
  );

  slides.forEach((slide) => revealObserver.observe(slide));

  function setActive(index) {
    if (index === activeIndex) return;
    activeIndex = index;
    dots.forEach((btn, idx) => {
      const current = idx === index;
      btn.setAttribute('aria-current', current ? 'true' : 'false');
    });
  }

  dots.forEach((button) => {
    button.addEventListener('click', () => {
      const target = document.querySelector(button.dataset.target);
      if (target) {
        target.scrollIntoView({ behavior: prefersReducedMotion() ? 'auto' : 'smooth', block: 'start' });
      }
    });
  });

  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  function scrollToIndex(delta) {
    const nextIndex = Math.min(slides.length - 1, Math.max(0, activeIndex + delta));
    slides[nextIndex].scrollIntoView({ behavior: prefersReducedMotion() ? 'auto' : 'smooth', block: 'start' });
  }

  prevBtn.addEventListener('click', () => scrollToIndex(-1));
  nextBtn.addEventListener('click', () => scrollToIndex(1));

  document.addEventListener('keydown', (event) => {
    if (['ArrowDown', 'PageDown'].includes(event.key)) {
      event.preventDefault();
      scrollToIndex(1);
    } else if (['ArrowUp', 'PageUp'].includes(event.key)) {
      event.preventDefault();
      scrollToIndex(-1);
    } else if (event.key === 'Home') {
      event.preventDefault();
      slides[0].scrollIntoView({ behavior: prefersReducedMotion() ? 'auto' : 'smooth', block: 'start' });
    } else if (event.key === 'End') {
      event.preventDefault();
      slides[slides.length - 1].scrollIntoView({ behavior: prefersReducedMotion() ? 'auto' : 'smooth', block: 'start' });
    }
  });

  function updateProgress() {
    const scrollTop = scroller.scrollTop;
    const max = scroller.scrollHeight - scroller.clientHeight;
    const ratio = max > 0 ? scrollTop / max : 0;
    progress.style.width = `${ratio * 100}%`;
  }

  scroller.addEventListener('scroll', () => {
    updateProgress();
  });

  updateProgress();

  // Magnetic FABs
  const fabs = [prevBtn, nextBtn];
  fabs.forEach((fab) => {
    fab.addEventListener('pointermove', (event) => {
      const rect = fab.getBoundingClientRect();
      const relX = event.clientX - rect.left;
      const relY = event.clientY - rect.top;
      const normX = (relX / rect.width - 0.5) * 2;
      const normY = (relY / rect.height - 0.5) * 2;
      fab.style.setProperty('--tx', `${normX * 10}px`);
      fab.style.setProperty('--ty', `${normY * 10}px`);
      fab.style.setProperty('--mx', `${relX}px`);
      fab.style.setProperty('--my', `${relY}px`);
    });
    fab.addEventListener('pointerleave', () => {
      fab.style.setProperty('--tx', '0px');
      fab.style.setProperty('--ty', '0px');
    });
  });

  // Starfield
  const canvas = document.getElementById('space');
  const ctx = canvas.getContext('2d');
  const STAR_COUNT = 220;
  const stars = [];
  const pointer = { x: 0, y: 0 };
  let width = 0;
  let height = 0;
  let dpr = Math.min(window.devicePixelRatio || 1, 2);
  const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  let reduceMotion = motionQuery.matches;

  if (motionQuery.addEventListener) {
    motionQuery.addEventListener('change', (event) => {
      reduceMotion = event.matches;
    });
  } else if (motionQuery.addListener) {
    motionQuery.addListener((event) => {
      reduceMotion = event.matches;
    });
  }

  function createStar() {
    return {
      x: Math.random(),
      y: Math.random(),
      z: Math.random() * 0.9 + 0.1,
    };
  }

  function resize() {
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    width = window.innerWidth;
    height = window.innerHeight;
    canvas.width = width * dpr;
    canvas.height = height * dpr;
    canvas.style.width = `${width}px`;
    canvas.style.height = `${height}px`;
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  }

  function initStars() {
    stars.length = 0;
    for (let i = 0; i < STAR_COUNT; i += 1) {
      stars.push(createStar());
    }
  }

  function wrapStar(star) {
    star.x = Math.random();
    star.y = -0.1;
    star.z = Math.random() * 0.9 + 0.1;
  }

  let lastTime = 0;
  function render(now) {
    const delta = now - lastTime;
    lastTime = now;
    ctx.clearRect(0, 0, width, height);

    for (const star of stars) {
      const speed = reduceMotion ? 0 : (delta || 16) * 0.00005 / star.z;
      star.y += speed;
      if (star.y > 1.2) {
        wrapStar(star);
      }
      const parallax = reduceMotion ? 0 : 30;
      const px = (star.x - 0.5) * width * (1 / star.z) + width / 2 + pointer.x * (1 - star.z) * parallax;
      const py = (star.y - 0.5) * height * (1 / star.z) + height / 2 + pointer.y * (1 - star.z) * parallax;

      if (px < -50 || px > width + 50 || py < -50 || py > height + 50) {
        wrapStar(star);
        continue;
      }

      const size = Math.max(0.4, (1.1 - star.z) * 2.2);
      const brightness = Math.min(255, Math.floor(200 + (1 - star.z) * 55));
      ctx.fillStyle = `rgba(${brightness}, ${brightness}, 255, 0.85)`;
      ctx.beginPath();
      ctx.arc(px, py, size, 0, Math.PI * 2);
      ctx.fill();
    }

    requestAnimationFrame(render);
  }

  window.addEventListener('resize', () => {
    resize();
  });

  window.addEventListener('pointermove', (event) => {
    const ratioX = (event.clientX / width - 0.5) * 2;
    const ratioY = (event.clientY / height - 0.5) * 2;
    pointer.x = ratioX;
    pointer.y = ratioY;
  });

  scroller.addEventListener('scroll', () => {
    const ratio = scroller.scrollTop / (scroller.scrollHeight - scroller.clientHeight || 1);
    pointer.y = (ratio - 0.5) * 0.6;
  });

  resize();
  initStars();
  requestAnimationFrame(render);
})();
