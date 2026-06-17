<?php get_header(); ?>

<?php
$cat    = get_queried_object();
$is_cat = is_category();
$title  = $is_cat ? single_cat_title('', false) : (is_tag() ? single_tag_title('', false) : 'Todos os Artigos');
$desc   = $is_cat && category_description() ? category_description() : 'Guias completos, análises e orientações para você se proteger de fraudes e golpes bancários.';
?>

<div class="fs-archive__hero">
  <div class="container">
    <span class="fs-eyebrow">Artigos</span>
    <h1 class="fs-archive__title"><?php echo esc_html($title); ?></h1>
    <p class="fs-archive__desc"><?php echo wp_kses_post($desc); ?></p>
  </div>
</div>

<?php
$cats = get_categories(['hide_empty' => true, 'number' => 8]);
if ($cats): ?>
<div class="fs-filter-strip">
  <div class="fs-filter-strip__inner">
    <div class="fs-filter-group">
      <a href="<?php echo home_url('/artigos/'); ?>" class="fs-filter-pill <?php echo !$is_cat ? 'is-active' : ''; ?>">Todos</a>
      <?php foreach ($cats as $c): ?>
        <a href="<?php echo get_category_link($c); ?>" class="fs-filter-pill <?php echo ($is_cat && get_queried_object_id() === $c->term_id) ? 'is-active' : ''; ?>">
          <?php echo esc_html($c->name); ?> <span><?php echo $c->count; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="fs-archive__body">
  <div class="container">

    <?php if (have_posts()):
      $first = true; ?>
      <?php while (have_posts()) : the_post();
        if ($first) {
          echo '<div class="fs-featured"><p class="fs-featured__label">Em Destaque</p>';
          fs_artigo_card_hero(get_post());
          echo '</div><p class="fs-section-title" style="margin-top:40px;">Mais Artigos</p><div class="fs-grid fs-grid--3">';
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
