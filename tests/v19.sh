#!/bin/bash
set -euo pipefail
umask 077

result=${TKL_TEST_RESULT:?TKL_TEST_RESULT is required}
password=${TKL_TEST_DB_PASS:?TKL_TEST_DB_PASS is required}
work=/run/tkl-v19-tests/mysql
database=tkl_v19_mysql_$$
table_name=persistence
client_conf=$work/client.cnf
password_file=$work/password

mysql_remote() {
    mysql --defaults-extra-file="$client_conf" --ssl "$@"
}

cleanup() {
    status=$?
    trap - EXIT
    mysql_remote --execute="DROP DATABASE IF EXISTS \`$database\`;" \
        >/dev/null 2>&1 || true
    rm -rf -- "$work"
    exit "$status"
}
trap cleanup EXIT

install -d -o root -g root -m 0700 "$work"
cat >"$client_conf" <<EOF
[client]
user=remote
password=$password
host=127.0.0.1
protocol=tcp
EOF
chmod 0600 "$client_conf"
printf '%s' "$password" >"$password_file"

php_fpm_service=$(systemctl list-unit-files 'php*-fpm.service' --no-legend |
    awk 'NR == 1 {print $1}')
test -n "$php_fpm_service"
systemctl --quiet is-active lighttpd.service mariadb.service \
    "$php_fpm_service" multi-user.target
grep -Fq 'Inithooks run completed' /var/log/inithooks.log

curl -kfsS https://127.0.0.1/ >"$work/landing.html"
grep -Eqi 'TurnKey.*(MySQL|MariaDB)|MySQL|MariaDB' "$work/landing.html"
curl -kfsS https://127.0.0.1:12322/ >"$work/adminer.html"
grep -Fqi Adminer "$work/adminer.html"

ssl_cipher=$(mysql_remote --batch --skip-column-names \
    --execute="SHOW STATUS LIKE 'Ssl_cipher';" | awk '{print $2}')
test -n "$ssl_cipher"
mysql_remote <<SQL
CREATE DATABASE \`$database\`;
CREATE TABLE \`$database\`.\`$table_name\` (message varchar(64) NOT NULL);
INSERT INTO \`$database\`.\`$table_name\` VALUES ('mysql-v19-persistence');
SQL

curl -kfsS -L -c "$work/cookies" -b "$work/cookies" \
    --data-urlencode 'auth[driver]=server' \
    --data-urlencode 'auth[server]=127.0.0.1' \
    --data-urlencode 'auth[username]=remote' \
    --data-urlencode "auth[password]@$password_file" \
    --data-urlencode "auth[db]=$database" \
    https://127.0.0.1:12322/ >"$work/adminer-login.html"
grep -Eqi 'MariaDB|MySQL' "$work/adminer-login.html"
grep -Fqi Logout "$work/adminer-login.html"
! grep -Eqi 'Access denied|Invalid credentials' "$work/adminer-login.html"

systemctl restart mariadb.service "$php_fpm_service" lighttpd.service
systemctl --quiet is-active mariadb.service "$php_fpm_service" lighttpd.service
test "$(mysql_remote --batch --skip-column-names \
    --execute="SELECT message FROM \`$database\`.\`$table_name\`;")" = \
    mysql-v19-persistence

mariadb_version=$(mysql_remote --batch --skip-column-names \
    --execute='SELECT VERSION();')
lighttpd_version=$(dpkg-query -W -f='${Version}' lighttpd)
php_version=$(dpkg-query -W -f='${Version}' php-fpm)
adminer_version=$(dpkg-query -W -f='${Version}' adminer)
for package in mariadb-server lighttpd php-fpm adminer; do
    candidate=$(apt-cache policy "$package" | awk '/Candidate:/ {print $2}')
    test -n "$candidate" && test "$candidate" != '(none)'
done
grep -Rqs '^Suites:.*trixie' /etc/apt/sources.list.d
if test -f /etc/apt/sources.list; then
    ! grep -qi bookworm /etc/apt/sources.list
fi
! grep -Rqi bookworm /etc/apt/sources.list.d
! grep -F -- "$password" /var/log/inithooks.log

cat >"$result" <<EOF
package_source=Debian Trixie APT packages for MariaDB, Lighttpd, PHP-FPM and Adminer
installed_version=MariaDB $mariadb_version; lighttpd $lighttpd_version; php-fpm $php_version; adminer $adminer_version
runtime_checks=normal firstboot completion, MariaDB remote-user TLS authentication, database create and query across service restart, web control panel, and authenticated Adminer HTTPS access
updater_command=apt-cache policy mariadb-server lighttpd php-fpm adminer
updater_result=eligible APT candidates found; installed packages unchanged
updater_channel=Debian and TurnKey Trixie signed APT repositories
integrity_evidence=installed dpkg state and configured signed Trixie Deb822 repositories; negotiated MariaDB TLS cipher $ssl_cipher; no Bookworm source remains
EOF
