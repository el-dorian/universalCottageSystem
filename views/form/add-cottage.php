<?php

use app\models\forms\AddCottageForm;
use app\models\management\BasePreferences;
use app\models\utils\CashHandler;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model AddCottageForm */

$form = ActiveForm::begin(['id' => 'addCottageForm', 'options' => ['class' => 'form-horizontal bg-default'], 'enableAjaxValidation' => true, 'action' => ['/form/add-cottage']]);

echo $form->field($model, 'cottageAlias', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textInput();

echo $form->field($model, 'registrationData', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textarea();

echo $form->field($model, 'cottageComment', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textarea();

echo $form->field($model, 'cottageSquare', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6"><div class="input-group">{input}<span class="input-group-text">м<sup>2</sup></span></div>{error}{hint}</div></div>'])
    ->textInput(['type' => 'number', 'min' => '0', 'step' => '1']);

echo $form->field($model, 'isPayElectricity', ['template' =>
    '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->checkbox();

echo $form->field($model, 'currentElectricityMeterValue', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6"><div class="input-group">{input}<span class="input-group-text">кВт</span></div>{error}{hint}</div></div>'])
    ->textInput(['type' => 'number', 'min' => '0', 'step' => '1'])->hint('кВт');

echo $form->field($model, 'electricityPayedFor', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textInput()->hint('Месяц, в формате 2020-02');

echo $form->field($model, 'isPayMembership', ['template' =>
    '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->checkbox();

$membershipPayedForHint = BasePreferences::getInstance()->membershipPaymentType === BasePreferences::STATE_PAY_YEARLY ? 'Год ' : 'Квартал в формате 2020-1';

echo $form->field($model, 'membershipPayedFor', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textInput()->hint($membershipPayedForHint);

echo $form->field($model, 'isPayTarget', ['template' =>
    '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->checkbox();

$targetPayedForHint = BasePreferences::getInstance()->targetPaymentType === BasePreferences::STATE_PAY_YEARLY ? 'Год ' : 'Квартал в формате 2020-1';

echo $form->field($model, 'targetPayedFor', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textInput()->hint($targetPayedForHint);


echo $form->field($model, 'initialDeposit', ['template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6"><div class="input-group">{input}<span class="input-group-text">' . CashHandler::RUBLE_SIGN . '</span></div>{error}{hint}</div></div>'])
    ->textInput(['type' => 'number', 'min' => '0', 'step' => '0.01']);

echo $form->field($model, 'isSlave', [
    'template' =>
    '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
    ->checkbox();

echo $form->field($model, 'masterCottageName', [
    'options' => ['style' => 'display:none;'],
    'template' =>
    '<div class="row with-margin"><div class="col-sm-6 text-center">{label}</div><div class="col-sm-6">{input}{error}{hint}</div></div>'])
    ->textInput();

echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

ActiveForm::end();
