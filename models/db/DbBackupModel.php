<?php


namespace app\models\db;


use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class DbBackupModel
{


    public function backup(): void
    {
        $command = DbSettings::getInstance()->mySqlPath . 'dump --user=' . DbSettings::getInstance()->dbLogin . ' --password=' . DbSettings::getInstance()->dbPass . ' ' . DbSettings::getInstance()->dbName . ' --skip-add-locks > ' . Yii::$app->basePath . '/storage/db.sql';
        exec($command);
    }
}