<?php
/**
 * Guia Antifraude — SEO técnico e prontidão para AdSense.
 */

defined('ABSPATH') || exit;

function fs_seo_get_description(): string {
    if (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            $description = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags($post->post_content), 28);
            return trim((string) $description);
        }
    }

    if (is_tax() || is_category() || is_tag()) {
        $term_description = term_description();
        if ($term_description) {
            return trim(wp_strip_all_tags($term_description));
        }
    }

    if (is_search()) {
        return 'Resultados de busca no ' . FS_BRAND_NAME . ' para alertas, golpes, fraudes bancarias e artigos de prevencao.';
    }

    return get_bloginfo('description') ?: 'Alertas, guias e orientacoes verificadas sobre golpes, fraudes bancarias e seguranca financeira no Brasil.';
}

function fs_seo_current_url(): string {
    if (is_singular()) {
        return get_permalink();
    }

    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $link = get_term_link($term);
            return is_wp_error($link) ? home_url('/') : $link;
        }
    }

    if (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }
        $link = $post_type ? get_post_type_archive_link((string) $post_type) : '';
        return $link ?: home_url('/');
    }

    return home_url(add_query_arg([], $GLOBALS['wp']->request ?? ''));
}

function fs_seo_image_url(): string {
    if (is_singular() && has_post_thumbnail()) {
        return (string) get_the_post_thumbnail_url(null, 'fs-hero');
    }

    $custom_logo_id = get_theme_mod('custom_logo');
    return $custom_logo_id ? (string) wp_get_attachment_image_url($custom_logo_id, 'full') : '';
}

function fs_seo_plugin_active(): bool {
    return defined('WPSEO_VERSION')
        || defined('RANK_MATH_VERSION')
        || defined('AIOSEO_VERSION')
        || defined('SEOPRESS_VERSION');
}

add_action('wp_head', function () {
    if (is_admin() || fs_seo_plugin_active()) {
        return;
    }

    $description = wp_html_excerpt(fs_seo_get_description(), 160, '');
    $url = fs_seo_current_url();
    $title = wp_get_document_title();
    $image = fs_seo_image_url();
    $type = is_singular(['post', 'golpe', 'fraude']) ? 'article' : 'website';

    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr(get_locale()) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    if ($image) {
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    } else {
        echo '<meta name="twitter:card" content="summary">' . "\n";
    }
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
}, 4);

