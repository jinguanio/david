#!/bin/bash

FLAGS='-I/usr/local/eyou/toolmail/opt/include' \
CPPFLAGS='-I/usr/local/eyou/toolmail/opt/include' \
LDFLAGS='-L/usr/local/eyou/toolmail/opt/lib -Wl,-R/usr/local/eyou/toolmail/opt/lib' \
./configure \
--with-php-config=/usr/local/eyou/toolmail/opt/php/bin/php-config \
--disable-event-sockets \
--with-event-core \
--with-event-extra \
--with-event-openssl \
--with-event-libevent-dir=/usr/local/eyou/toolmail/opt 
