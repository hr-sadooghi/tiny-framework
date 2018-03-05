<?php

function get_numerics($str) //get number in string as string
{
    preg_match_all('/\d+/', $str, $matches);
    if (isset($matches[0][0]))
        return $matches[0][0];
    return "";
}

function getDomainFromURL()
{
    $host = str_replace(" ", "", $_SERVER['HTTP_HOST']);
    $host = str_replace("/**/", "", $host);
    $host = str_replace("www.", "", $host);
    return parse_url($host, PHP_URL_HOST);
}

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

function getUserIP()
{
    $TheIp = $_SERVER['REMOTE_ADDR'] ?? 'NO-IP';
    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
        $TheIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return trim($TheIp);
}
