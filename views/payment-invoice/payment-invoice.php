<?php

use app\models\payment\PaymentInvoiceBuilder;
use dosamigos\datepicker\DatePicker;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model PaymentInvoiceBuilder */

/*echo '<div class="col-sm-12">';

$form = ActiveForm::begin(['id' => 'paymentInvoiceForm', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => true, 'action' => ["/payment-invoice/create?id=$model->cottage"]]);

try {
    echo $form->field($model, 'electricity')->widget(MultipleInput::class, [
        'id' => 'w_target',
        'allowEmptyList' => false,
        'cloneButton' => false,
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
                'name' => 'payable',
                'type' => 'checkbox',
                'title' => 'Оплачивается',
                'enableError' => true
            ],
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
} catch (Exception $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}

echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

ActiveForm::end();
echo '</div>';*/

echo "<div class='container'>";

$form = ActiveForm::begin(['id' => 'paymentInvoiceForm', 'enableAjaxValidation' => true, 'action' => ["/payment-invoice/create?id=$model->cottage"]]);


echo $form->field($model, 'payer', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->dropDownList($model->payers, ['prompt' => 'Выберите плательщика']);

try {
    echo $form->field($model, 'electricity')->widget(MultipleInput::class, [
        'id' => 'w_target',
        'allowEmptyList' => false,
        'cloneButton' => false,
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
                'name' => 'payable',
                'type' => 'checkbox',
                'title' => 'Оплатить',
                'enableError' => true
            ],
            [
                'name' => 'period',
                'type' => 'textInput',
                'title' => 'Месяц',
                'enableError' => true
            ],
            [
                'name' => 'sum',
                'type' => 'textInput',
                'title' => 'Сумма',
                'options' => ['type' => 'number', 'min' => 0, 'step' => '0.01'],
                'enableError' => true
            ],
        ]

    ]);
} catch (Exception $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}

echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

ActiveForm::end();

echo "</div>";