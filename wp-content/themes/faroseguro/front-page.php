<?php get_header(); ?>

<?php
/* ── Dados homepage ──────────────────────── */
$golpe_hero_q = get_posts(['post_type' => 'golpe', 'numberposts' => 1,
  'meta_query' => [['key' => 'nivel_risco', 'value' => 'alto']]]);
$golpe_hero   = $golpe_hero_q ? $golpe_hero_q[0] : get_posts(['post_type' => 'golpe', 'numberposts' => 1])[0] ?? null;
$hero_id      = $golpe_hero ? $golpe_hero->ID : 0;

$golpes_grid  = get_posts(['post_type' => 'golpe',  'numberposts' => 4, 'post__not_in' => [$hero_id]]);
$fraudes_grid = get_posts(['post_type' => 'fraude', 'numberposts' => 4]);

$destaque_q   = get_posts(['post_type' => 'post', 'numberposts' => 1,
  'meta_query' => [['key' => 'artigo_destaque', 'value' => '1']]]);
$artigo_hero  = $destaque_q ? $destaque_q[0] : (get_posts(['post_type' => 'post', 'numberposts' => 1])[0] ?? null);
$artigos_grid = get_posts(['post_type' => 'post', 'numberposts' => 6,
  'post__not_in' => $artigo_hero ? [$artigo_hero->ID] : []]);

$tipos_golpe  = get_terms(['taxonomy' => 'tipo_golpe',  'hide_empty' => true, 'number' => 5]);
$tipos_fraude = get_terms(['taxonomy' => 'tipo_fraude', 'hide_empty' => true, 'number' => 5]);

$total_golpes  = wp_count_posts('golpe')->publish;
$total_fraudes = wp_count_posts('fraude')->publish;
$total_posts   = wp_count_posts('post')->publish;
?>

