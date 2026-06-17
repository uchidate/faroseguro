<?php get_header(); ?>

<main class="fs-archive fs-archive--search">
  <div class="fs-archive__hero">
    <div class="container">
      <span class="fs-eyebrow">Busca</span>
      <h1 class="fs-archive__title">
        <?php if (get_search_query()): ?>
          Resultados para: <em>"<?php the_search_query(); ?>"</em>
        <?php else: ?>
          O que você procura?
        <?php endif; ?>
      </h1>
      <form class="fs-search-form" role="search" method="get" action="<?php echo home_url('/'); ?>">
        <input type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="Buscar golpes, artigos, termos…" class="fs-search-form__input" autofocus>
        <button type="submit" class="fs-btn fs-btn--primary">Buscar</button>
      </form>
    </div>
  </div>

  <div class="container fs-archive__body">
    <?php if (have_posts()): ?>
      <p class="fs-search-count"><?php printf('%d resultado(s) encontrado(s)', $wp_query->found_posts); ?></p>
      <div class="fs-search-results">
        <?php while (have_posts()) : the_post();
          $type = get_post_type();
          $nivel = $type === 'golpe' ? get_post_meta(get_the_ID(), 'nivel_risco', true) : null;
        ?>
          <article class="fs-search-result">
            <div class="fs-search-result__meta">
              <span class="fs-tag"><?php echo $type === 'golpe' ? '🚨 Alerta' : ($type === 'glossario' ? '📖 Glossário' : '📝 Artigo'); ?></span>
              <?php if ($nivel) echo fs_badge_risco($nivel); ?>
              <time class="fs-card__date"><?php the_date('d M Y'); ?></time>
            </div>
            <h2 class="fs-search-result__title">
              <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <p class="fs-search-result__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
          </article>
        <?php endwhile; ?>
      </div>
      <div class="fs-pagination"><?php the_posts_pagination(['mid_size' => 2]); ?></div>
    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhum resultado para <strong>"<?php the_search_query(); ?>"</strong>.</p>
        <p>Tente termos como: Pix, phishing, golpe do suporte, deepfake…</p>
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--ghost">Ver todos os alertas</a>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
