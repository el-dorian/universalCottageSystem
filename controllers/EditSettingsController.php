<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 18.09.2018
 * Time: 16:35
 */

namespace app\controllers;

use app\models\bank\BankPreferencesEditor;
use app\models\db\DbPreferencesEditor;
use app\models\email\MailPreferencesEditor;
use app\models\management\BasePreferencesEditor;
use app\models\selections\AjaxRequestStatus;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class EditSettingsController extends Controller
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
                            'mail-settings',
                            'db-settings',
                            'bank-settings',
                            'base-settings',
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
    public function actionMailSettings(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new MailPreferencesEditor(['scenario' => MailPreferencesEditor::SCENARIO_CHANGE]);
            $model->load(Yii::$app->request->post());
            if ($model->validate() && $model->saveSettings()) {
                return AjaxRequestStatus::success();
            }
            return AjaxRequestStatus::failed('Проверьте введённые данные');
        }
        throw new NotFoundHttpException('Страница не найдена');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDbSettings(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new DbPreferencesEditor(['scenario' => DbPreferencesEditor::SCENARIO_CHANGE]);
            $model->load(Yii::$app->request->post());
            if ($model->validate() && $model->saveSettings()) {
                return AjaxRequestStatus::success();
            }
            return AjaxRequestStatus::failed('Проверьте введённые данные');
        }
        throw new NotFoundHttpException('Страница не найдена');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionBankSettings(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new BankPreferencesEditor(['scenario' => BankPreferencesEditor::SCENARIO_CHANGE]);
            $model->load(Yii::$app->request->post());
            if ($model->validate() && $model->saveSettings()) {
                return AjaxRequestStatus::success();
            }
            return AjaxRequestStatus::failed('Проверьте введённые данные');
        }
        throw new NotFoundHttpException('Страница не найдена');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionBaseSettings(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new BasePreferencesEditor(['scenario' => BasePreferencesEditor::SCENARIO_CHANGE]);
            $model->load(Yii::$app->request->post());
            if ($model->validate() && $model->saveSettings()) {
                return AjaxRequestStatus::success();
            }
            return AjaxRequestStatus::failed('Проверьте введённые данные');
        }
        throw new NotFoundHttpException('Страница не найдена');
    }
}