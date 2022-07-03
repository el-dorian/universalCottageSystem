<?php

namespace app\controllers;

use app\models\databases\DbAccrualElectricity;
use app\models\electricity\ElectricityAccrualsHandler;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use app\models\selections\AjaxRequestStatus;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ElectricityAccrualsController extends Controller
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
                            'details',
                            'rollback',
                        ],
                        'roles' => ['writer'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDetails($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $accrual = DbAccrualElectricity::findOne($id);
            if ($accrual !== null) {
                $view = $this->renderAjax('accrual-details', ['accrual' => $accrual]);
                return AjaxRequestStatus::view('Детальные показания за ' . $accrual->period, $view);
            }
            return AjaxRequestStatus::failed("Данные по периоду не найдены");
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param string $id
     * @return array
     * @throws DbSettingsException
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionRollback(string $id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $accrual = DbAccrualElectricity::findOne($id);
            if ($accrual !== null) {
                try{
                    ElectricityAccrualsHandler::rollback($accrual);
                    return AjaxRequestStatus::successAndReload("Показания за " . TimeHandler::inflateMonth($accrual->period) . " удалены");
                }
                catch (MyException $e){
                    return AjaxRequestStatus::failed($e->getMessage());
                }
            }
            return AjaxRequestStatus::failed("Данные по периоду не найдены");
        }
        throw new NotFoundHttpException();
    }
}