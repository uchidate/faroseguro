<?php get_header(); ?>

<?php while (have_posts()) : the_post();
  $leitura    = fs_leitura();
  $atualizado = get_post_meta(get_the_ID(), 'atualizado_em', true);
  $destaque   = get_post_meta(get_the_ID(), 'artigo_destaque', true) === '1';
  $content    = get_the_content();
  $toc        = fs_build_toc($content);
?>

<main class="fs-single">

  <!-- Hero editorial -->
  <div class="fs-single__hero">
    <div class="container">
      <div class="fs-single__meta-top">
        <?php
        $cats = get_the_category();
        if ($cats): foreach ($cats as $c): ?>
          <a href="<?php echo get_category_link($c); ?>" class="fs-cat fs-cat--default"><?php echo esc_html($c->name); ?></a>
        <?php endforeach; endif; ?>
        <?php if ($destaque): ?>
          <span class="fs-badge fs-badge--novo">Destaque editorial</span>
        <?php endif; ?>
      </div>
      <h1 class="fs-single__title"><?php the_title(); ?></h1>
      <?php if (has_excerpt()): ?>
        <p class="fs-single__lead"><?php the_excerpt(); ?></p>
      <?php endif; ?>
      <div class="fs-single__byline">
        <span>Publicado em <time datetime="<?php the_date('c'); ?>"><?php the_date('d \d\e F \d\e Y'); ?></time></span>
        <?php if ($atualizado): ?>
          <span>Atualizado em <?php echo esc_html(date('d/m/Y', strtotime($atualizado))); ?></span>
        <?php endif; ?>
        <span><?php echo esc_html($leitura); ?></span>
        <span>Por <strong>Equipe Guia Antifraude</strong></span>
      </div>
    </div>
  </div>

  <?php if (has_post_thumbnail()): ?>
    <div class="fs-single__cover">
      <div class="container--narrow">
        <?php the_post_thumbnail('full', ['class' => 'fs-single__cover-img', 'loading' => 'eager']); ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="fs-single__body">
    <div class="container">
      <div class="fs-grid--aside">

        <!-- Corpo do artigo -->
        <div>

          <!-- Compartilhar (topo) -->
          <div class="fs-share">
            <span class="fs-share__label">Compartilhar</span>
            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' — ' . get_permalink()); ?>"
               target="_blank" rel="noopener" class="fs-share__btn fs-share__btn--wa">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.025.507 3.934 1.395 5.605L0 24l6.585-1.31A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.001-1.372l-.36-.214-3.713.737.788-3.612-.234-.372A9.818 9.818 0 1112 21.818z"/></svg>
              WhatsApp
            </a>
            <button class="fs-share__btn fs-share__btn--copy" data-share-copy="<?php echo esc_attr(get_permalink()); ?>">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
              Copiar link
            </button>
          </div>

          <!-- Conteúdo -->
          <div class="fs-prose" id="fs-article-content">
            <?php the_content(); ?>
          </div>

          <!-- Tags -->
          <?php $tags = get_the_tags(); if ($tags): ?>
            <div class="fs-single__tags">
              <?php foreach ($tags as $t): ?>
                <a href="<?php echo get_tag_link($t); ?>" class="fs-tag">#<?php echo esc_html($t->name); ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Fonte -->
          <p style="font-size:.75rem;color:var(--subtle);margin-top:24px;">
            Conteúdo verificado pela equipe editorial. Fontes: Banco Central do Brasil, Febraban, Senacon.
          </p>

          <!-- Nav prev/next -->
          <nav class="fs-post-nav" aria-label="Navegar entre artigos">
            <div class="fs-post-nav__item">
              <?php $prev = get_previous_post(); if ($prev): ?>
                <span class="fs-post-nav__label">Anterior</span>
                <div class="fs-post-nav__title"><a href="<?php echo get_permalink($prev); ?>"><?php echo esc_html(fs_editorial_text($prev->post_title)); ?></a></div>
              <?php endif; ?>
            </div>
            <div class="fs-post-nav__item fs-post-nav__item--next">
              <?php $next = get_next_post(); if ($next): ?>
                <span class="fs-post-nav__label">Próximo</span>
                <div class="fs-post-nav__title"><a href="<?php echo get_permalink($next); ?>"><?php echo esc_html(fs_editorial_text($next->post_title)); ?></a></div>
              <?php endif; ?>
            </div>
          </nav>

        </div>

        <!-- Sidebar sticky -->
        <aside class="fs-sidebar">

          <!-- TOC -->
          <?php if (!empty($toc)): ?>
          <div class="fs-toc" id="fs-toc">
            <p class="fs-toc__title">Neste artigo</p>
            <ol class="fs-toc__list">
              <?php foreach ($toc as $i => $item): ?>
                <li class="fs-toc__item" data-toc-index="<?php echo $i; ?>">
                  <a href="#<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['text']); ?></a>
                </li>
              <?php endforeach; ?>
            </ol>
          </div>
          <?php endif; ?>

          <!-- CTA -->
          <div class="fs-sidebar-widget fs-sidebar-widget--cta">
            <h3>Receber alertas</h3>
            <p>Identificamos novos golpes em até 24h. Denuncie se você viu algo suspeito.</p>
            <a href="/contato/" class="fs-btn fs-btn--primary">Denunciar Golpe</a>
          </div>

          <div class="fs-sidebar-widget fs-sidebar-widget--official">
            <div class="fs-sidebar-widget__head">
              <p class="fs-sidebar-widget__title">Canais oficiais</p>
            </div>
            <div class="fs-sidebar-widget__body">
              <?php fs_official_channels('fs-official-channels--compact'); ?>
            </div>
          </div>

          <!-- Alertas recentes -->
          <div class="fs-sidebar-widget">
            <div class="fs-sidebar-widget__head">
              <p class="fs-sidebar-widget__title">Alertas recentes</p>
            </div>
            <div class="fs-sidebar-widget__body">
              <?php
              $golpes = get_posts(['post_type' => 'golpe', 'numberposts' => 5, 'post_status' => 'publish']);
              foreach ($golpes as $g):
                $nivel = get_post_meta($g->ID, 'nivel_risco', true) ?: 'alto';
              ?>
                <div class="fs-sidebar-item">
                  <div class="fs-sidebar-item__text">
                    <div class="fs-sidebar-item__meta"><?php echo fs_badge_risco($nivel); ?></div>
                    <div class="fs-sidebar-item__title"><a href="<?php echo get_permalink($g); ?>"><?php echo esc_html(fs_editorial_text($g->post_title)); ?></a></div>
                  </div>
                </div>
              <?php endforeach; wp_reset_postdata(); ?>
            </div>
          </div>

          <!-- Categorias -->
          <div class="fs-sidebar-widget">
            <div class="fs-sidebar-widget__head">
              <p class="fs-sidebar-widget__title">Categorias</p>
            </div>
            <div class="fs-sidebar-widget__body">
              <ul class="fs-sidebar-categories">
                <?php wp_list_categories(['show_count' => true, 'title_li' => '', 'orderby' => 'count', 'order' => 'DESC', 'number' => 7]); ?>
              </ul>
            </div>
          </div>

          <?php fs_ad('sidebar'); ?>

        </aside>

      </div>
    </div>
  </div>

  <!-- Artigos relacionados -->
  <?php
  $cats_ids = wp_get_post_categories(get_the_ID());
  $related  = $cats_ids ? get_posts([
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => [get_the_ID()],
    'category__in'   => $cats_ids,
  ]) : [];
  if ($related): ?>
    <section class="fs-related">
      <div class="container">
        <h2 class="fs-related__title">Leia também</h2>
        <div class="fs-grid fs-grid--3">
          <?php foreach ($related as $p): fs_artigo_card($p); endforeach; wp_reset_postdata(); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

</main>

<?php endwhile; ?>
<?php get_footer(); ?>
