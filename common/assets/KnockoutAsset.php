<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class KnockoutAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class KnockoutAsset extends AssetBundle {

    public $sourcePath = '@common/web/js/';

    public $js = [
        'knockout/knockout.js',
        'knockout/knockout.mapping-latest.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}