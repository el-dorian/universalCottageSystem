<?php


namespace app\models\bank;


use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;

class BankPreferencesEditor extends Model
{
    public const SCENARIO_CHANGE = 'change';

    #[ArrayShape([self::SCENARIO_CHANGE => "string[]"])] public function scenarios(): array
    {
        return [
            self::SCENARIO_CHANGE => [
                'name',
                'personalAcc',
                'bankName',
                'bik',
                'correspAcc',
                'payerInn',
                'kpp',
            ],
        ];
    }

    public string $name;
    public string $personalAcc;
    public string $bankName;
    public string $bik;
    public string $correspAcc;
    public string $payerInn;
    public string $kpp;


    public function attributeLabels(): array
    {
        return [
            'name' => 'Название огранизации',
            'personalAcc' => 'Номер счёта',
            'bankName' => "Название банка",
            'bik' => "БИК",
            'correspAcc' => "Корр. счёт",
            'payerInn' => "ИНН плательщика",
            'kpp' => "КПП",
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $currentSettings = BankPreferences::getInstance();
        $this->name = $currentSettings->name;
        $this->personalAcc = $currentSettings->personalAcc;
        $this->bankName = $currentSettings->bankName;
        $this->bik = $currentSettings->bik;
        $this->correspAcc = $currentSettings->correspAcc;
        $this->payerInn = $currentSettings->payerInn;
        $this->kpp = $currentSettings->kpp;
    }

    public function rules(): array
    {
        return [
            [['name', 'personalAcc', 'bankName', 'bik', 'correspAcc', 'payerInn', 'kpp'], 'required']
        ];
    }

    public function saveSettings(): bool
    {
        BankPreferences::getInstance()->saveNewSettings(
            $this->name,
            $this->personalAcc,
            $this->bankName,
            $this->bik,
            $this->correspAcc,
            $this->payerInn,
            $this->kpp,
        );
        return true;
    }

}