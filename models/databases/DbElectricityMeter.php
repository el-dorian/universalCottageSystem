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
 * @property int $cottage [int(10) unsigned]
 * @property int $indication [int(11)]
 * @property string $description
 * @property bool $is_enabled [tinyint(1)]
 * @property string $last_filled_period [varchar(10)]  Период, за который внесены последние показания
 */
class DbElectricityMeter extends ActiveRecord implements TariffInterface
{


    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';

    #[ArrayShape([self::SCENARIO_DEFAULT => "string[]", self::SCENARIO_CREATE => "string[]", self::SCENARIO_EDIT => "string[]"])] public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['id', 'cottage', 'indication', 'description', 'is_enabled', 'last_filled_period'],
            self::SCENARIO_CREATE => ['id', 'cottage', 'indication', 'description', 'is_enabled', 'last_filled_period'],
            self::SCENARIO_EDIT => [ 'description', 'is_enabled'],
        ];
    }

    public static function tableName(): string
    {
        return 'electricity_meters';
    }

    public function rules(): array
    {
        return [
            [['is_enabled'], 'required'],
            [['cottage', 'indication', 'is_enabled', 'last_filled_period'], 'required', 'on' => [self::SCENARIO_CREATE]],
            ['description', 'string', 'skipOnEmpty' => true],
            [['indication'], 'number', 'min' => 0],
            ['last_filled_period', ElectricityMonthValidator::class],
        ];
    }


    #[ArrayShape(['description' => "string", 'is_enabled' => "string", 'last_filled_period' => "string", 'indication' => "string"])] public function attributeLabels(): array
    {
        return [
            'description' => 'Комментарий',
            'is_enabled' => 'Счётчик активен',
            'last_filled_period' => 'Последний заполненный период',
            'indication' => 'Показания счётчика на момент регистрации',
        ];
    }

    /**
     * @throws MyException
     * @throws DbSettingsException
     */
    public function add(): void
    {
        // регистрирую
        $dbTransaction = new DbTransaction();
        $this->save();
        ElectricityAccrualsHandler::registerNewCounter($this, $this->last_filled_period);
        $dbTransaction->commitTransaction();
    }
}