<?php get_header(); ?>

<?php while (have_posts()) : the_post();
  $nivel      = get_post_meta(get_the_ID(), 'nivel_risco', true) ?: 'alto';
  $novo       = get_post_meta(get_the_ID(), 'novo_modus', true) === '1';
  $prejuizo   = get_post_meta(get_the_ID(), 'prejuizo_estimado', true);
  $fonte      = get_post_meta(get_the_ID(), 'fonte_referencia', true);
  $como_age   = get_post_meta(get_the_ID(), 'como_age', true);
  $sinais     = get_post_meta(get_the_ID(), 'sinais_alerta', true);
  $protecao   = get_post_meta(get_the_ID(), 'como_se_proteger', true);
  $o_que_fazer = get_post_meta(get_the_ID(), 'o_que_fazer', true);
  $border_color = ['alto' => '#ef4444', 'medio' => '#f59e0b', 'baixo' => '#3b82f6'][$nivel] ?? '#ef4444';
  $tipos  = get_the_terms(get_the_ID(), 'tipo_golpe');
  $canais = get_the_terms(get_the_ID(), 'canal_golpe');
  $publ   = get_the_terms(get_the_ID(), 'publico_alvo');
?>
<main class="fs-single fs-single--golpe">

  <div class="fs-single__hero fs-single__hero--dark" style="border-bottom:4px solid <?php echo $border_color; ?>">
    <div class="container fs-single__hero-inner">
      <div class="fs-single__meta-top">
        <?php echo fs_badge_risco($nivel); ?>
        <?php if ($novo) echo '<span class="fs-badge fs-badge--novo">✦ Novo Modus Operandi</span>'; ?>
        <?php if ($tipos && !is_wp_error($tipos)) foreach ($tipos as $t) echo '<a href="' . get_term_link($t) . '" class="fs-tag fs-tag--light">' . esc_html($t->name) . '</a>'; ?>
      </div>
      <h1 class="fs-single__title"><?php the_title(); ?></h1>
      <?php if (has_excerpt()) echo '<p class="fs-single__lead">' . get_the_excerpt() . '</p>'; ?>
      <div class="fs-single__byline">
        <span>📅 <time datetime="<?php the_date('c'); ?>"><?php the_date('d \d\e F \d\e Y'); ?></time></span>
        <?php if ($prejuizo) echo '<span>💸 Prejuízo médio: <strong>' . esc_html($prejuizo) . '</strong></span>'; ?>
        <?php if ($canais && !is_wp_error($canais)) echo '<span>📡 Via: ' . implode(', ', wp_list_pluck($canais, 'name')) . '</span>'; ?>
      </div>
    </div>
  </div>

  <?php if (has_post_thumbnail()): ?>
  <div class="fs-single__cover"><div class="container">
    <?php the_post_thumbnail('fs-hero', ['class' => 'fs-single__cover-img', 'loading' => 'eager']); ?>
  </div></div>
  <?php endif; ?>

  <div class="fs-single__body container">
    <div class="fs-single__content">

      <!-- Ficha técnica -->
      <div class="fs-ficha">
        <h2 class="fs-ficha__title">Ficha do Golpe</h2>
        <div class="fs-ficha__grid">
          <div class="fs-ficha__item"><span class="fs-ficha__label">Nível de risco</span><?php echo fs_badge_risco($nivel); ?></div>
          <?php if ($prejuizo) echo '<div class="fs-ficha__item"><span class="fs-ficha__label">Prejuízo estimado</span><strong>' . esc_html($prejuizo) . '</strong></div>'; ?>
          <?php if ($tipos && !is_wp_error($tipos)) echo '<div class="fs-ficha__item"><span class="fs-ficha__label">Tipo</span><span>' . implode(', ', wp_list_pluck($tipos, 'name')) . '</span></div>'; ?>
          <?php if ($canais && !is_wp_error($canais)) echo '<div class="fs-ficha__item"><span class="fs-ficha__label">Canal</span><span>' . implode(', ', wp_list_pluck($canais, 'name')) . '</span></div>'; ?>
          <?php if ($publ && !is_wp_error($publ)) echo '<div class="fs-ficha__item"><span class="fs-ficha__label">Público-alvo</span><span>' . implode(', ', wp_list_pluck($publ, 'name')) . '</span></div>'; ?>
        </div>
      </div>

      <?php if ($como_age): ?>
      <div class="fs-section-block">
        <h2 class="fs-section-block__title">🔍 Como o golpe funciona</h2>
        <div class="fs-prose"><?php echo wpautop(esc_html($como_age)); ?></div>
      </div>
      <?php endif; ?>

      <?php if ($sinais): ?>
      <div class="fs-section-block fs-section-block--warning">
        <h2 class="fs-section-block__title">🚩 Sinais de alerta</h2>
        <ul class="fs-checklist fs-checklist--danger">
          <?php foreach (array_filter(array_map('trim', explode("\n", $sinais))) as $item) echo '<li>' . esc_html($item) . '</li>'; ?>
        </ul>
      </div>
      <?php endif; ?>

      <?php if ($protecao): ?>
      <div class="fs-section-block fs-section-block--safe">
        <h2 class="fs-section-block__title">✅ Como se proteger</h2>
        <ul class="fs-checklist fs-checklist--safe">
          <?php foreach (array_filter(array_map('trim', explode("\n", $protecao))) as $item) echo '<li>' . esc_html($item) . '</li>'; ?>
        </ul>
      </div>
      <?php endif; ?>

      <?php if (get_the_content()): ?>
      <div class="fs-prose fs-section-block"><?php the_content(); ?></div>
      <?php endif; ?>

      <?php if ($o_que_fazer): ?>
      <div class="fs-section-block fs-section-block--action">
        <h2 class="fs-section-block__title">⚡ Fui vítima — e agora?</h2>
        <div class="fs-prose"><?php echo wpautop(esc_html($o_que_fazer)); ?></div>
        <div class="fs-emergency-links">
          <a href="https://www.bcb.gov.br/meubc/registrarreclamacao" target="_blank" rel="noopener" class="fs-emergency-link">🏦 Banco Central</a>
          <a href="https://www.consumidor.gov.br" target="_blank" rel="noopener" class="fs-emergency-link">📋 Consumidor.gov.br</a>
          <a href="https://new.safernet.org.br/denuncie" target="_blank" rel="noopener" class="fs-emergency-link">🛡️ SaferNet</a>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($fonte) echo '<p class="fs-fonte">📎 Fonte: <a href="' . esc_url($fonte) . '" target="_blank" rel="noopener nofollow">' . esc_html(parse_url($fonte, PHP_URL_HOST)) . '</a></p>'; ?>

      <nav class="fs-single__nav-posts">
        <div class="fs-single__nav-prev"><?php previous_post_link('<span class="fs-single__nav-label">← Anterior</span><strong class="fs-single__nav-title">%link</strong>', '%title', true, '', 'golpe'); ?></div>
        <div class="fs-single__nav-next"><?php next_post_link('<span class="fs-single__nav-label">Próximo →</span><strong class="fs-single__nav-title">%link</strong>', '%title', true, '', 'golpe'); ?></div>
      </nav>
    </div>

    <aside class="fs-single__sidebar">
      <div class="fs-sidebar-widget fs-sidebar-widget--cta">
        <h3 class="fs-sidebar-widget__title">💬 Conhece outro golpe?</h3>
        <p>Contribua enviando um relato para nossa equipe.</p>
        <a href="/contato/" class="fs-btn fs-btn--primary" style="width:100%;justify-content:center;margin-top:12px">Enviar relato</a>
      </div>

      <?php
      if ($tipos && !is_wp_error($tipos)):
        $rel = get_posts(['post_type' => 'golpe', 'numberposts' => 4, 'post__not_in' => [get_the_ID()],
          'tax_query' => [['taxonomy' => 'tipo_golpe', 'field' => 'term_id', 'terms' => wp_list_pluck($tipos, 'term_id')]]]);
        if ($rel): ?>
        <div class="fs-sidebar-widget">
          <h3 class="fs-sidebar-widget__title">🔗 Golpes relacionados</h3>
          <?php foreach ($rel as $g): $n = get_post_meta($g->ID, 'nivel_risco', true) ?: 'alto'; ?>
            <a href="<?php echo get_permalink($g); ?>" class="fs-sidebar-item">
              <?php echo fs_badge_risco($n); ?>
              <span><?php echo esc_html($g->post_title); ?></span>
            </a>
          <?php endforeach; wp_reset_postdata(); ?>
        </div>
      <?php endif; endif; ?>

      <div class="fs-sidebar-widget">
        <h3 class="fs-sidebar-widget__title">📂 Tipos de Golpe</h3>
        <?php $all_tipos = get_terms(['taxonomy' => 'tipo_golpe', 'hide_empty' => true]);
        foreach ($all_tipos as $t): ?>
          <a href="<?php echo get_term_link($t); ?>" class="fs-sidebar-item fs-sidebar-item--plain">
            <span><?php echo esc_html($t->name); ?></span>
            <span class="fs-sidebar-item__count"><?php echo $t->count; ?></span>
          </a>
        <?php endforeach; ?>
      </div>

      <?php fs_ad('sidebar'); ?>
      <?php dynamic_sidebar('sidebar-golpes'); ?>
    </aside>
  </div>
</main>
<?php endwhile; ?>
<?php get_footer(); ?>
