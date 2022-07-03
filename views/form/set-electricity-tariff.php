<?php

use app\models\electricity\SetElectricityTariffModel;
use dosamigos\datepicker\DatePicker;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model SetElectricityTariffModel */

echo '<div class="col-sm-12">';

try {
    $form = ActiveForm::begin(['id' => 't_form', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => true, 'action' => ['/form/set-electricity-tariff']]);

    echo $form->field($model, 'entities')->widget(MultipleInput::class, [
        'id' => 'w_target',
        'allowEmptyList' => false,
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
                'title' => 'Месяц',
                'enableError' => true
            ],
            [
                'name' => 'preferential_limit',
                'type' => 'textInput',
                'title' => 'Лимит льготного потребления, кВт',
                'options' => ['type' => 'number', 'min' => 0, 'step' => '1', 'value' => 0],
                'enableError' => true
            ],
            [
                'name' => 'preferential_price',
                'type' => 'textInput',
                'title' => 'Льготная стоимость киловатта, руб.',
                'options' => ['type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => 0],
                'enableError' => true
            ],
            [
                'name' => 'routine_price',
                'type' => 'textInput',
                'title' => 'Стоимость киловатта сверх льготного лимита, руб.',
                'options' => ['type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => 0],
                'enableError' => true
            ],
            [
                'name' => 'date',
                'type' => DatePicker::class,
                'title' => 'Дата выставления',
                'attributeOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy'
                ],
                'enableError' => true
            ],
        ]

    ]);

    echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

    ActiveForm::end();

    echo '</div>';
} catch (Exception $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}