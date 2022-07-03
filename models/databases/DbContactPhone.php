<?php


namespace app\models\databases;

use app\models\interfaces\TariffInterface;
use yii\db\ActiveRecord;

/**
 * @package app\models\databases
 *
 * @property int $id [int(10) unsigned]
 * @property int $cottage [int(10) unsigned]
 * @property int $gardener [int(10) unsigned]
 * @property string $description
 * @property int $number [bigint(16) unsigned]
 */


class DbContactPhone extends ActiveRecord implements TariffInterface
{
    public static function tableName(): string
    {
        return 'gardener_phone';
    }

    public static function isDouble(DbContactPhone $newPhone): bool
    {
        return self::find()->where(['gardener' => $newPhone->gardener, 'number' => $newPhone->number])->count() > 0;
    }
}