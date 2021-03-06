<?php


use app\assets\ManagementAsset;
use app\models\bank\BankPreferencesEditor;
use app\models\db\DbPreferencesEditor;
use app\models\db\DbRestoreModel;
use app\models\email\MailPreferencesEditor;
use app\models\fines\FinesPreferencesEditor;
use app\models\management\BasePreferences;
use app\models\management\BasePreferencesEditor;
use nirvana\showloading\ShowLoadingAsset;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $basePreferencesEditor BasePreferencesEditor */
/* @var $mailPreferencesEditor MailPreferencesEditor */
/* @var $bankPreferencesEditor BankPreferencesEditor */
/* @var $dbPreferencesEditor DbPreferencesEditor */
/* @var $finesPreferencesEditor FinesPreferencesEditor */
/* @var $dbRestoreModel DbRestoreModel */

ManagementAsset::register($this);
ShowLoadingAsset::register($this);

$this->title = 'Настройки приложения';
?>

<ul class="nav nav-tabs justify-content-center">
    <li class="nav-item"><a class="nav-link active" href="#mail_prefs" data-toggle="tab">Почта</a></li>
    <li class="nav-item"><a class="nav-link" href="#db_prefs" data-toggle="tab">База данных</a></li>
    <li class="nav-item"><a class="nav-link" href="#bank_prefs" data-toggle="tab">Банк</a></li>
    <?php
    if(BasePreferences::getInstance()->payFines){
        echo '<li class="nav-item"><a class="nav-link" href="#fines_prefs" data-toggle="tab">Пени</a></li>';
    }
    ?>
    <li class="nav-item"><a href="#base_prefs" data-toggle="tab" class="nav-link">Обшие настройки</a></li>
</ul>

