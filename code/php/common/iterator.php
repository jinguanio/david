<?php
error_reporting(E_ALL);

$it = new RecursiveArrayIterator(array(  
    'A',  
    'B',  
    array(  
        'C',  
        'D'  
    ),  
    array(  
        array(  
            'E',  
            'F'  
        ),  
        array(  
            'G',  
            'H',  
            'I'  
        )  
    )  
));  

// $it is a RecursiveIterator but also an Iterator,  
// so it loops normally over the four elements  
// of the array.  
echo "Foreach over a RecursiveIterator: \n";  
foreach ($it as $value) {  
    print_r($value);  
    // but RecursiveIterators specify additional  
    // methods to explore children nodes  
    $children = $it->hasChildren() ? '{Yes}' : '{No}';  
    echo $children, ' ';  
}  
echo "\n";  

// we can bridge it to a different contract via  
// a RecursiveIteratorIterator, whose cryptic name  
// should be read as 'an Iterator that spans over  
// a RecursiveIterator'.  
echo "Foreach over a RecursiveIteratorIterator: \n";  
foreach (new RecursiveIteratorIterator($it) as $value) {  
    echo $value;  
}  
echo "\n"; 

