<?php

namespace frontend\assets;

use yii\web\View;

/**
 * Class SelectizeAsset
 * @package frontend\assets
 */
class SelectizeAsset extends \common\assets\SelectizeAsset {

    public $depends = [
        AppAsset::class,
    ];

    public $css = [
        'css/selectize.bootstrap3.css',
    ];

    public $jsOptions = ['position' => View::POS_HEAD];

}