#!/bin/sh
file="/run/vm_init"
if [ -f "$file" ]; then
  echo "init success"
else
  touch file
  mkdir -p /run/mysqld/
  chmod -R 777 /run /var/lib/nginx /var/log/nginx /env /var/log/mysql /var/lib/mysql /var/lib/nginx /var/lib/php7 /var/log/php7
fi
/usr/bin/supervisord -c /etc/supervisord.conf