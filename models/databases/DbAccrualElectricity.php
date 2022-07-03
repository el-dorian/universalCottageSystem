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
 * @property int $meter [int(10) unsigned]
 * @property int $time_of_entry [bigint(20) unsigned]  Дата внесения показаний
 * @property int $search_timestamp [bigint(20) unsigned]
 * @property int $previous_meter_values [bigint(20) unsigned]  Предыдущие показания счётчика
 * @property int $current_meter_values [bigint(20) unsigned]  Новые показания счётчика
 * @property int $values_difference [int(10) unsigned]  Потребление за месяц
 * @property int $preferential_consumption [int(10) unsigned]  Льготно потреблено кВт
 * @property int $routine_consumption [int(10) unsigned]  Потреблено кВт сверх льготного лимита
 * @property int $preferential_amount [int(10) unsigned]  Льготная стоимость
 * @property int $routine_amount [int(10) unsigned]  Стоимость сверх льготного лимита
 * @property int $preferential_limit [int(10) unsigned]
 * @property int $preferential_price [int(10) unsigned]
 * @property int $routine_price [int(10) unsigned]
 * @property int $total_amount [int(10) unsigned]
 * @property string $is_payed [enum('no', 'yes')]  Оплачено ли полностью
 * @property int $payed_sum [int(11) unsigned]
 */


class DbAccrualElectricity extends ActiveRecord implements TariffInterface
{
    public static function tableName(): string
    {
        return 'accrual_electricity';
    }

    public static function getLastAccrual(DbElectricityMeter $model): ?DbAccrualElectricity
    {
        return self::findOne(['meter' => $model->id]);
    }

    public static function exists(DbAccrualElectricity $newAccruals): bool
    {
        return self::find()->where(['period' => $newAccruals->period, 'cottage'=> $newAccruals->cottage, 'meter' => $newAccruals->meter])->count() > 0;
    }
}