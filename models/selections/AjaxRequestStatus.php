<?php


namespace app\models\selections;


use JetBrains\PhpStorm\ArrayShape;

class AjaxRequestStatus
{
    private  const STATUS_SUCCESS = 1;
    private  const STATUS_FAILED = 0;

     #[ArrayShape(['status' => "int"])] public static function success(): array
    {
        return ['status' => self::STATUS_SUCCESS];
    }

    #[ArrayShape(['status' => "int", 'message' => "string"])] public static function failed(string $message): array
    {
        return ['status' => self::STATUS_FAILED, 'message' => $message];
    }
}