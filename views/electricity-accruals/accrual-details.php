<?php

use app\models\databases\DbAccrualElectricity;
use app\models\handlers\TimeHandler;
use app\models\utils\CashHandler;
use yii\web\View;

/* @var $this View */
/* @var $accrual DbAccrualElectricity */

switch ($accrual->is_payed){
    case 'yes' : $state = '<b class="text-success">Оплачено</b>';
        break;
    case 'no' : $state = '<b class="text-danger">Не оплачено</b>';
        break;
    default : $state = "<b class=\"text-info\">Оплачено частично: " . CashHandler::intSumToSmoothFloat($accrual->payed_sum) . "</b>";
}

echo "<table class='table table-striped table-hover'>";
echo "<tr><td>Статус</td><td class='text-center'>$state</td></tr>";
echo "<tr><td>Период</td><td class='text-center'>" . TimeHandler::inflateMonth($accrual->period) . "</td></tr>";
echo "<tr><td>Время внесения данных</td><td class='text-center'>" . TimeHandler::timestampToDate($accrual->time_of_entry, true) . "</td></tr>";
echo "<tr><td>Показания на начало периода</td><td class='text-center'>$accrual->previous_meter_values кВт</td></tr>";
echo "<tr><td>Показания на конец периода</td><td class='text-center'>$accrual->current_meter_values кВт</td></tr>";
echo "<tr><td>Разница показаний</td><td class='text-center'>$accrual->values_difference кВт</td></tr>";
if($accrual->values_difference > 0){
    echo "<tr><td>Льготный лимит</td><td class='text-center'>$accrual->preferential_limit кВт</td></tr>";
    echo "<tr><td>Потребление в льготном лимите</td><td class='text-center'>$accrual->preferential_consumption кВт</td></tr>";
    echo "<tr><td>Стоимость кВт, льготно</td><td class='text-center'>" . CashHandler::intSumToSmoothFloat($accrual->preferential_price) . "</td></tr>";
    echo "<tr><td>Итого сумма льготно</td><td class='text-center'>$accrual->preferential_limit кВт * " . CashHandler::intSumToSmoothFloat($accrual->preferential_price) . "/кВт = <b class='text-info'>" . CashHandler::intSumToSmoothFloat($accrual->preferential_amount) . "</b></td></tr>";
    echo "<tr><td>Потребление вне льготного лимита</td><td class='text-center'>$accrual->routine_consumption кВт</td></tr>";
    echo "<tr><td>Стоимость кВт, сверх нормы</td><td class='text-center'>" . CashHandler::intSumToSmoothFloat($accrual->routine_price) . "</td></tr>";
    echo "<tr><td>Итого сумма сверх нормы</td><td class='text-center'>$accrual->routine_consumption кВт * " . CashHandler::intSumToSmoothFloat($accrual->routine_price) . "/кВт = <b class='text-info'>" . CashHandler::intSumToSmoothFloat($accrual->routine_amount) . "</b></td></tr>";
    echo "<tr><td>Общая стоимость</td><td class='text-center'> " . CashHandler::intSumToSmoothFloat($accrual->preferential_amount) . " + " . CashHandler::intSumToSmoothFloat($accrual->routine_amount) . " = <b class='text-primary'>" . CashHandler::intSumToSmoothFloat($accrual->total_amount) . "</b></td></tr>";


}
echo "</table>";
