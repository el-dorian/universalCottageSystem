<?php

use app\models\electricity\ElectricityFillModel;
use dosamigos\datepicker\DatePicker;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model ElectricityFillModel */

echo '<div class="col-sm-12">';

echo "<p>Последние внесённые показания: <b class='text-success'>$model->lastValue кВт</b></p>";

$form = ActiveForm::begin(['id' => 'insertElectricityValuesForm', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => true, 'action' => ['/electricity-meter/insert-values?id=' . $model->meterId]]);

echo $form->field($model, 'meterId', [
    'options' => ['style' => 'display:none;'],
    'template' => '{input}'])
    ->textInput();

try {
    echo $form->field($model, 'entities')->widget(MultipleInput::class, [
        'id' => 'w_periods',
        'min' => count($model->entities),
        'allowEmptyList' => true,
        'cloneButton' => true,
        'addButtonPosition' => MultipleInput::POS_HEADER, // show add button in the header
        'enableError' => true,
        'attributeOptions' => [
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validateOnChange' => true,
            'validateOnSubmit' => true,
            'validateOnBlur' => false,
        ],
        'columns' => [
            [
                'name' => 'period',
                'type' => 'textInput',
                'title' => 'Период потребления'
            ],
            [
                'name' => 'value',
                'type' => 'textInput',
                'options' => ['type' => 'number', 'step' => '1', 'min' => $model->lastValue],
                'title' => 'Показания на конец периода'
            ],
        ]

    ]);
} catch (Exception $e) {
    echo $e->getTraceAsString();
}

echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

ActiveForm::end();

// stupid hack, search for resolve, but i need glyphicons
echo '<div class="hidden">';
echo DatePicker::widget([
    'model' => $model,
    'attribute' => 'test',
    'template' => '{addon}{input}',
    'clientOptions' => [
        'autoclose' => true,
        'format' => 'dd-M-yyyy'
    ]
]);

echo '</div>';
echo '</div>';

