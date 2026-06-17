#!/bin/bash
# Deploy do tema guiaantifraude para produção.
# Executa no servidor via GitHub Actions.
set -euo pipefail

VOLUME="pjn7ad04hr2r75ocaa68yeki_wordpress-files"
IMAGE="guiaantifraude:latest"

echo "→ Buildando imagem do GitHub..."
docker build -t "$IMAGE" https://github.com/uchidate/faroseguro.git#main

echo "→ Copiando tema para o volume (sem downtime)..."
docker run --rm \
  -v "${VOLUME}:/html" \
  "$IMAGE" \
  bash -c '
    cp -r /var/www/html/wp-content/themes/guiaantifraude/. /html/wp-content/themes/guiaantifraude/
    cp -r /var/www/html/wp-content/themes/faroseguro/. /html/wp-content/themes/faroseguro/
    chown -R www-data:www-data /html/wp-content/themes/guiaantifraude /html/wp-content/themes/faroseguro
  '

echo "✓ Deploy concluído."
