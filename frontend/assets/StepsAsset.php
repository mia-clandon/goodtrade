<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class StepsAsset
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class StepsAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/libs/jquery.steps.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}