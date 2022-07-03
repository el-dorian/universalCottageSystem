<?php

namespace app\models\electricity;

use app\models\databases\DbAccrualElectricity;
use app\models\databases\DbCottage;
use app\models\databases\DbElectricityMeter;

class ElectricityDebtHandler
{

    /**
     * @return int
     */
    public static function countDebt(DbCottage $cottage): int
    {
        $debt = 0;
        $unpaidEntities = DbAccrualElectricity::find()->where(['cottage' => $cottage->id])->andWhere(['<>', 'is_payed', 'yes'])->all();
        if (!empty($unpaidEntities)) {
            foreach ($unpaidEntities as $unpaidEntity) {
                $debt += $unpaidEntity->total_amount - $unpaidEntity->payed_sum;
            }
        }
        return $debt;
    }
}