<?php

$count = 2;
echo pack('N', $count);
print_r(unpack('N', pack('N', $count)));
