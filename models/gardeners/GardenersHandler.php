<?php

namespace app\models\gardeners;

use app\models\databases\DbGardener;

class GardenersHandler
{

    public static function isNoPayers(array $gardeners): bool
    {
        /** @var DbGardener $gardener */
        foreach ($gardeners as $gardener) {
            if ($gardener->is_payer) {
                return false;
            }
        }
        return true;
    }
}