<?php

namespace Jugger\Ui;

/**
 * Класс для форматирования вывода даты текстом
 */
class DateTime
{
    /**
     * Возвращает дату в формате прошедшего времени. Например, "15 минут назад", "2 часа назад", "вчера", ...
     * @param \Bitrix\Main\Type\DateTime $datetime
     * @return string
     */
    public static function passed(\Bitrix\Main\Type\DateTime $datetime, $formatDateTime = 'd.m.Y H:i', $formatDay = 'H:i') {
        $time = $datetime->getTimestamp();
        $diff = time() - $time;
        //
        if (self::passedYesterday($time, $output)) {
            return $output;
        }
        elseif ($diff < 60) {
            return "только что";
        }
        elseif ($diff < 60 * 60) {
            return self::passedMinutes(0, $diff);
        }
        elseif ($diff < 60 * 60 * 24) {
            return self::passedHours(0, $diff);
        }
        else {
            return $datetime->format($formatDateTime);
        }
    }
    
    public static function passedYesterday($time, & $output) {
        $today = strtotime('midnight');
        $yesterday = strtotime('yesterday');
        if ($time > $yesterday && $time < $today) {
            $output = 'вчера в '.date("H:i", $time);
            return true;
        }
        return false;
    }
    
    public static function passedMinutes($time, $diff = null) {
        if (is_null($diff)) {
            $diff = time() - $time;
        }
        $x = intval($diff / 60);
        $y = $x % 10;
        if ($y === 1 && $x !== 11) {
            return $x." минуту назад";
        }
        elseif ($y > 1 && $y < 5 && ($x > 20 || $x < 10)) {
            return $x.' минуты назад';
        }
        else {
            return $x.' минут назад';
        }
    }
    
    public static function passedHours($time, $diff = null) {
        if (is_null($diff)) {
            $diff = time() - $time;
        }
        $x = intval($diff / 60 / 60);
        $y = $x % 10;
        if ($y === 1 && $x !== 11) {
            return $x." час назад";
        }
        elseif ($y > 1 && $y < 5 && ($x > 20 || $x < 10)) {
            return $x.' часа назад';
        }
        else {
            return $x.' часов назад';
        }
    }
}