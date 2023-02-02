<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Класс для \frontend\controllers\BaseController::registerScriptBundle (webpack 2 bundle.)
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class BundleAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $depends = [
        AppAsset::class
    ];
}