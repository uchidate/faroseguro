<?php
/**
 * Faro Seguro — Child theme do Kadence
 * Arquitetura de conteúdo para alertas de fraudes e golpes bancários.
 */

defined('ABSPATH') || exit;

require_once get_stylesheet_directory() . '/inc/ads.php';
require_once get_stylesheet_directory() . '/inc/seo-readiness.php';

/* ────────────────────────────────────────────
   1. ENQUEUE
   ──────────────────────────────────────────── */

add_action('wp_enqueue_scripts', function () {
    $ver = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'kadence-parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme('kadence')->get('Version')
    );

    wp_enqueue_style(
        'fs-style',
        get_stylesheet_uri(),
        ['kadence-parent-style'],
        $ver
    );

    // Inter via Bunny Fonts (sem Google, GDPR-friendly)
    wp_enqueue_style(
        'fs-inter',
        'https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap',
        [],
        null
    );

    wp_enqueue_script(
        'fs-main',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        [],
        $ver,
        true
    );

    // Passar dados para o JS
    wp_localize_script('fs-main', 'FS', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('fs_ajax'),
        'searchUrl' => home_url('/?s='),
    ]);
});

/* ────────────────────────────────────────────
   2. THEME SUPPORT
   ──────────────────────────────────────────── */

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style']);
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_editor_style('style.css');

    add_image_size('fs-hero',  1400, 600,  true);
    add_image_size('fs-card',   760, 430,  true);
    add_image_size('fs-thumb',  380, 215,  true);
    add_image_size('fs-square', 320, 320,  true);

    load_child_theme_textdomain('faro-seguro', get_stylesheet_directory() . '/languages');
});

/* ────────────────────────────────────────────
   3. MENUS & WIDGETS
   ──────────────────────────────────────────── */

add_action('after_setup_theme', function () {
    register_nav_menus([
        'primary' => 'Menu Principal',
        'footer'  => 'Menu Rodapé',
    ]);
});

add_action('widgets_init', function () {
    $defaults = [
        'before_widget' => '<div class="sidebar-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ];

    register_sidebar($defaults + [
        'name'        => 'Sidebar de Artigos',
        'id'          => 'sidebar-artigos',
        'description' => 'Exibida nos artigos e categorias.',
    ]);

    register_sidebar($defaults + [
        'name'        => 'Sidebar de Golpes',
        'id'          => 'sidebar-golpes',
        'description' => 'Exibida nos alertas de golpes.',
    ]);
});

/* ────────────────────────────────────────────
   4. POST TYPES
   ──────────────────────────────────────────── */

add_action('init', function () {

    /* 4a. Golpe — Alerta específico de fraude */
    register_post_type('golpe', [
        'label'         => 'Alertas de Golpe',
        'labels'        => [
            'name'               => 'Alertas de Golpe',
            'singular_name'      => 'Alerta de Golpe',
            'menu_name'          => 'Alertas',
            'add_new_item'       => 'Novo Alerta',
            'edit_item'          => 'Editar Alerta',
            'new_item'           => 'Novo Alerta',
            'view_item'          => 'Ver Alerta',
            'search_items'       => 'Buscar Alertas',
            'not_found'          => 'Nenhum alerta encontrado.',
            'not_found_in_trash' => 'Nenhum alerta na lixeira.',
            'all_items'          => 'Todos os Alertas',
        ],
        'public'            => true,
        'has_archive'       => true,
        'rewrite'           => ['slug' => 'golpes', 'with_front' => false],
        'supports'          => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'author'],
        'menu_icon'         => 'dashicons-shield-alt',
        'menu_position'     => 5,
        'show_in_rest'      => true,
        'taxonomies'        => ['tipo_golpe', 'canal_golpe', 'publico_alvo'],
    ]);

    /* 4b. Fraude — Acesso não autorizado, sem ação direta da vítima */
    register_post_type('fraude', [
        'label'         => 'Fraudes',
        'labels'        => [
            'name'               => 'Fraudes',
            'singular_name'      => 'Fraude',
            'menu_name'          => 'Fraudes',
            'add_new_item'       => 'Nova Fraude',
            'edit_item'          => 'Editar Fraude',
            'new_item'           => 'Nova Fraude',
            'view_item'          => 'Ver Fraude',
            'search_items'       => 'Buscar Fraudes',
            'not_found'          => 'Nenhuma fraude encontrada.',
            'not_found_in_trash' => 'Nenhuma fraude na lixeira.',
            'all_items'          => 'Todas as Fraudes',
        ],
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'fraudes', 'with_front' => false],
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'author'],
        'menu_icon'     => 'dashicons-warning',
        'menu_position' => 6,
        'show_in_rest'  => true,
        'taxonomies'    => ['tipo_fraude', 'canal_golpe', 'publico_alvo'],
    ]);

    /* 4c. Glossário — Dicionário de termos */
    register_post_type('glossario', [
        'label'         => 'Glossário',
        'labels'        => [
            'name'          => 'Glossário',
            'singular_name' => 'Termo',
            'add_new_item'  => 'Novo Termo',
            'edit_item'     => 'Editar Termo',
            'all_items'     => 'Todos os Termos',
        ],
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'glossario', 'with_front' => false],
        'supports'      => ['title', 'editor', 'excerpt'],
        'menu_icon'     => 'dashicons-book',
        'menu_position' => 6,
        'show_in_rest'  => true,
    ]);
});