<div class="tab-content" id="tabContent">

    <div class="tab-pane fade show active" id="mail_prefs" role="tabpanel" aria-labelledby="pills-home-tab">

        <?php
        $form = ActiveForm::begin([
            'id' => 'mail-preferences-form',
            'validateOnSubmit' => false,
            'action' => '/edit-settings/mail-settings',
            'options' => ['class' => 'form-horizontal preferences-form'],
        ]);

        echo $form->field($mailPreferencesEditor, 'senderServer', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($mailPreferencesEditor, 'senderEmail', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($mailPreferencesEditor, 'senderLogin', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($mailPreferencesEditor, 'senderPass', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->passwordInput();
        echo $form->field($mailPreferencesEditor, 'senderName', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($mailPreferencesEditor, 'debugSend', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();
        echo $form->field($mailPreferencesEditor, 'testEmailAddress', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($mailPreferencesEditor, 'sendToReserveAddress', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();
        echo $form->field($mailPreferencesEditor, 'reserveEmailAddress', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();

        echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

        ActiveForm::end();
        ?>

    </div>

    <div class="tab-pane fade" id="db_prefs" role="tabpanel" aria-labelledby="pills-profile-tab">

        <div class="text-center">
            <div class="btn-group with-margin" role="group" aria-label="dbOperations">
                <button type="button" id="backup-db" class="btn btn-primary">Резервная копия базы данных</button>
                <button type="button" id="restore-db" class="btn btn-warning">Восстановить базу данных</button>
            </div>
        </div>

        <?php
        $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'id' => 'restore-db-form',
                'action' => '/misc/restore-db'
            ]);
        echo $form->field($dbRestoreModel, 'file', ['template' =>
            '{input}'])
            ->fileInput(['class' => 'd-none', 'id' => 'restore-db-input', 'multiple' => false, 'accept' => '.sql']);
        ActiveForm::end();

        $form = ActiveForm::begin([
        'id' => 'db-preferences-form',
        'validateOnSubmit' => false,
        'action' => '/edit-settings/db-settings',
        'options' => ['class' => 'form-horizontal preferences-form'],
        ]);
        echo $form->field($dbPreferencesEditor, 'dsn', ['template' =>
        '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
        ->textInput();
        echo $form->field($dbPreferencesEditor, 'dbLogin', ['template' =>
        '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
        ->textInput();
        echo $form->field($dbPreferencesEditor, 'dbPass', ['template' =>
        '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
        ->passwordInput();
        echo $form->field($dbPreferencesEditor, 'dbName', ['template' =>
        '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
        ->textInput();
        echo $form->field($dbPreferencesEditor, 'mySqlPath', ['template' =>
        '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
        ->textInput();

        echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

        ActiveForm::end();
        ?>

    </div>

    <div class="tab-pane fade" id="bank_prefs" role="tabpanel" aria-labelledby="pills-profile-tab">
        <?php
        $form = ActiveForm::begin([
            'id' => 'bank-preferences-form',
            'validateOnSubmit' => false,
            'action' => '/edit-settings/bank-settings',
            'options' => ['class' => 'form-horizontal preferences-form'],
        ]);
        echo $form->field($bankPreferencesEditor, 'name', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($bankPreferencesEditor, 'personalAcc', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($bankPreferencesEditor, 'bankName', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($bankPreferencesEditor, 'bik', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($bankPreferencesEditor, 'correspAcc', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($bankPreferencesEditor, 'payerInn', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($bankPreferencesEditor, 'kpp', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();

        echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

        ActiveForm::end();
        ?>
    </div>

    <div class="tab-pane fade" id="fines_prefs" role="tabpanel" aria-labelledby="pills-profile-tab">
        <?php
        $form = ActiveForm::begin([
            'id' => 'fines-preferences-form',
            'validateOnSubmit' => false,
            'action' => '/edit-settings/fines-settings',
            'options' => ['class' => 'form-horizontal preferences-form'],
        ]);
        echo $form->field($finesPreferencesEditor, 'payElectricityFines', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();
        echo $form->field($finesPreferencesEditor, 'payMembershipFines', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();
        echo $form->field($finesPreferencesEditor, 'payTargetFines', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();
        echo $form->field($finesPreferencesEditor, 'electricityFinesRate', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput(['type' => 'number', 'step' => '0.01']);
        echo $form->field($finesPreferencesEditor, 'membershipFinesRate', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput(['type' => 'number', 'step' => '0.01']);
        echo $form->field($finesPreferencesEditor, 'targetFinesRate', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput(['type' => 'number', 'step' => '0.01']);
        echo $form->field($finesPreferencesEditor, 'electricityPeriodForPayment', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput(['type' => 'number', 'step' => '1']);
        echo $form->field($finesPreferencesEditor, 'membershipPeriodForPayment', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput(['type' => 'number', 'step' => '1']);
        echo $form->field($finesPreferencesEditor, 'targetPeriodForPayment', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput(['type' => 'number', 'step' => '1']);

        echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

        ActiveForm::end();
        ?>
    </div>

    <div class="tab-pane fade" id="base_prefs" role="tabpanel" aria-labelledby="pills-contact-tab">
        <?php
        $form = ActiveForm::begin([
            'id' => 'base-preferences-form',
            'validateOnSubmit' => false,
            'action' => '/edit-settings/base-settings',
            'options' => ['class' => 'form-horizontal preferences-form'],
        ]);

        echo '<div class="with-margin"><h5>Общие настройки</h5></div>';

        echo $form->field($basePreferencesEditor, 'cottagesQuantity', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput(['min' => 0, 'step' => 1, 'type' => 'number']);

        echo $form->field($basePreferencesEditor, 'sntName', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();

        echo '<div class="with-margin"><h5>Настройки платежей</h5></div>';

        echo $form->field($basePreferencesEditor, 'payTarget', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();

        echo $form->field($basePreferencesEditor, 'payFines', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();

        echo $form->field($basePreferencesEditor, 'targetPaymentType', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->dropDownList(['0' => 'Раз в квартал', '1' => 'Раз в год']);


        echo $form->field($basePreferencesEditor, 'membershipPaymentType', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->dropDownList(['0' => 'Раз в квартал', '1' => 'Раз в год']);


        echo '<div class="with-margin"><h5>Телеграм-бот</h5></div>';

        echo $form->field($basePreferencesEditor, 'useTelegramBot', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();
        echo $form->field($basePreferencesEditor, 'telegramApiKey', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($basePreferencesEditor, 'telegramSecret', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->textInput();
        echo $form->field($basePreferencesEditor, 'sendDebugToTelegram', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();
        echo $form->field($basePreferencesEditor, 'sendDbBackupToTelegram', ['template' =>
            '<div class="col-sm-4 with-margin">{label}</div><div class="col-sm-8">{input}{error}{hint}</div>'])
            ->checkbox();

        echo Html::submitButton("Сохранить", ['class' => 'btn btn-primary with-margin']);

        ActiveForm::end();
        ?>
    </div>

</div>
