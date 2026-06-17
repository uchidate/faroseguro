<?php get_header(); ?>

<?php
$tipo_atual  = is_tax('tipo_golpe')  ? get_queried_object() : null;
$canal_atual = is_tax('canal_golpe') ? get_queried_object() : null;
$tipos       = get_terms(['taxonomy' => 'tipo_golpe',  'hide_empty' => true]);
$canais      = get_terms(['taxonomy' => 'canal_golpe', 'hide_empty' => true]);
$hero_title  = $tipo_atual ? $tipo_atual->name : ($canal_atual ? $canal_atual->name : 'Central de Alertas');
$hero_desc   = $tipo_atual ? $tipo_atual->description : ($canal_atual ? $canal_atual->description : 'Todos os golpes e fraudes bancárias identificados pela equipe Faro Seguro. Atualizações em até 24h após identificação.');
?>

<div class="fs-archive__hero fs-archive__hero--dark">
  <div class="container">
    <span class="fs-eyebrow">🚨 Alertas</span>
    <h1 class="fs-archive__title"><?php echo esc_html($hero_title); ?></h1>
    <p class="fs-archive__desc"><?php echo esc_html($hero_desc); ?></p>
  </div>
</div>

<!-- Filtros -->
<div class="fs-filter-strip">
  <div class="fs-filter-strip__inner">
    <div class="fs-filter-group">
      <span class="fs-filter-group__label">Tipo</span>
      <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-filter-pill <?php echo (!$tipo_atual && !$canal_atual) ? 'is-active' : ''; ?>">Todos</a>
      <?php if ($tipos && !is_wp_error($tipos)): foreach ($tipos as $t): ?>
        <a href="<?php echo get_term_link($t); ?>" class="fs-filter-pill <?php echo ($tipo_atual && $tipo_atual->term_id === $t->term_id) ? 'is-active' : ''; ?>">
          <?php echo esc_html($t->name); ?> <span><?php echo $t->count; ?></span>
        </a>
      <?php endforeach; endif; ?>
    </div>
    <?php if ($canais && !is_wp_error($canais)): ?>
    <div class="fs-filter-group">
      <span class="fs-filter-group__label">Canal</span>
      <?php foreach ($canais as $c): ?>
        <a href="<?php echo get_term_link($c); ?>" class="fs-filter-pill <?php echo ($canal_atual && $canal_atual->term_id === $c->term_id) ? 'is-active' : ''; ?>">
          <?php echo esc_html($c->name); ?> <span><?php echo $c->count; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="fs-archive__body">
  <div class="container">

    <?php if (have_posts()): ?>
      <div class="fs-grid fs-grid--3">
        <?php while (have_posts()) : the_post(); fs_golpe_card(get_post(), true); endwhile; ?>
      </div>
      <div class="fs-pagination"><?php the_posts_pagination(['prev_text' => '← Anterior', 'next_text' => 'Próximos →', 'mid_size' => 2]); ?></div>
    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhum alerta encontrado nesta categoria.</p>
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--navy">Ver todos os alertas</a>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>
