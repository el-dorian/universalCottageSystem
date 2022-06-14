<?php

use app\assets\AuthAsset;
use app\models\auth\AuthForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $auth AuthForm */



$this->title = 'Необходима аутентификация';

AuthAsset::register($this);

?>
<div class="site-login">
    <div class="text-center">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>Доступ ограничен!</p>

        <p>Заполните поля для входа:</p>
    </div>

    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($auth, 'name')->textInput(['autofocus' => true])->hint('Введите логин.')->label('Логин') ?>

            <?= $form->field($auth, 'password')->passwordInput()->hint('Введите пароль.')->label('Пароль') ?>

            <div class="form-group text-center">
                <?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>