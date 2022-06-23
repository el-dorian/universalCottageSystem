<?php

namespace app\models\selections;

class ParsedQuarter
{
    public function __construct($full, $year, $quarter)
    {
        $this->full = $full;
        $this->year = $year;
        $this->quarter = $quarter;
        $this->intQuarter= (int) $quarter;
        $this->intYear = (int) $year;
    }

    public string $full;
    public string $year;
    public string $quarter;
    public int $intYear;
    public int $intQuarter;
}