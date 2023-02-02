<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class React
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class React extends AssetBundle {

    public $sourcePath = '@bower/react';

    public $js = [
        'react.min.js',
        'react-dom.min.js',
    ];

    public $depends = [
        'backend\assets\Babel',
    ];
}