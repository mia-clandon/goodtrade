<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class PerfectScrollbar
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class PerfectScrollbar extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/libs/perfect-scrollbar.jquery.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}