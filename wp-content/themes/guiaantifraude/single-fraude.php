<?php get_header(); ?>

<?php while (have_posts()) : the_post();
  $id            = get_the_ID();
  $nivel         = get_post_meta($id, 'nivel_risco',       true) ?: 'alto';
  $prejuizo      = get_post_meta($id, 'prejuizo_estimado', true);
  $nova_tecnica  = get_post_meta($id, 'nova_tecnica',      true) === '1';
  $como_funciona = get_post_meta($id, 'como_funciona',     true);
  $sinais        = get_post_meta($id, 'sinais_alerta',     true);
  $protecao      = get_post_meta($id, 'como_se_proteger',  true);
  $o_que_fazer   = get_post_meta($id, 'o_que_fazer',       true);
  $fonte         = get_post_meta($id, 'fonte_referencia',  true);
  $tipos         = get_the_terms($id, 'tipo_fraude');
  $canais        = get_the_terms($id, 'canal_golpe');
  $publicos      = get_the_terms($id, 'publico_alvo');

  $border_map   = ['alto' => '#7c3aed', 'medio' => '#2563eb', 'baixo' => '#0891b2'];
  $hero_border  = $border_map[$nivel] ?? '#7c3aed';
  $badge_map    = ['alto' => ['fs-badge--fraude-alto', 'Risco alto'], 'medio' => ['fs-badge--fraude-medio', 'Risco médio'], 'baixo' => ['fs-badge--fraude-baixo', 'Risco baixo']];
  [$badge_cls, $badge_label] = $badge_map[$nivel] ?? $badge_map['alto'];
?>

