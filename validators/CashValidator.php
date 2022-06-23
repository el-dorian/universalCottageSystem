<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 19.12.2018
 * Time: 13:50
 */

namespace app\validators;

use app\models\utils\CashHandler;
use yii\validators\Validator;

class CashValidator extends Validator{

    /**
     * @param $model
     * @param $attribute
     * @return void
     */
    public function validateAttribute($model, $attribute): void
    {
		if(!CashHandler::isFloatCash($model->$attribute)){
            $model->addError($attribute, "{$model->$attribute}: не похоже на цену");
        }
	}
}