<?php

namespace app\models\utils;

use app\models\exceptions\MyException;
use Throwable;

class CashHandler
{
    public const RUBLE_SIGN = '₽';

    public static function isFloatCash(string $value, $canBeNegative = false): bool
    {
        try {
            if ((float)$value === 0.0) {
                return true;
            }
            if ($canBeNegative) {
                return (float)$value;
            }
            return (float)$value >= 0;

        } catch (Throwable) {
        }
        return false;
    }

    /**
     * @throws MyException
     */
    public static function floatSumToIntSum(string $cottage_price, $canBeNegative = false): int
    {
        try {
            if (self::isFloatCash($cottage_price, $canBeNegative)) {
                return (int)(((float)$cottage_price) * 100);
            }
        } catch (Throwable) {
        }
        throw new MyException("$cottage_price не является допустимым числом");
    }

    public static function intSumToSmoothFloat(int $cottage_price): string
    {
        return (int)($cottage_price / 100) . " р. " . $cottage_price % 100 . " коп.";
    }
    public static function intSumToFloat(int $cottage_price): string
    {
        return (int)($cottage_price / 100) . "." . $cottage_price % 100;
    }
}