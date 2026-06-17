<?php get_header(); ?>

<main class="fs-archive fs-archive--golpes">

  <div class="fs-archive__hero fs-archive__hero--dark">
    <div class="container">
      <?php if (is_tax()): ?>
        <span class="fs-eyebrow"><?php echo get_taxonomy(get_queried_object()->taxonomy)->labels->singular_name; ?></span>
        <h1 class="fs-archive__title"><?php single_term_title(); ?></h1>
      <?php else: ?>
        <span class="fs-eyebrow">Central de Alertas</span>
        <h1 class="fs-archive__title">Golpes e Fraudes Bancárias</h1>
        <p class="fs-archive__desc">Modus operandi identificados e documentados. Fique informado antes de ser a próxima vítima.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Filtros -->
  <div class="fs-filter-strip">
    <div class="container fs-filter-strip__inner">

      <div class="fs-filter-group">
        <span class="fs-filter-group__label">Tipo:</span>
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-filter-pill <?php echo !is_tax('tipo_golpe') ? 'is-active' : ''; ?>">Todos</a>
        <?php $tipos = get_terms(['taxonomy' => 'tipo_golpe', 'hide_empty' => true]);
        foreach ($tipos as $t) echo '<a href="' . get_term_link($t) . '" class="fs-filter-pill ' . (is_tax('tipo_golpe', $t->slug) ? 'is-active' : '') . '">' . esc_html($t->name) . ' <span>' . $t->count . '</span></a>'; ?>
      </div>

      <div class="fs-filter-group">
        <span class="fs-filter-group__label">Canal:</span>
        <?php $canais = get_terms(['taxonomy' => 'canal_golpe', 'hide_empty' => true]);
        foreach ($canais as $c) echo '<a href="' . get_term_link($c) . '" class="fs-filter-pill ' . (is_tax('canal_golpe', $c->slug) ? 'is-active' : '') . '">' . esc_html($c->name) . '</a>'; ?>
      </div>

    </div>
  </div>

  <div class="container fs-archive__body">

    <?php if (have_posts()): ?>
      <div class="fs-grid fs-grid--3">
        <?php while (have_posts()) : the_post(); fs_golpe_card(get_post()); endwhile; ?>
      </div>
      <div class="fs-pagination">
        <?php the_posts_pagination(['mid_size' => 2, 'prev_text' => '← Anterior', 'next_text' => 'Próxima →']); ?>
      </div>
    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhum alerta encontrado nesta categoria.</p>
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--ghost">Ver todos os alertas</a>
      </div>
    <?php endif; ?>

  </div>

</main>

<?php get_footer(); ?>
