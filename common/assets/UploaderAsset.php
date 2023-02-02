<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class UploaderAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class UploaderAsset extends AssetBundle {

    public $sourcePath = '@common/web/js/uploader';

    public $js = [
        'fileuploader.js',
    ];

    public $css = [
        'fileuploader.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}