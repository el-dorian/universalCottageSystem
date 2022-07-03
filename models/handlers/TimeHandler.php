<?php


namespace app\models\handlers;


use app\models\exceptions\MyException;
use app\models\selections\ParsedMonth;
use app\models\selections\ParsedQuarter;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Throwable;


class TimeHandler
{
    public static array $months = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
    public static array $literallyMonths = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];


    /**
     * Верну дату и время из временной метки
     * @param int $timestamp Метка времени
     * @return string Строка формата
     */
    public static function timestampToDate(int $timestamp, $addTime = false): string
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        $answer = '';
        $day = $date->format('d');
        $answer .= $day;
        $month = mb_strtolower(self::$months[$date->format('m') - 1]);
        $answer .= ' ' . $month . ' ';
        $answer .= $date->format('Y') . ' года.';
        if($addTime){
            $answer .= $date->format(' H:i:s');
        }
        return $answer;
    }

    public static function isQuarter(string $period): bool
    {
        try {
            $exploded = explode('-', $period);
            $exploded[0] = (int)$exploded[0];
            $exploded[1] = (int)$exploded[1];
            if (count($exploded) === 2 && $exploded[0] > 1000 && $exploded[1] > 0 && $exploded[1] < 5) {
                return true;
            }
        } catch (Throwable) {
        }
        return false;
    }

    public static function isYear(string $period): bool
    {
        try {
            return (int)$period > 1900;
        } catch (Throwable) {
        }
        return false;
    }

    /**
     * @throws MyException
     */
    public static function dateToTimestamp(string $date): int
    {
        $timestamp = strtotime($date);
        if ($timestamp) {
            return $timestamp;
        }
        throw new MyException('$date не является верным значением даты');
    }

    /**
     * @param string $period
     * @return bool
     */
    public static function isMonth(string $period): bool
    {
        try {
            $match = null;
            if (preg_match('/^(\d{4})\W*([0-1]?\d)$/', $period, $match) && $match[2] > 0 && $match[2] < 13 && self::isYear($match[1])) {
                if ($match[2] < 10) {
                    $match[2] = '0' . (int)$match[2];
                }
                return true;
            }
        } catch (Throwable) {
        }
        return false;
    }

    /**
     * @throws MyException
     */
    public static function getQuarters(string $fromQuarter, string $toQuarter): array
    {
        $list = [];
        if($fromQuarter === $toQuarter){
            return $list;
        }
        $start = self::parseQuarter($fromQuarter);
        $finish = self::parseQuarter($toQuarter);
        $current = $start->full;
        do {
            $list[] = $current;
            $current = self::getNextQuarter($current)->full;
        } while ($current <= $finish->full);
        return $list;
    }

    public static function getNextQuarter($quarter): ParsedQuarter
    {
        $match = null;
        preg_match('/^(\d{4})\W*([1-4])$/', $quarter, $match);
        if ($match[2] > 3) {
            return new ParsedQuarter($match[1] + 1 . '-1', $match[1] + 1, 1);
        }
        return new ParsedQuarter($match[1] . '-' . ($match[2] + 1), $match[1], $match[2] + 1);
    }

    public static function getPreviousQuarter($quarter): ParsedQuarter
    {
        $match = null;
        preg_match('/^(\d{4})\W*([1-4])$/', $quarter, $match);
        if ($match[2] === '1') {
            return new ParsedQuarter($match[1] - 1 . '-4', $match[1] - 1, 4);
        }
        return new ParsedQuarter($match[1] . '-' . ($match[2] - 1), $match[1], $match[2] - 1);
    }


    /**
     * @param string $from
     * @param string|null $to
     * @return ParsedMonth[]
     * @throws MyException
     */
    public static function getMonths(string $from,?string $to = null): array
    {
        // составлю массив месяцев
        $list = [];
        $count = self::getMonthsDistance($from, $to);
        $month = self::parseMonth($from)->full;
        $match = null;
        preg_match('/^(\d{4})\W*(\d{2})$/', $month, $match);
        [, $year, $startMonth] = $match;
        if ($count > 0) {
            while ($count > 0) {
                if ($startMonth === '12' || $startMonth === 12) {
                    $startMonth = '01';
                    ++$year;
                } else {
                    ++$startMonth;
                    if ($startMonth < 10) {
                        $startMonth = '0' . $startMonth;
                    }
                }
                $list[$year . '-' . $startMonth] = new ParsedMonth("$year-$startMonth", $year, $month);
                --$count;
            }
        } else if ($count < 0) {
            --$startMonth;
            while ($count < 0) {
                if ($startMonth < 10) {
                    $startMonth = '0' . $startMonth;
                }
                $list[$year . '-' . $startMonth] =  new ParsedMonth("$year-$startMonth", $year, $month);
                if ($startMonth === '01' || $startMonth === 1 || $startMonth === '1') {
                    $startMonth = '12';
                    --$year;
                } else {
                    --$startMonth;
                }
                ++$count;
            }
            $list = array_reverse($list);
        }
        return $list;
    }

    /**
     * @throws \app\models\exceptions\MyException
     */
    public static function getMonthsDistance($monthFrom, $monthTo = null): int
    {
        $parsedMonthFrom = self::parseMonth($monthFrom);
        $parsedMonthTo = $monthTo ? self::parseMonth($monthTo) : self::parseMonth(self::getCurrentMonth());

        if ($parsedMonthFrom->year === $parsedMonthTo->year) {
            // если год совпадает-получаю разницу вычитанием
            return $parsedMonthTo->month - $parsedMonthFrom->month;
        }
        // проверю, в какую сторону считать
        if ($parsedMonthFrom->full <= $parsedMonthTo->full) {
            $difference = $parsedMonthTo->year - $parsedMonthFrom->year;
            // возвращаю сумму лет в этом году и лет прошлого года
            return $parsedMonthTo->intMonth + (12 - $parsedMonthFrom->intMonth) + (($difference - 1) * 12);
        }
        $difference = $parsedMonthFrom->intYear - $parsedMonthTo->intYear;
        return -((12 - $parsedMonthTo->intMonth) + $parsedMonthFrom->intMonth+ (($difference - 1) * 12));
    }

    public static function getCurrentMonth(): string
    {
        return strftime('%Y-%m', strtotime(date('Y-m')));
    }

    /**
     * @throws \app\models\exceptions\MyException
     */
    #[ArrayShape(['full' => "string", 'year' => "string", 'month' => "string"])] public static function parseMonth($month): ParsedMonth
    {
        $match = null;
        if (preg_match('/^(\d{4})\W*([0-1]?\d)$/', $month, $match) && $match[2] > 0 && $match[2] < 13 && self::isYear($match[1])) {
            if ($match[2] < 10) {
                $match[2] = '0' . (int)$match[2];
            }
            return new ParsedMonth("$match[1]-$match[2]", $match[1], $match[2]);
        }
        throw new MyException("Значение \"$month\" не является месяцем");
    }

    /**
     * @param string $input
     * @return ParsedQuarter
     * @throws MyException
     */
    public static function parseQuarter(string $input): ParsedQuarter
    {
        $match = null;
        if (preg_match('/^\s*(\d{4})\W*([1-4])\s*$/', $input, $match) && $match[1] > 0 && $match[2] < 5 && self::isYear($match[1])) {
            return new ParsedQuarter("$match[1]-$match[2]", $match[1], $match[2]);
        }
        throw new MyException("Значение \"$input\" не является кварталом");
    }

    public static function getYears(int $fromYear, int $toYear): array
    {
        $list[] = $fromYear;
        if ($fromYear !== $toYear) {
            $current = ++$fromYear;
            while ($current <= $toYear) {
                $list[] = $current;
                ++$current;
            }
        }
        return $list;
    }

    public static function getCurrentYear(): string
    {
        return date('Y');
    }


    public static function getCurrentQuarter(): ParsedQuarter
    {
        $year = strftime('%Y', strtotime(date('Y-m')));
        try {
            $quarter = self::convertMonthToQuarter(strftime('%m', strtotime(date('Y-m'))));
            return new ParsedQuarter("$year-$quarter", $year, $quarter);
        } catch (MyException) {}
        return new ParsedQuarter("$year-1", $year, 1);
    }

    public static function convertMonthToQuarter($month): int
    {
        switch ($month) {
            case 1:
            case 2:
            case 3:
                return 1;
            case 4:
            case 5:
            case 6:
                return 2;
            case 7:
            case 8:
            case 9:
                return 3;
            case 10:
            case 11:
            case 12:
                return 4;
        }
        throw new MyException("$month не месяц");
    }

    /**
     * @throws MyException
     */
    public static function inflateMonth(string $shortMonth): string
    {
        $parsedMonth = self::parseMonth($shortMonth);
        return self::$literallyMonths[$parsedMonth->intMonth - 1] . " $parsedMonth->year года";
    }

    /**
     * @throws MyException
     */
    public static function getNextMonth(string $month): string
    {
        return self::parseMonth($month)->getNext();
    }

    /**
     * @throws MyException
     */
    public static function getPreviousMonth(string $month): string
    {
        return self::parseMonth($month)->getPrevious();
    }

    /**
     * @throws MyException
     */
    public static function getMonthTimestamp($month): int
    {
        // получу отметку времени 2 числа первого месяца данного года - второго, чтобы исключить поправку на часовой пояс
        $parsed = self::parseMonth($month);
        return strtotime("2-$parsed->month-$parsed->year");
    }

}