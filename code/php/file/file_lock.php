<?php
$file = 'helper/text';   
$fp = fopen($file , 'w');   
if(flock($fp , LOCK_EX)){   
    fwrite($fp , "abc\n");   
    sleep(10);   
    fwrite($fp , "123\n");   
    flock($fp , LOCK_UN);   
}   
fclose($fp);   

