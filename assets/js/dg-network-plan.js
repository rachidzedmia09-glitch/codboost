(() => {
    const shell = document.querySelector('.dg-network-shell');
    if (!shell) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Scroll progress indicator
    const progress = document.createElement('div');
    progress.className = 'dg-scroll-progress';
    progress.innerHTML = '<span class="dg-scroll-progress__bar"></span>';
    document.body.append(progress);

    const progressBar = progress.querySelector('.dg-scroll-progress__bar');
    const updateProgress = () => {
        const scrollTop = window.scrollY;
        const height = document.documentElement.scrollHeight - window.innerHeight;
        const ratio = height > 0 ? Math.min(scrollTop / height, 1) : 0;
        progressBar.style.transform = `scaleX(${ratio})`;
    };

    updateProgress();
    window.addEventListener('scroll', updateProgress, { passive: true });
    window.addEventListener('resize', updateProgress);

    // Parallax background effect
    const hero = shell.querySelector('[data-parallax]');
    const heroBg = hero?.querySelector('.dg-hero__bg');
    if (hero && heroBg && !prefersReducedMotion) {
        const parallax = () => {
            const rect = hero.getBoundingClientRect();
            const progress = 1 - Math.min(Math.max((rect.top + rect.height) / (window.innerHeight + rect.height), 0), 1);
            heroBg.style.transform = `translateY(${progress * 60}px) scale(1.6)`;
        };
        parallax();
        window.addEventListener('scroll', parallax, { passive: true });
    }

    // Animate sections on intersection
    const sections = Array.from(shell.querySelectorAll('.dg-section'));
    const reveal = (entry) => {
        const { target } = entry;
        if (entry.isIntersecting) {
            target.animate(
                [
                    { transform: 'translateY(20px)', opacity: 0 },
                    { transform: 'translateY(0)', opacity: 1 }
                ],
                {
                    duration: prefersReducedMotion ? 0 : 600,
                    easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                    fill: 'forwards'
                }
            );
            observer.unobserve(target);
        }
    };

    const observer = new IntersectionObserver((entries) => entries.forEach(reveal), {
        threshold: 0.25,
        rootMargin: '0px 0px -10%'
    });

    sections.forEach((section) => observer.observe(section));

    // Table of contents
    const tocList = shell.querySelector('.dg-toc__list');
    const tocItems = sections.map((section) => {
        const item = document.createElement('li');
        const link = document.createElement('a');
        link.href = `#${section.id}`;
        link.dataset.index = section.dataset.index;
        link.className = 'dg-toc__link';
        link.textContent = section.querySelector('h2')?.textContent ?? section.id;
        item.append(link);
        tocList?.append(item);
        return { section, link };
    });

    const highlightCurrent = () => {
        const fromTop = window.scrollY + window.innerHeight * 0.3;
        let activeId = null;
        for (const { section } of tocItems) {
            const offset = section.offsetTop;
            if (offset <= fromTop) {
                activeId = section.id;
            }
        }
        tocItems.forEach(({ link, section }) => {
            const isActive = section.id === activeId;
            link.setAttribute('aria-current', isActive ? 'true' : 'false');
            section.toggleAttribute('data-highlight', isActive);
        });
    };

    highlightCurrent();
    window.addEventListener('scroll', highlightCurrent, { passive: true });

    // Command palette (Spotlight search)
    const commandButton = shell.querySelector('.dg-command');
    const commandPopover = document.getElementById('dg-command-palette');
    const commandInput = commandPopover?.querySelector('#dg-command-search');
    const commandResults = commandPopover?.querySelector('.dg-command-results');

    const renderCommandResults = (query = '') => {
        if (!commandResults) return;
        commandResults.innerHTML = '';
        const normalized = query.trim().toLowerCase();
        const matches = sections.filter((section) => {
            if (!normalized) return true;
            return section.textContent?.toLowerCase().includes(normalized);
        });
        matches.forEach((section) => {
            const option = document.createElement('button');
            option.type = 'button';
            option.dataset.target = section.id;
            option.textContent = section.querySelector('h2')?.textContent ?? section.id;
            commandResults.append(option);
        });
    };

    commandInput?.addEventListener('input', (event) => {
        renderCommandResults(event.target.value);
    });

    commandResults?.addEventListener('click', (event) => {
        const target = event.target.closest('button[data-target]');
        if (!target) return;
        const id = target.dataset.target;
        const section = document.getElementById(id);
        commandPopover?.hidePopover?.();
        section?.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
        section?.animate(
            [
                { boxShadow: '0 0 0 rgba(255, 255, 255, 0)' },
                { boxShadow: '0 0 0 8px rgba(255, 255, 255, 0.3)' },
                { boxShadow: '0 0 0 rgba(255, 255, 255, 0)' }
            ],
            {
                duration: prefersReducedMotion ? 0 : 900,
                easing: 'ease-out'
            }
        );
    });

    if (commandButton && commandInput) {
        commandButton.addEventListener('click', () => {
            renderCommandResults('');
            requestAnimationFrame(() => commandInput.focus());
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key.toLowerCase() === 'k' && (event.metaKey || event.ctrlKey)) {
            event.preventDefault();
            commandPopover?.togglePopover?.();
            renderCommandResults('');
            requestAnimationFrame(() => commandInput?.focus());
        }
        if (event.key === 'Escape' && commandPopover?.matches(':popover-open')) {
            commandPopover.hidePopover();
        }
    });

    // Dark mode toggle with View Transitions API
    const toggle = shell.querySelector('.dg-theme-toggle');
    const applyTheme = (next) => {
        if (!toggle) return;
        const perform = () => {
            shell.dataset.theme = next;
            toggle.setAttribute('aria-pressed', String(next === 'dark'));
        };
        if (document.startViewTransition) {
            document.startViewTransition(perform);
        } else {
            perform();
        }
    };

    if (toggle) {
        toggle.addEventListener('click', () => {
            const current = shell.dataset.theme === 'dark' ? 'light' : 'dark';
            applyTheme(current);
        });
    }

    // Scroll driven animation fallback for hero content
    if (!prefersReducedMotion && 'animate' in HTMLElement.prototype) {
        const heroContent = hero?.querySelector('.dg-hero__content');
        if (heroContent) {
            const animation = heroContent.animate(
                [
                    { transform: 'translateY(0px) scale(1)', filter: 'blur(0)' },
                    { transform: 'translateY(-60px) scale(0.98)', filter: 'blur(6px)' }
                ],
                {
                    duration: 1,
                    fill: 'both'
                }
            );
            animation.pause();
            const updateHeroAnimation = () => {
                const rect = heroContent.getBoundingClientRect();
                const ratio = Math.min(Math.max(1 - rect.top / window.innerHeight, 0), 1);
                animation.currentTime = ratio;
            };
            updateHeroAnimation();
            window.addEventListener('scroll', updateHeroAnimation, { passive: true });
        }
    }

    // Intersection Observer for shimmer placeholders (example skeleton component)
    const shimmerTargets = document.querySelectorAll('[data-shimmer]');
    if (shimmerTargets.length) {
        const shimmerObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                el.classList.remove('dg-shimmer');
                el.removeAttribute('data-shimmer');
                shimmerObserver.unobserve(el);
            });
        });
        shimmerTargets.forEach((target) => shimmerObserver.observe(target));
    }

    // Smooth anchor scrolling preference
    if (!prefersReducedMotion) {
        document.documentElement.style.scrollBehavior = 'smooth';
    }
})();
