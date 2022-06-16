<?php


namespace app\models\email;


class MailPreferences
{
    public string $senderName;
    public string $senderEmail;
    public string $senderLogin;
    public string $senderPass;
    public string $senderServer;
    public bool $debugSend;
    public bool $sendToReserveAddress;
    public string $testEmailAddress;
    public string $reserveEmailAddress;

    private function __construct($senderName, $senderEmail, $senderLogin, $senderPass, $senderServer, $debugSend, $sendToReserveAddress, $testEmailAddress, $reserveEmailAddress)
    {
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->senderLogin = $senderLogin;
        $this->senderPass = $senderPass;
        $this->senderServer = $senderServer;
        $this->debugSend = $debugSend === '1';
        $this->sendToReserveAddress = $sendToReserveAddress === '1';
        $this->testEmailAddress = $testEmailAddress;
        $this->reserveEmailAddress = $reserveEmailAddress;
    }

    private static ?MailPreferences $instance = null;

    public static function getInstance(): MailPreferences
    {
        if (self::$instance === null) {
            $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/email_preferences.ini';
            if (!is_file($settingsFile)) {
                file_put_contents($settingsFile, "test\ntest\ntest\ntest\ntest\ntest\ntest\ntest\ntest");
            }
            $settingsString = file_get_contents($settingsFile);
            $settingsArray = explode("\n", $settingsString);
            self::$instance = new MailPreferences($settingsArray[0], $settingsArray[1], $settingsArray[2], $settingsArray[3], $settingsArray[4], $settingsArray[5], $settingsArray[6], $settingsArray[7], $settingsArray[8]);
        }
        return self::$instance;
    }

    public function saveNewSettings(
        string $senderName,
        string $senderEmail,
        string $senderLogin,
        string $senderPass,
        string $senderServer,
        bool $debugSend,
        bool $sendToReserveAddress,
        string $testEmailAddress,
        string $reserveEmailAddress): void
    {
        $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/email_preferences.ini';
        file_put_contents($settingsFile, "$senderName\n$senderEmail\n$senderLogin\n$senderPass\n$senderServer\n$debugSend\n$sendToReserveAddress\n$testEmailAddress\n$reserveEmailAddress");
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->senderLogin = $senderLogin;
        $this->senderPass = $senderPass;
        $this->senderServer = $senderServer;
        $this->debugSend = $debugSend;
        $this->sendToReserveAddress = $sendToReserveAddress;
        $this->testEmailAddress = $testEmailAddress;
        $this->reserveEmailAddress = $reserveEmailAddress;
    }
}