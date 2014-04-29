<?php
error_reporting(E_ALL);

function quoteWords_1($text)
{
    if (!function_exists ('quoteWordsHelper')) {
        function quoteWordsHelper($string) {
            return preg_replace('/(\w)/','"$1"',$string);
        }
    }
    return array_map('quoteWordsHelper', $text);
}

function quoteWords($text)
{
    return array_map(function ($string) {
            return preg_replace('/(\w)/','"$1"',$string);
        }, $text);
}

$t = [
    'mkdir mv cp',
    'cd dir exit',
    ];
print_r(quoteWords($t));

