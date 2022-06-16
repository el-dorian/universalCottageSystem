<?php

/** @var yii\web\View $this */

/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    if (Yii::$app->user->can('write')) {
        $menuItems = [
            /*'<li><div id="goToCottageContainer"><label class="hidden" for="goToCottageInput"></label><div class="input-group">
                    <span class="input-group-btn">
                        <a class="btn btn-default" href="' . Url::to('cottage/previous') . '">
                            <span class="glyphicon glyphicon-backward"></span>
                        </a>
                    </span>
                     <input
                    type="text"
                    id="goToCottageInput"
                    class="form-control">
                    <span
                    class="input-group-btn"><a class="btn btn-default" href="' . Url::to('cottage/next') . '"><span
                            class="glyphicon glyphicon-forward"></span></a></span>
        </div></div></li>',*/
           // ['label' => 'С', 'url' => ['/count/index'], 'options' => ['class' => 'd-sm-none', 'title' => 'Статистика']],
            ['label' => 'В', 'url' => ['/search/search'], 'options' => ['class' => 'd-sm-none', 'title' => 'Выборки']],
            ['label' => 'З', 'url' => ['/filling'], 'options' => ['class' => 'd-sm-none', 'title' => 'Заполнение']],
            ['label' => 'Т', 'url' => ['/tariffs/index'], 'options' => ['class' => 'd-sm-none', 'title' => 'Тарифы']],
            ['label' => 'У', 'url' => ['/management/index'], 'options' => ['class' => 'd-sm-none', 'title' => 'Управление']],
           // ['label' => 'Статистика', 'url' => ['/count/index'], 'options' => ['class' => 'visible-xs']],
            ['label' => 'Выборки', 'url' => ['/search/search'], 'options' => ['class' => 'visible-xs']],
            ['label' => 'Заполнение', 'url' => ['/filling/power'], 'options' => ['class' => 'visible-xs']],
            ['label' => 'Тарифы', 'url' => ['/tariffs/index'], 'options' => ['class' => 'visible-xs']],
           // ['label' => '<span id="messagesScheduleMenuItem"><span class="glyphicon glyphicon-envelope"></span> ' . "<span id='unsendedMessagesBadge' class='badge'> " . MailingSchedule::countWaiting() . '</span></span>', 'url' => ['/site/mailing-schedule'], 'encode' => false, 'target' => '_blank'],
            ['label' => 'Управление', 'url' => ['/management/index'], 'options' => ['class' => 'visible-xs']],
            /*'<li>'
            . Html::beginForm(['/logout'], 'post', ['class' => 'form-inline'])
            . Html::submitButton(
                'Выход (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>'*/
        ];
    } else {
        $menuItems = [
            '<li>'
            . Html::beginForm(['/logout'], 'post', ['class' => 'form-inline'])
            . Html::submitButton(
                'Выход (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>'
        ];
    }

    try {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => $menuItems,
        ]);
    } catch (Exception $e) {
    }
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?php
        try {
            echo Alert::widget();
        } catch (Exception $e) {
        }
        ?>
        <?= $content ?>
    </div>
    <div id="notifications_div" class="no-print"></div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-left">&copy; <?=Yii::$app->name?> <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
