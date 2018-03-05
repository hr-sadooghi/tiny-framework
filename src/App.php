<?php

class App
{
    public static function getConfig($option)
    {
        return getenv($option);
    }

    public static function getEnv()
    {
        return self::getConfig('ENV');
    }

    public static function isDevEnv()
    {
        return self::getEnv() === 'DEV';
    }
}
