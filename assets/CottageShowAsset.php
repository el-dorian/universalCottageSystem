<?php

namespace app\assets;

use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class CottageShowAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/global_functions.js',
        'js/cottage_show.js',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];
}