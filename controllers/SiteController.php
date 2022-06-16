<?php

namespace app\controllers;

use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    #[ArrayShape(['access' => "array"])] public function behaviors():array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function(){
                    return $this->redirect('/access-denied', 301);
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'deny',
                        ],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'index'
                        ],
                        'roles' => ['reader'],

                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['error' => "string[]"])] public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionDeny(): Response|string
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect('/login', 301);
        }
        return $this->render('access-error');
    }
}
