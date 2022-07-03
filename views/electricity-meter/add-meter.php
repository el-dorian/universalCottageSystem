<?php

use app\models\databases\DbElectricityMeter;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model DbElectricityMeter */

echo '<div class="col-sm-12">';

$form = ActiveForm::begin(['id' => 'addMeterForm', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => true, 'action' => ['/electricity-meter/add?cottage=' . $model->cottage]]);

echo $form->field($model, 'cottage', [
    'options' => ['style' => 'display:none;'],
    'template' => '{input}'])
    ->textInput();

echo $form->field($model, 'indication', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6"><div class="input-group">{input}<span class="input-group-text">кВт</span></div>{error}{hint}</div></div>'])
    ->textInput(['type' => 'number', 'min' => '0', 'step' => '1'])->hint('кВт');

echo $form->field($model, 'last_filled_period', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textInput()->hint('Месяц, в формате 2020-02');

echo $form->field($model, 'description', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textarea();

echo $form->field($model, 'is_enabled', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->checkbox();


echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

ActiveForm::end();

echo '</div>';
