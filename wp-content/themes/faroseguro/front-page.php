<?php get_header(); ?>

<?php
$golpe_hero_q = get_posts(['post_type' => 'golpe', 'numberposts' => 1,
  'meta_query' => [['key' => 'nivel_risco', 'value' => 'alto']]]);
$golpe_hero   = $golpe_hero_q ? $golpe_hero_q[0]
              : (get_posts(['post_type' => 'golpe', 'numberposts' => 1])[0] ?? null);
$hero_id      = $golpe_hero ? $golpe_hero->ID : 0;

$golpes_grid     = get_posts(['post_type' => 'golpe',  'numberposts' => 3, 'post__not_in' => [$hero_id]]);
$fraudes_recentes= get_posts(['post_type' => 'fraude', 'numberposts' => 1,
  'meta_query' => [['key' => 'nivel_risco', 'value' => 'alto']]]);
$fraude_hero     = $fraudes_recentes ? $fraudes_recentes[0] : null;
$fraudes_grid    = get_posts(['post_type' => 'fraude', 'numberposts' => 4,
  'post__not_in' => $fraude_hero ? [$fraude_hero->ID] : []]);

$destaque_q   = get_posts(['post_type' => 'post', 'numberposts' => 1,
  'meta_query' => [['key' => 'artigo_destaque', 'value' => '1']]]);
$artigo_hero  = $destaque_q ? $destaque_q[0]
              : (get_posts(['post_type' => 'post', 'numberposts' => 1])[0] ?? null);
$artigos_grid = get_posts(['post_type' => 'post', 'numberposts' => 6,
  'post__not_in' => $artigo_hero ? [$artigo_hero->ID] : []]);

$tipos_golpe  = get_terms(['taxonomy' => 'tipo_golpe',  'hide_empty' => true, 'number' => 5]);
$tipos_fraude = get_terms(['taxonomy' => 'tipo_fraude', 'hide_empty' => true, 'number' => 5]);
$total_golpes  = wp_count_posts('golpe')->publish;
$total_fraudes = wp_count_posts('fraude')->publish;
$total_posts   = wp_count_posts('post')->publish;
?>