<main class="fs-single fs-single--fraude">

  <div class="fs-single__hero fs-single__hero--fraude fs-single__hero--fraude-<?php echo esc_attr($nivel); ?>">
    <div class="container">
      <?php echo fs_single_breadcrumb(); ?>
      <div class="fs-single__meta-top">
        <span class="fs-badge <?php echo $badge_cls; ?>"><?php echo $badge_label; ?></span>
        <?php if ($nova_tecnica): ?><span class="fs-badge fs-badge--novo">Nova técnica</span><?php endif; ?>
        <?php if ($tipos && !is_wp_error($tipos)): foreach ($tipos as $t): ?>
          <a href="<?php echo get_term_link($t); ?>" class="fs-tag fs-tag--light"><?php echo esc_html($t->name); ?></a>
        <?php endforeach; endif; ?>
      </div>
      <h1 class="fs-single__title"><?php the_title(); ?></h1>
      <?php if (has_excerpt()): ?><p class="fs-single__lead"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
      <div class="fs-single__byline">
        <span>Publicado em <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('d \d\e F \d\e Y'); ?></time></span>
        <?php if ($canais && !is_wp_error($canais)): ?><span>Canal: <?php echo esc_html(implode(', ', wp_list_pluck($canais, 'name'))); ?></span><?php endif; ?>
        <?php if ($publicos && !is_wp_error($publicos)): ?><span>Público-alvo: <?php echo esc_html(implode(', ', wp_list_pluck($publicos, 'name'))); ?></span><?php endif; ?>
        <?php if ($prejuizo): ?><span>Prejuízo estimado: <strong style="color:#a78bfa;"><?php echo esc_html($prejuizo); ?></strong></span><?php endif; ?>
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
            <div class="fs-ficha__header" style="background:#1a0f3a;"><p class="fs-ficha__header-title">Ficha da Fraude</p></div>
            <div class="fs-ficha__grid">
              <div class="fs-ficha__item"><p class="fs-ficha__label">Nível de risco</p><div class="fs-ficha__value"><span class="fs-badge <?php echo $badge_cls; ?>"><?php echo $badge_label; ?></span></div></div>
              <?php if ($tipos && !is_wp_error($tipos)): ?><div class="fs-ficha__item"><p class="fs-ficha__label">Tipo</p><p class="fs-ficha__value"><?php echo esc_html($tipos[0]->name); ?></p></div><?php endif; ?>
              <?php if ($canais && !is_wp_error($canais)): ?><div class="fs-ficha__item"><p class="fs-ficha__label">Canal</p><p class="fs-ficha__value"><?php echo esc_html(implode(' / ', wp_list_pluck($canais, 'name'))); ?></p></div><?php endif; ?>
              <?php if ($publicos && !is_wp_error($publicos)): ?><div class="fs-ficha__item"><p class="fs-ficha__label">Público-alvo</p><p class="fs-ficha__value"><?php echo esc_html(implode(', ', wp_list_pluck($publicos, 'name'))); ?></p></div><?php endif; ?>
              <?php if ($prejuizo): ?><div class="fs-ficha__item"><p class="fs-ficha__label">Prejuízo estimado</p><p class="fs-ficha__value" style="color:#7c3aed;"><?php echo esc_html($prejuizo); ?></p></div><?php endif; ?>
            </div>
          </div>

          <?php $sections = [
            'como_funciona'    => ['fs-block--how', 'Como a fraude acontece'],
            'sinais_alerta'    => ['fs-block--warn', 'Sinais de alerta', 'warn'],
            'como_se_proteger' => ['fs-block--safe', 'Como se proteger', 'safe'],
            'o_que_fazer'      => ['fs-block--action', 'Fui vítima — o que fazer'],
          ];
          foreach ($sections as $key => $section):
            $cls = $section[0];
            $label = $section[1];
            $checklist_type = $section[2] ?? 'safe';
            $val = get_post_meta($id, $key, true);
            if (!trim((string)$val)) continue;
          ?>
          <div class="fs-block <?php echo $cls; ?>">
            <div class="fs-block__header"><span aria-hidden="true" class="fs-block__mark"></span><h2><?php echo esc_html($label); ?></h2></div>
            <div class="fs-block__body">
              <?php if ($key === 'como_funciona'): ?>
                <div class="fs-prose"><?php echo wpautop(wp_kses_post($val)); ?></div>
              <?php else: ?>
                <ul class="fs-checklist fs-checklist--<?php echo $checklist_type ?? 'safe'; ?>">
                  <?php foreach (array_filter(array_map('trim', explode("\n", $val))) as $item): ?>
                    <li><?php echo esc_html($item); ?></li>
                  <?php endforeach; ?>
                </ul>
                <?php if ($key === 'o_que_fazer'): ?>
                <div class="fs-emergency">
                  <a href="https://www.bcb.gov.br/meubc/registrar_reclamacao" target="_blank" rel="noopener" class="fs-emergency__link">Registrar no Bacen</a>
                  <a href="https://www.consumidor.gov.br" target="_blank" rel="noopener" class="fs-emergency__link">Consumidor.gov</a>
                  <a href="/contato/" class="fs-emergency__link">Denunciar ao portal</a>
                </div>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>

          <?php the_content(); ?>
          <?php if ($fonte): ?><p class="fs-fonte">Fonte: <?php echo wp_kses_post($fonte); ?></p><?php endif; ?>

          <div class="fs-share" style="margin-top:32px;">
            <span class="fs-share__label">Compartilhar</span>
            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' — ' . get_permalink()); ?>" target="_blank" rel="noopener" class="fs-share__btn fs-share__btn--wa">WhatsApp</a>
            <button class="fs-share__btn fs-share__btn--copy" data-share-copy="<?php echo esc_attr(get_permalink()); ?>">Copiar link</button>
          </div>

          <nav class="fs-post-nav">
            <div class="fs-post-nav__item">
              <?php $prev = get_previous_post(); if ($prev): ?>
                <span class="fs-post-nav__label">Anterior</span>
                <div class="fs-post-nav__title"><a href="<?php echo get_permalink($prev); ?>"><?php echo esc_html(fs_editorial_text($prev->post_title)); ?></a></div>
              <?php endif; ?>
            </div>
            <div class="fs-post-nav__item fs-post-nav__item--next">
              <?php $next = get_next_post(); if ($next): ?>
                <span class="fs-post-nav__label">Próxima</span>
                <div class="fs-post-nav__title"><a href="<?php echo get_permalink($next); ?>"><?php echo esc_html(fs_editorial_text($next->post_title)); ?></a></div>
              <?php endif; ?>
            </div>
          </nav>

        </div>

        <aside class="fs-sidebar">
          <div class="fs-sidebar-widget" style="background:#1a0f3a;border-color:#2d1f5a;padding:20px;">
            <h3 style="color:#fff;font-size:var(--text-base);font-weight:700;margin-bottom:10px;">Diferença importante</h3>
            <p style="color:rgba(255,255,255,.6);font-size:var(--text-sm);line-height:1.65;">Na <strong style="color:#a78bfa;">fraude</strong>, o criminoso age por conta própria — você não precisa fazer nada para ser vítima. Verifique seu extrato regularmente.</p>
            <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-btn" style="margin-top:14px;width:100%;justify-content:center;background:#7c3aed;color:#fff;">Ver outras fraudes</a>
          </div>

          <?php
          $similares = get_posts(['post_type' => 'fraude', 'posts_per_page' => 4, 'post__not_in' => [$id]]);
          if ($similares): ?>
          <div class="fs-sidebar-widget">
            <div class="fs-sidebar-widget__head"><p class="fs-sidebar-widget__title">Fraudes Similares</p></div>
            <div class="fs-sidebar-widget__body">
              <?php foreach ($similares as $f):
                $fn = get_post_meta($f->ID, 'nivel_risco', true) ?: 'alto';
                $badge_map_s = ['alto' => 'fs-badge--fraude-alto', 'medio' => 'fs-badge--fraude-medio', 'baixo' => 'fs-badge--fraude-baixo'];
              ?>
                <div class="fs-sidebar-item">
                  <div class="fs-sidebar-item__text">
                    <div class="fs-sidebar-item__meta"><span class="fs-badge <?php echo $badge_map_s[$fn] ?? 'fs-badge--fraude-alto'; ?>" style="font-size:10px;">Risco <?php echo ucfirst($fn); ?></span></div>
                    <div class="fs-sidebar-item__title"><a href="<?php echo get_permalink($f); ?>"><?php echo esc_html(fs_editorial_text($f->post_title)); ?></a></div>
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
  $mais = get_posts(['post_type' => 'fraude', 'posts_per_page' => 3, 'post__not_in' => [$id]]);
  if ($mais): ?>
  <section class="fs-related" style="background:#0f0a2a;border-top:1px solid rgba(124,58,237,.2);">
    <div class="container">
      <h2 class="fs-related__title" style="color:#fff;">Outras Fraudes</h2>
      <div class="fs-grid fs-grid--3">
        <?php foreach ($mais as $f): fs_fraude_card($f, true); endforeach; wp_reset_postdata(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php endwhile; ?>
<?php get_footer(); ?>
