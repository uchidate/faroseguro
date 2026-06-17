<?php get_header(); ?>

<?php while (have_posts()) : the_post();
  $id          = get_the_ID();
  $nivel       = get_post_meta($id, 'nivel_risco',       true) ?: 'alto';
  $prejuizo    = get_post_meta($id, 'prejuizo_estimado', true);
  $novo_modus  = get_post_meta($id, 'novo_modus',        true) === '1';
  $como_age    = get_post_meta($id, 'como_age',          true);
  $sinais      = get_post_meta($id, 'sinais_alerta',     true);
  $protecao    = get_post_meta($id, 'como_se_proteger',  true);
  $o_que_fazer = get_post_meta($id, 'o_que_fazer',       true);
  $fonte       = get_post_meta($id, 'fonte_referencia',  true);
  $tipos       = get_the_terms($id, 'tipo_golpe');
  $canais      = get_the_terms($id, 'canal_golpe');
  $publicos    = get_the_terms($id, 'publico_alvo');

  $border_colors = ['alto' => '#dc2626', 'medio' => '#d97706', 'baixo' => '#2563eb'];
  $hero_border   = $border_colors[$nivel] ?? '#dc2626';
  $nivel_labels  = ['alto' => 'Risco alto', 'medio' => 'Risco médio', 'baixo' => 'Risco baixo'];
  $nivel_label   = $nivel_labels[$nivel] ?? 'Risco alto';
  $nivel_classes = ['alto' => '--red', 'medio' => '--yellow', 'baixo' => '--blue'];
  $nivel_class   = $nivel_classes[$nivel] ?? '--red';
?>

