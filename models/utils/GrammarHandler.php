<?php

namespace app\models\utils;

class GrammarHandler
{

    public static function isValidPhone(string $dirtyNumber): bool
    {
        // отсеку все не-цифры и проверю, что остаётся минимум 11 знаков, максимум 16
        $clearNumber = preg_replace("/\D/", "", $dirtyNumber);
        $len = strlen($clearNumber);
        return $len > 10 && $len < 17;
    }

    public static function clearPhoneNumber(string $dirtyNumber): int
    {
        if (str_starts_with($dirtyNumber, '8')) {
            $dirtyNumber = '+7' . substr($dirtyNumber, 1);
        }
        return (int)preg_replace("/\D/", "", $dirtyNumber);
    }

    public static function inflatePhoneNumber(int $number): string
    {
        $result = '';
        $stringified = (string)$number;
        $len = strlen($stringified);
        $lastGroup = substr($stringified, $len - 2);
        $secondGroup = substr($stringified, $len - 4, 2);
        $thirdGroup = substr($stringified, $len - 7, 3);
        $fourthGroup = substr($stringified, $len - 10, 3);
        $countryCode = substr($stringified, 0, $len - 10);
        $result .= "+$countryCode ($fourthGroup) $thirdGroup $secondGroup-$lastGroup";
        return $result;
    }

    public static function multiArrayToString(array $array): string
    {
        if( is_array( $array ) ){

            foreach( $array as $key => &$value ){

                if( @is_array( $value ) ){
                    $array[ $key ] = self::multiArrayToString( $value);
                }
            }

            return implode( "<br/>", $array );
        }

        // Not array
        return $array;
    }
}