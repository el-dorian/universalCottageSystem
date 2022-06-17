<?php


namespace app\models\management;


use Exception;
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
                'payTarget',
                'targetPaymentType',
                'membershipPaymentType',
                'payFines',
            ],
        ];
    }

    public string $sntName;
    public bool $useTelegramBot;
    public string $telegramApiKey;
    public string $telegramSecret;
    public bool $sendDebugToTelegram;
    public bool $sendDbBackupToTelegram;
    public bool $payTarget;
    public string $targetPaymentType;
    public string $membershipPaymentType;
    public bool $payFines;


 public function attributeLabels(): array
    {
        return [
            'sntName' => 'Имя СНТ',
            'useTelegramBot' => 'Использовать Телеграм-бот',
            'telegramApiKey' => 'API-key телеграма',
            'telegramSecret' => 'Секретная фраза для подключения администраторов к Телеграм-боту',
            'sendDebugToTelegram' => 'Отправка данных отладки в телеграм-бот',
            'sendDbBackupToTelegram' => 'Отправка резервной копии базы данных в телеграм при входе пользователя в управление программой',
            'payTarget' => 'Оплачивать целевые взносы',
            'targetPaymentType' => 'Принцип оплаты целевых взносов',
            'membershipPaymentType' => 'Принцип оплаты членских взносов',
            'payFines' => 'Оплачивать пени',
        ];
    }

    /**
     * @throws Exception
     */
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
        $this->payTarget = $currentSettings->payTarget;
        $this->targetPaymentType = $currentSettings->targetPaymentType;
        $this->membershipPaymentType = $currentSettings->membershipPaymentType;
        $this->payFines = $currentSettings->payFines;
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
                'sendDbBackupToTelegram',
                'payTarget',
                'targetPaymentType',
                'membershipPaymentType',
                'payFines',
                ], 'required']
        ];
    }

    /**
     * @throws Exception
     */
    public function saveSettings(): bool
    {
        BasePreferences::getInstance()->saveNewSettings(
            $this->sntName,
            $this->useTelegramBot,
            $this->telegramApiKey,
            $this->telegramSecret,
            $this->sendDebugToTelegram,
            $this->sendDbBackupToTelegram,
            $this->payTarget,
            $this->targetPaymentType,
            $this->membershipPaymentType,
            $this->payFines,
        );
        return true;
    }

}