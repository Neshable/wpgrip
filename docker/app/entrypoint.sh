#!/usr/bin/env bash
set -e

# ──────────────────────────────────────────────────────────────────────
# Wait for MySQL to be ready
# ──────────────────────────────────────────────────────────────────────
until php -r "new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-laravel}', '${DB_USERNAME:-laravel}', '${DB_PASSWORD:-}');" 2>/dev/null; do
    echo "[entrypoint] Waiting for MySQL at ${DB_HOST:-mysql}..."
    sleep 2
done
echo "[entrypoint] MySQL is ready."

# ──────────────────────────────────────────────────────────────────────
# If a command was passed (horizon / scheduler / artisan ...)
# skip the boot sequence and run it directly as www-data.
# ──────────────────────────────────────────────────────────────────────
if [ $# -gt 0 ]; then
    exec gosu www-data "$@"
fi

# ──────────────────────────────────────────────────────────────────────
# Full boot — php-fpm container only
# ──────────────────────────────────────────────────────────────────────
# Fix storage permissions
chown -R www-data:www-data storage bootstrap/cache

# Run migrations (idempotent)
gosu www-data php artisan migrate --force

# Warm caches
gosu www-data php artisan config:cache
gosu www-data php artisan route:cache
gosu www-data php artisan view:cache  || echo "[entrypoint] view:cache had errors (non-fatal)"
gosu www-data php artisan event:cache || echo "[entrypoint] event:cache had errors (non-fatal)"

echo "[entrypoint] Boot complete. Starting supervisor (php-fpm + sshd)."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
