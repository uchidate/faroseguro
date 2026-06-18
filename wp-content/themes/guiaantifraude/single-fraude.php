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

  $badge_map = ['alto' => ['fs-badge--fraude-alto', 'Risco alto'], 'medio' => ['fs-badge--fraude-medio', 'Risco médio'], 'baixo' => ['fs-badge--fraude-baixo', 'Risco baixo']];
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
        <?php $vl = fs_views_label($id); if ($vl): ?>
          <span class="fs-views-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <?php echo esc_html($vl); ?>
          </span>
        <?php endif; ?>
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

          <?php if (trim((string)$como_funciona)): ?>
          <div class="fs-block fs-block--how">
            <div class="fs-block__header"><span aria-hidden="true" class="fs-block__mark"></span><h2>Como a fraude acontece</h2></div>
            <div class="fs-block__body fs-prose"><?php echo wpautop(wp_kses_post($como_funciona)); ?></div>
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

          <?php if (trim((string)$o_que_fazer)): fs_checklist_interativo($o_que_fazer, $id); endif; ?>

          <?php the_content(); ?>
          <?php if ($fonte): ?><p class="fs-fonte">Fonte: <?php echo wp_kses_post($fonte); ?></p><?php endif; ?>

          <?php fs_share_bar($id); ?>
          <?php fs_related_posts($id, 'fraude', 'tipo_fraude'); ?>
          <?php fs_newsletter_widget('inline'); ?>

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
          $tipo_ids = ($tipos && !is_wp_error($tipos)) ? wp_list_pluck($tipos, 'term_id') : [];
          $similares = get_posts(['post_type' => 'fraude', 'posts_per_page' => 4, 'post__not_in' => [$id],
            'tax_query' => $tipo_ids ? [['taxonomy' => 'tipo_fraude', 'terms' => $tipo_ids]] : []]);
          if ($similares): ?>
          <div class="fs-sidebar-widget">
            <div class="fs-sidebar-widget__head"><p class="fs-sidebar-widget__title">Fraudes Similares</p></div>
            <div class="fs-sidebar-widget__body">
              <?php foreach ($similares as $f):
                $fn = get_post_meta($f->ID, 'nivel_risco', true) ?: 'alto';
                $bm = ['alto' => 'fs-badge--fraude-alto', 'medio' => 'fs-badge--fraude-medio', 'baixo' => 'fs-badge--fraude-baixo'];
              ?>
                <div class="fs-sidebar-item">
                  <div class="fs-sidebar-item__text">
                    <div class="fs-sidebar-item__meta"><span class="fs-badge <?php echo $bm[$fn] ?? 'fs-badge--fraude-alto'; ?>" style="font-size:10px;">Risco <?php echo ucfirst($fn); ?></span></div>
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

</main>

<?php endwhile; ?>
<?php get_footer(); ?>
