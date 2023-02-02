<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Class RequireJsAsset
 * RequireJs используемый Frontend'щиком.
 * @package frontend\assets
 * @author Артём Широких kowapssupport@gmail.com
 */
class RequireJsAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $jsOptions = [];

    public $js = [
        'js/libs/require.js',
    ];
}