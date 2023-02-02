<?php

namespace backend\assets;

use yii\web\{AssetBundle, YiiAsset};

/**
 * Class AppAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/main.css',//кастомные стили для админки.
    ];

    public $js = [
    ];

    public $depends = [
        YiiAsset::class,//ресурсы для Yii2
        JQueryCookie::class,//JQuery Cookie
        DashboardAsset::class,//ресурсы SB-ADMIN.
//        BootstrapAsset::class,
        FontAwesomeAsset::class,
    ];
}