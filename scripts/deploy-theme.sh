#!/bin/bash
# Deploy do tema via rsync + docker cp.
# Recebe os arquivos do CI runner via rsync, copia para o container sem downtime.
# Variáveis esperadas: THEME_SRC (dir local com arquivos do tema)
set -euo pipefail

CONTAINER="wordpress-pjn7ad04hr2r75ocaa68yeki"
REMOTE_TMP="/tmp/guiaantifraude-theme"
DEST="/var/www/html/wp-content/themes/guiaantifraude"

echo "→ Copiando arquivos do tema para o container..."
docker cp "${REMOTE_TMP}/." "${CONTAINER}:${DEST}/"

echo "→ Ajustando permissões..."
docker exec "${CONTAINER}" chown -R www-data:www-data "${DEST}"

echo "→ Limpando temp..."
rm -rf "${REMOTE_TMP}"

echo "✓ Deploy concluído."
