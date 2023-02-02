<?php

namespace backend\assets;

use yii\web\{AssetBundle, JqueryAsset};

/**
 * Class JQueryEasing
 * @package backend\assets
 */
class JQueryEasing extends AssetBundle
{
    public $sourcePath = '@npm/jquery.easing';

    public $js = [
        'jquery.easing.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}