<?php

namespace app\models\payment;

use app\models\databases\DbAccrualElectricity;
use app\models\databases\DbCottage;
use app\models\databases\DbGardener;
use app\models\exceptions\MyException;
use app\models\utils\CashHandler;
use yii\base\Model;

class PaymentInvoiceBuilder extends Model
{
    public int $cottage;
    public mixed $electricity;
    public array $payers = [];
    public string $payer = '';

    public function rules()
    {
        return
        [
            ['payer', 'required']
        ];
    }


    /**
     * @throws MyException
     */
    public function configure(DbCottage $cottage): void
    {
        $this->cottage = $cottage->id;
        $payers = DbGardener::find()->where(['cottage' => $cottage->id, 'is_payer' => 1])->all();
        if(empty($payers)){
            throw new MyException("Не найдены плательщики");
        }
        foreach ($payers as $payer){
            $this->payers[$payer->id] = $payer->personals;
            $this->payers["$payer->id-$payer->ownership_share"] = $payer->personals . " с долей собственности $payer->ownership_share";
        }

        // inflate electricity
        $unpaidElectricity = DbAccrualElectricity::find()->where(['cottage' => $cottage])->andWhere(['<>', 'is_payed', 'yes'])->orderBy('period')->all();
        if(!empty($unpaidElectricity)){
            $this->electricity = [];
            foreach ($unpaidElectricity as $item) {
                $this->electricity[$item->period] = ['period' => $item->period, 'sum' => CashHandler::intSumToFloat($item->total_amount)];
            }
        }
    }
}