add_action('wp_head', function () {
    if (is_admin()) {
        return;
    }

    $graph = [
        [
            '@type' => 'Organization',
            '@id' => home_url('/#organization'),
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
        ],
        [
            '@type' => 'WebSite',
            '@id' => home_url('/#website'),
            'url' => home_url('/'),
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'publisher' => ['@id' => home_url('/#organization')],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}, 8);

function fs_seo_post_plain_text(WP_Post $post): string {
    $meta_fields = ['como_age', 'como_funciona', 'sinais_alerta', 'como_se_proteger', 'o_que_fazer'];
    $chunks = [$post->post_title, $post->post_excerpt, $post->post_content];

    foreach ($meta_fields as $field) {
        $value = get_post_meta($post->ID, $field, true);
        if ($value) {
            $chunks[] = $value;
        }
    }

    return trim(wp_strip_all_tags(implode(' ', $chunks)));
}

function fs_seo_word_count(WP_Post $post): int {
    return str_word_count(fs_seo_post_plain_text($post));
}

function fs_seo_score_post(WP_Post $post): array {
    $word_count = fs_seo_word_count($post);
    $checks = [
        'title' => [strlen(get_the_title($post)) >= 35 && strlen(get_the_title($post)) <= 70, 'Titulo entre 35 e 70 caracteres'],
        'excerpt' => [strlen(trim((string) $post->post_excerpt)) >= 110 && strlen(trim((string) $post->post_excerpt)) <= 170, 'Resumo/descricao entre 110 e 170 caracteres'],
        'content' => [$word_count >= 600, 'Conteudo com pelo menos 600 palavras'],
        'image' => [has_post_thumbnail($post), 'Imagem destacada definida'],
        'taxonomy' => [count(wp_get_object_terms($post->ID, get_object_taxonomies($post->post_type), ['fields' => 'ids'])) > 0, 'Categoria ou taxonomia preenchida'],
        'freshness' => [strtotime($post->post_modified_gmt ?: $post->post_date_gmt) >= strtotime('-18 months'), 'Conteudo revisado nos ultimos 18 meses'],
    ];

    $passed = count(array_filter($checks, fn($check) => (bool) $check[0]));

    return [
        'score' => (int) round(($passed / count($checks)) * 100),
        'passed' => $passed,
        'total' => count($checks),
        'word_count' => $word_count,
        'checks' => $checks,
    ];
}

function fs_readiness_check(string $id, string $label, bool $passed, string $detail, int $weight = 1, string $severity = 'required'): array {
    return compact('id', 'label', 'passed', 'detail', 'weight', 'severity');
}

function fs_adsense_readiness_report(): array {
    $published_counts = [
        'post' => (int) wp_count_posts('post')->publish,
        'golpe' => (int) wp_count_posts('golpe')->publish,
        'fraude' => (int) wp_count_posts('fraude')->publish,
        'glossario' => (int) wp_count_posts('glossario')->publish,
    ];
    $total_editorial = array_sum($published_counts);

    $sample = get_posts([
        'post_type' => ['post', 'golpe', 'fraude'],
        'post_status' => 'publish',
        'numberposts' => 50,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
    $long_content_count = 0;
    $seo_scores = [];
    foreach ($sample as $post) {
        $score = fs_seo_score_post($post);
        $seo_scores[] = $score['score'];
        if ($score['word_count'] >= 600) {
            $long_content_count++;
        }
    }
    $avg_seo = $seo_scores ? (int) round(array_sum($seo_scores) / count($seo_scores)) : 0;

    $required_pages = [
        'politica-de-privacidade' => 'Politica de Privacidade',
        'termos-de-uso' => 'Termos de Uso',
        'sobre-nos' => 'Sobre o Portal',
        'contato' => 'Contato/denuncia',
    ];
    $missing_pages = [];
    foreach ($required_pages as $slug => $label) {
        if (!get_page_by_path($slug)) {
            $missing_pages[] = $label;
        }
    }

    $publisher_ok = defined('FS_ADSENSE_ID') && preg_match('/^ca-pub-\d{16}$/', FS_ADSENSE_ID);
    $slots = $GLOBALS['fs_ad_slots'] ?? [];
    $filled_slots = count(array_filter($slots));
    $ads_txt_paths = [
        trailingslashit(ABSPATH) . 'ads.txt',
        dirname(get_stylesheet_directory(), 3) . '/ads.txt',
    ];
    $ads_txt_ok = false;
    foreach (array_unique($ads_txt_paths) as $ads_txt_path) {
        if (defined('FS_ADSENSE_ID') && file_exists($ads_txt_path)) {
            $ads_txt_ok = strpos((string) file_get_contents($ads_txt_path), str_replace('ca-', '', FS_ADSENSE_ID)) !== false;
            if ($ads_txt_ok) {
                break;
            }
        }
    }

    $menu_locations = get_nav_menu_locations();
    $has_primary_menu = !empty($menu_locations['primary']);
    $permalink_ok = (bool) get_option('permalink_structure');
    $sitemap_ok = function_exists('wp_sitemaps_get_server') && (bool) get_option('blog_public');

    $checks = [
        fs_readiness_check('indexing', 'Indexacao publica', (bool) get_option('blog_public'), 'Configuracao "Evitar mecanismos de busca" precisa estar desligada.', 3),
        fs_readiness_check('permalinks', 'URLs amigaveis', $permalink_ok, 'Permalinks devem usar estrutura legivel, nao apenas ?p=123.', 2),
        fs_readiness_check('sitemap', 'Sitemap do WordPress', $sitemap_ok, 'Sitemap XML depende de indexacao publica e WordPress 5.5+.', 2),
        fs_readiness_check('navigation', 'Menu principal configurado', $has_primary_menu, 'AdSense valoriza navegacao clara e facil de usar.', 2),
        fs_readiness_check('legal_pages', 'Paginas de confianca', empty($missing_pages), empty($missing_pages) ? 'Paginas essenciais encontradas.' : 'Faltando: ' . implode(', ', $missing_pages) . '.', 3),
        fs_readiness_check('content_volume', 'Volume editorial', $total_editorial >= 25, "{$total_editorial} conteudos publicados; alvo minimo recomendado: 25.", 3),
        fs_readiness_check('long_content', 'Conteudo substancial', $long_content_count >= 10, "{$long_content_count} dos ultimos " . count($sample) . " conteudos tem 600+ palavras.", 3),
        fs_readiness_check('avg_seo', 'SEO medio dos conteudos', $avg_seo >= 75, "Media SEO da amostra: {$avg_seo}%.", 2),
        fs_readiness_check('publisher', 'Publisher ID valido', (bool) $publisher_ok, defined('FS_ADSENSE_ID') ? FS_ADSENSE_ID : 'FS_ADSENSE_ID nao definido.', 3),
        fs_readiness_check('ads_txt', 'ads.txt publicado', (bool) $ads_txt_ok, $ads_txt_ok ? 'Arquivo ads.txt contem o publisher ID.' : 'Publique ads.txt na raiz publica do WordPress.', 3),
        fs_readiness_check('ad_slots', 'Slots de anuncio criados', $filled_slots > 0, "{$filled_slots} slots preenchidos em inc/ads.php.", 2),
        fs_readiness_check('prod_ads', 'Anuncios ativos em producao', defined('FS_ADS_ENABLED') && FS_ADS_ENABLED, 'FS_ADS_ENABLED fica falso quando WP_DEBUG esta ativo.', 1, 'warning'),
    ];

    $max = array_sum(array_map(fn($check) => $check['weight'], $checks));
    $earned = array_sum(array_map(fn($check) => $check['passed'] ? $check['weight'] : 0, $checks));

    return [
        'score' => $max ? (int) round(($earned / $max) * 100) : 0,
        'checks' => $checks,
        'counts' => $published_counts,
        'avg_seo' => $avg_seo,
    ];
}

add_action('admin_menu', function () {
    add_management_page(
        'SEO & AdSense',
        'SEO & AdSense',
        'manage_options',
        'fs-seo-adsense',
        'fs_render_seo_adsense_page'
    );
});

function fs_render_seo_adsense_page(): void {
    $report = fs_adsense_readiness_report();
    $status = $report['score'] >= 85 ? 'Pronto para revisao' : ($report['score'] >= 70 ? 'Quase pronto' : 'Precisa ajustes');
    ?>
    <div class="wrap fs-readiness">
      <h1>SEO & AdSense</h1>
      <p>Avaliacao baseada em sinais tecnicos do WordPress e nos criterios publicos do Google: conteudo original, navegacao clara, paginas confiaveis e configuracao correta de anuncios.</p>
      <div class="fs-readiness-score">
        <strong><?php echo esc_html($report['score']); ?>%</strong>
        <span><?php echo esc_html($status); ?></span>
      </div>
      <h2>Checklist de prontidao</h2>
      <table class="widefat striped">
        <thead><tr><th>Status</th><th>Item</th><th>Detalhe</th></tr></thead>
        <tbody>
          <?php foreach ($report['checks'] as $check): ?>
            <tr>
              <td><?php echo $check['passed'] ? '<span class="fs-ok">OK</span>' : '<span class="fs-fail">Ajustar</span>'; ?></td>
              <td><strong><?php echo esc_html($check['label']); ?></strong></td>
              <td><?php echo esc_html($check['detail']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <h2>Fontes oficiais usadas como criterio</h2>
      <ul>
        <li><a href="https://support.google.com/adsense/answer/7299563" target="_blank" rel="noopener">Google AdSense: paginas prontas para AdSense</a></li>
        <li><a href="https://support.google.com/adsense/answer/9724" target="_blank" rel="noopener">Google AdSense: requisitos de elegibilidade</a></li>
        <li><a href="https://developers.google.com/search/docs/fundamentals/seo-starter-guide" target="_blank" rel="noopener">Google Search Central: guia inicial de SEO</a></li>
      </ul>
    </div>
    <?php
}

add_action('admin_head-tools_page_fs-seo-adsense', function () {
    ?>
    <style>
      .fs-readiness-score{display:flex;align-items:baseline;gap:14px;margin:18px 0 24px;padding:18px 20px;background:#fff;border:1px solid #dcdcde;border-left:4px solid #f97316;max-width:720px}
      .fs-readiness-score strong{font-size:40px;line-height:1;color:#0f1f36}
      .fs-readiness-score span{font-size:16px;font-weight:600}
      .fs-ok{color:#008a20;font-weight:700}
      .fs-fail{color:#b32d2e;font-weight:700}
    </style>
    <?php
});

add_filter('manage_posts_columns', function ($cols) {
    $cols['fs_seo_score'] = 'SEO';
    return $cols;
}, 20);

function fs_render_seo_score_admin_column(string $col, int $post_id): void {
    if ($col !== 'fs_seo_score') {
        return;
    }
    $post = get_post($post_id);
    if (!$post instanceof WP_Post || !in_array($post->post_type, ['post', 'golpe', 'fraude'], true)) {
        echo '—';
        return;
    }
    $score = fs_seo_score_post($post);
    echo esc_html($score['score'] . '%');
}

add_action('manage_posts_custom_column', 'fs_render_seo_score_admin_column', 20, 2);

foreach (['golpe', 'fraude'] as $post_type) {
    add_filter("manage_{$post_type}_posts_columns", function ($cols) {
        $cols['fs_seo_score'] = 'SEO';
        return $cols;
    }, 20);
    add_action("manage_{$post_type}_posts_custom_column", 'fs_render_seo_score_admin_column', 20, 2);
}
