<?php

class PDate
{
// Copyright (C) 2009  Vahid sohrabloo (iranphp.org) 
// 
// This program is free software; you can redistribute it and/or 
// modify it under the terms of the GNU General Public License 
// as published by the Free Software Foundation; either version 2 
// of the License, or (at your option) any later version. 
// 
// This program is distributed in the hope that it will be useful, 
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
// GNU General Public License for more details. 
// 
// A copy of the GNU General Public License is available from: 
// 
//    <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">http://www.gnu.org/copyleft/gpl.html</a> 
// 


// Version 1.1

    public static $pdateWeekName = array(
        "شنبه",
        "یکشنبه",
        "دوشنبه",
        "سه شنبه",
        "چهارشنبه",
        "پنج شنبه",
        "جمعه");

    public static $pdateMonthName = array(
        "",
        "فروردین",
        "اردیبهشت",
        "خرداد",
        "تیر",
        "مرداد",
        "شهریور",
        "مهر",
        "آبان",
        "آذر",
        "دی",
        "بهمن",
        "اسفند");


    public static $MonthDays = array(0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

    public static function getMonthNameList()
    {
        return self::$pdateMonthName;
    }

    /**
     * Alias for getPDaysPerMonth
     * @param $monthNumber
     * @param $year
     * @return mixed
     */
    public static function getMonthDays($monthNumber, $year)
    {
        return self::getPDaysPerMonth($monthNumber, $year);
    }

    static function getPDaysPerMonth($monthNumber, $year)
    {
        $days_in_month = array(0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
        if (self::isKabise($year))
            $days_in_month[12] = 30;
        return $days_in_month[$monthNumber];
    }

    /**
     * Alias for getPMonthNameByNumber()
     * @param $month_number
     * @return string
     */
    public static function getMonthName($month_number)
    {
        return self::getPMonthNameByNumber($month_number);
    }

    static function getPMonthNameByNumber($month_number)
    {
        $month_number = intval($month_number);
        if ($month_number >= 1 && $month_number <= 12)
            return self::$pdateMonthName[$month_number];
        return "";
    }

    public static function miladi_to_shamsi_date($str)
    {
        $str = str_replace("/", "-", $str);
        $date_miladi_arr = explode("-", $str);
        $miladi_date = mktime(0, 0, 0, $date_miladi_arr[1], $date_miladi_arr[2], $date_miladi_arr[0]);
        return self::date("Y/m/d", $miladi_date);
    }

    public static function miladi_to_shamsi_date_ToFormat($str, $outputFormat = "Y/m/d")
    {
        $str = str_replace("/", "-", $str);
        $date_miladi_arr = explode("-", $str);
        $miladi_date = mktime(0, 0, 0, $date_miladi_arr[1], $date_miladi_arr[2], $date_miladi_arr[0]);
        $shamsi = self::date($outputFormat, $miladi_date);
        return $shamsi;
    }

    public static function miladiToShamsiDateTime($date_time)
    {
        if ($date_time === null || $date_time === '')
            return '';
        $date_time = explode(' ', $date_time);
        if (!$date_time)
            return '';
        $date_time[0] = self::miladi_to_shamsi_date($date_time[0]);
        return implode(' ', $date_time);
    }

    public static function miladiToShamsiDateTimeEnToFaDigit($date_time)
    {
        return PNumber::en2FaDigit(self::miladiToShamsiDateTime($date_time));
    }

    public static function shamsiToMiladiDateTimeFaToEnDigit($date_time)
    {
        $date_time = explode(' ', trim($date_time));
        if (!$date_time || $date_time[0] === '')
            return '';
        $date_time[0] = self::shamsi_to_miladi(farsi_to_english_digit($date_time[0]));
        return farsi_to_english_digit(implode(' ', $date_time));
    }

    public static function shamsi_to_miladi_splited($j_y, $j_m, $j_d, $seperator = "/")
    {
        $result = self::jalali_to_gregorian($j_y, $j_m, $j_d);
        if ($result[1] < 10)
            $result[1] = "0" . $result[1];
        if ($result[2] < 10)
            $result[2] = "0" . $result[2];
        return implode($seperator, $result);
    }

    public static function shamsi_to_miladi($str, $outSeparator = '/')
    {
        $str = str_replace("/", "-", $str);
        list($j_y, $j_m, $j_d) = explode('-', $str);
        $result = self::jalali_to_gregorian($j_y, $j_m, $j_d);
        if ($result[1] < 10)
            $result[1] = "0" . $result[1];
        if ($result[2] < 10)
            $result[2] = "0" . $result[2];
        return $result[0] . $outSeparator . $result[1] . $outSeparator . $result[2];
    }


    public static function date($format, $timestamp = "")
    {
        if ($timestamp === "") {
            $timestamp = time();
        }

        // Create need date parametrs
        $date = date("Y-m-d-w", $timestamp);
        list($gYear, $gMonth, $gDay, $gWeek) = explode('-', $date);
        list($pYear, $pMonth, $pDay) = self::gregorian_to_jalali($gYear, $gMonth, $gDay);
        $pWeek = $gWeek + 1;
        if ($pWeek == 7) $pWeek = 0;

        $lenghFormat = strlen($format);
        $i = 0;
        $result = "";
        while ($i < $lenghFormat) {
            $par = $format{$i};
            if ($par == '\\') {
                $result .= $format{++$i};
                $i++;
                continue;
            }
            switch ($par) {
                //Day
                case 'd':
                    $result .= ($pDay < 10) ? "0" . $pDay : $pDay;
                    break;

                case 'D':
                    $result .= substr(self::$pdateMonthName[$pWeek], 0, 2);
                    break;

                case 'j':
                    $result .= $pDay;
                    break;

                case 'l':
                    $result .= self::$pdateMonthName[$pWeek];
                    break;

                case 'N':
                    $result .= $pWeek + 1;
                    break;

                case 'w':
                    $result .= $pWeek;
                    break;

                case 'z':
                    $result .= self::DayOfYear($pYear, $pMonth, $pDay);
                    break;

                //Week
                case 'W':
                    $result .= ceil(self::DayOfYear($pYear, $pMonth, $pDay) / 7);
                    break;

                //Month
                case 'F':
                    $result .= self::$pdateMonthName[$pMonth];
                    break;

                case 'm':
                    $result .= ($pMonth < 10) ? "0" . $pMonth : $pMonth;
                    break;

                case 'M':
                    $result .= substr(self::$pdateMonthName[$pMonth], 0, 6);
                    break;

                case 'n':
                    $result .= $pMonth;
                    break;

                case 't':
                    $result .= (self::isKabise($pYear) and $pMonth == 12) ? 30 : self::$pdateMonthName[$pMonth];
                    break;

                //Years
                case 'L':
                    $result .= (int)self::isKabise($pYear);
                    break;

                case 'Y':
                case 'o':
                    $result .= $pYear;
                    break;

                case 'y':
                    $result .= substr($pYear, 2);
                    break;

                //Time
                case 'a':
                case 'A':
                    if (date('a', $timestamp) == 'am') {
                        $result .= ($par == 'a') ? 'ق.ظ' : 'قبل از ظهر';
                    } else {
                        $result .= ($par == 'a') ? 'ب.ظ' : 'بعد از ظهر';
                    }
                    break;

                case 'B':
                case 'g':
                case 'G':
                case 'h':
                case 'H':
                case 's':
                case 'u':
                case 'i':

                    //Timezone
                case 'e':
                case 'I':
                case 'O':
                case 'P':
                case 'T':
                case 'Z':
                    $result .= date($par, $timestamp);
                    break;

                //Full Date/Time

                case 'c':
                    $result .= $pYear . "-" . $pMonth . "-" . $pDay . "T" . date("H::i:sP", $timestamp);
                    break;

                case 'r':
                    $result .= substr(self::$pdateMonthName[$pWeek], 0, 2) . "، " . $pDay . " " . substr(self::$pdateMonthName[$pMonth], 0, 6) . " " . $pYear . " " . date("H::i:s P", $timestamp);
                    break;
                case 'U':
                    $result .= $timestamp;
                    break;
                default:
                    $result .= $par;


            }
            $i++;
        }
        return $result;


    }


    static function pstrftime($format, $timestamp = "")
    {
        if ($timestamp === "") {
            $timestamp = time();
        }
        // Create need date parametrs
        $date = date("Y-m-d-w", $timestamp);
        list($gYear, $gMonth, $gDay, $gWeek) = explode('-', $date);
        list($pYear, $pMonth, $pDay) = self::gregorian_to_jalali($gYear, $gMonth, $gDay);
        $pWeek = $gWeek + 1;
        if ($pWeek == 7) $pWeek = 0;

        $lenghFormat = strlen($format);
        $i = 0;
        $result = "";
        while ($i < $lenghFormat) {
            $par = $format{$i};
            if ($par == "%") {
                $type = $format{++$i};
                switch ($type) {
                    //Day
                    case 'a':
                        $result .= substr(self::$pdateMonthName[$pWeek], 0, 2);
                        break;

                    case 'A':
                        $result .= self::$pdateMonthName[$pWeek];
                        break;

                    case 'd':
                        $result .= ($pDay < 10) ? "0" . $pDay : $pDay;
                        break;

                    case 'e':
                        $result .= $pDay;
                        break;

                    case 'j':
                        $dayinM = self::DayOfYear($pYear, $pMonth, $pDay);
                        $result .= ($dayinM < 10) ? "00" . $dayinM : (($dayinM < 100) ? "0" . $dayinM : $dayinM);
                        break;

                    case 'u':
                        $result .= $pWeek + 1;
                        break;

                    case 'w':
                        $result .= $pWeek;
                        break;

                    //Week
                    case 'U':
                        $result .= floor(self::DayOfYear($pYear, $pMonth, $pDay) / 7);
                        break;

                    case 'V':
                    case 'W':
                        $result .= ceil(self::DayOfYear($pYear, $pMonth, $pDay) / 7);
                        break;

                    //Month
                    case 'b':
                    case 'h':
                        $result .= substr(self::$pdateMonthName[$pMonth], 0, 6);
                        break;

                    case 'B':
                        $result .= self::$pdateMonthName[$pMonth];
                        break;

                    case 'm':
                        $result .= ($pMonth < 10) ? "0" . $pMonth : $pMonth;
                        break;

                    //Year
                    case 'C':
                        $result .= ceil($pYear / 100);
                        break;

                    case 'g':
                    case 'y':
                        $result .= substr($pYear, 2);
                        break;

                    case 'G':
                    case 'Y':
                        $result .= $pYear;
                        break;

                    //Time
                    case 'H':
                    case 'I':
                    case 'l':
                    case 'M':
                    case 'R':
                    case 'S':
                    case 'T':
                    case 'X':
                    case 'z':
                    case 'Z':
                        $result .= strftime("%" . $type, $timestamp);
                        break;
                    case 'p':
                    case 'P':
                    case 'r':
                        if (date('a', $timestamp) == 'am') {
                            $result .= ($type == 'p') ? 'ق.ظ' : (($type == 'P') ? 'قبل از ظهر' : strftime("%I:%M:%S قبل از ظهر", $timestamp));
                        } else {
                            $result .= ($type == 'p') ? 'ب.ظ' : (($type == 'P') ? 'بعد از ظهر' : strftime("%I:%M:%S بعد از ظهر", $timestamp));
                        }
                        break;

                    //Time and Date Stamps
                    case 'c':
                        $result .= substr(self::$pdateMonthName[$pWeek], 0, 2) . " " . substr(self::$pdateMonthName[$pMonth], 0, 6) . " " . $pDay . " " . strftime("%T", $timestamp) . " " . $pYear;
                        break;

                    case 'D':
                    case 'x':
                        $result .= (($pMonth < 10) ? "0" . $pMonth : $pMonth) . "/" . (($pDay < 10) ? "0" . $pDay : $pDay) . "/" . substr($pYear, 2);
                        break;

                    case 'F':
                        $result .= $pYear . "-" . (($pMonth < 10) ? "0" . $pMonth : $pMonth) . "-" . (($pDay < 10) ? "0" . $pDay : $pDay);
                        break;

                    case 's':
                        $result .= $timestamp;
                        break;

                    //Miscellaneous
                    case 'n':
                        $result .= "\n";
                        break;

                    case 't':
                        $result .= "\t";
                        break;

                    case '%':
                        $result .= "%";
                        break;

                    default:
                        $result .= "%" . $type;


                }
            } else {
                $result .= $par;
            }
            $i++;
        }
        return $result;
    }

    public static function DayOfYear($pYear, $pMonth, $pDay)
    {
        $days = 0;
        for ($i = 1; $i < $pMonth; $i++) {
            $days += self::$MonthDays[$i];
        }
        return $days + $pDay;
    }

    public static function isKabise($year)
    {
        $mod = $year % 33;
        if ($mod == 1 or $mod == 5 or $mod == 9 or $mod == 13 or $mod == 17 or $mod == 22 or $mod == 26 or $mod == 30) return true;
        return false;
    }

    static function pmktime($hour = 0, $minute = 0, $second = 0, $month = 0, $day = 0, $year = 0, $is_dst = -1)
    {

        if ($hour == 0 && $minute == 0 && $second == 0 && $month == 0 && $day == 0 && $year == 0) return time();

        list($year, $month, $day) = self::jalali_to_gregorian($year, $month, $day);

        return mktime($hour, $minute, $second, $month, $day, $year, $is_dst);
    }

    public static function pcheckdate($month, $day, $year)
    {
        if ($month < 1 || $month > 12 || $year < 1 || $year > 32767 || $day < 1) {
            return false;
        }
        if ($day > self::$pdateMonthName[$month]) {
            if ($month != 12 && $day != 12 && !self::isKabise($year)) {
                return false;
            }
        }
        return true;
    }


    static function pgetdate($timestamp = "")
    {
        if ($timestamp === "")
            $timestamp = mktime();
        list($seconds, $minutes, $hours, $mday, $wday, $mon, $year, $yday, $weekday, $month) = explode("-", self::date("s-i-G-j-w-n-Y-z-l-F", $timestamp));
        return array(
            0 => $timestamp,
            "seconds" => $seconds,
            "minutes" => $minutes,
            "hours" => $hours,
            "mday" => $mday,
            "wday" => $wday,
            "mon" => $mon,
            "year" => $year,
            "yday" => $yday,
            "weekday" => $weekday,
            "month" => $month,
        );
    }


// Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// A copy of the GNU General Public License is available from:
//
//    <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">http://www.gnu.org/copyleft/gpl.html</a>
//


    static function div($a, $b)
    {
        return (int)($a / $b);
    }

    static function gregorian_to_jalali($g_y, $g_m, $g_d)
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);


        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + self::div($gy + 3, 4) - self::div($gy + 99, 100) + self::div($gy + 399, 400);

        for ($i = 0; $i < $gm; ++$i)
            $g_day_no += $g_days_in_month[$i];
        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)))
            /* leap and after Feb */
            $g_day_no++;
        $g_day_no += $gd;

        $j_day_no = $g_day_no - 79;

        $j_np = self::div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
        $j_day_no = $j_day_no % 12053;

        $jy = 979 + 33 * $j_np + 4 * self::div($j_day_no, 1461); /* 1461 = 365*4 + 4/4 */

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no - 1, 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
            $j_day_no -= $j_days_in_month[$i];
        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return array($jy, $jm, $jd);
    }

    static function jalali_to_gregorian($j_y, $j_m, $j_d)
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);


        $jy = $j_y - 979;
        $jm = $j_m - 1;
        $jd = $j_d - 1;

        $j_day_no = 365 * $jy + self::div($jy, 33) * 8 + self::div($jy % 33 + 3, 4);
        for ($i = 0; $i < $jm; ++$i)
            $j_day_no += $j_days_in_month[$i];

        $j_day_no += $jd;

        $g_day_no = $j_day_no + 79;

        $gy = 1600 + 400 * self::div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */ {
            $g_day_no--;
            $gy += 100 * self::div($g_day_no, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
                $g_day_no++;
            else
                $leap = false;
        }

        $gy += 4 * self::div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
        $gm = $i + 1;
        $gd = $g_day_no + 1;

        return array($gy, $gm, $gd);
    }

    public static function validateDate($date, $seperator = '/')
    {
        $date = str_replace($seperator, '-', farsi_to_english_digit($date));
        $r = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date);
        if ($r) {
            list($y, $m, $d) = explode('-', $date);
            $y = intval($y);
            $m = intval($m);
            $d = intval($d);
            if ($d > self::getMonthDays($m, $y))
                return false;
            return true;
        }
        return false;
    }
}