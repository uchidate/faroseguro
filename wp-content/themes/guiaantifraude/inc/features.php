<?php
/* Features complementares: views, share, relacionados, checklist */

/* ── Post Views ─────────────────────────────── */
function fs_record_view(int $post_id): void {
    if (is_user_logged_in() && current_user_can('edit_posts')) return;
    $count = (int) get_post_meta($post_id, 'fs_views', true);
    update_post_meta($post_id, 'fs_views', $count + 1);
}
function fs_get_views(int $post_id): int {
    return (int) get_post_meta($post_id, 'fs_views', true);
}
function fs_views_label(int $post_id): string {
    $n = fs_get_views($post_id);
    if ($n < 10)  return '';
    if ($n < 1000) return number_format($n, 0, ',', '.') . ' visualizações';
    return number_format($n / 1000, 1, ',', '.') . 'k visualizações';
}

add_action('wp', function () {
    if (is_singular(['golpe', 'fraude', 'post'])) {
        fs_record_view(get_the_ID());
    }
});

/* ── Checklist interativo "O que fazer agora" ─ */
function fs_checklist_interativo(string $content, int $post_id): void {
    $items = array_filter(array_map('trim', explode("\n", $content)));
    if (!$items) return;
    $key = 'fs-checklist-' . $post_id;
    ?>
    <div class="fs-action-checklist" data-key="<?php echo esc_attr($key); ?>">
      <div class="fs-action-checklist__header">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        <h2>Fui vítima — o que fazer agora</h2>
        <span class="fs-action-checklist__progress">0 / <?php echo count($items); ?></span>
      </div>
      <ul class="fs-action-checklist__list">
        <?php foreach (array_values($items) as $i => $item): ?>
        <li class="fs-action-checklist__item" data-index="<?php echo $i; ?>">
          <button class="fs-action-checklist__check" aria-label="Marcar como feito" aria-pressed="false">
            <svg class="fs-action-checklist__check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
          </button>
          <span><?php echo esc_html($item); ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
      <div class="fs-action-checklist__done" aria-live="polite">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Checklist concluído! Guarde os protocolos de atendimento recebidos.
      </div>
      <div class="fs-emergency">
        <a href="https://www.bcb.gov.br/meubc/registrar_reclamacao" target="_blank" rel="noopener" class="fs-emergency__link">Bacen</a>
        <a href="https://www.consumidor.gov.br" target="_blank" rel="noopener" class="fs-emergency__link">Consumidor.gov</a>
        <a href="/contato/" class="fs-emergency__link">Denunciar ao portal</a>
      </div>
    </div>
    <?php
}

/* ── Relacionados ───────────────────────────── */
function fs_related_posts(int $post_id, string $post_type, string $taxonomy): void {
    $terms = get_the_terms($post_id, $taxonomy);
    $term_ids = ($terms && !is_wp_error($terms)) ? wp_list_pluck($terms, 'term_id') : [];

    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => 3,
        'post__not_in'   => [$post_id],
        'orderby'        => 'rand',
    ];
    if ($term_ids) {
        $args['tax_query'] = [['taxonomy' => $taxonomy, 'terms' => $term_ids]];
    }
    $posts = get_posts($args);
    if (!$posts) return;
    $label = $post_type === 'golpe' ? 'Golpes Relacionados' : 'Fraudes Relacionadas';
    $fn    = $post_type === 'golpe' ? 'fs_golpe_card' : 'fs_fraude_card';
    ?>
    <section class="fs-related fs-related--inline">
      <div class="fs-related__head">
        <h2 class="fs-related__title"><?php echo esc_html($label); ?></h2>
        <a href="<?php echo get_post_type_archive_link($post_type); ?>" class="fs-related__all">Ver todos</a>
      </div>
      <div class="fs-grid fs-grid--3">
        <?php foreach ($posts as $p): $fn($p, true); endforeach; wp_reset_postdata(); ?>
      </div>
    </section>
    <?php
}

/* ── Web Share ──────────────────────────────── */
function fs_share_bar(int $post_id): void {
    $url   = get_permalink($post_id);
    $title = get_the_title($post_id);
    $wa    = 'https://wa.me/?text=' . rawurlencode($title . ' — ' . $url);
    ?>
    <div class="fs-share-bar">
      <span class="fs-share-bar__label">Compartilhar este alerta</span>
      <div class="fs-share-bar__buttons">
        <button class="fs-share-bar__btn fs-share-bar__btn--native" data-share-title="<?php echo esc_attr($title); ?>" data-share-url="<?php echo esc_attr($url); ?>" aria-label="Compartilhar">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
          Compartilhar
        </button>
        <a class="fs-share-bar__btn fs-share-bar__btn--wa" href="<?php echo esc_attr($wa); ?>" target="_blank" rel="noopener" aria-label="Compartilhar no WhatsApp">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
          WhatsApp
        </a>
        <button class="fs-share-bar__btn fs-share-bar__btn--copy" data-share-copy="<?php echo esc_attr($url); ?>" aria-label="Copiar link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          Copiar link
        </button>
      </div>
    </div>
    <?php
}

/* ── AJAX filter para archives ──────────────── */
function fs_ajax_filter_posts() {
    $post_type = sanitize_key($_POST['post_type'] ?? 'golpe');
    $taxonomy  = sanitize_key($_POST['taxonomy']  ?? '');
    $term_id   = (int) ($_POST['term_id'] ?? 0);
    $paged     = max(1, (int) ($_POST['paged'] ?? 1));

    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => 12,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    if ($taxonomy && $term_id) {
        $args['tax_query'] = [['taxonomy' => $taxonomy, 'terms' => $term_id]];
    }

    $q = new WP_Query($args);
    ob_start();
    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            if ($post_type === 'golpe') fs_golpe_card(get_post(), true);
            else fs_fraude_card(get_post(), true);
        }
    } else {
        echo '<p class="fs-empty-msg">Nenhum resultado encontrado.</p>';
    }
    wp_reset_postdata();
    $html  = ob_get_clean();
    $total = $q->found_posts;
    $pages = $q->max_num_pages;

    wp_send_json_success(['html' => $html, 'total' => $total, 'pages' => $pages, 'current' => $paged]);
}
add_action('wp_ajax_fs_filter_posts',        'fs_ajax_filter_posts');
add_action('wp_ajax_nopriv_fs_filter_posts', 'fs_ajax_filter_posts');
