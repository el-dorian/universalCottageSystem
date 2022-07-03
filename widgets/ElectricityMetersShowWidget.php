<?php

namespace app\widgets;

use app\models\databases\DbContactEmail;
use app\models\databases\DbContactPhone;
use app\models\databases\DbElectricityMeter;
use app\models\databases\DbGardener;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use app\models\utils\GrammarHandler;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class ElectricityMetersShowWidget extends Widget
{

    /**
     * @var DbElectricityMeter[]
     */
    public array $meters;
    public string $content = '';

    /**
     * @throws MyException
     */
    public function init():void
    {
        if(!empty($this->meters)){
            $this->content .= '<table class="table"><tr><th>Статус</th><th>Примечание</th><th>Текущие показания</th><th>Месяц</th><th>Действия</th></tr>';
            foreach ($this->meters as $meter) {
                $this->content .= "<tr><td>" . ($meter->is_enabled ? "<b class='text-success'>Активен</b>" : "<b class='text-waining'>Неактивен</b>") . "</td><td>$meter->description</td><td>$meter->indication кВт</td><td>" . TimeHandler::inflateMonth($meter->last_filled_period) . "</td><td><div class='btn-group'><button class='btn btn-info ajax-form-trigger tooltip-enabled' data-toggle='tooltip' data-placement='top' title='Редактировать данные' data-action='/electricity-meter/edit?id=$meter->id'><i class='fa fa-edit'></i></button><button class='btn btn-primary ajax-form-trigger tooltip-enabled' data-toggle='tooltip' data-placement='top' title='Список показаний' data-action='/electricity-meter/history?id=$meter->id'><i class='fa fa-list'></i></button><button class='btn btn-warning ajax-form-trigger tooltip-enabled' data-toggle='tooltip' data-placement='top' title='Внести показания' data-action='/electricity-meter/insert-values?id=$meter->id'><i class='fa fa-plus'></i></button>" . (Yii::$app->user->can('manage') ? "<button class='btn btn-danger tooltip-enabled ajax-promise-trigger' data-promise='Удаляем счётчик со всеми данными?' data-toggle='tooltip' data-placement='top' title='Удалить счётчик совсем' data-action='/electricity-meter/drop-meter?id=$meter->id'><i class='fa fa-trash'></i></button>" : '') . "</div></td></tr>";
            }
            $this->content .= '</table>';
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