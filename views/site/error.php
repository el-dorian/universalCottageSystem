<?php

/* @var $this yii\web\View */
/* @var $exception Exception */


use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);

$this->title = $exception->getMessage();
?>
<div class="site-error">


    <h1><?= Html::encode("Ошибка : {$exception->getMessage()}") ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($exception->getTraceAsString())) ?>
    </div>

    <p>
        Произошла ошибка во время запроса!
    </p>
</div>
