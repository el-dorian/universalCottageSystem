<?php


namespace app\models\databases;

use yii\db\ActiveRecord;

/**
 * Class DbCottage
 * @package app\models\databases
 *
 * @property int $id [int(10) unsigned]
 * @property int $bound_bill_id [int(10) unsigned]
 * @property int $bound_transaction_id [int(10) unsigned]
 * @property int $sum [int(10) unsigned]
 * @property string $direction [enum('incoming', 'outgoing')]
 * @property int $cottage_deposit_before [int(10) unsigned]
 * @property int $cottage_deposit_after [int(10) unsigned]
 * @property int $action_timestamp [int(10) unsigned]
 * @property string $description
 * @property int $cottage [int(11)]
 */


class DbDepositTransfer extends ActiveRecord
{
    const INCOMING = 'incoming';
    const OUTGOING = 'outgoing';

    public static function tableName(): string
    {
        return 'deposit_transfer';
    }

}