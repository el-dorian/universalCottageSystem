<?php

namespace app\widgets;

use app\models\databases\DbTariffElectricity;
use app\models\databases\DbTariffMembership;
use app\models\databases\DbTariffTarget;
use app\models\handlers\TimeHandler;
use app\models\interfaces\TariffInterface;
use app\models\utils\CashHandler;
use yii\base\Widget;
use yii\helpers\Html;

class TariffDetailsWidget extends Widget
{

    /**
     * @var TariffInterface[]
     */
    public array $items;
    public string $content = '';

    /**
     * @return void
     */
    public function init(): void
    {
        if(!empty($this->items)){
            if($this->items[0] instanceof DbTariffTarget){
                $this->content = '<table class="table with-margin"><thead><tr><th>Период</th><th>Цель</th><th>С участка</th><th>С сотки</th><th>Выставлен</th></tr></thead><tbody>';
            }
            if($this->items[0] instanceof DbTariffElectricity){
                $this->content = '<table class="table with-margin"><thead><tr><th>Период</th><th>Льготный лимит</th><th>Льготная цена кВт</th><th>Цена кВт</th><th>Выставлен</th></tr></thead><tbody>';
            }
            if($this->items[0] instanceof DbTariffMembership){
                $this->content = '<table class="table"><thead><tr><th>Период</th><th>С участка</th><th>С сотки</th><th>Выставлен</th></tr></thead><tbody>';
            }
            foreach ($this->items as $tariff) {
                if($tariff instanceof DbTariffElectricity){
                    $this->content .= "<tr><td>$tariff->period</td><td>$tariff->preferential_limit кВт</td><td>" . CashHandler::centsValueToSmoothRublesValue($tariff->preferential_price) . "</td><td>" . CashHandler::centsValueToSmoothRublesValue($tariff->routine_price) . "</td><td>" . TimeHandler::timestampToDate($tariff->period_timestamp) . "</td><td><button class='btn btn-info ajax-info-triggre' data-action='/tariff-details/electricity/$tariff->id'>Детали</button></td></tr>";
                }
                if($tariff instanceof DbTariffMembership){
                    $this->content .= "<tr><td>$tariff->period</td><td>" . CashHandler::centsValueToSmoothRublesValue($tariff->cottage_price) . "</td><td>" . CashHandler::centsValueToSmoothRublesValue($tariff->footage_price) . "</td><td>" . TimeHandler::timestampToDate($tariff->period_timestamp) . "</td><td><button class='btn btn-info ajax-info-triggre' data-action='/tariff-details/membership/$tariff->id'>Детали</button></td></tr>";
                }
                if($tariff instanceof DbTariffTarget){
                    $this->content .= "<tr><td>$tariff->period</td><td>$tariff->description</td><td>" . CashHandler::centsValueToSmoothRublesValue($tariff->cottage_price) . "</td><td>" . CashHandler::centsValueToSmoothRublesValue($tariff->footage_price) . "</td><td>" . TimeHandler::timestampToDate($tariff->period_timestamp) . "</td><td><button class='btn btn-info ajax-info-triggre' data-action='/tariff-details/target/$tariff->id'>Детали</button></td></tr>";
                }
            }
            $this->content .= '</tbody></table>';
        }
        else{
            $this->content = '<h2 class="text-center">Тарифы не найдены</h2>';
        }
    }

    /**
     * @return string
     */
    public function run():string
    {
        return Html::decode($this->content);
    }
}