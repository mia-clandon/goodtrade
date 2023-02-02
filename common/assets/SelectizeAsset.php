<?php

namespace common\assets;

use yii\web\{AssetBundle, JqueryAsset};

/**
 * Class Selectize
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class SelectizeAsset extends AssetBundle {

    public $sourcePath = '@bower/selectize/dist';

    public $css = [
        'css/selectize.bootstrap4.css',
    ];

    public $js = [
        'js/selectize.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}