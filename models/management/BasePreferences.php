<?php


namespace app\models\management;


class BasePreferences
{
    public string $sntName;
    public bool $useTelegramBot;
    public string $telegramApiKey;
    public string $telegramSecret;
    public bool $sendDebugToTelegram;
    public bool $sendDbBackupToTelegram;
    // tariffs
    public bool $payTarget;
    public string $targetPaymentType;
    public string $membershipPaymentType;
    public bool $payFines;

    private function __construct(
        string $sntName,
        bool $useTelegramBot,
        string $telegramApiKey,
        string $telegramSecret,
        bool $sendDebugToTelegram,
        bool $sendDbBackupToTelegram,
        bool $payTarget,
        string $targetPaymentType,
        string $membershipPaymentType,
        bool $payFines
    )
    {
        $this->refreshData($sntName, $useTelegramBot, $telegramApiKey, $telegramSecret, $sendDebugToTelegram, $sendDbBackupToTelegram, $payTarget, $targetPaymentType, $membershipPaymentType, $payFines);
    }

    private static ?BasePreferences $instance = null;


    public static function getInstance(): BasePreferences
    {
        if (self::$instance === null) {
            $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/base_preferences.ini';
            if (!is_file($settingsFile)) {
                file_put_contents($settingsFile, "");
            }
            $settingsString = file_get_contents($settingsFile);
            $settingsArray = explode("\n", $settingsString);
            self::$instance = new BasePreferences(
                empty($settingsArray[0]) ? 'not set' : $settingsArray[0],
                $settingsArray[1] === '1',
                empty($settingsArray[2]) ? '0' : $settingsArray[2],
                empty($settingsArray[3]) ? '0' : $settingsArray[3],
                $settingsArray[4] === '1',
                $settingsArray[5] === '1',
                $settingsArray[6] === '1',
                empty($settingsArray[7]) ? '0' : $settingsArray[7],
                empty($settingsArray[8]) ? '0' : $settingsArray[8],
                $settingsArray[9] === '1',
            );
        }
        return self::$instance;
    }

    public function saveNewSettings(
        string $sntName,
        bool $useTelegramBot,
        string $telegramApiKey,
        string $telegramSecret,
        bool $sendDebugToTelegram,
        bool $sendDbBackupToTelegram,
        bool $payTarget,
        string $targetPaymentType,
        string $membershipPaymentType,
        bool $payFines
    ): void
    {
        $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/base_preferences.ini';
        file_put_contents($settingsFile, "$sntName\n$useTelegramBot\n$telegramApiKey\n$telegramSecret\n$sendDebugToTelegram\n$sendDbBackupToTelegram\n$payTarget\n$targetPaymentType\n$membershipPaymentType\n$payFines"
        );
        $this->refreshData($sntName, $useTelegramBot, $telegramApiKey, $telegramSecret, $sendDebugToTelegram, $sendDbBackupToTelegram, $payTarget, $targetPaymentType, $membershipPaymentType, $payFines);
    }

    /**
     * @param string $sntName
     * @param bool $useTelegramBot
     * @param string $telegramApiKey
     * @param string $telegramSecret
     * @param bool $sendDebugToTelegram
     * @param bool $sendDbBackupToTelegram
     * @param bool $payTarget
     * @param string $targetPaymentType
     * @param string $membershipPaymentType
     * @param bool $payFines
     */
    public function refreshData(string $sntName, bool $useTelegramBot, string $telegramApiKey, string $telegramSecret, bool $sendDebugToTelegram, bool $sendDbBackupToTelegram, bool $payTarget, string $targetPaymentType, string $membershipPaymentType, bool $payFines): void
    {
        $this->sntName = $sntName;
        $this->useTelegramBot = $useTelegramBot;
        $this->telegramApiKey = $telegramApiKey;
        $this->telegramSecret = $telegramSecret;
        $this->sendDebugToTelegram = $sendDebugToTelegram;
        $this->sendDbBackupToTelegram = $sendDbBackupToTelegram;
        $this->payTarget = $payTarget;
        $this->targetPaymentType = $targetPaymentType;
        $this->membershipPaymentType = $membershipPaymentType;
        $this->payFines = $payFines;
    }
}