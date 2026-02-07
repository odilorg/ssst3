#!/usr/bin/env bash
# ============================================================
# deploy.sh — Production-safe deploy for Jahongir Travel staging
#
# Usage:
#   chmod +x deploy.sh
#   ./deploy.sh
#
# Or run remotely:
#   ssh -i /path/to/key -p 2222 root@62.72.22.205 \
#       "cd /domains/staging.jahongir-travel.uz && bash deploy.sh"
#
# Logs: /var/log/jt_staging_deploy.log
# Lock: /tmp/jt_staging_deploy.lock (flock, auto-released)
# ============================================================

set -euo pipefail

# ── Variables ───────────────────────────────────────────────
APP_DIR="/domains/staging.jahongir-travel.uz"
BRANCH="master"
PHP_BIN="php8.3"
LOCK_FILE="/tmp/jt_staging_deploy.lock"
LOG_FILE="/var/log/jt_staging_deploy.log"
BYPASS_SECRET="deploy-bypass-$(date +%s)"
APP_IS_DOWN=false

# ── Logging ─────────────────────────────────────────────────
log() {
    local msg="[$(date '+%Y-%m-%d %H:%M:%S')] $*"
    echo "$msg"
    echo "$msg" >> "$LOG_FILE"
}

banner() {
    log "========================================"
    log "  $*"
    log "========================================"
}

# ── Failure trap ────────────────────────────────────────────
cleanup() {
    local exit_code=$?
    if [ $exit_code -ne 0 ]; then
        log "DEPLOY FAILED (exit code $exit_code) at line ${BASH_LINENO[0]:-unknown}"
        log "Last command: ${BASH_COMMAND:-unknown}"
    fi
    if [ "$APP_IS_DOWN" = true ]; then
        log "Bringing app back up..."
        cd "$APP_DIR"
        $PHP_BIN artisan up 2>>"$LOG_FILE" || true
        APP_IS_DOWN=false
        log "App is back up."
    fi
    if [ $exit_code -ne 0 ]; then
        log "Deploy FAILED. Check $LOG_FILE for details."
    fi
}
trap cleanup EXIT

# ── Acquire deploy lock ─────────────────────────────────────
exec 200>"$LOCK_FILE"
if ! flock -n 200; then
    echo "Another deploy is already running (lock: $LOCK_FILE). Aborting."
    exit 1
fi

# ── Start ───────────────────────────────────────────────────
banner "DEPLOY STARTED"
log "Branch: $BRANCH"
log "App dir: $APP_DIR"
log "PHP: $($PHP_BIN -v 2>/dev/null | head -1)"
log "Node: $(node -v 2>/dev/null)"

cd "$APP_DIR"

# ── Step 1: Git update ──────────────────────────────────────
banner "Step 1/9: Git update"
log "Fetching origin..."
git fetch origin 2>>"$LOG_FILE"

log "Resetting to origin/$BRANCH..."
git reset --hard "origin/$BRANCH" 2>>"$LOG_FILE"

log "Cleaning untracked files (preserving .env, storage/app)..."
git clean -fd \
    --exclude=.env \
    --exclude=storage/app/ \
    --exclude=storage/logs/ \
    --exclude=storage/framework/sessions/ \
    --exclude=storage/framework/cache/ \
    2>>"$LOG_FILE"

COMMIT=$(git log -1 --format='%h %s')
log "Now at: $COMMIT"

# ── Step 2: Maintenance mode ────────────────────────────────
banner "Step 2/9: Maintenance mode"
$PHP_BIN artisan down --secret="$BYPASS_SECRET" 2>>"$LOG_FILE" || true
APP_IS_DOWN=true
log "App is down. Bypass: /app/$BYPASS_SECRET"

# ── Step 3: Composer ─────────────────────────────────────────
banner "Step 3/9: Composer install"
composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev \
    2>>"$LOG_FILE"
log "Composer done."

# ── Step 4: Frontend build ──────────────────────────────────
banner "Step 4/9: Frontend build (Vite)"
if [ -f "package-lock.json" ]; then
    log "Found package-lock.json, running npm ci..."
    npm ci 2>>"$LOG_FILE"
else
    log "No package-lock.json, running npm install..."
    npm install 2>>"$LOG_FILE"
fi

npm run build 2>>"$LOG_FILE"
log "Frontend build done."

# ── Step 5: Clear caches ────────────────────────────────────
banner "Step 5/9: Clear caches"
$PHP_BIN artisan config:clear 2>>"$LOG_FILE"
$PHP_BIN artisan route:clear 2>>"$LOG_FILE"
$PHP_BIN artisan view:clear 2>>"$LOG_FILE"
$PHP_BIN artisan cache:clear 2>>"$LOG_FILE"
log "All caches cleared."

# ── Step 6: Database migrations ─────────────────────────────
banner "Step 6/9: Database migrations"
$PHP_BIN artisan migrate --force 2>>"$LOG_FILE"
log "Migrations done."

# ── Step 7: Rebuild caches ──────────────────────────────────
banner "Step 7/9: Rebuild caches"
$PHP_BIN artisan config:cache 2>>"$LOG_FILE"
log "Config cached."

$PHP_BIN artisan route:cache 2>>"$LOG_FILE"
log "Routes cached."

# view:cache may fail due to Filament v4 component issue — tolerate it
if $PHP_BIN artisan view:cache 2>>"$LOG_FILE"; then
    log "Views cached."
else
    log "WARNING: view:cache failed (known Filament v4 issue). Skipping."
fi

# ── Step 8: Permissions ─────────────────────────────────────
banner "Step 8/9: Fix permissions"
chgrp -R www-data storage bootstrap/cache 2>>"$LOG_FILE"
chmod -R ug+rwX storage bootstrap/cache 2>>"$LOG_FILE"
log "Permissions fixed (storage + bootstrap/cache -> www-data)."

# ── Step 9: Go live ─────────────────────────────────────────
banner "Step 9/9: Go live"
$PHP_BIN artisan up 2>>"$LOG_FILE"
APP_IS_DOWN=false
log "App is UP."

# ── Health check ────────────────────────────────────────────
banner "Health check"
$PHP_BIN artisan --version 2>>"$LOG_FILE" | tee -a "$LOG_FILE"

# Quick HTTP check (Nginx serves locally)
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1" 2>/dev/null || echo "000")
if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ]; then
    log "HTTP check: OK ($HTTP_STATUS)"
else
    log "WARNING: HTTP check returned $HTTP_STATUS (may need Nginx reload)"
fi

# ── Done ────────────────────────────────────────────────────
banner "DEPLOY COMPLETED SUCCESSFULLY"
log "Commit: $COMMIT"
log "Duration: ${SECONDS}s"
