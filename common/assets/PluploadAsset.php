<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class PluploadAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class PluploadAsset extends AssetBundle {

    public $sourcePath = '@bower/moxiecode/plupload/js';

    public $js = [
        'plupload.full.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}