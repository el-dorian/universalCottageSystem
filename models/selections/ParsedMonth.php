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

    public function getNext(): string
    {
        if($this->intMonth === 12){
            $this->month = '01';
            ++$this->year;
        }
        else{
            ++$this->intMonth;
            if($this->intMonth < 10){
                $this->month = "0$this->intMonth";
            }
            else{
                $this->month = $this->intMonth;
            }
        }
        return "$this->year-$this->month";
    }

    public function getPrevious(): string
    {
        if($this->intMonth === 1){
            $this->month = '12';
            --$this->year;
        }
        else{
            --$this->intMonth;
            if($this->intMonth < 10){
                $this->month = "0$this->intMonth";
            }
            else{
                $this->month = $this->intMonth;
            }
        }
        return "$this->year-$this->month";
    }
}