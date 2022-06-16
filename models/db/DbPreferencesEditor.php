<?php


namespace app\models\db;


use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;

class DbPreferencesEditor extends Model
{
    public const SCENARIO_CHANGE = 'change';

    #[ArrayShape([self::SCENARIO_CHANGE => "string[]"])] public function scenarios(): array
    {
        return [
            self::SCENARIO_CHANGE => [
                'dsn',
                'dbLogin',
                'dbPass',
                'dbName',
                'mySqlPath',
            ],
        ];
    }

    public string $dsn;
    public string $dbLogin;
    public string $dbPass;
    public string $dbName;
    public string $mySqlPath;



 #[ArrayShape(['dsn' => "string", 'dbLogin' => "string", 'dbPass' => "string", 'dbName' => "string", 'mySqlPath' => "string"])] public function attributeLabels(): array
    {
        return [
            'dsn' => 'DSN',
            'dbLogin' => 'Логин',
            'dbPass' => 'Пароль',
            'dbName' => 'Имя базы данных',
            'mySqlPath' => 'Путь к исполняемому файлу MySql',
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $currentSettings = DbSettings::getInstance();
        $this->dsn = $currentSettings->dsn;
        $this->dbLogin = $currentSettings->dbLogin;
        $this->dbPass = $currentSettings->dbPass;
        $this->dbName = $currentSettings->dbName;
        $this->mySqlPath = $currentSettings->mySqlPath;
    }

    public function rules(): array
    {
        return [
            [['dsn', 'dbLogin', 'dbPass', 'dbName'], 'required']
        ];
    }

    public function saveSettings(): bool
    {
        DbSettings::getInstance()->saveNewSettings(
            $this->dsn,
            $this->dbLogin,
            $this->dbPass,
            $this->dbName,
            $this->mySqlPath
        );
        return true;
    }

}