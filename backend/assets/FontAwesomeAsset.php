<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class FontAwesomeAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class FontAwesomeAsset extends AssetBundle {

    public $sourcePath = '@bower/font-awesome';

    public $css = [
        'css/all.css',
    ];
}