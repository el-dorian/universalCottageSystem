<?php


namespace app\models\databases;

use yii\db\ActiveRecord;

/**
 * Class DbCottage
 * @package app\models\databases
 *
 * @property int $id [int(10) unsigned]
 * @property string $alias [varchar(255)]
 * @property int $master_cottage_id [int(10) unsigned]
 * @property int $total_debt [int(10) unsigned]
 * @property int $debt_target [int(10) unsigned]
 * @property int $debt_membership [int(10) unsigned]
 * @property int $debt_electricity [int(10) unsigned]
 * @property int $debt_single [int(10) unsigned]
 * @property bool $has_expired_debt [tinyint(1)]
 * @property bool $has_opened_bill [tinyint(1)]
 * @property bool $has_contact_mail [tinyint(1)]
 * @property int $square [int(11)]
 * @property int $deposit [int(11)]
 * @property bool $is_pay_for_electricity [tinyint(1)]
 * @property bool $is_pay_for_membership [tinyint(1)]
 * @property bool $is_pay_for_target [tinyint(1)]
 * @property string $registration_information
 * @property string $comment
 */


class DbCottage extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'cottage';
    }

    public static function getByAlias(string $alias): ?DbCottage
    {
        return self::findOne(['alias' => $alias]);
    }

}