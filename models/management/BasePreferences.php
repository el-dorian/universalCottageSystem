<?php


namespace app\models\management;


class BasePreferences
{
    public string $sntName;
    public bool $useTelegramBot;
    public string $telegramApiKey;
    public string $telegramSecret;
    public string $sendDebugToTelegram;
    public string $sendDbBackupToTelegram;

    private function __construct(
        string $sntName,
        bool $useTelegramBot,
        string $telegramApiKey,
        string $telegramSecret,
        bool $sendDebugToTelegram,
        bool $sendDbBackupToTelegram,
    )
    {
        $this->sntName = $sntName;
        $this->useTelegramBot = $useTelegramBot;
        $this->telegramApiKey = $telegramApiKey;
        $this->telegramSecret = $telegramSecret;
        $this->sendDebugToTelegram = $sendDebugToTelegram;
        $this->sendDbBackupToTelegram = $sendDbBackupToTelegram;
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
                empty($settingsArray[0]) ? '' : $settingsArray[0],
                !empty($settingsArray[1]) && $settingsArray[1] === '1',
                empty($settingsArray[2]) ? '' : $settingsArray[2],
                empty($settingsArray[3]) ? '' : $settingsArray[3],
                !empty($settingsArray[4]) && $settingsArray[4] === '1',
                !empty($settingsArray[5]) && $settingsArray[5] === '1',
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
        bool $sendDbBackupToTelegram): void
    {
        $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/base_preferences.ini';
        file_put_contents($settingsFile, "$sntName\n$useTelegramBot\n$telegramApiKey\n$telegramSecret\n$sendDebugToTelegram\n$sendDbBackupToTelegram");
        $this->sntName = $sntName;
        $this->useTelegramBot = $useTelegramBot;
        $this->telegramApiKey = $telegramApiKey;
        $this->telegramSecret = $telegramSecret;
        $this->sendDebugToTelegram = $sendDebugToTelegram;
        $this->sendDbBackupToTelegram = $sendDbBackupToTelegram;
    }
}