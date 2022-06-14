<?php

use app\models\db\DbSettings;
use yii\db\Connection;

require_once __DIR__ . '/../models/db/DbSettings.php';

$settings = DbSettings::getInstance();

return [
    'class' => Connection::class,
    'dsn' => $settings->dsn,
    'username' => $settings->dbLogin,
    'password' => $settings->dbPass,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
