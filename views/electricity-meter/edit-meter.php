<?php

use app\models\databases\DbElectricityMeter;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model DbElectricityMeter */

echo '<div class="col-sm-12">';

$form = ActiveForm::begin(['id' => 'editMeterForm', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => true, 'action' => ['/electricity-meter/edit?id=' . $model->id]]);

echo $form->field($model, 'description', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->textarea();

echo $form->field($model, 'is_enabled', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->checkbox();


echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

ActiveForm::end();

echo '</div>';