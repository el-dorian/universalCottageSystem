<?php


namespace app\models\handlers;


use DateTime;

class TimeHandler
{
    public static array $months = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];


    /**
     * Верну дату и время из временной метки
     * @param int $timestamp Метка времени
     * @return string Строка формата
     */
    public static function timestampToDateTime(int $timestamp): string
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        $answer = '';
        $day = $date->format('d');
        $answer .= $day;
        $month = mb_strtolower(self::$months[$date->format('m') - 1]);
        $answer .= ' ' . $month . ' ';
        $answer .= $date->format('Y') . ' года.';
        $answer .= $date->format(' H:i:s');
        return $answer;
    }
}