<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="fs-header" id="fs-header">
  <div class="fs-header__inner">

    <a class="fs-header__logo" href="<?php echo esc_url(home_url('/')); ?>">
      <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
        <path d="M14 2L4 7v7c0 5.55 4.27 10.74 10 12 5.73-1.26 10-6.45 10-12V7L14 2z" fill="#f97316"/>
        <path d="M14 6L8 9.5v5c0 3.47 2.67 6.71 6 7.5 3.33-.79 6-4.03 6-7.5v-5L14 6z" fill="#1a2e4a"/>
        <circle cx="14" cy="14.5" r="3" fill="#f97316"/>
      </svg>
      <span><?php bloginfo('name'); ?></span>
    </a>

    <nav class="fs-nav" id="fs-nav" aria-label="Menu principal">
      <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'fs-nav__list',
        'fallback_cb'    => false,
        'items_wrap'     => '<ul class="fs-nav__list">%3$s</ul>',
      ]);
      ?>
      <a href="/contato/" class="fs-header__cta">Fale Conosco</a>
    </nav>

    <button class="fs-hamburger" id="fs-hamburger" aria-label="Abrir menu" aria-expanded="false" aria-controls="fs-nav">
      <span></span>
      <span></span>
      <span></span>
    </button>

  </div>
</header>

<div class="fs-nav-overlay" id="fs-nav-overlay"></div>
