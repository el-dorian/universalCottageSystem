<?php

namespace app\models\electricity;

use app\models\databases\DbAccrualElectricity;
use app\models\databases\DbCottage;
use app\models\databases\DbElectricityMeter;
use app\models\exceptions\DbSettingsException;
use app\models\utils\DbTransaction;
use yii\db\StaleObjectException;

class ElectricityMeterHandler
{

    /**
     * @throws StaleObjectException
     * @throws DbSettingsException
     */
    public static function dropMeter($id): void
    {
        $transaction = new DbTransaction();
        $meter = DbElectricityMeter::findOne($id);
        if ($meter !== null) {
            $cottage = DbCottage::findOne($meter->cottage);
            if ($cottage !== null) {
                $accruals = DbAccrualElectricity::findAll(['meter' => $meter]);
                if (!empty($accruals)) {
                    foreach ($accruals as $accrual) {
                        $cottage->total_debt -= $accrual->total_amount - $accrual->payed_sum;
                        $cottage->debt_electricity -= $accrual->total_amount - $accrual->payed_sum;
                        $accrual->delete();
                    }
                }
                $cottage->save();
                $meter->delete();
            }
            $transaction->commitTransaction();
        }
    }
}