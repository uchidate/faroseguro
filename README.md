# Guia Antifraude

Tema WordPress para o dominio `guiaantifraude.com`, com arquitetura editorial para alertas de golpes, fraudes bancarias, glossario e artigos educativos.

## Arquivos principais

- `wp-content/themes/guiaantifraude/` — child theme WordPress.
- `wp-content/themes/guiaantifraude/functions.php` — CPTs, taxonomias, metaboxes, schema e helpers editoriais.
- `wp-content/themes/guiaantifraude/inc/ads.php` — configuracao do Google AdSense.
- `wp-content/themes/guiaantifraude/inc/seo-readiness.php` — SEO tecnico e painel de prontidao para AdSense.
- `ads.txt` — autorizacao do publisher AdSense na raiz publica.
- `Dockerfile` — imagem WordPress com o tema.
- `docker-compose.yml` — WordPress + MySQL para desenvolvimento local.
- `scripts/lint-php.sh` — lint PHP usando a imagem oficial do WordPress.

## Como usar

### WordPress local

1. Suba o ambiente:
   - `docker compose up --build`
2. Acesse:
   - `http://localhost:8080`
3. Ative o tema `Guia Antifraude` no painel do WordPress.

### Dominio

O dominio publico da marca e:

- `https://guiaantifraude.com`

Em producao, configure `WP_HOME` e `WP_SITEURL` para esse dominio no `wp-config.php`, painel da hospedagem ou variaveis de ambiente equivalentes.

### Validacao PHP

O projeto nao depende de `php` instalado na maquina local. Use Docker:

- `scripts/lint-php.sh`

O script roda `php -l` em todos os arquivos PHP do tema usando a imagem `wordpress:latest`.

### SEO e AdSense

No admin do WordPress, acesse:

- `Ferramentas > SEO & AdSense`

O painel avalia indexacao, sitemap, paginas essenciais, volume editorial, conteudo substancial, publisher ID, `ads.txt`, slots de anuncio e configuracao de producao.

### Deploy

1. Crie um repositório Git com esses arquivos.
2. No Coolify, configure um novo app utilizando o `Dockerfile`.
3. Configure o domínio principal como `guiaantifraude.com`.
4. Aponte o DNS do domínio para o servidor do Coolify.

### Coolify

No app WordPress do Coolify (`https://coolify.hallyuhub.com.br`), configure:

- Domains: `https://guiaantifraude.com`
- Build Pack: Dockerfile
- Repository branch: `main`
- Auto deploy: enabled

Variáveis recomendadas:

```text
WORDPRESS_CONFIG_EXTRA=define('WP_HOME', 'https://guiaantifraude.com');
define('WP_SITEURL', 'https://guiaantifraude.com');
define('FORCE_SSL_ADMIN', true);
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
```

Depois do deploy, ative o tema `Guia Antifraude` no WordPress se ele não for ativado automaticamente.

Observacao de transicao: a imagem Docker tambem copia o tema para o caminho legado `faroseguro` para evitar quebra caso o banco de producao ainda tenha o stylesheet antigo ativo. Depois de ativar `Guia Antifraude` no WordPress, esse fallback pode ser removido em uma limpeza futura.
