# Faro Seguro

Tema WordPress para o dominio `faroseguro.com.br`, com arquitetura editorial para alertas de golpes, fraudes bancarias, glossario e artigos educativos.

## Arquivos principais

- `wp-content/themes/faroseguro/` — child theme WordPress.
- `wp-content/themes/faroseguro/functions.php` — CPTs, taxonomias, metaboxes, schema e helpers editoriais.
- `wp-content/themes/faroseguro/inc/ads.php` — configuracao do Google AdSense.
- `wp-content/themes/faroseguro/inc/seo-readiness.php` — SEO tecnico e painel de prontidao para AdSense.
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
3. Ative o tema `Faro Seguro` no painel do WordPress.

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
3. Aponte o domínio para o app ou use a URL gerada pelo Coolify.
