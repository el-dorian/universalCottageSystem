<?php

use app\models\databases\DbAccrualElectricity;
use app\models\handlers\TimeHandler;
use app\models\utils\CashHandler;
use yii\web\View;

/* @var $this View */
/* @var $values DbAccrualElectricity[] */

echo "<table class='table table-striped'><thead><tr><th>Период</th><th>Старт</th><th>Финиш</th><th>Расход</th><th>Стоимость</th><th>Статус</th><th>Действия</th></tr></thead>";

foreach ($values as $value){
    switch ($value->is_payed){
        case 'yes' : $state = '<b class="text-success">Оплачено</b>';
        break;
        case 'no' : $state = '<b class="text-danger">Не оплачено</b>';
        break;
        default : $state = "<b class=\"text-info\">Оплачено частично: " . CashHandler::intSumToSmoothFloat($value->payed_sum) . "</b>";
    }
    echo "<tr>
<td>" . TimeHandler::inflateMonth($value->period) . "</td>
<td>$value->previous_meter_values кВт</td>
<td>$value->current_meter_values кВт</td>
<td>$value->values_difference кВт</td>
<td>" . CashHandler::intSumToSmoothFloat($value->total_amount) . "</td>
<td>$state</td>
<td><div class='btn-group'><button class='btn btn-info ajax-form-trigger tooltip-enabled' data-toggle='tooltip' data-placement='top' title='Детали' data-action='/electricity-accruals/details?id=$value->id'><i class='fa fa-info'></i></button><button class='btn btn-warning ajax-form-trigger tooltip-enabled' title='Назначить индивидуально' data-action='/electricity-accruals/set-individual?id=$value->id'><i class='fa fa-edit'></i></button><button class='btn btn-danger tooltip-enabled ajax-promise-trigger' data-toggle='tooltip' data-placement='top' title='Удалить показания' data-action='/electricity-accruals/rollback?id=$value->id' data-promise='Удалить показания за $value->period? Также будут удалены все показания, внесённые после данного периода, если они имеются. Показания удаляются окончательно,отменить это действие нельзя.'><i class='fa fa-trash'></i></button></div></td></tr>";
}

echo "</table>";

?>

<script>handleTooltips(); handlePromiseTriggers();</script>
