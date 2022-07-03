<?php

use app\models\databases\DbGardener;
use dosamigos\datepicker\DatePicker;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model DbGardener */

echo '<div class="col-sm-12">';

$form = ActiveForm::begin(['id' => 'addGardenerForm', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => true, 'action' => ['/form/edit-gardener?id=' . $model->id]]);

echo $form->field($model, 'id', [
    'options' => ['style' => 'display:none;'],
    'template' => '{input}'])
    ->textInput();
echo $form->field($model, 'cottage', [
    'options' => ['style' => 'display:none;'],
    'template' => '{input}'])
    ->textInput();

echo $form->field($model, 'personals', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->textInput();

echo $form->field($model, 'address', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->textarea();

echo $form->field($model, 'passport_data', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->textarea();

echo $form->field($model, 'description', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->textarea();

echo $form->field($model, 'is_payer', ['template' =>
    '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->checkbox();

echo $form->field($model, 'ownership_share', [
    'options' => ['style' => ($model->is_payer ? '' : 'display:none;')],
    'template' =>
        '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textInput(['type' => 'number', 'min' => '0', 'step' => '1']);

try {
    echo $form->field($model, 'emails')->widget(MultipleInput::class, [
        'id' => 'w_emails',
        'min' => 0,
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
                'name' => 'email',
                'type' => 'textInput',
                'title' => 'Адрес электронной почты',
                'options' => ['type' => 'email']
            ],
            [
                'name' => 'description',
                'type' => 'textInput',
                'title' => 'Комментарий'
            ],
        ]

    ]);
} catch (Exception $e) {
    echo $e->getTraceAsString();
}

try {
    echo $form->field($model, 'phones')->widget(MultipleInput::class, [
        'id' => 'w_phones',
        'allowEmptyList' => true,
        'cloneButton' => true,
        'min' => 0,
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
                'name' => 'phone',
                'type' => 'textInput',
                'title' => 'Номер телефона',
                'options' => ['type' => 'phone']
            ],
            [
                'name' => 'description',
                'type' => 'textInput',
                'title' => 'Комментарий'
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
try {
    echo DatePicker::widget([
        'model' => $model,
        'attribute' => 'test',
        'template' => '{addon}{input}',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'dd-M-yyyy'
        ]
    ]);
} catch (Exception $e) {
}

echo '</div>';
echo '</div>';