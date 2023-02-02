<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class Babel
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class Babel extends AssetBundle {

    public $sourcePath = '@bower/babel';

    public $js = [
        'browser.min.js',
    ];

}