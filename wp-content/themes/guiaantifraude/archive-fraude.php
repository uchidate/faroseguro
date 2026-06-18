<?php get_header(); ?>

<?php
$tipo_atual   = is_tax('tipo_fraude') ? get_queried_object() : null;
$tipos_fraude = get_terms(['taxonomy' => 'tipo_fraude', 'hide_empty' => true]);
$title        = $tipo_atual ? $tipo_atual->name : 'Central de Fraudes';
$desc         = $tipo_atual ? $tipo_atual->description : 'Fraudes bancárias onde o acesso não autorizado ocorre sem ação direta da vítima — cartão clonado, conta invadida, SIM swap, vazamento de dados.';

$total_fraudes = wp_count_posts('fraude')->publish;
fs_archive_hero('Fraudes', $title, $desc, 'fraude', (int) $total_fraudes, 'fraudes catalogadas');

fs_archive_filter_strip([
    [
        'label'      => 'Tipo',
        'all_url'    => get_post_type_archive_link('fraude'),
        'all_active' => !$tipo_atual,
        'terms'      => $tipos_fraude ?: [],
        'current_id' => $tipo_atual ? $tipo_atual->term_id : null,
    ],
], 'fraude');
?>

<div class="fs-archive__body">
  <div class="container">
    <?php if (have_posts()): ?>
      <div class="fs-grid fs-grid--3">
        <?php while (have_posts()) : the_post(); fs_fraude_card(get_post(), true); endwhile; ?>
      </div>
      <div class="fs-pagination"><?php the_posts_pagination(['prev_text' => 'Anterior', 'next_text' => 'Próximas', 'mid_size' => 2]); ?></div>
    <?php else: ?>
      <div class="fs-empty">
        <p>Nenhuma fraude encontrada nesta categoria.</p>
        <a href="<?php echo get_post_type_archive_link('fraude'); ?>" class="fs-btn fs-btn--purple">Ver todas as fraudes</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
