<?php get_header(); ?>

<?php while (have_posts()) : the_post();
  $leitura    = fs_leitura();
  $atualizado = get_post_meta(get_the_ID(), 'atualizado_em', true);
  $destaque   = get_post_meta(get_the_ID(), 'artigo_destaque', true) === '1';
?>

<main class="fs-single fs-single--artigo">

  <!-- Hero do artigo -->
  <div class="fs-single__hero">
    <div class="container fs-single__hero-inner">
      <div class="fs-single__meta-top">
        <?= fs_post_tags(get_the_ID(), 'category') ?>
        <?= fs_post_tags(get_the_ID(), 'publico_alvo') ?>
      </div>
      <h1 class="fs-single__title"><?php the_title(); ?></h1>
      <?php if (has_excerpt()): ?>
        <p class="fs-single__lead"><?php the_excerpt(); ?></p>
      <?php endif; ?>
      <div class="fs-single__byline">
        <span>📅 <time datetime="<?php the_date('c'); ?>"><?php the_date('d \d\e F \d\e Y'); ?></time></span>
        <?php if ($atualizado): ?>
          <span>🔄 Atualizado em <?= esc_html(date('d/m/Y', strtotime($atualizado))) ?></span>
        <?php endif; ?>
        <span>⏱ <?= $leitura ?></span>
      </div>
    </div>
  </div>

  <?php if (has_post_thumbnail()): ?>
    <div class="fs-single__cover">
      <div class="container">
        <?php the_post_thumbnail('fs-hero', ['class' => 'fs-single__cover-img', 'loading' => 'eager']); ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Corpo -->
  <div class="fs-single__body container">

    <div class="fs-single__content">
      <div class="fs-prose">
        <?php the_content(); ?>
      </div>

      <!-- Tags -->
      <?php $tags = get_the_tags(); if ($tags): ?>
        <div class="fs-single__tags">
          <?php foreach ($tags as $t): ?>
            <a href="<?= get_tag_link($t) ?>" class="fs-tag">#<?= esc_html($t->name) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Navegação entre artigos -->
      <nav class="fs-single__nav-posts">
        <div class="fs-single__nav-prev">
          <?php previous_post_link('<span class="fs-single__nav-label">← Anterior</span><strong class="fs-single__nav-title">%link</strong>'); ?>
        </div>
        <div class="fs-single__nav-next">
          <?php next_post_link('<span class="fs-single__nav-label">Próximo →</span><strong class="fs-single__nav-title">%link</strong>'); ?>
        </div>
      </nav>
    </div>

    <!-- Sidebar -->
    <aside class="fs-single__sidebar">

      <!-- Alertas recentes -->
      <div class="fs-sidebar-widget">
        <h3 class="fs-sidebar-widget__title">🚨 Alertas Recentes</h3>
        <?php
        $golpes = get_posts(['post_type' => 'golpe', 'numberposts' => 4, 'post_status' => 'publish']);
        foreach ($golpes as $g):
          $nivel = get_post_meta($g->ID, 'nivel_risco', true) ?: 'alto';
        ?>
          <a href="<?= get_permalink($g) ?>" class="fs-sidebar-item">
            <?= fs_badge_risco($nivel) ?>
            <span><?= esc_html($g->post_title) ?></span>
          </a>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>

      <!-- Categorias -->
      <div class="fs-sidebar-widget">
        <h3 class="fs-sidebar-widget__title">📂 Categorias</h3>
        <ul class="fs-sidebar-categories">
          <?php wp_list_categories(['show_count' => true, 'title_li' => '', 'orderby' => 'count', 'order' => 'DESC', 'number' => 8]); ?>
        </ul>
      </div>

      <?php fs_ad('sidebar'); ?>
      <?php dynamic_sidebar('sidebar-artigos'); ?>

    </aside>

  </div>

  <!-- Artigos relacionados -->
  <?php
  $cats      = wp_get_post_categories(get_the_ID());
  $related   = $cats ? get_posts([
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => [get_the_ID()],
    'category__in'   => $cats,
  ]) : [];
  if ($related): ?>
    <section class="fs-related">
      <div class="container">
        <h2 class="fs-related__title">Leia também</h2>
        <div class="fs-grid fs-grid--3">
          <?php foreach ($related as $p): setup_postdata($p); fs_artigo_card($p); endforeach; wp_reset_postdata(); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

</main>

<?php endwhile; ?>
<?php get_footer(); ?>
