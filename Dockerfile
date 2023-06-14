FROM alpine:3.15
WORKDIR /www
VOLUME ["/www","/var/lib/mysql"]
RUN echo -e "https://mirrors.ustc.edu.cn/alpine/v3.15/main\nhttps://mirrors.ustc.edu.cn/alpine/v3.15/community\nhttps://mirrors.ustc.edu.cn/alpine/v3.15/main" > /etc/apk/repositories && \
        apk add  --no-cache \
    curl  \
    nginx \
    bash  \
    php7  \
    php7-mysqli  \
    php7-pdo_mysql  \
    php7-mbstring  \
    php7-json  \
    php7-zlib  \
    php7-gd  \
    php7-intl  \
    php7-session  \
    php7-fpm  \
    php7-curl  \
    php7-tokenizer \
    php7-posix  \
    php7-sockets  \
    php7-fileinfo  \
    php7-ctype  \
    php7-bcmath  \
    php7-openssl  \
    php7-dom  \
    php7-iconv  \
    php7-zip \
    mysql mysql-client \
    supervisor  && \
    ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && \
    echo "Asia/Shanghai" >> /etc/timezone && \
    mysql_install_db --user=nobody --datadir=/var/lib/mysql && \
    rm -rf /var/cache/apk/* /tmp/* && \
    mkdir -p /var/log/mysql && mkdir -p /run/mysqld/



# 配置基础运行环境
COPY docker/config/nginx.conf /etc/nginx/nginx.conf
COPY docker/config/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY docker/config/supervisord.conf /etc/supervisord.conf
COPY docker/config/mysql.cnf /etc/mysql/my.cnf
COPY docker/config/login.sh /etc/profile
COPY src/. /www
EXPOSE 80 3306
RUN  chown -R nobody:nobody  /www /run /var/lib/nginx /var/log/nginx  /var/log/mysql /var/lib/mysql  /var/lib/nginx /var/lib/php7 /var/log/php7 && touch /www/docker_runtime
# 设置MySQL文件和文件夹的权限
USER nobody
# 配置python
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]
#CMD ["/sbin/init"]


