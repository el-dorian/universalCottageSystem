<?php

namespace app\controllers;

use app\models\databases\DbCottage;
use app\models\payment\PaymentInvoiceBuilder;
use app\models\selections\AjaxRequestStatus;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
    public function actionCreate($id): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            // удаляю данные по садоводу, если они есть
            $cottage = DbCottage::findOne($id);
            if ($cottage !== null) {
                $model = new PaymentInvoiceBuilder();
                $model->configure($cottage);
                $view = $this->renderAjax('payment-invoice', ['model' => $model]);
                return AjaxRequestStatus::view('Создание счёта на оплату', $view);
            }
            return AjaxRequestStatus::failed('Не найдены данные по участку $id');
        }
        throw new NotFoundHttpException();
    }
}