<footer class="fs-footer">
  <div class="fs-footer__main">
    <div class="container fs-footer__grid">

      <div class="fs-footer__col fs-footer__col--brand">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="fs-footer__logo">
          <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <path d="M16 2L4 8v8c0 6.63 5.12 12.84 12 14.4C22.88 28.84 28 22.63 28 16V8L16 2z" fill="#f97316"/>
            <path d="M16 6.5L8 10.5V16c0 4.17 3.22 8.07 8 9.1 4.78-1.03 8-4.93 8-9.1v-5.5L16 6.5z" fill="rgba(255,255,255,0.1)"/>
            <circle cx="16" cy="16" r="3.5" fill="#f97316"/>
            <path d="M16 12.5v7M12.5 16h7" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <span>Faro Seguro</span>
        </a>
        <p class="fs-footer__about">Portal de alertas e educação sobre fraudes e golpes financeiros no Brasil. Conteúdo verificado, publicado em até 24h após identificação de novos modus operandi.</p>
        <div class="fs-footer__badges">
          <span class="fs-footer__badge">SSL Seguro</span>
          <span class="fs-footer__badge">Baseado no BCB</span>
          <span class="fs-footer__badge">Conteúdo verificado</span>
        </div>
      </div>

      <div class="fs-footer__col">
        <h3 class="fs-footer__col-title">Alertas</h3>
        <?php
        $tipos = get_terms(['taxonomy' => 'tipo_golpe', 'hide_empty' => true, 'number' => 6]);
        if ($tipos && !is_wp_error($tipos)): ?>
        <ul class="fs-footer__links">
          <li><a href="<?php echo get_post_type_archive_link('golpe'); ?>">Todos os alertas</a></li>
          <?php foreach ($tipos as $t): ?>
            <li><a href="<?php echo get_term_link($t); ?>"><?php echo esc_html($t->name); ?></a></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <div class="fs-footer__col">
        <h3 class="fs-footer__col-title">Artigos</h3>
        <?php
        $cats = get_categories(['hide_empty' => true, 'number' => 6]);
        if ($cats): ?>
        <ul class="fs-footer__links">
          <li><a href="<?php echo home_url('/artigos/'); ?>">Todos os artigos</a></li>
          <?php foreach ($cats as $c): ?>
            <li><a href="<?php echo get_category_link($c); ?>"><?php echo esc_html($c->name); ?></a></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <div class="fs-footer__col">
        <h3 class="fs-footer__col-title">Recursos</h3>
        <ul class="fs-footer__links">
          <li><a href="<?php echo home_url('/glossario/'); ?>">Glossário de Termos</a></li>
          <li><a href="<?php echo home_url('/sobre-nos/'); ?>">Sobre o Portal</a></li>
          <li><a href="<?php echo home_url('/contato/'); ?>">Denunciar um Golpe</a></li>
          <li><a href="https://www.bcb.gov.br/meubc/registrarreclamacao" target="_blank" rel="noopener">Registrar no Bacen ↗</a></li>
          <li><a href="https://www.consumidor.gov.br" target="_blank" rel="noopener">Consumidor.gov.br ↗</a></li>
          <li><a href="<?php echo home_url('/feed/'); ?>">Feed RSS</a></li>
        </ul>
      </div>

    </div>
  </div>

  <div class="fs-footer__bottom">
    <div class="container fs-footer__bottom-inner">
      <p>© <?php echo date('Y'); ?> Faro Seguro. Todos os direitos reservados.</p>
      <p>
        Conteúdo de caráter informativo. Não nos responsabilizamos por decisões financeiras individuais. Fontes: Banco Central do Brasil, Febraban, Senacon e Ministério da Justiça.
        &nbsp;·&nbsp; <a href="<?php echo home_url('/politica-de-privacidade/'); ?>">Política de Privacidade</a>
        &nbsp;·&nbsp; <a href="<?php echo home_url('/termos-de-uso/'); ?>">Termos de Uso</a>
        &nbsp;·&nbsp; <a href="<?php echo home_url('/sobre-nos/'); ?>">Sobre o Portal</a>
      </p>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
