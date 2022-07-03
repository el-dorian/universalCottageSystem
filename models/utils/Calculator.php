<?php

namespace app\models\utils;

class Calculator
{

    public static function countWithSquare(mixed $cottage_price, mixed $footage_price, int $square)
    {
        return $cottage_price + ($square * $footage_price / 100);
    }
}