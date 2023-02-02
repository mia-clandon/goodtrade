<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class PartIndexerAsset
 * @package backend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class PartIndexerAsset extends AssetBundle  {

    public $js = [
        'js/PartIndexer.js',
    ];

    public $depends = [
        'backend\assets\AppAsset',
    ];
}