<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 24.09.2018
 * Time: 12:13
 */

namespace app\assets;

use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class ManagementAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/global_functions.js',
        'js/management.js',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];
}