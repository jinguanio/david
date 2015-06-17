#!/bin/bash

CFLAGS='-I/usr/local/eyou/toolmail/opt/include' \
CPPFLAGS='-I/usr/local/eyou/toolmail/opt/include' \
LDFLAGS='-L/usr/local/eyou/toolmail/opt/lib -Wl,-R/usr/local/eyou/toolmail/opt/lib' \
./configure --with-php-config=/usr/local/eyou/toolmail/opt/php/bin/php-config \
--enable-xdebug \
--with-gnu-ld \
--with-libedit 
