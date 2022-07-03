<?php


namespace app\models\databases;

use app\models\electricity\ElectricityAccrualsHandler;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use app\models\interfaces\TariffInterface;
use app\models\utils\DbTransaction;
use app\validators\ElectricityMonthValidator;
use JetBrains\PhpStorm\ArrayShape;
use yii\db\ActiveRecord;

/**
 * @package app\models\databases
 *
 * @property int $id [int(10) unsigned]
 * @property string $alias [varchar(255)]
 * @property int $cottage [int(10) unsigned]
 * @property int $cost [int(10) unsigned]
 * @property int $time_of_create [int(10) unsigned]
 * @property string $is_payed [enum('yes', 'no', 'partial')]
 * @property int $paid [int(10) unsigned]
 * @property int $deposit_used [int(10) unsigned]
 * @property int $deposit_gained [int(10) unsigned]
 * @property int $discount [int(10) unsigned]
 * @property bool $is_email_sent [tinyint(1) unsigned]
 * @property bool $is_invoice_printed [tinyint(1) unsigned]
 * @property int $payer [int(10) unsigned]
 */
class DbPaymentInvoice extends ActiveRecord implements TariffInterface
{

    public static function tableName(): string
    {
        return 'payment_invoice';
    }
}