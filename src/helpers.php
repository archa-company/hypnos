<?php

use Morpheus\Shared\Helper;
use Morpheus\Shared\Dev;

if (!function_exists('dd')) {
    function dd()
    {
        Dev::dd(func_get_args());
    }
}

if (!function_exists('debug')) {
    function debug()
    {
        Dev::debug(func_get_args());
    }
}

if (!function_exists('dump')) {
    function dump($data)
    {
        Dev::dump($data);
    }
}

if (!function_exists('redirectTo')) {
    function redirectTo($url, $status = 301)
    {
        Helper::redirectTo($url, $status);
    }
}
