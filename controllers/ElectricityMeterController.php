<?php

namespace app\controllers;

use app\models\databases\DbAccrualElectricity;
use app\models\databases\DbElectricityMeter;
use app\models\electricity\ElectricityFillModel;
use app\models\electricity\ElectricityMeterHandler;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\selections\AjaxRequestStatus;
use app\models\utils\GrammarHandler;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ElectricityMeterController extends Controller
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
                            'edit',
                            'add',
                            'history',
                            'insert-values'
                        ],
                        'roles' => ['writer'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'drop-meter',
                        ],
                        'roles' => ['manager'],
                    ],
                ],
            ],
        ];
    }


    /**
     * @throws NotFoundHttpException
     */
    public function actionEdit($id): Response|array
    {
        $model = DbElectricityMeter::findOne($id);
        if ($model !== null) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if (Yii::$app->request->isGet) {
                    $view = $this->renderAjax('edit-meter', ['model' => $model]);
                    return AjaxRequestStatus::view('Изменение данных по счётчику', $view);
                }
                $model->load(Yii::$app->request->post());
                return ActiveForm::validate($model);
            }

            if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    $model->save();
                    Yii::$app->session->addFlash('success', 'Данные счётчика изменены.');
                } else {
                    Yii::$app->session->addFlash('danger', implode('<br/>', $model->errors));
                }
                return $this->redirect($_SERVER['HTTP_REFERER'], 301);
            }
        }
        throw new NotFoundHttpException();
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionAdd(int $cottage): Response|array
    {
        $model = new DbElectricityMeter(['scenario' => DbElectricityMeter::SCENARIO_CREATE]);
        $model->cottage = $cottage;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isGet) {
                $view = $this->renderAjax('add-meter', ['model' => $model]);
                return AjaxRequestStatus::view('Добавление счётчика электроэнергии', $view);
            }
            $model->load(Yii::$app->request->post());
            return ActiveForm::validate($model);
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->add();
                Yii::$app->session->addFlash('success', 'Счётчик добавлен.');
            } else {
                Yii::$app->session->addFlash('danger', implode('<br/>', $model->errors));
            }
            return $this->redirect($_SERVER['HTTP_REFERER'], 301);
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionHistory($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $meter = DbElectricityMeter::findOne($id);
            if ($meter !== null) {
                $values = DbAccrualElectricity::find()->where(['meter' => $meter->id])->orderBy('period DESC')->all();
                $view = $this->renderAjax('meter-history', ['values' => $values]);
                return AjaxRequestStatus::view('История показаний по счётчику ' . $meter->description, $view);
            }
            return AjaxRequestStatus::failed("Данные по счётчику не найдены");
        }
        throw new NotFoundHttpException();
    }

    /**
     * @throws NotFoundHttpException
     * @throws MyException
     * @throws DbSettingsException
     */
    public function actionInsertValues($id): Response|array
    {
        $meter = DbElectricityMeter::findOne($id);
        $model = new ElectricityFillModel();
        if (Yii::$app->request->isAjax) {
            if ($meter !== null) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $lastValue = DbAccrualElectricity::getLastAccrual($meter);
                if ($lastValue !== null) {
                    if (Yii::$app->request->isGet) {
                        $model->setup($lastValue);
                        $view = $this->renderAjax('insert-values', ['model' => $model]);
                        return AjaxRequestStatus::view('Внесение показаний по электроэнергии ' . $meter->description, $view);
                    }
                    $model->meterId = $lastValue->meter;
                    $model->load(Yii::$app->request->post());
                    return ActiveForm::validate($model);
                }
            }
            return AjaxRequestStatus::failed("Данные по счётчику не найдены");
        }
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->insertValues();
                Yii::$app->session->addFlash('success', 'Данные об электроэнергии внесены.');
            } else {
                Yii::$app->session->addFlash('danger', GrammarHandler::multiArrayToString($model->errors));
            }
            return $this->redirect($_SERVER['HTTP_REFERER'], 301);
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $id
     * @return array
     * @throws DbSettingsException
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */

    #[ArrayShape(['status' => "int", 'message' => "string", 'reload' => "bool"])] public function actionDropMeter($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            ElectricityMeterHandler::dropMeter($id);
            return AjaxRequestStatus::successAndReload("Данные полностью удалены!");
        }
        throw new NotFoundHttpException();
    }
}