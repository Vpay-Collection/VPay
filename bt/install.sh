#获取主域名(网站名称)
domain=$1
#获取配置文件位置
config_file=/www/server/panel/vhost/nginx/${domain}.conf
#获取PHP版本
php_version=$(cat $config_file|grep 'enable-php'|grep -Eo "[0-9]+"|head -n 1)
#获取PHP执行路径
php_bin=/www/server/php/$php_version/bin/php
root_path=$(cat $config_file|grep 'root '|awk '{print $2}'|sed "s/;//"|sed "s/public//")
${php_bin} "${root_path}/replace.php" "${config_file}"