/* ────────────────────────────────────────────
   5. TAXONOMIAS
   ──────────────────────────────────────────── */

add_action('init', function () {

    /* 5a. Tipo de Golpe */
    register_taxonomy('tipo_golpe', ['golpe'], [
        'label'         => 'Tipo de Golpe',
        'labels'        => [
            'name'          => 'Tipos de Golpe',
            'singular_name' => 'Tipo de Golpe',
            'all_items'     => 'Todos os Tipos',
            'add_new_item'  => 'Novo Tipo',
        ],
        'hierarchical'  => true,
        'rewrite'       => ['slug' => 'tipo-golpe', 'with_front' => false],
        'show_in_rest'  => true,
        'show_admin_column' => true,
    ]);

    /* 5b. Canal do Golpe */
    register_taxonomy('canal_golpe', ['golpe'], [
        'label'         => 'Canal',
        'labels'        => [
            'name'          => 'Canais',
            'singular_name' => 'Canal',
            'all_items'     => 'Todos os Canais',
            'add_new_item'  => 'Novo Canal',
        ],
        'hierarchical'  => false,
        'rewrite'       => ['slug' => 'canal', 'with_front' => false],
        'show_in_rest'  => true,
        'show_admin_column' => true,
    ]);

    /* 5c. Tipo de Fraude */
    register_taxonomy('tipo_fraude', ['fraude'], [
        'label'         => 'Tipo de Fraude',
        'labels'        => [
            'name'          => 'Tipos de Fraude',
            'singular_name' => 'Tipo de Fraude',
            'all_items'     => 'Todos os Tipos',
            'add_new_item'  => 'Novo Tipo',
        ],
        'hierarchical'      => true,
        'rewrite'           => ['slug' => 'tipo-fraude', 'with_front' => false],
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ]);

    /* 5d. Público-alvo */
    register_taxonomy('publico_alvo', ['golpe', 'fraude', 'post'], [
        'label'         => 'Público-Alvo',
        'labels'        => [
            'name'          => 'Públicos-Alvo',
            'singular_name' => 'Público-Alvo',
            'all_items'     => 'Todos os Públicos',
        ],
        'hierarchical'  => false,
        'rewrite'       => ['slug' => 'publico', 'with_front' => false],
        'show_in_rest'  => true,
        'show_admin_column' => true,
    ]);

    /* 5d. Categorias de Artigos — usa a nativa 'category'
       mas adiciona categorias específicas via admin.
       Aqui só garantimos que está habilitada para 'post'. */
});

/* ────────────────────────────────────────────
   6a. METABOX — Fraude
   ──────────────────────────────────────────── */

add_action('add_meta_boxes', function () {
    add_meta_box('fs_fraude_meta',      '⚠️ Dados da Fraude',    'fs_render_fraude_metabox',  'fraude', 'side', 'high');
    add_meta_box('fs_fraude_conteudo',  '📋 Estrutura da Fraude','fs_render_fraude_conteudo_metabox', 'fraude', 'normal', 'high');
});

function fs_render_fraude_metabox($post) {
    wp_nonce_field('fs_fraude_meta_save', 'fs_fraude_nonce');
    $nivel    = get_post_meta($post->ID, 'nivel_risco', true) ?: 'alto';
    $prejuizo = get_post_meta($post->ID, 'prejuizo_estimado', true);
    $nova     = get_post_meta($post->ID, 'nova_tecnica', true);
    $fonte    = get_post_meta($post->ID, 'fonte_referencia', true);
    ?>
    <p><label><strong>Nível de Risco</strong></label><br>
    <select name="nivel_risco" style="width:100%;margin-top:4px">
      <?php foreach (['alto' => '🔴 Alto', 'medio' => '🟡 Médio', 'baixo' => '🔵 Baixo'] as $v => $l): ?>
        <option value="<?php echo $v; ?>" <?php selected($nivel, $v); ?>><?php echo $l; ?></option>
      <?php endforeach; ?>
    </select></p>
    <p><label><strong>Prejuízo Estimado</strong></label><br>
    <input type="text" name="prejuizo_estimado" value="<?php echo esc_attr($prejuizo); ?>" placeholder="Ex: R$ 5.000 – R$ 50.000" style="width:100%"></p>
    <p><label><input type="checkbox" name="nova_tecnica" value="1" <?php checked($nova, '1'); ?>> Nova técnica identificada</label></p>
    <p><label><strong>Fonte / Referência</strong></label><br>
    <textarea name="fonte_referencia_fraude" style="width:100%;height:60px"><?php echo esc_textarea($fonte); ?></textarea></p>
    <?php
}

