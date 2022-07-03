<?php

use app\assets\CottageShowAsset;
use app\models\databases\DbCottage;
use app\models\databases\DbElectricityMeter;
use app\models\databases\DbGardener;
use app\models\electricity\ElectricityDebtHandler;
use app\models\gardeners\GardenersHandler;
use app\models\utils\CashHandler;
use app\widgets\ElectricityMetersShowWidget;
use app\widgets\GardenersShowWidget;
use nirvana\showloading\ShowLoadingAsset;
use yii\web\View;


/* @var $this View */
/* @var $cottage DbCottage */
/* @var $gardeners DbGardener[] */

ShowLoadingAsset::register($this);
CottageShowAsset::register($this);

$this->title = "Участок $cottage->alias";

if (GardenersHandler::isNoPayers($gardeners)) {
    Yii::$app->session->addFlash('danger', "<div class='text-center'>Не зарегистрированы плательщики. <button class='btn btn-sm btn-info  ajax-form-trigger' data-action='/form/add-gardener?cottage=$cottage->id'>Добавить плательщика</button></div>");
}

?>

<div class="row">
    <div class="col-lg-12">
        <h1>Участок № <?= $cottage->alias ?></h1>
        <table class="table table-hover">
            <tbody>
            <tr><td colspan="2">Сведения об электроэнергии</td></tr>
            <?php
            // электроэнергия
            if ($cottage->is_pay_for_electricity) {
                $meters = DbElectricityMeter::findAll(['cottage' => $cottage->id]);
                $electricityDebt = ElectricityDebtHandler::countDebt($cottage);
                if($electricityDebt > 0){
                    echo "<tr><td>Общая задолженность по электроэнергии</td><td><b class='text-danger'>" . CashHandler::intSumToSmoothFloat($electricityDebt) . "</b></td></tr>";

                }
                else{
                    echo "<tr><td colspan='2' class='text-center'><b class='text-success'>У участка нет долгов по электроэнергии</b></td></tr>";

                }
                echo '<tr><td colspan="2">Зарегистрированные счётчики</td></tr>';
                echo '<tr><td colspan="2">';
                try {
                    echo ElectricityMetersShowWidget::widget(['meters' => $meters]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo $e->getTraceAsString();
                }
                echo '</td></tr>';
                echo '<tr><td colspan="2"><button class="btn btn-info ajax-form-trigger" data-action="/electricity-meter/add?cottage=' . $cottage->id . '">Добавить счётчик</button></td></tr>';
            } else {
                echo '<tr><td colspan="2">Участок не электрифицирован <button class="btn btn-info ajax-form-trigger" data-action="/electricity-meter/add">Добавить счётчик электроэнергии</button></td></tr>';
            }
            ?>
            </tbody>
            <tbody>
            </tbody>
        </table>
        <?php
        try {
            echo GardenersShowWidget::widget(['gardeners' => $gardeners]);
        } catch (Exception $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        }
        echo "<button class='btn btn-sm btn-info ajax-form-trigger' data-action='/form/add-gardener?cottage=$cottage->id'>Добавить плательщика</button>";
        ?>
    </div>
</div>

<footer class="footer mt-auto py-3 text-muted fixed-bottom">
    <div class="container">
        <div class="btn-group"><button class="btn btn-success ajax-form-trigger" data-action="/payment-invoice/create?id=<?=$cottage->id?>">Выписать счёт</button></div>
    </div>
</footer>







