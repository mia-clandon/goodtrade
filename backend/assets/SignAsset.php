<?php

namespace backend\assets;

use yii\web\{AssetBundle, YiiAsset};

/**
 * Class AppAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class SignAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
        YiiAsset::class,
        DashboardAsset::class,
    ];
}