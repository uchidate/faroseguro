/* Guia Antifraude — main.js */
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

  /* ── Hero paths expandíveis ──────────────── */
  document.querySelectorAll('.fs-hero-path__trigger[aria-controls]').forEach(trigger => {
    const card = trigger.closest('.fs-hero-path');
    const sub  = document.getElementById(trigger.getAttribute('aria-controls'));
    if (!card || !sub) return;
    trigger.addEventListener('click', () => {
      const open = card.classList.toggle('open');
      trigger.setAttribute('aria-expanded', String(open));
    });
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


  /* ── Newsletter ───────────────────────────── */
  document.querySelectorAll('.fs-newsletter').forEach(widget => {
    const form  = widget.querySelector('.fs-newsletter__form');
    const msg   = widget.querySelector('.fs-newsletter__msg');
    const ajax  = widget.dataset.ajax;
    const nonce = widget.dataset.nonce;
    if (!form) return;
    form.addEventListener('submit', async e => {
      e.preventDefault();
      const email = form.querySelector('input[name="email"]').value.trim();
      if (!email) return;
      form.classList.add('loading');
      msg.textContent = '';
      try {
        const body = new URLSearchParams({ action: 'fs_newsletter_subscribe', nonce, email });
        const res  = await fetch(ajax, { method: 'POST', body });
        const data = await res.json();
        msg.textContent = data.data?.message ?? (data.success ? 'Inscrito!' : 'Erro. Tente novamente.');
        msg.className   = 'fs-newsletter__msg ' + (data.success ? 'success' : 'error');
        if (data.success) form.reset();
      } catch {
        msg.textContent = 'Erro de conexão.';
        msg.className   = 'fs-newsletter__msg error';
      } finally {
        form.classList.remove('loading');
      }
    });
  });

  /* ── Checklist interativo ─────────────────── */
  document.querySelectorAll('.fs-action-checklist').forEach(cl => {
    const key    = cl.dataset.key;
    const items  = cl.querySelectorAll('.fs-action-checklist__item');
    const prog   = cl.querySelector('.fs-action-checklist__progress');
    const total  = items.length;
    const saved  = JSON.parse(localStorage.getItem(key) || '[]');

    function updateProgress() {
      const done = cl.querySelectorAll('.fs-action-checklist__item.done').length;
      if (prog) prog.textContent = `${done} / ${total}`;
      if (done === total) cl.classList.add('all-done');
      else cl.classList.remove('all-done');
      const state = [...items].map(i => i.classList.contains('done'));
      localStorage.setItem(key, JSON.stringify(state));
    }

    items.forEach((item, i) => {
      const btn = item.querySelector('.fs-action-checklist__check');
      if (saved[i]) { item.classList.add('done'); btn?.setAttribute('aria-pressed', 'true'); }
      btn?.addEventListener('click', () => {
        const done = item.classList.toggle('done');
        btn.setAttribute('aria-pressed', String(done));
        updateProgress();
      });
    });
    updateProgress();
  });

  /* ── Web Share ────────────────────────────── */
  document.querySelectorAll('.fs-share-bar__btn--native').forEach(btn => {
    if (!navigator.share) { btn.style.display = 'none'; return; }
    btn.addEventListener('click', async () => {
      try {
        await navigator.share({ title: btn.dataset.shareTitle, url: btn.dataset.shareUrl });
      } catch {}
    });
  });

  /* ── Quiz ─────────────────────────────────── */
  const quizIntro   = document.getElementById('fs-quiz-intro');
  const quiz        = document.getElementById('fs-quiz');
  const quizResult  = document.getElementById('fs-quiz-result');
  const quizStart   = document.getElementById('fs-quiz-start');
  const quizBack    = document.getElementById('fs-quiz-back');
  const quizRestart = document.getElementById('fs-quiz-restart');
  const quizFill    = document.getElementById('fs-quiz-fill');
  const quizStepLbl = document.getElementById('fs-quiz-step-label');

  if (quizStart && quiz) {
    const questions = [...quiz.querySelectorAll('.fs-quiz__question')];
    const answers   = {};
    let current     = 0;

    const RESULTS = {
      golpe: {
        icon: '⚠️',
        title: 'Você foi vítima de um Golpe',
        desc: 'No golpe, o criminoso manipulou você a agir — fazer uma transferência, fornecer dados ou instalar algo. A ação partiu de você, induzida por engenharia social.',
        links: [
          { text: 'Como identificar golpes', url: '/golpes/' },
          { text: 'O que fazer agora — Bacen', url: 'https://www.bcb.gov.br/meubc/registrar_reclamacao' },
          { text: 'Registrar ocorrência no Consumidor.gov', url: 'https://www.consumidor.gov.br' },
        ],
      },
      fraude: {
        icon: '🔒',
        title: 'Você foi vítima de uma Fraude',
        desc: 'Na fraude, o criminoso agiu por conta própria — sem que você precisasse fazer nada. Acesso não autorizado à conta, clonagem, uso indevido de dados.',
        links: [
          { text: 'Como identificar fraudes', url: '/fraudes/' },
          { text: 'Verificar seu CPF — Registrato BCB', url: 'https://registrato.bcb.gov.br' },
          { text: 'Registrar ocorrência — Delegacia Digital', url: 'https://www.delegaciaeletronica.policiacivil.sp.gov.br' },
        ],
      },
    };

    function classify() {
      const q2 = answers[2] ?? '';
      if (q2 === 'nao' || q2 === '') return 'fraude';
      return 'golpe';
    }

    function showQuestion(n) {
      questions.forEach(q => { q.hidden = true; delete q.dataset.active; });
      questions[n].hidden = false;
      questions[n].dataset.active = 'true';
      const pct = ((n) / questions.length) * 100;
      if (quizFill) quizFill.style.width = pct + '%';
      if (quizStepLbl) quizStepLbl.textContent = `Pergunta ${n + 1} de ${questions.length}`;
      if (quizBack) quizBack.hidden = n === 0;
    }

    function showResult() {
      const type = classify();
      const r = RESULTS[type];
      quiz.hidden = true;
      quizResult.hidden = false;
      document.getElementById('fs-quiz-result-icon').textContent  = r.icon;
      document.getElementById('fs-quiz-result-title').textContent = r.title;
      document.getElementById('fs-quiz-result-desc').textContent  = r.desc;
      const linksEl = document.getElementById('fs-quiz-result-links');
      linksEl.innerHTML = r.links.map(l =>
        `<a class="fs-quiz-result__link" href="${l.url}" ${l.url.startsWith('http') ? 'target="_blank" rel="noopener"' : ''}>
          ${l.text}
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>`
      ).join('');
      if (quizFill) quizFill.style.width = '100%';
    }

    quizStart.addEventListener('click', () => {
      quizIntro.hidden = true;
      quiz.hidden = false;
      showQuestion(0);
    });

    quiz.addEventListener('click', e => {
      const opt = e.target.closest('.fs-quiz__opt');
      if (!opt) return;
      answers[current + 1] = opt.dataset.value;
      opt.closest('.fs-quiz__options')?.querySelectorAll('.fs-quiz__opt').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      setTimeout(() => {
        if (current < questions.length - 1) { current++; showQuestion(current); }
        else showResult();
      }, 280);
    });

    quizBack?.addEventListener('click', () => {
      if (current > 0) { current--; showQuestion(current); }
    });

    quizRestart?.addEventListener('click', () => {
      Object.keys(answers).forEach(k => delete answers[k]);
      current = 0;
      quizResult.hidden = true;
      quiz.hidden = false;
      quizIntro.hidden = false;
      quiz.hidden = true;
      quizIntro.hidden = false;
      questions.forEach(q => { q.querySelectorAll('.fs-quiz__opt').forEach(o => o.classList.remove('selected')); });
    });
  }

  /* ── Tema claro / escuro ──────────────────── */
  const themeToggle = document.getElementById('fs-theme-toggle');
  const DARK = 'dark';
  const PREF_KEY = 'fs-theme';

  function applyTheme(dark) {
    document.documentElement.dataset.theme = dark ? DARK : '';
    if (themeToggle) themeToggle.setAttribute('aria-pressed', String(dark));
  }

  const savedTheme = localStorage.getItem(PREF_KEY);
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  applyTheme(savedTheme ? savedTheme === DARK : prefersDark);

  themeToggle?.addEventListener('click', () => {
    const isDark = document.documentElement.dataset.theme === DARK;
    applyTheme(!isDark);
    localStorage.setItem(PREF_KEY, !isDark ? DARK : 'light');
  });

  /* ── Archive filter AJAX ──────────────────── */
  document.querySelectorAll('.fs-archive-filter').forEach(bar => {
    const grid     = document.getElementById(bar.dataset.grid);
    const ajax     = bar.dataset.ajax;
    const postType = bar.dataset.postType;
    if (!grid || !ajax) return;

    bar.addEventListener('click', async e => {
      const pill = e.target.closest('.fs-archive-filter__pill');
      if (!pill) return;
      bar.querySelectorAll('.fs-archive-filter__pill').forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      grid.classList.add('loading');
      const body = new URLSearchParams({
        action:    'fs_filter_posts',
        post_type: postType,
        taxonomy:  pill.dataset.taxonomy ?? '',
        term_id:   pill.dataset.termId ?? 0,
        paged:     1,
      });
      try {
        const res  = await fetch(ajax, { method: 'POST', body });
        const data = await res.json();
        if (data.success) grid.innerHTML = data.data.html;
      } finally {
        grid.classList.remove('loading');
      }
    });
  });

})();
