#!/bin/sh
set -eu

for name in DB_HOST DB_PORT DB_NAME DB_USER DB_PASSWORD APP_URL SMART_SCHOOL_APP_KEY; do
    eval "value=\${$name:-}"
    if [ -z "$value" ]; then
        echo "Required environment variable is missing: $name" >&2
        exit 1
    fi
done

case "$APP_URL" in
    http://*|https://*) ;;
    *) echo "APP_URL must start with http:// or https://" >&2; exit 1 ;;
esac

if [ "${#SMART_SCHOOL_APP_KEY}" -lt 32 ]; then
    echo "SMART_SCHOOL_APP_KEY must contain at least 32 random characters." >&2
    exit 1
fi

mkdir -p /var/www/html/application/tmp /var/www/html/application/logs \
    /var/www/html/application/cache /var/www/html/uploads /var/www/html/temp \
    /var/lib/school-demo/sessions /var/www/html/backend/captcha_images
chown www-data:www-data /var/www/html/application/tmp /var/www/html/application/logs \
    /var/www/html/application/cache /var/www/html/uploads /var/www/html/temp \
    /var/lib/school-demo/sessions /var/www/html/backend/captcha_images

# Wait without logging database credentials. Compose also waits for DB initialization.
attempt=0
until php -r '
mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASSWORD"), getenv("DB_NAME"), (int)getenv("DB_PORT"));
exit($db->connect_errno ? 1 : 0);
'; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 60 ]; then
        echo "Database is not ready after 120 seconds; inspect database service logs." >&2
        exit 1
    fi
    sleep 2
done

exec docker-php-entrypoint "$@"
