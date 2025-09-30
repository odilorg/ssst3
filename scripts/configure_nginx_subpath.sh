#!/usr/bin/env bash
set -euo pipefail

# Configure Laravel app to run under a subpath and update Nginx accordingly.
#
# Usage:
#   sudo bash configure_nginx_subpath.sh \
#     --app-dir /var/www/tour_app \
#     --app-url https://62.72.22.205/tour_app \
#     --site-file /etc/nginx/sites-available/default \
#     --php-fpm-sock /run/php/php8.2-fpm.sock
#
# All flags are optional; sensible defaults are provided.

APP_DIR="/var/www/tour_app"
APP_URL="https://62.72.22.205/tour_app"
SITE_FILE="/etc/nginx/sites-available/default"
PHP_FPM_SOCK="/run/php/php8.2-fpm.sock"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --app-dir) APP_DIR="$2"; shift 2;;
    --app-url) APP_URL="$2"; shift 2;;
    --site-file) SITE_FILE="$2"; shift 2;;
    --php-fpm-sock) PHP_FPM_SOCK="$2"; shift 2;;
    *) echo "Unknown option: $1" >&2; exit 1;;
  esac
done

echo "[1/4] Updating APP_URL in ${APP_DIR}/.env to ${APP_URL}"
cd "$APP_DIR"
if grep -q '^APP_URL=' .env 2>/dev/null; then
  sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env
else
  echo "APP_URL=${APP_URL}" >> .env
fi

php artisan config:clear
php artisan optimize

echo "[2/4] Writing Nginx snippet for subpath to /etc/nginx/snippets/tour_app_subpath.conf"
cat >/etc/nginx/snippets/tour_app_subpath.conf <<'EOF'
location ^~ /tour_app {
    alias ${APP_DIR}/public;
    index index.php;
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ ^/tour_app/(.+\.php)$ {
    alias ${APP_DIR}/public/$1;
    include snippets/fastcgi-php.conf;
    fastcgi_param SCRIPT_FILENAME $request_filename;
    fastcgi_pass unix:${PHP_FPM_SOCK};
}
EOF

echo "[3/4] Ensuring snippet is included in ${SITE_FILE}"
if ! grep -q 'tour_app_subpath.conf' "$SITE_FILE"; then
  # Insert include inside the first server { } block
  awk '
  BEGIN { in_server = 0 }
  /server[[:space:]]*\{/ { if (!first_server) { in_server = 1; first_server=1 } }
  in_server && /^\}/ && !done {
    print "    include /etc/nginx/snippets/tour_app_subpath.conf;";
    done=1
  }
  { print }
  ' "$SITE_FILE" >"${SITE_FILE}.tmp" && mv "${SITE_FILE}.tmp" "$SITE_FILE"
fi

echo "[4/4] Testing and reloading Nginx"
nginx -t
systemctl reload nginx

echo "Done. App should be available at: ${APP_URL}"


