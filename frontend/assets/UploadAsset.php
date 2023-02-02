<?php

namespace frontend\assets;

use common\assets\JqueryUiAsset;
use yii\web\AssetBundle;

/**
 * Class UploadAsset
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class UploadAsset extends AssetBundle {

    public $sourcePath = '@bower/blueimp-file-upload';

    public $js = [
        'js/jquery.iframe-transport.js',
        'js/jquery.fileupload.js',
    ];

    public $depends = [
        JqueryUiAsset::class,
    ];
}