<?php

namespace app\models\payment;

use app\models\databases\DbAccrualElectricity;
use app\models\databases\DbCottage;
use app\models\databases\DbElectricityMeter;
use app\models\databases\DbGardener;
use app\models\exceptions\MyException;
use app\models\utils\CashHandler;
use yii\base\Model;
use yii\helpers\Json;

class PaymentInvoiceBuilder extends Model
{
    public int $cottage;
    public mixed $electricity = [];
    public array $payers = [];
    public string $payer = '';



    public function rules()
    {
        return
            [
                [['payer'], 'required'],
                ['electricity', 'validateElectricity', 'skipOnEmpty' => true],
            ];
    }

    public function attributeLabels()
    {
        return [
            'payer' => "Плательщик",
            'electricity' => "Электроэнергия"
        ];
    }

    /**
     * @throws MyException
     */
    public function validateElectricity($attribute): void
    {
        $data = $this->$attribute;
        if (is_string($data)) {
            $data = Json::encode($data);
        }
        $doublesCheckArray = [];
        foreach ($data as $key => $value) {
            $period = $value['period'];
            $sum = $value['sum'];
            $meter = $value['meter'];

            $target = "{$attribute}[$key][period]";
            if (isset($doublesCheckArray[$period])) {
                $this->addError($target, 'Дублирование месяца!');
            }
            $doublesCheckArray[$period] = 1;


            $target = "{$attribute}[$key][sum]";
            // проверю, что значение не больше, чем осталось заплатить
            $accrual = DbAccrualElectricity::findOne(['meter' => $meter, 'period' => $period]);
            if($accrual !== null){
                if(CashHandler::floatSumToIntSum($sum) > $accrual->getLeftToPay()){
                    $this->addError($target, "Максимальная стоимость: " . CashHandler::intSumToSmoothFloat($accrual->getLeftToPay()));
                }
            }
            else{
                $this->addError($target, "Не найдены начисления за $period!");
            }
        }
    }


    /**
     * @throws MyException
     */
    public function configure(DbCottage $cottage): void
    {
        $this->cottage = $cottage->id;
        $payers = DbGardener::find()->where(['cottage' => $cottage->id, 'is_payer' => 1])->all();
        if (empty($payers)) {
            throw new MyException("Не найдены плательщики");
        }
        foreach ($payers as $payer) {
            $this->payers[$payer->id] = $payer->personals;
            $this->payers["$payer->id-$payer->ownership_share"] = $payer->personals . " с долей собственности $payer->ownership_share";
        }
        $unpaidElectricity = DbAccrualElectricity::find()->where(['cottage' => $cottage])->andWhere(['<>', 'is_payed', 'yes'])->orderBy('period')->all();
        if(!empty($unpaidElectricity)){
            foreach ($unpaidElectricity as $item) {
                $meterInfo = DbElectricityMeter::findOne($item->meter);
                if($meterInfo !== null){
                    $this->electricity[] = ['period' => $item->period, 'sum' => CashHandler::intSumToFloat($item->total_amount),'meter' => $item->meter, 'meterDescription' => empty($meterInfo->description) ?'Нет комментария': $meterInfo->description];
                }
            }
        }
    }

    /**
     * @throws MyException
     */
    public function countTotal(): int
    {
        $sum = 0;
        if(!empty($this->electricity)){
            $data = $this->electricity;
            if (is_string($data)) {
                $data = Json::encode($data);
            }
            foreach ($data as $electricityItem) {
                if($electricityItem['selected_for_pay']){
                    $sum += CashHandler::floatSumToIntSum($electricityItem['sum']);
                }
            }
        }
            return $sum;
    }
}