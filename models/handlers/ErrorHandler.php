<?php


namespace app\models\handlers;


use app\models\utils\Utils;
use Exception;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class ErrorHandler
{

    public static function sendError(Exception $e): void
    {
        $root = Yii::$app->basePath;
        $errorInfo = TimeHandler::timestampToDateTime(time()) . "\r\n";
        $errorInfo .= 'url ' . Url::to() . "\r\n";
        $errorInfo .= 'message ' . $e->getMessage() . "\r\n";
        $errorInfo .= 'code ' . $e->getCode() . "\r\n";
        $errorInfo .= 'in file ' . $e->getFile() . "\r\n";
        $errorInfo .= 'in sting ' . $e->getLine() . "\r\n";
        $errorInfo .= $e->getTraceAsString() . "\r\n";
        if (!empty($_POST)) {
            $errorInfo .= 'post is ';
            $errorInfo .= Utils::arrayToString($_POST);
        }
        if (!empty($_GET)) {
            $errorInfo .= 'get is ';
            $errorInfo .= Utils::arrayToString($_GET);
        }
        // отправлю ошибки асинхронно
        //self::asyncSendErrors();
        if(!($e instanceof NotFoundHttpException)){
            TelegramHandler::sendDebug($errorInfo);
        }
    }
}