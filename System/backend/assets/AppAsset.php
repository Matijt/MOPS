<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'docs/css/prettify.css',
        'css/bootstrap-multiselect.css',
        'css/modal.css',
    ];
    public $js = [
      'js/modal.js',
        'docs/js/prettify.js',
        'js/modal.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
