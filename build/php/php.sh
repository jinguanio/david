#!/bin/bash

#
# 注意：
# (1) 多行的 ./configure 中间不能存在 # 注释语句
# (2) 多行的 ./configure 中间不能存在空行
#
# 可以通过 sh -x 命令来查看 bash 执行的语句是否正确
#
# example:
#     sh -x ~/xb/php.sh 2>&1 | tee /tmp/a
#

press() {
    if [ -z $1 ]; then
        echo "Press any key to continue..."
        read -n 1
    fi
}

if [ -d '/tmp/php' ]; then
    sudo rm -fr /tmp/php
    echo "delete /tmp/php succ"
    press $1
fi

cd /home/libo/source/c
rm -fr php-5.4.30
cp -a php-5.4.30.origin php-5.4.30
echo "copy php-5.4.30 succ"
press $1

opt="/usr/local/eyou/toolmail/opt"
prefix="/tmp/php"
conf_file="/tmp/php/etc"

cd /home/libo/source/c/php-5.4.30

CFLAGS="-I${opt}/include" \
CPPFLAGS="-I${opt}/include" \
LDFLAGS="-L${opt}/lib -Wl,-R${opt}/lib" \
./configure \
--prefix=${prefix} \
--with-config-file-path=${conf_file} \
--with-openssl \
--with-zlib \
--with-zlib-dir \
--without-iconv \
--with-freetype-dir \
--with-gettext \
--with-curl=${opt} \
--with-iconv=${opt} \
--with-iconv-dir=${opt} \
--with-mysql=mysqlnd \
--with-mysqli=mysqlnd \
--with-pdo-mysql=mysqlnd \
--with-kerberos \
--with-gmp \
--enable-mbstring \
--enable-zip \
--enable-pcntl \
--enable-shmop \
--enable-bcmath \
--enable-soap \
--enable-igbinary \
--enable-redis-igbinary \
--enable-redis \
--disable-redis-session \
--enable-redis-igbinary \
--enable-embase \
--enable-raphf \
--enable-propro \
--enable-sockets \
--with-http \
--with-http-zlib-dir=${opt} \
--with-http-libcurl-dir=${opt} \
--with-http-libevent-dir=${opt} \
#--with-apxs2=${opt}/bin/apxs \

if [ $? -eq 0 ]; then
    echo "confiure succ"
else
    echo "confiure succ"
fi
press $1

make -j 2
if [ $? -eq 0 ]; then
    echo "make succ"
else
    echo "make fail"
fi
press $1

make install
if [ $? -eq 0 ]; then
    echo "make install succ"
else
    echo "make install fail"
fi
press $1

cp php.ini-development ${conf_file}/php.ini
if [ $? -eq 0 ]; then
    echo "create php.ini succ"
else
    echo "create php.ini fail"
fi
press $1

cd ${prefix}
mkdir log
if [ $? -eq 0 ]; then
    echo "create log succ"
else
    echo "create log fail"
fi
