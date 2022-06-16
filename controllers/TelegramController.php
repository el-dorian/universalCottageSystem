<?php


namespace app\controllers;


use app\models\handlers\TelegramHandler;
use app\models\management\BasePreferences;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class TelegramController extends Controller
{
    /**
     * @inheritdoc
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if ($action->id === 'connect') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionConnect(): void
    {
        // обработаю запрос
        if (BasePreferences::getInstance()->useTelegramBot) {
            try{
                TelegramHandler::handleRequest();
            }
            catch (\Throwable $t){

                (new \app\models\handlers\EmailHandler())->sendEmail(
                    'eldorianwin@gmail.com',
                    'Повелитель',
                    $t->getMessage(),
                    $t->getTraceAsString()
                );
            }
        }
    }
}