function fs_render_fraude_conteudo_metabox($post) {
    wp_nonce_field('fs_fraude_conteudo_save', 'fs_fraude_conteudo_nonce');
    $fields = [
        'como_funciona'    => ['Como acontece', 'Descreva o mecanismo técnico da fraude…'],
        'sinais_alerta'    => ['Sinais de alerta', 'Um sinal por linha…'],
        'como_se_proteger' => ['Como se proteger', 'Uma dica por linha…'],
        'o_que_fazer'      => ['O que fazer se for vítima', 'Um passo por linha…'],
    ];
    foreach ($fields as $key => [$label, $placeholder]):
        $val = get_post_meta($post->ID, $key, true);
        ?>
        <p><label><strong><?php echo $label; ?></strong></label><br>
        <textarea name="<?php echo $key; ?>" style="width:100%;height:100px;margin-top:4px" placeholder="<?php echo $placeholder; ?>"><?php echo esc_textarea($val); ?></textarea></p>
        <?php
    endforeach;
}

add_action('save_post_fraude', function ($post_id) {
    if (!isset($_POST['fs_fraude_nonce']) || !wp_verify_nonce($_POST['fs_fraude_nonce'], 'fs_fraude_meta_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields_single = ['nivel_risco', 'prejuizo_estimado'];
    foreach ($fields_single as $f) {
        if (isset($_POST[$f])) update_post_meta($post_id, $f, sanitize_text_field($_POST[$f]));
    }
    update_post_meta($post_id, 'nova_tecnica', isset($_POST['nova_tecnica']) ? '1' : '0');
    if (isset($_POST['fonte_referencia_fraude'])) {
        update_post_meta($post_id, 'fonte_referencia', sanitize_textarea_field($_POST['fonte_referencia_fraude']));
    }

    $fields_text = ['como_funciona', 'sinais_alerta', 'como_se_proteger', 'o_que_fazer'];
    foreach ($fields_text as $f) {
        if (isset($_POST[$f])) update_post_meta($post_id, $f, sanitize_textarea_field($_POST[$f]));
    }
});

/* ────────────────────────────────────────────
   6. CUSTOM FIELDS — Metaboxes (Golpe)
   ──────────────────────────────────────────── */

add_action('add_meta_boxes', function () {

    add_meta_box(
        'fs_golpe_meta',
        '🚨 Dados do Alerta',
        'fs_render_golpe_metabox',
        'golpe',
        'side',
        'high'
    );

    add_meta_box(
        'fs_golpe_conteudo',
        '📋 Estrutura do Golpe',
        'fs_render_golpe_conteudo_metabox',
        'golpe',
        'normal',
        'high'
    );
});

function fs_render_golpe_metabox($post) {
    wp_nonce_field('fs_golpe_meta_save', 'fs_golpe_nonce');
    $nivel     = get_post_meta($post->ID, 'nivel_risco', true) ?: 'alto';
    $prejuizo  = get_post_meta($post->ID, 'prejuizo_estimado', true);
    $novidade  = get_post_meta($post->ID, 'novo_modus', true);
    $fonte     = get_post_meta($post->ID, 'fonte_referencia', true);
    ?>
    <p>
      <label><strong>Nível de Risco</strong></label><br>
      <select name="nivel_risco" style="width:100%;margin-top:4px">
        <?php foreach (['alto' => '🚨 Alto', 'medio' => '⚠️ Médio', 'baixo' => 'ℹ️ Baixo'] as $v => $l): ?>
          <option value="<?= $v ?>" <?= selected($nivel, $v, false) ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <p>
      <label><strong>Prejuízo Médio Estimado</strong></label><br>
      <input type="text" name="prejuizo_estimado" value="<?= esc_attr($prejuizo) ?>"
             placeholder="ex: R$ 2.000" style="width:100%;margin-top:4px">
    </p>
    <p>
      <label>
        <input type="checkbox" name="novo_modus" value="1" <?= checked($novidade, '1', false) ?>>
        <strong>Novo modus operandi</strong>
      </label>
    </p>
    <p>
      <label><strong>Fonte / Referência</strong></label><br>
      <input type="url" name="fonte_referencia" value="<?= esc_attr($fonte) ?>"
             placeholder="https://..." style="width:100%;margin-top:4px">
    </p>
    <?php
}

function fs_render_golpe_conteudo_metabox($post) {
    $como_age      = get_post_meta($post->ID, 'como_age', true);
    $sinais        = get_post_meta($post->ID, 'sinais_alerta', true);
    $como_proteger = get_post_meta($post->ID, 'como_se_proteger', true);
    $o_que_fazer   = get_post_meta($post->ID, 'o_que_fazer', true);
    ?>
    <p style="color:#666;font-size:12px;margin-bottom:12px">
      Preencha estes campos estruturados para gerar automaticamente seções no post.
      O editor principal é para contexto adicional e casos reais.
    </p>
    <?php
    $fields = [
        'como_age'         => ['Como o Golpe Funciona', 'Descreva o passo a passo do modus operandi...', $como_age, 5],
        'sinais_alerta'    => ['Sinais de Alerta (um por linha)', "Ligação não solicitada pedindo dados\nUrgência excessiva na conversa", $sinais, 4],
        'como_se_proteger' => ['Como se Proteger (um por linha)', "Nunca transfira dinheiro por pedido telefônico\nConfirme pelo app oficial do banco", $como_proteger, 4],
        'o_que_fazer'      => ['O que Fazer se Cair no Golpe', 'Contate o banco imediatamente, registre boletim de ocorrência...', $o_que_fazer, 3],
    ];
    foreach ($fields as $key => [$label, $placeholder, $value, $rows]): ?>
    <p>
      <label><strong><?= $label ?></strong></label><br>
      <textarea name="<?= $key ?>" rows="<?= $rows ?>" placeholder="<?= $placeholder ?>"
        style="width:100%;margin-top:4px;font-family:inherit"><?= esc_textarea($value) ?></textarea>
    </p>
    <?php endforeach;
}

add_action('save_post_golpe', function ($post_id) {
    if (!isset($_POST['fs_golpe_nonce']) ||
        !wp_verify_nonce($_POST['fs_golpe_nonce'], 'fs_golpe_meta_save') ||
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $text_fields = ['prejuizo_estimado', 'fonte_referencia', 'como_age', 'sinais_alerta', 'como_se_proteger', 'o_que_fazer'];
    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
        }
    }

    $nivel = sanitize_text_field($_POST['nivel_risco'] ?? 'alto');
    update_post_meta($post_id, 'nivel_risco', in_array($nivel, ['alto', 'medio', 'baixo']) ? $nivel : 'alto');
    update_post_meta($post_id, 'novo_modus', isset($_POST['novo_modus']) ? '1' : '0');
});

/* ────────────────────────────────────────────
   7. CUSTOM FIELDS — Artigos (post)
   ──────────────────────────────────────────── */

add_action('add_meta_boxes', function () {
    add_meta_box(
        'fs_artigo_meta',
        '📝 Dados do Artigo',
        function ($post) {
            wp_nonce_field('fs_artigo_meta_save', 'fs_artigo_nonce');
            $minutos     = get_post_meta($post->ID, 'leitura_minutos', true);
            $atualizado  = get_post_meta($post->ID, 'atualizado_em', true);
            $destaque    = get_post_meta($post->ID, 'artigo_destaque', true);
            ?>
            <p>
              <label><strong>Tempo de Leitura (min)</strong></label><br>
              <input type="number" name="leitura_minutos" value="<?= esc_attr($minutos) ?>"
                     min="1" max="60" style="width:80px;margin-top:4px">
              <span style="color:#888;font-size:12px;margin-left:6px">Se vazio, calcula automaticamente.</span>
            </p>
            <p>
              <label><strong>Última Atualização</strong></label><br>
              <input type="date" name="atualizado_em" value="<?= esc_attr($atualizado) ?>" style="margin-top:4px">
            </p>
            <p>
              <label>
                <input type="checkbox" name="artigo_destaque" value="1" <?= checked($destaque, '1', false) ?>>
                <strong>Artigo em Destaque</strong> (aparece no topo da Home)
              </label>
            </p>
            <?php
        },
        'post',
        'side',
        'default'
    );
});

add_action('save_post_post', function ($post_id) {
    if (!isset($_POST['fs_artigo_nonce']) ||
        !wp_verify_nonce($_POST['fs_artigo_nonce'], 'fs_artigo_meta_save') ||
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $minutos = (int) ($_POST['leitura_minutos'] ?? 0);
    if ($minutos > 0) {
        update_post_meta($post_id, 'leitura_minutos', $minutos);
    } else {
        // Auto-calcular: ~200 palavras por minuto
        $content = get_post_field('post_content', $post_id);
        $words   = str_word_count(strip_tags($content));
        update_post_meta($post_id, 'leitura_minutos', max(1, round($words / 200)));
    }

    if (!empty($_POST['atualizado_em'])) {
        update_post_meta($post_id, 'atualizado_em', sanitize_text_field($_POST['atualizado_em']));
    }

    update_post_meta($post_id, 'artigo_destaque', isset($_POST['artigo_destaque']) ? '1' : '0');
});

/* ────────────────────────────────────────────
   8. HELPERS — funções reutilizáveis nos templates
   ──────────────────────────────────────────── */

/**
 * Retorna o badge HTML de nível de risco.
 */
function fs_badge_risco(string $nivel = 'alto'): string {
    $map = [
        'alto'  => ['fs-badge--red',    'Alto Risco'],
        'medio' => ['fs-badge--yellow', 'Risco Médio'],
        'baixo' => ['fs-badge--blue',   'Risco Baixo'],
    ];
    [$cls, $label] = $map[$nivel] ?? $map['alto'];
    return "<span class=\"fs-badge {$cls}\">{$label}</span>";
}

/**
 * Retorna o tempo de leitura formatado.
 */
function fs_leitura(int $post_id = 0): string {
    $post_id = $post_id ?: get_the_ID();
    $min     = (int) get_post_meta($post_id, 'leitura_minutos', true);
    if (!$min) {
        $words = str_word_count(strip_tags(get_post_field('post_content', $post_id)));
        $min   = max(1, round($words / 200));
    }
    return $min . ' min de leitura';
}

/**
 * Retorna lista de tags de categorias/taxonomias do post.
 */
function fs_post_tags(int $post_id = 0, string $taxonomy = 'category'): string {
    $post_id = $post_id ?: get_the_ID();
    $terms   = get_the_terms($post_id, $taxonomy);
    if (!$terms || is_wp_error($terms)) return '';

    $out = '';
    foreach ($terms as $t) {
        $url  = get_term_link($t);
        $out .= "<a href=\"" . esc_url($url) . "\" class=\"fs-tag\">" . esc_html($t->name) . "</a>";
    }
    return $out;
}

/**
 * Renderiza um card de golpe.
 */
function fs_golpe_card(WP_Post $post, bool $show_excerpt = true): void {
    $nivel   = get_post_meta($post->ID, 'nivel_risco', true) ?: 'alto';
    $novo    = get_post_meta($post->ID, 'novo_modus', true) === '1';
    $border  = ['alto' => '#ef4444', 'medio' => '#f59e0b', 'baixo' => '#3b82f6'][$nivel] ?? '#ef4444';
    $canal   = fs_post_tags($post->ID, 'canal_golpe');
    ?>
    <article class="fs-card fs-golpe-card" style="border-top:3px solid <?= $border ?>">
      <?php if (has_post_thumbnail($post->ID)): ?>
        <div class="fs-card__image">
          <a href="<?= get_permalink($post) ?>">
            <?= get_the_post_thumbnail($post->ID, 'fs-card') ?>
          </a>
        </div>
      <?php endif; ?>
      <div class="fs-card__body">
        <div class="fs-card__meta">
          <?= fs_badge_risco($nivel) ?>
          <?php if ($novo): ?>
            <span class="fs-badge fs-badge--novo">✦ Novo</span>
          <?php endif; ?>
          <?= $canal ?>
        </div>
        <h2 class="fs-card__title">
          <a href="<?= get_permalink($post) ?>"><?= get_the_title($post) ?></a>
        </h2>
        <?php if ($show_excerpt): ?>
          <p class="fs-card__excerpt"><?= wp_trim_words(get_the_excerpt($post), 22) ?></p>
        <?php endif; ?>
        <div class="fs-card__footer">
          <time class="fs-card__date" datetime="<?= get_the_date('c', $post) ?>">
            <?= get_the_date('d M Y', $post) ?>
          </time>
          <a href="<?= get_permalink($post) ?>" class="fs-card__link">Ler alerta →</a>
        </div>
      </div>
    </article>
    <?php
}

/**
 * Renderiza um card de fraude (visual distinto do golpe — azul/roxo).
 */
function fs_fraude_card(WP_Post $post, bool $show_excerpt = true): void {
    $nivel   = get_post_meta($post->ID, 'nivel_risco', true) ?: 'alto';
    $nova    = get_post_meta($post->ID, 'nova_tecnica', true) === '1';
    $tipos   = get_the_terms($post->ID, 'tipo_fraude');
    $border  = ['alto' => '#7c3aed', 'medio' => '#2563eb', 'baixo' => '#0891b2'][$nivel] ?? '#7c3aed';
    $badge_map = ['alto' => ['fs-badge--fraude-alto', 'Alto Risco'], 'medio' => ['fs-badge--fraude-medio', 'Risco Médio'], 'baixo' => ['fs-badge--fraude-baixo', 'Baixo Risco']];
    [$badge_cls, $badge_label] = $badge_map[$nivel] ?? $badge_map['alto'];
    ?>
    <article class="fs-card fs-fraude-card" style="border-top:3px solid <?php echo esc_attr($border); ?>">
      <?php if (has_post_thumbnail($post->ID)): ?>
        <div class="fs-card__image">
          <a href="<?php echo get_permalink($post); ?>"><?php echo get_the_post_thumbnail($post->ID, 'fs-card', ['loading' => 'eager']); ?></a>
        </div>
      <?php endif; ?>
      <div class="fs-card__body">
        <div class="fs-card__meta">
          <span class="fs-badge <?php echo $badge_cls; ?>"><?php echo $badge_label; ?></span>
          <?php if ($nova): ?><span class="fs-badge fs-badge--novo">✦ Nova técnica</span><?php endif; ?>
          <?php if ($tipos && !is_wp_error($tipos)): ?><span class="fs-tag"><?php echo esc_html($tipos[0]->name); ?></span><?php endif; ?>
        </div>
        <h2 class="fs-card__title"><a href="<?php echo get_permalink($post); ?>"><?php echo get_the_title($post); ?></a></h2>
        <?php if ($show_excerpt): ?>
          <p class="fs-card__excerpt"><?php echo wp_trim_words(get_the_excerpt($post), 22); ?></p>
        <?php endif; ?>
        <div class="fs-card__footer">
          <time class="fs-card__date" datetime="<?php echo get_the_date('c', $post); ?>"><?php echo get_the_date('d M Y', $post); ?></time>
          <a href="<?php echo get_permalink($post); ?>" class="fs-card__link" style="color:var(--purple);">Ver detalhes →</a>
        </div>
      </div>
    </article>
    <?php
}

/**
 * Renderiza um card de artigo.
 */
function fs_artigo_card(WP_Post $post, bool $large = false): void {
    $leitura   = fs_leitura($post->ID);
    $categorias = fs_post_tags($post->ID, 'category');
    ?>
    <article class="fs-card fs-artigo-card <?= $large ? 'fs-artigo-card--large' : '' ?>">
      <?php if (has_post_thumbnail($post->ID)): ?>
        <div class="fs-card__image">
          <a href="<?= get_permalink($post) ?>">
            <?= get_the_post_thumbnail($post->ID, $large ? 'fs-hero' : 'fs-card') ?>
          </a>
        </div>
      <?php endif; ?>
      <div class="fs-card__body">
        <div class="fs-card__meta">
          <?= $categorias ?>
          <span class="fs-card__date"><?= $leitura ?></span>
        </div>
        <<?= $large ? 'h2' : 'h3' ?> class="fs-card__title">
          <a href="<?= get_permalink($post) ?>"><?= get_the_title($post) ?></a>
        </<?= $large ? 'h2' : 'h3' ?>>
        <p class="fs-card__excerpt"><?= wp_trim_words(get_the_excerpt($post), $large ? 30 : 20) ?></p>
        <div class="fs-card__footer">
          <time class="fs-card__date" datetime="<?= get_the_date('c', $post) ?>">
            <?= get_the_date('d M Y', $post) ?>
          </time>
          <a href="<?= get_permalink($post) ?>" class="fs-card__link">Ler artigo →</a>
        </div>
      </div>
    </article>
    <?php
}

/**
 * Gera TOC a partir dos H2s do conteúdo.
 * Adiciona IDs nos headings para anchor links.
 *
 * @return array [['id' => 'slug', 'text' => 'Título'], …]
 */
function fs_build_toc(string $content): array {
    if (!preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $content, $matches)) return [];

    $items = [];
    foreach ($matches[1] as $text) {
        $clean = wp_strip_all_tags($text);
        $id    = sanitize_title($clean);
        if ($id) $items[] = ['id' => $id, 'text' => $clean];
    }

    // Injetar IDs nos headings do conteúdo via filtro
    add_filter('the_content', function ($c) use ($items) {
        foreach ($items as $item) {
            $c = preg_replace(
                '/<h2([^>]*)>(' . preg_quote($item['text'], '/') . ')<\/h2>/i',
                '<h2$1 id="' . esc_attr($item['id']) . '">' . $item['text'] . '</h2>',
                $c,
                1
            );
        }
        return $c;
    }, 5);

    return $items;
}

/**
 * Renderiza um card de artigo em layout hero (ocupando grid completo).
 */
function fs_artigo_card_hero(WP_Post $post): void {
    $leitura   = fs_leitura($post->ID);
    $destaque  = get_post_meta($post->ID, 'artigo_destaque', true) === '1';
    $cats      = get_the_terms($post->ID, 'category');
    $cat_html  = '';
    if ($cats && !is_wp_error($cats)) {
        foreach (array_slice($cats, 0, 1) as $c) {
            $cat_html = '<a href="' . get_term_link($c) . '" class="fs-cat fs-cat--default">' . esc_html($c->name) . '</a>';
        }
    }
    ?>
    <article class="fs-card fs-card--hero">
      <?php if (has_post_thumbnail($post->ID)): ?>
        <div class="fs-card__image">
          <a href="<?php echo get_permalink($post); ?>">
            <?php echo get_the_post_thumbnail($post->ID, 'full', ['loading' => 'eager', 'fetchpriority' => 'high']); ?>
          </a>
          <?php if ($destaque): ?>
            <div class="fs-card__image-badge"><span class="fs-badge fs-badge--novo">Destaque</span></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="fs-card__body">
        <div class="fs-card__meta">
          <?php echo $cat_html; ?>
          <span style="font-size:.72rem;color:var(--subtle);">⏱ <?php echo esc_html($leitura); ?></span>
        </div>
        <h2 class="fs-card__title"><a href="<?php echo get_permalink($post); ?>"><?php echo get_the_title($post); ?></a></h2>
        <p class="fs-card__excerpt"><?php echo wp_trim_words(get_the_excerpt($post), 35); ?></p>
        <div class="fs-card__footer">
          <time class="fs-card__date" datetime="<?php echo get_the_date('c', $post); ?>"><?php echo get_the_date('d \d\e F \d\e Y', $post); ?></time>
          <a href="<?php echo get_permalink($post); ?>" class="fs-card__link">Ler artigo completo →</a>
        </div>
      </div>
    </article>
    <?php
}

/* ────────────────────────────────────────────
   9. SCHEMA MARKUP (JSON-LD)
   ──────────────────────────────────────────── */

add_action('wp_head', function () {
    if (is_singular('post')) {
        $post = get_queried_object();
        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title($post),
            'description'   => get_the_excerpt($post),
            'datePublished' => get_the_date('c', $post),
            'dateModified'  => get_the_modified_date('c', $post),
            'author'        => [
                '@type' => 'Organization',
                'name'  => get_bloginfo('name'),
                'url'   => home_url('/'),
            ],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => get_bloginfo('name'),
                'url'   => home_url('/'),
            ],
        ];
        if (has_post_thumbnail($post)) {
            $schema['image'] = get_the_post_thumbnail_url($post, 'fs-hero');
        }
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }

    if (is_singular('golpe')) {
        $post   = get_queried_object();
        $nivel  = get_post_meta($post->ID, 'nivel_risco', true);
        // sinais_alerta usado no schema em versão futura
        $protec = get_post_meta($post->ID, 'como_se_proteger', true);

        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title($post),
            'description'   => get_the_excerpt($post),
            'datePublished' => get_the_date('c', $post),
            'dateModified'  => get_the_modified_date('c', $post),
            'author'        => ['@type' => 'Organization', 'name' => get_bloginfo('name')],
            'publisher'     => ['@type' => 'Organization', 'name' => get_bloginfo('name'), 'url' => home_url('/')],
            'keywords'      => 'golpe, fraude, ' . $nivel . ' risco',
        ];

        // HowTo para "como se proteger"
        if ($protec) {
            $steps = array_values(array_filter(array_map('trim', explode("\n", $protec))));
            $schema['@graph'][] = [
                '@type'   => 'HowTo',
                'name'    => 'Como se proteger: ' . get_the_title($post),
                'step'    => array_map(fn($s, $i) => [
                    '@type' => 'HowToStep',
                    'position' => $i + 1,
                    'text' => $s,
                ], $steps, array_keys($steps)),
            ];
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
});

