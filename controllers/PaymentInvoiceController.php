<?php

namespace app\controllers;

use app\models\databases\DbCottage;
use app\models\exceptions\MyException;
use app\models\payment\PaymentInvoiceBuilder;
use app\models\selections\AjaxRequestStatus;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class PaymentInvoiceController extends Controller
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
                            'create',
                            'count-total',
                        ],
                        'roles' => ['writer'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     * @throws MyException
     */
    public function actionCreate($id): array
    {
        $cottage = DbCottage::findOne($id);
        if ($cottage !== null) {
            $model = new PaymentInvoiceBuilder();
            $model->configure($cottage);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if (Yii::$app->request->isGet) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $view = $this->renderAjax('payment-invoice', ['model' => $model]);
                    return AjaxRequestStatus::view('Создание счёта на оплату', $view);
                }
                $model->load(Yii::$app->request->post());
                return ActiveForm::validate($model);
            }
        }
        throw new NotFoundHttpException();
    }

    /**
     * @throws NotFoundHttpException
     * @throws MyException
     */
    public function actionCountTotal(): array
    {
        $model = new PaymentInvoiceBuilder();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isPost) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->load(Yii::$app->request->post());
                if ($model->validate()) {
                    return AjaxRequestStatus::successWithMessage($model->countTotal());
                }
                return AjaxRequestStatus::failed('not valid form');
            }
        }
        throw new NotFoundHttpException();
    }
}