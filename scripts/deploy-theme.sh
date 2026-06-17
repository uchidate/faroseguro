#!/bin/bash
# Deploy do tema guiaantifraude para o container WordPress em produção.
# Executado via SSH pelo GitHub Actions.
set -euo pipefail

CONTAINER="wordpress-pjn7ad04hr2r75ocaa68yeki"
REMOTE_THEME="/var/www/html/wp-content/themes/guiaantifraude"
TMP_TAR="/tmp/guiaantifraude-theme.tar.gz"
TMP_DIR="/tmp/guiaantifraude-theme"

echo "→ Verificando container..."
if ! docker inspect "$CONTAINER" > /dev/null 2>&1; then
  echo "ERRO: Container $CONTAINER não encontrado."
  exit 1
fi

echo "→ Extraindo tema do arquivo recebido..."
rm -rf "$TMP_DIR"
mkdir -p "$TMP_DIR"
tar xzf "$TMP_TAR" -C "$TMP_DIR"

echo "→ Copiando tema para o container (sem downtime)..."
docker cp "$TMP_DIR/guiaantifraude/." "$CONTAINER:$REMOTE_THEME/"

echo "→ Ajustando permissões..."
docker exec "$CONTAINER" chown -R www-data:www-data "$REMOTE_THEME"

echo "→ Limpando temporários..."
rm -rf "$TMP_TAR" "$TMP_DIR"

echo "✓ Deploy concluído."
