<?php

namespace frontend\assets;

use yii\web\View;

/**
 * Class SelectizeDisableAsset
 * @package frontend\assets
 */
class SelectizeDisableAsset extends \common\assets\SelectizeDisableAsset {

    public $depends = [
        SelectizeAsset::class,
        AppAsset::class,
    ];

    public $jsOptions = ['position' => View::POS_HEAD];
}