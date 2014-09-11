<?php
try {
    throw new Exception('error');
} catch (Exception $e) {
    echo "exception\n";
} 
finally {
    echo "finally\n";
}

