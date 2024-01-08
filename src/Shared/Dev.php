<?php

namespace Morpheus\Shared;

class Dev
{

    public static function isDevMode()
    {
        return defined('DEV_MODE') && \DEV_MODE;
    }

    public static function isDisableAds()
    {
        return defined('DISABLE_ADS') && \DISABLE_ADS;
    }

    public static function debug()
    {
        echo '<pre style="background-color: #222; color: #eee; padding: 15px; border-radius: 5px;">';
        array_map(function ($x) {
            print_r($x);
        }, func_get_args());
        echo '</pre>';
    }

    public static function dump($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    public static function dd()
    {
        array_map(function ($x) {
            self::debug($x);
        }, func_get_args());
        die;
    }
}
