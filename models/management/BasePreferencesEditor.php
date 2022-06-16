<?php


namespace app\models\management;


use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;

class BasePreferencesEditor extends Model
{
    public const SCENARIO_CHANGE = 'change';

    #[ArrayShape([self::SCENARIO_CHANGE => "string[]"])] public function scenarios(): array
    {
        return [
            self::SCENARIO_CHANGE => [
                'sntName',
                'useTelegramBot',
                'telegramApiKey',
                'telegramSecret',
                'sendDebugToTelegram',
                'sendDbBackupToTelegram',
            ],
        ];
    }

    public string $sntName;
    public bool $useTelegramBot;
    public string $telegramApiKey;
    public string $telegramSecret;
    public bool $sendDebugToTelegram;
    public bool $sendDbBackupToTelegram;


 #[ArrayShape(['sntName' => "string", 'useTelegramBot' => "string", 'telegramApiKey' => "string", 'telegramSecret' => "string", 'sendDebugToTelegram' => "string", 'sendDbBackupToTelegram' => "string"])] public function attributeLabels(): array
    {
        return [
            'sntName' => 'Имя СНТ',
            'useTelegramBot' => 'Использовать Телеграм-бот',
            'telegramApiKey' => 'API-key телеграма',
            'telegramSecret' => 'Секретная фраза для подключения администраторов к Телеграм-боту',
            'sendDebugToTelegram' => 'Отправка данных отладки в телеграм-бот',
            'sendDbBackupToTelegram' => 'Отправка резервной копии базы данных в телеграм при входе пользователя в управление программой',
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $currentSettings = BasePreferences::getInstance();
        $this->sntName = $currentSettings->sntName;
        $this->useTelegramBot = $currentSettings->useTelegramBot;
        $this->telegramApiKey = $currentSettings->telegramApiKey;
        $this->telegramSecret = $currentSettings->telegramSecret;
        $this->sendDebugToTelegram = $currentSettings->sendDebugToTelegram;
        $this->sendDbBackupToTelegram = $currentSettings->sendDbBackupToTelegram;
    }

    public function rules(): array
    {
        return [
            [[
                'sntName',
                'useTelegramBot',
                'telegramApiKey',
                'telegramSecret',
                'sendDebugToTelegram',
                'sendDbBackupToTelegram',], 'required']
        ];
    }

    public function saveSettings(): bool
    {
        BasePreferences::getInstance()->saveNewSettings(
            $this->sntName,
            $this->useTelegramBot,
            $this->telegramApiKey,
            $this->telegramSecret,
            $this->sendDebugToTelegram,
            $this->sendDbBackupToTelegram,
        );
        return true;
    }

}