#!/bin/bash
set -Eeuo pipefail

# This database is deliberately disposable and contains fictional demo records.
# No production database, external API secret or host-machine database is used.
export PORT="${PORT:-10000}"
[[ "$PORT" =~ ^[0-9]+$ ]] && (( PORT >= 1024 && PORT <= 65535 )) || {
    echo 'Invalid web-service port.' >&2; exit 1;
}
export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-}}"
case "$APP_URL" in https://*) ;; *) echo 'Set canonical HTTPS APP_URL or RENDER_EXTERNAL_URL.' >&2; exit 1 ;; esac
export APP_URL="${APP_URL%/}/"
export APP_SESSION_PATH=/var/lib/school-demo/sessions
export CI_ENV=production
export DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=school_demo DB_USER=school_app
export DB_PASSWORD="$(php -r 'echo bin2hex(random_bytes(24));')"
export SMART_SCHOOL_APP_KEY="${SMART_SCHOOL_APP_KEY:-$(php -r 'echo bin2hex(random_bytes(32));')}"

MYSQL_PID=''
APACHE_PID=''
cleanup() {
    trap - EXIT INT TERM
    [ -z "$APACHE_PID" ] || kill -TERM "$APACHE_PID" 2>/dev/null || true
    [ -z "$MYSQL_PID" ] || kill -TERM "$MYSQL_PID" 2>/dev/null || true
    wait 2>/dev/null || true
}
trap cleanup EXIT
trap 'exit 0' INT TERM

mkdir -p /run/mysqld /var/lib/demo-mysql "$APP_SESSION_PATH"
chown mysql:mysql /run/mysqld /var/lib/demo-mysql
chown www-data:www-data "$APP_SESSION_PATH"

if [ ! -d /var/lib/demo-mysql/mysql ]; then
    mariadb-install-db --no-defaults --user=mysql --datadir=/var/lib/demo-mysql \
        --auth-root-authentication-method=socket --skip-test-db >/dev/null
fi
mariadbd --defaults-file=/etc/school-demo-mariadb.cnf --user=mysql &
MYSQL_PID=$!

ready=0
for attempt in $(seq 1 90); do
    if mariadb-admin --no-defaults --protocol=socket --socket=/run/mysqld/mysqld.sock -uroot ping --silent >/dev/null 2>&1; then
        ready=1; break
    fi
    if ! kill -0 "$MYSQL_PID" 2>/dev/null; then
        echo 'The demo database exited during initialization.' >&2
        tail -n 30 /var/lib/demo-mysql/server-error.log >&2 || true
        exit 1
    fi
    sleep 1
done
[ "$ready" -eq 1 ] || { echo 'The demo database did not become ready.' >&2; exit 1; }

# Secrets are passed on stdin, never in command arguments or application logs.
mariadb --no-defaults --protocol=socket --socket=/run/mysqld/mysqld.sock -uroot <<SQL
CREATE DATABASE IF NOT EXISTS school_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'school_app'@'127.0.0.1' IDENTIFIED BY '$DB_PASSWORD';
ALTER USER 'school_app'@'127.0.0.1' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON school_demo.* TO 'school_app'@'127.0.0.1';
SQL

if [ ! -f /var/lib/demo-mysql/.school-seeded ]; then
    mariadb --no-defaults --protocol=socket --socket=/run/mysqld/mysqld.sock -uroot school_demo < /opt/demo/database.sql
    mariadb --no-defaults --protocol=socket --socket=/run/mysqld/mysqld.sock -uroot school_demo <<'SQL'
UPDATE staff SET password='!PUBLIC-DEMO-OWNER-DISABLED!' WHERE id=1;
SQL
    touch /var/lib/demo-mysql/.school-seeded
    echo 'Fictional demonstration database initialized.'
fi

printf 'Listen %s\n' "$PORT" > /etc/apache2/ports.conf
sed -i "s/RENDER_PORT/$PORT/g" /etc/apache2/sites-available/000-default.conf
apache2ctl configtest
apache2-foreground &
APACHE_PID=$!
echo 'School demonstration web service started.'

# A stopped child must not leave a superficially healthy half-running service.
set +e
wait -n "$MYSQL_PID" "$APACHE_PID"
status=$?
set -e
echo 'A demonstration service process stopped; shutting down.' >&2
if [ "$status" -eq 0 ]; then exit 1; else exit "$status"; fi
