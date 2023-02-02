<?php

namespace common\assets;

use yii\web\AssetBundle;

use backend\assets\AppAsset;

/**
 * Class FormAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class FormAsset extends AssetBundle {

    public $sourcePath = '@common/web/js/';

    public $js = [
        'form.js',
    ];

    public $depends = [
        AppAsset::class,
        NotifyJsAsset::class,
    ];
}