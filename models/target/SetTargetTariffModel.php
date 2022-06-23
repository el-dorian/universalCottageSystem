<?php

namespace app\models\target;

use app\models\databases\DbTariffTarget;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use app\models\management\BasePreferences;
use app\models\utils\CashHandler;
use app\models\utils\DbTransaction;
use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;
use yii\helpers\Json;

class SetTargetTariffModel extends Model
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
            if(BasePreferences::getInstance()->targetPaymentType === BasePreferences::STATE_PAY_YEARLY){
                if(empty($value['period'])){
                    $this->addError($target, 'Необходимо ввести год');
                    return;
                }
                if(isset($preparedResults[$value['period']])){
                    $this->addError($target, 'Вы уже ввели данные за этот год!');
                    return;
                }
                if(DbTariffTarget::find()->where(['period' => $value['period']])->count() > 0){
                    $this->addError($target, 'В базе уже есть данные за этот год!');
                    return;
                }
            }
            else{
                if(empty($value['period'])){
                    $this->addError($target, 'Необходимо ввести квартал');
                    return;
                }
                if(isset($preparedResults[$value['period']])){
                    $this->addError($target, 'Вы уже ввели данные за этот квартал!');
                    return;
                }
                if(DbTariffTarget::find()->where(['period' => $value['period']])->count()){
                    $this->addError($target, 'В базе уже есть данные за этот квартал!');
                    return;
                }
                if(!TimeHandler::isQuarter($value['period'])){
                    $this->addError($target, 'Неверное введение, квартал нужно вводить в формате xxxx-x!');
                    return;
                }
            }
            // _mark cottage price
            $target = $attribute . '[' . $key . '][cottage_price]';
            if(empty($value['cottage_price'])){
                $this->addError($target, 'Необходимо ввести цену с участка');
                return;
            }
            if(!CashHandler::isFloatCash($value['cottage_price'])){
                $this->addError($target, 'Не похоже на верное число');
                return;
            }
            $target = $attribute . '[' . $key . '][footage_price]';
            if(empty($value['footage_price'])){
                $this->addError($target, 'Необходимо ввести цену с участка');
                return;
            }
            if(!CashHandler::isFloatCash($value['footage_price'])){
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
           // _mark description
            $target = $attribute . '[' . $key . '][description]';
            if(empty($value['description'])){
                $this->addError($target, 'Необходимо ввести цель платежа.');
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
            $newTariff = new DbTariffTarget();
            $newTariff->period = $periodForFilling['period'];
            $newTariff->description = htmlspecialchars($periodForFilling['description']);
            $newTariff->cottage_price = CashHandler::centsValueToRublesValue($periodForFilling['cottage_price']);
            $newTariff->footage_price = CashHandler::centsValueToRublesValue($periodForFilling['footage_price']);
            $newTariff->period_timestamp = TimeHandler::dateToTimestamp($periodForFilling['date']);
            $newTariff->save();
        }
        $dbTransaction->commitTransaction();
    }
}