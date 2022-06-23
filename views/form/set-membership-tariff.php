<?php

use app\models\management\BasePreferences;
use app\models\membership\SetMembershipTariffModel;
use dosamigos\datepicker\DatePicker;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model SetMembershipTariffModel */

echo '<div class="col-sm-12">';

try {
    $form = ActiveForm::begin(['id' => 't_form', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => true, 'action' => ['/form/set-membership-tariff']]);

    if(BasePreferences::getInstance()->membershipPaymentType === BasePreferences::STATE_PAY_QUARTERLY){
        $periodTitle = 'Квартал';
        $options = [];
    }
    else{
        $periodTitle = 'Год';
        $options = ['type' => 'number', 'min' => 0];
    }

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
                'title' => $periodTitle,
                'options' => $options,
                'enableError' => true
            ],
            [
                'name' => 'cottage_price',
                'type' => 'textInput',
                'title' => 'Цена с участка',
                'options' => ['type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => 0]
            ],
            [
                'name' => 'footage_price',
                'type' => 'textInput',
                'title' => 'Цена с сотки',
                'options' => ['type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => 0]
            ],
            [
                'name'  => 'date',
                'type' => DatePicker::class,
                'title' => 'Дата выставления',
                'options' => [
                ]
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