<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class SizzleAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class SizzleAsset extends AssetBundle {

    public $sourcePath = '@bower/sizzle/dist';

    public $js = [
        'sizzle.js',
    ];
}