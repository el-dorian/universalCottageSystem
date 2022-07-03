<?php

namespace app\models\electricity;

use app\models\databases\DbAccrualElectricity;
use app\models\databases\DbCottage;
use app\models\databases\DbElectricityMeter;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use app\models\utils\DbTransaction;
use yii\db\StaleObjectException;

class ElectricityAccrualsHandler
{

    /**
     * @throws MyException
     */
    public static function registerNewCounter(DbElectricityMeter $meter, string $targetPayedFor): void
    {
        // register new zero accrual for meter
        $newElectricityAccrual = new DbAccrualElectricity();
        $newElectricityAccrual->cottage = $meter->cottage;
        $newElectricityAccrual->period = $targetPayedFor;
        $newElectricityAccrual->meter = $meter->id;
        $newElectricityAccrual->time_of_entry = time();
        $newElectricityAccrual->search_timestamp = TimeHandler::dateToTimestamp($targetPayedFor . '-1');
        $newElectricityAccrual->previous_meter_values = $meter->indication;
        $newElectricityAccrual->current_meter_values = $meter->indication;
        $newElectricityAccrual->values_difference = 0;
        $newElectricityAccrual->preferential_consumption = 0;
        $newElectricityAccrual->routine_consumption = 0;
        $newElectricityAccrual->preferential_amount = 0;
        $newElectricityAccrual->routine_amount = 0;
        $newElectricityAccrual->preferential_limit = 0;
        $newElectricityAccrual->preferential_price = 0;
        $newElectricityAccrual->routine_price = 0;
        $newElectricityAccrual->total_amount = 0;
        $newElectricityAccrual->is_payed = 'yes';
        $newElectricityAccrual->save();
    }

    /**
     * @param DbAccrualElectricity $accrual
     * @return void
     * @throws MyException
     * @throws StaleObjectException
     * @throws DbSettingsException
     */
    public static function rollback(DbAccrualElectricity $accrual): void
    {
        $dbTransaction = new DbTransaction();
        $cottage = DbCottage::findOne($accrual->cottage);
        $meter = DbElectricityMeter::findOne($accrual->meter);
        if($cottage !== null && $meter !== null){
            if(DbAccrualElectricity::find()->where(['<', 'search_timestamp', $accrual->search_timestamp])->andWhere(['meter' => $meter->id])->count() < 1){
                throw new MyException("Нельзя удалять начальные показания");
            }
            $accrualsForDelete = DbAccrualElectricity::find()->where(['>=', 'search_timestamp', $accrual->search_timestamp])->andWhere(['meter' => $meter->id])->orderBy('search_timestamp DESC')->all();
            foreach ($accrualsForDelete as $item) {
                $cottage->debt_electricity -= $item->total_amount;
                $cottage->total_debt -= $item->total_amount;
                $meter->indication = $item->previous_meter_values;
                $meter->last_filled_period = TimeHandler::getPreviousMonth($item->period);
                $item->delete();
                $cottage->save();
                $meter->save();
            }
            $dbTransaction->commitTransaction();
        }
    }
}