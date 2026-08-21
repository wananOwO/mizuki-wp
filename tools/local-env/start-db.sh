#!/usr/bin/env bash
set -euo pipefail
BIN="$(ls -d "/c/Program Files/MariaDB"*/bin | head -1)"
exec "$BIN/mysqld.exe" --datadir="$(cygpath -w "e:/code/mizuki-wp/.wp-local/db-data")" --port=3307 --console
