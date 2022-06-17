<?php


namespace app\models\utils;


use Exception;

class Utils
{
    /**
     * Массив в строку
     * @param $arr
     * @return string
     */
    public static function arrayToString($arr): string
    {
        $answer = '';
        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                if (is_array($value)) {
                    $val = self::arrayToString($value);
                    $answer .= "\r\n\t $key => $val";
                } else {
                    $answer .= "\r\n\t $key => $value";
                }
            }
        }
        return $answer;
    }


    /**
     * @throws Exception
     */
    public static function generateRandomString($length = 32): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}