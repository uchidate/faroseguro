<?php get_header(); ?>

<?php
$cats = get_categories(['hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 8]);
$cat_atual = is_category() ? get_queried_object() : null;
?>

<div class="fs-archive__hero fs-archive__hero--light">
  <div class="container">
    <span class="fs-eyebrow fs-eyebrow--dark">Artigos</span>
    <h1 class="fs-archive__title fs-archive__title--dark">Artigos Educativos</h1>
    <p class="fs-archive__desc fs-archive__desc--dark">Guias práticos e análises sobre segurança financeira, proteção contra fraudes e golpes no Brasil.</p>
  </div>
</div>

<?php if ($cats): ?>
<div class="fs-filter-strip fs-filter-strip--light">
  <div class="fs-filter-strip__inner">
    <div class="fs-filter-group">
      <a href="<?php echo home_url('/artigos/'); ?>" class="fs-filter-pill <?php echo (!$cat_atual) ? 'is-active' : ''; ?>">Todos</a>
      <?php foreach ($cats as $c): ?>
        <a href="<?php echo get_category_link($c); ?>" class="fs-filter-pill <?php echo ($cat_atual && $cat_atual->term_id === $c->term_id) ? 'is-active' : ''; ?>">
          <?php echo esc_html($c->name); ?> <span><?php echo $c->count; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="fs-archive__body">
  <div class="container">
    <?php if (have_posts()): ?>
      <div class="fs-grid fs-grid--3">
        <?php while (have_posts()): the_post(); fs_artigo_card(get_post()); endwhile; ?>
      </div>
      <div class="fs-pagination">
        <?php the_posts_pagination(['prev_text' => '← Anterior', 'next_text' => 'Próximos →', 'mid_size' => 2]); ?>
      </div>
    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhum artigo encontrado.</p>
        <a href="<?php echo home_url('/'); ?>" class="fs-btn fs-btn--navy">Voltar ao início</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
