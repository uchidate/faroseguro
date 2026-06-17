<?php
/**
 * Faro Seguro — Sistema de anúncios (Google AdSense)
 *
 * Como usar:
 *   fs_ad('in_article')   — dentro do conteúdo
 *   fs_ad('sidebar')      — sidebar 300x250
 *   fs_ad('leaderboard')  — topo/rodapé 728x90
 *   fs_ad('between')      — entre seções de cards
 *
 * Para ativar: defina FS_ADSENSE_ID nas constantes abaixo
 * ou em wp-config.php via:  define('FS_ADSENSE_ID', 'ca-pub-XXXXXXXXXXXXXXXX');
 */

defined('ABSPATH') || exit;

/* ── Configuração ────────────────────────────── */

// Publisher ID — altere após aprovação do AdSense
if (!defined('FS_ADSENSE_ID')) {
    define('FS_ADSENSE_ID', 'ca-pub-6015098995926392');
}

// Desabilitar ads em ambientes de dev
if (!defined('FS_ADS_ENABLED')) {
    define('FS_ADS_ENABLED', !empty(FS_ADSENSE_ID) && !WP_DEBUG);
}

// Slots de cada unidade de anúncio (criar no painel AdSense)
$GLOBALS['fs_ad_slots'] = [
    'in_article'  => '',   // ex: 1234567890 — Unidade "In-article"
    'sidebar'     => '',   // ex: 0987654321 — Unidade "Display" 300x250
    'leaderboard' => '',   // ex: 1122334455 — Unidade "Leaderboard" 728x90
    'between'     => '',   // ex: 5544332211 — Unidade "Display" responsive
];

/* ── Carregar script do AdSense (uma vez) ────── */

add_action('wp_head', function () {
    if (empty(FS_ADSENSE_ID) || is_admin()) return;
    echo '<meta name="google-adsense-account" content="' . esc_attr(FS_ADSENSE_ID) . '">' . "\n";
    echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr(FS_ADSENSE_ID) . '" crossorigin="anonymous"></script>' . "\n";
}, 1);

/* ── Helper principal ────────────────────────── */

/**
 * Renderiza uma unidade de anúncio.
 *
 * @param string $zone       in_article | sidebar | leaderboard | between
 * @param bool   $echo       true = echo, false = return
 */
function fs_ad(string $zone = 'in_article', bool $echo = true): string {
    // Não exibir: admin, feeds, bots conhecidos
    if (is_admin() || is_feed() || !is_singular() && $zone === 'in_article') {
        return '';
    }

    $slot = $GLOBALS['fs_ad_slots'][$zone] ?? '';

    // Sem publisher ID ou slot: exibe placeholder no debug
    if (!FS_ADS_ENABLED || empty($slot)) {
        if (WP_DEBUG) {
            $labels = [
                'in_article'  => 'In-Article (responsive)',
                'sidebar'     => 'Sidebar 300×250',
                'leaderboard' => 'Leaderboard 728×90',
                'between'     => 'Between-sections (responsive)',
            ];
            $label = $labels[$zone] ?? $zone;
            $html = "<div class=\"fs-ad-placeholder\" aria-hidden=\"true\">[AdSense — {$label}]</div>";
            if ($echo) { echo $html; return ''; }
            return $html;
        }
        return '';
    }

    $formats = [
        'in_article'  => ['auto', 'true'],
        'sidebar'     => ['auto', 'false'],
        'leaderboard' => ['auto', 'false'],
        'between'     => ['auto', 'false'],
    ];

    [$format, $full_width] = $formats[$zone];

    $html = sprintf(
        '<div class="fs-ad fs-ad--%s">
          <ins class="adsbygoogle"
               style="display:block"
               data-ad-client="%s"
               data-ad-slot="%s"
               data-ad-format="%s"
               data-full-width-responsive="%s"></ins>
          <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>',
        esc_attr($zone),
        esc_attr(FS_ADSENSE_ID),
        esc_attr($slot),
        esc_attr($format),
        esc_attr($full_width)
    );

    if ($echo) { echo $html; return ''; }
    return $html;
}

/* ── Injeção automática no conteúdo ─────────── */

/**
 * Injeta ad após o 2º parágrafo do conteúdo do post.
 * Isso é o "in-article" mais eficaz para RPM.
 */
add_filter('the_content', function (string $content): string {
    if (!is_singular(['post', 'golpe']) || is_admin()) return $content;

    $ad = fs_ad('in_article', false);
    if (!$ad) return $content;

    // Encontra o fechamento do 2º <p> e injeta depois
    $paragraphs = explode('</p>', $content);
    if (count($paragraphs) < 3) return $content;

    // Injeta após o 2º parágrafo
    $paragraphs[1] .= '</p>' . $ad;
    array_splice($paragraphs, 2, 0, ['']);  // ajusta o split

    return implode('', $paragraphs);
}, 20);
