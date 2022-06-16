<?php


namespace app\models\bank;


class BankPreferences
{
    public string $st = 'ST00012';
    public string $name;
    public string $personalAcc;
    public string $bankName;
    public string $bik;
    public string $correspAcc;
    public string $payerInn;
    public string $kpp;

    private function __construct($name, $personalAcc, $bankName, $bik, $correspAcc, $payerInn, $kpp)
    {
        $this->name = $name;
        $this->personalAcc = $personalAcc;
        $this->bankName = $bankName;
        $this->bik = $bik;
        $this->correspAcc = $correspAcc;
        $this->payerInn = $payerInn;
        $this->kpp = $kpp;
    }

    private static ?BankPreferences $instance = null;

    public static function getInstance(): BankPreferences
    {
        if (self::$instance === null) {
            $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/bank_preferences.ini';
            if (!is_file($settingsFile)) {
                file_put_contents($settingsFile, "test\ntest\ntest\ntest\ntest\ntest\ntest\ntest");
            }
            $settingsString = file_get_contents($settingsFile);
            $settingsArray = explode("\n", $settingsString);
            self::$instance = new BankPreferences($settingsArray[0], $settingsArray[1], $settingsArray[2], $settingsArray[3], $settingsArray[4], $settingsArray[5], $settingsArray[6]);
        }
        return self::$instance;
    }

    public function saveNewSettings(string $name, string $personalAcc, string $bankName, string $bik, string $correspAcc, string $payerInn, string $kpp): void
    {
        $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/bank_preferences.ini';
        file_put_contents($settingsFile, "$name\n$personalAcc\n$bankName\n$bik\n$correspAcc\n$payerInn\n$kpp");
        $this->name = $name;
        $this->personalAcc = $personalAcc;
        $this->bankName = $bankName;
        $this->bik = $bik;
        $this->correspAcc = $correspAcc;
        $this->payerInn = $payerInn;
        $this->kpp = $kpp;
    }
}