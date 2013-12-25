<?php
$file = 'helper/text';   
$fp = fopen($file , 'r');   
if(flock($fp , LOCK_SH | LOCK_NB)){   
    //if(flock($fp , LOCK_SH)){   
    echo fread($fp , 100);   
    flock($fp , LOCK_UN);   
} else{   
    echo "Lock file failed...\n";   
}   
fclose($fp); 
