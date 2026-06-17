<footer class="fs-footer">
  <div class="fs-footer__inner">

    <div class="fs-footer__brand">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="fs-footer__logo">
        <svg width="24" height="24" viewBox="0 0 28 28" fill="none" aria-hidden="true">
          <path d="M14 2L4 7v7c0 5.55 4.27 10.74 10 12 5.73-1.26 10-6.45 10-12V7L14 2z" fill="#f97316"/>
          <path d="M14 6L8 9.5v5c0 3.47 2.67 6.71 6 7.5 3.33-.79 6-4.03 6-7.5v-5L14 6z" fill="rgba(255,255,255,0.15)"/>
          <circle cx="14" cy="14.5" r="3" fill="#fff"/>
        </svg>
        <?php bloginfo('name'); ?>
      </a>
      <p class="fs-footer__tagline"><?php bloginfo('description'); ?></p>
    </div>

    <nav class="fs-footer__nav" aria-label="Links do rodapé">
      <span class="fs-footer__nav-title">Navegação</span>
      <?php
      wp_nav_menu([
        'theme_location' => 'footer',
        'container'      => false,
        'menu_class'     => 'fs-footer__nav-list',
        'fallback_cb'    => function () {
          echo '<ul class="fs-footer__nav-list">
            <li><a href="/">Home</a></li>
            <li><a href="/golpes/">Alertas</a></li>
            <li><a href="/sobre-nos/">Sobre</a></li>
            <li><a href="/contato/">Contato</a></li>
          </ul>';
        },
      ]);
      ?>
    </nav>

    <div class="fs-footer__contact">
      <span class="fs-footer__nav-title">Denuncie um golpe</span>
      <p>Identificou um novo modus operandi? Envie para nossa equipe.</p>
      <a href="/contato/" class="fs-footer__contact-link">Enviar relato →</a>
    </div>

  </div>

  <div class="fs-footer__bottom">
    <p>© <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Conteúdo informativo — não nos responsabilizamos por decisões financeiras.</p>
    <p>Dados baseados em fontes públicas: Banco Central do Brasil, Febraban e Senacon.</p>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
