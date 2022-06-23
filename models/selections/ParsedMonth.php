<?php

namespace app\models\selections;

class ParsedMonth
{
    public function __construct($full, $year, $month)
    {
        $this->full = $full;
        $this->year = $year;
        $this->month = $month;
        $this->intMonth = (int) $month;
        $this->intYear = (int) $year;
    }

    public string $full;
    public string $year;
    public string $month;
    public int $intYear;
    public int $intMonth;
}