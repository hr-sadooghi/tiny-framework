<?php

class PNumber
{
    public static $enDigit = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    public static $faDigit = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    public static function en2FaDigit($string)
    {
        return str_replace(PNumber::$enDigit, PNumber::$faDigit, $string);
    }

    public static function fa2EnDigit($string)
    {
        return str_replace(PNumber::$faDigit, PNumber::$enDigit, $string);
    }

    public static function thousandsSeparator($number)
    {

    }

    public static function digit2Word($number)
    {

    }

}