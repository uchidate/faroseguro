<?php get_header(); ?>

<?php
$is_cat = is_category();
$title  = $is_cat ? single_cat_title('', false) : (is_tag() ? single_tag_title('', false) : 'Todos os Artigos');
$desc   = $is_cat && category_description() ? category_description() : 'Guias completos, análises e orientações para você se proteger de fraudes e golpes bancários.';
$cats   = get_categories(['hide_empty' => true, 'number' => 8]);

$total_artigos = wp_count_posts('post')->publish;
$total_artigos = (int) wp_count_posts('post')->publish;
fs_archive_hero('Artigos', $title, $desc, 'dark', $total_artigos, 'artigos publicados');

fs_archive_filter_strip([
    [
        'label'      => 'Categoria',
        'all_url'    => home_url('/artigos/'),
        'all_active' => !$is_cat,
        'terms'      => $cats,
        'current_id' => $is_cat ? get_queried_object_id() : null,
    ],
]);
?>

<div class="fs-archive__body">
  <div class="container">
    <?php if (have_posts()):
      $first = true; ?>
      <?php while (have_posts()) : the_post();
        if ($first) {
          echo '<div class="fs-featured"><p class="fs-featured__label">Em Destaque</p>';
          fs_artigo_card_hero(get_post());
          echo '</div><p class="fs-section-title fs-section-title--spaced">Mais Artigos</p><div class="fs-grid fs-grid--3">';
          $first = false;
          continue;
        }
        fs_artigo_card(get_post());
      endwhile;
      if (!$first) echo '</div>';
      ?>
      <div class="fs-pagination"><?php the_posts_pagination(['prev_text' => 'Anterior', 'next_text' => 'Próximos', 'mid_size' => 2]); ?></div>
    <?php else: ?>
      <div class="fs-empty"><p>Nenhum artigo encontrado.</p></div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