/* ────────────────────────────────────────────
   10. BUSCA — incluir CPTs
   ──────────────────────────────────────────── */

add_filter('pre_get_posts', function (WP_Query $q) {
    if ($q->is_search() && $q->is_main_query() && !is_admin()) {
        $q->set('post_type', ['post', 'golpe', 'glossario']);
    }

    // Paginação dos arquivos de CPT
    if (!is_admin() && $q->is_main_query()) {
        if ($q->is_post_type_archive('golpe') || $q->is_tax(['tipo_golpe', 'canal_golpe', 'publico_alvo'])) {
            $q->set('posts_per_page', 12);
            $q->set('orderby', 'date');
            $q->set('order', 'DESC');
        }
        if ($q->is_home() || $q->is_archive()) {
            $q->set('posts_per_page', 10);
        }
    }
});

/* ────────────────────────────────────────────
   11. SHORTCODES
   ──────────────────────────────────────────── */

// [alerta nivel="alto"]Texto do aviso[/alerta]
add_shortcode('alerta', function ($atts, $content = '') {
    $a = shortcode_atts(['nivel' => 'alto'], $atts);
    $map = [
        'alto'  => ['#fee2e2', '#dc2626', '#fecaca', '🚨'],
        'medio' => ['#fffbeb', '#d97706', '#fde68a', '⚠️'],
        'baixo' => ['#eff6ff', '#2563eb', '#bfdbfe', 'ℹ️'],
    ];
    [$bg, $color, $border_color, $icon] = $map[$a['nivel']] ?? $map['alto'];
    return "<div class=\"fs-alerta\" style=\"background:{$bg};border:1px solid {$border_color};border-left:4px solid {$color};border-radius:8px;padding:1rem 1.25rem;margin:1.5rem 0\">"
         . "<strong style=\"color:{$color}\">{$icon} Atenção:</strong> "
         . do_shortcode($content)
         . "</div>";
});

