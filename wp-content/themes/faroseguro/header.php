<!doctype html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?> >
    <?php wp_body_open(); ?>
    <header class="site-header">
      <div class="container header-inner">
        <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
        <nav class="nav" id="site-nav">
          <?php
          if (has_nav_menu('primary')) {
            wp_nav_menu(array(
              'theme_location' => 'primary',
              'container' => false,
              'menu_class' => 'nav-menu',
              'fallback_cb' => 'faro_seguro_menu_fallback',
            ));
          } else {
            faro_seguro_menu_fallback();
          }
          ?>
        </nav>
        <button class="nav-toggle" id="nav-toggle" aria-label="Abrir menu" aria-expanded="false">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </header>
