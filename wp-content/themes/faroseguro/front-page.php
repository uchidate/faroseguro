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

  <!-- ÚLTIMOS ALERTAS — hero compacto editorial -->
  <section class="fs-latest-alerts">
    <div class="container fs-latest-alerts__inner">
      <div class="fs-latest-alerts__bar">
        <span class="fs-latest-alerts__bar-label">
          <span class="fs-latest-alerts__bar-dot"></span>
          Alertas em tempo real
        </span>
      </div>
      <div class="fs-latest-alerts__grid">

        <!-- Golpe -->
        <?php if ($golpe_hero):
          $nivel   = get_post_meta($golpe_hero->ID, 'nivel_risco', true) ?: 'alto';
          $tipos_h = get_the_terms($golpe_hero->ID, 'tipo_golpe');
        ?>
        <div class="fs-latest-alert fs-latest-alert--golpe">
          <div class="fs-latest-alert__label">
            <span class="fs-latest-alert__dot"></span>
            Golpe em circulação
          </div>
          <?php if ($tipos_h && !is_wp_error($tipos_h)): ?>
            <span class="fs-latest-alert__type"><?php echo esc_html($tipos_h[0]->name); ?></span>
          <?php endif; ?>
          <h2 class="fs-latest-alert__title">
            <a href="<?php echo get_permalink($golpe_hero); ?>"><?php echo esc_html(fs_editorial_text($golpe_hero->post_title)); ?></a>
          </h2>
          <p class="fs-latest-alert__desc"><?php echo wp_trim_words(get_the_excerpt($golpe_hero), 20); ?></p>
          <div class="fs-latest-alert__foot">
            <?php echo fs_badge_risco($nivel); ?>
            <a href="<?php echo get_permalink($golpe_hero); ?>" class="fs-latest-alert__link">
              Ler análise completa
            </a>
          </div>
        </div>
        <?php endif; wp_reset_postdata(); ?>

        <div class="fs-latest-alerts__sep" aria-hidden="true"></div>

        <!-- Fraude -->
        <?php if ($fraude_hero):
          $fnivel = get_post_meta($fraude_hero->ID, 'nivel_risco', true) ?: 'alto';
          $ftipos = get_the_terms($fraude_hero->ID, 'tipo_fraude');
          $fbl = ['alto' => ['fs-badge--fraude-alto','Alto Risco'],'medio' => ['fs-badge--fraude-medio','Risco Médio'],'baixo' => ['fs-badge--fraude-baixo','Baixo Risco']];
          [$fbc,$fbl2] = $fbl[$fnivel] ?? $fbl['alto'];
        ?>
        <div class="fs-latest-alert fs-latest-alert--fraude">
          <div class="fs-latest-alert__label">
            <span class="fs-latest-alert__dot"></span>
            Nova fraude identificada
          </div>
          <?php if ($ftipos && !is_wp_error($ftipos)): ?>
            <span class="fs-latest-alert__type"><?php echo esc_html($ftipos[0]->name); ?></span>
          <?php endif; ?>
          <h2 class="fs-latest-alert__title">
            <a href="<?php echo get_permalink($fraude_hero); ?>"><?php echo esc_html(fs_editorial_text($fraude_hero->post_title)); ?></a>
          </h2>
          <p class="fs-latest-alert__desc"><?php echo wp_trim_words(get_the_excerpt($fraude_hero), 20); ?></p>
          <div class="fs-latest-alert__foot">
            <span class="fs-badge <?php echo $fbc; ?>"><?php echo $fbl2; ?></span>
            <a href="<?php echo get_permalink($fraude_hero); ?>" class="fs-latest-alert__link">
              Ler análise completa
            </a>
          </div>
        </div>
        <?php else: ?>
        <div class="fs-latest-alert fs-latest-alert--fraude">
          <div class="fs-latest-alert__label">
            <span class="fs-latest-alert__dot"></span>
            Fraudes Bancárias
          </div>
          <h2 class="fs-latest-alert__title" style="font-size:1.1rem;">Cartão clonado, SIM Swap, Credential Stuffing…</h2>
          <p class="fs-latest-alert__desc">Acontece sem sua ação. Acesso não autorizado à sua conta bancária.</p>
          <div class="fs-latest-alert__foot">
            <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-latest-alert__link">Ver todas as fraudes</a>
          </div>
        </div>
        <?php endif; wp_reset_postdata(); ?>

      </div><!-- .fs-latest-alerts__grid -->
    </div><!-- .container -->
  </section>

  <!-- Concept bar compacta -->
  <div class="fs-concept-bar">
    <div class="container fs-concept-bar__inner">
      <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-concept-bar__item">
        <svg class="fs-concept-bar__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div class="fs-concept-bar__item-text">
          <strong>Golpe</strong>
          <span>Você é manipulado a agir — pagar, transferir ou entregar dados</span>
        </div>
      </a>
      <div class="fs-concept-bar__sep"></div>
      <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-concept-bar__item">
        <svg class="fs-concept-bar__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <div class="fs-concept-bar__item-text">
          <strong>Fraude</strong>
          <span>Acontece sem sua ação — conta invadida, cartão clonado, dados vazados</span>
        </div>
      </a>
    </div>
  </div>

  <!-- Trilhas rápidas por intenção do usuário -->
  <section class="fs-user-paths">
    <div class="container">
      <div class="fs-user-paths__grid">
        <a href="<?php echo esc_url(home_url('/?s=pix')); ?>" class="fs-user-path fs-user-path--red">
          <span class="fs-user-path__icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
          </span>
          <span><strong>Recebi uma mensagem suspeita</strong><small>Compare sinais de phishing, falso atendimento e golpe do Pix.</small></span>
        </a>
        <a href="<?php echo esc_url(get_post_type_archive_link('fraude')); ?>" class="fs-user-path fs-user-path--purple">
          <span class="fs-user-path__icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <span><strong>Minha conta foi acessada</strong><small>Veja fraudes por invasão, SIM swap, vazamento e cartão clonado.</small></span>
        </a>
        <a href="<?php echo esc_url(home_url('/contato/')); ?>" class="fs-user-path fs-user-path--orange">
          <span class="fs-user-path__icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="m22 2-7 20-4-9-9-4Z"/></svg>
          </span>
          <span><strong>Quero denunciar um caso</strong><small>Envie detalhes para ajudar outras pessoas e reúna canais oficiais.</small></span>
        </a>
      </div>
    </div>
  </section>

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
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--ghost fs-btn--sm">Ver todos</a>
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

  <section class="fs-trust-band">
    <div class="container fs-trust-band__inner">
      <div>
        <p class="fs-eyebrow">Conteúdo verificado</p>
        <h2>Informação prática antes de você clicar, pagar ou responder.</h2>
      </div>
      <div class="fs-trust-band__checks">
        <span>Fontes oficiais citadas</span>
        <span>Atualização editorial</span>
        <span>Separação entre golpe e fraude</span>
      </div>
    </div>
  </section>

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
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-btn fs-btn--ghost fs-btn--sm">Ver todas</a>
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

  <?php fs_guides_section($artigo_hero ? [$artigo_hero->ID] : []); ?>

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
        <a href="<?php echo home_url('/artigos/'); ?>" class="fs-btn fs-btn--ghost fs-btn--sm">Todos os artigos</a>
      </div>
      <?php fs_artigo_card_hero($artigo_hero); ?>
    </div>
  </section>
  <?php endif; wp_reset_postdata(); ?>

  <section class="fs-home-section fs-home-section--official">
    <div class="container">
      <div class="fs-home-section__head">
        <div>
          <h2 class="fs-home-section__title">
            <span class="fs-home-section__dot" style="background:var(--blue)"></span>
            Canais oficiais para resolver o problema
          </h2>
          <p class="fs-home-section__sub">Use o portal para se orientar, mas registre reclamações e ocorrências nos canais competentes.</p>
        </div>
      </div>
      <?php fs_official_channels('fs-official-channels--home'); ?>
    </div>
  </section>

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
