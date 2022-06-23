<?php


namespace app\models\databases;

use app\models\interfaces\TariffInterface;
use yii\db\ActiveRecord;

/**
 * @package app\models\databases
 *
 * @property int $id [int(10) unsigned]
 * @property string $period [varchar(10)]  Период оплаты
 * @property int $preferential_limit [int(10) unsigned]  Лимит льготного потребления электроэнергии, кВт
 * @property int $preferential_price [int(10) unsigned]  Льготная цена
 * @property int $routine_price [int(10) unsigned]  Обычная цена
 * @property int $period_timestamp [bigint(20) unsigned]  Время добавления тарифа
 */


class DbTariffElectricity extends ActiveRecord implements TariffInterface
{
    public static function tableName(): string
    {
        return 'tariff_electricity';
    }

    /**
     * @return DbTariffTarget[]
     */
    public static function getAll(): array
    {
        return self::find()->orderBy('period DESC')->all();
    }

}