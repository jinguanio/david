#!/bin/bash

CFLAGS='-I/usr/local/eyou/toolmail/opt/include' \
CPPFLAGS='-I/usr/local/eyou/toolmail/opt/include' \
LDFLAGS='-L/usr/local/eyou/toolmail/opt/lib -Wl,-R/usr/local/eyou/toolmail/opt/lib' \
./configure \
--prefix=/usr/local/eyou/toolmail/opt/php \
--with-apxs2=/usr/local/eyou/toolmail/opt/bin/apxs \
--with-config-file-path=/usr/local/eyou/toolmail/etc/php \
--with-openssl \
--with-zlib \
--with-zlib-dir \
--without-iconv \
--with-freetype-dir \
--with-gettext \
--enable-mbstring \
--with-curl=/usr/local/eyou/toolmail/opt \
--with-iconv=/usr/local/eyou/toolmail/opt \
--with-iconv-dir=/usr/local/eyou/toolmail/opt \
--with-mysql=mysqlnd \
--with-mysqli=mysqlnd \
--with-pdo-mysql=mysqlnd \
--with-kerberos \
--with-gmp \
--enable-zip \
--enable-pcntl \
--enable-shmop \
--enable-bcmath \
--enable-soap \
--enable-http \
--enable-redis \
--enable-sockets \
--enable-swoole-debug \
--with-swoole \
--with-http-curl-requests=/usr/local/eyou/toolmail/opt \
--with-http-curl-libevent=/usr/local/eyou/toolmail/opt \
--with-http-zlib-compression \
--with-http-magic-mime=/usr/local/eyou/toolmail/opt \
--with-libevent=/usr/local/eyou/toolmail/opt
