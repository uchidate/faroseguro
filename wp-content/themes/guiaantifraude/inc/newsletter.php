<?php
/* Newsletter — tabela, AJAX handlers e componentes de UI */

/* ── Criar tabela na ativação do tema ───────── */
function fs_newsletter_create_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'fs_newsletter';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) return;
    $charset = $wpdb->get_charset_collate();
    $wpdb->query("CREATE TABLE $table (
        id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        email      VARCHAR(191)    NOT NULL,
        created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
        confirmed  TINYINT(1)      NOT NULL DEFAULT 0,
        ip         VARCHAR(45)     NULL,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset;");
}
add_action('after_switch_theme', 'fs_newsletter_create_table');
add_action('init', function () {
    global $wpdb;
    $table = $wpdb->prefix . 'fs_newsletter';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table) {
        fs_newsletter_create_table();
    }
});

/* ── AJAX handler ───────────────────────────── */
function fs_newsletter_subscribe() {
    check_ajax_referer('fs_newsletter_nonce', 'nonce');
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'E-mail inválido.']);
    }
    global $wpdb;
    $table = $wpdb->prefix . 'fs_newsletter';
    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE email = %s", $email));
    if ($exists) {
        wp_send_json_success(['message' => 'Você já está inscrito. Obrigado!']);
    }
    $inserted = $wpdb->insert($table, [
        'email'      => $email,
        'confirmed'  => 1,
        'ip'         => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
        'created_at' => current_time('mysql'),
    ]);
    if ($inserted) {
        wp_send_json_success(['message' => 'Inscrito! Você receberá alertas de novos golpes e fraudes.']);
    }
    wp_send_json_error(['message' => 'Erro ao salvar. Tente novamente.']);
}
add_action('wp_ajax_fs_newsletter_subscribe',        'fs_newsletter_subscribe');
add_action('wp_ajax_nopriv_fs_newsletter_subscribe', 'fs_newsletter_subscribe');

/* ── Componente de UI ───────────────────────── */
function fs_newsletter_widget(string $variant = 'inline'): void {
    $nonce = wp_create_nonce('fs_newsletter_nonce');
    $ajax  = admin_url('admin-ajax.php');
    $cls   = 'fs-newsletter fs-newsletter--' . esc_attr($variant);
    ?>
    <div class="<?php echo $cls; ?>" data-ajax="<?php echo esc_attr($ajax); ?>" data-nonce="<?php echo esc_attr($nonce); ?>">
      <div class="fs-newsletter__inner">
        <div class="fs-newsletter__copy">
          <svg class="fs-newsletter__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <div>
            <strong>Alertas de novos golpes</strong>
            <span>Receba por e-mail quando identificarmos ameaças novas.</span>
          </div>
        </div>
        <form class="fs-newsletter__form" novalidate>
          <input type="email" name="email" placeholder="seu@email.com" required autocomplete="email">
          <button type="submit">
            <span class="fs-newsletter__btn-text">Me avisar</span>
            <span class="fs-newsletter__btn-loading" aria-hidden="true">…</span>
          </button>
        </form>
        <p class="fs-newsletter__msg" role="alert" aria-live="polite"></p>
        <p class="fs-newsletter__privacy">Sem spam. Cancele quando quiser.</p>
      </div>
    </div>
    <?php
}

/* ── Admin: ver inscritos ───────────────────── */
add_action('admin_menu', function () {
    add_menu_page('Newsletter', 'Newsletter', 'manage_options', 'fs-newsletter', function () {
        global $wpdb;
        $table = $wpdb->prefix . 'fs_newsletter';
        $rows  = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 200");
        echo '<div class="wrap"><h1>Inscritos na Newsletter</h1>';
        echo '<table class="widefat striped"><thead><tr><th>E-mail</th><th>Data</th><th>IP</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            echo '<tr><td>' . esc_html($r->email) . '</td><td>' . esc_html($r->created_at) . '</td><td>' . esc_html($r->ip) . '</td></tr>';
        }
        echo '</tbody></table></div>';
    }, 'dashicons-email-alt', 30);
});
