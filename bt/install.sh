#
# Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
#

#获取主域名(网站名称)
domain=$1
#获取配置文件位置
config_file=/www/server/panel/vhost/nginx/${domain}.conf
#获取PHP版本
php_version=$(cat $config_file|grep 'enable-php'|grep -Eo "[0-9]+"|head -n 1)
#获取PHP执行路径
php_bin=/www/server/php/$php_version/bin/php
root_path=$(pwd)
${php_bin} "${root_path}/install.php" "${config_file}"
chown -R www:www "${root_path}"