// [ultimos-golpes qtd="4"]
add_shortcode('ultimos-golpes', function ($atts) {
    $a = shortcode_atts(['qtd' => 4], $atts);
    $posts = get_posts(['post_type' => 'golpe', 'numberposts' => (int) $a['qtd'], 'post_status' => 'publish']);
    if (!$posts) return '';

    ob_start();
    echo '<div class="fs-grid fs-grid--4">';
    foreach ($posts as $post) {
        setup_postdata($post);
        fs_golpe_card($post, false);
    }
    wp_reset_postdata();
    echo '</div>';
    return ob_get_clean();
});

// [ultimos-artigos qtd="3"]
add_shortcode('ultimos-artigos', function ($atts) {
    $a = shortcode_atts(['qtd' => 3], $atts);
    $posts = get_posts(['post_type' => 'post', 'numberposts' => (int) $a['qtd'], 'post_status' => 'publish']);
    if (!$posts) return '';

    ob_start();
    echo '<div class="fs-grid fs-grid--3">';
    foreach ($posts as $post) {
        setup_postdata($post);
        fs_artigo_card($post, false);
    }
    wp_reset_postdata();
    echo '</div>';
    return ob_get_clean();
});

/* ────────────────────────────────────────────
   12. ADMIN — melhorias editoriais
   ──────────────────────────────────────────── */

