#!/usr/bin/env bash
set -euo pipefail

# ========= CONFIG =========
APP_DIR="/var/www/tour_app"
REPO_URL="https://github.com/odilorg/ssst3.git"
BRANCH_NAME="feature/contract-based-pricing"

DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_NAME="ssst3"
DB_USER="ssst3"
DB_PASS="ssst3_pass"

PHP_VERSION="8.2"
PHP_FPM_SOCK="/run/php/php${PHP_VERSION}-fpm.sock"

SERVER_NAME="_"   # change to your domain if needed, e.g. example.com
# =========================

echo "[1/9] Installing required packages (Nginx, MySQL, PHP ${PHP_VERSION})..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y nginx mysql-server \
  php${PHP_VERSION} php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql \
  php${PHP_VERSION}-xml php${PHP_VERSION}-curl php${PHP_VERSION}-mbstring \
  php${PHP_VERSION}-zip unzip git curl

systemctl enable --now nginx
systemctl enable --now mysql
systemctl enable --now php${PHP_VERSION}-fpm

echo "[2/9] Creating application directory ${APP_DIR}..."
mkdir -p "${APP_DIR}"
chown -R root:www-data "${APP_DIR}"
chmod -R 775 "${APP_DIR}"

echo "[3/9] Cloning repository and checking out branch..."
if [ -d "${APP_DIR}/.git" ]; then
  cd "${APP_DIR}"
  git fetch --all --prune
  git checkout -B "${BRANCH_NAME}" "origin/${BRANCH_NAME}" || true
else
  git clone "${REPO_URL}" "${APP_DIR}"
  cd "${APP_DIR}"
  git checkout -B "${BRANCH_NAME}" "origin/${BRANCH_NAME}" || true
fi

echo "[4/9] Installing Composer (if missing) and PHP dependencies..."
if ! command -v composer >/dev/null 2>&1; then
  EXPECTED_SIGNATURE="$(curl -s https://composer.github.io/installer.sig)"
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_SIGNATURE="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
  if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
    echo "ERROR: Invalid composer installer signature" >&2
    rm -f composer-setup.php
    exit 1
  fi
  php composer-setup.php --install-dir=/usr/local/bin --filename=composer
  rm -f composer-setup.php
fi
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --prefer-dist --no-progress

echo "[5/9] Creating .env, app key, storage link, and permissions..."
[ -f .env ] || cp .env.example .env
php artisan key:generate --force
php artisan storage:link || true
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "[6/9] Provisioning MySQL database and user..."
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';" || true
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;"

echo "[7/9] Configuring .env to use MySQL..."
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env
sed -i "s/^DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i "s/^DB_PORT=.*/DB_PORT=${DB_PORT}/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env

echo "[8/9] Running migrations and optimizing..."
php artisan config:clear
php artisan migrate --force
php artisan optimize

echo "[9/9] Creating Nginx site and reloading..."
cat >/etc/nginx/sites-available/tour_app <<EOF
server {
    listen 80;
    server_name ${SERVER_NAME};
    root ${APP_DIR}/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:${PHP_FPM_SOCK};
    }

    location ~ /\.|^/\. {
        deny all;
    }

    access_log /var/log/nginx/tour_app_access.log;
    error_log  /var/log/nginx/tour_app_error.log;
}
EOF

ln -sf /etc/nginx/sites-available/tour_app /etc/nginx/sites-enabled/tour_app
nginx -t
systemctl reload nginx

echo "Deployment complete.\n- Path: ${APP_DIR}\n- Branch: ${BRANCH_NAME}\n- Nginx: enabled for ${SERVER_NAME} -> ${APP_DIR}/public\n- DB: ${DB_NAME} (user ${DB_USER})\n"