<main>

  <!-- ══════════════════════════════════════
       HERO — Golpe de maior risco
  ════════════════════════════════════════ -->
  <?php if ($golpe_hero):
    $nivel    = get_post_meta($golpe_hero->ID, 'nivel_risco', true) ?: 'alto';
    $prejuizo = get_post_meta($golpe_hero->ID, 'prejuizo_estimado', true);
    $tipos_h  = get_the_terms($golpe_hero->ID, 'tipo_golpe');
    $canais_h = get_the_terms($golpe_hero->ID, 'canal_golpe');
    $border   = ['alto' => '#dc2626', 'medio' => '#d97706', 'baixo' => '#2563eb'][$nivel] ?? '#dc2626';
  ?>
  <section class="fs-home-hero" style="border-bottom:4px solid <?php echo esc_attr($border); ?>">
    <div class="container fs-home-hero__inner">
      <div class="fs-home-hero__content">
        <div class="fs-home-hero__eyebrow">
          <span class="fs-home-hero__live">● GOLPE EM CIRCULAÇÃO</span>
        </div>
        <?php if ($tipos_h && !is_wp_error($tipos_h)): foreach (array_slice($tipos_h, 0, 2) as $t): ?>
          <a href="<?php echo get_term_link($t); ?>" class="fs-tag fs-tag--light" style="margin-right:4px;"><?php echo esc_html($t->name); ?></a>
        <?php endforeach; endif; ?>
        <h1 class="fs-home-hero__title"><a href="<?php echo get_permalink($golpe_hero); ?>"><?php echo esc_html($golpe_hero->post_title); ?></a></h1>
        <p class="fs-home-hero__desc"><?php echo wp_trim_words(get_the_excerpt($golpe_hero), 28); ?></p>
        <div class="fs-home-hero__meta">
          <?php echo fs_badge_risco($nivel); ?>
          <?php if ($canais_h && !is_wp_error($canais_h)): ?>
            <span class="fs-home-hero__sep">|</span>
            <span>📡 <?php echo esc_html(implode(', ', wp_list_pluck($canais_h, 'name'))); ?></span>
          <?php endif; ?>
          <?php if ($prejuizo): ?>
            <span class="fs-home-hero__sep">|</span>
            <span>💸 <?php echo esc_html($prejuizo); ?></span>
          <?php endif; ?>
        </div>
        <div class="fs-home-hero__actions">
          <a href="<?php echo get_permalink($golpe_hero); ?>" class="fs-btn fs-btn--primary">Ler análise completa →</a>
          <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--outline-w">Todos os golpes</a>
        </div>
      </div>
      <div class="fs-home-hero__image <?php echo !has_post_thumbnail($golpe_hero->ID) ? 'fs-home-hero__image--empty' : ''; ?>">
        <?php if (has_post_thumbnail($golpe_hero->ID)): ?>
          <a href="<?php echo get_permalink($golpe_hero); ?>"><?php echo get_the_post_thumbnail($golpe_hero->ID, 'full', ['loading' => 'eager']); ?></a>
        <?php else: ?>
          <svg width="80" height="80" viewBox="0 0 32 32" fill="none" opacity=".15">
            <path d="M16 2L4 8v8c0 6.63 5.12 12.84 12 14.4C22.88 28.84 28 22.63 28 16V8L16 2z" fill="#f97316"/>
            <circle cx="16" cy="16" r="5" fill="white"/>
          </svg>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <?php endif; wp_reset_postdata(); ?>

  <!-- Stats bar -->
  <div class="fs-stats-bar">
    <div class="container fs-stats-bar__inner">
      <div class="fs-stat">
        <span class="fs-stat__num" data-count="<?php echo $total_golpes; ?>"><?php echo $total_golpes; ?></span>
        <span class="fs-stat__label">Golpes documentados</span>
      </div>
      <div class="fs-stat__divider"></div>
      <div class="fs-stat">
        <span class="fs-stat__num" data-count="<?php echo $total_fraudes; ?>"><?php echo $total_fraudes; ?></span>
        <span class="fs-stat__label">Fraudes catalogadas</span>
      </div>
      <div class="fs-stat__divider"></div>
      <div class="fs-stat">
        <span class="fs-stat__num" data-count="<?php echo $total_posts; ?>"><?php echo $total_posts; ?></span>
        <span class="fs-stat__label">Artigos educativos</span>
      </div>
      <div class="fs-stat__divider"></div>
      <div class="fs-stat">
        <span class="fs-stat__num">24h</span>
        <span class="fs-stat__label">Tempo de publicação</span>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════
       GOLPES — Engenharia social / manipulação
  ════════════════════════════════════════ -->
  <?php if ($golpes_grid): ?>
  <section class="fs-home-section">
    <div class="container">
      <div class="fs-home-section__head">
        <div>
          <h2 class="fs-home-section__title">
            <span class="fs-home-section__dot" style="background:var(--red)"></span>
            Golpes
          </h2>
          <p class="fs-home-section__sub">Você é manipulado a agir — pagar, transferir, entregar dados</p>
        </div>
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--ghost fs-btn--sm">Ver todos os golpes →</a>
      </div>
      <?php if ($tipos_golpe && !is_wp_error($tipos_golpe)): ?>
      <div class="fs-filter-bar">
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-filter-pill is-active">Todos</a>
        <?php foreach ($tipos_golpe as $t): ?>
          <a href="<?php echo get_term_link($t); ?>" class="fs-filter-pill"><?php echo esc_html($t->name); ?> <span><?php echo $t->count; ?></span></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="fs-grid fs-grid--4">
        <?php foreach ($golpes_grid as $g): fs_golpe_card($g, true); endforeach; wp_reset_postdata(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ══════════════════════════════════════
       FRAUDES — Acesso não autorizado
  ════════════════════════════════════════ -->
  <section class="fs-home-fraudes">
    <div class="container">
      <div class="fs-home-section__head">
        <div>
          <h2 class="fs-home-section__title">
            <span class="fs-home-section__dot" style="background:#a78bfa"></span>
            Fraudes
          </h2>
          <p class="fs-home-section__sub">Acontece sem sua ação — cartão clonado, conta invadida, dados vazados</p>
        </div>
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-btn fs-btn--ghost fs-btn--sm">Ver todas as fraudes →</a>
      </div>

      <?php if ($fraudes_grid): ?>
        <?php if ($tipos_fraude && !is_wp_error($tipos_fraude)): ?>
        <div class="fs-filter-bar">
          <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-filter-pill is-active">Todas</a>
          <?php foreach ($tipos_fraude as $t): ?>
            <a href="<?php echo get_term_link($t); ?>" class="fs-filter-pill"><?php echo esc_html($t->name); ?> <span><?php echo $t->count; ?></span></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="fs-grid fs-grid--4">
          <?php foreach ($fraudes_grid as $f): fs_fraude_card($f, true); endforeach; wp_reset_postdata(); ?>
        </div>
      <?php else: ?>
        <div style="text-align:center;padding:40px 0;color:rgba(255,255,255,.35);">
          <p style="font-size:var(--text-lg);">Primeiros registros de fraudes em breve.</p>
          <p style="font-size:var(--text-sm);margin-top:8px;">Acompanhe: cartão clonado, SIM swap, vazamento de dados bancários.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ══════════════════════════════════════
       ARTIGO EM DESTAQUE + GRID
  ════════════════════════════════════════ -->
  <?php if ($artigo_hero): ?>
  <section class="fs-home-section fs-home-section--surface">
    <div class="container">
      <div class="fs-home-section__head">
        <div>
          <h2 class="fs-home-section__title">
            <span class="fs-home-section__dot" style="background:var(--orange)"></span>
            Leitura Recomendada
          </h2>
        </div>
        <a href="<?php echo home_url('/artigos/'); ?>" class="fs-btn fs-btn--ghost fs-btn--sm">Todos os artigos →</a>
      </div>
      <?php fs_artigo_card_hero($artigo_hero); ?>
    </div>
  </section>
  <?php endif; wp_reset_postdata(); ?>

  <?php if ($artigos_grid): ?>
  <section class="fs-home-section">
    <div class="container">
      <h2 class="fs-home-section__title" style="margin-bottom:24px;">
        <span class="fs-home-section__dot" style="background:var(--blue)"></span>
        Artigos Educativos
      </h2>
      <div class="fs-grid fs-grid--3">
        <?php foreach ($artigos_grid as $a): fs_artigo_card($a); endforeach; wp_reset_postdata(); ?>
      </div>
      <div style="text-align:center;margin-top:32px;">
        <a href="<?php echo home_url('/artigos/'); ?>" class="fs-btn fs-btn--ghost">Ver todos os artigos</a>
      </div>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php get_footer(); ?>
