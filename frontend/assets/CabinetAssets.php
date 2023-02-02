<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class CabinetAssets
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class CabinetAssets extends AssetBundle {

    public $jsOptions = ['position' => View::POS_HEAD];

    public $depends = [
        AppAsset::class,
        UploadAsset::class,
        BundleAsset::class,
    ];
}