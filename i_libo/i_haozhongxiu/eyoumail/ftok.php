<?php
$bin = pack('NN', 78 ,23);
$array = unpack('Noo/Nss', $bin);
print_r($array);
?> 


