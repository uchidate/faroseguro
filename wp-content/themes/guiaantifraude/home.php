<?php get_header(); ?>

<?php
$cats      = get_categories(['hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 8]);
$cat_atual = is_category() ? get_queried_object() : null;

fs_archive_hero('Artigos', 'Artigos Educativos', 'Guias práticos e análises sobre segurança financeira, proteção contra fraudes e golpes no Brasil.', 'dark');

fs_archive_filter_strip([
    [
        'label'      => 'Categoria',
        'all_url'    => home_url('/artigos/'),
        'all_active' => !$cat_atual,
        'terms'      => $cats,
        'current_id' => $cat_atual ? $cat_atual->term_id : null,
    ],
]);
?>

<div class="fs-archive__body">
  <div class="container">
    <?php if (have_posts()): ?>
      <div class="fs-grid fs-grid--3">
        <?php while (have_posts()): the_post(); fs_artigo_card(get_post()); endwhile; ?>
      </div>
      <div class="fs-pagination">
        <?php the_posts_pagination(['prev_text' => 'Anterior', 'next_text' => 'Próximos', 'mid_size' => 2]); ?>
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
