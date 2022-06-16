<?php


namespace app\models\email;


use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;

class MailPreferencesEditor extends Model
{
    public const SCENARIO_CHANGE = 'change';

    #[ArrayShape([self::SCENARIO_CHANGE => "string[]"])] public function scenarios(): array
    {
        return [
            self::SCENARIO_CHANGE => [
                'senderServer',
                'senderEmail',
                'senderLogin',
                'senderPass',
                'senderName',
                'debugSend',
                'testEmailAddress',
                'sendToReserveAddress',
                'reserveEmailAddress',
            ],
        ];
    }

    public string $senderName;
    public string $senderEmail;
    public string $senderLogin;
    public string $senderPass;
    public string $senderServer;
    public bool $debugSend;
    public bool $sendToReserveAddress;
    public string $testEmailAddress;
    public string $reserveEmailAddress;


    #[ArrayShape(['senderServer' => "string", 'senderEmail' => "string", 'senderPass' => "string", 'senderLogin' => "string", 'senderName' => "string", 'debugSend' => "string", 'testEmailAddress' => "string", 'sendToReserveAddress' => "string", 'reserveEmailAddress' => "string"])] public function attributeLabels(): array
    {
        return [
            'senderServer' => 'Сервер SMTP',
            'senderEmail' => 'Адрес почты',
            'senderPass' => 'Пароль почты',
            'senderLogin' => 'Имя пользователя',
            'senderName' => 'Имя отправителя',
            'debugSend' => 'Письма вместо адресатов отправляются на нижеуказанный адрес почты',
            'testEmailAddress' => 'Адрес почты для теста',
            'sendToReserveAddress' => 'Отправка резервной копии письма',
            'reserveEmailAddress' => 'Адрес резервной почты',
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $currentSettings = MailPreferences::getInstance();
        $this->senderName = $currentSettings->senderName;
        $this->senderEmail = $currentSettings->senderEmail;
        $this->senderLogin = $currentSettings->senderLogin;
        $this->senderPass = $currentSettings->senderPass;
        $this->senderServer = $currentSettings->senderServer;
        $this->debugSend = $currentSettings->debugSend;
        $this->sendToReserveAddress = $currentSettings->sendToReserveAddress;
        $this->testEmailAddress = $currentSettings->testEmailAddress;
        $this->reserveEmailAddress = $currentSettings->reserveEmailAddress;
    }

    public function rules(): array
    {
        return [
            [['senderServer', 'senderEmail', 'senderPass', 'senderLogin', 'senderName', 'debugSend', 'testEmailAddress', 'sendToReserveAddress'], 'required'],
            [['testEmailAddress', 'reserveEmailAddress', $this->senderEmail], 'email']
        ];
    }

    public function saveSettings(): bool
    {
        MailPreferences::getInstance()->saveNewSettings(
            $this->senderName,
            $this->senderEmail,
            $this->senderLogin,
            $this->senderPass,
            $this->senderServer,
            $this->debugSend,
            $this->sendToReserveAddress,
            $this->testEmailAddress,
            $this->reserveEmailAddress
        );
        return true;
    }

}