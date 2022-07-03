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
 * @property string $address [varchar(500)]
 * @property string $description
 */


class DbContactEmail extends ActiveRecord implements TariffInterface
{
    public static function tableName(): string
    {
        return 'gardener_email';
    }

    public static function isDouble(DbContactEmail $newEmail): bool
    {
        return self::find()->where(['gardener' => $newEmail->gardener, 'address' => $newEmail->address])->count() > 0;
    }
}