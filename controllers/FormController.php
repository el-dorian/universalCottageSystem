<?php

namespace app\controllers;

use app\models\databases\DbContactEmail;
use app\models\databases\DbContactPhone;
use app\models\databases\DbCottage;
use app\models\databases\DbGardener;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\forms\AddCottageForm;
use app\models\electricity\SetElectricityTariffModel;
use app\models\membership\SetMembershipTariffModel;
use app\models\selections\AjaxRequestStatus;
use app\models\target\SetTargetTariffModel;
use app\models\utils\GrammarHandler;
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
                            'add-gardener',
                            'edit-gardener',
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
    public function actionAddCottage(?int $requestedId = null): Response|array
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
                } catch (MyException $e) {
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

    /**
     * @throws NotFoundHttpException
     */
    public function actionAddGardener(?string $cottage = null): Response|array
    {
        $model = new DbGardener();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isGet) {
                $cottageItem = DbCottage::getByAlias($cottage);
                if ($cottageItem !== null) {
                    $model->cottage = $cottageItem->id;
                    $view = $this->renderAjax('add-gardener', ['model' => $model]);
                    return AjaxRequestStatus::view('Добавление контакта', $view);
                }
            } else if (Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                return ActiveForm::validate($model);
            }
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                try {
                    $model->add();
                    Yii::$app->session->addFlash('success', 'Контакт добавлен.');
                } catch (DbSettingsException $e) {
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
    public function actionEditGardener(int $id): Response|array
    {
        $model = DbGardener::findOne($id);
        if($model !== null){
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if (Yii::$app->request->isGet) {
                        $mails = DbContactEmail::findAll(['gardener' => $id]);
                        if(!empty($mails)){
                            $model->emails = [];
                            foreach ($mails as $mail){
                                $model->emails[] = ['email' => $mail->address, 'description' => $mail->description, 'id' => $mail->id];
                            }
                        }
                        $phones = DbContactPhone::findAll(['gardener' => $id]);
                        if(!empty($phones)){
                            $model->phones = [];
                            foreach ($phones as $phone){
                                $model->phones[] = ['phone' => GrammarHandler::inflatePhoneNumber($phone->number), 'description' => $phone->description, 'id' => $phone->id];
                            }
                        }
                        $view = $this->renderAjax('change-gardener', ['model' => $model]);
                        return AjaxRequestStatus::view('Изменение контакта', $view);
                }
                    $model->load(Yii::$app->request->post());
                    return ActiveForm::validate($model);
            }

            if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    try {
                        $model->change();
                        Yii::$app->session->addFlash('success', 'Контакт добавлен.');
                    } catch (DbSettingsException $e) {
                        Yii::$app->session->addFlash('danger', $e->getMessage());
                    }
                } else {
                    Yii::$app->session->addFlash('danger', implode('<br/>', $model->errors));
                }
                return $this->redirect($_SERVER['HTTP_REFERER'], 301);
            }
        }

        throw new NotFoundHttpException();
    }
}