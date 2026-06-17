<?php
/**
 * Guia Antifraude — Componentes editoriais
 *
 * Shortcodes disponíveis no editor WordPress:
 *
 *   [faq]
 *     [faq-item q="Pergunta aqui?"]Resposta aqui.[/faq-item]
 *   [/faq]
 *
 *   [estatistica numero="R$ 10bi" descricao="perdidos em fraudes em 2024" fonte="Febraban"]
 *
 *   [checklist titulo="O que fazer agora"]
 *   - Ligue para o banco
 *   - Registre o B.O.
 *   [/checklist]
 *
 *   [passo numero="1" titulo="Bloqueie seu cartão"]Texto explicativo.[/passo]
 *
 *   [destaque]Texto em evidência — citação, alerta suave ou resumo.[/destaque]
 *
 *   [comparativo titulo="Golpe vs. Fraude"]
 *   Coluna A | Coluna B
 *   Você age | Acontece sem você
 *   [/comparativo]
 *
 *   [resumo]
 *   - Ponto 1
 *   - Ponto 2
 *   [/resumo]
 */

defined('ABSPATH') || exit;

/* ─────────────────────────────────────────
   FAQ com schema FAQPage automático
───────────────────────────────────────── */

// Armazena FAQs da página para o schema JSON-LD
$GLOBALS['fs_faq_items'] = [];

add_shortcode('faq', function ($atts, $content = '') {
    $content = do_shortcode($content);
    $titulo  = shortcode_atts(['titulo' => 'Perguntas frequentes'], $atts)['titulo'];

    return "<div class=\"fs-faq\" itemscope itemtype=\"https://schema.org/FAQPage\">"
         . "<h2 class=\"fs-faq__title\">{$titulo}</h2>"
         . "<div class=\"fs-faq__list\">{$content}</div>"
         . "</div>";
});

add_shortcode('faq-item', function ($atts, $content = '') {
    $a = shortcode_atts(['q' => ''], $atts);
    $q = esc_html($a['q']);
    $r = wp_kses_post(do_shortcode($content));

    // Acumula para o schema
    $GLOBALS['fs_faq_items'][] = ['q' => $a['q'], 'a' => wp_strip_all_tags($content)];

    return "<div class=\"fs-faq__item\" itemscope itemprop=\"mainEntity\" itemtype=\"https://schema.org/Question\">"
         . "<button class=\"fs-faq__question\" aria-expanded=\"false\" itemprop=\"name\">{$q}"
         . "<svg class=\"fs-faq__chevron\" width=\"18\" height=\"18\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"><polyline points=\"6 9 12 15 18 9\"/></svg>"
         . "</button>"
         . "<div class=\"fs-faq__answer\" itemscope itemprop=\"acceptedAnswer\" itemtype=\"https://schema.org/Answer\">"
         . "<div itemprop=\"text\">{$r}</div>"
         . "</div>"
         . "</div>";
});

// Injeta FAQPage schema no <head> se houver FAQs na página
add_action('wp_footer', function () {
    if (empty($GLOBALS['fs_faq_items'])) return;

    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(fn($item) => [
            '@type'          => 'Question',
            'name'           => $item['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
        ], $GLOBALS['fs_faq_items']),
    ];

    echo '<script type="application/ld+json">'
       . wp_json_encode($schema, JSON_UNESCAPED_UNICODE)
       . '</script>' . "\n";
});

/* ─────────────────────────────────────────
   Estatística em destaque
───────────────────────────────────────── */

add_shortcode('estatistica', function ($atts) {
    $a = shortcode_atts([
        'numero'    => '',
        'descricao' => '',
        'fonte'     => '',
        'cor'       => 'red', // red | orange | blue | green
    ], $atts);

    $fonte_html = $a['fonte']
        ? "<span class=\"fs-stat-card__fonte\">Fonte: " . esc_html($a['fonte']) . "</span>"
        : '';

    return "<div class=\"fs-stat-card fs-stat-card--{$a['cor']}\">"
         . "<span class=\"fs-stat-card__numero\">" . esc_html($a['numero']) . "</span>"
         . "<span class=\"fs-stat-card__desc\">" . esc_html($a['descricao']) . "</span>"
         . $fonte_html
         . "</div>";
});

/* ─────────────────────────────────────────
   Checklist visual
───────────────────────────────────────── */

add_shortcode('checklist', function ($atts, $content = '') {
    $a = shortcode_atts([
        'titulo' => '',
        'tipo'   => 'ok', // ok | warn | steps
    ], $atts);

    // Parse linhas que começam com "-" ou "*"
    $lines = array_filter(array_map('trim', explode("\n", strip_tags($content))));
    $items = '';
    foreach ($lines as $line) {
        $line = ltrim($line, '-* ');
        if ($line === '') continue;
        $items .= "<li>" . esc_html($line) . "</li>";
    }

    if (!$items) return '';

    $titulo_html = $a['titulo']
        ? "<p class=\"fs-checklist-block__titulo\">" . esc_html($a['titulo']) . "</p>"
        : '';

    return "<div class=\"fs-checklist-block fs-checklist-block--{$a['tipo']}\">"
         . $titulo_html
         . "<ul class=\"fs-checklist fs-checklist--{$a['tipo']}\">{$items}</ul>"
         . "</div>";
});

/* ─────────────────────────────────────────
   Passo a passo numerado
───────────────────────────────────────── */

