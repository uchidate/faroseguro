<?php get_header(); ?>

<div class="fs-archive__hero fs-archive__hero--dark">
  <div class="container">
    <span class="fs-eyebrow">🔍 Busca</span>
    <h1 class="fs-archive__title">
      <?php if (get_search_query()): ?>Resultados para "<?php the_search_query(); ?>"
      <?php else: ?>Buscar no Faro Seguro<?php endif; ?>
    </h1>
    <form class="fs-search-form" role="search" method="get" action="<?php echo home_url('/'); ?>">
      <input class="fs-search-form__input" type="search" name="s" placeholder="Buscar golpes, artigos, termos…" value="<?php echo get_search_query(); ?>" autofocus>
      <button type="submit" class="fs-btn fs-btn--primary">Buscar</button>
    </form>
  </div>
</div>

<div class="fs-archive__body">
  <div class="container--narrow">

    <?php if (have_posts()): ?>
      <p style="font-size:.875rem;color:var(--muted);margin-bottom:24px;">
        <?php echo $wp_query->found_posts; ?> resultado(s) encontrado(s)
      </p>
      <div style="border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden;">
        <?php while (have_posts()) : the_post();
          $type = get_post_type();
          $type_labels = ['post' => '📰 Artigo', 'golpe' => '🚨 Alerta', 'glossario' => '📖 Glossário'];
          $type_label  = $type_labels[$type] ?? $type;
        ?>
          <article class="fs-search-result">
            <div class="fs-search-result__meta">
              <span class="fs-badge fs-badge--blue"><?php echo $type_label; ?></span>
              <?php if ($type === 'golpe'):
                $nivel = get_post_meta(get_the_ID(), 'nivel_risco', true) ?: 'alto';
                echo fs_badge_risco($nivel);
              endif; ?>
              <span style="font-size:.72rem;color:var(--subtle);"><?php the_date('d/m/Y'); ?></span>
            </div>
            <h2 class="fs-search-result__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p class="fs-search-result__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
          </article>
        <?php endwhile; ?>
      </div>
      <div class="fs-pagination"><?php the_posts_pagination(['prev_text' => '← Anterior', 'next_text' => 'Próximos →']); ?></div>
    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhum resultado para "<?php the_search_query(); ?>".</p>
        <p style="font-size:.875rem;margin-top:8px;">Tente termos como "pix", "whatsapp", "phishing" ou "central antifraude".</p>
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--navy" style="margin-top:20px;">Ver todos os alertas</a>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>
