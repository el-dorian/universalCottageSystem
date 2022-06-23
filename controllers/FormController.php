<?php

namespace app\controllers;

use app\models\databases\DbCottage;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\forms\AddCottageForm;
use app\models\electricity\SetElectricityTariffModel;
use app\models\membership\SetMembershipTariffModel;
use app\models\selections\AjaxRequestStatus;
use app\models\target\SetTargetTariffModel;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class FormController extends Controller
{

    public $layout = 'auth';

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
                            'add-cottage',
                            'set-targets-tariff',
                            'set-membership-tariff',
                            'set-electricity-tariff',
                        ],
                        'roles' => ['writer'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionAddCottage(?int $requestedId = null)
    {
        $model = new AddCottageForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isGet) {
                if ($requestedId !== null) {
                    // check cottage with this number not registered
                    if (DbCottage::find()->where(['alias' => $requestedId])->count() > 0) {
                        return AjaxRequestStatus::failed('Участок с данным идентификатором уже зарегистрирован');
                    }
                    $model->cottageAlias = $requestedId;
                }
                $view = $this->renderAjax('add-cottage', ['model' => $model]);
                return AjaxRequestStatus::view('Регистрация участка', $view);
            }
            $model->load(Yii::$app->request->post());
            return ActiveForm::validate($model);
        }
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                try {
                    $model->save();
                    Yii::$app->session->addFlash('success', "Участок <a target='_blank' href='/cottage/show/$model->cottageAlias'>$model->cottageAlias</a> добавлен!.");
                } catch (MyException|DbSettingsException $e) {
                    Yii::$app->session->addFlash('danger', $e->getMessage());
                }

            } else {
                Yii::$app->session->addFlash('danger', implode('<br/>', $model->errors));
            }
            return $this->redirect($_SERVER['HTTP_REFERER'], 301);
        }
        throw new NotFoundHttpException();
    }

    /**
     * @throws NotFoundHttpException
     */
    #[ArrayShape(['status' => "int", 'title' => "string", 'view' => "string", 'delay' => "false"])] public function actionSetTargetsTariff(): Response|array
    {
        $model = new SetTargetTariffModel();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isGet) {
                $view = $this->renderAjax('set-targets-tariff', ['model' => $model]);
                return AjaxRequestStatus::view('Назначение целевых платежей', $view);
            }
// validate
            $model->load(Yii::$app->request->post());
            return ActiveForm::validate($model);
        }
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                try {
                    $model->save();
                    Yii::$app->session->addFlash('success', 'Целевые взносы сохранены.');
                } catch (MyException|DbSettingsException $e) {
                    Yii::$app->session->addFlash('danger', $e->getMessage());
                }

            } else {
                Yii::$app->session->addFlash('danger', implode('<br/>', $model->errors));
            }
            return $this->redirect($_SERVER['HTTP_REFERER'], 301);
        }
        throw new NotFoundHttpException();
    }

    /**
     * @throws NotFoundHttpException
     */
    #[ArrayShape(['status' => "int", 'title' => "string", 'view' => "string", 'delay' => "false"])] public function actionSetMembershipTariff(): Response|array
    {
        $model = new SetMembershipTariffModel();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isGet) {
                $view = $this->renderAjax('set-membership-tariff', ['model' => $model]);
                return AjaxRequestStatus::view('Назначение членских взносов', $view);
            }
// validate
            $model->load(Yii::$app->request->post());
            return ActiveForm::validate($model);
        }
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                try {
                    $model->save();
                    Yii::$app->session->addFlash('success', 'Членские взносы сохранены.');
                } catch (MyException|DbSettingsException $e) {
                    Yii::$app->session->addFlash('danger', $e->getMessage());
                }

            } else {
                Yii::$app->session->addFlash('danger', implode('<br/>', $model->errors));
            }
            return $this->redirect($_SERVER['HTTP_REFERER'], 301);
        }
        throw new NotFoundHttpException();
    }

    /**
     * @throws NotFoundHttpException
     */
    #[ArrayShape(['status' => "int", 'title' => "string", 'view' => "string", 'delay' => "false"])] public function actionSetElectricityTariff(): Response|array
    {
        $model = new SetElectricityTariffModel();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isGet) {
                $view = $this->renderAjax('set-electricity-tariff', ['model' => $model]);
                return AjaxRequestStatus::view('Назначение тарифов электроэнергии', $view);
            }
// validate
            $model->load(Yii::$app->request->post());
            return ActiveForm::validate($model);
        }
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                try {
                    $model->save();
                    Yii::$app->session->addFlash('success', 'Тарифы электроэнергии сохранены.');
                } catch (MyException|DbSettingsException $e) {
                    Yii::$app->session->addFlash('danger', $e->getMessage());
                }
            } else {
                Yii::$app->session->addFlash('danger', implode('<br/>', $model->errors));
            }
            return $this->redirect($_SERVER['HTTP_REFERER'], 301);
        }
        throw new NotFoundHttpException();
    }

}