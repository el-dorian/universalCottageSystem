<?php

use app\models\payment\PaymentInvoiceBuilder;
use dosamigos\datepicker\DatePicker;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model PaymentInvoiceBuilder */

echo "<div class='container'>";

$form = ActiveForm::begin(['id' => 'paymentInvoiceForm', 'enableAjaxValidation' => true, 'action' => ["/payment-invoice/create?id=$model->cottage"]]);


echo $form->field($model, 'payer', ['template' =>
    '<div class="row with-margin"><div class="col-sm-2 text-center">{label}</div><div class="col-sm-10">{input}{error}{hint}</div></div>'])
    ->dropDownList($model->payers, ['prompt' => 'Выберите плательщика']);

try {
    echo $form->field($model, 'electricity')->widget(MultipleInput::class, [
        'id' => 'w_electricity',
        'min' => 0,
        'allowEmptyList' => true,
        'cloneButton' => false,
        'addButtonPosition' => MultipleInput::POS_HEADER, // show add button in the header
        'enableError' => true,
        'removeButtonOptions' => ['class' => 'd-none'],
        'attributeOptions' => [
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validateOnChange' => true,
            'validateOnSubmit' => true,
            'validateOnBlur' => false,
        ],
        'addButtonOptions' => [
            'class' => 'd-none'
        ],
        'columns' => [
            [
                'name' => 'selected_for_pay',
                'type' => 'checkbox',
                'title' => 'Оплата',
            ],
            [
                'name' => 'period',
                'type' => 'textInput',
                'options' => ['readonly' => true],
                'title' => 'Период оплаты',
            ],
            [
                'name' => 'sum',
                'type' => 'textInput',
                'title' => 'Сумма',
                'options' => ['type' => 'number', 'min' => 0, 'step' => '0.01']
            ],
            [
                'name' => 'meterDescription',
                'type' => 'textInput',
                'title' => 'Счётчик',
                'options' => ['readonly' => true],
            ],
            [
                'name' => 'meter',
                'type' => 'textInput',
                'options' => ['readonly' => true, 'class' => 'd-none'],
                'headerOptions' => ['class' => 'd-none'],
                'title' => '',
            ],
        ]

    ]);
} catch (Exception $e) {
    echo $e->getTraceAsString();
}

echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

ActiveForm::end();

echo "</div>";
?>

<script>
    $('#paymentInvoiceForm').on('ajaxComplete', function (e) {
        // отправлю запрос на пересчёт общей стоимости счёта
        sendAjax(
            'post',
            '/payment-invoice/count-total',
            function (answer) {
                console.log(answer)
            },
            $(this),
            true
        )
    });
</script>
