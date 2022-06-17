<?php


namespace app\models\fines;


use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;

class FinesPreferencesEditor extends Model
{
    public const SCENARIO_CHANGE = 'change';

    #[ArrayShape([self::SCENARIO_CHANGE => "string[]"])] public function scenarios(): array
    {
        return [
            self::SCENARIO_CHANGE => [
                'payElectricityFines',
                'payMembershipFines',
                'payTargetFines',
                'electricityFinesRate',
                'membershipFinesRate',
                'targetFinesRate',
                'electricityPeriodForPayment',
                'membershipPeriodForPayment',
                'targetPeriodForPayment',
            ],
        ];
    }

    public bool $payElectricityFines;
    public bool $payMembershipFines;
    public bool $payTargetFines;
    public string $electricityFinesRate;
    public string $membershipFinesRate;
    public string $targetFinesRate;
    public string $electricityPeriodForPayment;
    public string $membershipPeriodForPayment;
    public string $targetPeriodForPayment;


    #[ArrayShape(['payElectricityFines' => "string", 'payMembershipFines' => "string", 'payTargetFines' => "string", 'electricityFinesRate' => "string", 'membershipFinesRate' => "string", 'targetFinesRate' => "string", 'electricityPeriodForPayment' => "string", 'membershipPeriodForPayment' => "string", 'targetPeriodForPayment' => "string"])] public function attributeLabels(): array
    {
        return [
            'payElectricityFines' => 'Оплачивать пени по электроэнергии',
            'payMembershipFines' => 'Оплачивать пени по членским взносам',
            'payTargetFines' => 'Оплачивать пени по целевым взносам',
            'electricityFinesRate' => 'Ставка пени по электроэнергии (% в день)',
            'membershipFinesRate' => 'Ставка пени по членским (% в день)',
            'targetFinesRate' => 'Ставка пени по целевым (% в день)',
            'electricityPeriodForPayment' => 'Период для оплаты электроэнергии (дней) до начисления пени',
            'membershipPeriodForPayment' => 'Период для оплаты членских (дней) до начисления пени',
            'targetPeriodForPayment' => 'Период для оплаты целевых (дней) до начисления пени',
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $currentSettings = FinesPreferences::getInstance();
        $this->payElectricityFines = $currentSettings->payElectricityFines;
        $this->payMembershipFines = $currentSettings->payMembershipFines;
        $this->payTargetFines = $currentSettings->payTargetFines;
        $this->electricityFinesRate = $currentSettings->electricityFinesRate;
        $this->membershipFinesRate = $currentSettings->membershipFinesRate;
        $this->targetFinesRate = $currentSettings->targetFinesRate;
        $this->electricityPeriodForPayment = $currentSettings->electricityPeriodForPayment;
        $this->membershipPeriodForPayment = $currentSettings->membershipPeriodForPayment;
        $this->targetPeriodForPayment = $currentSettings->targetPeriodForPayment;
    }

    public function rules(): array
    {
        return [
            [[
                'payElectricityFines',
                'payMembershipFines',
                'payTargetFines',
                'electricityFinesRate',
                'membershipFinesRate',
                'targetFinesRate',
                'electricityPeriodForPayment',
                'membershipPeriodForPayment',
                'targetPeriodForPayment',
            ], 'required'],
        ];
    }

    public function saveSettings(): bool
    {
        FinesPreferences::getInstance()->saveNewSettings(
            $this->payElectricityFines,
            $this->payMembershipFines,
            $this->payTargetFines,
            $this->electricityFinesRate,
            $this->membershipFinesRate,
            $this->targetFinesRate,
            $this->electricityPeriodForPayment,
            $this->membershipPeriodForPayment,
            $this->targetPeriodForPayment
        );
        return true;
    }

}