add_shortcode('passo', function ($atts, $content = '') {
    $a = shortcode_atts(['numero' => '1', 'titulo' => ''], $atts);

    $titulo_html = $a['titulo']
        ? "<strong class=\"fs-passo__titulo\">" . esc_html($a['titulo']) . "</strong>"
        : '';

    return "<div class=\"fs-passo\">"
         . "<span class=\"fs-passo__num\">" . esc_html($a['numero']) . "</span>"
         . "<div class=\"fs-passo__body\">"
         . $titulo_html
         . "<div class=\"fs-passo__desc\">" . wp_kses_post(do_shortcode($content)) . "</div>"
         . "</div>"
         . "</div>";
});

/* ─────────────────────────────────────────
   Destaque / callout
───────────────────────────────────────── */

add_shortcode('destaque', function ($atts, $content = '') {
    $a = shortcode_atts(['tipo' => 'info'], $atts); // info | perigo | dica | importante

    $icones = [
        'info'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
        'perigo'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        'dica'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
        'importante'=> '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    ];

    $icone = $icones[$a['tipo']] ?? $icones['info'];

    return "<div class=\"fs-destaque fs-destaque--{$a['tipo']}\">"
         . "<span class=\"fs-destaque__icone\" aria-hidden=\"true\">{$icone}</span>"
         . "<div class=\"fs-destaque__corpo\">" . wp_kses_post(do_shortcode($content)) . "</div>"
         . "</div>";
});

/* ─────────────────────────────────────────
   Tabela comparativa
───────────────────────────────────────── */

add_shortcode('comparativo', function ($atts, $content = '') {
    $a = shortcode_atts(['titulo' => '', 'col1' => '', 'col2' => ''], $atts);

    $lines = array_filter(array_map('trim', explode("\n", strip_tags($content))));
    $rows  = '';
    $has_header = false;

    foreach ($lines as $line) {
        if ($line === '') continue;
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) < 2) continue;

        if (!$has_header && ($a['col1'] || $a['col2'])) {
            // Já tem header via atributo, não precisa de linha
        }

        $rows .= "<tr><td>" . esc_html($parts[0]) . "</td><td>" . esc_html($parts[1]) . "</td></tr>";
    }

    if (!$rows) return '';

    $titulo_html = $a['titulo']
        ? "<caption class=\"fs-comparativo__titulo\">" . esc_html($a['titulo']) . "</caption>"
        : '';

    $head_html = ($a['col1'] && $a['col2'])
        ? "<thead><tr><th>" . esc_html($a['col1']) . "</th><th>" . esc_html($a['col2']) . "</th></tr></thead>"
        : '';

    return "<div class=\"fs-comparativo\"><table class=\"fs-comparativo__table\">"
         . $titulo_html . $head_html
         . "<tbody>{$rows}</tbody>"
         . "</table></div>";
});

/* ─────────────────────────────────────────
   Resumo rápido (topo do artigo)
───────────────────────────────────────── */

add_shortcode('resumo', function ($atts, $content = '') {
    $a = shortcode_atts(['titulo' => 'Resumo do artigo'], $atts);

    $lines = array_filter(array_map('trim', explode("\n", strip_tags($content))));
    $items = '';
    foreach ($lines as $line) {
        $line = ltrim($line, '-* ');
        if ($line === '') continue;
        $items .= "<li>" . esc_html($line) . "</li>";
    }

    if (!$items) return '';

    return "<div class=\"fs-resumo\">"
         . "<div class=\"fs-resumo__header\">"
         . "<svg width=\"18\" height=\"18\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"><path d=\"M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z\"/><polyline points=\"14 2 14 8 20 8\"/><line x1=\"16\" y1=\"13\" x2=\"8\" y2=\"13\"/><line x1=\"16\" y1=\"17\" x2=\"8\" y2=\"17\"/><polyline points=\"10 9 9 9 8 9\"/></svg>"
         . "<span>" . esc_html($a['titulo']) . "</span>"
         . "</div>"
         . "<ul class=\"fs-resumo__list\">{$items}</ul>"
         . "</div>";
});

/* ─────────────────────────────────────────
   Progress bar de leitura (script inline)
───────────────────────────────────────── */

add_action('wp_footer', function () {
    if (!is_singular(['post', 'golpe', 'fraude'])) return;
    ?>
    <script>
    (function() {
      var bar = document.getElementById('fs-read-progress');
      if (!bar) return;
      var article = document.getElementById('fs-article-content') || document.querySelector('.fs-prose');
      if (!article) return;
      function updateBar() {
        var rect   = article.getBoundingClientRect();
        var total  = article.offsetHeight;
        var read   = Math.max(0, -rect.top);
        var pct    = Math.min(100, Math.round((read / total) * 100));
        bar.style.width = pct + '%';
      }
      window.addEventListener('scroll', updateBar, {passive: true});
      updateBar();
    })();
    </script>
    <?php
});

/* ─────────────────────────────────────────
   FAQ accordion JS
───────────────────────────────────────── */

add_action('wp_footer', function () {
    if (!is_singular()) return;
    ?>
    <script>
    document.querySelectorAll('.fs-faq__question').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var expanded = this.getAttribute('aria-expanded') === 'true';
        // Fecha todos
        document.querySelectorAll('.fs-faq__question').forEach(function(b) {
          b.setAttribute('aria-expanded', 'false');
          b.nextElementSibling.style.maxHeight = null;
        });
        // Abre o clicado (se estava fechado)
        if (!expanded) {
          this.setAttribute('aria-expanded', 'true');
          var answer = this.nextElementSibling;
          answer.style.maxHeight = answer.scrollHeight + 'px';
        }
      });
    });
    </script>
    <?php
});
