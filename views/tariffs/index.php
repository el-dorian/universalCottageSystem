<?php

use app\assets\TariffsAsset;
use app\models\databases\DbTariffMembership;
use app\models\databases\DbTariffTarget;
use app\widgets\TariffDetailsWidget;
use nirvana\showloading\ShowLoadingAsset;
use yii\web\View;


/* @var $this View */
/* @var $targetTariffs DbTariffTarget[] */
/* @var $membershipTariffs DbTariffMembership[] */
/* @var $electricityTariffs \app\models\databases\DbTariffElectricity[] */

TariffsAsset::register($this);
ShowLoadingAsset::register($this);

$this->title = 'Данные по тарифам';
?>

<ul class="nav nav-tabs justify-content-center">
    <li class="nav-item"><a class="nav-link active" href="#electricity_details" data-toggle="tab">Электроэнергия</a></li>
    <li class="nav-item"><a class="nav-link" href="#membership_details" data-toggle="tab">Членские взносы</a></li>
    <li class="nav-item"><a class="nav-link" href="#target_details" data-toggle="tab">Целевые взносы</a></li>
</ul>

<div class="tab-content" id="tabContent">
    <div class="tab-pane fade show active" id="electricity_details" role="tabpanel" aria-labelledby="pills-home-tab">
        <div class="col-sm-12 with-margin text-center">
            <button class="btn btn-success ajax-form-trigger" data-action="/form/set-electricity-tariff">Заполнить тарифы по
                электроэнергии
            </button>
        </div>
        <?php
        try {
            echo TariffDetailsWidget::widget(['items' => $electricityTariffs]);
        } catch (Exception $e) {
        }
        ?>
    </div>
    <div class="tab-pane fade show" id="membership_details" role="tabpanel" aria-labelledby="pills-home-tab">
        <div class="col-sm-12 with-margin text-center">
            <button class="btn btn-success ajax-form-trigger" data-action="/form/set-membership-tariff">Заполнить тарифы по
                членским взносам
            </button>
        </div>
        <?php
        try {
            echo TariffDetailsWidget::widget(['items' => $membershipTariffs]);
        } catch (Exception $e) {
        }
        ?>

    </div>
    <div class="tab-pane fade show" id="target_details" role="tabpanel" aria-labelledby="pills-home-tab">
        <div class="col-sm-12 with-margin text-center">
            <button class="btn btn-success ajax-form-trigger" data-action="/form/set-targets-tariff">Заполнить тарифы по
                целевым платежам
            </button>
        </div>
        <?php
        try {
            echo TariffDetailsWidget::widget(['items' => $targetTariffs]);
        } catch (Exception $e) {
        }
        ?>


    </div>
</div>
