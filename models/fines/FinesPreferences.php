<?php


namespace app\models\fines;


class FinesPreferences
{
    public bool $payElectricityFines;
    public bool $payMembershipFines;
    public bool $payTargetFines;
    public string $electricityFinesRate;
    public string $membershipFinesRate;
    public string $targetFinesRate;
    public string $electricityPeriodForPayment;
    public string $membershipPeriodForPayment;
    public string $targetPeriodForPayment;

    private function __construct(
        $payElectricityFines,
        $payMembershipFines,
        $payTargetFines,
        $electricityFinesRate,
        $membershipFinesRate,
        $targetFinesRate,
        $electricityPeriodForPayment,
        $membershipPeriodForPayment,
        $targetPeriodForPayment,
    )
    {
        $this->updateData($payElectricityFines, $payMembershipFines, $payTargetFines, $electricityFinesRate, $membershipFinesRate, $targetFinesRate, $electricityPeriodForPayment, $membershipPeriodForPayment, $targetPeriodForPayment);
    }

    private static ?FinesPreferences $instance = null;

    public static function getInstance(): FinesPreferences
    {
        if (self::$instance === null) {
            $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/fines_preferences.ini';
            if (!is_file($settingsFile)) {
                file_put_contents($settingsFile, "0\n0\n0\n0\n0\n0\n0\n0\n0");
            }
            $settingsString = file_get_contents($settingsFile);
            $settingsArray = explode("\n", $settingsString);
            self::$instance = new FinesPreferences(
                $settingsArray[0],
                $settingsArray[1],
                $settingsArray[2],
                $settingsArray[3],
                $settingsArray[4],
                $settingsArray[5],
                $settingsArray[6],
                $settingsArray[7],
                $settingsArray[8]
            );
        }
        return self::$instance;
    }

    public function saveNewSettings(
        $payElectricityFines,
        $payMembershipFines,
        $payTargetFines,
        $electricityFinesRate,
        $membershipFinesRate,
        $targetFinesRate,
        $electricityPeriodForPayment,
        $membershipPeriodForPayment,
        $targetPeriodForPayment): void
    {
        $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/fines_preferences.ini';
        file_put_contents($settingsFile, "$payElectricityFines\n$payMembershipFines\n$payTargetFines\n$electricityFinesRate\n$membershipFinesRate\n$targetFinesRate\n$electricityPeriodForPayment\n$membershipPeriodForPayment\n$targetPeriodForPayment");
        $this->updateData($payElectricityFines, $payMembershipFines, $payTargetFines, $electricityFinesRate, $membershipFinesRate, $targetFinesRate, $electricityPeriodForPayment, $membershipPeriodForPayment, $targetPeriodForPayment);
    }

    /**
     * @param $payElectricityFines
     * @param $payMembershipFines
     * @param $payTargetFines
     * @param $electricityFinesRate
     * @param $membershipFinesRate
     * @param $targetFinesRate
     * @param $electricityPeriodForPayment
     * @param $membershipPeriodForPayment
     * @param $targetPeriodForPayment
     */
    public function updateData($payElectricityFines, $payMembershipFines, $payTargetFines, $electricityFinesRate, $membershipFinesRate, $targetFinesRate, $electricityPeriodForPayment, $membershipPeriodForPayment, $targetPeriodForPayment): void
    {
        $this->payElectricityFines = $payElectricityFines;
        $this->payMembershipFines = $payMembershipFines;
        $this->payTargetFines = $payTargetFines;
        $this->electricityFinesRate = $electricityFinesRate;
        $this->membershipFinesRate = $membershipFinesRate;
        $this->targetFinesRate = $targetFinesRate;
        $this->electricityPeriodForPayment = $electricityPeriodForPayment;
        $this->membershipPeriodForPayment = $membershipPeriodForPayment;
        $this->targetPeriodForPayment = $targetPeriodForPayment;
    }
}