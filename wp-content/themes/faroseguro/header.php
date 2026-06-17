<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Barra de alertas urgentes (ticker) -->
<?php
$latest_golpes = get_posts(['post_type' => 'golpe', 'numberposts' => 6, 'post_status' => 'publish',
  'meta_query' => [['key' => 'nivel_risco', 'value' => 'alto']]]);
if ($latest_golpes): ?>
<div class="fs-ticker" role="marquee" aria-label="Alertas recentes">
  <span class="fs-ticker__label">ALERTAS</span>
  <div class="fs-ticker__track">
    <div class="fs-ticker__inner" id="fs-ticker">
      <?php foreach ($latest_golpes as $g): ?>
        <a href="<?= get_permalink($g) ?>" class="fs-ticker__item">
          <?= esc_html($g->post_title) ?>
        </a>
        <span class="fs-ticker__sep" aria-hidden="true">·</span>
      <?php endforeach; wp_reset_postdata(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Header principal -->
<header class="fs-header" id="fs-header">
  <div class="fs-header__top">
    <div class="fs-header__top-inner">

      <a class="fs-logo" href="<?php echo esc_url(home_url('/')); ?>">
        <svg class="fs-logo__icon" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
          <path d="M16 2L4 8v8c0 6.63 5.12 12.84 12 14.4C22.88 28.84 28 22.63 28 16V8L16 2z" fill="#f97316"/>
          <path d="M16 6.5L8 10.5V16c0 4.17 3.22 8.07 8 9.1 4.78-1.03 8-4.93 8-9.1v-5.5L16 6.5z" fill="#0f1f36"/>
          <circle cx="16" cy="16" r="3.5" fill="#f97316"/>
          <path d="M16 12.5v7M12.5 16h7" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <div class="fs-logo__text">
          <span class="fs-logo__name">Faro Seguro</span>
          <span class="fs-logo__tagline">Alertas de Fraudes e Golpes</span>
        </div>
      </a>

      <div class="fs-header__center">
        <nav class="fs-nav" id="fs-nav" aria-label="Menu principal">
          <?php wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'items_wrap'     => '<ul class="fs-nav__list">%3$s</ul>',
            'fallback_cb'    => function () {
              echo '<ul class="fs-nav__list">
                <li><a href="/">Home</a></li>
                <li><a href="/golpes/">Alertas</a></li>
                <li><a href="/artigos/" class="current-menu-item">Artigos</a></li>
                <li><a href="/glossario/">Glossário</a></li>
                <li><a href="/sobre-nos/">Sobre</a></li>
              </ul>';
            },
          ]); ?>
        </nav>
      </div>

      <div class="fs-header__right">
        <button class="fs-search-toggle" id="fs-search-toggle" aria-label="Buscar" aria-expanded="false">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
        </button>
        <a href="/contato/" class="fs-header__cta">
          Denunciar Golpe
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <button class="fs-hamburger" id="fs-hamburger" aria-label="Menu" aria-expanded="false" aria-controls="fs-nav">
          <span></span><span></span><span></span>
        </button>
      </div>

    </div>
  </div>

  <!-- Barra de busca expansível -->
  <div class="fs-search-bar" id="fs-search-bar" aria-hidden="true">
    <div class="fs-header__top-inner">
      <form class="fs-search-bar__form" role="search" method="get" action="<?php echo home_url('/'); ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="search" name="s" id="fs-search-input"
               placeholder="Buscar golpes, artigos, termos…"
               value="<?php echo get_search_query(); ?>"
               autocomplete="off">
        <button type="button" class="fs-search-bar__close" id="fs-search-close" aria-label="Fechar busca">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </form>
    </div>
  </div>
</header>

<!-- Reading progress bar -->
<?php if (is_singular(['post', 'golpe'])): ?>
<div class="fs-progress" id="fs-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="Progresso de leitura">
  <div class="fs-progress__bar" id="fs-progress-bar"></div>
</div>
<?php endif; ?>

<!-- Breadcrumbs -->
<?php if (!is_front_page() && !is_home()): ?>
<nav class="fs-breadcrumbs" aria-label="Breadcrumb">
  <div class="container">
    <ol class="fs-breadcrumbs__list">
      <li><a href="<?php echo home_url('/'); ?>">Início</a></li>
      <?php if (is_singular('golpe')): ?>
        <li><a href="<?php echo get_post_type_archive_link('golpe'); ?>">Alertas</a></li>
        <?php $tipos = get_the_terms(get_the_ID(), 'tipo_golpe');
        if ($tipos && !is_wp_error($tipos)): ?>
          <li><a href="<?php echo get_term_link($tipos[0]); ?>"><?php echo esc_html($tipos[0]->name); ?></a></li>
        <?php endif; ?>
        <li aria-current="page"><?php echo wp_trim_words(get_the_title(), 6); ?></li>
      <?php elseif (is_singular('post')): ?>
        <li><a href="<?php echo home_url('/artigos/'); ?>">Artigos</a></li>
        <?php $cats = get_the_category();
        if ($cats): ?>
          <li><a href="<?php echo get_category_link($cats[0]); ?>"><?php echo esc_html($cats[0]->name); ?></a></li>
        <?php endif; ?>
        <li aria-current="page"><?php echo wp_trim_words(get_the_title(), 6); ?></li>
      <?php elseif (is_post_type_archive('golpe') || is_tax('tipo_golpe') || is_tax('canal_golpe')): ?>
        <li><a href="<?php echo get_post_type_archive_link('golpe'); ?>">Alertas</a></li>
        <?php if (is_tax()): ?><li aria-current="page"><?php single_term_title(); ?></li><?php endif; ?>
      <?php elseif (is_category()): ?>
        <li>Artigos</li>
        <li aria-current="page"><?php single_cat_title(); ?></li>
      <?php elseif (is_search()): ?>
        <li aria-current="page">Busca: "<?php the_search_query(); ?>"</li>
      <?php elseif (is_page()): ?>
        <li aria-current="page"><?php the_title(); ?></li>
      <?php endif; ?>
    </ol>
  </div>
</nav>
<?php endif; ?>

<div class="fs-nav-overlay" id="fs-nav-overlay"></div>
