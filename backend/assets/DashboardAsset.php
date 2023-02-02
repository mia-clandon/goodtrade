<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class DashboardAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class DashboardAsset extends AssetBundle {

    public $sourcePath = '@npm/sb-admin-2';

    public $css = [
        'css/sb-admin-2.min.css',
    ];

    public $js = [
        'js/sb-admin-2.min.js',
    ];

    public $depends = [
        JQueryEasing::class,
        BootstrapAsset::class,
    ];
}