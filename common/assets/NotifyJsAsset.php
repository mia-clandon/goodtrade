<?php

namespace common\assets;

use yii\web\{AssetBundle, JqueryAsset};

/**
 * Class NotifyJsAsset
 * @package common\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class NotifyJsAsset extends AssetBundle {

    public $sourcePath = '@bower/notifyjs/dist';

    public $js = [
        'notify.js',
        'styles/metro/notify-metro.js',
    ];

    public $css = [
        'styles/metro/notify-metro.css',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}