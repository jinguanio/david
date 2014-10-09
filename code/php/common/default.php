<?php
function t($a, $b = 1, $c = 2)
{
    print_r(func_get_args());
}

t(1,1, 3);

