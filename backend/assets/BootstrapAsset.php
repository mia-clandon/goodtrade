<?php

namespace backend\assets;

use yii\web\JqueryAsset;

/**
 * Class BootstrapAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class BootstrapAsset extends \yii\bootstrap\BootstrapAsset
{
    public $sourcePath = '@npm/bootstrap/dist';

    public $js = [
        'js/bootstrap.bundle.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}