<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class MochaAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class MochaAsset extends AssetBundle {

    public $sourcePath = '@bower/mocha';

    public $css = [
        'mocha.css',
    ];

    public $js = [
        'mocha.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}