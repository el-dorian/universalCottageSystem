<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 18.09.2018
 * Time: 16:35
 */

namespace app\controllers;

use app\models\databases\DbCottage;
use app\models\databases\DbGardener;
use JetBrains\PhpStorm\ArrayShape;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CottageController extends Controller
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
                            'show',
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
    public function actionShow($alias): string
    {
        $cottage = DbCottage::findOne(['alias' => $alias]);
        if($cottage !== null){
            $gardeners = DbGardener::findAll(['cottage' => $cottage->id]);
            return $this->render('show', ['cottage' => $cottage, 'gardeners' => $gardeners]);
        }
        throw new NotFoundHttpException('Данный участок не зарегистрирован');
    }
}