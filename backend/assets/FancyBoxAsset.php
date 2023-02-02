<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class FancyBox
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class FancyBoxAsset extends AssetBundle {

    public $sourcePath = '@bower/fancybox/dist';

    public $css = [
        'jquery.fancybox.min.css',
    ];

    public $js = [
        'jquery.fancybox.min.js',
    ];

    public $depends = [];
}