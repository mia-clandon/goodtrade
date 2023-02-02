<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class PluploadAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class LazyLoadAsset extends AssetBundle {

    public $sourcePath = '@bower/jquery.lazyload';

    public $js = [
        'jquery.lazyload.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}