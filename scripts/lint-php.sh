#!/bin/sh
set -eu

docker run --rm \
  -v "$PWD:/app" \
  -w /app \
  wordpress:latest \
  sh -lc 'find wp-content/themes/faroseguro -name "*.php" -print0 | xargs -0 -n 1 php -l'
