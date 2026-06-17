/* Faro Seguro — main.js */

(function () {
  'use strict';

  /* ── Header: sombra ao scrollar ── */
  const header  = document.getElementById('fs-header');
  const SCROLL_THRESHOLD = 10;

  if (header) {
    const onScroll = () => {
      header.classList.toggle('scrolled', window.scrollY > SCROLL_THRESHOLD);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ── Menu mobile ── */
  const hamburger = document.getElementById('fs-hamburger');
  const nav       = document.getElementById('fs-nav');
  const overlay   = document.getElementById('fs-nav-overlay');

  function openMenu() {
    nav.classList.add('open');
    hamburger.setAttribute('aria-expanded', 'true');
    overlay.classList.add('visible');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    nav.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false');
    overlay.classList.remove('visible');
    document.body.style.overflow = '';
  }

  if (hamburger && nav && overlay) {
    hamburger.addEventListener('click', () => {
      nav.classList.contains('open') ? closeMenu() : openMenu();
    });

    overlay.addEventListener('click', closeMenu);

    // Fechar ao navegar
    nav.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', closeMenu);
    });

    // Fechar com Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && nav.classList.contains('open')) closeMenu();
    });
  }

  /* ── Reveal suave nos cards ao scrollar ── */
  if ('IntersectionObserver' in window) {
    const revealEls = document.querySelectorAll('.fs-card, .fs-reveal');

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('fs-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
    );

    revealEls.forEach((el) => {
      el.classList.add('fs-hidden');
      observer.observe(el);
    });
  }

  /* ── Contadores animados (stats) ── */
  const counters = document.querySelectorAll('[data-count]');

  if (counters.length && 'IntersectionObserver' in window) {
    const countObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;

          const el     = entry.target;
          const target = parseFloat(el.dataset.count);
          const suffix = el.dataset.suffix || '';
          const prefix = el.dataset.prefix || '';
          const dur    = 1200;
          const start  = performance.now();

          const tick = (now) => {
            const elapsed = Math.min((now - start) / dur, 1);
            const ease    = 1 - Math.pow(1 - elapsed, 3); // easeOutCubic
            const value   = target * ease;
            el.textContent = prefix + (Number.isInteger(target)
              ? Math.round(value)
              : value.toFixed(1)) + suffix;
            if (elapsed < 1) requestAnimationFrame(tick);
          };

          requestAnimationFrame(tick);
          countObserver.unobserve(el);
        });
      },
      { threshold: 0.5 }
    );

    counters.forEach((el) => countObserver.observe(el));
  }
})();
