<?php get_header(); ?>

<main class="fs-archive fs-archive--artigos">

  <div class="fs-archive__hero">
    <div class="container">
      <?php if (is_category()): ?>
        <span class="fs-eyebrow">Categoria</span>
        <h1 class="fs-archive__title"><?php single_cat_title(); ?></h1>
        <?php if (category_description()) echo '<p class="fs-archive__desc">' . category_description() . '</p>'; ?>
      <?php elseif (is_tag()): ?>
        <span class="fs-eyebrow">Tag</span>
        <h1 class="fs-archive__title">#<?php single_tag_title(); ?></h1>
      <?php elseif (is_tax('publico_alvo')): ?>
        <span class="fs-eyebrow">Público-alvo</span>
        <h1 class="fs-archive__title"><?php single_term_title(); ?></h1>
      <?php else: ?>
        <span class="fs-eyebrow">Artigos</span>
        <h1 class="fs-archive__title">Todos os Artigos</h1>
        <p class="fs-archive__desc">Conteúdo educativo sobre fraudes, golpes e como se proteger no ambiente digital e financeiro.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="container fs-archive__body">

    <!-- Filtro de categorias -->
    <div class="fs-filter-bar">
      <?php
      $cats = get_categories(['hide_empty' => true, 'number' => 10]);
      $current_cat = get_queried_object_id();
      ?>
      <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="fs-filter-pill <?php echo !is_category() ? 'is-active' : ''; ?>">Todos</a>
      <?php foreach ($cats as $cat): ?>
        <a href="<?php echo get_category_link($cat); ?>" class="fs-filter-pill <?php echo is_category($cat->term_id) ? 'is-active' : ''; ?>">
          <?php echo esc_html($cat->name); ?> <span><?php echo $cat->count; ?></span>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Grid de artigos -->
    <?php if (have_posts()): ?>
      <div class="fs-grid fs-grid--3">
        <?php $i = 0; while (have_posts()) : the_post(); $i++;
          fs_artigo_card(get_post(), $i === 1);
        endwhile; ?>
      </div>

      <div class="fs-pagination">
        <?php the_posts_pagination(['mid_size' => 2, 'prev_text' => '← Anterior', 'next_text' => 'Próxima →']); ?>
      </div>

    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhum artigo encontrado.</p>
        <a href="<?php echo home_url('/'); ?>" class="fs-btn fs-btn--ghost">← Voltar ao início</a>
      </div>
    <?php endif; ?>

  </div>

</main>

<?php get_footer(); ?>