// Colunas customizadas na listagem de golpes
add_filter('manage_golpe_posts_columns', function ($cols) {
    return array_merge(
        array_slice($cols, 0, 2),
        [
            'nivel_risco'   => '🚨 Risco',
            'novo_modus'    => '✦ Novo',
            'canal_golpe'   => 'Canal',
        ],
        array_slice($cols, 2)
    );
});

add_action('manage_golpe_posts_custom_column', function ($col, $post_id) {
    if ($col === 'nivel_risco') {
        $n = get_post_meta($post_id, 'nivel_risco', true) ?: 'alto';
        $labels = ['alto' => '🚨 Alto', 'medio' => '⚠️ Médio', 'baixo' => 'ℹ️ Baixo'];
        echo $labels[$n] ?? '—';
    }
    if ($col === 'novo_modus') {
        echo get_post_meta($post_id, 'novo_modus', true) === '1' ? '✦ Sim' : '—';
    }
    if ($col === 'canal_golpe') {
        $terms = get_the_terms($post_id, 'canal_golpe');
        echo $terms && !is_wp_error($terms) ? implode(', ', wp_list_pluck($terms, 'name')) : '—';
    }
}, 10, 2);

// Colunas de artigo: leitura + destaque
add_filter('manage_posts_columns', function ($cols) {
    $cols['leitura_min'] = '⏱ Leitura';
    $cols['destaque']    = '⭐ Destaque';
    return $cols;
});

add_action('manage_posts_custom_column', function ($col, $post_id) {
    if ($col === 'leitura_min') echo fs_leitura($post_id);
    if ($col === 'destaque')    echo get_post_meta($post_id, 'artigo_destaque', true) === '1' ? '⭐ Sim' : '—';
}, 10, 2);

/* ────────────────────────────────────────────
   13. SEGURANÇA & PERFORMANCE
   ──────────────────────────────────────────── */

// Remover versão do WP do HTML
remove_action('wp_head', 'wp_generator');

// Desabilitar XML-RPC (vetor de ataque)
add_filter('xmlrpc_enabled', '__return_false');

// Remover emojis (desnecessários, carregam JS extra)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

// Headers de segurança
add_action('send_headers', function () {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
});