<main class="fs-single fs-single--golpe">

  <div class="fs-single__hero" style="border-bottom: 4px solid <?php echo esc_attr($hero_border); ?>;">
    <div class="container">
      <div class="fs-single__meta-top">
        <span class="fs-badge fs-badge<?php echo $nivel_class; ?>"><?php echo $nivel_label; ?></span>
        <?php if ($novo_modus): ?><span class="fs-badge fs-badge--novo">Novo modus operandi</span><?php endif; ?>
        <?php if ($tipos && !is_wp_error($tipos)): foreach ($tipos as $t): ?>
          <a href="<?php echo get_term_link($t); ?>" class="fs-tag fs-tag--light"><?php echo esc_html($t->name); ?></a>
        <?php endforeach; endif; ?>
      </div>
      <h1 class="fs-single__title"><?php the_title(); ?></h1>
      <?php if (has_excerpt()): ?><p class="fs-single__lead"><?php the_excerpt(); ?></p><?php endif; ?>
      <div class="fs-single__byline">
        <span>Publicado em <time datetime="<?php the_date('c'); ?>"><?php the_date('d \d\e F \d\e Y'); ?></time></span>
        <?php if ($canais && !is_wp_error($canais)): ?><span>Canal: <?php echo esc_html(implode(', ', wp_list_pluck($canais, 'name'))); ?></span><?php endif; ?>
        <?php if ($publicos && !is_wp_error($publicos)): ?><span>Público-alvo: <?php echo esc_html(implode(', ', wp_list_pluck($publicos, 'name'))); ?></span><?php endif; ?>
        <?php if ($prejuizo): ?><span>Prejuízo estimado: <strong style="color:#fb923c;"><?php echo esc_html($prejuizo); ?></strong></span><?php endif; ?>
      </div>
    </div>
  </div>

  <?php if (has_post_thumbnail()): ?>
  <div class="fs-single__cover"><div class="container--narrow">
    <?php the_post_thumbnail('full', ['class' => 'fs-single__cover-img', 'loading' => 'eager']); ?>
  </div></div>
  <?php endif; ?>

  <div class="fs-single__body">
    <div class="container">
      <div class="fs-grid--aside">

        <div>

          <!-- Ficha técnica -->
          <div class="fs-ficha">
            <div class="fs-ficha__header"><p class="fs-ficha__header-title">Ficha do Golpe</p></div>
            <div class="fs-ficha__grid">
              <div class="fs-ficha__item"><p class="fs-ficha__label">Nível de risco</p><div class="fs-ficha__value"><?php echo fs_badge_risco($nivel); ?></div></div>
              <?php if ($tipos && !is_wp_error($tipos)): ?><div class="fs-ficha__item"><p class="fs-ficha__label">Categoria</p><p class="fs-ficha__value"><?php echo esc_html($tipos[0]->name); ?></p></div><?php endif; ?>
              <?php if ($canais && !is_wp_error($canais)): ?><div class="fs-ficha__item"><p class="fs-ficha__label">Canal</p><p class="fs-ficha__value"><?php echo esc_html(implode(' / ', wp_list_pluck($canais, 'name'))); ?></p></div><?php endif; ?>
              <?php if ($publicos && !is_wp_error($publicos)): ?><div class="fs-ficha__item"><p class="fs-ficha__label">Público-alvo</p><p class="fs-ficha__value"><?php echo esc_html(implode(', ', wp_list_pluck($publicos, 'name'))); ?></p></div><?php endif; ?>
              <?php if ($prejuizo): ?><div class="fs-ficha__item"><p class="fs-ficha__label">Prejuízo estimado</p><p class="fs-ficha__value" style="color:var(--red);"><?php echo esc_html($prejuizo); ?></p></div><?php endif; ?>
            </div>
          </div>

          <?php if (trim((string)$como_age)): ?>
          <div class="fs-block fs-block--how">
            <div class="fs-block__header"><span aria-hidden="true" class="fs-block__mark"></span><h2>Como o golpe funciona</h2></div>
            <div class="fs-block__body fs-prose"><?php echo wpautop(wp_kses_post($como_age)); ?></div>
          </div>
          <?php endif; ?>

          <?php if (trim((string)$sinais)): ?>
          <div class="fs-block fs-block--warn">
            <div class="fs-block__header"><span aria-hidden="true" class="fs-block__mark"></span><h2>Sinais de alerta</h2></div>
            <div class="fs-block__body">
              <ul class="fs-checklist fs-checklist--warn">
                <?php foreach (array_filter(array_map('trim', explode("\n", $sinais))) as $s): ?>
                  <li><?php echo esc_html($s); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php endif; ?>

          <?php if (trim((string)$protecao)): ?>
          <div class="fs-block fs-block--safe">
            <div class="fs-block__header"><span aria-hidden="true" class="fs-block__mark"></span><h2>Como se proteger</h2></div>
            <div class="fs-block__body">
              <ul class="fs-checklist fs-checklist--safe">
                <?php foreach (array_filter(array_map('trim', explode("\n", $protecao))) as $p): ?>
                  <li><?php echo esc_html($p); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php endif; ?>

          <?php if (trim((string)$o_que_fazer)): ?>
          <div class="fs-block fs-block--action">
            <div class="fs-block__header"><span aria-hidden="true" class="fs-block__mark"></span><h2>Fui vítima — o que fazer agora</h2></div>
            <div class="fs-block__body">
              <ul class="fs-checklist fs-checklist--safe">
                <?php foreach (array_filter(array_map('trim', explode("\n", $o_que_fazer))) as $a): ?>
                  <li><?php echo esc_html($a); ?></li>
                <?php endforeach; ?>
              </ul>
              <div class="fs-emergency">
                <a href="https://www.bcb.gov.br/meubc/registrar_reclamacao" target="_blank" rel="noopener" class="fs-emergency__link">Registrar no Bacen</a>
                <a href="https://www.consumidor.gov.br" target="_blank" rel="noopener" class="fs-emergency__link">Consumidor.gov</a>
                <a href="/contato/" class="fs-emergency__link">Denunciar ao portal</a>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php the_content(); ?>

          <?php if ($fonte): ?><p class="fs-fonte">Fonte: <?php echo wp_kses_post($fonte); ?></p><?php endif; ?>

          <div class="fs-share" style="margin-top:32px;">
            <span class="fs-share__label">Compartilhar alerta</span>
            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' — ' . get_permalink()); ?>" target="_blank" rel="noopener" class="fs-share__btn fs-share__btn--wa">WhatsApp</a>
            <button class="fs-share__btn fs-share__btn--copy" data-share-copy="<?php echo esc_attr(get_permalink()); ?>">Copiar link</button>
          </div>

          <nav class="fs-post-nav">
            <div class="fs-post-nav__item">
              <?php $prev = get_previous_post(); if ($prev): ?>
                <span class="fs-post-nav__label">Alerta anterior</span>
                <div class="fs-post-nav__title"><a href="<?php echo get_permalink($prev); ?>"><?php echo esc_html(fs_editorial_text($prev->post_title)); ?></a></div>
              <?php endif; ?>
            </div>
            <div class="fs-post-nav__item fs-post-nav__item--next">
              <?php $next = get_next_post(); if ($next): ?>
                <span class="fs-post-nav__label">Próximo alerta</span>
                <div class="fs-post-nav__title"><a href="<?php echo get_permalink($next); ?>"><?php echo esc_html(fs_editorial_text($next->post_title)); ?></a></div>
              <?php endif; ?>
            </div>
          </nav>

        </div>

        <!-- Sidebar -->
        <aside class="fs-sidebar">

          <div class="fs-sidebar-widget fs-sidebar-widget--cta">
            <h3>Proteja-se agora</h3>
            <p>Conheceu este golpe? Denuncie para proteger outras pessoas.</p>
            <a href="/contato/" class="fs-btn fs-btn--primary">Denunciar ao Portal</a>
            <a href="https://www.bcb.gov.br/meubc/registrar_reclamacao" target="_blank" rel="noopener" class="fs-btn fs-btn--outline-w" style="margin-top:8px;">Registrar no Bacen</a>
          </div>

          <div class="fs-sidebar-widget fs-sidebar-widget--official">
            <div class="fs-sidebar-widget__head"><p class="fs-sidebar-widget__title">Resolver nos canais oficiais</p></div>
            <div class="fs-sidebar-widget__body">
              <?php fs_official_channels('fs-official-channels--compact'); ?>
            </div>
          </div>

          <?php
          $tipo_ids = ($tipos && !is_wp_error($tipos)) ? wp_list_pluck($tipos, 'term_id') : [];
          $similares = get_posts([
            'post_type' => 'golpe', 'posts_per_page' => 4, 'post__not_in' => [$id],
            'tax_query' => $tipo_ids ? [['taxonomy' => 'tipo_golpe', 'terms' => $tipo_ids]] : [],
          ]);
          if ($similares): ?>
          <div class="fs-sidebar-widget">
            <div class="fs-sidebar-widget__head"><p class="fs-sidebar-widget__title">Golpes Similares</p></div>
            <div class="fs-sidebar-widget__body">
              <?php foreach ($similares as $g):
                $gnivel = get_post_meta($g->ID, 'nivel_risco', true) ?: 'alto'; ?>
                <div class="fs-sidebar-item">
                  <div class="fs-sidebar-item__text">
                    <div class="fs-sidebar-item__meta"><?php echo fs_badge_risco($gnivel); ?></div>
                    <div class="fs-sidebar-item__title"><a href="<?php echo get_permalink($g); ?>"><?php echo esc_html(fs_editorial_text($g->post_title)); ?></a></div>
                  </div>
                </div>
              <?php endforeach; wp_reset_postdata(); ?>
            </div>
          </div>
          <?php endif; ?>

          <?php $artigos = get_posts(['post_type' => 'post', 'numberposts' => 3]); if ($artigos): ?>
          <div class="fs-sidebar-widget">
            <div class="fs-sidebar-widget__head"><p class="fs-sidebar-widget__title">Leia Também</p></div>
            <div class="fs-sidebar-widget__body">
              <?php foreach ($artigos as $art): ?>
                <div class="fs-sidebar-item">
                  <?php if (has_post_thumbnail($art->ID)): ?><a href="<?php echo get_permalink($art); ?>"><?php echo get_the_post_thumbnail($art->ID, [64, 48]); ?></a><?php endif; ?>
                  <div class="fs-sidebar-item__text">
                    <div class="fs-sidebar-item__title"><a href="<?php echo get_permalink($art); ?>"><?php echo esc_html(fs_editorial_text($art->post_title)); ?></a></div>
                    <div class="fs-sidebar-item__meta"><?php echo get_the_date('d/m/Y', $art->ID); ?></div>
                  </div>
                </div>
              <?php endforeach; wp_reset_postdata(); ?>
            </div>
          </div>
          <?php endif; ?>

          <?php fs_ad('sidebar'); ?>
          <?php dynamic_sidebar('sidebar-golpes'); ?>

        </aside>

      </div>
    </div>
  </div>

  <?php
  $mais = get_posts(['post_type' => 'golpe', 'posts_per_page' => 3, 'post__not_in' => [$id], 'orderby' => 'date', 'order' => 'DESC']);
  if ($mais): ?>
  <section class="fs-related">
    <div class="container">
      <h2 class="fs-related__title">Outros Alertas Recentes</h2>
      <div class="fs-grid fs-grid--3">
        <?php foreach ($mais as $g): fs_golpe_card($g, true); endforeach; wp_reset_postdata(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php endwhile; ?>
<?php get_footer(); ?>
