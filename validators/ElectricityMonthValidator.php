<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 19.12.2018
 * Time: 13:50
 */

namespace app\validators;

use app\models\databases\DbTariffElectricity;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use yii\validators\Validator;

class ElectricityMonthValidator extends Validator{

    /**
     * @param $model
     * @param $attribute
     * @return void
     */
    public function validateAttribute($model, $attribute): void
    {
        if (!TimeHandler::isMonth($model->$attribute)) {
            $model->addError($attribute, 'Введите месяц в формате xxxx-xx');
            return;
        }
        try {
            $monthsForFill = TimeHandler::getMonths($model->$attribute);
        } catch (MyException) {
            $model->addError($attribute, 'Введите месяц в формате xxxx-xx');
            return;
        }
        if (!empty($monthsForFill)) {
            $unfilledMonths = '';
            foreach ($monthsForFill as $item) {
                if (DbTariffElectricity::find()->where(['period' => $item->full])->count() < 1) {
                    $unfilledMonths .= "$item->full, ";
                }
            }
        }
        if (!empty($unfilledMonths)) {
            $model->addError($attribute, "Не заполнены тарифы! $unfilledMonths заполните их в окне тарифов.");
        }
	}
}