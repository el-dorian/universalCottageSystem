<?php


namespace app\models\db;


use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class DbRestoreModel extends Model
{
    public ?UploadedFile $file = null;

    public function rules(): array
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false],
            [['file'], 'required'],
        ];
    }

    public function restore(): void
    {
        $file = $this->file->tempName;
        $fileName = Yii::$app->basePath . '/storage/db.sql';
        file_put_contents($fileName, file_get_contents($file));
        $cmd = DbSettings::getInstance()->mySqlPath . ' --user=' . DbSettings::getInstance()->dbLogin . ' --password=' . DbSettings::getInstance()->dbPass . ' ' . DbSettings::getInstance()->dbName . ' < ' . $fileName;
        exec($cmd);
        Yii::$app->session->addFlash('success', 'База данных восстановлена!');
    }
}