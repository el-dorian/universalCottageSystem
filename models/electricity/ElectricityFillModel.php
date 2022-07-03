<?php

namespace app\models\electricity;

use app\models\databases\DbAccrualElectricity;
use app\models\databases\DbCottage;
use app\models\databases\DbElectricityMeter;
use app\models\databases\DbTariffElectricity;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use app\models\utils\DbTransaction;
use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;
use yii\helpers\Json;

class ElectricityFillModel extends Model
{
    public mixed $entities = null;
    public ?int $meterId;
    public mixed $test = null;
    public string $lastValue = '';

    #[ArrayShape(['entities' => "string"])] public function attributeLabels(): array
    {
        return [
            'entities' => 'Периоды оплаты'
        ];
    }

    public function rules(): array
    {
        return [
            [['entities', 'meterId'], 'required'],
            ['entities', 'validateEntities']
        ];
    }

    /**
     * @throws MyException
     */
    public function validateEntities($attribute): void
    {
        $data = $this->$attribute;
        if (is_string($data)) {
            $data = Json::encode($data);
        }
        $meter = DbElectricityMeter::findOne($this->meterId);
        if ($meter !== null) {
            $lastPeriod = $meter->last_filled_period;
            $previousData = $meter->indication;
            foreach ($data as $key => $period) {
                $target = "{$attribute}[$key][period]";
                if (!TimeHandler::isMonth($period['period'])) {
                    $this->addError($target, "{$period['period']}- не является месяцем. Нужный формат: хххх-хх");
                    return;
                }
                $nextPeriod = TimeHandler::getNextMonth($lastPeriod);
                if ($period['period'] !== $nextPeriod) {
                    $this->addError($target, 'Следующим должен быть заполнен ' . $nextPeriod);
                    return;
                }
                $lastPeriod = $nextPeriod;
                $target = "{$attribute}[$key][value]";
                if ($period['value'] < $previousData) {
                    $this->addError($target, 'Значение не может быть меньше чем значение предыдущего периода: ' . $previousData);
                    return;
                }
                $previousData = $period['value'];
            }
        }
    }

    /**
     * @throws MyException
     */
    public function setup(DbAccrualElectricity $lastValue): void
    {
        $this->meterId = $lastValue->meter;
        $this->lastValue = $lastValue->current_meter_values;
        // посчитаю, сколько месяцев надо заполнить до текущего месяца
        if ($lastValue->period < TimeHandler::getCurrentMonth()) {
            $periodsForFill = TimeHandler::getMonths($lastValue->period);
            $periods = [];
            if (!empty($periodsForFill)) {
                foreach ($periodsForFill as $period) {
                    $periods[] = ['period' => $period->full];
                }
            }
            $this->entities = $periods;
        } else {
            $this->entities = [['period' => TimeHandler::getNextMonth($lastValue->period)]];
        }
    }

    /**
     * @throws MyException
     * @throws DbSettingsException
     */
    public function insertValues(): void
    {
        $dbTransaction = new DbTransaction();
        $data = $this->entities;
        if (is_string($data)) {
            $data = Json::encode($data);
        }
        $meter = DbElectricityMeter::findOne($this->meterId);
        if ($meter !== null) {
            $cottage = DbCottage::findOne($meter->cottage);
            if ($cottage !== null) {
                $lastIndication = $meter->indication;
                foreach ($data as $indication) {
                    $newAccruals = new DbAccrualElectricity();
                    $newAccruals->cottage = $cottage->id;
                    $newAccruals->period = $indication['period'];
                    $newAccruals->meter = $this->meterId;
                    if(DbAccrualElectricity::exists($newAccruals)){
                        throw new MyException("Данные уже заполнены: $newAccruals->period");
                    }
                    $newAccruals->time_of_entry = time();
                    $newAccruals->search_timestamp = TimeHandler::getMonthTimestamp($newAccruals->period);
                    $tariff = DbTariffElectricity::findOne(['period' => $newAccruals->period]);
                    if ($tariff !== null) {
                        $newAccruals->previous_meter_values = $lastIndication;
                        $newAccruals->current_meter_values = $indication['value'];
                        $newAccruals->values_difference = $newAccruals->current_meter_values - $newAccruals->previous_meter_values;
                        $newAccruals->preferential_limit = $tariff->preferential_limit;
                        $newAccruals->preferential_price = $tariff->preferential_price;
                        $newAccruals->routine_price = $tariff->routine_price;
                        if ($newAccruals->values_difference === 0) {
                            $newAccruals->preferential_consumption = 0;
                            $newAccruals->routine_consumption = 0;
                            $newAccruals->preferential_amount = 0;
                            $newAccruals->routine_amount = 0;
                            $newAccruals->total_amount = 0;
                            $newAccruals->is_payed = 'yes';
                        } else {
                            $inLimit = 0;
                            $overLimit = 0;
                            if ($newAccruals->preferential_limit >= $newAccruals->values_difference) {
                                $inLimit = $newAccruals->values_difference;
                            } else {
                                $inLimit = $newAccruals->preferential_limit;
                                $overLimit = $newAccruals->values_difference - $newAccruals->preferential_limit;
                            }
                            $newAccruals->preferential_consumption = $inLimit;
                            $newAccruals->routine_consumption = $overLimit;
                            $newAccruals->preferential_amount = $newAccruals->preferential_consumption * $newAccruals->preferential_price;
                            $newAccruals->routine_amount = $newAccruals->routine_consumption * $newAccruals->routine_price;
                            $newAccruals->total_amount = $newAccruals->preferential_amount + $newAccruals->routine_amount;
                            $newAccruals->is_payed = 'no';
                        }
                        $newAccruals->save();
                        $cottage->debt_electricity += $newAccruals->total_amount;
                        $cottage->total_debt += $newAccruals->total_amount;
                        $cottage->save();
                        $lastIndication = $newAccruals->current_meter_values;
                        $meter->indication = $lastIndication;
                        $meter->last_filled_period = $newAccruals->period;
                        $meter->save();
                    }
                    else{
                        throw new MyException("Не найден тариф $newAccruals->period");
                    }
                }
            }
            else{
                throw new MyException("Не найдены данные об участке $meter->cottage");
            }
        }
        else{
            throw new MyException("Не найдены данные счётчика $this->meterId");
        }
        $dbTransaction->commitTransaction();
    }

}