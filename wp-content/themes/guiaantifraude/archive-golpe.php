<?php get_header(); ?>

<?php
$tipo_atual  = is_tax('tipo_golpe')  ? get_queried_object() : null;
$canal_atual = is_tax('canal_golpe') ? get_queried_object() : null;
$tipos       = get_terms(['taxonomy' => 'tipo_golpe',  'hide_empty' => true]);
$canais      = get_terms(['taxonomy' => 'canal_golpe', 'hide_empty' => true]);
$hero_title  = $tipo_atual ? $tipo_atual->name : ($canal_atual ? $canal_atual->name : 'Catálogo de Golpes');
$hero_desc   = $tipo_atual ? $tipo_atual->description : ($canal_atual ? $canal_atual->description : 'Todos os golpes e fraudes bancárias identificados pela equipe Guia Antifraude. Atualizações em até 24h após identificação.');

fs_archive_hero('Golpes', $hero_title, $hero_desc, 'dark');

fs_archive_filter_strip([
    [
        'label'      => 'Tipo',
        'all_url'    => get_post_type_archive_link('golpe'),
        'all_active' => !$tipo_atual && !$canal_atual,
        'terms'      => $tipos ?: [],
        'current_id' => $tipo_atual ? $tipo_atual->term_id : null,
    ],
    [
        'label'      => 'Canal',
        'terms'      => $canais ?: [],
        'current_id' => $canal_atual ? $canal_atual->term_id : null,
    ],
]);
?>

<div class="fs-archive__body">
  <div class="container">
    <?php if (have_posts()): ?>
      <div class="fs-grid fs-grid--3">
        <?php while (have_posts()) : the_post(); fs_golpe_card(get_post(), true); endwhile; ?>
      </div>
      <div class="fs-pagination"><?php the_posts_pagination(['prev_text' => 'Anterior', 'next_text' => 'Próximos', 'mid_size' => 2]); ?></div>
    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhum golpe encontrado nesta categoria.</p>
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>" class="fs-btn fs-btn--navy">Ver todos os golpes</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
