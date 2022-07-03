<?php

namespace app\models\electricity;

use app\models\databases\DbTariffElectricity;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use app\models\utils\CashHandler;
use app\models\utils\DbTransaction;
use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;
use yii\helpers\Json;

class SetElectricityTariffModel extends Model
{
    public mixed $entities = '';

    public function rules(): array
    {
        return [
            [['entities'], 'required'],
            [['entities'], 'validateData']
        ];
    }

    #[ArrayShape(['entities' => "string"])] public function attributeLabels(): array
    {
        return [
            'entities' => 'Настройки тарифа'
        ];
    }

    public function validateData($attribute): void
    {
        $preparedResults = [];
        $data = $this->$attribute;
        if(is_string($data)){
            $data = Json::encode($data);
        }
        if (empty($data)) {
            $this->addError($attribute, 'Необходимо заполнить данные');
            return;
        }
        // _mark PERIOD
        foreach ($data as $key => $value) {
            $target = $attribute . '[' . $key . '][period]';
                if(empty($value['period'])){
                    $this->addError($target, 'Необходимо ввести месяц');
                    return;
                }
                if(isset($preparedResults[$value['period']])){
                    $this->addError($target, 'Вы уже ввели данные за этот месяц!');
                    return;
                }
                if(DbTariffElectricity::find()->where(['period' => $value['period']])->count() > 0){
                    $this->addError($target, 'В базе уже есть данные за этот месяц!');
                    return;
                }

            if(!TimeHandler::isMonth($value['period'])){
                $this->addError($target, 'Неверное введение, месяц нужно вводить в формате xxxx-xx!');
                return;
            }

            // _mark cottage price
            $target = $attribute . '[' . $key . '][preferential_limit]';
            if(empty($value['preferential_limit'])){
                $this->addError($target, 'Необходимо ввести лимит льготного потребления');
                return;
            }
            if(!is_int((int)$value['preferential_limit'])){
                $this->addError($target, 'Не похоже на верное число');
                return;
            }
            $target = $attribute . '[' . $key . '][preferential_price]';
            if(empty($value['preferential_price'])){
                $this->addError($target, 'Необходимо ввести льготную стоимость киловатта');
                return;
            }
            if(!CashHandler::isFloatCash($value['preferential_price'])){
                $this->addError($target, 'Не похоже на верное число');
                return;
            }
            $target = $attribute . '[' . $key . '][routine_price]';
            if(empty($value['routine_price'])){
                $this->addError($target, 'Необходимо ввести стоимость киловатта сверх льготного лимита');
                return;
            }
            if(!CashHandler::isFloatCash($value['routine_price'])){
                $this->addError($target, 'Не похоже на верное число');
                return;
            }
            // _mark date
            $target = $attribute . '[' . $key . '][date]';
            if(empty($value['date'])){
                $this->addError($target, 'Необходимо ввести дату начала действия тарифа');
                return;
            }
           if(!strtotime($value['date'])){
               $this->addError($target, 'Не похоже на дату');
               return;
           }
            $newResult = [];
            $preparedResults[$value['period']] = $newResult;
        }
    }

    /**
     * @throws MyException
     * @throws DbSettingsException
     */
   public function save(): void
    {
        $dbTransaction = new DbTransaction();
        foreach ($this->entities as $periodForFilling) {
            $newTariff = new DbTariffElectricity();
            $newTariff->period = $periodForFilling['period'];
            $newTariff->preferential_limit = $periodForFilling['preferential_limit'];
            $newTariff->preferential_price = CashHandler::floatSumToIntSum($periodForFilling['preferential_price']);
            $newTariff->routine_price = CashHandler::floatSumToIntSum($periodForFilling['routine_price']);
            $newTariff->period_timestamp = TimeHandler::dateToTimestamp($periodForFilling['date']);
            $newTariff->save();
        }
        $dbTransaction->commitTransaction();
    }
}