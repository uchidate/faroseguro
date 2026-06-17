/* Faro Seguro — main.js */
(function () {
  'use strict';

  /* ── Header scroll ───────────────────────── */
  const header = document.getElementById('fs-header');
  if (header) {
    let ticking = false;
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          header.classList.toggle('scrolled', window.scrollY > 10);
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

  /* ── Menu mobile ─────────────────────────── */
  const burger  = document.getElementById('fs-hamburger');
  const nav     = document.getElementById('fs-nav');
  const overlay = document.getElementById('fs-nav-overlay');

  function openMenu() {
    nav?.classList.add('open');
    overlay?.classList.add('visible');
    burger?.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    nav?.classList.remove('open');
    overlay?.classList.remove('visible');
    burger?.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  burger?.addEventListener('click', () =>
    burger.getAttribute('aria-expanded') === 'true' ? closeMenu() : openMenu()
  );
  overlay?.addEventListener('click', closeMenu);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });

  /* ── Busca expansível ─────────────────────── */
  const searchToggle = document.getElementById('fs-search-toggle');
  const searchBar    = document.getElementById('fs-search-bar');
  const searchClose  = document.getElementById('fs-search-close');
  const searchInput  = document.getElementById('fs-search-input');

  searchToggle?.addEventListener('click', () => {
    const open = searchBar?.classList.toggle('open');
    searchToggle.setAttribute('aria-expanded', String(open));
    searchBar?.setAttribute('aria-hidden', String(!open));
    if (open) setTimeout(() => searchInput?.focus(), 160);
  });
  searchClose?.addEventListener('click', () => {
    searchBar?.classList.remove('open');
    searchToggle?.setAttribute('aria-expanded', 'false');
    searchBar?.setAttribute('aria-hidden', 'true');
  });

  /* ── Reading progress bar ─────────────────── */
  const progressBar = document.getElementById('fs-progress-bar');
  const article     = document.getElementById('fs-article-content');

  if (progressBar && article) {
    window.addEventListener('scroll', () => {
      const rect   = article.getBoundingClientRect();
      const total  = article.offsetHeight - window.innerHeight;
      const scroll = Math.max(0, -rect.top);
      const pct    = total > 0 ? Math.min(100, (scroll / total) * 100) : 0;
      progressBar.style.width = pct + '%';
      document.getElementById('fs-progress')?.setAttribute('aria-valuenow', Math.round(pct));
    }, { passive: true });
  }

  /* ── TOC — destaque de seção ativa ─────────── */
  const tocItems = document.querySelectorAll('.fs-toc__item');
  const headings = [];

  tocItems.forEach((item, i) => {
    const link = item.querySelector('a');
    const id   = link?.getAttribute('href')?.replace('#', '');
    const el   = id ? document.getElementById(id) : null;
    if (el) headings.push({ el, item });
  });

  if (headings.length) {
    const io = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          headings.forEach(h => h.item.classList.remove('active'));
          const match = headings.find(h => h.el === entry.target);
          if (match) match.item.classList.add('active');
        }
      });
    }, { rootMargin: '-20% 0px -75% 0px' });

    headings.forEach(h => io.observe(h.el));
  }

  /* ── Reveal cards ─────────────────────────── */
  const revealEls = document.querySelectorAll('.fs-card, .fs-reveal');
  if (revealEls.length) {
    revealEls.forEach(el => el.classList.add('fs-hidden'));
    const revealObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.remove('fs-hidden');
          entry.target.classList.add('fs-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08 });
    revealEls.forEach(el => revealObserver.observe(el));
  }

  /* ── Counters animados [data-count] ─────────── */
  const counters = document.querySelectorAll('[data-count]');
  if (counters.length) {
    function easeOut(t) { return 1 - Math.pow(1 - t, 3); }
    const countObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el    = entry.target;
        const end   = parseFloat(el.dataset.count);
        const dur   = 1800;
        const start = performance.now();
        function tick(now) {
          const t    = Math.min(1, (now - start) / dur);
          const val  = easeOut(t) * end;
          el.textContent = Number.isInteger(end) ? Math.round(val).toLocaleString('pt-BR') : val.toFixed(1);
          if (t < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
        countObserver.unobserve(el);
      });
    }, { threshold: 0.5 });
    counters.forEach(el => countObserver.observe(el));
  }

  /* ── Compartilhar — Copiar link ─────────────── */
  document.querySelectorAll('[data-share-copy]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const url = btn.dataset.shareCopy;
      try {
        await navigator.clipboard.writeText(url);
        const orig = btn.textContent;
        btn.textContent = 'Link copiado';
        btn.classList.add('copied');
        setTimeout(() => {
          btn.textContent = orig;
          btn.classList.remove('copied');
        }, 2500);
      } catch {
        prompt('Copie o link:', url);
      }
    });
  });

  /* ── Ticker: duplica itens para loop infinito ─── */
  const ticker = document.getElementById('fs-ticker');
  if (ticker && ticker.children.length) {
    ticker.innerHTML += ticker.innerHTML;
  }

})();
