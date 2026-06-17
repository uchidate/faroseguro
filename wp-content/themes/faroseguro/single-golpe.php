<?php get_header(); ?>

<main id="main" class="site-main">
<?php while (have_posts()) : the_post();
  $nivel = get_post_meta(get_the_ID(), 'nivel_risco', true);
  $badge_map = [
    'alto'  => ['color' => '#ef4444', 'bg' => '#fee2e2', 'label' => '🚨 Risco Alto'],
    'medio' => ['color' => '#f59e0b', 'bg' => '#fef3c7', 'label' => '⚠️ Risco Médio'],
    'baixo' => ['color' => '#3b82f6', 'bg' => '#dbeafe', 'label' => 'ℹ️ Risco Baixo'],
  ];
  $badge = $badge_map[$nivel] ?? $badge_map['alto'];
  $tipos = get_the_terms(get_the_ID(), 'tipo_golpe');
?>

  <!-- Hero do post -->
  <div style="background:#1a2e4a;padding:64px 20px 48px">
    <div class="container" style="max-width:780px">
      <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;align-items:center">
        <?php if ($tipos && !is_wp_error($tipos)) foreach ($tipos as $tipo) : ?>
          <a href="<?php echo get_term_link($tipo); ?>"
             style="display:inline-block;padding:5px 12px;border-radius:99px;font-size:12px;font-weight:700;background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.85);text-decoration:none">
            <?php echo esc_html($tipo->name); ?>
          </a>
        <?php endforeach; ?>
        <span style="display:inline-block;padding:5px 12px;border-radius:99px;font-size:12px;font-weight:700;background:<?php echo $badge['bg']; ?>;color:<?php echo $badge['color']; ?>">
          <?php echo $badge['label']; ?>
        </span>
      </div>

      <h1 style="color:#fff;font-size:clamp(1.5rem,3vw,2.4rem);font-weight:800;line-height:1.2;margin:0 0 20px">
        <?php the_title(); ?>
      </h1>

      <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:rgba(255,255,255,0.6)">
        <span>📅 <?php echo get_the_date(); ?></span>
        <span>✍️ <?php the_author(); ?></span>
      </div>
    </div>
  </div>

  <!-- Alerta de destaque -->
  <div style="background:<?php echo $badge['bg']; ?>;border-left:5px solid <?php echo $badge['color']; ?>;padding:16px 20px">
    <div class="container" style="max-width:780px">
      <strong style="color:<?php echo $badge['color']; ?>"><?php echo $badge['label']; ?>:</strong>
      <span style="color:#374151;margin-left:8px"><?php echo get_the_excerpt(); ?></span>
    </div>
  </div>

  <!-- Conteúdo -->
  <div style="background:#fff;padding:56px 20px 80px">
    <div class="container" style="max-width:780px">

      <?php if (has_post_thumbnail()) : ?>
        <div style="margin-bottom:40px;border-radius:12px;overflow:hidden">
          <?php the_post_thumbnail('fs-card', ['style' => 'width:100%;height:auto']); ?>
        </div>
      <?php endif; ?>

      <div class="entry-content" style="font-size:17px;line-height:1.85;color:#374151">
        <?php the_content(); ?>
      </div>

      <!-- Navegação entre posts -->
      <div style="margin-top:56px;padding-top:32px;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;gap:16px">
        <div><?php previous_post_link('<span style="font-size:13px;color:#94a3b8;display:block;margin-bottom:4px">← Anterior</span><strong style="color:#1a2e4a">%link</strong>', '%title', true, '', 'golpe'); ?></div>
        <div style="text-align:right"><?php next_post_link('<span style="font-size:13px;color:#94a3b8;display:block;margin-bottom:4px">Próximo →</span><strong style="color:#1a2e4a">%link</strong>', '%title', true, '', 'golpe'); ?></div>
      </div>

      <!-- Voltar ao arquivo -->
      <div style="margin-top:32px;text-align:center">
        <a href="<?php echo get_post_type_archive_link('golpe'); ?>"
           style="display:inline-block;padding:12px 28px;background:#1a2e4a;color:#fff;border-radius:8px;font-weight:600;font-size:14px;text-decoration:none">
          ← Ver todos os alertas
        </a>
      </div>

    </div>
  </div>

<?php endwhile; ?>
</main>

<?php get_footer(); ?>
