<?php
/*
Template Name: Contato e Denúncia
*/
get_header();
?>

<main class="fs-page fs-page--contato">

  <!-- Hero -->
  <div class="fs-contato__hero">
    <div class="container">
      <div class="fs-contato__hero-inner">
        <div class="fs-contato__hero-text">
          <p class="fs-eyebrow">Guia Antifraude</p>
          <h1>Contato e denúncia</h1>
          <p class="fs-contato__hero-lead">Identificou um golpe em circulação? Recebeu uma mensagem suspeita? Use este canal para reportar. Cada denúncia ajuda a proteger outras pessoas.</p>
          <div class="fs-contato__stats">
            <div class="fs-contato__stat">
              <strong>24h</strong>
              <span>Tempo médio de análise</span>
            </div>
            <div class="fs-contato__stat">
              <strong>100%</strong>
              <span>Denúncias verificadas</span>
            </div>
            <div class="fs-contato__stat">
              <strong>Gratuito</strong>
              <span>Sem qualquer custo</span>
            </div>
          </div>
        </div>
        <div class="fs-contato__hero-badge">
          <div class="fs-contato__shield">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            <span>Canal Seguro</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="fs-contato__body">
    <div class="container">
      <div class="fs-contato__grid">

        <!-- Formulário principal -->
        <div class="fs-contato__main">

          <div class="fs-contato__section">
            <h2 class="fs-contato__section-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              Reportar golpe ou fraude
            </h2>

            <?php if (function_exists('wpforms_display')): ?>
              <?php wpforms_display(1, true, true); ?>
            <?php else: ?>
            <div class="fs-contato__no-form">
              <div class="fs-contato__no-form-inner">
                <p class="fs-contato__no-form-title">Envie seu relato por e-mail</p>
                <p>Descreva o golpe ou fraude com o máximo de detalhes possível: como ocorreu, quais canais foram usados, prints ou capturas de tela, e qualquer informação que possa ajudar outras pessoas a se protegerem.</p>
                <a href="mailto:contato@guiaantifraude.com?subject=Denúncia de golpe" class="fs-btn fs-btn--primary fs-btn--lg">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
                  Enviar e-mail de denúncia
                </a>
                <p class="fs-contato__no-form-tip">Responderemos em até 24 horas úteis.</p>
              </div>
            </div>
            <?php endif; ?>
          </div>

          <!-- O que incluir na denúncia -->
          <div class="fs-contato__tips">
            <h3 class="fs-contato__tips-title">O que incluir na denúncia</h3>
            <div class="fs-contato__tips-grid">
              <div class="fs-contato__tip">
                <div class="fs-contato__tip-icon fs-contato__tip-icon--blue">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="12" y2="17"/></svg>
                </div>
                <div>
                  <strong>Descrição do golpe</strong>
                  <span>Como aconteceu, qual era o roteiro dos criminosos</span>
                </div>
              </div>
              <div class="fs-contato__tip">
                <div class="fs-contato__tip-icon fs-contato__tip-icon--orange">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                </div>
                <div>
                  <strong>Canal utilizado</strong>
                  <span>WhatsApp, ligação, e-mail, SMS, redes sociais</span>
                </div>
              </div>
              <div class="fs-contato__tip">
                <div class="fs-contato__tip-icon fs-contato__tip-icon--green">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div>
                  <strong>Prints e evidências</strong>
                  <span>Capturas de tela, links, números de telefone</span>
                </div>
              </div>
              <div class="fs-contato__tip">
                <div class="fs-contato__tip-icon fs-contato__tip-icon--purple">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                  <strong>Data e horário</strong>
                  <span>Quando ocorreu a tentativa ou o golpe</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Sidebar -->
        <aside class="fs-contato__aside">

          <!-- Canais oficiais -->
          <div class="fs-contato__card">
            <div class="fs-contato__card-header">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-8h6v8"/></svg>
              <h3>Canais oficiais</h3>
            </div>
            <p class="fs-contato__card-desc">Se sofreu prejuízo financeiro, registre também nos órgãos competentes:</p>
            <div class="fs-contato__channels">

              <a href="https://www.bcb.gov.br/meubc/registrar_reclamacao" target="_blank" rel="noopener" class="fs-contato__channel">
                <div class="fs-contato__channel-icon fs-contato__channel-icon--blue">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                </div>
                <div class="fs-contato__channel-info">
                  <strong>Banco Central (Bacen)</strong>
                  <span>Registre reclamação contra banco ou fintech</span>
                </div>
                <svg class="fs-contato__channel-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
              </a>

              <a href="https://www.consumidor.gov.br" target="_blank" rel="noopener" class="fs-contato__channel">
                <div class="fs-contato__channel-icon fs-contato__channel-icon--green">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                </div>
                <div class="fs-contato__channel-info">
                  <strong>Consumidor.gov.br</strong>
                  <span>Reclamação formal contra empresas</span>
                </div>
                <svg class="fs-contato__channel-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
              </a>

              <a href="https://delegaciavirtual.sinesp.gov.br" target="_blank" rel="noopener" class="fs-contato__channel">
                <div class="fs-contato__channel-icon fs-contato__channel-icon--orange">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="fs-contato__channel-info">
                  <strong>Delegacia Virtual</strong>
                  <span>Boletim de ocorrência online (SINESP)</span>
                </div>
                <svg class="fs-contato__channel-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
              </a>

              <a href="https://www.gov.br/senacon/pt-br" target="_blank" rel="noopener" class="fs-contato__channel">
                <div class="fs-contato__channel-icon fs-contato__channel-icon--purple">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
                </div>
                <div class="fs-contato__channel-info">
                  <strong>Senacon</strong>
                  <span>Secretaria Nacional do Consumidor</span>
                </div>
                <svg class="fs-contato__channel-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
              </a>

            </div>
          </div>

          <!-- Aviso legal -->
          <div class="fs-contato__card fs-contato__card--warn">
            <div class="fs-contato__card-header">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              <h3>Aviso importante</h3>
            </div>
            <ul class="fs-contato__warn-list">
              <li>Este portal é informativo. Não prestamos assessoria jurídica.</li>
              <li>Não solicitamos dados bancários ou senhas.</li>
              <li>Para emergências, ligue imediatamente para o seu banco.</li>
            </ul>
          </div>

          <!-- Emergências -->
          <div class="fs-contato__card fs-contato__card--emergency">
            <div class="fs-contato__card-header">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.12 1.18a2 2 0 012-2.18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 6a16 16 0 006.29 6.29l.75-.75a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7a2 2 0 011.72 2.03z"/></svg>
              <h3>Emergências</h3>
            </div>
            <p>Se está acontecendo agora:</p>
            <div class="fs-contato__emergency-list">
              <div class="fs-contato__emergency-item">
                <span class="fs-contato__emergency-label">Polícia</span>
                <a href="tel:190" class="fs-contato__emergency-number">190</a>
              </div>
              <div class="fs-contato__emergency-item">
                <span class="fs-contato__emergency-label">Procon</span>
                <a href="tel:151" class="fs-contato__emergency-number">151</a>
              </div>
            </div>
          </div>

        </aside>
      </div>
    </div>
  </div>

</main>

<?php get_footer(); ?>
