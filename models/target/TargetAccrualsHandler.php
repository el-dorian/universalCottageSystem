<?php

namespace app\models\target;

use app\models\databases\DbAccrualMembership;
use app\models\databases\DbCottage;
use app\models\databases\DbTariffMembership;
use app\models\databases\DbTariffTarget;
use app\models\utils\Calculator;

class TargetAccrualsHandler
{

    public static function registerNewCottage(DbCottage $cottage, string $targetPayedFor)
    {
        $totalDebt = 0;
        $tariffs = DbTariffTarget::find()->where(['>', 'period', $targetPayedFor])->all();
        if(!empty($tariffs)){
            foreach ($tariffs as $tariff) {
                $accrued = Calculator::countWithSquare($tariff->cottage_price, $tariff->footage_price, $cottage->square);
                $newAccrual = new DbAccrualMembership();
                $newAccrual->cottage = $cottage->id;
                $newAccrual->period = $tariff->period;
                $newAccrual->cottage_price = $tariff->cottage_price;
                $newAccrual->footage_price = $tariff->footage_price;
                $newAccrual->cottage_calculated_area = $cottage->square;
                $newAccrual->total_amount = $accrued;
                if($newAccrual->total_amount === 0){
                    $newAccrual->is_payed = 'yes';
                }
                else{
                    $newAccrual->is_payed = 'no';
                }
                $newAccrual->save();
                $totalDebt += $accrued;
            }
        }
        return $totalDebt;
    }
}