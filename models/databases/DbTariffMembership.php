<?php


namespace app\models\databases;

use app\models\interfaces\TariffInterface;
use yii\db\ActiveRecord;

/**
 * @package app\models\databases
 *
 * @property int $id [int(10) unsigned]
 * @property string $period [varchar(10)]  Период оплаты
 * @property int $cottage_price [int(10) unsigned]  Цена с участка
 * @property int $footage_price [int(10) unsigned]  Цена с сотки
 * @property int $period_timestamp [bigint(20)]  Временная метка периода оплаты
 */


class DbTariffMembership extends ActiveRecord implements TariffInterface
{
    public static function tableName(): string
    {
        return 'tariff_membership';
    }

    /**
     * @return DbTariffTarget[]
     */
    public static function getAll(): array
    {
        return self::find()->orderBy('period DESC')->all();
    }

}