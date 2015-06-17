#!/bin/bash

CFLAGS='-I/usr/local/eyou/toolmail/opt/include' \
CPPFLAGS='-I/usr/local/eyou/toolmail/opt/include' \
LDFLAGS='-L/usr/local/eyou/toolmail/opt/lib -Wl,-R/usr/local/eyou/toolmail/opt/lib' \
./configure \
--prefix=/usr/local/eyou/toolmail/opt \
