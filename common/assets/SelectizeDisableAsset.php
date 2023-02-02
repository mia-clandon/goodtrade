<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class SelectizeDisableAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js-libs/selectize-disabled-plugin.js',
    ];

    public $depends = [
        SelectizeAsset::class,
    ];
}