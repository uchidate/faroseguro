<?php
/**
 * Faro Seguro — Child theme do Kadence
 * Foco: informação e alertas sobre fraudes e golpes bancários
 */

// Garantir que o pai (Kadence) carrega primeiro
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'kadence-parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme('kadence')->get('Version')
    );

    wp_enqueue_style(
        'faro-seguro-child',
        get_stylesheet_uri(),
        ['kadence-parent-style'],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'faro-seguro-main',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        [],
        '1.0.0',
        true
    );
});

// Suporte a features do tema
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_editor_style('style.css');

    // Tamanhos de imagem
    add_image_size('fs-card', 760, 430, true);
    add_image_size('fs-thumb', 380, 215, true);

    load_child_theme_textdomain('faro-seguro', get_stylesheet_directory() . '/languages');
});

// Menus
add_action('after_setup_theme', function () {
    register_nav_menus([
        'primary' => __('Menu Principal', 'faro-seguro'),
        'footer'  => __('Menu Rodapé', 'faro-seguro'),
    ]);
});

// Widgets
add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('Sidebar Principal', 'faro-seguro'),
        'id'            => 'sidebar-main',
        'description'   => __('Sidebar exibida em posts e páginas internas.', 'faro-seguro'),
        'before_widget' => '<div class="sidebar-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
});

// Custom Post Type: Tipo de Golpe
add_action('init', function () {
    register_post_type('golpe', [
        'label'               => 'Golpes e Fraudes',
        'labels'              => [
            'name'          => 'Golpes e Fraudes',
            'singular_name' => 'Golpe',
            'add_new_item'  => 'Novo Alerta de Golpe',
            'edit_item'     => 'Editar Golpe',
            'view_item'     => 'Ver Golpe',
            'search_items'  => 'Buscar Golpes',
            'not_found'     => 'Nenhum golpe cadastrado.',
        ],
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => ['slug' => 'golpes'],
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'menu_icon'           => 'dashicons-warning',
        'show_in_rest'        => true, // habilita Gutenberg
    ]);

    // Taxonomia: Categoria de Golpe
    register_taxonomy('tipo_golpe', 'golpe', [
        'label'        => 'Tipos de Golpe',
        'hierarchical' => true,
        'rewrite'      => ['slug' => 'tipo-golpe'],
        'show_in_rest' => true,
    ]);
});

// Shortcode: [alerta-golpe tipo="pix" nivel="alto"]
add_shortcode('alerta-golpe', function ($atts) {
    $a = shortcode_atts([
        'tipo'  => 'Golpe',
        'nivel' => 'alto',
    ], $atts);

    $cores = [
        'alto'   => ['bg' => '#fee2e2', 'border' => '#ef4444', 'icon' => '🚨'],
        'medio'  => ['bg' => '#fef3c7', 'border' => '#f59e0b', 'icon' => '⚠️'],
        'baixo'  => ['bg' => '#dbeafe', 'border' => '#3b82f6', 'icon' => 'ℹ️'],
    ];

    $c = $cores[$a['nivel']] ?? $cores['alto'];

    return sprintf(
        '<div class="fs-alerta" style="background:%s;border-left:4px solid %s;padding:1rem 1.25rem;border-radius:8px;margin:1.5rem 0;">
            <strong>%s Alerta: %s</strong>
        </div>',
        esc_attr($c['bg']),
        esc_attr($c['border']),
        $c['icon'],
        esc_html($a['tipo'])
    );
});

// Nível de risco no loop de posts
add_filter('the_excerpt', function ($excerpt) {
    global $post;
    if ($post->post_type !== 'golpe') return $excerpt;

    $nivel = get_post_meta($post->ID, 'nivel_risco', true);
    if (!$nivel) return $excerpt;

    $badges = [
        'alto'  => '<span class="fs-badge fs-badge--red">🚨 Risco Alto</span>',
        'medio' => '<span class="fs-badge fs-badge--yellow">⚠️ Risco Médio</span>',
        'baixo' => '<span class="fs-badge fs-badge--blue">ℹ️ Risco Baixo</span>',
    ];

    return ($badges[$nivel] ?? '') . $excerpt;
});

// Adiciona metabox de Nível de Risco no CPT Golpe
add_action('add_meta_boxes', function () {
    add_meta_box('fs_nivel_risco', 'Nível de Risco', function ($post) {
        $valor = get_post_meta($post->ID, 'nivel_risco', true);
        echo '<label>Nível: <select name="nivel_risco">
            <option value="alto"  ' . selected($valor, 'alto',  false) . '>🚨 Alto</option>
            <option value="medio" ' . selected($valor, 'medio', false) . '>⚠️ Médio</option>
            <option value="baixo" ' . selected($valor, 'baixo', false) . '>ℹ️ Baixo</option>
        </select></label>';
    }, 'golpe', 'side');
});

add_action('save_post_golpe', function ($post_id) {
    if (isset($_POST['nivel_risco'])) {
        update_post_meta($post_id, 'nivel_risco', sanitize_text_field($_POST['nivel_risco']));
    }
});
