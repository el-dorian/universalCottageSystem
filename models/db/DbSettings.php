<?php


namespace app\models\db;


class DbSettings
{
    public string $dsn;
    public string $dbLogin;
    public string $dbPass;
    public string $dbName;
    public string $mySqlPath;

    private function __construct($connectionName, $dbLogin, $dbPass, $dbName, $mySqlPath)
    {
        $this->dsn = $connectionName;
        $this->dbLogin = $dbLogin;
        $this->dbPass = $dbPass;
        $this->dbName = $dbName;
        $this->mySqlPath = $mySqlPath;
    }

    private static ?DbSettings $instance = null;

    public static function getInstance(): DbSettings
    {
        if (self::$instance === null) {
            $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/db_preferences.ini';
            if (!is_file($settingsFile)) {
                file_put_contents($settingsFile, "test\ntest\ntest\ntest\ntest");
            }
            $settingsString = file_get_contents($settingsFile);
            $settingsArray = explode("\n", $settingsString);
            self::$instance = new DbSettings($settingsArray[0], $settingsArray[1], $settingsArray[2], $settingsArray[3], $settingsArray[4]);
        }
        return self::$instance;
    }

    public function saveNewSettings(string $dsn, string $dbLogin, string $dbPass, string $dbName, string $mySqlPath): void
    {
        $settingsFile = $_SERVER['DOCUMENT_ROOT'] . '/../settings/db_preferences.ini';
        file_put_contents($settingsFile, "$dsn\n$dbLogin\n$dbPass\n$dbName\n$mySqlPath");
        $this->dsn = $dsn;
        $this->dbLogin = $dbLogin;
        $this->dbPass = $dbPass;
        $this->dbName = $dbName;
        $this->mySqlPath = $mySqlPath;
    }
}