<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 18.09.2018
 * Time: 16:35
 */

namespace app\controllers;

use app\models\bank\BankPreferencesEditor;
use app\models\db\DbBackupModel;
use app\models\db\DbRestoreModel;
use app\models\email\MailPreferencesEditor;
use app\models\selections\AjaxRequestStatus;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class MiscController extends Controller
{
    /**
     * @return array
     */
    #[ArrayShape(['access' => "array"])] public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    return $this->redirect('/access-denied', 301);
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'make-db-backup',
                            'download-db-backup',
                            'restore-db',
                        ],
                        'roles' => ['writer'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionRestoreDb(): Response
    {
        if (Yii::$app->request->isPost) {
            $restoreModel = new DbRestoreModel();
            $restoreModel->file = UploadedFile::getInstance($restoreModel, 'file');
            $restoreModel->restore();
            return $this->redirect('/management/index#db_prefs', 301);
        }
        throw new NotFoundHttpException('Страница не найдена');
    }

    public function actionMakeDbBackup(): void
    {
        (new DbBackupModel())->backup();
    }
    public function actionDownloadDbBackup(): void
    {
        $path = Yii::$app->basePath . '/storage/db.sql';
        $date = new DateTime();
        $d = $date->format('Y-m-d H:i:s');
        Yii::$app->response->sendFile($path, "Резервная копия базы данных СНТ $d.sql");
    }
}