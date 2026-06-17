<?php get_header(); ?>

<?php
$tipo_atual   = is_tax('tipo_fraude') ? get_queried_object() : null;
$tipos_fraude = get_terms(['taxonomy' => 'tipo_fraude', 'hide_empty' => true]);
$title        = $tipo_atual ? $tipo_atual->name : 'Central de Fraudes';
$desc         = $tipo_atual ? $tipo_atual->description : 'Fraudes bancárias onde o acesso não autorizado ocorre sem ação direta da vítima — cartão clonado, conta invadida, SIM swap, vazamento de dados.';
?>

<div class="fs-archive__hero" style="background:#0f0a2a;border-bottom:4px solid #7c3aed;">
  <div class="container">
    <span class="fs-eyebrow" style="color:rgba(167,139,250,.8);">Fraudes</span>
    <h1 class="fs-archive__title" style="color:#fff;"><?php echo esc_html($title); ?></h1>
    <p class="fs-archive__desc" style="color:rgba(255,255,255,.55);"><?php echo esc_html($desc); ?></p>
  </div>
</div>

<!-- Diferencial visual entre golpes e fraudes -->
<div style="background:#1a0f3a;border-bottom:1px solid rgba(124,58,237,.3);padding:14px 0;">
  <div style="width:min(1200px,100% - 2rem);margin:0 auto;display:flex;gap:32px;flex-wrap:wrap;align-items:center;">
    <span style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(167,139,250,.6);">Tipo</span>
    <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-filter-pill" style="background:rgba(124,58,237,.2);border-color:rgba(124,58,237,.4);color:#c4b5fd;" <?php echo !$tipo_atual ? 'style="background:#7c3aed;border-color:#7c3aed;color:#fff;"' : ''; ?>>Todas</a>
    <?php if ($tipos_fraude && !is_wp_error($tipos_fraude)): foreach ($tipos_fraude as $t): ?>
      <a href="<?php echo get_term_link($t); ?>" class="fs-filter-pill" style="<?php echo ($tipo_atual && $tipo_atual->term_id === $t->term_id) ? 'background:#7c3aed;border-color:#7c3aed;color:#fff;' : 'background:rgba(124,58,237,.1);border-color:rgba(124,58,237,.25);color:rgba(167,139,250,.8);'; ?>">
        <?php echo esc_html($t->name); ?> <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.4);"><?php echo $t->count; ?></span>
      </a>
    <?php endforeach; endif; ?>
  </div>
</div>

<div class="fs-archive__body">
  <div class="container">
    <?php if (have_posts()): ?>
      <div class="fs-grid fs-grid--3">
        <?php while (have_posts()) : the_post(); fs_fraude_card(get_post(), true); endwhile; ?>
      </div>
      <div class="fs-pagination"><?php the_posts_pagination(['prev_text' => 'Anterior', 'next_text' => 'Próximas', 'mid_size' => 2]); ?></div>
    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhuma fraude encontrada nesta categoria.</p>
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-btn fs-btn--navy">Ver todas as fraudes</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
