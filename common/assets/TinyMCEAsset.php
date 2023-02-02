<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class TinyMCEAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class TinyMCEAsset extends AssetBundle {

    public $sourcePath = '@common/web/js/tinymce';

    public $js = [
        'tinymce.min.js',
        'jquery.tinymce.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}