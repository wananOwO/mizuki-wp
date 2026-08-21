#!/usr/bin/env bash
set -euo pipefail
ROOT="e:/code/mizuki-wp"
exec php -c "$ROOT/tools/local-env/php.ini" -S 127.0.0.1:8080 -t "$ROOT/.wp-local/wordpress" "$ROOT/tools/local-env/router.php"
