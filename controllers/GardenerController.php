<?php

namespace app\controllers;

use app\models\databases\DbContactEmail;
use app\models\databases\DbContactPhone;
use app\models\databases\DbGardener;
use app\models\exceptions\DbSettingsException;
use app\models\selections\AjaxRequestStatus;
use app\models\utils\DbTransaction;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class GardenerController extends Controller
{

    #[ArrayShape(['access' => "array"])] public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    return $this->redirect('/site/deny', 301);
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'delete',
                        ],
                        'roles' => ['writer'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws StaleObjectException|DbSettingsException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            // удаляю данные по садоводу, если они есть
            $gardener = DbGardener::findOne($id);
            if ($gardener !== null) {
                $transaction = new DbTransaction();
                // удалю учётную запись и связанные с ней адреса электронной почты и телефонов
                $emails = DbContactEmail::findAll(['gardener' => $gardener->id]);
                $phones = DbContactPhone::findAll(['gardener' => $gardener->id]);
                if (!empty($emails)) {
                    foreach ($emails as $email) {
                        $email->delete();
                    }
                }
                if (!empty($phones)) {
                    foreach ($phones as $phone) {
                        $phone->delete();
                    }
                }
                $gardener->delete();
                $transaction->commitTransaction();
                return AjaxRequestStatus::successAndReload('Данные о садоводе удалены');
            }
            return AjaxRequestStatus::failed('Не найдены данные по садоводу');
        }
        throw new NotFoundHttpException();
    }
}