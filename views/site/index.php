<?php


/* @var $this View */

/* @var $cottages DbCottage[] */

use app\assets\MainAsset;
use app\models\databases\DbCottage;
use app\widgets\CottagesShowWidget;
use nirvana\showloading\ShowLoadingAsset;
use yii\web\View;

MainAsset::register($this);
ShowLoadingAsset::register($this)

?>

<div class="col-lg-12">

    <div class="text-center with-margin"><button id="addCottageBtn" class="btn btn-success ajax-form-trigger" data-action="/form/add-cottage">Добавить участок</button></div>
    <?php try {
        echo CottagesShowWidget::widget(['cottages' => $cottages]);
    } catch (Exception $e) {
        echo $e->getTraceAsString();
    } ?>
</div>
