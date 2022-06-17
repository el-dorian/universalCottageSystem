<?php

namespace app\controllers;

use app\models\handlers\TelegramHandler;
use app\models\handlers\TimeHandler;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
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
                            'error'
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

    #[ArrayShape(['error' => "string[]"])] public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

/*public function actionError(): Response|string
{
    $e = Yii::$app->errorHandler->exception;
    if ($e !== null) {
        $errorInfo = '';
        $errorInfo .= TimeHandler::timestampToDateTime(time()) . "\r\n";
        $errorInfo .= 'url ' . Url::to() . "\r\n";
        $errorInfo .= 'message ' . $e->getMessage() . "\n";
        $errorInfo .= 'code ' . $e->getCode() . "\n";
        $errorInfo .= 'in file ' . $e->getFile() . "\n";
        $errorInfo .= 'in sting ' . $e->getLine() . "\n";
        $errorInfo .= $e->getTraceAsString() . "\n";
        TelegramHandler::sendDebug("Ошибка: $errorInfo");
        return $this->render('error', ['exception' => $e]);
    }
    return $this->redirect('/', 301);
}*/

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
