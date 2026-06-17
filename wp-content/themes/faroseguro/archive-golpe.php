<?php get_header(); ?>

<main id="main" class="site-main">

  <!-- Hero do arquivo -->
  <div style="background:#1a2e4a;padding:64px 20px 48px;text-align:center">
    <div class="container">
      <p style="color:#f97316;font-size:13px;font-weight:700;letter-spacing:3px;text-transform:uppercase;margin:0 0 12px">Alertas atualizados</p>
      <h1 style="color:#fff;font-size:clamp(1.8rem,3vw,2.8rem);font-weight:800;margin:0 0 16px">Golpes e Fraudes Bancárias</h1>
      <p style="color:rgba(255,255,255,0.72);font-size:17px;max-width:580px;margin:0 auto;line-height:1.75">
        Modus operandi identificados e analisados para você não cair na mesma armadilha.
      </p>
    </div>
  </div>

  <!-- Filtro por categoria -->
  <div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:16px 20px">
    <div class="container" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
      <span style="font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:1px">Filtrar:</span>
      <a href="<?php echo get_post_type_archive_link('golpe'); ?>"
         style="display:inline-block;padding:6px 14px;border-radius:99px;font-size:13px;font-weight:600;background:<?php echo !is_tax() ? '#1a2e4a' : '#e2e8f0'; ?>;color:<?php echo !is_tax() ? '#fff' : '#64748b'; ?>;text-decoration:none">
        Todos
      </a>
      <?php
      $tipos = get_terms(['taxonomy' => 'tipo_golpe', 'hide_empty' => true]);
      foreach ($tipos as $tipo) :
        $ativo = is_tax('tipo_golpe', $tipo->slug);
      ?>
      <a href="<?php echo get_term_link($tipo); ?>"
         style="display:inline-block;padding:6px 14px;border-radius:99px;font-size:13px;font-weight:600;background:<?php echo $ativo ? '#1a2e4a' : '#e2e8f0'; ?>;color:<?php echo $ativo ? '#fff' : '#64748b'; ?>;text-decoration:none">
        <?php echo esc_html($tipo->name); ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Grid de alertas -->
  <div style="background:#f8fafc;padding:48px 20px 80px">
    <div class="container">
      <?php if (have_posts()) : ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px">
          <?php while (have_posts()) : the_post();
            $nivel = get_post_meta(get_the_ID(), 'nivel_risco', true);
            $badge_map = [
              'alto'  => ['color' => '#ef4444', 'label' => '🚨 Risco Alto'],
              'medio' => ['color' => '#f59e0b', 'label' => '⚠️ Risco Médio'],
              'baixo' => ['color' => '#3b82f6', 'label' => 'ℹ️ Risco Baixo'],
            ];
            $badge = $badge_map[$nivel] ?? $badge_map['alto'];
          ?>
          <article style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.07);border-top:3px solid <?php echo $badge['color']; ?>;display:flex;flex-direction:column">
            <?php if (has_post_thumbnail()) : ?>
              <a href="<?php the_permalink(); ?>" style="display:block;overflow:hidden;height:200px">
                <?php the_post_thumbnail('fs-card', ['style' => 'width:100%;height:100%;object-fit:cover']); ?>
              </a>
            <?php endif; ?>
            <div style="padding:24px 24px 28px;flex:1;display:flex;flex-direction:column">
              <span style="display:inline-block;background:<?php echo $badge['color']; ?>1a;color:<?php echo $badge['color']; ?>;font-size:12px;font-weight:700;padding:4px 10px;border-radius:99px;margin-bottom:14px">
                <?php echo $badge['label']; ?>
              </span>
              <h2 style="font-size:17px;font-weight:700;color:#1a2e4a;line-height:1.4;margin:0 0 10px">
                <a href="<?php the_permalink(); ?>" style="color:#1a2e4a;text-decoration:none"><?php the_title(); ?></a>
              </h2>
              <p style="color:#64748b;font-size:14px;line-height:1.7;margin:0 0 auto;flex:1"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
              <div style="margin-top:20px;display:flex;justify-content:space-between;align-items:center">
                <span style="font-size:12px;color:#94a3b8"><?php echo get_the_date(); ?></span>
                <a href="<?php the_permalink(); ?>" style="font-size:13px;font-weight:600;color:#f97316;text-decoration:none">Ler alerta →</a>
              </div>
            </div>
          </article>
          <?php endwhile; ?>
        </div>

        <div style="margin-top:48px">
          <?php the_posts_pagination(['mid_size' => 2]); ?>
        </div>

      <?php else : ?>
        <p style="text-align:center;color:#64748b;font-size:17px">Nenhum alerta encontrado nesta categoria.</p>
      <?php endif; ?>
    </div>
  </div>

</main>

<?php get_footer(); ?>
