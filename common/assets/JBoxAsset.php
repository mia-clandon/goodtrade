<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class JBoxAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class JBoxAsset extends AssetBundle {

    public $sourcePath = '@bower/stephanwagner/jbox/Source';

    public $js = [
        'jBox.min.js',
    ];

    public $css = [
        'jBox.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}