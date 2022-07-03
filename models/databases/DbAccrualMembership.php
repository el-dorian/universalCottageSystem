<?php


namespace app\models\databases;

use app\models\interfaces\TariffInterface;
use yii\db\ActiveRecord;

/**
 * @package app\models\databases
 *
 * @property int $id [int(10) unsigned]
 * @property int $cottage [int(10) unsigned]
 * @property string $period [varchar(10)]
 * @property int $cottage_price [int(10) unsigned]
 * @property int $footage_price [int(10) unsigned]
 * @property int $cottage_calculated_area [int(10) unsigned]
 * @property int $payed_outside_program [int(10) unsigned]
 * @property int $total_amount [int(10) unsigned]
 * @property string $is_payed [enum('no', 'yes')]
 * @property int $payed_sum [int(10) unsigned]
 */


class DbAccrualMembership extends ActiveRecord implements TariffInterface
{
    public static function tableName(): string
    {
        return 'accrual_membership';
    }
}