<main class="fs-homepage">

  <!-- SPLIT HERO -->
  <section class="fs-split-hero">

    <?php if ($golpe_hero):
      $nivel    = get_post_meta($golpe_hero->ID, 'nivel_risco', true) ?: 'alto';
      $prejuizo = get_post_meta($golpe_hero->ID, 'prejuizo_estimado', true);
      $tipos_h  = get_the_terms($golpe_hero->ID, 'tipo_golpe');
      $canais_h = get_the_terms($golpe_hero->ID, 'canal_golpe');
    ?>
    <div class="fs-split-hero__panel fs-split-hero__panel--golpe">
      <?php if (has_post_thumbnail($golpe_hero->ID)): ?>
        <div class="fs-split-hero__bg">
          <?php echo get_the_post_thumbnail($golpe_hero->ID, 'full', ['loading' => 'eager', 'fetchpriority' => 'high']); ?>
        </div>
      <?php endif; ?>
      <div class="fs-split-hero__overlay"></div>
      <div class="fs-split-hero__content">
        <div class="fs-split-hero__eyebrow fs-split-hero__eyebrow--golpe">
          <span class="fs-split-hero__dot"></span>
          Golpe em circulação
        </div>
        <?php if ($tipos_h && !is_wp_error($tipos_h)): ?>
          <span class="fs-split-hero__type"><?php echo esc_html($tipos_h[0]->name); ?></span>
        <?php endif; ?>
        <h2 class="fs-split-hero__title">
          <a href="<?php echo get_permalink($golpe_hero); ?>"><?php echo esc_html($golpe_hero->post_title); ?></a>
        </h2>
        <p class="fs-split-hero__desc"><?php echo wp_trim_words(get_the_excerpt($golpe_hero), 22); ?></p>
        <div class="fs-split-hero__meta">
          <?php echo fs_badge_risco($nivel); ?>
          <?php if ($prejuizo): ?>
            <span style="color:rgba(255,255,255,.45);font-size:.7rem;">💸 <?php echo esc_html($prejuizo); ?></span>
          <?php endif; ?>
        </div>
        <a href="<?php echo get_permalink($golpe_hero); ?>" class="fs-split-hero__link">
          Ler análise completa
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
    <?php endif; wp_reset_postdata(); ?>

    <div class="fs-split-hero__divider" aria-hidden="true"></div>

    <!-- Fraude -->
    <?php if ($fraude_hero):
      $fnivel   = get_post_meta($fraude_hero->ID, 'nivel_risco', true) ?: 'alto';
      $fprejuizo= get_post_meta($fraude_hero->ID, 'prejuizo_estimado', true);
      $ftipos   = get_the_terms($fraude_hero->ID, 'tipo_fraude');
    ?>
    <div class="fs-split-hero__panel fs-split-hero__panel--fraude">
      <?php if (has_post_thumbnail($fraude_hero->ID)): ?>
        <div class="fs-split-hero__bg">
          <?php echo get_the_post_thumbnail($fraude_hero->ID, 'full', ['loading' => 'eager', 'fetchpriority' => 'high']); ?>
        </div>
      <?php endif; ?>
      <div class="fs-split-hero__overlay fs-split-hero__overlay--fraude"></div>
      <div class="fs-split-hero__content">
        <div class="fs-split-hero__eyebrow fs-split-hero__eyebrow--fraude">
          <span class="fs-split-hero__dot"></span>
          Nova fraude identificada
        </div>
        <?php if ($ftipos && !is_wp_error($ftipos)): ?>
          <span class="fs-split-hero__type"><?php echo esc_html($ftipos[0]->name); ?></span>
        <?php endif; ?>
        <h2 class="fs-split-hero__title">
          <a href="<?php echo get_permalink($fraude_hero); ?>"><?php echo esc_html($fraude_hero->post_title); ?></a>
        </h2>
        <p class="fs-split-hero__desc"><?php echo wp_trim_words(get_the_excerpt($fraude_hero), 20); ?></p>
        <div class="fs-split-hero__meta">
          <?php
          $bl = ['alto' => ['fs-badge--fraude-alto','🔓 Alto Risco'],'medio' => ['fs-badge--fraude-medio','⚠️ Médio'],'baixo' => ['fs-badge--fraude-baixo','ℹ️ Baixo']];
          [$bc,$bl2] = $bl[$fnivel] ?? $bl['alto'];
          ?>
          <span class="fs-badge <?php echo $bc; ?>"><?php echo $bl2; ?></span>
          <?php if ($fprejuizo): ?>
            <span style="color:rgba(255,255,255,.45);font-size:.7rem;">💸 <?php echo esc_html($fprejuizo); ?></span>
          <?php endif; ?>
        </div>
        <a href="<?php echo get_permalink($fraude_hero); ?>" class="fs-split-hero__link">
          Ler análise completa
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
    <?php else: ?>
    <div class="fs-split-hero__panel fs-split-hero__panel--fraude fs-split-hero__panel--empty">
      <div class="fs-split-hero__content" style="align-items:center;text-align:center;">
        <div class="fs-split-hero__label" style="color:rgba(167,139,250,.7);justify-content:center;">
          <span class="fs-split-hero__dot" style="background:#a78bfa;"></span>
          Fraudes Bancárias
        </div>
        <h2 class="fs-split-hero__title" style="font-size:1.25rem;">Cartão clonado, SIM Swap, Credential Stuffing…</h2>
        <p class="fs-split-hero__desc" style="font-size:.875rem;">Acontece sem sua ação. Acesso não autorizado à sua conta.</p>
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-split-hero__link" style="border-color:rgba(167,139,250,.4);color:#c4b5fd;">
          Ver todas as fraudes →
        </a>
      </div>
    </div>
    <?php endif; wp_reset_postdata(); ?>

  </section>

  <!-- Concept bar compacta -->
  <div class="fs-concept-bar">
    <div class="container fs-concept-bar__inner">
      <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-concept-bar__item">
        <span class="fs-concept-bar__icon">🪤</span>
        <div class="fs-concept-bar__item-text">
          <strong>Golpe</strong>
          <span>Você é manipulado a agir — pagar, transferir ou entregar dados</span>
        </div>
      </a>
      <div class="fs-concept-bar__sep"></div>
      <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-concept-bar__item">
        <span class="fs-concept-bar__icon">🔓</span>
        <div class="fs-concept-bar__item-text">
          <strong>Fraude</strong>
          <span>Acontece sem sua ação — conta invadida, cartão clonado, dados vazados</span>
        </div>
      </a>
    </div>
  </div>

  <!-- ── GOLPES ─────────────────────────────── -->
  <?php if ($golpes_grid): ?>
  <section class="fs-home-section">
    <div class="container">
      <div class="fs-home-section__head">
        <div>
          <h2 class="fs-home-section__title">
            <span class="fs-home-section__dot" style="background:var(--red)"></span>
            Golpes Recentes
          </h2>
          <p class="fs-home-section__sub">Engenharia social — vítima é manipulada a agir</p>
        </div>
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--ghost fs-btn--sm">Ver todos →</a>
      </div>
      <?php if ($tipos_golpe && !is_wp_error($tipos_golpe)): ?>
      <div class="fs-filter-bar">
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-filter-pill is-active">Todos</a>
        <?php foreach ($tipos_golpe as $t): ?>
          <a href="<?php echo get_term_link($t); ?>" class="fs-filter-pill"><?php echo esc_html($t->name); ?> <span><?php echo $t->count; ?></span></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="fs-grid fs-grid--3">
        <?php foreach ($golpes_grid as $g): fs_golpe_card($g, true); endforeach; wp_reset_postdata(); ?>
      </div>
      <div style="text-align:center;margin-top:28px;">
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--navy">Ver todos os golpes</a>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ── FRAUDES ────────────────────────────── -->
  <section class="fs-home-fraudes">
    <div class="container">
      <div class="fs-home-section__head">
        <div>
          <h2 class="fs-home-section__title">
            <span class="fs-home-section__dot" style="background:#a78bfa"></span>
            Fraudes Catalogadas
          </h2>
          <p class="fs-home-section__sub">Acesso não autorizado — acontece sem que você perceba</p>
        </div>
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-btn fs-btn--ghost fs-btn--sm">Ver todas →</a>
      </div>
      <?php if ($tipos_fraude && !is_wp_error($tipos_fraude)): ?>
      <div class="fs-filter-bar">
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-filter-pill is-active">Todas</a>
        <?php foreach ($tipos_fraude as $t): ?>
          <a href="<?php echo get_term_link($t); ?>" class="fs-filter-pill"><?php echo esc_html($t->name); ?> <span><?php echo $t->count; ?></span></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php if ($fraudes_grid): ?>
      <div class="fs-grid fs-grid--4">
        <?php foreach ($fraudes_grid as $f): fs_fraude_card($f, true); endforeach; wp_reset_postdata(); ?>
      </div>
      <div style="text-align:center;margin-top:28px;">
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-btn" style="background:#7c3aed;color:#fff;">Ver todas as fraudes</a>
      </div>
      <?php else: ?>
        <p style="color:rgba(255,255,255,.3);text-align:center;padding:40px 0;">Catalogando fraudes — em breve.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Stats bar -->
  <div class="fs-stats-bar">
    <div class="container fs-stats-bar__inner">
      <div class="fs-stat"><span class="fs-stat__num" data-count="<?php echo $total_golpes; ?>"><?php echo $total_golpes; ?></span><span class="fs-stat__label">Golpes documentados</span></div>
      <div class="fs-stat__divider"></div>
      <div class="fs-stat"><span class="fs-stat__num" data-count="<?php echo $total_fraudes; ?>"><?php echo $total_fraudes; ?></span><span class="fs-stat__label">Fraudes catalogadas</span></div>
      <div class="fs-stat__divider"></div>
      <div class="fs-stat"><span class="fs-stat__num" data-count="<?php echo $total_posts; ?>"><?php echo $total_posts; ?></span><span class="fs-stat__label">Artigos educativos</span></div>
      <div class="fs-stat__divider"></div>
      <div class="fs-stat"><span class="fs-stat__num">24h</span><span class="fs-stat__label">Tempo de publicação</span></div>
    </div>
  </div>

  <!-- ── ARTIGO DESTAQUE + GRID ─────────────── -->
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
