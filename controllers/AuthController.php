<?php

namespace app\controllers;

use app\models\auth\AuthForm;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ErrorAction;

class AuthController extends Controller
{

    public $layout = 'auth';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    return $this->redirect('/', 301);
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['signup'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    #[ArrayShape(['error' => "string[]"])] public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionLogin()
    {
        $auth = new AuthForm(['scenario' => AuthForm::SCENARIO_LOGIN]);
        if (Yii::$app->request->isPost && $auth->load(Yii::$app->request->post()) && $auth->validate() && $auth->login()) {
            if (!empty(Yii::$app->request->post()['AuthForm']['name'])) {
                /*Telegram::sendDebug("logged in " . Yii::$app->request->post()['AuthForm']['name']);
                // make backup
                Utils::sendDbBackup();*/
            }
            return $this->goHome();
        }
        return $this->render('login', [
            'auth' => $auth,
        ]);
    }

    public function actionLogout()
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->user->logout();
            return $this->redirect('/login', 301);
        }
        return $this->redirect('/', 301);
    }
}
