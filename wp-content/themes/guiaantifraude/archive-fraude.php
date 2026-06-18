<?php get_header(); ?>

<?php
$tipo_atual   = is_tax('tipo_fraude') ? get_queried_object() : null;
$tipos_fraude = get_terms(['taxonomy' => 'tipo_fraude', 'hide_empty' => true]);
$title        = $tipo_atual ? $tipo_atual->name : 'Central de Fraudes';
$desc         = $tipo_atual ? $tipo_atual->description : 'Fraudes bancárias onde o acesso não autorizado ocorre sem ação direta da vítima — cartão clonado, conta invadida, SIM swap, vazamento de dados.';
?>

<div class="fs-archive__hero fs-archive__hero--fraude">
  <div class="container">
    <span class="fs-eyebrow">Fraudes</span>
    <h1 class="fs-archive__title"><?php echo esc_html($title); ?></h1>
    <p class="fs-archive__desc"><?php echo esc_html($desc); ?></p>
  </div>
</div>

<div class="fs-filter-strip fs-filter-strip--fraude">
  <div class="fs-filter-strip__inner">
    <div class="fs-filter-group">
      <span class="fs-filter-group__label">Tipo</span>
      <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-filter-pill <?php echo !$tipo_atual ? 'is-active' : ''; ?>">Todas</a>
      <?php if ($tipos_fraude && !is_wp_error($tipos_fraude)): foreach ($tipos_fraude as $t): ?>
        <a href="<?php echo get_term_link($t); ?>" class="fs-filter-pill <?php echo ($tipo_atual && $tipo_atual->term_id === $t->term_id) ? 'is-active' : ''; ?>">
          <?php echo esc_html($t->name); ?> <span><?php echo $t->count; ?></span>
        </a>
      <?php endforeach; endif; ?>
    </div>
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
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-btn fs-btn--purple">Ver todas as fraudes</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
