#!/usr/bin/env bash
# WP-CLI wrapper for Mizuki dev environment (container: wp-manual)
# Usage: tools/wp-cli.sh <wp-cli-command-and-args>
# Example: tools/wp-cli.sh theme activate mizuki
set -euo pipefail
exec docker exec wp-manual wp --allow-root "$@"
